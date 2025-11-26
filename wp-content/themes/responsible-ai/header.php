<?php
/**
 * Header Template
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$org_info = responsibleai_get_organization_info();
$social = responsibleai_get_social_links();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link sr-only" href="#main-content">
    <?php esc_html_e('Skip to main content', 'responsible-ai'); ?>
</a>

<header class="site-header" id="site-header">
    <div class="container">
        <div class="site-header__inner">
            <!-- Logo -->
            <div class="site-header__logo">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" rel="home">
                    <?php if (has_custom_logo()) : ?>
                        <?php the_custom_logo(); ?>
                    <?php else : ?>
                        <span class="site-logo__text"><?php bloginfo('name'); ?></span>
                    <?php endif; ?>
                </a>
            </div>

            <!-- Primary Navigation -->
            <nav class="site-nav" id="site-nav" aria-label="<?php esc_attr_e('Primary navigation', 'responsible-ai'); ?>">
                <?php
                if (has_nav_menu('primary')) {
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_class'     => 'nav-menu',
                        'container'      => false,
                        'walker'         => new ResponsibleAI_Nav_Walker(),
                        'fallback_cb'    => 'responsibleai_fallback_menu',
                    ));
                } else {
                    responsibleai_fallback_menu();
                }
                ?>
            </nav>

            <!-- Header Actions -->
            <div class="site-header__actions">
                <a href="<?php echo esc_url(home_url('/join/')); ?>" class="btn btn-cta btn-sm">
                    <?php esc_html_e('Join', 'responsible-ai'); ?>
                </a>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" id="mobile-menu-toggle"
                        aria-controls="site-nav"
                        aria-expanded="false"
                        aria-label="<?php esc_attr_e('Toggle navigation menu', 'responsible-ai'); ?>">
                    <span class="hamburger">
                        <span class="hamburger__line"></span>
                        <span class="hamburger__line"></span>
                        <span class="hamburger__line"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</header>

<main id="main-content" class="site-main">
