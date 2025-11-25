<?php
/**
 * Template Name: Contact Us
 *
 * Custom page template for the Contact Us page
 * Content is managed via ACF fields with section visibility toggles.
 * Matches the live site at mydefenselaw.com/contact_us.php
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

get_header();

// Get ACF fields with defaults
$contact = mydefenselaw_get_contact_page_fields();

// Section visibility toggles (default to showing if field not set)
$show_hero = get_field('enable_contact_hero') !== false;
$show_info = get_field('enable_contact_info') !== false;
$show_why = get_field('enable_contact_why') !== false;
$show_form = get_field('enable_contact_form') !== false;
$show_process = get_field('enable_contact_process') !== false;
?>

<?php if ($show_hero) : ?>
<!-- Contact Hero Section -->
<section class="contact-hero">
    <div class="contact-hero-overlay"></div>
    <div class="container">
        <div class="contact-hero-content">
            <?php if (!empty($contact['hero_badge'])) : ?>
            <div class="contact-hero-badge">
                <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                <span><?php echo esc_html($contact['hero_badge']); ?></span>
            </div>
            <?php endif; ?>
            <h1><?php echo esc_html($contact['hero_title']); ?></h1>
            <p class="hero-subtitle"><?php echo esc_html($contact['hero_subtitle']); ?></p>
            <div class="contact-hero-cta">
                <a href="tel:<?php echo esc_attr($contact['phone_raw']); ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-phone" aria-hidden="true"></i> <?php echo esc_html($contact['phone']); ?>
                </a>
                <span class="hero-availability">
                    <i class="fas fa-clock" aria-hidden="true"></i> <?php echo esc_html($contact['hours_label']); ?>
                </span>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Page Header / Breadcrumb -->
<section class="page-header page-header-slim">
    <div class="container">
        <div class="page-header-content">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>">Home</a> / <span aria-current="page"><?php echo esc_html(get_the_title()); ?></span>
            </nav>
        </div>
    </div>
</section>

<!-- Main Contact Content -->
<main class="contact-page-main">
    <div class="container">
        <div class="contact-page-grid">

            <!-- Left Column: Info + Why Choose -->
            <div class="contact-info-column">

                <?php if ($show_info) : ?>
                <!-- Contact Information -->
                <div class="contact-info-box">
                    <h2><?php echo esc_html($contact['info_title']); ?></h2>

                    <div class="contact-info-details">
                        <div class="contact-info-item">
                            <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                            <div>
                                <strong><?php echo esc_html($contact['location']); ?></strong>
                                <p><?php echo esc_html($contact['serving']); ?></p>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <i class="fas fa-phone" aria-hidden="true"></i>
                            <div>
                                <strong>Call Us</strong>
                                <p><a href="tel:<?php echo esc_attr($contact['phone_raw']); ?>"><?php echo esc_html($contact['phone']); ?></a></p>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <i class="fas fa-clock" aria-hidden="true"></i>
                            <div>
                                <strong><?php echo esc_html($contact['hours_label']); ?></strong>
                                <p><?php echo esc_html($contact['office_hours']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($show_why && !empty($contact['why_features'])) : ?>
                <!-- Why Choose Us -->
                <div class="contact-why-box">
                    <h3><?php echo esc_html($contact['why_title']); ?></h3>

                    <ul class="contact-why-list">
                        <?php foreach ($contact['why_features'] as $feature) : ?>
                        <li>
                            <i class="<?php echo esc_attr($feature['feature_icon']); ?>" aria-hidden="true"></i>
                            <div>
                                <strong><?php echo esc_html($feature['feature_title']); ?></strong>
                                <span><?php echo esc_html($feature['feature_description']); ?></span>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

            </div>

            <!-- Right Column: Contact Form -->
            <?php if ($show_form) : ?>
            <div class="contact-form-column">
                <div class="contact-form-box">
                    <div class="form-header">
                        <h3><?php echo esc_html($contact['form_title']); ?></h3>
                        <p><?php echo esc_html($contact['form_subtitle']); ?></p>
                    </div>

                    <form id="contactPageForm" class="contact-page-form" method="post">
                        <input type="hidden" name="action" value="mydefenselaw_contact_form">
                        <?php wp_nonce_field('mydefenselaw_contact', 'contact_nonce'); ?>

                        <!-- Honeypot field for spam protection -->
                        <div class="hp-field" aria-hidden="true">
                            <label for="website_url">Website</label>
                            <input type="text" name="website_url" id="website_url" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="form-row">
                            <div class="form-field">
                                <label for="firstName">First Name <span class="required">*</span></label>
                                <input type="text" id="firstName" name="firstName" required placeholder="Your first name">
                            </div>
                            <div class="form-field">
                                <label for="lastName">Last Name <span class="required">*</span></label>
                                <input type="text" id="lastName" name="lastName" required placeholder="Your last name">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-field">
                                <label for="phone">Phone <span class="required">*</span></label>
                                <input type="tel" id="phone" name="phone" required placeholder="(555) 555-5555">
                            </div>
                            <div class="form-field">
                                <label for="email">Email <span class="required">*</span></label>
                                <input type="email" id="email" name="email" required placeholder="your@email.com">
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="legalIssue">Legal Issue Type <span class="required">*</span></label>
                            <select id="legalIssue" name="legalIssue" required>
                                <option value="">Select your legal issue...</option>
                                <option value="Civil Defense Litigation">Civil Defense Litigation</option>
                                <option value="Consumer Protection">Consumer Protection</option>
                                <option value="Family Law">Family Law</option>
                                <option value="Bankruptcy">Bankruptcy</option>
                                <option value="Contract Law">Contract Law</option>
                                <option value="Real Estate Law">Real Estate Law</option>
                                <option value="Landlord/Tenant">Landlord/Tenant</option>
                                <option value="Intellectual Property">Intellectual Property</option>
                                <option value="Traffic Tickets">Traffic Tickets</option>
                                <option value="Other">Other Legal Matter</option>
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="urgency">How Soon Do You Need Help?</label>
                            <select id="urgency" name="urgency">
                                <option value="Immediate">Immediately (within 24 hours)</option>
                                <option value="Urgent">Urgent (within 1 week)</option>
                                <option value="Normal" selected>Normal (within 2 weeks)</option>
                                <option value="Planning">Planning Ahead (no rush)</option>
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="message">Brief Description of Your Case</label>
                            <textarea id="message" name="message" rows="4" placeholder="Please provide a brief description of your legal matter..."></textarea>
                        </div>

                        <div class="form-field checkbox-field">
                            <label class="checkbox-label">
                                <input type="checkbox" name="urgent" id="urgentCheckbox">
                                <span class="checkmark"></span>
                                <span class="label-text"><i class="fas fa-exclamation-triangle" aria-hidden="true"></i> This is urgent - I need immediate assistance</span>
                            </label>
                        </div>

                        <div class="form-field checkbox-field">
                            <label class="checkbox-label">
                                <input type="checkbox" name="agreement" id="agreement" required>
                                <span class="checkmark"></span>
                                <span class="label-text">I understand that submitting this form does not create an attorney-client relationship. <span class="required">*</span></span>
                            </label>
                        </div>

                        <div class="form-response" id="formResponse"></div>

                        <button type="submit" class="btn btn-primary btn-full btn-lg">
                            <i class="fas fa-paper-plane" aria-hidden="true"></i> <?php echo esc_html($contact['form_button']); ?>
                        </button>

                        <p class="form-disclaimer">
                            <i class="fas fa-lock" aria-hidden="true"></i> <?php echo esc_html($contact['form_disclaimer']); ?>
                        </p>
                    </form>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php if ($show_process && !empty($contact['process_steps'])) : ?>
<!-- What to Expect Section -->
<section class="contact-process-section">
    <div class="container">
        <h2><?php echo esc_html($contact['process_title']); ?></h2>

        <div class="process-steps">
            <?php $step_num = 1; ?>
            <?php foreach ($contact['process_steps'] as $step) : ?>
            <div class="process-step">
                <div class="step-number"><?php echo $step_num; ?></div>
                <div class="step-icon">
                    <i class="<?php echo esc_attr($step['step_icon']); ?>" aria-hidden="true"></i>
                </div>
                <h3><?php echo esc_html($step['step_title']); ?></h3>
                <p><?php echo esc_html($step['step_description']); ?></p>
            </div>
            <?php $step_num++; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="contact-cta-section">
    <div class="container">
        <div class="contact-cta-content">
            <h2>Ready to Get Started?</h2>
            <p>Contact us today for your free consultation and take the first step toward resolving your legal matter.</p>
            <div class="cta-buttons">
                <a href="tel:<?php echo esc_attr($contact['phone_raw']); ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-phone" aria-hidden="true"></i> Call <?php echo esc_html($contact['phone']); ?>
                </a>
                <button class="btn btn-secondary btn-lg consultation-trigger" aria-label="Request a free legal consultation">
                    <i class="fas fa-calendar-check" aria-hidden="true"></i> Schedule Consultation
                </button>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactPageForm');
    const responseDiv = document.getElementById('formResponse');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            submitBtn.disabled = true;

            const formData = new FormData(form);

            fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                responseDiv.innerHTML = data.data.message;
                responseDiv.className = 'form-response ' + (data.success ? 'success' : 'error');

                if (data.success) {
                    form.reset();
                }

                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;

                responseDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            })
            .catch(error => {
                responseDiv.innerHTML = 'An error occurred. Please try again or call us directly.';
                responseDiv.className = 'form-response error';
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});
</script>

<?php
get_footer();
