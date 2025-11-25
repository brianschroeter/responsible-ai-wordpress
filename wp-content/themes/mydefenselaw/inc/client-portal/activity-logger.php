<?php
/**
 * Client Portal Activity Logger
 *
 * Logs client and attorney activities within the portal for security,
 * auditing, and compliance purposes.
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Log portal activity
 *
 * Creates a portal_activity post to track user actions with metadata.
 *
 * @param string $action        Action type (login_success, document_viewed, etc.)
 * @param string $resource_type Resource type (document, case, message, etc.)
 * @param int    $resource_id   Resource post ID
 * @param string $details       Additional details about the action
 * @return int|false Activity post ID or false on failure
 */
function mydefenselaw_portal_log_activity($action, $resource_type = '', $resource_id = 0, $details = '') {
    // Get current user (might be 0 for failed login attempts)
    $user_id = get_current_user_id();

    // For non-authenticated actions (like failed logins), we still want to log
    $user_name = 'Guest';
    $user_role = 'none';

    if ($user_id > 0) {
        $user = get_userdata($user_id);
        $user_name = $user->display_name;
        $user_role = implode(', ', (array) $user->roles);
    }

    // Get IP address and user agent
    $ip_address = mydefenselaw_portal_get_ip();
    $user_agent = isset($_SERVER['HTTP_USER_AGENT'])
        ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT']))
        : '';

    // Build activity title
    $title = sprintf(
        '%s - %s - %s',
        $user_name,
        $action,
        gmdate('Y-m-d H:i:s')
    );

    // Create activity post
    $activity_id = wp_insert_post(array(
        'post_type'    => 'portal_activity',
        'post_status'  => 'publish',
        'post_title'   => sanitize_text_field($title),
        'post_content' => sanitize_textarea_field($details),
        'post_author'  => $user_id > 0 ? $user_id : 1, // Use admin ID if no user
    ), true);

    if (is_wp_error($activity_id)) {
        return false;
    }

    // Save core metadata
    update_post_meta($activity_id, '_action', sanitize_text_field($action));
    update_post_meta($activity_id, '_resource_type', sanitize_text_field($resource_type));
    update_post_meta($activity_id, '_resource_id', intval($resource_id));
    update_post_meta($activity_id, '_user_id', $user_id);
    update_post_meta($activity_id, '_user_name', sanitize_text_field($user_name));
    update_post_meta($activity_id, '_user_role', sanitize_text_field($user_role));
    update_post_meta($activity_id, '_ip_address', sanitize_text_field($ip_address));
    update_post_meta($activity_id, '_user_agent', sanitize_text_field($user_agent));
    update_post_meta($activity_id, '_timestamp', time());
    update_post_meta($activity_id, '_details', sanitize_textarea_field($details));

    // Get resource title if available
    if ($resource_id > 0) {
        $resource_post = get_post($resource_id);
        if ($resource_post) {
            update_post_meta($activity_id, '_resource_title', sanitize_text_field($resource_post->post_title));
        }
    }

    // Save with ACF if available
    if (function_exists('update_field')) {
        update_field('activity_action', $action, $activity_id);
        update_field('activity_user', $user_id, $activity_id);
        update_field('activity_resource_type', $resource_type, $activity_id);
        update_field('activity_resource_id', $resource_id, $activity_id);
        update_field('activity_timestamp', current_time('Y-m-d H:i:s'), $activity_id);
        update_field('activity_ip', $ip_address, $activity_id);
        update_field('activity_details', $details, $activity_id);
    }

    // Trigger action hook for extensibility
    do_action('mydefenselaw_portal_activity_logged', $activity_id, $action, $resource_type, $resource_id, $user_id);

    return $activity_id;
}

/**
 * Get user's IP address (handles proxies)
 *
 * Uses the security.php function if available, otherwise provides fallback.
 *
 * @return string IP address
 */
function mydefenselaw_portal_get_ip_address() {
    // Use the security module's IP function if available
    if (function_exists('mydefenselaw_portal_get_ip')) {
        return mydefenselaw_portal_get_ip();
    }

    // Fallback implementation
    $ip_keys = array(
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR',
    );

    foreach ($ip_keys as $key) {
        if (isset($_SERVER[$key])) {
            $ip_list = explode(',', sanitize_text_field(wp_unslash($_SERVER[$key])));
            foreach ($ip_list as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
    }

    return 'unknown';
}

/**
 * Get recent activity for a user
 *
 * @param int $user_id User ID
 * @param int $limit   Number of activities to retrieve
 * @return array Array of activity posts
 */
function mydefenselaw_portal_get_user_activity($user_id, $limit = 10) {
    $args = array(
        'post_type'      => 'portal_activity',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'meta_query'     => array(
            array(
                'key'     => '_user_id',
                'value'   => $user_id,
                'compare' => '=',
            ),
        ),
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    return get_posts($args);
}

/**
 * Get recent activity for a resource
 *
 * @param string $resource_type Resource type (document, case, message, etc.)
 * @param int    $resource_id   Resource post ID
 * @param int    $limit         Number of activities to retrieve
 * @return array Array of activity posts
 */
function mydefenselaw_portal_get_resource_activity($resource_type, $resource_id, $limit = 10) {
    $args = array(
        'post_type'      => 'portal_activity',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => '_resource_type',
                'value'   => sanitize_text_field($resource_type),
                'compare' => '=',
            ),
            array(
                'key'     => '_resource_id',
                'value'   => intval($resource_id),
                'compare' => '=',
                'type'    => 'NUMERIC',
            ),
        ),
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    return get_posts($args);
}

/**
 * Get activity by action type
 *
 * @param string $action Action type to filter by
 * @param int    $limit  Number of activities to retrieve
 * @return array Array of activity posts
 */
function mydefenselaw_portal_get_activity_by_action($action, $limit = 10) {
    $args = array(
        'post_type'      => 'portal_activity',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'meta_query'     => array(
            array(
                'key'     => '_action',
                'value'   => sanitize_text_field($action),
                'compare' => '=',
            ),
        ),
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    return get_posts($args);
}

/**
 * Get all portal activity with optional filters
 *
 * @param array $args Query arguments
 * @return array Array of activity posts
 */
function mydefenselaw_portal_get_all_activity($args = array()) {
    $defaults = array(
        'post_type'      => 'portal_activity',
        'post_status'    => 'publish',
        'posts_per_page' => 50,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    $args = wp_parse_args($args, $defaults);

    return get_posts($args);
}

/**
 * Get activity within date range
 *
 * @param string $start_date Start date (Y-m-d format)
 * @param string $end_date   End date (Y-m-d format)
 * @param int    $limit      Number of activities to retrieve
 * @return array Array of activity posts
 */
function mydefenselaw_portal_get_activity_by_date($start_date, $end_date, $limit = 100) {
    $args = array(
        'post_type'      => 'portal_activity',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'date_query'     => array(
            array(
                'after'     => $start_date,
                'before'    => $end_date,
                'inclusive' => true,
            ),
        ),
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    return get_posts($args);
}

/**
 * Get suspicious activities (failed logins, unauthorized access, etc.)
 *
 * @param int $limit Number of activities to retrieve
 * @return array Array of activity posts
 */
function mydefenselaw_portal_get_suspicious_activity($limit = 20) {
    $suspicious_actions = array(
        'login_failed',
        'unauthorized_access',
        'session_expired',
        'rate_limit_exceeded',
    );

    $args = array(
        'post_type'      => 'portal_activity',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'meta_query'     => array(
            array(
                'key'     => '_action',
                'value'   => $suspicious_actions,
                'compare' => 'IN',
            ),
        ),
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    return get_posts($args);
}

/**
 * Format activity for display
 *
 * @param int $activity_id Activity post ID
 * @return array|null Formatted activity data or null if invalid
 */
function mydefenselaw_portal_format_activity($activity_id) {
    $activity = get_post($activity_id);

    if (!$activity || $activity->post_type !== 'portal_activity') {
        return null;
    }

    // Get metadata
    $action        = get_post_meta($activity_id, '_action', true);
    $resource_type = get_post_meta($activity_id, '_resource_type', true);
    $resource_id   = get_post_meta($activity_id, '_resource_id', true);
    $user_id       = get_post_meta($activity_id, '_user_id', true);
    $user_name     = get_post_meta($activity_id, '_user_name', true);
    $user_role     = get_post_meta($activity_id, '_user_role', true);
    $ip_address    = get_post_meta($activity_id, '_ip_address', true);
    $timestamp     = get_post_meta($activity_id, '_timestamp', true);
    $details       = get_post_meta($activity_id, '_details', true);

    // Build human-readable message
    $message = mydefenselaw_portal_get_activity_message(
        $action,
        $resource_type,
        $resource_id,
        $user_name
    );

    return array(
        'id'            => $activity_id,
        'action'        => $action,
        'resource_type' => $resource_type,
        'resource_id'   => $resource_id,
        'user_id'       => $user_id,
        'user_name'     => $user_name,
        'user_role'     => $user_role,
        'ip_address'    => $ip_address,
        'message'       => $message,
        'details'       => $details,
        'timestamp'     => $timestamp,
        'date'          => get_the_date('', $activity),
        'time'          => get_the_time('', $activity),
        'date_human'    => human_time_diff($timestamp, time()) . ' ago',
    );
}

/**
 * Get human-readable activity message
 *
 * @param string $action        Action type
 * @param string $resource_type Resource type
 * @param int    $resource_id   Resource ID
 * @param string $user_name     User display name
 * @return string Human-readable message
 */
function mydefenselaw_portal_get_activity_message($action, $resource_type, $resource_id, $user_name) {
    $resource_title = '';

    if ($resource_id) {
        $resource_post = get_post($resource_id);
        if ($resource_post) {
            $resource_title = $resource_post->post_title;
        }
    }

    // Activity action types and their messages
    $messages = array(
        // Authentication
        'login_success'      => sprintf(__('%s logged in successfully', 'mydefenselaw'), $user_name),
        'login_failed'       => sprintf(__('Failed login attempt for %s', 'mydefenselaw'), $user_name),
        'logout'             => sprintf(__('%s logged out', 'mydefenselaw'), $user_name),
        'session_expired'    => sprintf(__('%s session expired', 'mydefenselaw'), $user_name),

        // Document actions
        'document_viewed'    => sprintf(__('%s viewed document: %s', 'mydefenselaw'), $user_name, $resource_title),
        'document_downloaded' => sprintf(__('%s downloaded document: %s', 'mydefenselaw'), $user_name, $resource_title),
        'document_uploaded'  => sprintf(__('%s uploaded document: %s', 'mydefenselaw'), $user_name, $resource_title),
        'document_deleted'   => sprintf(__('%s deleted document: %s', 'mydefenselaw'), $user_name, $resource_title),

        // Message actions
        'message_sent'       => sprintf(__('%s sent a message', 'mydefenselaw'), $user_name),
        'message_read'       => sprintf(__('%s read message: %s', 'mydefenselaw'), $user_name, $resource_title),

        // Profile actions
        'profile_updated'    => sprintf(__('%s updated their profile', 'mydefenselaw'), $user_name),
        'password_changed'   => sprintf(__('%s changed their password', 'mydefenselaw'), $user_name),

        // Case actions
        'case_viewed'        => sprintf(__('%s viewed case: %s', 'mydefenselaw'), $user_name, $resource_title),

        // Security events
        'unauthorized_access' => sprintf(__('%s attempted unauthorized access to: %s', 'mydefenselaw'), $user_name, $resource_title),
        'rate_limit_exceeded' => sprintf(__('Rate limit exceeded for %s', 'mydefenselaw'), $user_name),
    );

    // Allow filtering of messages
    $messages = apply_filters('mydefenselaw_portal_activity_messages', $messages, $action, $resource_type, $resource_id, $user_name);

    if (isset($messages[$action])) {
        return $messages[$action];
    }

    // Default message
    if ($resource_title) {
        return sprintf(
            __('%s performed %s on %s: %s', 'mydefenselaw'),
            $user_name,
            $action,
            $resource_type,
            $resource_title
        );
    }

    return sprintf(
        __('%s performed %s', 'mydefenselaw'),
        $user_name,
        $action
    );
}

/**
 * Get activity statistics
 *
 * @param array $args Optional filters (user_id, date_range, etc.)
 * @return array Activity statistics
 */
function mydefenselaw_portal_get_activity_stats($args = array()) {
    $defaults = array(
        'user_id'    => null,
        'start_date' => null,
        'end_date'   => null,
    );

    $args = wp_parse_args($args, $defaults);

    $query_args = array(
        'post_type'      => 'portal_activity',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    );

    // Add user filter if specified
    if ($args['user_id']) {
        $query_args['meta_query'] = array(
            array(
                'key'     => '_user_id',
                'value'   => $args['user_id'],
                'compare' => '=',
            ),
        );
    }

    // Add date filter if specified
    if ($args['start_date'] && $args['end_date']) {
        $query_args['date_query'] = array(
            array(
                'after'     => $args['start_date'],
                'before'    => $args['end_date'],
                'inclusive' => true,
            ),
        );
    }

    $activities = get_posts($query_args);
    $total = count($activities);

    // Count by action type
    $action_counts = array();
    foreach ($activities as $activity_id) {
        $action = get_post_meta($activity_id, '_action', true);
        if (!isset($action_counts[$action])) {
            $action_counts[$action] = 0;
        }
        $action_counts[$action]++;
    }

    // Sort by count
    arsort($action_counts);

    return array(
        'total'         => $total,
        'by_action'     => $action_counts,
        'most_common'   => array_key_first($action_counts),
    );
}

/**
 * Delete old activity logs
 *
 * Removes activity logs older than specified days.
 *
 * @param int $days Number of days to keep (default: 90)
 * @return int Number of deleted activities
 */
function mydefenselaw_portal_cleanup_old_activity($days = 90) {
    $cutoff_date = gmdate('Y-m-d', strtotime("-{$days} days"));

    $args = array(
        'post_type'      => 'portal_activity',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'date_query'     => array(
            array(
                'before'    => $cutoff_date,
                'inclusive' => false,
            ),
        ),
    );

    $old_activities = get_posts($args);
    $deleted = 0;

    foreach ($old_activities as $activity_id) {
        if (wp_delete_post($activity_id, true)) {
            $deleted++;
        }
    }

    return $deleted;
}

/**
 * Export activity logs to CSV
 *
 * @param array $args Query arguments for filtering
 * @return string CSV content
 */
function mydefenselaw_portal_export_activity_csv($args = array()) {
    $activities = mydefenselaw_portal_get_all_activity($args);

    // CSV headers
    $csv = "Date,Time,User,Role,Action,Resource Type,Resource ID,IP Address,Details\n";

    foreach ($activities as $activity) {
        $formatted = mydefenselaw_portal_format_activity($activity->ID);

        if ($formatted) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $formatted['date'],
                $formatted['time'],
                $formatted['user_name'],
                $formatted['user_role'],
                $formatted['action'],
                $formatted['resource_type'],
                $formatted['resource_id'],
                $formatted['ip_address'],
                str_replace('"', '""', $formatted['details'])
            );
        }
    }

    return $csv;
}
