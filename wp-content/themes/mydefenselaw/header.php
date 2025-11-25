<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php get_template_part('template-parts/global/top-bar'); ?>

<!-- Header -->
<header class="header">
    <div class="container">
        <div class="header-content">
            <div class="logo-section">
                <div class="logo">
                    <?php if (has_custom_logo()) : ?>
                        <?php the_custom_logo(); ?>
                    <?php else : ?>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="<?php bloginfo('name'); ?> - Attorneys at Law" class="logo-image">
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="header-contact">
                <div class="consultation-cta">
                    <span class="cta-label">Free Consultation</span>
                    <a href="tel:<?php echo esc_attr(str_replace('.', '', get_theme_mod('contact_phone', '8884440253'))); ?>" class="phone-main"><?php echo get_theme_mod('contact_phone', '888.444.0253'); ?></a>
                    <button class="btn-consultation" id="modalTriggerHeader">Get Started Today</button>
                </div>
            </div>

            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>

<!-- Navigation -->
<nav class="navigation" id="navigation">
    <div class="container">
        <?php
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'menu_class'     => 'nav-menu',
            'container'      => false,
            'fallback_cb'    => false,
            'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
            'walker'         => new class extends Walker_Nav_Menu {
                function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
                    $classes = empty($item->classes) ? array() : (array) $item->classes;
                    $classes[] = 'nav-link';

                    if ($item->current) {
                        $classes[] = 'active';
                    }

                    $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
                    $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

                    $output .= '<li>';

                    $attributes  = !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
                    $attributes .= $class_names;

                    // Add icon for Client Portal
                    $icon = '';
                    if (strpos($item->title, 'Client Portal') !== false || strpos($item->url, '/clients/') !== false) {
                        $icon = '<i class="fas fa-lock" style="margin-right: 0.5rem;"></i> ';
                        $attributes .= ' style="font-weight: 600;"';
                    }

                    $output .= '<a' . $attributes . '>';
                    $output .= $icon . apply_filters('the_title', $item->title, $item->ID);
                    $output .= '</a>';
                }
            }
        ));
        ?>
    </div>
</nav>

<!-- Page Header for non-homepage pages -->
<?php if (!is_front_page() && !is_home()) : ?>
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1><?php the_title(); ?></h1>
            <nav class="breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>">Home</a> / <span><?php the_title(); ?></span>
            </nav>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!is_front_page() && !is_home()) : ?>
<main class="main-content"><div class="container">
<?php endif; ?>
