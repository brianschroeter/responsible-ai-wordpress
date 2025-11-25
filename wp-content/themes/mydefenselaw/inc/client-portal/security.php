<?php
/**
 * Client Portal Security Layer
 *
 * Handles authentication, session management, rate limiting, and access control
 * for the client portal system.
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize security features
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_init_security() {
    // Add security headers on portal pages
    add_action('send_headers', 'mydefenselaw_portal_security_headers');

    // Rate limiting on login
    add_filter('authenticate', 'mydefenselaw_portal_authenticate_rate_limit', 30, 3);

    // Track failed logins
    add_action('wp_login_failed', 'mydefenselaw_portal_failed_login', 10, 2);

    // Clear rate limit on successful login
    add_action('wp_login', 'mydefenselaw_portal_successful_login', 10, 2);

    // Log logout events
    add_action('wp_logout', 'mydefenselaw_portal_logout');
}

/**
 * Check if current user can access portal
 *
 * Verifies: logged in, has client role, session not expired
 *
 * @param bool $redirect Whether to redirect on failure (default: true)
 * @return bool True if user can access portal, false otherwise
 */
function mydefenselaw_portal_require_auth($redirect = true) {
    // Check if user is logged in
    if (!is_user_logged_in()) {
        if ($redirect) {
            wp_redirect(wp_login_url(mydefenselaw_portal_get_current_url()));
            exit;
        }
        return false;
    }

    $user = wp_get_current_user();

    // Check if user has client role
    if (!in_array('client', (array) $user->roles)) {
        if ($redirect) {
            wp_redirect(home_url());
            exit;
        }
        return false;
    }

    // Check session timeout
    if (mydefenselaw_portal_check_timeout()) {
        if ($redirect) {
            // Log the timeout
            mydefenselaw_portal_log_activity('session_expired', '', 0, 'Session timed out due to inactivity');

            // Destroy session
            wp_logout();

            // Redirect to login with message
            wp_redirect(add_query_arg(
                'portal_message',
                'session_expired',
                wp_login_url(mydefenselaw_portal_get_current_url())
            ));
            exit;
        }
        return false;
    }

    // Update last activity timestamp
    mydefenselaw_portal_update_activity();

    return true;
}

/**
 * Get current portal client user object
 *
 * @return WP_User|false User object if valid client, false otherwise
 */
function mydefenselaw_portal_get_current_client() {
    if (!is_user_logged_in()) {
        return false;
    }

    $user = wp_get_current_user();

    // Verify user has client role
    if (!in_array('client', (array) $user->roles)) {
        return false;
    }

    return $user;
}

/**
 * Set session timeout via cookie expiration
 *
 * Hooks into 'auth_cookie_expiration' filter to set custom timeout
 * for client portal users.
 *
 * @param int  $expiration Time in seconds until expiration
 * @param int  $user_id    User ID
 * @param bool $remember   Whether "remember me" was checked
 * @return int Modified expiration time
 */
function mydefenselaw_portal_session_timeout($expiration, $user_id, $remember) {
    // Don't modify if "remember me" is checked
    if ($remember) {
        return $expiration;
    }

    // Check if user has client role
    $user = get_userdata($user_id);
    if (!$user || !in_array('client', (array) $user->roles)) {
        return $expiration;
    }

    // Get custom timeout from ACF options (in minutes), default to 30 minutes
    $timeout_minutes = 30;
    if (function_exists('get_field')) {
        $custom_timeout = get_field('portal_session_timeout', 'option');
        if ($custom_timeout && is_numeric($custom_timeout)) {
            $timeout_minutes = intval($custom_timeout);
        }
    }

    return $timeout_minutes * MINUTE_IN_SECONDS;
}
add_filter('auth_cookie_expiration', 'mydefenselaw_portal_session_timeout', 10, 3);

/**
 * Update user's last activity timestamp
 *
 * Called on each portal page load to track activity for timeout detection.
 */
function mydefenselaw_portal_update_activity() {
    $user = mydefenselaw_portal_get_current_client();
    if (!$user) {
        return;
    }

    update_user_meta($user->ID, '_portal_last_activity', time());
}

/**
 * Check if session has timed out based on activity
 *
 * @return bool True if session timed out, false otherwise
 */
function mydefenselaw_portal_check_timeout() {
    $user = mydefenselaw_portal_get_current_client();
    if (!$user) {
        return false;
    }

    $last_activity = get_user_meta($user->ID, '_portal_last_activity', true);
    if (!$last_activity) {
        // No activity recorded yet, set it now
        mydefenselaw_portal_update_activity();
        return false;
    }

    // Get timeout duration (in minutes), default to 30
    $timeout_minutes = 30;
    if (function_exists('get_field')) {
        $custom_timeout = get_field('portal_session_timeout', 'option');
        if ($custom_timeout && is_numeric($custom_timeout)) {
            $timeout_minutes = intval($custom_timeout);
        }
    }

    $timeout_seconds = $timeout_minutes * MINUTE_IN_SECONDS;
    $time_since_activity = time() - intval($last_activity);

    return $time_since_activity > $timeout_seconds;
}

/**
 * Check if user is rate limited for login attempts
 *
 * @param string $username Username attempting login
 * @return bool True if rate limited, false otherwise
 */
function mydefenselaw_portal_check_rate_limit($username) {
    $ip = mydefenselaw_portal_get_ip();
    $transient_key = 'portal_login_attempts_' . md5($username . $ip);

    $attempts = get_transient($transient_key);

    // No attempts recorded
    if (!$attempts) {
        return false;
    }

    // Check if exceeded 5 attempts
    return intval($attempts) >= 5;
}

/**
 * Record failed login attempt
 *
 * @param string $username Username that failed login
 */
function mydefenselaw_portal_record_failed_attempt($username) {
    $ip = mydefenselaw_portal_get_ip();
    $transient_key = 'portal_login_attempts_' . md5($username . $ip);

    $attempts = get_transient($transient_key);
    $attempts = $attempts ? intval($attempts) + 1 : 1;

    // Store for 15 minutes
    set_transient($transient_key, $attempts, 15 * MINUTE_IN_SECONDS);

    // Log the failed attempt
    mydefenselaw_portal_log_activity(
        'login_failed',
        '',
        0,
        sprintf('Failed login attempt for username: %s (Attempt %d/5)', $username, $attempts)
    );
}

/**
 * Clear login attempts for user
 *
 * @param string $username Username to clear attempts for
 */
function mydefenselaw_portal_clear_attempts($username) {
    $ip = mydefenselaw_portal_get_ip();
    $transient_key = 'portal_login_attempts_' . md5($username . $ip);
    delete_transient($transient_key);
}

/**
 * Check if user is rate limited
 *
 * @param string $username Username to check
 * @return bool True if rate limited, false otherwise
 */
function mydefenselaw_portal_is_rate_limited($username) {
    return mydefenselaw_portal_check_rate_limit($username);
}

/**
 * Check if user can access a specific case
 *
 * @param int $user_id User ID to check
 * @param int $case_id Case ID to check access for
 * @return bool True if user can access case, false otherwise
 */
function mydefenselaw_portal_can_access_case($user_id, $case_id) {
    // Admin can access all cases
    if (user_can($user_id, 'manage_options')) {
        return true;
    }

    // Get the assigned client for this case
    $assigned_client = get_post_meta($case_id, '_assigned_client', true);

    // Client can only access their own cases
    return intval($assigned_client) === intval($user_id);
}

/**
 * Check if user can access a specific document
 *
 * @param int $user_id    User ID to check
 * @param int $document_id Document ID to check access for
 * @return bool True if user can access document, false otherwise
 */
function mydefenselaw_portal_can_access_document($user_id, $document_id) {
    // Admin can access all documents
    if (user_can($user_id, 'manage_options')) {
        return true;
    }

    // Get the case this document belongs to
    $case_id = get_post_meta($document_id, '_case_id', true);

    if (!$case_id) {
        return false;
    }

    // Check if user can access the parent case
    return mydefenselaw_portal_can_access_case($user_id, $case_id);
}

/**
 * Check if user can access a specific message
 *
 * @param int $user_id    User ID to check
 * @param int $message_id Message ID to check access for
 * @return bool True if user can access message, false otherwise
 */
function mydefenselaw_portal_can_access_message($user_id, $message_id) {
    // Admin can access all messages
    if (user_can($user_id, 'manage_options')) {
        return true;
    }

    // Get the case this message belongs to
    $case_id = get_post_meta($message_id, '_case_id', true);

    if (!$case_id) {
        return false;
    }

    // Check if user can access the parent case
    return mydefenselaw_portal_can_access_case($user_id, $case_id);
}

// Note: mydefenselaw_portal_get_client_cases, mydefenselaw_portal_get_client_documents,
// and mydefenselaw_portal_get_client_messages are defined in template-tags.php
// with enhanced functionality (additional $args parameter support)

/**
 * Add security headers for portal pages
 */
function mydefenselaw_portal_security_headers() {
    // Only add headers on portal pages
    if (!mydefenselaw_portal_is_portal_page()) {
        return;
    }

    // Prevent clickjacking
    header('X-Frame-Options: SAMEORIGIN');

    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');

    // Enable XSS protection
    header('X-XSS-Protection: 1; mode=block');

    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Content Security Policy (adjust as needed)
    $csp = "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src 'self' https://cdnjs.cloudflare.com https://fonts.gstatic.com; img-src 'self' data: https:;";
    header('Content-Security-Policy: ' . $csp);
}
add_action('send_headers', 'mydefenselaw_portal_security_headers');

// Note: mydefenselaw_portal_create_nonce, mydefenselaw_portal_verify_nonce,
// and mydefenselaw_portal_is_portal_page are defined in inc/enqueue.php

/**
 * Get current URL for redirects
 *
 * @return string Current URL
 */
function mydefenselaw_portal_get_current_url() {
    global $wp;
    return home_url(add_query_arg(array(), $wp->request));
}

/**
 * Handle login rate limiting
 *
 * @param WP_User|WP_Error|null $user     WP_User or WP_Error object if a previous callback failed authentication
 * @param string                $username Username or email address
 * @param string                $password User password
 * @return WP_User|WP_Error
 */
function mydefenselaw_portal_authenticate_rate_limit($user, $username, $password) {
    // Skip if already error
    if (is_wp_error($user)) {
        return $user;
    }

    // Skip if no username provided
    if (empty($username)) {
        return $user;
    }

    // Check if user is rate limited
    if (mydefenselaw_portal_is_rate_limited($username)) {
        // Log the rate limit event
        mydefenselaw_portal_log_activity(
            'login_failed',
            '',
            0,
            sprintf('Rate limit exceeded for username: %s', $username)
        );

        return new WP_Error(
            'rate_limited',
            __('Too many failed login attempts. Please try again in 15 minutes.', 'mydefenselaw')
        );
    }

    return $user;
}
add_filter('authenticate', 'mydefenselaw_portal_authenticate_rate_limit', 30, 3);

/**
 * Handle failed login attempts
 *
 * @param string   $username Username or email address
 * @param WP_Error $error    WP_Error object containing errors
 */
function mydefenselaw_portal_failed_login($username, $error) {
    // Only track if it's an actual authentication failure (not empty fields)
    if ($error->get_error_code() === 'incorrect_password' ||
        $error->get_error_code() === 'invalid_username') {
        mydefenselaw_portal_record_failed_attempt($username);
    }
}
add_action('wp_login_failed', 'mydefenselaw_portal_failed_login', 10, 2);

/**
 * Clear login attempts on successful login
 *
 * @param string  $username Username
 * @param WP_User $user     WP_User object
 */
function mydefenselaw_portal_successful_login($username, $user) {
    // Clear any failed attempts
    mydefenselaw_portal_clear_attempts($username);

    // Log successful login
    mydefenselaw_portal_log_activity('login_success', '', 0, 'User logged in successfully');

    // Initialize last activity
    update_user_meta($user->ID, '_portal_last_activity', time());
}
add_action('wp_login', 'mydefenselaw_portal_successful_login', 10, 2);

/**
 * Handle logout
 */
function mydefenselaw_portal_logout() {
    $user = mydefenselaw_portal_get_current_client();
    if ($user) {
        mydefenselaw_portal_log_activity('logout', '', 0, 'User logged out');
    }
}
add_action('wp_logout', 'mydefenselaw_portal_logout');

/**
 * Sanitize and validate file upload
 *
 * @param array $file File array from $_FILES
 * @return array|WP_Error Sanitized file array or WP_Error on failure
 */
function mydefenselaw_portal_validate_file_upload($file) {
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return new WP_Error('no_file', __('No file was uploaded.', 'mydefenselaw'));
    }

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return new WP_Error('upload_error', __('File upload failed.', 'mydefenselaw'));
    }

    // Validate file type (whitelist approach)
    $allowed_types = array(
        'pdf'  => 'application/pdf',
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'txt'  => 'text/plain',
    );

    $file_type = wp_check_filetype($file['name']);

    if (!in_array($file_type['type'], $allowed_types)) {
        return new WP_Error('invalid_file_type', __('File type not allowed.', 'mydefenselaw'));
    }

    // Check file size (10MB max)
    $max_size = 10 * 1024 * 1024; // 10MB in bytes
    if ($file['size'] > $max_size) {
        return new WP_Error('file_too_large', __('File size exceeds 10MB limit.', 'mydefenselaw'));
    }

    return $file;
}

/**
 * Get user's IP address (handles proxies)
 *
 * @return string IP address
 */
function mydefenselaw_portal_get_ip() {
    // Check for proxy headers
    $ip = '';

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_CLIENT_IP']));
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Can contain multiple IPs, get the first one
        $ip_list = explode(',', sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR'])));
        $ip = trim($ip_list[0]);
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
    }

    // Validate IP address
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return $ip;
    }

    return 'unknown';
}
