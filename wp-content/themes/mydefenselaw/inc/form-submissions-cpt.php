<?php
/**
 * Form Submissions Custom Post Type
 *
 * Manages all contact form submissions in WordPress admin
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Form Submissions CPT
 */
function mydefenselaw_register_form_submissions_cpt() {
    register_post_type('form_submission', array(
        'labels' => array(
            'name'                  => __('Form Submissions', 'mydefenselaw'),
            'singular_name'         => __('Form Submission', 'mydefenselaw'),
            'menu_name'             => __('Form Submissions', 'mydefenselaw'),
            'add_new'               => __('Add New', 'mydefenselaw'),
            'add_new_item'          => __('Add New Submission', 'mydefenselaw'),
            'edit_item'             => __('View Submission', 'mydefenselaw'),
            'new_item'              => __('New Submission', 'mydefenselaw'),
            'view_item'             => __('View Submission', 'mydefenselaw'),
            'search_items'          => __('Search Submissions', 'mydefenselaw'),
            'not_found'             => __('No submissions found', 'mydefenselaw'),
            'not_found_in_trash'    => __('No submissions found in trash', 'mydefenselaw'),
            'all_items'             => __('All Submissions', 'mydefenselaw'),
        ),
        'public'              => false,
        'publicly_queryable'  => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
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
        'menu_position'       => 25,
        'menu_icon'           => 'dashicons-email-alt',
        'supports'            => array('title'),
    ));
}
add_action('init', 'mydefenselaw_register_form_submissions_cpt');

/**
 * Define submission statuses
 */
function mydefenselaw_get_submission_statuses() {
    return array(
        'new'        => __('New', 'mydefenselaw'),
        'read'       => __('Read', 'mydefenselaw'),
        'contacted'  => __('Contacted', 'mydefenselaw'),
        'converted'  => __('Converted', 'mydefenselaw'),
        'spam'       => __('Spam', 'mydefenselaw'),
        'archived'   => __('Archived', 'mydefenselaw'),
    );
}

/**
 * Get form sources
 */
function mydefenselaw_get_form_sources() {
    return array(
        'homepage'     => __('Homepage Contact', 'mydefenselaw'),
        'modal'        => __('Consultation Modal', 'mydefenselaw'),
        'contact_page' => __('Contact Page', 'mydefenselaw'),
    );
}

/**
 * Get legal issue labels
 */
function mydefenselaw_get_legal_issues() {
    return array(
        'civil-litigation'      => __('Civil Defense Litigation', 'mydefenselaw'),
        'consumer-protection'   => __('Consumer Protection', 'mydefenselaw'),
        'family-law'            => __('Family Law', 'mydefenselaw'),
        'bankruptcy'            => __('Bankruptcy', 'mydefenselaw'),
        'contract-dispute'      => __('Contract Law', 'mydefenselaw'),
        'real-estate'           => __('Real Estate Law', 'mydefenselaw'),
        'landlord-tenant'       => __('Landlord/Tenant Issues', 'mydefenselaw'),
        'intellectual-property' => __('Intellectual Property', 'mydefenselaw'),
        'traffic-tickets'       => __('Traffic Tickets', 'mydefenselaw'),
        'other'                 => __('Other', 'mydefenselaw'),
    );
}

/**
 * Add custom columns to admin list
 */
function mydefenselaw_submission_columns($columns) {
    $new_columns = array(
        'cb'           => $columns['cb'],
        'status_icon'  => '',
        'title'        => __('Name', 'mydefenselaw'),
        'email'        => __('Email', 'mydefenselaw'),
        'phone'        => __('Phone', 'mydefenselaw'),
        'legal_issue'  => __('Legal Issue', 'mydefenselaw'),
        'form_source'  => __('Source', 'mydefenselaw'),
        'is_urgent'    => __('Urgent', 'mydefenselaw'),
        'sub_status'   => __('Status', 'mydefenselaw'),
        'date'         => __('Date', 'mydefenselaw'),
    );
    return $new_columns;
}
add_filter('manage_form_submission_posts_columns', 'mydefenselaw_submission_columns');

/**
 * Populate custom columns
 */
function mydefenselaw_submission_column_content($column, $post_id) {
    $statuses = mydefenselaw_get_submission_statuses();
    $sources = mydefenselaw_get_form_sources();
    $legal_issues = mydefenselaw_get_legal_issues();

    switch ($column) {
        case 'status_icon':
            $status = get_post_meta($post_id, '_submission_status', true) ?: 'new';
            $is_urgent = get_post_meta($post_id, '_is_urgent', true);

            $colors = array(
                'new'       => '#2271b1',
                'read'      => '#72aee6',
                'contacted' => '#f0b849',
                'converted' => '#00a32a',
                'spam'      => '#d63638',
                'archived'  => '#787c82',
            );
            $color = $colors[$status] ?? '#787c82';

            echo '<span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:' . esc_attr($color) . ';" title="' . esc_attr($statuses[$status] ?? $status) . '"></span>';
            if ($is_urgent) {
                echo ' <span style="color:#d63638;" title="Urgent"><span class="dashicons dashicons-warning"></span></span>';
            }
            break;

        case 'email':
            $email = get_post_meta($post_id, '_email', true);
            if ($email) {
                echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
            } else {
                echo '<span style="color:#999;">—</span>';
            }
            break;

        case 'phone':
            $phone = get_post_meta($post_id, '_phone', true);
            if ($phone) {
                echo '<a href="tel:' . esc_attr(preg_replace('/[^0-9+]/', '', $phone)) . '">' . esc_html($phone) . '</a>';
            } else {
                echo '<span style="color:#999;">—</span>';
            }
            break;

        case 'legal_issue':
            $issue = get_post_meta($post_id, '_legal_issue', true);
            echo esc_html($legal_issues[$issue] ?? $issue ?: '—');
            break;

        case 'form_source':
            $source = get_post_meta($post_id, '_form_source', true);
            $source_colors = array(
                'homepage'     => '#dce4ef',
                'modal'        => '#e6f0e6',
                'contact_page' => '#f9f0e4',
            );
            $bg = $source_colors[$source] ?? '#f0f0f0';
            echo '<span style="background:' . esc_attr($bg) . ';padding:2px 8px;border-radius:3px;font-size:12px;">' . esc_html($sources[$source] ?? $source ?: '—') . '</span>';
            break;

        case 'is_urgent':
            $is_urgent = get_post_meta($post_id, '_is_urgent', true);
            if ($is_urgent) {
                echo '<span style="color:#d63638;font-weight:bold;">Yes</span>';
            } else {
                echo '<span style="color:#999;">No</span>';
            }
            break;

        case 'sub_status':
            $status = get_post_meta($post_id, '_submission_status', true) ?: 'new';
            $status_label = $statuses[$status] ?? $status;
            $colors = array(
                'new'       => '#2271b1',
                'read'      => '#72aee6',
                'contacted' => '#f0b849',
                'converted' => '#00a32a',
                'spam'      => '#d63638',
                'archived'  => '#787c82',
            );
            $color = $colors[$status] ?? '#787c82';
            echo '<span style="color:' . esc_attr($color) . ';font-weight:500;">' . esc_html($status_label) . '</span>';
            break;
    }
}
add_action('manage_form_submission_posts_custom_column', 'mydefenselaw_submission_column_content', 10, 2);

/**
 * Make columns sortable
 */
function mydefenselaw_submission_sortable_columns($columns) {
    $columns['sub_status'] = 'sub_status';
    $columns['legal_issue'] = 'legal_issue';
    $columns['form_source'] = 'form_source';
    $columns['is_urgent'] = 'is_urgent';
    return $columns;
}
add_filter('manage_edit-form_submission_sortable_columns', 'mydefenselaw_submission_sortable_columns');

/**
 * Handle sorting by custom meta
 */
function mydefenselaw_submission_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->get('post_type') !== 'form_submission') {
        return;
    }

    $orderby = $query->get('orderby');

    switch ($orderby) {
        case 'sub_status':
            $query->set('meta_key', '_submission_status');
            $query->set('orderby', 'meta_value');
            break;
        case 'legal_issue':
            $query->set('meta_key', '_legal_issue');
            $query->set('orderby', 'meta_value');
            break;
        case 'form_source':
            $query->set('meta_key', '_form_source');
            $query->set('orderby', 'meta_value');
            break;
        case 'is_urgent':
            $query->set('meta_key', '_is_urgent');
            $query->set('orderby', 'meta_value');
            break;
    }
}
add_action('pre_get_posts', 'mydefenselaw_submission_orderby');

/**
 * Add filter dropdowns
 */
function mydefenselaw_submission_filters($post_type) {
    if ($post_type !== 'form_submission') {
        return;
    }

    $statuses = mydefenselaw_get_submission_statuses();
    $sources = mydefenselaw_get_form_sources();
    $legal_issues = mydefenselaw_get_legal_issues();

    // Status filter
    $current_status = isset($_GET['submission_status']) ? sanitize_text_field($_GET['submission_status']) : '';
    echo '<select name="submission_status">';
    echo '<option value="">' . esc_html__('All Statuses', 'mydefenselaw') . '</option>';
    foreach ($statuses as $value => $label) {
        printf(
            '<option value="%s" %s>%s</option>',
            esc_attr($value),
            selected($current_status, $value, false),
            esc_html($label)
        );
    }
    echo '</select>';

    // Source filter
    $current_source = isset($_GET['form_source']) ? sanitize_text_field($_GET['form_source']) : '';
    echo '<select name="form_source">';
    echo '<option value="">' . esc_html__('All Sources', 'mydefenselaw') . '</option>';
    foreach ($sources as $value => $label) {
        printf(
            '<option value="%s" %s>%s</option>',
            esc_attr($value),
            selected($current_source, $value, false),
            esc_html($label)
        );
    }
    echo '</select>';

    // Legal issue filter
    $current_issue = isset($_GET['legal_issue']) ? sanitize_text_field($_GET['legal_issue']) : '';
    echo '<select name="legal_issue">';
    echo '<option value="">' . esc_html__('All Legal Issues', 'mydefenselaw') . '</option>';
    foreach ($legal_issues as $value => $label) {
        printf(
            '<option value="%s" %s>%s</option>',
            esc_attr($value),
            selected($current_issue, $value, false),
            esc_html($label)
        );
    }
    echo '</select>';

    // Urgent filter
    $current_urgent = isset($_GET['is_urgent']) ? sanitize_text_field($_GET['is_urgent']) : '';
    echo '<select name="is_urgent">';
    echo '<option value="">' . esc_html__('All Urgency', 'mydefenselaw') . '</option>';
    echo '<option value="1"' . selected($current_urgent, '1', false) . '>' . esc_html__('Urgent Only', 'mydefenselaw') . '</option>';
    echo '<option value="0"' . selected($current_urgent, '0', false) . '>' . esc_html__('Not Urgent', 'mydefenselaw') . '</option>';
    echo '</select>';
}
add_action('restrict_manage_posts', 'mydefenselaw_submission_filters');

/**
 * Apply filters to query
 */
function mydefenselaw_submission_filter_query($query) {
    global $pagenow;

    if (!is_admin() || $pagenow !== 'edit.php' || !$query->is_main_query()) {
        return;
    }

    if ($query->get('post_type') !== 'form_submission') {
        return;
    }

    $meta_query = array();

    if (!empty($_GET['submission_status'])) {
        $meta_query[] = array(
            'key'   => '_submission_status',
            'value' => sanitize_text_field($_GET['submission_status']),
        );
    }

    if (!empty($_GET['form_source'])) {
        $meta_query[] = array(
            'key'   => '_form_source',
            'value' => sanitize_text_field($_GET['form_source']),
        );
    }

    if (!empty($_GET['legal_issue'])) {
        $meta_query[] = array(
            'key'   => '_legal_issue',
            'value' => sanitize_text_field($_GET['legal_issue']),
        );
    }

    if (isset($_GET['is_urgent']) && $_GET['is_urgent'] !== '') {
        $meta_query[] = array(
            'key'   => '_is_urgent',
            'value' => sanitize_text_field($_GET['is_urgent']),
        );
    }

    if (!empty($meta_query)) {
        $meta_query['relation'] = 'AND';
        $query->set('meta_query', $meta_query);
    }
}
add_action('pre_get_posts', 'mydefenselaw_submission_filter_query');

/**
 * Add bulk actions for status changes
 */
function mydefenselaw_submission_bulk_actions($actions) {
    $statuses = mydefenselaw_get_submission_statuses();

    foreach ($statuses as $value => $label) {
        $actions['mark_' . $value] = sprintf(__('Mark as %s', 'mydefenselaw'), $label);
    }

    return $actions;
}
add_filter('bulk_actions-edit-form_submission', 'mydefenselaw_submission_bulk_actions');

/**
 * Handle bulk action
 */
function mydefenselaw_handle_bulk_actions($redirect_to, $action, $post_ids) {
    if (strpos($action, 'mark_') !== 0) {
        return $redirect_to;
    }

    $status = str_replace('mark_', '', $action);
    $statuses = mydefenselaw_get_submission_statuses();

    if (!isset($statuses[$status])) {
        return $redirect_to;
    }

    foreach ($post_ids as $post_id) {
        update_post_meta($post_id, '_submission_status', $status);
    }

    $redirect_to = add_query_arg('bulk_status_updated', count($post_ids), $redirect_to);
    return $redirect_to;
}
add_filter('handle_bulk_actions-edit-form_submission', 'mydefenselaw_handle_bulk_actions', 10, 3);

/**
 * Show bulk action notice
 */
function mydefenselaw_bulk_action_notice() {
    if (!empty($_REQUEST['bulk_status_updated'])) {
        $count = intval($_REQUEST['bulk_status_updated']);
        printf(
            '<div class="notice notice-success is-dismissible"><p>' .
            _n('%s submission status updated.', '%s submission statuses updated.', $count, 'mydefenselaw') .
            '</p></div>',
            $count
        );
    }
}
add_action('admin_notices', 'mydefenselaw_bulk_action_notice');

/**
 * Remove quick edit and add view link
 */
function mydefenselaw_submission_row_actions($actions, $post) {
    if ($post->post_type === 'form_submission') {
        unset($actions['inline hide-if-no-js']);
        unset($actions['edit']);
        $actions = array_merge(
            array('view' => '<a href="' . get_edit_post_link($post->ID) . '">' . __('View', 'mydefenselaw') . '</a>'),
            $actions
        );
    }
    return $actions;
}
add_filter('post_row_actions', 'mydefenselaw_submission_row_actions', 10, 2);

/**
 * Add meta box for submission details
 */
function mydefenselaw_submission_meta_boxes() {
    add_meta_box(
        'submission_details',
        __('Submission Details', 'mydefenselaw'),
        'mydefenselaw_submission_details_metabox',
        'form_submission',
        'normal',
        'high'
    );

    add_meta_box(
        'submission_status_box',
        __('Submission Status', 'mydefenselaw'),
        'mydefenselaw_submission_status_metabox',
        'form_submission',
        'side',
        'high'
    );

    add_meta_box(
        'submission_meta',
        __('Submission Metadata', 'mydefenselaw'),
        'mydefenselaw_submission_meta_metabox',
        'form_submission',
        'side',
        'default'
    );

    // Remove default publishing box
    remove_meta_box('submitdiv', 'form_submission', 'side');
}
add_action('add_meta_boxes', 'mydefenselaw_submission_meta_boxes');

/**
 * Submission details meta box
 */
function mydefenselaw_submission_details_metabox($post) {
    $first_name = get_post_meta($post->ID, '_first_name', true);
    $last_name = get_post_meta($post->ID, '_last_name', true);
    $email = get_post_meta($post->ID, '_email', true);
    $phone = get_post_meta($post->ID, '_phone', true);
    $legal_issue = get_post_meta($post->ID, '_legal_issue', true);
    $urgency = get_post_meta($post->ID, '_urgency', true);
    $message = get_post_meta($post->ID, '_message', true);
    $is_urgent = get_post_meta($post->ID, '_is_urgent', true);

    $legal_issues = mydefenselaw_get_legal_issues();
    ?>
    <style>
        .submission-details { max-width: 800px; }
        .submission-details .detail-row {
            display: flex;
            border-bottom: 1px solid #eee;
            padding: 12px 0;
        }
        .submission-details .detail-label {
            width: 150px;
            font-weight: 600;
            color: #1d2327;
        }
        .submission-details .detail-value {
            flex: 1;
        }
        .submission-details .message-content {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            margin-top: 10px;
            white-space: pre-wrap;
        }
        .urgent-badge {
            background: #d63638;
            color: #fff;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>

    <div class="submission-details">
        <?php if ($is_urgent) : ?>
        <div style="background:#fef2f2;border:1px solid #d63638;padding:10px 15px;border-radius:4px;margin-bottom:20px;">
            <span class="urgent-badge">URGENT</span>
            <span style="margin-left:10px;color:#991b1b;">This submission was marked as urgent by the sender.</span>
        </div>
        <?php endif; ?>

        <div class="detail-row">
            <div class="detail-label"><?php esc_html_e('Full Name', 'mydefenselaw'); ?></div>
            <div class="detail-value"><?php echo esc_html($first_name . ' ' . $last_name); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label"><?php esc_html_e('Email', 'mydefenselaw'); ?></div>
            <div class="detail-value">
                <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label"><?php esc_html_e('Phone', 'mydefenselaw'); ?></div>
            <div class="detail-value">
                <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label"><?php esc_html_e('Legal Issue', 'mydefenselaw'); ?></div>
            <div class="detail-value"><?php echo esc_html($legal_issues[$legal_issue] ?? $legal_issue); ?></div>
        </div>

        <?php if ($urgency) : ?>
        <div class="detail-row">
            <div class="detail-label"><?php esc_html_e('Urgency Level', 'mydefenselaw'); ?></div>
            <div class="detail-value"><?php echo esc_html(ucfirst($urgency)); ?></div>
        </div>
        <?php endif; ?>

        <div class="detail-row" style="flex-direction:column;border:none;">
            <div class="detail-label"><?php esc_html_e('Message', 'mydefenselaw'); ?></div>
            <div class="message-content"><?php echo $message ? esc_html($message) : '<em style="color:#666;">' . __('No message provided', 'mydefenselaw') . '</em>'; ?></div>
        </div>
    </div>

    <div style="margin-top:20px;">
        <a href="mailto:<?php echo esc_attr($email); ?>?subject=RE: Your Consultation Request" class="button button-primary">
            <span class="dashicons dashicons-email" style="margin-top:4px;"></span>
            <?php esc_html_e('Reply via Email', 'mydefenselaw'); ?>
        </a>
        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>" class="button">
            <span class="dashicons dashicons-phone" style="margin-top:4px;"></span>
            <?php esc_html_e('Call', 'mydefenselaw'); ?>
        </a>
    </div>
    <?php
}

/**
 * Submission status meta box
 */
function mydefenselaw_submission_status_metabox($post) {
    $current_status = get_post_meta($post->ID, '_submission_status', true) ?: 'new';
    $statuses = mydefenselaw_get_submission_statuses();

    wp_nonce_field('mydefenselaw_submission_status', 'submission_status_nonce');
    ?>
    <div style="padding: 10px 0;">
        <label for="submission_status" style="display:block;margin-bottom:8px;font-weight:600;">
            <?php esc_html_e('Current Status', 'mydefenselaw'); ?>
        </label>
        <select name="submission_status" id="submission_status" style="width:100%;">
            <?php foreach ($statuses as $value => $label) : ?>
            <option value="<?php echo esc_attr($value); ?>" <?php selected($current_status, $value); ?>>
                <?php echo esc_html($label); ?>
            </option>
            <?php endforeach; ?>
        </select>

        <p style="margin-top:15px;">
            <button type="submit" name="save_submission" class="button button-primary" style="width:100%;">
                <?php esc_html_e('Update Status', 'mydefenselaw'); ?>
            </button>
        </p>

        <p style="margin-top:10px;">
            <a href="<?php echo esc_url(admin_url('edit.php?post_type=form_submission')); ?>" class="button" style="width:100%;text-align:center;">
                <?php esc_html_e('&larr; Back to All Submissions', 'mydefenselaw'); ?>
            </a>
        </p>
    </div>
    <?php
}

/**
 * Submission metadata meta box
 */
function mydefenselaw_submission_meta_metabox($post) {
    $form_source = get_post_meta($post->ID, '_form_source', true);
    $ip_address = get_post_meta($post->ID, '_ip_address', true);
    $user_agent = get_post_meta($post->ID, '_user_agent', true);
    $page_url = get_post_meta($post->ID, '_page_url', true);

    $sources = mydefenselaw_get_form_sources();
    ?>
    <div style="padding: 5px 0;">
        <p><strong><?php esc_html_e('Form Source:', 'mydefenselaw'); ?></strong><br>
        <?php echo esc_html($sources[$form_source] ?? $form_source ?: '—'); ?></p>

        <p><strong><?php esc_html_e('Submitted:', 'mydefenselaw'); ?></strong><br>
        <?php echo esc_html(get_the_date('F j, Y') . ' at ' . get_the_time('g:i a')); ?></p>

        <?php if ($ip_address) : ?>
        <p><strong><?php esc_html_e('IP Address:', 'mydefenselaw'); ?></strong><br>
        <?php echo esc_html($ip_address); ?></p>
        <?php endif; ?>

        <?php if ($page_url) : ?>
        <p><strong><?php esc_html_e('Page URL:', 'mydefenselaw'); ?></strong><br>
        <a href="<?php echo esc_url($page_url); ?>" target="_blank" style="word-break:break-all;">
            <?php echo esc_html($page_url); ?>
        </a></p>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Save submission status
 */
function mydefenselaw_save_submission_status($post_id) {
    if (!isset($_POST['submission_status_nonce']) ||
        !wp_verify_nonce($_POST['submission_status_nonce'], 'mydefenselaw_submission_status')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['submission_status'])) {
        $status = sanitize_text_field($_POST['submission_status']);
        $statuses = mydefenselaw_get_submission_statuses();

        if (isset($statuses[$status])) {
            update_post_meta($post_id, '_submission_status', $status);
        }
    }
}
add_action('save_post_form_submission', 'mydefenselaw_save_submission_status');

/**
 * Auto-mark as read when viewed
 */
function mydefenselaw_mark_submission_read() {
    global $pagenow, $post;

    if ($pagenow !== 'post.php' || !isset($_GET['post'])) {
        return;
    }

    $post_id = intval($_GET['post']);
    $post = get_post($post_id);

    if (!$post || $post->post_type !== 'form_submission') {
        return;
    }

    $current_status = get_post_meta($post_id, '_submission_status', true);

    if ($current_status === 'new' || empty($current_status)) {
        update_post_meta($post_id, '_submission_status', 'read');
    }
}
add_action('admin_init', 'mydefenselaw_mark_submission_read');

/**
 * Add unread count to menu
 */
function mydefenselaw_submission_menu_count() {
    global $menu;

    $count = wp_count_posts('form_submission');

    // Count "new" status submissions
    $new_count = get_posts(array(
        'post_type'   => 'form_submission',
        'post_status' => 'publish',
        'meta_query'  => array(
            'relation' => 'OR',
            array(
                'key'     => '_submission_status',
                'value'   => 'new',
                'compare' => '=',
            ),
            array(
                'key'     => '_submission_status',
                'compare' => 'NOT EXISTS',
            ),
        ),
        'fields'      => 'ids',
        'numberposts' => -1,
    ));

    $new_count = count($new_count);

    if ($new_count > 0) {
        foreach ($menu as $key => $value) {
            if (isset($value[2]) && $value[2] === 'edit.php?post_type=form_submission') {
                $menu[$key][0] .= sprintf(
                    ' <span class="awaiting-mod count-%d"><span class="pending-count">%d</span></span>',
                    $new_count,
                    $new_count
                );
                break;
            }
        }
    }
}
add_action('admin_menu', 'mydefenselaw_submission_menu_count', 999);

/**
 * Add admin styles
 */
function mydefenselaw_submission_admin_styles() {
    global $pagenow, $post_type;

    if ($post_type !== 'form_submission') {
        return;
    }
    ?>
    <style>
        .column-status_icon { width: 40px; text-align: center; }
        .column-is_urgent { width: 60px; }
        .column-sub_status { width: 100px; }
        .column-form_source { width: 130px; }
        .widefat .column-cb { width: 2.2em; }

        /* Highlight new submissions */
        tr.status-publish td {
            background: inherit;
        }

        /* Add subtle background for urgent items */
        tr[data-urgent="1"] td {
            background: #fef8f8 !important;
        }
    </style>
    <?php
}
add_action('admin_head', 'mydefenselaw_submission_admin_styles');

/**
 * Add urgent data attribute to rows
 */
function mydefenselaw_submission_row_class($classes, $class, $post_id) {
    if (get_post_type($post_id) === 'form_submission') {
        $is_urgent = get_post_meta($post_id, '_is_urgent', true);
        if ($is_urgent) {
            $classes[] = 'submission-urgent';
        }
    }
    return $classes;
}
add_filter('post_class', 'mydefenselaw_submission_row_class', 10, 3);

/**
 * Create a new form submission
 *
 * @param array $data Form submission data
 * @return int|WP_Error Post ID on success, WP_Error on failure
 */
function mydefenselaw_create_form_submission($data) {
    $first_name = sanitize_text_field($data['first_name'] ?? '');
    $last_name = sanitize_text_field($data['last_name'] ?? '');
    $email = sanitize_email($data['email'] ?? '');
    $phone = sanitize_text_field($data['phone'] ?? '');
    $legal_issue = sanitize_text_field($data['legal_issue'] ?? '');
    $urgency = sanitize_text_field($data['urgency'] ?? '');
    $message = sanitize_textarea_field($data['message'] ?? '');
    $is_urgent = !empty($data['is_urgent']);
    $form_source = sanitize_text_field($data['form_source'] ?? 'unknown');
    $ip_address = sanitize_text_field($data['ip_address'] ?? '');
    $user_agent = sanitize_text_field($data['user_agent'] ?? '');
    $page_url = esc_url_raw($data['page_url'] ?? '');

    // Create post
    $post_id = wp_insert_post(array(
        'post_type'   => 'form_submission',
        'post_status' => 'publish',
        'post_title'  => $first_name . ' ' . $last_name,
    ));

    if (is_wp_error($post_id)) {
        return $post_id;
    }

    // Save meta data
    update_post_meta($post_id, '_first_name', $first_name);
    update_post_meta($post_id, '_last_name', $last_name);
    update_post_meta($post_id, '_email', $email);
    update_post_meta($post_id, '_phone', $phone);
    update_post_meta($post_id, '_legal_issue', $legal_issue);
    update_post_meta($post_id, '_urgency', $urgency);
    update_post_meta($post_id, '_message', $message);
    update_post_meta($post_id, '_is_urgent', $is_urgent ? '1' : '0');
    update_post_meta($post_id, '_form_source', $form_source);
    update_post_meta($post_id, '_ip_address', $ip_address);
    update_post_meta($post_id, '_user_agent', $user_agent);
    update_post_meta($post_id, '_page_url', $page_url);
    update_post_meta($post_id, '_submission_status', 'new');

    return $post_id;
}

/**
 * Add export functionality
 */
function mydefenselaw_add_export_button() {
    global $pagenow, $post_type;

    if ($pagenow !== 'edit.php' || $post_type !== 'form_submission') {
        return;
    }
    ?>
    <script>
    jQuery(document).ready(function($) {
        $('.wrap h1.wp-heading-inline').after(
            '<a href="<?php echo esc_url(admin_url('admin-post.php?action=export_submissions')); ?>" class="page-title-action">' +
            '<?php echo esc_js(__('Export CSV', 'mydefenselaw')); ?></a>'
        );
    });
    </script>
    <?php
}
add_action('admin_footer', 'mydefenselaw_add_export_button');

/**
 * Handle CSV export
 */
function mydefenselaw_export_submissions() {
    if (!current_user_can('edit_posts')) {
        wp_die(__('Unauthorized access', 'mydefenselaw'));
    }

    $submissions = get_posts(array(
        'post_type'   => 'form_submission',
        'post_status' => 'publish',
        'numberposts' => -1,
        'orderby'     => 'date',
        'order'       => 'DESC',
    ));

    $filename = 'form-submissions-' . date('Y-m-d') . '.csv';

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    // Header row
    fputcsv($output, array(
        'Date',
        'First Name',
        'Last Name',
        'Email',
        'Phone',
        'Legal Issue',
        'Urgency',
        'Message',
        'Is Urgent',
        'Status',
        'Source',
        'IP Address',
    ));

    foreach ($submissions as $submission) {
        $legal_issues = mydefenselaw_get_legal_issues();
        $statuses = mydefenselaw_get_submission_statuses();
        $sources = mydefenselaw_get_form_sources();

        $legal_issue = get_post_meta($submission->ID, '_legal_issue', true);
        $status = get_post_meta($submission->ID, '_submission_status', true);
        $source = get_post_meta($submission->ID, '_form_source', true);

        fputcsv($output, array(
            get_the_date('Y-m-d H:i:s', $submission),
            get_post_meta($submission->ID, '_first_name', true),
            get_post_meta($submission->ID, '_last_name', true),
            get_post_meta($submission->ID, '_email', true),
            get_post_meta($submission->ID, '_phone', true),
            $legal_issues[$legal_issue] ?? $legal_issue,
            get_post_meta($submission->ID, '_urgency', true),
            get_post_meta($submission->ID, '_message', true),
            get_post_meta($submission->ID, '_is_urgent', true) ? 'Yes' : 'No',
            $statuses[$status] ?? $status,
            $sources[$source] ?? $source,
            get_post_meta($submission->ID, '_ip_address', true),
        ));
    }

    fclose($output);
    exit;
}
add_action('admin_post_export_submissions', 'mydefenselaw_export_submissions');
