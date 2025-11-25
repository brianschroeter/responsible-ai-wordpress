<?php
/**
 * Template Name: Contact Us
 *
 * Custom page template for the Contact Us page
 * Pixel-perfect match to live site at mydefenselaw.com/contact_us.php
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

get_header();

// Get ACF fields with defaults
$contact = mydefenselaw_get_contact_page_fields();
?>

<!-- Page Header - Minimal like live site -->
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

<!-- Main Content - White container like live site -->
<main class="main-content">
    <div class="container">
        <div class="section-header" style="text-align: center; margin-bottom: 3rem;">
            <h2><?php echo esc_html($contact['hero_title']); ?></h2>
            <p><?php echo esc_html($contact['hero_subtitle']); ?></p>
        </div>

        <div class="contact-grid">
            <div class="contact-info">
                <h3><?php echo esc_html($contact['info_title']); ?></h3>
                <div class="contact-details">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Office Location</strong>
                            <p><?php echo esc_html($contact['location']); ?><br><?php echo esc_html($contact['serving']); ?></p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <strong>Phone</strong>
                            <p><a href="tel:<?php echo esc_attr($contact['phone_raw']); ?>"><?php echo esc_html($contact['phone']); ?></a></p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Availability</strong>
                            <p><?php echo esc_html($contact['hours_label']); ?><br><?php echo esc_html($contact['office_hours']); ?></p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Get Started</strong>
                            <p><a href="#contactForm">Request Free Consultation</a></p>
                        </div>
                    </div>
                </div>

                <h3 style="margin-top: 3rem;"><?php echo esc_html($contact['why_title']); ?></h3>
                <ul style="margin-left: 0; list-style: none; padding: 0;">
                    <?php foreach ($contact['why_features'] as $feature) : ?>
                    <li style="margin-bottom: 1rem; display: flex; align-items: flex-start; gap: 1rem;">
                        <i class="fas fa-check-circle" style="color: #dc2626; margin-top: 0.25rem;"></i>
                        <span><strong><?php echo esc_html($feature['feature_title']); ?></strong> - <?php echo esc_html($feature['feature_description']); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="contact-form-section">
                <div class="form-header">
                    <h4><?php echo esc_html($contact['form_title']); ?></h4>
                    <p><?php echo esc_html($contact['form_subtitle']); ?></p>
                </div>
                <form class="contact-form" id="contactForm" method="post">
                    <input type="hidden" name="action" value="mydefenselaw_contact_form">
                    <?php wp_nonce_field('mydefenselaw_contact', 'contact_nonce'); ?>

                    <!-- Honeypot field for spam protection -->
                    <div style="position: absolute; left: -9999px;" aria-hidden="true">
                        <input type="text" name="website_url" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="form-row">
                        <input type="text" name="firstName" placeholder="First Name *" required>
                        <input type="text" name="lastName" placeholder="Last Name *" required>
                    </div>
                    <input type="tel" name="phone" placeholder="Phone Number *" required>
                    <input type="email" name="email" placeholder="Email Address *" required>
                    <select name="legalIssue" required>
                        <option value="">What type of legal issue? *</option>
                        <option value="civil-litigation">Civil Defense Litigation</option>
                        <option value="consumer-protection">Consumer Protection</option>
                        <option value="family-law">Family Law</option>
                        <option value="bankruptcy">Bankruptcy</option>
                        <option value="contract-dispute">Contract Law</option>
                        <option value="real-estate">Real Estate Law</option>
                        <option value="landlord-tenant">Landlord/Tenant Issues</option>
                        <option value="intellectual-property">Intellectual Property</option>
                        <option value="traffic-tickets">Traffic Tickets</option>
                        <option value="other">Other</option>
                    </select>
                    <select name="urgency">
                        <option value="">How urgent is your matter?</option>
                        <option value="immediate">Immediate (within 24 hours)</option>
                        <option value="urgent">Urgent (within 1 week)</option>
                        <option value="normal">Normal (within 2 weeks)</option>
                        <option value="planning">Planning ahead</option>
                    </select>
                    <textarea name="message" placeholder="Briefly describe your situation..." rows="4"></textarea>
                    <label class="checkbox-label">
                        <input type="checkbox" name="urgent">
                        <span class="checkmark"></span>
                        This is urgent - I need immediate assistance
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="agreement" required>
                        <span class="checkmark"></span>
                        I understand that Defense Lawyers, P.A. has not yet agreed to represent me, and that submitting this form does not constitute a contract.
                    </label>
                    <div class="form-response" id="formResponse"></div>
                    <button type="submit" class="btn btn-primary btn-full">
                        <i class="fas fa-paper-plane"></i>
                        <?php echo esc_html($contact['form_button']); ?>
                    </button>
                </form>
            </div>
        </div>

        <div style="margin-top: 4rem; padding: 2rem; background: #f8fafc; border-radius: 8px;">
            <h3><?php echo esc_html($contact['process_title']); ?></h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-top: 2rem;">
                <?php $step_num = 1; ?>
                <?php foreach ($contact['process_steps'] as $step) : ?>
                <div>
                    <h4><i class="<?php echo esc_attr($step['step_icon']); ?>" style="color: #dc2626; margin-right: 0.5rem;"></i>Step <?php echo $step_num; ?>: <?php echo esc_html($step['step_title']); ?></h4>
                    <p><?php echo esc_html($step['step_description']); ?></p>
                </div>
                <?php $step_num++; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
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
