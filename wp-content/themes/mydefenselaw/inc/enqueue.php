<?php
/**
 * Asset Enqueue Functions
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

/**
 * Enqueue conditional assets based on page template
 */
function mydefenselaw_conditional_assets() {
    // Load practice areas styles on relevant pages
    if (is_page_template('page-practice-areas.php') || is_singular('practice_area')) {
        wp_enqueue_style(
            'mydefenselaw-practice-areas',
            get_template_directory_uri() . '/assets/css/practice-areas.css',
            array('mydefenselaw-style'),
            MYDEFENSELAW_VERSION
        );
    }

    // Load contact page specific assets
    if (is_page_template('page-contact.php')) {
        wp_enqueue_style(
            'mydefenselaw-contact',
            get_template_directory_uri() . '/assets/css/contact.css',
            array('mydefenselaw-style'),
            MYDEFENSELAW_VERSION
        );
    }
}
add_action('wp_enqueue_scripts', 'mydefenselaw_conditional_assets', 20);

/**
 * Enqueue admin styles
 */
function mydefenselaw_admin_styles() {
    wp_enqueue_style(
        'mydefenselaw-admin',
        get_template_directory_uri() . '/assets/css/admin.css',
        array(),
        MYDEFENSELAW_VERSION
    );
}
add_action('admin_enqueue_scripts', 'mydefenselaw_admin_styles');

/**
 * Add async/defer attributes to scripts
 */
function mydefenselaw_script_attributes($tag, $handle, $src) {
    $async_scripts = array('mydefenselaw-script');

    if (in_array($handle, $async_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'mydefenselaw_script_attributes', 10, 3);

/**
 * Preload critical assets
 */
function mydefenselaw_preload_assets() {
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="<?php echo get_template_directory_uri(); ?>/assets/css/main.css" as="style">
    <?php
}
add_action('wp_head', 'mydefenselaw_preload_assets', 1);
