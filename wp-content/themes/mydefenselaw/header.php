<?php
/**
 * The header template
 * Pixel-perfect match of source site
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

$phone = get_theme_mod('contact_phone', '888.444.0253');
$phone_raw = preg_replace('/[^0-9]/', '', $phone);
$is_homepage = is_front_page();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#main-content">
        <?php esc_html_e('Skip to content', 'mydefenselaw'); ?>
    </a>

    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="top-bar-content">
                <div class="contact-quick">
                    <span><i class="fas fa-phone"></i> <?php echo esc_html($phone); ?></span>
                    <span><i class="fas fa-map-marker-alt"></i> Boca Raton, FL</span>
                    <span><i class="fas fa-clock"></i> Available 24/7</span>
                </div>
                <div class="emergency-notice">
                    <strong>Emergency? Call Now for Immediate Help</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo-section">
                    <div class="logo">
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <?php if (has_custom_logo()) : ?>
                                <?php
                                $custom_logo_id = get_theme_mod('custom_logo');
                                $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                                ?>
                                <img src="<?php echo esc_url($logo[0]); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="logo-image" width="200" height="80">
                            <?php else : ?>
                                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/logo.png" alt="Defense Lawyers, P.A. - Attorneys at Law" class="logo-image" width="200" height="80">
                            <?php endif; ?>
                        </a>
                    </div>
                </div>

                <div class="header-contact">
                    <div class="consultation-cta">
                        <span class="cta-label">Free Consultation</span>
                        <a href="tel:<?php echo esc_attr($phone_raw); ?>" class="phone-main"><?php echo esc_html($phone); ?></a>
                        <button class="btn-consultation consultation-trigger" id="modalTriggerHeader">Get Started Today</button>
                    </div>
                </div>

                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle navigation" aria-expanded="false" aria-controls="navigation">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="navigation" id="navigation" aria-label="<?php esc_attr_e('Main navigation', 'mydefenselaw'); ?>">
        <div class="container">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_id'        => 'primary-menu',
                'menu_class'     => 'nav-menu',
                'container'      => false,
                'fallback_cb'    => 'mydefenselaw_fallback_menu',
                'walker'         => new MyDefenseLaw_Nav_Walker(),
            ));
            ?>
        </div>
    </nav>

    <main id="main-content" class="site-main">
