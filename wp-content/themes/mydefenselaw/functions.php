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
    // Add default posts RSS feed links to head (comments disabled)
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
 * Enqueue Client Portal Scripts
 *
 * Loads portal-specific JavaScript with localized AJAX data and nonces.
 * Only loads on portal pages to reduce overhead.
 */
function mydefenselaw_portal_enqueue_scripts() {
	// Only load on portal pages
	if (!mydefenselaw_portal_is_portal_page()) {
		return;
	}

	// Enqueue portal JavaScript (create this file if needed)
	wp_enqueue_script(
		'mydefenselaw-portal-script',
		get_template_directory_uri() . '/assets/js/portal.js',
		array('jquery'),
		MYDEFENSELAW_VERSION,
		true
	);

	// Localize script with AJAX URL and nonces
	wp_localize_script('mydefenselaw-portal-script', 'portalData', array(
		'ajaxUrl' => admin_url('admin-ajax.php'),
		'nonces' => array(
			'upload_document' => mydefenselaw_portal_create_nonce('upload_document'),
			'delete_document' => mydefenselaw_portal_create_nonce('delete_document'),
			'create_folder' => mydefenselaw_portal_create_nonce('create_folder'),
			'move_document' => mydefenselaw_portal_create_nonce('move_document'),
			'send_message' => mydefenselaw_portal_create_nonce('send_message'),
			'mark_read' => mydefenselaw_portal_create_nonce('mark_read'),
			'get_messages' => mydefenselaw_portal_create_nonce('get_messages'),
			'update_profile' => mydefenselaw_portal_create_nonce('update_profile'),
			'change_password' => mydefenselaw_portal_create_nonce('change_password'),
			'update_notifications' => mydefenselaw_portal_create_nonce('update_notifications'),
		),
		'i18n' => array(
			'uploadError' => __('Upload failed. Please try again.', 'mydefenselaw'),
			'sendError' => __('Failed to send message. Please try again.', 'mydefenselaw'),
			'confirmDelete' => __('Are you sure you want to delete this?', 'mydefenselaw'),
			'loading' => __('Loading...', 'mydefenselaw'),
			'success' => __('Success!', 'mydefenselaw'),
			'error' => __('Error', 'mydefenselaw'),
		),
	));

	// Enqueue portal styles if needed
	wp_enqueue_style(
		'mydefenselaw-portal-style',
		get_template_directory_uri() . '/assets/css/portal.css',
		array('mydefenselaw-style'),
		MYDEFENSELAW_VERSION
	);
}
add_action('wp_enqueue_scripts', 'mydefenselaw_portal_enqueue_scripts');

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
 * Disable Gutenberg for Pages with Custom Templates
 *
 * ACF's hide_on_screen only works with the Classic Editor.
 * Since all custom page templates are managed via ACF fields, we disable
 * Gutenberg for any page using a custom template.
 */
function mydefenselaw_disable_gutenberg_for_custom_templates($use_block_editor, $post) {
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
    // Custom templates have a non-empty slug (default template has empty slug)
    if (!empty($template)) {
        return false;
    }

    return $use_block_editor;
}
add_filter('use_block_editor_for_post', 'mydefenselaw_disable_gutenberg_for_custom_templates', 100, 2);

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
 * Custom Nav Walker - Adds nav-link class to menu items
 */
class MyDefenseLaw_Nav_Walker extends Walker_Nav_Menu {
    /**
     * Start the element output.
     *
     * @param string   $output Used to append additional content (passed by reference).
     * @param WP_Post  $item   Menu item data object.
     * @param int      $depth  Depth of menu item. Used for padding.
     * @param stdClass $args   An object of wp_nav_menu() arguments.
     * @param int      $id     Current item ID.
     */
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
        $classes[] = 'menu-item-' . $item->ID;

        /**
         * Filters the arguments for a single nav menu item.
         *
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param WP_Post  $item  Menu item data object.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $args = apply_filters('nav_menu_item_args', $args, $item, $depth);

        /**
         * Filters the CSS classes applied to a menu item's list item element.
         *
         * @param string[] $classes Array of the CSS classes that are applied to the menu item's `<li>` element.
         * @param WP_Post  $item    The current menu item.
         * @param stdClass $args    An object of wp_nav_menu() arguments.
         * @param int      $depth   Depth of menu item. Used for padding.
         */
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        /**
         * Filters the ID applied to a menu item's list item element.
         *
         * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
         * @param WP_Post  $item    The current menu item.
         * @param stdClass $args    An object of wp_nav_menu() arguments.
         * @param int      $depth   Depth of menu item. Used for padding.
         */
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

        /**
         * Filters the HTML attributes applied to a menu item's anchor element.
         *
         * @param array $atts {
         *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
         *
         *     @type string $title        Title attribute.
         *     @type string $target       Target attribute.
         *     @type string $rel          The rel attribute.
         *     @type string $href         The href attribute.
         *     @type string $aria_current The aria-current attribute.
         * }
         * @param WP_Post  $item  The current menu item.
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (is_scalar($value) && '' !== $value && false !== $value) {
                $value       = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        /** This filter is documented in wp-includes/post-template.php */
        $title = apply_filters('the_title', $item->title, $item->ID);

        /**
         * Filters a menu item's title.
         *
         * @param string   $title The menu item's title.
         * @param WP_Post  $item  The current menu item.
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

        $item_output  = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . $title . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        /**
         * Filters a menu item's starting output.
         *
         * @param string   $item_output The menu item's starting HTML output.
         * @param WP_Post  $item        Menu item data object.
         * @param int      $depth       Depth of menu item. Used for padding.
         * @param stdClass $args        An object of wp_nav_menu() arguments.
         */
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}

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
require_once get_template_directory() . '/inc/form-submissions-cpt.php';
require_once get_template_directory() . '/inc/acf-sync-helper.php';
require_once get_template_directory() . '/inc/disable-posts.php';
require_once get_template_directory() . '/inc/disable-comments.php';
require_once get_template_directory() . '/inc/admin-customization.php';
require_once get_template_directory() . '/inc/translatepress-language-menu.php';
require_once get_template_directory() . '/inc/seo-schema.php';
require_once get_template_directory() . '/inc/client-portal/init.php';
