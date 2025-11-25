<?php
/**
 * Language Switcher Menu Item
 * Simple PHP implementation - no TranslatePress API dependency
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add language switcher to primary menu
 */
function mydefenselaw_add_language_menu_item($items, $args) {
    // Only add to primary menu
    if ($args->theme_location !== 'primary') {
        return $items;
    }

    // Get current URL path
    $path = $_SERVER['REQUEST_URI'];

    // Simple check: does the path start with /es/?
    if (strpos($path, '/es/') === 0 || $path === '/es') {
        // On Spanish page - link to English
        // Remove /es from the beginning
        $english_path = preg_replace('#^/es#', '', $path);
        if (empty($english_path)) {
            $english_path = '/';
        }

        $link_text = 'English';
        $target_url = $english_path;
        $hreflang = 'en';
    } else {
        // On English page - link to Spanish
        // Add /es to the beginning
        $target_url = '/es' . $path;
        $link_text = 'En Español';
        $hreflang = 'es';
    }

    // Build menu item
    // NOTE: Using esc_attr() instead of esc_url() because TranslatePress
    // hooks into esc_url() and adds the current language prefix to ALL URLs
    $language_item = '<li class="menu-item menu-item-language-switcher">';
    $language_item .= '<a href="' . esc_attr($target_url) . '" class="nav-link language-link" hreflang="' . esc_attr($hreflang) . '">';
    $language_item .= esc_html($link_text);
    $language_item .= '</a></li>';

    return $items . $language_item;
}
add_filter('wp_nav_menu_items', 'mydefenselaw_add_language_menu_item', 10, 2);

/**
 * CSS for language switcher
 */
function mydefenselaw_language_menu_styles() {
    ?>
    <style>
        .menu-item-language-switcher .language-link {
            font-weight: 600;
        }
        .menu-item-language-switcher .language-link::before {
            content: '\f0ac';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            margin-right: 6px;
            font-size: 0.9em;
        }
    </style>
    <?php
}
add_action('wp_head', 'mydefenselaw_language_menu_styles');
