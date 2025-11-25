<?php
/**
 * Client Portal Custom Post Types
 *
 * Manages all client portal post types and taxonomies
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Client Portal CPTs
 */
function mydefenselaw_portal_register_post_types() {
    // Client Case CPT
    register_post_type('client_case', array(
        'labels' => array(
            'name'                  => __('Client Cases', 'mydefenselaw'),
            'singular_name'         => __('Client Case', 'mydefenselaw'),
            'menu_name'             => __('Cases', 'mydefenselaw'),
            'add_new'               => __('Add New', 'mydefenselaw'),
            'add_new_item'          => __('Add New Case', 'mydefenselaw'),
            'edit_item'             => __('Edit Case', 'mydefenselaw'),
            'new_item'              => __('New Case', 'mydefenselaw'),
            'view_item'             => __('View Case', 'mydefenselaw'),
            'search_items'          => __('Search Cases', 'mydefenselaw'),
            'not_found'             => __('No cases found', 'mydefenselaw'),
            'not_found_in_trash'    => __('No cases found in trash', 'mydefenselaw'),
            'all_items'             => __('All Cases', 'mydefenselaw'),
        ),
        'public'              => false,
        'publicly_queryable'  => false,
        'show_ui'             => true,
        'show_in_menu'        => 'client-portal',
        'show_in_nav_menus'   => false,
        'show_in_rest'        => false,
        'query_var'           => false,
        'capability_type'     => 'post',
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => null,
        'menu_icon'           => 'dashicons-portfolio',
        'supports'            => array('title', 'revisions'),
    ));

    // Client Document CPT
    register_post_type('client_document', array(
        'labels' => array(
            'name'                  => __('Client Documents', 'mydefenselaw'),
            'singular_name'         => __('Client Document', 'mydefenselaw'),
            'menu_name'             => __('Documents', 'mydefenselaw'),
            'add_new'               => __('Add New', 'mydefenselaw'),
            'add_new_item'          => __('Add New Document', 'mydefenselaw'),
            'edit_item'             => __('Edit Document', 'mydefenselaw'),
            'new_item'              => __('New Document', 'mydefenselaw'),
            'view_item'             => __('View Document', 'mydefenselaw'),
            'search_items'          => __('Search Documents', 'mydefenselaw'),
            'not_found'             => __('No documents found', 'mydefenselaw'),
            'not_found_in_trash'    => __('No documents found in trash', 'mydefenselaw'),
            'all_items'             => __('All Documents', 'mydefenselaw'),
        ),
        'public'              => false,
        'publicly_queryable'  => false,
        'show_ui'             => true,
        'show_in_menu'        => 'client-portal',
        'show_in_nav_menus'   => false,
        'show_in_rest'        => false,
        'query_var'           => false,
        'capability_type'     => 'post',
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => null,
        'menu_icon'           => 'dashicons-media-document',
        'supports'            => array('title'),
    ));

    // Client Message CPT
    register_post_type('client_message', array(
        'labels' => array(
            'name'                  => __('Client Messages', 'mydefenselaw'),
            'singular_name'         => __('Client Message', 'mydefenselaw'),
            'menu_name'             => __('Messages', 'mydefenselaw'),
            'add_new'               => __('Add New', 'mydefenselaw'),
            'add_new_item'          => __('Add New Message', 'mydefenselaw'),
            'edit_item'             => __('Edit Message', 'mydefenselaw'),
            'new_item'              => __('New Message', 'mydefenselaw'),
            'view_item'             => __('View Message', 'mydefenselaw'),
            'search_items'          => __('Search Messages', 'mydefenselaw'),
            'not_found'             => __('No messages found', 'mydefenselaw'),
            'not_found_in_trash'    => __('No messages found in trash', 'mydefenselaw'),
            'all_items'             => __('All Messages', 'mydefenselaw'),
        ),
        'public'              => false,
        'publicly_queryable'  => false,
        'show_ui'             => true,
        'show_in_menu'        => 'client-portal',
        'show_in_nav_menus'   => false,
        'show_in_rest'        => false,
        'query_var'           => false,
        'capability_type'     => 'post',
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => null,
        'menu_icon'           => 'dashicons-email-alt',
        'supports'            => array('title', 'editor'),
    ));

    // Document Folder CPT
    register_post_type('client_folder', array(
        'labels' => array(
            'name'                  => __('Document Folders', 'mydefenselaw'),
            'singular_name'         => __('Document Folder', 'mydefenselaw'),
            'menu_name'             => __('Folders', 'mydefenselaw'),
            'add_new'               => __('Add New', 'mydefenselaw'),
            'add_new_item'          => __('Add New Folder', 'mydefenselaw'),
            'edit_item'             => __('Edit Folder', 'mydefenselaw'),
            'new_item'              => __('New Folder', 'mydefenselaw'),
            'view_item'             => __('View Folder', 'mydefenselaw'),
            'search_items'          => __('Search Folders', 'mydefenselaw'),
            'not_found'             => __('No folders found', 'mydefenselaw'),
            'not_found_in_trash'    => __('No folders found in trash', 'mydefenselaw'),
            'all_items'             => __('All Folders', 'mydefenselaw'),
        ),
        'public'              => false,
        'publicly_queryable'  => false,
        'show_ui'             => true,
        'show_in_menu'        => 'client-portal',
        'show_in_nav_menus'   => false,
        'show_in_rest'        => false,
        'query_var'           => false,
        'capability_type'     => 'post',
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => null,
        'menu_icon'           => 'dashicons-category',
        'supports'            => array('title'),
    ));

    // Portal Activity CPT (Audit Logging)
    register_post_type('portal_activity', array(
        'labels' => array(
            'name'                  => __('Portal Activity', 'mydefenselaw'),
            'singular_name'         => __('Activity Log', 'mydefenselaw'),
            'menu_name'             => __('Activity Log', 'mydefenselaw'),
            'add_new'               => __('Add New', 'mydefenselaw'),
            'add_new_item'          => __('Add New Log Entry', 'mydefenselaw'),
            'edit_item'             => __('View Log Entry', 'mydefenselaw'),
            'new_item'              => __('New Log Entry', 'mydefenselaw'),
            'view_item'             => __('View Log Entry', 'mydefenselaw'),
            'search_items'          => __('Search Activity', 'mydefenselaw'),
            'not_found'             => __('No activity found', 'mydefenselaw'),
            'not_found_in_trash'    => __('No activity found in trash', 'mydefenselaw'),
            'all_items'             => __('All Activity', 'mydefenselaw'),
        ),
        'public'              => false,
        'publicly_queryable'  => false,
        'show_ui'             => true,
        'show_in_menu'        => 'client-portal',
        'show_in_nav_menus'   => false,
        'show_in_rest'        => false,
        'query_var'           => false,
        'capability_type'     => 'post',
        'capabilities'        => array(
            'create_posts' => 'do_not_allow', // Disable manual creation
        ),
        'map_meta_cap'        => true,
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => null,
        'menu_icon'           => 'dashicons-visibility',
        'supports'            => array('title'),
    ));
}
add_action('init', 'mydefenselaw_portal_register_post_types');

/**
 * Register Client Portal Taxonomies
 */
function mydefenselaw_portal_register_taxonomies() {
    // Case Status Taxonomy
    register_taxonomy('case_status', array('client_case'), array(
        'labels' => array(
            'name'              => __('Case Statuses', 'mydefenselaw'),
            'singular_name'     => __('Case Status', 'mydefenselaw'),
            'search_items'      => __('Search Statuses', 'mydefenselaw'),
            'all_items'         => __('All Statuses', 'mydefenselaw'),
            'edit_item'         => __('Edit Status', 'mydefenselaw'),
            'update_item'       => __('Update Status', 'mydefenselaw'),
            'add_new_item'      => __('Add New Status', 'mydefenselaw'),
            'new_item_name'     => __('New Status Name', 'mydefenselaw'),
            'menu_name'         => __('Case Status', 'mydefenselaw'),
        ),
        'hierarchical'      => false,
        'show_ui'           => true,
        'show_in_menu'      => true,
        'show_admin_column' => true,
        'show_in_rest'      => false,
        'query_var'         => true,
        'rewrite'           => false,
    ));

    // Document Type Taxonomy
    register_taxonomy('document_type', array('client_document'), array(
        'labels' => array(
            'name'              => __('Document Types', 'mydefenselaw'),
            'singular_name'     => __('Document Type', 'mydefenselaw'),
            'search_items'      => __('Search Types', 'mydefenselaw'),
            'all_items'         => __('All Types', 'mydefenselaw'),
            'edit_item'         => __('Edit Type', 'mydefenselaw'),
            'update_item'       => __('Update Type', 'mydefenselaw'),
            'add_new_item'      => __('Add New Type', 'mydefenselaw'),
            'new_item_name'     => __('New Type Name', 'mydefenselaw'),
            'menu_name'         => __('Document Types', 'mydefenselaw'),
        ),
        'hierarchical'      => false,
        'show_ui'           => true,
        'show_in_menu'      => true,
        'show_admin_column' => true,
        'show_in_rest'      => false,
        'query_var'         => true,
        'rewrite'           => false,
    ));

    // Message Thread Taxonomy
    register_taxonomy('message_thread', array('client_message'), array(
        'labels' => array(
            'name'              => __('Message Threads', 'mydefenselaw'),
            'singular_name'     => __('Message Thread', 'mydefenselaw'),
            'search_items'      => __('Search Threads', 'mydefenselaw'),
            'all_items'         => __('All Threads', 'mydefenselaw'),
            'edit_item'         => __('Edit Thread', 'mydefenselaw'),
            'update_item'       => __('Update Thread', 'mydefenselaw'),
            'add_new_item'      => __('Add New Thread', 'mydefenselaw'),
            'new_item_name'     => __('New Thread Name', 'mydefenselaw'),
            'menu_name'         => __('Message Threads', 'mydefenselaw'),
        ),
        'hierarchical'      => false,
        'show_ui'           => true,
        'show_in_menu'      => true,
        'show_admin_column' => true,
        'show_in_rest'      => false,
        'query_var'         => true,
        'rewrite'           => false,
    ));
}
add_action('init', 'mydefenselaw_portal_register_taxonomies');

/**
 * Create default taxonomy terms
 */
function mydefenselaw_portal_create_default_terms() {
    // Create default case statuses with colors
    $case_statuses = array(
        'active'  => array('label' => __('Active', 'mydefenselaw'), 'color' => '#00a32a'),
        'pending' => array('label' => __('Pending', 'mydefenselaw'), 'color' => '#f0b849'),
        'on-hold' => array('label' => __('On Hold', 'mydefenselaw'), 'color' => '#787c82'),
        'closed'  => array('label' => __('Closed', 'mydefenselaw'), 'color' => '#2271b1'),
    );

    foreach ($case_statuses as $slug => $data) {
        if (!term_exists($slug, 'case_status')) {
            $term = wp_insert_term($data['label'], 'case_status', array('slug' => $slug));
            if (!is_wp_error($term)) {
                update_term_meta($term['term_id'], 'status_color', $data['color']);
            }
        }
    }
}
add_action('init', 'mydefenselaw_portal_create_default_terms', 100);

/**
 * Flush rewrite rules on theme activation
 */
function mydefenselaw_portal_rewrite_flush() {
    mydefenselaw_portal_register_post_types();
    mydefenselaw_portal_register_taxonomies();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'mydefenselaw_portal_rewrite_flush');

/**
 * Add custom columns to Client Case admin list
 */
function mydefenselaw_portal_case_columns($columns) {
    $new_columns = array(
        'cb'             => $columns['cb'],
        'title'          => __('Case Title', 'mydefenselaw'),
        'case_status'    => __('Status', 'mydefenselaw'),
        'client_name'    => __('Client Name', 'mydefenselaw'),
        'attorney'       => __('Attorney', 'mydefenselaw'),
        'court_date'     => __('Next Court Date', 'mydefenselaw'),
        'date'           => __('Created', 'mydefenselaw'),
    );
    return $new_columns;
}
add_filter('manage_client_case_posts_columns', 'mydefenselaw_portal_case_columns');

/**
 * Populate custom columns for Client Case
 */
function mydefenselaw_portal_case_column_content($column, $post_id) {
    switch ($column) {
        case 'case_status':
            $terms = get_the_terms($post_id, 'case_status');
            if ($terms && !is_wp_error($terms)) {
                $term = $terms[0];
                $color = get_term_meta($term->term_id, 'status_color', true);
                $color = $color ?: '#787c82';
                echo '<span style="display:inline-block;padding:3px 10px;border-radius:3px;background:' . esc_attr($color) . ';color:#fff;font-weight:500;font-size:12px;">' . esc_html($term->name) . '</span>';
            } else {
                echo '<span style="color:#999;">—</span>';
            }
            break;

        case 'client_name':
            $client_name = get_post_meta($post_id, '_client_name', true);
            echo $client_name ? esc_html($client_name) : '<span style="color:#999;">—</span>';
            break;

        case 'attorney':
            $attorney_id = get_post_meta($post_id, '_attorney_id', true);
            if ($attorney_id) {
                $attorney = get_post($attorney_id);
                echo $attorney ? esc_html($attorney->post_title) : '<span style="color:#999;">—</span>';
            } else {
                echo '<span style="color:#999;">—</span>';
            }
            break;

        case 'court_date':
            $court_date = get_post_meta($post_id, '_next_court_date', true);
            if ($court_date) {
                $timestamp = strtotime($court_date);
                $formatted = date('M j, Y', $timestamp);
                $today = strtotime('today');

                if ($timestamp < $today) {
                    echo '<span style="color:#d63638;">' . esc_html($formatted) . ' (Past)</span>';
                } elseif ($timestamp < strtotime('+7 days')) {
                    echo '<span style="color:#f0b849;font-weight:600;">' . esc_html($formatted) . ' (Soon)</span>';
                } else {
                    echo esc_html($formatted);
                }
            } else {
                echo '<span style="color:#999;">—</span>';
            }
            break;
    }
}
add_action('manage_client_case_posts_custom_column', 'mydefenselaw_portal_case_column_content', 10, 2);

/**
 * Add custom columns to Client Document admin list
 */
function mydefenselaw_portal_document_columns($columns) {
    $new_columns = array(
        'cb'             => $columns['cb'],
        'title'          => __('Document Name', 'mydefenselaw'),
        'document_type'  => __('Type', 'mydefenselaw'),
        'client_name'    => __('Client Name', 'mydefenselaw'),
        'case_link'      => __('Case', 'mydefenselaw'),
        'file_type'      => __('File Type', 'mydefenselaw'),
        'date'           => __('Uploaded', 'mydefenselaw'),
    );
    return $new_columns;
}
add_filter('manage_client_document_posts_columns', 'mydefenselaw_portal_document_columns');

/**
 * Populate custom columns for Client Document
 */
function mydefenselaw_portal_document_column_content($column, $post_id) {
    switch ($column) {
        case 'client_name':
            $client_name = get_post_meta($post_id, '_client_name', true);
            echo $client_name ? esc_html($client_name) : '<span style="color:#999;">—</span>';
            break;

        case 'case_link':
            $case_id = get_post_meta($post_id, '_case_id', true);
            if ($case_id) {
                $case = get_post($case_id);
                if ($case) {
                    echo '<a href="' . esc_url(get_edit_post_link($case_id)) . '">' . esc_html($case->post_title) . '</a>';
                } else {
                    echo '<span style="color:#999;">—</span>';
                }
            } else {
                echo '<span style="color:#999;">—</span>';
            }
            break;

        case 'file_type':
            $file_path = get_post_meta($post_id, '_file_path', true);
            if ($file_path) {
                $extension = strtoupper(pathinfo($file_path, PATHINFO_EXTENSION));
                $colors = array(
                    'PDF'  => '#d63638',
                    'DOC'  => '#2271b1',
                    'DOCX' => '#2271b1',
                    'XLS'  => '#00a32a',
                    'XLSX' => '#00a32a',
                    'JPG'  => '#f0b849',
                    'JPEG' => '#f0b849',
                    'PNG'  => '#f0b849',
                );
                $color = $colors[$extension] ?? '#787c82';
                echo '<span style="display:inline-block;padding:2px 8px;border-radius:3px;background:' . esc_attr($color) . ';color:#fff;font-size:11px;font-weight:600;">' . esc_html($extension) . '</span>';
            } else {
                echo '<span style="color:#999;">—</span>';
            }
            break;
    }
}
add_action('manage_client_document_posts_custom_column', 'mydefenselaw_portal_document_column_content', 10, 2);

/**
 * Add custom columns to Client Message admin list
 */
function mydefenselaw_portal_message_columns($columns) {
    $new_columns = array(
        'cb'             => $columns['cb'],
        'read_status'    => '',
        'title'          => __('Subject', 'mydefenselaw'),
        'client_name'    => __('Client', 'mydefenselaw'),
        'message_from'   => __('From', 'mydefenselaw'),
        'message_thread' => __('Thread', 'mydefenselaw'),
        'date'           => __('Date', 'mydefenselaw'),
    );
    return $new_columns;
}
add_filter('manage_client_message_posts_columns', 'mydefenselaw_portal_message_columns');

/**
 * Populate custom columns for Client Message
 */
function mydefenselaw_portal_message_column_content($column, $post_id) {
    switch ($column) {
        case 'read_status':
            $is_read = get_post_meta($post_id, '_is_read', true);
            if ($is_read) {
                echo '<span class="dashicons dashicons-yes-alt" style="color:#00a32a;" title="Read"></span>';
            } else {
                echo '<span class="dashicons dashicons-marker" style="color:#2271b1;" title="Unread"></span>';
            }
            break;

        case 'client_name':
            $client_name = get_post_meta($post_id, '_client_name', true);
            echo $client_name ? esc_html($client_name) : '<span style="color:#999;">—</span>';
            break;

        case 'message_from':
            $from_type = get_post_meta($post_id, '_from_type', true);
            if ($from_type === 'client') {
                echo '<span style="padding:2px 8px;border-radius:3px;background:#e6f0e6;color:#00a32a;font-size:12px;font-weight:500;">Client</span>';
            } elseif ($from_type === 'staff') {
                echo '<span style="padding:2px 8px;border-radius:3px;background:#dce4ef;color:#2271b1;font-size:12px;font-weight:500;">Staff</span>';
            } else {
                echo '<span style="color:#999;">—</span>';
            }
            break;
    }
}
add_action('manage_client_message_posts_custom_column', 'mydefenselaw_portal_message_column_content', 10, 2);

/**
 * Make columns sortable
 */
function mydefenselaw_portal_case_sortable_columns($columns) {
    $columns['client_name'] = 'client_name';
    $columns['court_date'] = 'court_date';
    return $columns;
}
add_filter('manage_edit-client_case_sortable_columns', 'mydefenselaw_portal_case_sortable_columns');

/**
 * Handle sorting by custom meta
 */
function mydefenselaw_portal_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    $post_type = $query->get('post_type');
    if (!in_array($post_type, array('client_case', 'client_document', 'client_message'))) {
        return;
    }

    $orderby = $query->get('orderby');

    switch ($orderby) {
        case 'client_name':
            $query->set('meta_key', '_client_name');
            $query->set('orderby', 'meta_value');
            break;
        case 'court_date':
            $query->set('meta_key', '_next_court_date');
            $query->set('orderby', 'meta_value');
            break;
    }
}
add_action('pre_get_posts', 'mydefenselaw_portal_orderby');

/**
 * Add admin styles for columns
 */
function mydefenselaw_portal_admin_styles() {
    global $post_type;

    if (!in_array($post_type, array('client_case', 'client_document', 'client_message', 'portal_activity'))) {
        return;
    }
    ?>
    <style>
        .column-read_status { width: 40px; text-align: center; }
        .column-case_status { width: 100px; }
        .column-file_type { width: 80px; }
        .column-message_from { width: 80px; }
        .column-court_date { width: 130px; }

        /* Highlight unread messages */
        tr[data-unread="1"] td {
            background: #f0f6fc !important;
            font-weight: 500;
        }
    </style>
    <?php
}
add_action('admin_head', 'mydefenselaw_portal_admin_styles');

/**
 * Add unread data attribute to message rows
 */
function mydefenselaw_portal_message_row_class($classes, $class, $post_id) {
    if (get_post_type($post_id) === 'client_message') {
        $is_read = get_post_meta($post_id, '_is_read', true);
        if (!$is_read) {
            $classes[] = 'message-unread';
        }
    }
    return $classes;
}
add_filter('post_class', 'mydefenselaw_portal_message_row_class', 10, 3);
