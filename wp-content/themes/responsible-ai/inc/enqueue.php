<?php
/**
 * Enqueue Additional Scripts and Styles
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Preload critical assets
 */
function responsibleai_preload_assets() {
    // Preconnect to Google Fonts
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";

    // Preconnect to CDN
    echo '<link rel="preconnect" href="https://cdnjs.cloudflare.com">' . "\n";
    echo '<link rel="preconnect" href="https://cdn.jsdelivr.net">' . "\n";
}
add_action('wp_head', 'responsibleai_preload_assets', 1);

/**
 * Add defer/async attributes to scripts
 */
function responsibleai_script_loader_tag($tag, $handle, $src) {
    // Scripts to defer
    $defer_scripts = array('responsibleai-script', 'swiper');

    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'responsibleai_script_loader_tag', 10, 3);

/**
 * Enqueue admin styles
 */
function responsibleai_admin_styles() {
    wp_enqueue_style(
        'responsibleai-admin',
        get_template_directory_uri() . '/assets/css/admin.css',
        array(),
        RESPONSIBLEAI_VERSION
    );
}
add_action('admin_enqueue_scripts', 'responsibleai_admin_styles');

/**
 * Add editor styles
 */
function responsibleai_editor_styles() {
    add_editor_style('assets/css/editor-style.css');
}
add_action('after_setup_theme', 'responsibleai_editor_styles');

/**
 * Get nonce for specific action
 */
function responsibleai_get_nonce($action = 'responsibleai_contact') {
    return wp_create_nonce($action);
}

/**
 * Verify nonce for specific action
 */
function responsibleai_verify_nonce($nonce, $action = 'responsibleai_contact') {
    return wp_verify_nonce($nonce, $action);
}
