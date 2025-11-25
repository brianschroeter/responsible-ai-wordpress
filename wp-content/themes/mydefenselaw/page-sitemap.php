<?php
/**
 * Template Name: Site Map
 *
 * Custom page template for the Site Map page
 * Displays all site pages, practice areas, and resources in an organized grid layout
 * Matches the live site at mydefenselaw.com/site_map.php
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

get_header();

// Get phone from theme options
$phone = get_field('contact_phone', 'option') ?: '(954) 523-9200';
$phone_raw = preg_replace('/[^0-9]/', '', $phone);

// Get address from theme options
$address_city = get_field('contact_city', 'option') ?: 'Boca Raton';
$address_state = get_field('contact_state', 'option') ?: 'Florida';
$office_hours = get_field('office_hours', 'option') ?: 'Mon - Fri: 9:00 AM - 5:00 PM';
$service_area = get_field('service_area', 'option') ?: 'Serving All of South Florida';

// Get practice areas dynamically
$practice_areas = get_posts(array(
    'post_type'      => 'practice_area',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'title',
    'order'          => 'ASC',
));

// Get WordPress privacy policy page
$privacy_policy_page = get_privacy_policy_url();
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

            <!-- Main Pages -->
            <div class="sitemap-section">
                <h2>Main Pages</h2>
                <ul class="sitemap-links">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                    <li><a href="<?php echo esc_url(mydefenselaw_get_page_url_by_slug('about-the-firm')); ?>">About The Firm</a></li>
                    <li><a href="<?php echo esc_url(mydefenselaw_get_page_url_by_slug('latest-legal-news')); ?>">Latest News</a></li>
                    <li><a href="<?php echo esc_url(mydefenselaw_get_page_url_by_slug('resources')); ?>">Legal Resources</a></li>
                    <li><a href="<?php echo esc_url(mydefenselaw_get_page_url_by_slug('contact-us')); ?>">Contact Us</a></li>
                    <li><a href="#" class="consultation-trigger">Free Consultation</a></li>
                </ul>
            </div>

            <!-- Practice Areas -->
            <div class="sitemap-section">
                <h2>Practice Areas</h2>
                <ul class="sitemap-links">
                    <?php if ($practice_areas) : ?>
                        <?php foreach ($practice_areas as $area) : ?>
                            <li><a href="<?php echo esc_url(get_permalink($area->ID)); ?>"><?php echo esc_html($area->post_title); ?></a></li>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <li><a href="<?php echo esc_url(home_url('/practice-area/civil-defense-litigation/')); ?>">Civil Defense Litigation</a></li>
                        <li><a href="<?php echo esc_url(home_url('/practice-area/consumer-protection/')); ?>">Consumer Protection</a></li>
                        <li><a href="<?php echo esc_url(home_url('/practice-area/bankruptcy-law/')); ?>">Bankruptcy Law</a></li>
                        <li><a href="<?php echo esc_url(home_url('/practice-area/contract-law/')); ?>">Contract Law</a></li>
                        <li><a href="<?php echo esc_url(home_url('/practice-area/family-law/')); ?>">Family Law</a></li>
                        <li><a href="<?php echo esc_url(home_url('/practice-area/real-estate-law/')); ?>">Real Estate Law</a></li>
                        <li><a href="<?php echo esc_url(home_url('/practice-area/landlord-tenant-issues/')); ?>">Landlord / Tenant Issues</a></li>
                        <li><a href="<?php echo esc_url(home_url('/practice-area/intellectual-property/')); ?>">Intellectual Property</a></li>
                        <li><a href="<?php echo esc_url(home_url('/practice-area/traffic-tickets/')); ?>">Traffic Tickets</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- En Espanol -->
            <div class="sitemap-section">
                <h2>En Espa&ntilde;ol</h2>
                <ul class="sitemap-links">
                    <li><a href="<?php echo esc_url(home_url('/es/')); ?>">P&aacute;gina Principal</a></li>
                    <li><a href="<?php echo esc_url(home_url('/es/sobre-nosotros/')); ?>">Sobre Nosotros</a></li>
                    <li><a href="<?php echo esc_url(home_url('/es/areas-de-practica/')); ?>">&Aacute;reas de Pr&aacute;ctica</a></li>
                    <li><a href="<?php echo esc_url(home_url('/es/contacto/')); ?>">Cont&aacute;ctenos</a></li>
                    <li><a href="<?php echo esc_url(home_url('/es/recursos/')); ?>">Recursos Legales</a></li>
                </ul>
            </div>

            <!-- Legal Information -->
            <div class="sitemap-section">
                <h2>Legal Information</h2>
                <ul class="sitemap-links">
                    <?php if ($privacy_policy_page) : ?>
                        <li><a href="<?php echo esc_url($privacy_policy_page); ?>">Privacy Policy</a></li>
                    <?php else : ?>
                        <li><a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">Privacy Policy</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo esc_url(home_url('/terms-of-service/')); ?>">Terms of Service</a></li>
                    <li><a href="<?php echo esc_url(get_permalink()); ?>">Site Map</a></li>
                    <li><a href="<?php echo esc_url(home_url('/accessibility/')); ?>">Accessibility</a></li>
                </ul>
            </div>

            <!-- Client Services -->
            <div class="sitemap-section">
                <h2>Client Services</h2>
                <ul class="sitemap-links">
                    <li><a href="<?php echo esc_url(mydefenselaw_get_page_url_by_slug('portal-login')); ?>">Client Portal Login</a></li>
                    <li><a href="#" class="consultation-trigger">Schedule Consultation</a></li>
                    <li><a href="<?php echo esc_url(mydefenselaw_get_page_url_by_slug('contact-us')); ?>">Contact Form</a></li>
                    <li><a href="tel:<?php echo esc_attr($phone_raw); ?>">Call Us: <?php echo esc_html($phone); ?></a></li>
                </ul>
            </div>

            <!-- Contact Information -->
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
            <button class="btn btn-outline consultation-trigger">
                <i class="fas fa-calendar-check" aria-hidden="true"></i> Free Consultation
            </button>
            <a href="<?php echo esc_url(mydefenselaw_get_page_url_by_slug('contact-us')); ?>" class="btn btn-outline">
                <i class="fas fa-envelope" aria-hidden="true"></i> Contact Form
            </a>
        </div>

        <!-- Legal Notice -->
        <div class="sitemap-legal-notice">
            <p><strong>Important Notice:</strong> The information on this website is for general informational purposes only and does not constitute legal advice. Contacting Defense Lawyers, P.A. through this website does not create an attorney-client relationship. Please do not send any confidential information until an attorney-client relationship has been established.</p>
        </div>

    </div>
</main>

<?php
get_footer();
