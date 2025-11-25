<?php
/**
 * Asset Enqueue Functions
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

/**
 * Enqueue conditional assets based on page template
 */
function mydefenselaw_conditional_assets() {
    // Load practice areas styles on relevant pages
    if (is_page_template('page-practice-areas.php') || is_singular('practice_area')) {
        wp_enqueue_style(
            'mydefenselaw-practice-areas',
            get_template_directory_uri() . '/assets/css/practice-areas.css',
            array('mydefenselaw-style'),
            MYDEFENSELAW_VERSION
        );
    }

    // Load contact page specific assets
    if (is_page_template('page-contact.php')) {
        wp_enqueue_style(
            'mydefenselaw-contact',
            get_template_directory_uri() . '/assets/css/contact.css',
            array('mydefenselaw-style'),
            MYDEFENSELAW_VERSION
        );
    }
}
add_action('wp_enqueue_scripts', 'mydefenselaw_conditional_assets', 20);

/**
 * Enqueue portal-specific assets
 */
function mydefenselaw_portal_enqueue_assets() {
    // Only load on portal pages
    if (!mydefenselaw_portal_is_portal_page()) {
        return;
    }

    // Portal CSS - Make sure it's enqueued with proper preload
    wp_enqueue_style(
        'mydefenselaw-portal',
        get_template_directory_uri() . '/assets/css/portal.css',
        array('mydefenselaw-style'),
        MYDEFENSELAW_VERSION
    );
    wp_style_add_data('mydefenselaw-portal', 'media', 'all');

    // Portal JS
    wp_enqueue_script(
        'mydefenselaw-portal',
        get_template_directory_uri() . '/assets/js/portal.js',
        array('jquery'),
        MYDEFENSELAW_VERSION,
        true
    );

    // Localize script with configuration
    wp_localize_script('mydefenselaw-portal', 'portalData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonces' => array(
            'upload_document' => wp_create_nonce('mydefenselaw_portal_upload_document'),
            'delete_document' => wp_create_nonce('mydefenselaw_portal_delete_document'),
            'create_folder' => wp_create_nonce('mydefenselaw_portal_create_folder'),
            'send_message' => wp_create_nonce('mydefenselaw_portal_send_message'),
            'send_reply' => wp_create_nonce('mydefenselaw_portal_send_reply'),
            'mark_read' => wp_create_nonce('mydefenselaw_portal_mark_read'),
            'update_profile' => wp_create_nonce('mydefenselaw_portal_update_profile'),
            'change_password' => wp_create_nonce('mydefenselaw_portal_change_password'),
            'update_notifications' => wp_create_nonce('mydefenselaw_portal_update_notifications'),
            'get_unread_count' => wp_create_nonce('mydefenselaw_portal_get_unread_count'),
            'get_conversation' => wp_create_nonce('mydefenselaw_portal_get_conversation'),
        ),
        'i18n' => array(
            'uploadError' => __('Upload failed', 'mydefenselaw'),
            'confirmDelete' => __('Are you sure you want to delete this item?', 'mydefenselaw'),
            'sessionWarning' => __('Your session will expire soon. Please save your work.', 'mydefenselaw'),
            'sessionExpired' => __('Your session has expired. Please log in again.', 'mydefenselaw'),
            'errorGeneric' => __('An error occurred. Please try again.', 'mydefenselaw'),
            'successSaved' => __('Changes saved successfully.', 'mydefenselaw'),
            'requiredField' => __('This field is required.', 'mydefenselaw'),
            'invalidEmail' => __('Please enter a valid email address.', 'mydefenselaw'),
        ),
        'config' => array(
            'maxFileSize' => wp_max_upload_size(),
            'allowedFileTypes' => array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'zip'),
            'pollInterval' => 30000, // 30 seconds
            'sessionTimeout' => 30 * 60 * 1000, // 30 minutes
        ),
    ));

    // Localize translatable strings for portal.js
    wp_localize_script('mydefenselaw-portal', 'mydefenseLawPortalStrings', array(
        'uploadProgress' => __('Uploading %s: %d%%', 'mydefenselaw'),
        'uploadSuccess' => __('%s uploaded successfully', 'mydefenselaw'),
        'uploadFailed' => __('Upload failed', 'mydefenselaw'),
        'uploadError' => __('Upload error: Invalid response', 'mydefenselaw'),
        'serverError' => __('Upload failed: Server error', 'mydefenselaw'),
        'deleteConfirm' => __('Are you sure you want to delete "%s"?', 'mydefenselaw'),
        'deleteSuccess' => __('Document deleted successfully', 'mydefenselaw'),
        'deleteFailed' => __('Failed to delete document', 'mydefenselaw'),
        'loadConversationFailed' => __('Failed to load conversation', 'mydefenselaw'),
        'loadConversationError' => __('Error loading conversation', 'mydefenselaw'),
        'enterMessage' => __('Please enter a message', 'mydefenselaw'),
        'sendReplyFailed' => __('Failed to send reply', 'mydefenselaw'),
        'sendReplyError' => __('Error sending reply', 'mydefenselaw'),
        'sendReply' => __('Send Reply', 'mydefenselaw'),
        'sendMessageFailed' => __('Failed to send message', 'mydefenselaw'),
        'sendMessageError' => __('Error sending message', 'mydefenselaw'),
        'sendMessage' => __('Send Message', 'mydefenselaw'),
        'messageSent' => __('Message sent successfully', 'mydefenselaw'),
        'saving' => __('Saving...', 'mydefenselaw'),
        'profileUpdated' => __('Profile updated successfully', 'mydefenselaw'),
        'profileUpdateFailed' => __('Failed to update profile', 'mydefenselaw'),
        'profileUpdateError' => __('Error updating profile', 'mydefenselaw'),
        'saveChanges' => __('Save Changes', 'mydefenselaw'),
        'fillPasswordFields' => __('Please fill in all password fields', 'mydefenselaw'),
        'passwordsNoMatch' => __('New passwords do not match', 'mydefenselaw'),
        'passwordMinLength' => __('Password must be at least 8 characters', 'mydefenselaw'),
        'changing' => __('Changing...', 'mydefenselaw'),
        'passwordChanged' => __('Password changed successfully', 'mydefenselaw'),
        'passwordChangeFailed' => __('Failed to change password', 'mydefenselaw'),
        'passwordChangeError' => __('Error changing password', 'mydefenselaw'),
        'changePassword' => __('Change Password', 'mydefenselaw'),
        'sessionExpiring' => __('Your session will expire in 5 minutes. Please save your work.', 'mydefenselaw'),
        'fieldRequired' => __('This field is required', 'mydefenselaw'),
        'minLength' => __('Must be at least %d characters', 'mydefenselaw'),
        'notificationPrefsUpdated' => __('Notification preferences updated', 'mydefenselaw'),
    ));
}
add_action('wp_enqueue_scripts', 'mydefenselaw_portal_enqueue_assets', 20);

/**
 * Check if current page is a portal page
 */
function mydefenselaw_portal_is_portal_page() {
    // Check if we're on any portal-related page
    // This assumes portal pages will use a specific template or URL structure

    // Check for portal page templates
    if (is_page_template('page-portal-dashboard.php') ||
        is_page_template('page-portal-documents.php') ||
        is_page_template('page-portal-messages.php') ||
        is_page_template('page-portal-cases.php') ||
        is_page_template('page-portal-profile.php') ||
        is_page_template('page-portal-login.php')) {
        return true;
    }

    // Check for portal page by page slug
    $page_id = get_queried_object_id();
    if ($page_id) {
        $post = get_post($page_id);
        if ($post && (strpos($post->post_name, 'portal') !== false)) {
            return true;
        }
    }

    // Check for portal URL slug (e.g., /portal/*)
    $current_url = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($current_url, '/portal/') !== false) {
        return true;
    }

    // Check for portal post type
    if (get_post_type() === 'client_document' ||
        get_post_type() === 'client_message' ||
        get_post_type() === 'client_case') {
        return true;
    }

    // Check if body has portal class (fallback for portal pages)
    if (is_singular('page')) {
        $post_id = get_the_ID();
        if ($post_id) {
            $page = get_page($post_id);
            if ($page && strpos($page->post_name, 'portal') !== false) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Create nonce helper for portal actions
 */
function mydefenselaw_portal_create_nonce($action) {
    return wp_create_nonce('mydefenselaw_portal_' . $action);
}

/**
 * Verify nonce helper for portal actions
 */
function mydefenselaw_portal_verify_nonce($nonce, $action) {
    return wp_verify_nonce($nonce, 'mydefenselaw_portal_' . $action);
}

/**
 * Enqueue admin styles
 */
function mydefenselaw_admin_styles() {
    wp_enqueue_style(
        'mydefenselaw-admin',
        get_template_directory_uri() . '/assets/css/admin.css',
        array(),
        MYDEFENSELAW_VERSION
    );
}
add_action('admin_enqueue_scripts', 'mydefenselaw_admin_styles');

/**
 * Add async/defer attributes to scripts
 */
function mydefenselaw_script_attributes($tag, $handle, $src) {
    $async_scripts = array('mydefenselaw-script');

    if (in_array($handle, $async_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'mydefenselaw_script_attributes', 10, 3);

/**
 * Preload critical assets
 */
function mydefenselaw_preload_assets() {
    // Only preload font connections - stylesheets are efficiently loaded by wp_enqueue_style
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php
}
add_action('wp_head', 'mydefenselaw_preload_assets', 1);
