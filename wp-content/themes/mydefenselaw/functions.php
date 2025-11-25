<?php
/**
 * My Defense Law Theme Functions
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

// Define theme version constant
define('MYDEFENSELAW_VERSION', '1.0.0');

/**
 * Theme Setup
 */
function mydefenselaw_setup() {
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails
    add_theme_support('post-thumbnails');

    // Switch default core markup to output valid HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // Add theme support for Custom Logo
    add_theme_support('custom-logo', array(
        'height'      => 80,
        'width'       => 200,
        'flex-width'  => true,
        'flex-height' => true,
    ));

    // Register Navigation Menus
    register_nav_menus(array(
        'primary'        => __('Primary Menu', 'mydefenselaw'),
        'footer-legal'   => __('Footer Legal Services', 'mydefenselaw'),
        'footer-about'   => __('Footer About', 'mydefenselaw'),
        'footer-contact' => __('Footer Contact', 'mydefenselaw'),
    ));
}
add_action('after_setup_theme', 'mydefenselaw_setup');

/**
 * Register Widget Areas
 */
function mydefenselaw_widgets_init() {
    // Sidebar for practice areas
    register_sidebar(array(
        'name'          => __('Practice Areas Sidebar', 'mydefenselaw'),
        'id'            => 'sidebar-practice-areas',
        'description'   => __('Widgets for practice areas pages', 'mydefenselaw'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    // Footer widgets
    register_sidebar(array(
        'name'          => __('Footer Widgets', 'mydefenselaw'),
        'id'            => 'footer-widgets',
        'description'   => __('Widgets for footer area', 'mydefenselaw'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'mydefenselaw_widgets_init');

/**
 * Enqueue Scripts and Styles
 */
function mydefenselaw_scripts() {
    // Google Fonts
    wp_enqueue_style(
        'mydefenselaw-fonts',
        'https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&family=Open+Sans:wght@300;400;600;700&display=swap',
        array(),
        null
    );

    // Font Awesome 6.4.0
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
        array(),
        '6.4.0'
    );

    // Main stylesheet
    wp_enqueue_style(
        'mydefenselaw-style',
        get_template_directory_uri() . '/assets/css/main.css',
        array(),
        MYDEFENSELAW_VERSION
    );

    // Main JavaScript
    wp_enqueue_script(
        'mydefenselaw-script',
        get_template_directory_uri() . '/assets/js/main.js',
        array('jquery'),
        MYDEFENSELAW_VERSION,
        true
    );

    // Localize script for AJAX
    wp_localize_script('mydefenselaw-script', 'mydefenselawData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('mydefenselaw_contact'),
    ));
}
add_action('wp_enqueue_scripts', 'mydefenselaw_scripts');

/**
 * ACF Options Page Setup
 */
function mydefenselaw_acf_options_page() {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page(array(
            'page_title'  => __('Theme Settings', 'mydefenselaw'),
            'menu_title'  => __('Theme Settings', 'mydefenselaw'),
            'menu_slug'   => 'theme-settings',
            'capability'  => 'manage_options',
            'redirect'    => false,
            'icon_url'    => 'dashicons-admin-generic',
        ));

        acf_add_options_sub_page(array(
            'page_title'  => __('Contact Information', 'mydefenselaw'),
            'menu_title'  => __('Contact Info', 'mydefenselaw'),
            'parent_slug' => 'theme-settings',
        ));

        acf_add_options_sub_page(array(
            'page_title'  => __('Social Media', 'mydefenselaw'),
            'menu_title'  => __('Social Media', 'mydefenselaw'),
            'parent_slug' => 'theme-settings',
        ));
    }
}
add_action('acf/init', 'mydefenselaw_acf_options_page');

/**
 * Disable Gutenberg for Front Page
 *
 * ACF's hide_on_screen only works with the Classic Editor.
 * Since the homepage is entirely managed via ACF fields, we disable
 * Gutenberg for the front page to enable hide_on_screen functionality.
 */
function mydefenselaw_disable_gutenberg_for_front_page($use_block_editor, $post) {
    if (!$post) {
        return $use_block_editor;
    }

    // Disable Gutenberg for the static front page
    if (absint(get_option('page_on_front')) === $post->ID) {
        return false;
    }

    // Also disable for pages using the front-page.php template
    if (get_page_template_slug($post->ID) === 'front-page.php') {
        return false;
    }

    return $use_block_editor;
}
add_filter('use_block_editor_for_post', 'mydefenselaw_disable_gutenberg_for_front_page', 100, 2);

/**
 * Remove editor support from front page post type
 */
function mydefenselaw_remove_editor_from_front_page() {
    $front_page_id = get_option('page_on_front');
    if ($front_page_id && is_admin() && isset($_GET['post']) && absint($_GET['post']) == $front_page_id) {
        remove_post_type_support('page', 'editor');
    }
}
add_action('admin_init', 'mydefenselaw_remove_editor_from_front_page');

/**
 * ACF JSON Save Point
 */
function mydefenselaw_acf_json_save_point($path) {
    return get_stylesheet_directory() . '/acf-json';
}
add_filter('acf/settings/save_json', 'mydefenselaw_acf_json_save_point');

/**
 * ACF JSON Load Point
 */
function mydefenselaw_acf_json_load_point($paths) {
    unset($paths[0]);
    $paths[] = get_stylesheet_directory() . '/acf-json';
    return $paths;
}
add_filter('acf/settings/load_json', 'mydefenselaw_acf_json_load_point');

/**
 * Fallback Menu
 */
function mydefenselaw_fallback_menu() {
    ?>
    <ul class="nav-menu">
        <li><a href="<?php echo esc_url(home_url('/')); ?>" class="nav-link<?php echo is_front_page() ? ' active' : ''; ?>">Home</a></li>
        <li><a href="<?php echo esc_url(home_url('/about/')); ?>" class="nav-link">About The Firm</a></li>
        <li><a href="<?php echo esc_url(home_url('/news/')); ?>" class="nav-link">Latest News</a></li>
        <li><a href="<?php echo esc_url(home_url('/resources/')); ?>" class="nav-link">Resources</a></li>
        <li><a href="<?php echo esc_url(home_url('/contact/')); ?>" class="nav-link">Contact Us</a></li>
    </ul>
    <?php
}

/**
 * Footer Menu Fallbacks
 */
function mydefenselaw_footer_legal_fallback() {
    ?>
    <ul>
        <li><a href="<?php echo esc_url(home_url('/civil-defense-litigation/')); ?>"><?php esc_html_e('Civil Defense Litigation', 'mydefenselaw'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/consumer-protection/')); ?>"><?php esc_html_e('Consumer Protection', 'mydefenselaw'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/bankruptcy-law/')); ?>"><?php esc_html_e('Bankruptcy Law', 'mydefenselaw'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/contract-law/')); ?>"><?php esc_html_e('Contract Law', 'mydefenselaw'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/family-law/')); ?>"><?php esc_html_e('Family Law', 'mydefenselaw'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/real-estate-law/')); ?>"><?php esc_html_e('Real Estate Law', 'mydefenselaw'); ?></a></li>
    </ul>
    <?php
}

function mydefenselaw_footer_about_fallback() {
    ?>
    <ul>
        <li><a href="<?php echo esc_url(home_url('/about/')); ?>"><?php esc_html_e('Our Firm', 'mydefenselaw'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/about/#attorneys')); ?>"><?php esc_html_e('Our Attorneys', 'mydefenselaw'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/news/')); ?>"><?php esc_html_e('Latest News', 'mydefenselaw'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/resources/')); ?>"><?php esc_html_e('Legal Resources', 'mydefenselaw'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/sitemap/')); ?>"><?php esc_html_e('Site Map', 'mydefenselaw'); ?></a></li>
    </ul>
    <?php
}

/**
 * Include Additional Files
 */
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/template-tags.php';
require_once get_template_directory() . '/inc/contact-form-handler.php';
require_once get_template_directory() . '/inc/custom-post-types.php';
require_once get_template_directory() . '/inc/acf-sync-helper.php';
