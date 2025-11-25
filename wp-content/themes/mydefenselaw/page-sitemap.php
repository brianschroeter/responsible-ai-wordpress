<?php
/**
 * Template Name: Site Map
 *
 * Custom page template for the Site Map page
 * Displays all site pages, practice areas, and resources in an organized grid layout
 * Uses official WordPress menus and dynamic queries for all links
 * Matches the live site at mydefenselaw.com/site_map.php
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

get_header();

// Get phone from theme options
$phone = get_field('contact_phone', 'option') ?: '888.444.0253';
$phone_raw = preg_replace('/[^0-9]/', '', $phone);

// Get address from theme options
$address_city = get_field('contact_city', 'option') ?: 'Boca Raton';
$address_state = get_field('contact_state', 'option') ?: 'Florida';
$office_hours = get_field('office_hours', 'option') ?: 'Mon - Fri: 9:00 AM - 5:00 PM';
$service_area = get_field('service_area', 'option') ?: 'Serving All of South Florida';

// ============================================================================
// DYNAMIC DATA: Main Pages from Primary Menu
// ============================================================================
$main_menu_items = wp_get_nav_menu_items('primary-menu');
if (!$main_menu_items) {
    // Fallback: Get menu by location if menu name doesn't work
    $menu_locations = get_nav_menu_locations();
    if (isset($menu_locations['primary'])) {
        $main_menu_items = wp_get_nav_menu_items($menu_locations['primary']);
    }
}

// ============================================================================
// DYNAMIC DATA: Practice Areas from Custom Post Type
// ============================================================================
// Use suppress_filters to prevent TranslatePress from translating titles
// This ensures English titles display in the English Practice Areas section
$practice_areas = get_posts(array(
    'post_type'        => 'practice_area',
    'posts_per_page'   => -1,
    'post_status'      => 'publish',
    'orderby'          => 'menu_order title',
    'order'            => 'ASC',
    'suppress_filters' => true,
));

// ============================================================================
// DYNAMIC DATA: Privacy Policy (WordPress Settings > Privacy)
// ============================================================================
// Get WordPress privacy policy page (may be set in Settings > Privacy)
$privacy_policy_page_id = get_option('wp_page_for_privacy_policy');
$privacy_policy_url = $privacy_policy_page_id ? get_permalink($privacy_policy_page_id) : '';

// ============================================================================
// DYNAMIC DATA: Spanish Pages (TranslatePress integration)
// ============================================================================
// Build Spanish URLs dynamically from main menu items
$spanish_pages = array();
if ($main_menu_items) {
    foreach ($main_menu_items as $item) {
        // Skip Client Portal from Spanish menu (usually not translated)
        if (stripos($item->title, 'portal') !== false) {
            continue;
        }

        // Get the page URL and create Spanish version
        $url = $item->url;
        $parsed = parse_url($url);
        $path = isset($parsed['path']) ? $parsed['path'] : '/';

        // Build Spanish URL using TranslatePress pattern
        $spanish_url = home_url('/es' . $path);

        // Spanish translations for common page titles
        $spanish_titles = array(
            'Home'             => 'Página Principal',
            'About The Firm'   => 'Sobre Nosotros',
            'About'            => 'Sobre Nosotros',
            'Latest Legal News'=> 'Noticias Legales',
            'News'             => 'Noticias',
            'Resources'        => 'Recursos Legales',
            'Legal Resources'  => 'Recursos Legales',
            'Contact Us'       => 'Contáctenos',
            'Contact'          => 'Contáctenos',
            'Practice Areas'   => 'Áreas de Práctica',
        );

        $spanish_title = isset($spanish_titles[$item->title]) ? $spanish_titles[$item->title] : $item->title;

        $spanish_pages[] = array(
            'title' => $spanish_title,
            'url'   => $spanish_url,
        );
    }
}

// Add Practice Areas link for Spanish
$spanish_pages[] = array(
    'title' => 'Áreas de Práctica',
    'url'   => home_url('/es/practice-areas/'),
);

// Add Free Consultation link for Spanish
$spanish_pages[] = array(
    'title' => 'Consulta Gratuita',
    'url'   => home_url('/es/free-consultation/'),
);

// Add Legal News link for Spanish
$spanish_pages[] = array(
    'title' => 'Noticias Legales',
    'url'   => home_url('/es/latest-legal-news/'),
);
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1><?php echo esc_html(get_the_title()); ?></h1>
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>">Home</a> / <span aria-current="page"><?php echo esc_html(get_the_title()); ?></span>
            </nav>
        </div>
    </div>
</section>

<!-- Main Content -->
<main class="main-content sitemap-page">
    <div class="container">

        <!-- Sitemap Grid -->
        <div class="sitemap-grid">

            <!-- Main Pages (from WordPress Primary Menu) -->
            <div class="sitemap-section">
                <h2>Main Pages</h2>
                <ul class="sitemap-links">
                    <?php if ($main_menu_items && !is_wp_error($main_menu_items)) : ?>
                        <?php foreach ($main_menu_items as $item) : ?>
                            <?php
                            // Skip portal pages - they go in Client Services
                            if (stripos($item->title, 'portal') !== false) continue;
                            ?>
                            <li><a href="<?php echo esc_url($item->url); ?>"><?php echo esc_html($item->title); ?></a></li>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <!-- Fallback if no menu exists -->
                        <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                        <li><a href="<?php echo esc_url(home_url('/about-the-firm/')); ?>">About The Firm</a></li>
                        <li><a href="<?php echo esc_url(home_url('/latest-legal-news/')); ?>">Latest News</a></li>
                        <li><a href="<?php echo esc_url(home_url('/resources/')); ?>">Legal Resources</a></li>
                        <li><a href="<?php echo esc_url(home_url('/contact-us/')); ?>">Contact Us</a></li>
                    <?php endif; ?>
                    <li><a href="#" class="consultation-trigger">Free Consultation</a></li>
                </ul>
            </div>

            <!-- Practice Areas (from Custom Post Type) -->
            <div class="sitemap-section">
                <h2>Practice Areas</h2>
                <ul class="sitemap-links">
                    <?php if ($practice_areas) : ?>
                        <?php foreach ($practice_areas as $area) : ?>
                            <li><a href="<?php echo esc_url(get_permalink($area->ID)); ?>"><?php echo esc_html($area->post_title); ?></a></li>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <li><em>No practice areas found.</em></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- En Español (dynamically generated from main pages) -->
            <div class="sitemap-section">
                <h2>En Español</h2>
                <ul class="sitemap-links">
                    <?php if (!empty($spanish_pages)) : ?>
                        <?php foreach ($spanish_pages as $page) : ?>
                            <li><a href="<?php echo esc_url($page['url']); ?>"><?php echo esc_html($page['title']); ?></a></li>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <!-- Fallback Spanish links -->
                        <li><a href="<?php echo esc_url(home_url('/es/')); ?>">Página Principal</a></li>
                        <li><a href="<?php echo esc_url(home_url('/es/about-the-firm/')); ?>">Sobre Nosotros</a></li>
                        <li><a href="<?php echo esc_url(home_url('/es/practice-areas/')); ?>">Áreas de Práctica</a></li>
                        <li><a href="<?php echo esc_url(home_url('/es/contact-us/')); ?>">Contáctenos</a></li>
                        <li><a href="<?php echo esc_url(home_url('/es/resources/')); ?>">Recursos Legales</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Legal Information (from page queries) -->
            <div class="sitemap-section">
                <h2>Legal Information</h2>
                <ul class="sitemap-links">
                    <?php
                    // Track which pages we've output to avoid duplicates
                    $output_slugs = array();

                    // Privacy Policy - use WordPress setting if available, otherwise check query
                    if ($privacy_policy_url) : ?>
                        <li><a href="<?php echo esc_url($privacy_policy_url); ?>">Privacy Policy</a></li>
                        <?php $output_slugs[] = 'privacy-policy'; ?>
                    <?php else :
                        $privacy_page = get_page_by_path('privacy-policy');
                        if ($privacy_page) : ?>
                            <li><a href="<?php echo esc_url(get_permalink($privacy_page->ID)); ?>"><?php echo esc_html($privacy_page->post_title); ?></a></li>
                            <?php $output_slugs[] = 'privacy-policy'; ?>
                        <?php else : ?>
                            <li><a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">Privacy Policy</a></li>
                            <?php $output_slugs[] = 'privacy-policy'; ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php
                    // Terms of Service
                    $terms_page = get_page_by_path('terms-of-service');
                    if ($terms_page) : ?>
                        <li><a href="<?php echo esc_url(get_permalink($terms_page->ID)); ?>"><?php echo esc_html($terms_page->post_title); ?></a></li>
                    <?php else : ?>
                        <li><a href="<?php echo esc_url(home_url('/terms-of-service/')); ?>">Terms of Service</a></li>
                    <?php endif; ?>

                    <!-- Site Map (current page) -->
                    <li><a href="<?php echo esc_url(get_permalink()); ?>">Site Map</a></li>

                    <?php
                    // Accessibility
                    $accessibility_page = get_page_by_path('accessibility');
                    if ($accessibility_page) : ?>
                        <li><a href="<?php echo esc_url(get_permalink($accessibility_page->ID)); ?>"><?php echo esc_html($accessibility_page->post_title); ?></a></li>
                    <?php else : ?>
                        <li><a href="<?php echo esc_url(home_url('/accessibility/')); ?>">Accessibility</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Client Services (from Portal pages query + contact) -->
            <div class="sitemap-section">
                <h2>Client Services</h2>
                <ul class="sitemap-links">
                    <?php
                    // Check if portal login page exists
                    $portal_login = get_page_by_path('portal-login');
                    if ($portal_login) : ?>
                        <li><a href="<?php echo esc_url(get_permalink($portal_login->ID)); ?>">Client Portal Login</a></li>
                    <?php else : ?>
                        <li><a href="<?php echo esc_url(home_url('/portal-login/')); ?>">Client Portal Login</a></li>
                    <?php endif; ?>

                    <?php
                    // Free Consultation page if exists
                    $consultation_page = get_page_by_path('free-consultation');
                    if ($consultation_page) : ?>
                        <li><a href="<?php echo esc_url(get_permalink($consultation_page->ID)); ?>">Schedule Consultation</a></li>
                    <?php else : ?>
                        <li><a href="#" class="consultation-trigger">Schedule Consultation</a></li>
                    <?php endif; ?>

                    <?php
                    // Contact page
                    $contact_page = get_page_by_path('contact-us');
                    if ($contact_page) : ?>
                        <li><a href="<?php echo esc_url(get_permalink($contact_page->ID)); ?>">Contact Form</a></li>
                    <?php else : ?>
                        <li><a href="<?php echo esc_url(home_url('/contact-us/')); ?>">Contact Form</a></li>
                    <?php endif; ?>

                    <li><a href="tel:<?php echo esc_attr($phone_raw); ?>">Call Us: <?php echo esc_html($phone); ?></a></li>
                </ul>
            </div>

            <!-- Contact Information (from ACF Theme Options) -->
            <div class="sitemap-section">
                <h2>Contact Information</h2>
                <ul class="sitemap-links contact-info-list">
                    <li>
                        <i class="fas fa-phone" aria-hidden="true"></i>
                        <a href="tel:<?php echo esc_attr($phone_raw); ?>"><?php echo esc_html($phone); ?></a>
                    </li>
                    <li>
                        <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                        <span><?php echo esc_html($address_city . ', ' . $address_state); ?></span>
                    </li>
                    <li>
                        <i class="fas fa-clock" aria-hidden="true"></i>
                        <span><?php echo esc_html($office_hours); ?></span>
                    </li>
                    <li>
                        <i class="fas fa-globe" aria-hidden="true"></i>
                        <span><?php echo esc_html($service_area); ?></span>
                    </li>
                </ul>
                <div class="emergency-callout">
                    <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                    <strong>24/7 Emergency Line Available</strong>
                </div>
            </div>

        </div>

        <!-- Quick Action Buttons -->
        <div class="sitemap-cta">
            <a href="tel:<?php echo esc_attr($phone_raw); ?>" class="btn btn-primary">
                <i class="fas fa-phone" aria-hidden="true"></i> Call <?php echo esc_html($phone); ?>
            </a>
            <?php if ($consultation_page) : ?>
                <a href="<?php echo esc_url(get_permalink($consultation_page->ID)); ?>" class="btn btn-outline">
                    <i class="fas fa-calendar-check" aria-hidden="true"></i> Free Consultation
                </a>
            <?php else : ?>
                <button class="btn btn-outline consultation-trigger">
                    <i class="fas fa-calendar-check" aria-hidden="true"></i> Free Consultation
                </button>
            <?php endif; ?>
            <?php if ($contact_page) : ?>
                <a href="<?php echo esc_url(get_permalink($contact_page->ID)); ?>" class="btn btn-outline">
                    <i class="fas fa-envelope" aria-hidden="true"></i> Contact Form
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="btn btn-outline">
                    <i class="fas fa-envelope" aria-hidden="true"></i> Contact Form
                </a>
            <?php endif; ?>
        </div>

        <!-- Legal Notice -->
        <div class="sitemap-legal-notice">
            <p><strong>Important Notice:</strong> The information on this website is for general informational purposes only and does not constitute legal advice. Contacting Defense Lawyers, P.A. through this website does not create an attorney-client relationship. Please do not send any confidential information until an attorney-client relationship has been established.</p>
        </div>

    </div>
</main>

<?php
get_footer();
