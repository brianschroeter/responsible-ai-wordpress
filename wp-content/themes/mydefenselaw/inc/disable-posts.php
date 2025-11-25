<?php
/**
 * Disable Default Posts Post Type
 *
 * Removes the default 'Posts' post type from the WordPress admin backend.
 * This law firm site uses custom post types (practice_area, attorney, etc.)
 * instead of the default blog posts.
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Remove Posts from Admin Menu
 *
 * Removes the Posts menu item from the admin sidebar.
 */
function mydefenselaw_remove_posts_menu() {
    remove_menu_page('edit.php');
}
add_action('admin_menu', 'mydefenselaw_remove_posts_menu');

/**
 * Remove Posts from Admin Bar
 *
 * Removes the "+ New > Post" option from the admin bar.
 */
function mydefenselaw_remove_posts_from_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_node('new-post');
}
add_action('wp_before_admin_bar_render', 'mydefenselaw_remove_posts_from_admin_bar');

/**
 * Remove Posts-Related Dashboard Widgets
 *
 * Removes Quick Draft and Activity widgets that reference posts.
 */
function mydefenselaw_remove_posts_dashboard_widgets() {
    // Remove Quick Draft widget
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');

    // Remove Recent Drafts (if present)
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
}
add_action('wp_dashboard_setup', 'mydefenselaw_remove_posts_dashboard_widgets');

/**
 * Redirect Posts Admin Pages
 *
 * If someone tries to access the Posts list or edit page directly,
 * redirect them to the dashboard.
 */
function mydefenselaw_redirect_posts_admin_pages() {
    global $pagenow;

    // Check if we're on post-related admin pages for the 'post' post type
    if ($pagenow === 'edit.php' || $pagenow === 'post-new.php') {
        // Only redirect if no post_type is set (defaults to 'post') or explicitly 'post'
        if (!isset($_GET['post_type']) || $_GET['post_type'] === 'post') {
            wp_redirect(admin_url('index.php'));
            exit;
        }
    }

    // Also redirect single post edit page
    if ($pagenow === 'post.php' && isset($_GET['post'])) {
        $post_id = intval($_GET['post']);
        if ($post_id && get_post_type($post_id) === 'post') {
            wp_redirect(admin_url('index.php'));
            exit;
        }
    }
}
add_action('admin_init', 'mydefenselaw_redirect_posts_admin_pages');

/**
 * Remove Posts from "At a Glance" Dashboard Widget
 *
 * Filters the dashboard glance items to remove post count.
 */
function mydefenselaw_remove_posts_from_glance($items) {
    // Remove posts from At a Glance widget
    foreach ($items as $key => $item) {
        if (strpos($item, 'post-count') !== false) {
            unset($items[$key]);
        }
    }
    return $items;
}
add_filter('dashboard_glance_items', 'mydefenselaw_remove_posts_from_glance', 99);

/**
 * Hide Posts Count in Right Now Widget (Legacy)
 *
 * For older WordPress versions that use Right Now widget.
 */
function mydefenselaw_hide_posts_right_now() {
    ?>
    <style>
        #dashboard_right_now .post-count,
        #dashboard_right_now .comment-count {
            display: none !important;
        }
    </style>
    <?php
}
add_action('admin_head-index.php', 'mydefenselaw_hide_posts_right_now');

/**
 * Remove Posts from Search in Admin
 *
 * Exclude posts post type from admin search results.
 */
function mydefenselaw_exclude_posts_from_admin_search($query) {
    if (is_admin() && $query->is_main_query() && $query->is_search()) {
        $post_types = $query->get('post_type');

        // If searching all post types, exclude 'post'
        if (empty($post_types) || $post_types === 'any') {
            $all_post_types = get_post_types(array('public' => true), 'names');
            unset($all_post_types['post']);
            $query->set('post_type', array_values($all_post_types));
        }
    }
}
add_action('pre_get_posts', 'mydefenselaw_exclude_posts_from_admin_search');
