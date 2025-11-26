<?php
/**
 * Language Switcher Menu Item
 * Simple PHP implementation - no TranslatePress API dependency
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Tell TranslatePress to skip URL replacement in language switcher
 */
function mydefenselaw_skip_language_switcher_translation($skip_selectors) {
    $skip_selectors[] = '.menu-item-language-switcher';
    $skip_selectors[] = '.language-link';
    $skip_selectors[] = 'a[data-trp-skip]';
    return $skip_selectors;
}
add_filter('trp_skip_selectors_from_replacing', 'mydefenselaw_skip_language_switcher_translation');

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
    // Using multiple attributes to prevent TranslatePress from modifying the URL:
    // - data-no-translation: Standard TranslatePress skip attribute
    // - data-trp-skip: Alternative TranslatePress skip attribute
    // - class "language-link": Excluded via trp_skip_selectors_from_replacing filter
    $language_item = '<li class="menu-item menu-item-language-switcher" data-no-translation>';
    $language_item .= '<a href="' . esc_attr($target_url) . '" class="nav-link language-link" hreflang="' . esc_attr($hreflang) . '" data-no-translation data-trp-skip>';
    $language_item .= '<span data-no-translation>' . esc_html($link_text) . '</span>';
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

/**
 * JavaScript fallback to fix language switcher URL
 * Runs client-side to correct any TranslatePress URL modifications
 */
function mydefenselaw_language_switcher_js() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var langLink = document.querySelector('.language-link');
        if (!langLink) return;

        var path = window.location.pathname;
        var isSpanish = path.indexOf('/es/') === 0 || path === '/es';
        var correctHref;

        if (isSpanish) {
            // On Spanish page - link should go to English (remove /es)
            correctHref = path.replace(/^\/es/, '') || '/';
        } else {
            // On English page - link should go to Spanish (add /es)
            correctHref = '/es' + path;
        }

        // Fix the href if TranslatePress modified it
        if (langLink.getAttribute('href') !== correctHref) {
            langLink.setAttribute('href', correctHref);
        }
    });
    </script>
    <?php
}
add_action('wp_footer', 'mydefenselaw_language_switcher_js', 99);
