<?php
/**
 * Template Name: Contact
 *
 * Contact page template with contact form and information
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get contact info from ACF or theme options
$org_info = responsibleai_get_organization_info();
$social = responsibleai_get_social_links();

// Get page-specific contact fields (if available via ACF)
$contact_fields = array(
    'enable_map'        => function_exists('get_field') ? get_field('enable_map') : true,
    'enable_hours'      => function_exists('get_field') ? get_field('enable_hours') : true,
    'office_hours'      => function_exists('get_field') ? get_field('office_hours') : array(),
    'map_embed'         => function_exists('get_field') ? get_field('map_embed') : '',
    'additional_email'  => function_exists('get_field') ? get_field('contact_email') : '',
    'additional_phone'  => function_exists('get_field') ? get_field('contact_phone') : '',
    'additional_address'=> function_exists('get_field') ? get_field('contact_address') : '',
);

// Use page-specific fields if available, otherwise fall back to theme options
$contact_email = !empty($contact_fields['additional_email']) ? $contact_fields['additional_email'] : $org_info['email'];
$contact_phone = !empty($contact_fields['additional_phone']) ? $contact_fields['additional_phone'] : $org_info['phone'];
$contact_address = !empty($contact_fields['additional_address']) ? $contact_fields['additional_address'] : $org_info['address'];

get_header();
?>

<!-- Contact Hero Section -->
<section class="page-hero">
    <div class="container">
        <div class="page-hero__content">
            <h1 class="page-hero__title"><?php esc_html_e('Contact Us', 'responsible-ai'); ?></h1>
            <p class="page-hero__subtitle">
                <?php esc_html_e('Get in touch with our team to learn more about our certification programs, resources, and how we can help you build responsible AI systems.', 'responsible-ai'); ?>
            </p>
        </div>
    </div>
</section>

<!-- Contact Content Section -->
<section class="contact-section section-padding">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Form Column -->
            <div class="contact-grid__form">
                <div class="form-card">
                    <h2 class="form-card__title"><?php esc_html_e('Send us a message', 'responsible-ai'); ?></h2>
                    <p class="form-card__description">
                        <?php esc_html_e('Fill out the form below and we\'ll get back to you as soon as possible.', 'responsible-ai'); ?>
                    </p>

                    <form class="contact-form" id="contact-form" method="post">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="contact-name" class="form-label">
                                    <?php esc_html_e('Full Name', 'responsible-ai'); ?> <span class="required">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="contact-name"
                                    name="name"
                                    class="form-control"
                                    required
                                    placeholder="<?php esc_attr_e('John Doe', 'responsible-ai'); ?>"
                                >
                            </div>

                            <div class="form-group">
                                <label for="contact-email" class="form-label">
                                    <?php esc_html_e('Email Address', 'responsible-ai'); ?> <span class="required">*</span>
                                </label>
                                <input
                                    type="email"
                                    id="contact-email"
                                    name="email"
                                    class="form-control"
                                    required
                                    placeholder="<?php esc_attr_e('john@example.com', 'responsible-ai'); ?>"
                                >
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="contact-organization" class="form-label">
                                    <?php esc_html_e('Organization', 'responsible-ai'); ?>
                                </label>
                                <input
                                    type="text"
                                    id="contact-organization"
                                    name="organization"
                                    class="form-control"
                                    placeholder="<?php esc_attr_e('Company Name', 'responsible-ai'); ?>"
                                >
                            </div>

                            <div class="form-group">
                                <label for="contact-subject" class="form-label">
                                    <?php esc_html_e('Subject', 'responsible-ai'); ?> <span class="required">*</span>
                                </label>
                                <select
                                    id="contact-subject"
                                    name="subject"
                                    class="form-control"
                                    required
                                >
                                    <option value=""><?php esc_html_e('Select a subject...', 'responsible-ai'); ?></option>
                                    <option value="certification"><?php esc_html_e('Certification Inquiry', 'responsible-ai'); ?></option>
                                    <option value="membership"><?php esc_html_e('Membership', 'responsible-ai'); ?></option>
                                    <option value="partnership"><?php esc_html_e('Partnership Opportunities', 'responsible-ai'); ?></option>
                                    <option value="training"><?php esc_html_e('Training & Resources', 'responsible-ai'); ?></option>
                                    <option value="media"><?php esc_html_e('Media Inquiry', 'responsible-ai'); ?></option>
                                    <option value="support"><?php esc_html_e('Technical Support', 'responsible-ai'); ?></option>
                                    <option value="general"><?php esc_html_e('General Inquiry', 'responsible-ai'); ?></option>
                                    <option value="other"><?php esc_html_e('Other', 'responsible-ai'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="contact-message" class="form-label">
                                <?php esc_html_e('Message', 'responsible-ai'); ?> <span class="required">*</span>
                            </label>
                            <textarea
                                id="contact-message"
                                name="message"
                                class="form-control"
                                rows="6"
                                required
                                placeholder="<?php esc_attr_e('Tell us about your inquiry...', 'responsible-ai'); ?>"
                            ></textarea>
                        </div>

                        <!-- Honeypot field (hidden from users, catches bots) -->
                        <div class="form-group" style="position: absolute; left: -9999px; opacity: 0;">
                            <label for="contact-website"><?php esc_html_e('Website', 'responsible-ai'); ?></label>
                            <input
                                type="text"
                                id="contact-website"
                                name="website_url"
                                tabindex="-1"
                                autocomplete="off"
                            >
                        </div>

                        <!-- WordPress nonce for security -->
                        <?php wp_nonce_field('responsibleai_contact', 'contact_nonce'); ?>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <?php esc_html_e('Send Message', 'responsible-ai'); ?>
                            </button>
                        </div>

                        <!-- Form messages -->
                        <div class="form-message" id="contact-form-message"></div>
                    </form>
                </div>
            </div>

            <!-- Contact Information Column -->
            <div class="contact-grid__info">
                <div class="contact-info-card">
                    <h3 class="contact-info-card__title"><?php esc_html_e('Get in Touch', 'responsible-ai'); ?></h3>
                    <p class="contact-info-card__text">
                        <?php esc_html_e('Reach out to us directly through any of the following channels:', 'responsible-ai'); ?>
                    </p>

                    <!-- Contact Details -->
                    <div class="contact-details">
                        <?php if ($contact_email) : ?>
                        <div class="contact-detail">
                            <div class="contact-detail__icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-detail__content">
                                <h4 class="contact-detail__label"><?php esc_html_e('Email', 'responsible-ai'); ?></h4>
                                <a href="mailto:<?php echo esc_attr($contact_email); ?>" class="contact-detail__value">
                                    <?php echo esc_html($contact_email); ?>
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($contact_phone) : ?>
                        <div class="contact-detail">
                            <div class="contact-detail__icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-detail__content">
                                <h4 class="contact-detail__label"><?php esc_html_e('Phone', 'responsible-ai'); ?></h4>
                                <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $contact_phone)); ?>" class="contact-detail__value">
                                    <?php echo esc_html($contact_phone); ?>
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($contact_address) : ?>
                        <div class="contact-detail">
                            <div class="contact-detail__icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-detail__content">
                                <h4 class="contact-detail__label"><?php esc_html_e('Address', 'responsible-ai'); ?></h4>
                                <p class="contact-detail__value">
                                    <?php echo nl2br(esc_html($contact_address)); ?>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Social Links -->
                    <?php if (array_filter($social)) : ?>
                    <div class="contact-social">
                        <h4 class="contact-social__title"><?php esc_html_e('Follow Us', 'responsible-ai'); ?></h4>
                        <div class="contact-social__links">
                            <?php if ($social['linkedin']) : ?>
                                <a href="<?php echo esc_url($social['linkedin']); ?>" target="_blank" rel="noopener" class="contact-social__link" aria-label="<?php esc_attr_e('LinkedIn', 'responsible-ai'); ?>">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            <?php endif; ?>
                            <?php if ($social['twitter']) : ?>
                                <a href="<?php echo esc_url($social['twitter']); ?>" target="_blank" rel="noopener" class="contact-social__link" aria-label="<?php esc_attr_e('Twitter', 'responsible-ai'); ?>">
                                    <i class="fab fa-x-twitter"></i>
                                </a>
                            <?php endif; ?>
                            <?php if ($social['youtube']) : ?>
                                <a href="<?php echo esc_url($social['youtube']); ?>" target="_blank" rel="noopener" class="contact-social__link" aria-label="<?php esc_attr_e('YouTube', 'responsible-ai'); ?>">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            <?php endif; ?>
                            <?php if ($social['github']) : ?>
                                <a href="<?php echo esc_url($social['github']); ?>" target="_blank" rel="noopener" class="contact-social__link" aria-label="<?php esc_attr_e('GitHub', 'responsible-ai'); ?>">
                                    <i class="fab fa-github"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Office Hours (if enabled and available) -->
                <?php if ($contact_fields['enable_hours'] && !empty($contact_fields['office_hours'])) : ?>
                <div class="contact-info-card contact-hours-card">
                    <h3 class="contact-info-card__title"><?php esc_html_e('Office Hours', 'responsible-ai'); ?></h3>
                    <ul class="contact-hours-list">
                        <?php foreach ($contact_fields['office_hours'] as $hours) : ?>
                            <?php if (!empty($hours['day']) && !empty($hours['time'])) : ?>
                            <li class="contact-hours-list__item">
                                <span class="contact-hours-list__day"><?php echo esc_html($hours['day']); ?></span>
                                <span class="contact-hours-list__time"><?php echo esc_html($hours['time']); ?></span>
                            </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Map Section (if enabled) -->
<?php if ($contact_fields['enable_map']) : ?>
<section class="contact-map-section">
    <div class="contact-map">
        <?php if (!empty($contact_fields['map_embed'])) : ?>
            <!-- Custom map embed from ACF -->
            <div class="contact-map__embed">
                <?php echo wp_kses_post($contact_fields['map_embed']); ?>
            </div>
        <?php else : ?>
            <!-- Map placeholder or default map -->
            <div class="contact-map__placeholder">
                <div class="contact-map__placeholder-content">
                    <i class="fas fa-map-marked-alt"></i>
                    <p><?php esc_html_e('Map location coming soon', 'responsible-ai'); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
