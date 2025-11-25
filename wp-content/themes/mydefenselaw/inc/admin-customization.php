<?php
/**
 * MyDefenseLaw Admin Customization
 *
 * Styles and simplifies the WordPress admin to match the frontend theme.
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue Admin Styles and Fonts
 */
function mydefenselaw_admin_enqueue_styles() {
    // Google Fonts (same as frontend)
    wp_enqueue_style(
        'mydefenselaw-admin-fonts',
        'https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700;900&family=Open+Sans:wght@400;600;700&display=swap',
        array(),
        null
    );

    // Custom admin stylesheet
    wp_enqueue_style(
        'mydefenselaw-admin-style',
        get_template_directory_uri() . '/assets/css/admin-style.css',
        array('mydefenselaw-admin-fonts'),
        MYDEFENSELAW_VERSION
    );
}
add_action('admin_enqueue_scripts', 'mydefenselaw_admin_enqueue_styles');

/**
 * Enqueue Login Page Styles
 */
function mydefenselaw_login_enqueue_styles() {
    // Google Fonts
    wp_enqueue_style(
        'mydefenselaw-login-fonts',
        'https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700;900&family=Open+Sans:wght@400;600;700&display=swap',
        array(),
        null
    );

    // Admin stylesheet (includes login styles)
    wp_enqueue_style(
        'mydefenselaw-login-style',
        get_template_directory_uri() . '/assets/css/admin-style.css',
        array('mydefenselaw-login-fonts'),
        MYDEFENSELAW_VERSION
    );
}
add_action('login_enqueue_scripts', 'mydefenselaw_login_enqueue_styles');

/**
 * Custom Login Logo
 */
function mydefenselaw_login_logo() {
    $custom_logo_id = get_theme_mod('custom_logo');
    $logo_url = '';

    if ($custom_logo_id) {
        $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
    }

    // Use a default or site name if no logo
    if (!$logo_url) {
        $logo_url = get_template_directory_uri() . '/assets/images/logo.png';
    }

    ?>
    <style type="text/css">
        body.login h1 a {
            <?php if ($logo_url && file_exists(str_replace(get_template_directory_uri(), get_template_directory(), $logo_url))) : ?>
            background-image: url(<?php echo esc_url($logo_url); ?>);
            <?php else : ?>
            background-image: none;
            <?php endif; ?>
            background-size: contain;
            background-position: center;
            width: 100%;
            height: 80px;
            margin-bottom: 20px;
        }
    </style>
    <?php
}
add_action('login_head', 'mydefenselaw_login_logo');

/**
 * Custom Login Logo URL
 */
function mydefenselaw_login_logo_url() {
    return home_url('/');
}
add_filter('login_headerurl', 'mydefenselaw_login_logo_url');

/**
 * Custom Login Logo Title
 */
function mydefenselaw_login_logo_title() {
    return get_bloginfo('name');
}
add_filter('login_headertext', 'mydefenselaw_login_logo_title');

/**
 * Customize Admin Footer Text
 */
function mydefenselaw_admin_footer_text($text) {
    return sprintf(
        '<span style="color: #64748b;">%s &copy; %s | Powered by <a href="https://wordpress.org" target="_blank" style="color: #1a365d;">WordPress</a></span>',
        esc_html(get_bloginfo('name')),
        date('Y')
    );
}
add_filter('admin_footer_text', 'mydefenselaw_admin_footer_text');

/**
 * Remove WordPress Version from Footer
 */
function mydefenselaw_update_footer($text) {
    return '';
}
add_filter('update_footer', 'mydefenselaw_update_footer', 99);

/**
 * Remove Default Dashboard Widgets for Custom Interface
 */
function mydefenselaw_remove_dashboard_widgets() {
    // Remove all default widgets for a clean slate
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');

    // Remove third-party plugin widgets
    remove_meta_box('yoast_db_widget', 'dashboard', 'normal');
    remove_meta_box('wpseo-dashboard-overview', 'dashboard', 'normal');
}
add_action('wp_dashboard_setup', 'mydefenselaw_remove_dashboard_widgets', 999);

/**
 * Add Custom Dashboard Widgets
 */
function mydefenselaw_add_dashboard_widgets() {
    // Main Welcome Widget - Full Width
    wp_add_dashboard_widget(
        'mydefenselaw_welcome_widget',
        '',
        'mydefenselaw_welcome_widget_content'
    );

    // Site Statistics Widget
    wp_add_dashboard_widget(
        'mydefenselaw_stats_widget',
        '<span class="dashicons dashicons-chart-bar"></span> Site Statistics',
        'mydefenselaw_stats_widget_content'
    );

    // Recent Form Submissions Widget
    wp_add_dashboard_widget(
        'mydefenselaw_submissions_widget',
        '<span class="dashicons dashicons-email"></span> Recent Form Submissions',
        'mydefenselaw_submissions_widget_content'
    );

    // Quick Links Widget
    wp_add_dashboard_widget(
        'mydefenselaw_links_widget',
        '<span class="dashicons dashicons-admin-links"></span> Helpful Resources',
        'mydefenselaw_links_widget_content'
    );
}
add_action('wp_dashboard_setup', 'mydefenselaw_add_dashboard_widgets');

/**
 * Welcome Widget Content - Hero Section with Quick Actions
 */
function mydefenselaw_welcome_widget_content() {
    $current_user = wp_get_current_user();
    $hour = (int) current_time('G');

    if ($hour < 12) {
        $greeting = 'Good morning';
    } elseif ($hour < 17) {
        $greeting = 'Good afternoon';
    } else {
        $greeting = 'Good evening';
    }
    ?>
    <div class="mdl-dashboard-welcome">
        <div class="mdl-welcome-hero">
            <div class="mdl-welcome-content">
                <div class="mdl-welcome-icon">
                    <span class="dashicons dashicons-shield"></span>
                </div>
                <div class="mdl-welcome-text">
                    <h2><?php echo esc_html($greeting); ?>, <?php echo esc_html($current_user->display_name); ?>!</h2>
                    <p>Welcome to My Defense Law website administration panel.</p>
                </div>
            </div>
            <div class="mdl-welcome-date">
                <span class="mdl-date-day"><?php echo current_time('l'); ?></span>
                <span class="mdl-date-full"><?php echo current_time('F j, Y'); ?></span>
            </div>
        </div>

        <div class="mdl-quick-actions-grid">
            <a href="<?php echo admin_url('edit.php?post_type=practice_area'); ?>" class="mdl-action-card mdl-action-primary">
                <div class="mdl-action-icon">
                    <span class="dashicons dashicons-portfolio"></span>
                </div>
                <div class="mdl-action-content">
                    <h3>Practice Areas</h3>
                    <p>Manage legal services</p>
                </div>
                <span class="mdl-action-arrow dashicons dashicons-arrow-right-alt2"></span>
            </a>

            <a href="<?php echo admin_url('edit.php?post_type=attorney'); ?>" class="mdl-action-card">
                <div class="mdl-action-icon">
                    <span class="dashicons dashicons-groups"></span>
                </div>
                <div class="mdl-action-content">
                    <h3>Attorneys</h3>
                    <p>Edit team profiles</p>
                </div>
                <span class="mdl-action-arrow dashicons dashicons-arrow-right-alt2"></span>
            </a>

            <a href="<?php echo admin_url('edit.php?post_type=form_submission'); ?>" class="mdl-action-card">
                <div class="mdl-action-icon">
                    <span class="dashicons dashicons-email-alt"></span>
                </div>
                <div class="mdl-action-content">
                    <h3>Form Submissions</h3>
                    <p>View client inquiries</p>
                </div>
                <span class="mdl-action-arrow dashicons dashicons-arrow-right-alt2"></span>
            </a>

            <a href="<?php echo admin_url('edit.php?post_type=page'); ?>" class="mdl-action-card">
                <div class="mdl-action-icon">
                    <span class="dashicons dashicons-admin-page"></span>
                </div>
                <div class="mdl-action-content">
                    <h3>Pages</h3>
                    <p>Edit website pages</p>
                </div>
                <span class="mdl-action-arrow dashicons dashicons-arrow-right-alt2"></span>
            </a>

            <a href="<?php echo admin_url('admin.php?page=theme-settings'); ?>" class="mdl-action-card">
                <div class="mdl-action-icon">
                    <span class="dashicons dashicons-admin-generic"></span>
                </div>
                <div class="mdl-action-content">
                    <h3>Theme Settings</h3>
                    <p>Configure options</p>
                </div>
                <span class="mdl-action-arrow dashicons dashicons-arrow-right-alt2"></span>
            </a>

            <a href="<?php echo home_url('/'); ?>" class="mdl-action-card" target="_blank">
                <div class="mdl-action-icon">
                    <span class="dashicons dashicons-external"></span>
                </div>
                <div class="mdl-action-content">
                    <h3>View Website</h3>
                    <p>See live site</p>
                </div>
                <span class="mdl-action-arrow dashicons dashicons-arrow-right-alt2"></span>
            </a>
        </div>
    </div>
    <?php
}

/**
 * Site Statistics Widget Content
 */
function mydefenselaw_stats_widget_content() {
    // Get counts
    $practice_areas = wp_count_posts('practice_area');
    $attorneys = wp_count_posts('attorney');
    $pages = wp_count_posts('page');
    $submissions = wp_count_posts('form_submission');

    $practice_count = isset($practice_areas->publish) ? $practice_areas->publish : 0;
    $attorney_count = isset($attorneys->publish) ? $attorneys->publish : 0;
    $page_count = isset($pages->publish) ? $pages->publish : 0;
    $submission_count = isset($submissions->publish) ? $submissions->publish : 0;
    ?>
    <div class="mdl-stats-grid">
        <div class="mdl-stat-card">
            <div class="mdl-stat-icon" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);">
                <span class="dashicons dashicons-portfolio"></span>
            </div>
            <div class="mdl-stat-content">
                <span class="mdl-stat-number"><?php echo esc_html($practice_count); ?></span>
                <span class="mdl-stat-label">Practice Areas</span>
            </div>
        </div>

        <div class="mdl-stat-card">
            <div class="mdl-stat-icon" style="background: linear-gradient(135deg, #1a365d 0%, #2d4a7c 100%);">
                <span class="dashicons dashicons-groups"></span>
            </div>
            <div class="mdl-stat-content">
                <span class="mdl-stat-number"><?php echo esc_html($attorney_count); ?></span>
                <span class="mdl-stat-label">Attorneys</span>
            </div>
        </div>

        <div class="mdl-stat-card">
            <div class="mdl-stat-icon" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                <span class="dashicons dashicons-admin-page"></span>
            </div>
            <div class="mdl-stat-content">
                <span class="mdl-stat-number"><?php echo esc_html($page_count); ?></span>
                <span class="mdl-stat-label">Pages</span>
            </div>
        </div>

        <div class="mdl-stat-card">
            <div class="mdl-stat-icon" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                <span class="dashicons dashicons-email-alt"></span>
            </div>
            <div class="mdl-stat-content">
                <span class="mdl-stat-number"><?php echo esc_html($submission_count); ?></span>
                <span class="mdl-stat-label">Submissions</span>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Recent Form Submissions Widget Content
 */
function mydefenselaw_submissions_widget_content() {
    $submissions = get_posts(array(
        'post_type' => 'form_submission',
        'posts_per_page' => 5,
        'orderby' => 'date',
        'order' => 'DESC',
    ));

    if (empty($submissions)) {
        ?>
        <div class="mdl-empty-state">
            <span class="dashicons dashicons-email"></span>
            <p>No form submissions yet.</p>
        </div>
        <?php
        return;
    }
    ?>
    <div class="mdl-submissions-list">
        <?php foreach ($submissions as $submission) :
            $name = get_post_meta($submission->ID, 'name', true);
            $email = get_post_meta($submission->ID, 'email', true);
            $date = get_the_date('M j, g:i a', $submission);
        ?>
        <a href="<?php echo get_edit_post_link($submission->ID); ?>" class="mdl-submission-item">
            <div class="mdl-submission-avatar">
                <?php echo strtoupper(substr($name ?: 'U', 0, 1)); ?>
            </div>
            <div class="mdl-submission-info">
                <span class="mdl-submission-name"><?php echo esc_html($name ?: 'Unknown'); ?></span>
                <span class="mdl-submission-email"><?php echo esc_html($email ?: 'No email'); ?></span>
            </div>
            <div class="mdl-submission-date">
                <?php echo esc_html($date); ?>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <div class="mdl-widget-footer">
        <a href="<?php echo admin_url('edit.php?post_type=form_submission'); ?>" class="mdl-view-all">
            View All Submissions <span class="dashicons dashicons-arrow-right-alt2"></span>
        </a>
    </div>
    <?php
}

/**
 * Helpful Links Widget Content
 */
function mydefenselaw_links_widget_content() {
    ?>
    <div class="mdl-links-grid">
        <a href="<?php echo admin_url('upload.php'); ?>" class="mdl-link-item">
            <span class="dashicons dashicons-admin-media"></span>
            <span>Media Library</span>
        </a>
        <a href="<?php echo admin_url('nav-menus.php'); ?>" class="mdl-link-item">
            <span class="dashicons dashicons-menu"></span>
            <span>Navigation Menus</span>
        </a>
        <a href="<?php echo admin_url('customize.php'); ?>" class="mdl-link-item">
            <span class="dashicons dashicons-admin-customizer"></span>
            <span>Customize</span>
        </a>
        <a href="<?php echo admin_url('users.php'); ?>" class="mdl-link-item">
            <span class="dashicons dashicons-admin-users"></span>
            <span>Users</span>
        </a>
    </div>
    <div class="mdl-support-box">
        <span class="dashicons dashicons-sos"></span>
        <div class="mdl-support-text">
            <strong>Need Help?</strong>
            <p>Contact your web administrator for technical support.</p>
        </div>
    </div>
    <?php
}

/**
 * Reorder Dashboard Widgets
 */
function mydefenselaw_dashboard_widget_order() {
    global $wp_meta_boxes;

    // Ensure welcome widget is first and full-width
    if (isset($wp_meta_boxes['dashboard']['normal']['core']['mydefenselaw_welcome_widget'])) {
        $welcome = $wp_meta_boxes['dashboard']['normal']['core']['mydefenselaw_welcome_widget'];
        unset($wp_meta_boxes['dashboard']['normal']['core']['mydefenselaw_welcome_widget']);
        $wp_meta_boxes['dashboard']['normal']['core'] = array_merge(
            array('mydefenselaw_welcome_widget' => $welcome),
            $wp_meta_boxes['dashboard']['normal']['core']
        );
    }
}
add_action('wp_dashboard_setup', 'mydefenselaw_dashboard_widget_order', 999);

/**
 * Simplify Admin Menu for Non-Admins (Optional)
 * Uncomment to enable
 */
/*
function mydefenselaw_simplify_admin_menu() {
    if (!current_user_can('manage_options')) {
        // Remove menu items for editors/authors
        remove_menu_page('tools.php');
        remove_menu_page('edit-comments.php');
    }
}
add_action('admin_menu', 'mydefenselaw_simplify_admin_menu', 999);
*/

/**
 * Add Admin Body Class for Custom Styling Hooks
 */
function mydefenselaw_admin_body_class($classes) {
    $classes .= ' mydefenselaw-admin';
    return $classes;
}
add_filter('admin_body_class', 'mydefenselaw_admin_body_class');

/**
 * Customize Howdy Text in Admin Bar
 */
function mydefenselaw_howdy_message($wp_admin_bar) {
    $my_account = $wp_admin_bar->get_node('my-account');
    $current_user = wp_get_current_user();

    $new_title = sprintf('Hello, %s', $current_user->display_name);

    $wp_admin_bar->add_node(array(
        'id' => 'my-account',
        'title' => $new_title,
    ));
}
add_action('admin_bar_menu', 'mydefenselaw_howdy_message', 25);

/**
 * Remove WordPress Logo from Admin Bar (Optional)
 * Uncomment to enable
 */
/*
function mydefenselaw_remove_wp_logo($wp_admin_bar) {
    $wp_admin_bar->remove_node('wp-logo');
}
add_action('admin_bar_menu', 'mydefenselaw_remove_wp_logo', 999);
*/

/**
 * Customize Site Name in Admin Bar
 * Note: WordPress already displays a home icon via CSS ::before pseudo-element
 * so we don't need to add an additional icon span
 */
function mydefenselaw_admin_bar_site_name($wp_admin_bar) {
    $site_node = $wp_admin_bar->get_node('site-name');
    if ($site_node) {
        $wp_admin_bar->add_node(array(
            'id' => 'site-name',
            'title' => get_bloginfo('name'),
        ));
    }
}
add_action('admin_bar_menu', 'mydefenselaw_admin_bar_site_name', 31);

/**
 * Custom Editor Styles
 */
function mydefenselaw_add_editor_styles() {
    add_editor_style(array(
        'https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700;900&family=Open+Sans:wght@400;600;700&display=swap',
        'assets/css/editor-style.css',
    ));
}
add_action('after_setup_theme', 'mydefenselaw_add_editor_styles');

/**
 * Hide Promotional/Sales Admin Notices from Plugins
 * Keeps the admin clean and distraction-free
 */
function mydefenselaw_hide_promo_notices() {
    ?>
    <style type="text/css">
        /* TranslatePress promotional notices */
        .trp-notice,
        .trp-admin-notice,
        [class*="trp-"][class*="notice"],

        /* Generic promotional/sales patterns */
        .notice[class*="promo"],
        .notice[class*="sale"],
        .notice[class*="black-friday"],
        .notice[class*="discount"],
        .notice[class*="upgrade"],
        .notice[class*="review"],
        .notice[class*="rate-us"],
        .notice[class*="feedback"],

        /* Common plugin promo classes */
        .yoast-notice,
        .elementor-message,
        .woocommerce-message[class*="promo"],
        .jetpack-jitm-message,
        [class*="plugin"][class*="promo"],
        [class*="plugin"][class*="upsell"],

        /* ACF promotional */
        .acf-admin-notice[data-dismiss*="promo"],

        /* Wordfence promotional */
        .wordfence-notice[class*="promo"],

        /* Hide notices with promotional links */
        .notice a[href*="black-friday"],
        .notice a[href*="utm_campaign"]:not([href*="security"]):not([href*="update"]) {
            display: none !important;
        }
    </style>
    <?php
}
add_action('admin_head', 'mydefenselaw_hide_promo_notices');

/**
 * Remove specific plugin admin notices programmatically
 */
function mydefenselaw_remove_plugin_notices() {
    // Remove all admin notices from TranslatePress
    remove_all_actions('admin_notices', 10);

    // Re-add WordPress core notices only
    add_action('admin_notices', function() {
        // Let WordPress core notices through
        settings_errors();
    });
}
// Uncomment below line to aggressively remove ALL third-party notices
// add_action('admin_init', 'mydefenselaw_remove_plugin_notices', 999);
