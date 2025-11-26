<?php
/**
 * Admin Customization
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add custom admin footer text
 */
function responsibleai_admin_footer_text($text) {
    return sprintf(
        /* translators: 1: Theme name, 2: WordPress link */
        __('Responsible AI Theme powered by %2$s', 'responsible-ai'),
        'Responsible AI',
        '<a href="https://wordpress.org" target="_blank">WordPress</a>'
    );
}
add_filter('admin_footer_text', 'responsibleai_admin_footer_text');

/**
 * Customize login page logo
 */
function responsibleai_login_logo() {
    $logo_url = get_template_directory_uri() . '/assets/images/logo.svg';
    ?>
    <style>
        #login h1 a {
            background-image: url(<?php echo esc_url($logo_url); ?>);
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            width: 200px;
            height: 80px;
        }
        body.login {
            background-color: #0a1628;
        }
        .login form {
            background-color: #0f1d32;
            border: 1px solid #1e293b;
        }
        .login label {
            color: #e2e8f0;
        }
        .login input[type="text"],
        .login input[type="password"] {
            background-color: #1a2d4a;
            border-color: #334155;
            color: #e2e8f0;
        }
        .login .button-primary {
            background-color: #2563eb;
            border-color: #2563eb;
        }
        .login .button-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }
        .login #backtoblog a,
        .login #nav a {
            color: #94a3b8;
        }
        .login #backtoblog a:hover,
        .login #nav a:hover {
            color: #2563eb;
        }
        .login .message {
            background-color: #1a2d4a;
            border-left-color: #2563eb;
            color: #e2e8f0;
        }
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'responsibleai_login_logo');

/**
 * Customize login page logo URL
 */
function responsibleai_login_logo_url() {
    return home_url('/');
}
add_filter('login_headerurl', 'responsibleai_login_logo_url');

/**
 * Customize login page logo title
 */
function responsibleai_login_logo_title() {
    return get_bloginfo('name');
}
add_filter('login_headertext', 'responsibleai_login_logo_title');

/**
 * Add custom dashboard widget
 */
function responsibleai_dashboard_widgets() {
    wp_add_dashboard_widget(
        'responsibleai_welcome_widget',
        __('Responsible AI Theme', 'responsible-ai'),
        'responsibleai_welcome_widget_content'
    );
}
add_action('wp_dashboard_setup', 'responsibleai_dashboard_widgets');

/**
 * Dashboard widget content
 */
function responsibleai_welcome_widget_content() {
    ?>
    <div style="padding: 10px 0;">
        <h3 style="margin-top: 0;"><?php esc_html_e('Quick Links', 'responsible-ai'); ?></h3>
        <ul style="margin: 0; padding-left: 20px;">
            <li><a href="<?php echo admin_url('admin.php?page=theme-settings'); ?>"><?php esc_html_e('Theme Settings', 'responsible-ai'); ?></a></li>
            <li><a href="<?php echo admin_url('edit.php?post_type=rai_event'); ?>"><?php esc_html_e('Manage Events', 'responsible-ai'); ?></a></li>
            <li><a href="<?php echo admin_url('edit.php?post_type=rai_resource'); ?>"><?php esc_html_e('Manage Resources', 'responsible-ai'); ?></a></li>
            <li><a href="<?php echo admin_url('edit.php?post_type=case_study'); ?>"><?php esc_html_e('Manage Case Studies', 'responsible-ai'); ?></a></li>
            <li><a href="<?php echo esc_url(home_url('/')); ?>" target="_blank"><?php esc_html_e('View Site', 'responsible-ai'); ?></a></li>
        </ul>
    </div>
    <?php
}

/**
 * Reorder admin menu items
 */
function responsibleai_admin_menu_order($menu_order) {
    if (!$menu_order) {
        return true;
    }

    // Define custom order
    $custom_order = array(
        'index.php',                      // Dashboard
        'separator1',
        'edit.php?post_type=page',        // Pages
        'edit.php',                       // Posts (Blog)
        'edit.php?post_type=rai_event',   // Events
        'edit.php?post_type=rai_resource', // Resources
        'edit.php?post_type=case_study',  // Case Studies
        'separator2',
        'upload.php',                     // Media
        'separator-last',
        'theme-settings',                 // Theme Settings
    );

    return $custom_order;
}
add_filter('custom_menu_order', 'responsibleai_admin_menu_order');
add_filter('menu_order', 'responsibleai_admin_menu_order');

/**
 * Add admin body classes
 */
function responsibleai_admin_body_class($classes) {
    $classes .= ' responsibleai-admin';
    return $classes;
}
add_filter('admin_body_class', 'responsibleai_admin_body_class');
