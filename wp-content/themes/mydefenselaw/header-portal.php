<?php
/**
 * Portal Header Template
 * Simplified header for client portal pages
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

$phone = get_theme_mod('contact_phone', '888.444.0253');
$phone_raw = preg_replace('/[^0-9]/', '', $phone);

// Get current user
$current_user = wp_get_current_user();
$unread_count = 0;

// Get unread message count if client helper exists
if (function_exists('mydefenselaw_portal_get_unread_message_count')) {
    $unread_count = mydefenselaw_portal_get_unread_message_count($current_user->ID);
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>

<body <?php body_class('portal-page'); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site portal-site">
    <a class="skip-link screen-reader-text" href="#portal-main-content">
        <?php esc_html_e('Skip to content', 'mydefenselaw'); ?>
    </a>

    <!-- Portal Header -->
    <header class="portal-header">
        <div class="container">
            <div class="portal-header-content">
                <div class="portal-logo">
                    <a href="<?php echo esc_url(home_url('/portal/dashboard/')); ?>">
                        <?php if (has_custom_logo()) : ?>
                            <?php
                            $custom_logo_id = get_theme_mod('custom_logo');
                            $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                            ?>
                            <img src="<?php echo esc_url($logo[0]); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?> - Client Portal" class="logo-image" width="150" height="60">
                        <?php else : ?>
                            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/logo.png" alt="<?php echo esc_attr(get_bloginfo('name')); ?> - Client Portal" class="logo-image" width="150" height="60">
                        <?php endif; ?>
                        <span class="portal-badge">Client Portal</span>
                    </a>
                </div>

                <!-- Portal Navigation -->
                <nav class="portal-nav" aria-label="<?php esc_attr_e('Portal navigation', 'mydefenselaw'); ?>">
                    <ul class="portal-menu" id="portalMenu">
                        <li class="<?php echo (is_page('portal-dashboard') ? 'active' : ''); ?>">
                            <a href="<?php echo esc_url(home_url('/portal/dashboard/')); ?>">
                                <i class="fas fa-th-large"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="<?php echo (is_page('portal-documents') ? 'active' : ''); ?>">
                            <a href="<?php echo esc_url(home_url('/portal/documents/')); ?>">
                                <i class="fas fa-folder-open"></i>
                                <span>Documents</span>
                            </a>
                        </li>
                        <li class="<?php echo (is_page('portal-messages') ? 'active' : ''); ?>">
                            <a href="<?php echo esc_url(home_url('/portal/messages/')); ?>">
                                <i class="fas fa-envelope"></i>
                                <span>Messages</span>
                                <?php if ($unread_count > 0) : ?>
                                    <span class="badge" data-count="<?php echo esc_attr($unread_count); ?>"><?php echo esc_html($unread_count); ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="<?php echo (is_page('portal-cases') ? 'active' : ''); ?>">
                            <a href="<?php echo esc_url(home_url('/portal/cases/')); ?>">
                                <i class="fas fa-briefcase"></i>
                                <span>Cases</span>
                            </a>
                        </li>
                        <li class="<?php echo (is_page('portal-profile') ? 'active' : ''); ?>">
                            <a href="<?php echo esc_url(home_url('/portal/profile/')); ?>">
                                <i class="fas fa-user"></i>
                                <span>Profile</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(wp_logout_url(home_url('/portal/login/'))); ?>" class="logout-link">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Mobile Menu Toggle -->
                <button class="portal-mobile-menu-btn" id="portalMobileMenuBtn" aria-label="Toggle navigation" aria-expanded="false" aria-controls="portalMenu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <!-- User Info -->
                <div class="portal-user-info">
                    <span class="user-greeting">Welcome, <strong><?php echo esc_html($current_user->first_name ?: $current_user->display_name); ?></strong></span>
                    <a href="<?php echo esc_url(home_url('/portal/profile/')); ?>" class="user-avatar" aria-label="View profile">
                        <?php echo get_avatar($current_user->ID, 32, '', '', array('class' => 'avatar-img')); ?>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main id="portal-main-content" class="portal-main">
