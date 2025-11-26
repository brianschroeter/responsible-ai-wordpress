<?php
/**
 * Custom Post Types Registration
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Custom Post Types
 */
function responsibleai_register_post_types() {

    // Events CPT
    register_post_type('rai_event', array(
        'labels' => array(
            'name'               => __('Events', 'responsible-ai'),
            'singular_name'      => __('Event', 'responsible-ai'),
            'menu_name'          => __('Events', 'responsible-ai'),
            'add_new'            => __('Add New', 'responsible-ai'),
            'add_new_item'       => __('Add New Event', 'responsible-ai'),
            'edit_item'          => __('Edit Event', 'responsible-ai'),
            'new_item'           => __('New Event', 'responsible-ai'),
            'view_item'          => __('View Event', 'responsible-ai'),
            'search_items'       => __('Search Events', 'responsible-ai'),
            'not_found'          => __('No events found', 'responsible-ai'),
            'not_found_in_trash' => __('No events found in trash', 'responsible-ai'),
        ),
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => array('slug' => 'events', 'with_front' => false),
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-calendar-alt',
        'supports'            => array('title', 'thumbnail', 'excerpt', 'revisions'),
        'show_in_rest'        => false, // Use Classic Editor with ACF
    ));

    // Resources CPT
    register_post_type('rai_resource', array(
        'labels' => array(
            'name'               => __('Resources', 'responsible-ai'),
            'singular_name'      => __('Resource', 'responsible-ai'),
            'menu_name'          => __('Resources', 'responsible-ai'),
            'add_new'            => __('Add New', 'responsible-ai'),
            'add_new_item'       => __('Add New Resource', 'responsible-ai'),
            'edit_item'          => __('Edit Resource', 'responsible-ai'),
            'new_item'           => __('New Resource', 'responsible-ai'),
            'view_item'          => __('View Resource', 'responsible-ai'),
            'search_items'       => __('Search Resources', 'responsible-ai'),
            'not_found'          => __('No resources found', 'responsible-ai'),
            'not_found_in_trash' => __('No resources found in trash', 'responsible-ai'),
        ),
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => array('slug' => 'resources', 'with_front' => false),
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'menu_position'       => 6,
        'menu_icon'           => 'dashicons-media-document',
        'supports'            => array('title', 'thumbnail', 'excerpt', 'revisions'),
        'show_in_rest'        => false,
    ));

    // Case Studies CPT
    register_post_type('case_study', array(
        'labels' => array(
            'name'               => __('Case Studies', 'responsible-ai'),
            'singular_name'      => __('Case Study', 'responsible-ai'),
            'menu_name'          => __('Case Studies', 'responsible-ai'),
            'add_new'            => __('Add New', 'responsible-ai'),
            'add_new_item'       => __('Add New Case Study', 'responsible-ai'),
            'edit_item'          => __('Edit Case Study', 'responsible-ai'),
            'new_item'           => __('New Case Study', 'responsible-ai'),
            'view_item'          => __('View Case Study', 'responsible-ai'),
            'search_items'       => __('Search Case Studies', 'responsible-ai'),
            'not_found'          => __('No case studies found', 'responsible-ai'),
            'not_found_in_trash' => __('No case studies found in trash', 'responsible-ai'),
        ),
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => array('slug' => 'case-studies', 'with_front' => false),
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'menu_position'       => 7,
        'menu_icon'           => 'dashicons-analytics',
        'supports'            => array('title', 'thumbnail', 'excerpt', 'revisions'),
        'show_in_rest'        => false,
    ));
}
add_action('init', 'responsibleai_register_post_types');

/**
 * Register Custom Taxonomies
 */
function responsibleai_register_taxonomies() {

    // Event Type Taxonomy
    register_taxonomy('event_type', 'rai_event', array(
        'labels' => array(
            'name'              => __('Event Types', 'responsible-ai'),
            'singular_name'     => __('Event Type', 'responsible-ai'),
            'search_items'      => __('Search Event Types', 'responsible-ai'),
            'all_items'         => __('All Event Types', 'responsible-ai'),
            'edit_item'         => __('Edit Event Type', 'responsible-ai'),
            'update_item'       => __('Update Event Type', 'responsible-ai'),
            'add_new_item'      => __('Add New Event Type', 'responsible-ai'),
            'new_item_name'     => __('New Event Type Name', 'responsible-ai'),
            'menu_name'         => __('Event Types', 'responsible-ai'),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'event-type'),
        'show_in_rest'      => true,
    ));

    // Resource Type Taxonomy
    register_taxonomy('resource_type', 'rai_resource', array(
        'labels' => array(
            'name'              => __('Resource Types', 'responsible-ai'),
            'singular_name'     => __('Resource Type', 'responsible-ai'),
            'search_items'      => __('Search Resource Types', 'responsible-ai'),
            'all_items'         => __('All Resource Types', 'responsible-ai'),
            'edit_item'         => __('Edit Resource Type', 'responsible-ai'),
            'update_item'       => __('Update Resource Type', 'responsible-ai'),
            'add_new_item'      => __('Add New Resource Type', 'responsible-ai'),
            'new_item_name'     => __('New Resource Type Name', 'responsible-ai'),
            'menu_name'         => __('Resource Types', 'responsible-ai'),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'resource-type'),
        'show_in_rest'      => true,
    ));

    // Resource Topic Taxonomy
    register_taxonomy('resource_topic', 'rai_resource', array(
        'labels' => array(
            'name'              => __('Topics', 'responsible-ai'),
            'singular_name'     => __('Topic', 'responsible-ai'),
            'search_items'      => __('Search Topics', 'responsible-ai'),
            'all_items'         => __('All Topics', 'responsible-ai'),
            'edit_item'         => __('Edit Topic', 'responsible-ai'),
            'update_item'       => __('Update Topic', 'responsible-ai'),
            'add_new_item'      => __('Add New Topic', 'responsible-ai'),
            'new_item_name'     => __('New Topic Name', 'responsible-ai'),
            'menu_name'         => __('Topics', 'responsible-ai'),
        ),
        'hierarchical'      => false,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'topic'),
        'show_in_rest'      => true,
    ));

    // Industry Taxonomy (for Case Studies)
    register_taxonomy('industry', 'case_study', array(
        'labels' => array(
            'name'              => __('Industries', 'responsible-ai'),
            'singular_name'     => __('Industry', 'responsible-ai'),
            'search_items'      => __('Search Industries', 'responsible-ai'),
            'all_items'         => __('All Industries', 'responsible-ai'),
            'edit_item'         => __('Edit Industry', 'responsible-ai'),
            'update_item'       => __('Update Industry', 'responsible-ai'),
            'add_new_item'      => __('Add New Industry', 'responsible-ai'),
            'new_item_name'     => __('New Industry Name', 'responsible-ai'),
            'menu_name'         => __('Industries', 'responsible-ai'),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'industry'),
        'show_in_rest'      => true,
    ));
}
add_action('init', 'responsibleai_register_taxonomies');

/**
 * Add Custom Admin Columns for Events
 */
function responsibleai_event_columns($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['event_date'] = __('Event Date', 'responsible-ai');
            $new_columns['event_format'] = __('Format', 'responsible-ai');
        }
    }
    return $new_columns;
}
add_filter('manage_rai_event_posts_columns', 'responsibleai_event_columns');

/**
 * Populate Custom Admin Columns for Events
 */
function responsibleai_event_column_content($column, $post_id) {
    if ($column === 'event_date') {
        $date = get_field('event_date', $post_id);
        echo $date ? esc_html($date) : '&mdash;';
    }
    if ($column === 'event_format') {
        $format = get_field('event_format', $post_id);
        echo $format ? esc_html($format) : '&mdash;';
    }
}
add_action('manage_rai_event_posts_custom_column', 'responsibleai_event_column_content', 10, 2);

/**
 * Make Event Date Column Sortable
 */
function responsibleai_sortable_event_columns($columns) {
    $columns['event_date'] = 'event_date';
    return $columns;
}
add_filter('manage_edit-rai_event_sortable_columns', 'responsibleai_sortable_event_columns');

/**
 * Add Custom Admin Columns for Resources
 */
function responsibleai_resource_columns($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['resource_format'] = __('Format', 'responsible-ai');
            $new_columns['featured'] = __('Featured', 'responsible-ai');
        }
    }
    return $new_columns;
}
add_filter('manage_rai_resource_posts_columns', 'responsibleai_resource_columns');

/**
 * Populate Custom Admin Columns for Resources
 */
function responsibleai_resource_column_content($column, $post_id) {
    if ($column === 'resource_format') {
        $format = get_field('resource_format', $post_id);
        echo $format ? esc_html($format) : '&mdash;';
    }
    if ($column === 'featured') {
        $featured = get_field('featured', $post_id);
        echo $featured ? '<span class="dashicons dashicons-star-filled" style="color:#f0b849;"></span>' : '&mdash;';
    }
}
add_action('manage_rai_resource_posts_custom_column', 'responsibleai_resource_column_content', 10, 2);

/**
 * Flush rewrite rules on theme activation
 */
function responsibleai_rewrite_flush() {
    responsibleai_register_post_types();
    responsibleai_register_taxonomies();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'responsibleai_rewrite_flush');
