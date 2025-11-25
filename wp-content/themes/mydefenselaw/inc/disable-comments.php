<?php
/**
 * Disable Comments Functionality
 *
 * Completely removes the comments feature from WordPress admin and frontend.
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Disable support for comments and trackbacks in all post types
 */
function mydefenselaw_disable_comments_post_types_support() {
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'mydefenselaw_disable_comments_post_types_support');

/**
 * Close comments on the front-end
 */
function mydefenselaw_disable_comments_status() {
    return false;
}
add_filter('comments_open', 'mydefenselaw_disable_comments_status', 20, 2);
add_filter('pings_open', 'mydefenselaw_disable_comments_status', 20, 2);

/**
 * Hide existing comments
 */
function mydefenselaw_disable_comments_hide_existing($comments) {
    return array();
}
add_filter('comments_array', 'mydefenselaw_disable_comments_hide_existing', 10, 2);

/**
 * Remove Comments page from admin menu
 */
function mydefenselaw_disable_comments_admin_menu() {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'mydefenselaw_disable_comments_admin_menu');

/**
 * Remove comments links from admin bar
 */
function mydefenselaw_disable_comments_admin_bar() {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
}
add_action('init', 'mydefenselaw_disable_comments_admin_bar');

/**
 * Remove comments metabox from dashboard
 */
function mydefenselaw_disable_comments_dashboard() {
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'mydefenselaw_disable_comments_dashboard');

/**
 * Remove comments column from posts/pages list tables
 */
function mydefenselaw_disable_comments_columns($columns) {
    unset($columns['comments']);
    return $columns;
}
add_filter('manage_posts_columns', 'mydefenselaw_disable_comments_columns');
add_filter('manage_pages_columns', 'mydefenselaw_disable_comments_columns');

/**
 * Redirect any direct access to edit-comments.php
 */
function mydefenselaw_disable_comments_admin_redirect() {
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_safe_redirect(admin_url());
        exit;
    }
}
add_action('admin_init', 'mydefenselaw_disable_comments_admin_redirect');

/**
 * Remove Discussion settings submenu
 */
function mydefenselaw_remove_discussion_submenu() {
    remove_submenu_page('options-general.php', 'options-discussion.php');
}
add_action('admin_menu', 'mydefenselaw_remove_discussion_submenu');

/**
 * Remove comment-reply script
 */
function mydefenselaw_disable_comment_reply_script() {
    wp_dequeue_script('comment-reply');
}
add_action('wp_enqueue_scripts', 'mydefenselaw_disable_comment_reply_script');

/**
 * Remove X-Pingback header
 */
function mydefenselaw_disable_x_pingback($headers) {
    unset($headers['X-Pingback']);
    return $headers;
}
add_filter('wp_headers', 'mydefenselaw_disable_x_pingback');

/**
 * Disable XML-RPC pingback method
 */
function mydefenselaw_disable_xmlrpc_pingback($methods) {
    unset($methods['pingback.ping']);
    unset($methods['pingback.extensions.getPingbacks']);
    return $methods;
}
add_filter('xmlrpc_methods', 'mydefenselaw_disable_xmlrpc_pingback');

/**
 * Remove comment feed links from head
 */
function mydefenselaw_remove_comment_feed_links() {
    remove_action('wp_head', 'feed_links_extra', 3);
}
add_action('init', 'mydefenselaw_remove_comment_feed_links');
