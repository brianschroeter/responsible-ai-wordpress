<?php
/**
 * TranslatePress Language Menu Item
 *
 * Adds a conditional language switcher to the primary menu.
 * - On English pages: Shows "En Español" linking to Spanish version
 * - On Spanish pages: Shows "English" linking to English version
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add conditional language menu item to primary navigation
 *
 * @param string   $items The HTML list content for the menu items.
 * @param stdClass $args  An object containing wp_nav_menu() arguments.
 * @return string Modified menu items HTML.
 */
function mydefenselaw_add_language_menu_item($items, $args) {
    // Only add to primary menu
    if ($args->theme_location !== 'primary') {
        return $items;
    }

    // Check if TranslatePress is active
    if (!class_exists('TRP_Translate_Press')) {
        return $items;
    }

    // Get TranslatePress instance and settings
    $trp = TRP_Translate_Press::get_trp_instance();
    $trp_settings = $trp->get_component('settings');
    $settings = $trp_settings->get_settings();

    // Get URL converter component
    $url_converter = $trp->get_component('url_converter');

    // Get current language
    global $TRP_LANGUAGE;
    $current_language = isset($TRP_LANGUAGE) ? $TRP_LANGUAGE : $settings['default-language'];

    // Determine the target language and link text
    $default_lang = $settings['default-language']; // en_US

    // Get the actual current URL from the browser (not TranslatePress's normalized version)
    $protocol = is_ssl() ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $request_uri = $_SERVER['REQUEST_URI'];
    $actual_current_url = $protocol . $host . $request_uri;

    // Check if we're actually on a Spanish page by looking at the URL
    $is_on_spanish_page = (strpos($request_uri, '/es/') === 0 || strpos($request_uri, '/es') === 0);

    if (!$is_on_spanish_page) {
        // We're on English - show link to Spanish
        $target_language = 'es_ES';
        $link_text = 'En Español';
        // Add /es/ after the host
        $target_url = $protocol . $host . '/es' . $request_uri;
    } else {
        // We're on Spanish - show link to English
        $target_language = $default_lang;
        $link_text = 'English';
        // Remove /es from the path
        $english_path = preg_replace('#^/es(/|$)#', '/', $request_uri);
        $target_url = $protocol . $host . $english_path;
    }

    // Build the menu item HTML
    $language_item = sprintf(
        '<li class="menu-item menu-item-language-switcher"><a href="%s" class="nav-link language-link" hreflang="%s">%s</a></li>',
        esc_url($target_url),
        esc_attr($target_language === 'es_ES' ? 'es' : 'en'),
        esc_html($link_text)
    );

    // Append to end of menu
    return $items . $language_item;
}
add_filter('wp_nav_menu_items', 'mydefenselaw_add_language_menu_item', 10, 2);

/**
 * Helper function to get current page URL
 * TranslatePress compatible
 *
 * @return string Current page URL
 */
function trp_get_current_page_url() {
    global $wp;

    // Use TranslatePress method if available
    if (class_exists('TRP_Translate_Press')) {
        $trp = TRP_Translate_Press::get_trp_instance();
        $url_converter = $trp->get_component('url_converter');
        if (method_exists($url_converter, 'cur_page_url')) {
            return $url_converter->cur_page_url();
        }
    }

    // Fallback to WordPress method
    return home_url(add_query_arg(array(), $wp->request));
}

/**
 * Add custom CSS for language menu item
 */
function mydefenselaw_language_menu_styles() {
    ?>
    <style>
        /* Language switcher menu item */
        .menu-item-language-switcher .language-link {
            font-weight: 600;
            position: relative;
        }

        .menu-item-language-switcher .language-link::before {
            content: '\f0ac'; /* Font Awesome globe icon */
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            margin-right: 6px;
            font-size: 0.9em;
        }

        /* Optional: Different styling for Spanish link */
        .menu-item-language-switcher .language-link[hreflang="es"]::before {
            content: '\f0ac';
        }
    </style>
    <?php
}
add_action('wp_head', 'mydefenselaw_language_menu_styles');
