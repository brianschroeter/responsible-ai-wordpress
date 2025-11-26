<?php
/**
 * Responsible AI Theme Functions
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define theme version constant
define('RESPONSIBLEAI_VERSION', '1.0.0');

/**
 * Theme Setup
 */
function responsibleai_setup() {
    // Load text domain for translations
    load_theme_textdomain('responsible-ai', get_template_directory() . '/languages');

    // Add default posts RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails
    add_theme_support('post-thumbnails');

    // Switch default core markup to output valid HTML5
    add_theme_support('html5', array(
        'search-form',
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
        'primary'          => __('Primary Menu', 'responsible-ai'),
        'footer-about'     => __('Footer About', 'responsible-ai'),
        'footer-resources' => __('Footer Resources', 'responsible-ai'),
        'footer-community' => __('Footer Community', 'responsible-ai'),
        'footer-legal'     => __('Footer Legal', 'responsible-ai'),
    ));

    // Add custom image sizes
    add_image_size('hero-slide', 1920, 1080, true);
    add_image_size('team-photo', 400, 500, true);
    add_image_size('partner-logo', 200, 100, false);
    add_image_size('resource-thumb', 400, 300, true);
    add_image_size('blog-card', 800, 450, true);
}
add_action('after_setup_theme', 'responsibleai_setup');

/**
 * Register Widget Areas
 */
function responsibleai_widgets_init() {
    // Footer widgets
    register_sidebar(array(
        'name'          => __('Footer Widgets', 'responsible-ai'),
        'id'            => 'footer-widgets',
        'description'   => __('Widgets for footer area', 'responsible-ai'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="footer-widget-title">',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'responsibleai_widgets_init');

/**
 * Enqueue Scripts and Styles
 */
function responsibleai_scripts() {
    // Google Fonts - Roboto
    wp_enqueue_style(
        'responsibleai-fonts',
        'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap',
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

    // Swiper.js CSS (for carousels)
    wp_enqueue_style(
        'swiper',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
        array(),
        '11.0.0'
    );

    // Main stylesheet
    wp_enqueue_style(
        'responsibleai-style',
        get_template_directory_uri() . '/assets/css/main.css',
        array('swiper'),
        RESPONSIBLEAI_VERSION
    );

    // Swiper.js (for carousels)
    wp_enqueue_script(
        'swiper',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
        array(),
        '11.0.0',
        true
    );

    // Main JavaScript
    wp_enqueue_script(
        'responsibleai-script',
        get_template_directory_uri() . '/assets/js/main.js',
        array('swiper'),
        RESPONSIBLEAI_VERSION,
        true
    );

    // Localize script for AJAX
    wp_localize_script('responsibleai-script', 'responsibleaiData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('responsibleai_contact'),
        'siteUrl' => home_url('/'),
    ));

    // Localize translatable strings for main.js
    wp_localize_script('responsibleai-script', 'responsibleaiStrings', array(
        'formRequired'     => __('This field is required', 'responsible-ai'),
        'emailInvalid'     => __('Please enter a valid email address', 'responsible-ai'),
        'fieldsRequired'   => __('Please fill in all required fields correctly.', 'responsible-ai'),
        'thankYou'         => __('Thank you! We\'ll be in touch soon.', 'responsible-ai'),
        'success'          => __('Success!', 'responsible-ai'),
        'errorOccurred'    => __('Something went wrong. Please try again.', 'responsible-ai'),
        'formError'        => __('Error submitting form', 'responsible-ai'),
        'sending'          => __('Sending...', 'responsible-ai'),
        'subscribeSuccess' => __('Thank you for subscribing!', 'responsible-ai'),
    ));
}
add_action('wp_enqueue_scripts', 'responsibleai_scripts');

/**
 * ACF Options Page Setup
 */
function responsibleai_acf_options_page() {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page(array(
            'page_title'  => __('Theme Settings', 'responsible-ai'),
            'menu_title'  => __('Theme Settings', 'responsible-ai'),
            'menu_slug'   => 'theme-settings',
            'capability'  => 'manage_options',
            'redirect'    => false,
            'icon_url'    => 'dashicons-admin-settings',
            'position'    => 59,
        ));

        acf_add_options_sub_page(array(
            'page_title'  => __('Organization Info', 'responsible-ai'),
            'menu_title'  => __('Organization Info', 'responsible-ai'),
            'parent_slug' => 'theme-settings',
        ));

        acf_add_options_sub_page(array(
            'page_title'  => __('Social Media', 'responsible-ai'),
            'menu_title'  => __('Social Media', 'responsible-ai'),
            'parent_slug' => 'theme-settings',
        ));

        acf_add_options_sub_page(array(
            'page_title'  => __('Newsletter Settings', 'responsible-ai'),
            'menu_title'  => __('Newsletter', 'responsible-ai'),
            'parent_slug' => 'theme-settings',
        ));
    }
}
add_action('acf/init', 'responsibleai_acf_options_page');

/**
 * Disable Gutenberg for Pages with Custom Templates
 *
 * ACF's hide_on_screen only works with the Classic Editor.
 * Since all custom page templates are managed via ACF fields, we disable
 * Gutenberg for any page using a custom template.
 */
function responsibleai_disable_gutenberg_for_custom_templates($use_block_editor, $post) {
    if (!$post || $post->post_type !== 'page') {
        return $use_block_editor;
    }

    // Disable Gutenberg for the static front page
    if (absint(get_option('page_on_front')) === $post->ID) {
        return false;
    }

    // Get the page template slug
    $template = get_page_template_slug($post->ID);

    // Disable Gutenberg for any page with a custom template assigned
    if (!empty($template)) {
        return false;
    }

    return $use_block_editor;
}
add_filter('use_block_editor_for_post', 'responsibleai_disable_gutenberg_for_custom_templates', 100, 2);

/**
 * Remove editor support from front page
 */
function responsibleai_remove_editor_from_front_page() {
    $front_page_id = get_option('page_on_front');
    if ($front_page_id && is_admin() && isset($_GET['post']) && absint($_GET['post']) == $front_page_id) {
        remove_post_type_support('page', 'editor');
    }
}
add_action('admin_init', 'responsibleai_remove_editor_from_front_page');

/**
 * ACF JSON Save Point
 */
function responsibleai_acf_json_save_point($path) {
    return get_stylesheet_directory() . '/acf-json';
}
add_filter('acf/settings/save_json', 'responsibleai_acf_json_save_point');

/**
 * ACF JSON Load Point
 */
function responsibleai_acf_json_load_point($paths) {
    unset($paths[0]);
    $paths[] = get_stylesheet_directory() . '/acf-json';
    return $paths;
}
add_filter('acf/settings/load_json', 'responsibleai_acf_json_load_point');

/**
 * Custom Nav Walker - Adds nav-link class to menu items
 */
class ResponsibleAI_Nav_Walker extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = ($depth) ? str_repeat($t, $depth) : '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'nav-item';

        $args = apply_filters('nav_menu_item_args', $args, $item, $depth);

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names . '>';

        $atts = array();
        $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        if ('_blank' === $item->target && empty($item->xfn)) {
            $atts['rel'] = 'noopener';
        } else {
            $atts['rel'] = $item->xfn;
        }
        $atts['href'] = !empty($item->url) ? $item->url : '';
        $atts['aria-current'] = $item->current ? 'page' : '';

        // Add nav-link class to all menu item links
        $link_classes = array('nav-link');

        // Add active class to current menu item
        if ($item->current || $item->current_item_ancestor || $item->current_item_parent) {
            $link_classes[] = 'active';
        }

        $atts['class'] = implode(' ', $link_classes);

        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (is_scalar($value) && '' !== $value && false !== $value) {
                $value       = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title = apply_filters('the_title', $item->title, $item->ID);
        $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

        $item_output  = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . $title . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}

/**
 * Fallback Menu
 */
function responsibleai_fallback_menu() {
    ?>
    <ul class="nav-menu">
        <li class="nav-item"><a href="<?php echo esc_url(home_url('/')); ?>" class="nav-link<?php echo is_front_page() ? ' active' : ''; ?>"><?php esc_html_e('Home', 'responsible-ai'); ?></a></li>
        <li class="nav-item"><a href="<?php echo esc_url(home_url('/who-we-are/')); ?>" class="nav-link"><?php esc_html_e('Who We Are', 'responsible-ai'); ?></a></li>
        <li class="nav-item"><a href="<?php echo esc_url(home_url('/raise-pathways/')); ?>" class="nav-link"><?php esc_html_e('RAISE Pathways', 'responsible-ai'); ?></a></li>
        <li class="nav-item"><a href="<?php echo esc_url(home_url('/tools-guides/')); ?>" class="nav-link"><?php esc_html_e('Tools & Guides', 'responsible-ai'); ?></a></li>
        <li class="nav-item"><a href="<?php echo esc_url(home_url('/join/')); ?>" class="nav-link"><?php esc_html_e('Join', 'responsible-ai'); ?></a></li>
    </ul>
    <?php
}

/**
 * Footer Menu Fallbacks
 */
function responsibleai_footer_about_fallback() {
    ?>
    <ul>
        <li><a href="<?php echo esc_url(home_url('/who-we-are/')); ?>"><?php esc_html_e('Who We Are', 'responsible-ai'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/raise-pathways/')); ?>"><?php esc_html_e('RAISE Pathways', 'responsible-ai'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/news/')); ?>"><?php esc_html_e('News', 'responsible-ai'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/careers/')); ?>"><?php esc_html_e('Careers', 'responsible-ai'); ?></a></li>
    </ul>
    <?php
}

function responsibleai_footer_resources_fallback() {
    ?>
    <ul>
        <li><a href="<?php echo esc_url(home_url('/tools-guides/')); ?>"><?php esc_html_e('Tools & Guides', 'responsible-ai'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/ai-maturity-assessment/')); ?>"><?php esc_html_e('AI Maturity Assessment', 'responsible-ai'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/responsible-ai-handbook/')); ?>"><?php esc_html_e('RAI Handbook', 'responsible-ai'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/case-studies/')); ?>"><?php esc_html_e('Case Studies', 'responsible-ai'); ?></a></li>
    </ul>
    <?php
}

function responsibleai_footer_community_fallback() {
    ?>
    <ul>
        <li><a href="<?php echo esc_url(home_url('/community/')); ?>"><?php esc_html_e('Community', 'responsible-ai'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/events/')); ?>"><?php esc_html_e('Events', 'responsible-ai'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/membership/')); ?>"><?php esc_html_e('Membership', 'responsible-ai'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact', 'responsible-ai'); ?></a></li>
    </ul>
    <?php
}

function responsibleai_footer_legal_fallback() {
    ?>
    <ul>
        <li><a href="<?php echo esc_url(home_url('/privacy/')); ?>"><?php esc_html_e('Privacy Policy', 'responsible-ai'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/terms/')); ?>"><?php esc_html_e('Terms of Service', 'responsible-ai'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/ai-code-of-conduct/')); ?>"><?php esc_html_e('AI Code of Conduct', 'responsible-ai'); ?></a></li>
    </ul>
    <?php
}

/**
 * Include Additional Files
 * Load in dependency order
 */
require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/template-tags.php';
require_once get_template_directory() . '/inc/email-templates.php';
require_once get_template_directory() . '/inc/contact-form-handler.php';
require_once get_template_directory() . '/inc/custom-post-types.php';
require_once get_template_directory() . '/inc/acf-sync-helper.php';
require_once get_template_directory() . '/inc/seo-schema.php';
require_once get_template_directory() . '/inc/admin-customization.php';
require_once get_template_directory() . '/inc/disable-comments.php';
