<?php
/**
 * Custom Post Types Registration
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Custom Post Types
 */
function mydefenselaw_register_post_types() {
    // Practice Areas CPT
    register_post_type('practice_area', array(
        'labels' => array(
            'name'                  => __('Practice Areas', 'mydefenselaw'),
            'singular_name'         => __('Practice Area', 'mydefenselaw'),
            'menu_name'             => __('Practice Areas', 'mydefenselaw'),
            'add_new'               => __('Add New', 'mydefenselaw'),
            'add_new_item'          => __('Add New Practice Area', 'mydefenselaw'),
            'edit_item'             => __('Edit Practice Area', 'mydefenselaw'),
            'new_item'              => __('New Practice Area', 'mydefenselaw'),
            'view_item'             => __('View Practice Area', 'mydefenselaw'),
            'search_items'          => __('Search Practice Areas', 'mydefenselaw'),
            'not_found'             => __('No practice areas found', 'mydefenselaw'),
            'not_found_in_trash'    => __('No practice areas found in trash', 'mydefenselaw'),
            'all_items'             => __('All Practice Areas', 'mydefenselaw'),
            'archives'              => __('Practice Area Archives', 'mydefenselaw'),
        ),
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_rest'        => true,
        'query_var'           => true,
        'rewrite'             => array('slug' => 'practice-areas', 'with_front' => false),
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-portfolio',
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions'),
    ));

    // Attorneys CPT
    register_post_type('attorney', array(
        'labels' => array(
            'name'                  => __('Attorneys', 'mydefenselaw'),
            'singular_name'         => __('Attorney', 'mydefenselaw'),
            'menu_name'             => __('Attorneys', 'mydefenselaw'),
            'add_new'               => __('Add New', 'mydefenselaw'),
            'add_new_item'          => __('Add New Attorney', 'mydefenselaw'),
            'edit_item'             => __('Edit Attorney', 'mydefenselaw'),
            'new_item'              => __('New Attorney', 'mydefenselaw'),
            'view_item'             => __('View Attorney', 'mydefenselaw'),
            'search_items'          => __('Search Attorneys', 'mydefenselaw'),
            'not_found'             => __('No attorneys found', 'mydefenselaw'),
            'not_found_in_trash'    => __('No attorneys found in trash', 'mydefenselaw'),
            'all_items'             => __('All Attorneys', 'mydefenselaw'),
            'archives'              => __('Attorney Archives', 'mydefenselaw'),
        ),
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_rest'        => true,
        'query_var'           => true,
        'rewrite'             => array('slug' => 'attorneys', 'with_front' => false),
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'menu_position'       => 6,
        'menu_icon'           => 'dashicons-businessperson',
        'supports'            => array('title', 'thumbnail', 'excerpt', 'revisions'),
    ));

    // Testimonials CPT
    register_post_type('testimonial', array(
        'labels' => array(
            'name'                  => __('Testimonials', 'mydefenselaw'),
            'singular_name'         => __('Testimonial', 'mydefenselaw'),
            'menu_name'             => __('Testimonials', 'mydefenselaw'),
            'add_new'               => __('Add New', 'mydefenselaw'),
            'add_new_item'          => __('Add New Testimonial', 'mydefenselaw'),
            'edit_item'             => __('Edit Testimonial', 'mydefenselaw'),
            'new_item'              => __('New Testimonial', 'mydefenselaw'),
            'view_item'             => __('View Testimonial', 'mydefenselaw'),
            'search_items'          => __('Search Testimonials', 'mydefenselaw'),
            'not_found'             => __('No testimonials found', 'mydefenselaw'),
            'not_found_in_trash'    => __('No testimonials found in trash', 'mydefenselaw'),
            'all_items'             => __('All Testimonials', 'mydefenselaw'),
        ),
        'public'              => false,
        'publicly_queryable'  => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => false,
        'show_in_rest'        => true,
        'query_var'           => false,
        'capability_type'     => 'post',
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => 7,
        'menu_icon'           => 'dashicons-format-quote',
        'supports'            => array('title', 'editor', 'thumbnail'),
    ));

    // Case Results CPT
    register_post_type('case_result', array(
        'labels' => array(
            'name'                  => __('Case Results', 'mydefenselaw'),
            'singular_name'         => __('Case Result', 'mydefenselaw'),
            'menu_name'             => __('Case Results', 'mydefenselaw'),
            'add_new'               => __('Add New', 'mydefenselaw'),
            'add_new_item'          => __('Add New Case Result', 'mydefenselaw'),
            'edit_item'             => __('Edit Case Result', 'mydefenselaw'),
            'new_item'              => __('New Case Result', 'mydefenselaw'),
            'view_item'             => __('View Case Result', 'mydefenselaw'),
            'search_items'          => __('Search Case Results', 'mydefenselaw'),
            'not_found'             => __('No case results found', 'mydefenselaw'),
            'not_found_in_trash'    => __('No case results found in trash', 'mydefenselaw'),
            'all_items'             => __('All Case Results', 'mydefenselaw'),
        ),
        'public'              => false,
        'publicly_queryable'  => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => false,
        'show_in_rest'        => true,
        'query_var'           => false,
        'capability_type'     => 'post',
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => 8,
        'menu_icon'           => 'dashicons-awards',
        'supports'            => array('title', 'editor'),
    ));
}
add_action('init', 'mydefenselaw_register_post_types');

/**
 * Flush rewrite rules on theme activation
 */
function mydefenselaw_rewrite_flush() {
    mydefenselaw_register_post_types();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'mydefenselaw_rewrite_flush');

/**
 * Add custom columns to Practice Areas admin list
 */
function mydefenselaw_practice_area_columns($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['icon'] = __('Icon', 'mydefenselaw');
        }
    }
    return $new_columns;
}
add_filter('manage_practice_area_posts_columns', 'mydefenselaw_practice_area_columns');

/**
 * Populate custom columns for Practice Areas
 */
function mydefenselaw_practice_area_column_content($column, $post_id) {
    if ($column === 'icon') {
        $icon = get_field('practice_area_icon', $post_id);
        if ($icon) {
            echo '<i class="' . esc_attr($icon) . '" style="font-size: 24px;"></i>';
        } else {
            echo '<span style="color: #999;">—</span>';
        }
    }
}
add_action('manage_practice_area_posts_custom_column', 'mydefenselaw_practice_area_column_content', 10, 2);

/**
 * Add custom columns to Attorneys admin list
 */
function mydefenselaw_attorney_columns($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['attorney_title'] = __('Title', 'mydefenselaw');
            $new_columns['email'] = __('Email', 'mydefenselaw');
        }
    }
    return $new_columns;
}
add_filter('manage_attorney_posts_columns', 'mydefenselaw_attorney_columns');

/**
 * Populate custom columns for Attorneys
 */
function mydefenselaw_attorney_column_content($column, $post_id) {
    switch ($column) {
        case 'attorney_title':
            $title = get_field('attorney_title', $post_id);
            echo $title ? esc_html($title) : '<span style="color: #999;">—</span>';
            break;
        case 'email':
            $email = get_field('email', $post_id);
            echo $email ? '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>' : '<span style="color: #999;">—</span>';
            break;
    }
}
add_action('manage_attorney_posts_custom_column', 'mydefenselaw_attorney_column_content', 10, 2);

/**
 * Load Font Awesome in admin for icon display
 */
function mydefenselaw_admin_font_awesome() {
    $screen = get_current_screen();
    if ($screen && in_array($screen->post_type, array('practice_area', 'attorney'))) {
        wp_enqueue_style(
            'font-awesome-admin',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
            array(),
            '6.4.0'
        );
    }
}
add_action('admin_enqueue_scripts', 'mydefenselaw_admin_font_awesome');
