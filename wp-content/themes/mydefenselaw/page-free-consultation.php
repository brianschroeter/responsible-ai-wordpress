<?php
/**
 * Template Name: Free Legal Consultation
 *
 * Custom page template for the Free Legal Consultation page
 * Pixel-perfect match to live site at mydefenselaw.com/free_legal_consultation.php
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

get_header();

// Get ACF fields with defaults
$consultation = mydefenselaw_get_free_consultation_fields();

// Section visibility toggles (default to showing if field not set)
$show_form = get_field('enable_consultation_form') !== false;
$show_what_to_expect = get_field('enable_consultation_expect') !== false;
$show_why_choose = get_field('enable_consultation_why') !== false;
$show_practice_areas = get_field('enable_consultation_practice_areas') !== false;
?>

<!-- Page Header with Emergency Badge -->
<section class="page-header consultation-page-header">
    <div class="container">
        <div class="page-header-content">
            <div class="emergency-badge">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo esc_html($consultation['emergency_text']); ?>
            </div>
            <h1><?php echo esc_html(get_the_title()); ?></h1>
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>">Home</a> / <span aria-current="page"><?php echo esc_html(get_the_title()); ?></span>
            </nav>
        </div>
    </div>
</section>

<!-- Main Content -->
<main class="main-content consultation-page">
    <div class="container">

        <?php if ($show_form) : ?>
        <!-- Consultation Form Section -->
        <div class="consultation-grid">
            <div class="consultation-info">
                <h2><?php echo esc_html($consultation['intro_title']); ?></h2>
                <p class="lead-text"><?php echo esc_html($consultation['intro_text']); ?></p>

                <?php if ($show_what_to_expect && !empty($consultation['expect_items'])) : ?>
                <!-- What to Expect Section -->
                <div class="expect-section">
                    <h3><i class="fas fa-clipboard-list"></i> <?php echo esc_html($consultation['expect_title']); ?></h3>
                    <ul class="expect-list">
                        <?php foreach ($consultation['expect_items'] as $item) : ?>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo esc_html($item['item_text']); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if ($show_why_choose && !empty($consultation['why_features'])) : ?>
                <!-- Why Choose Our Free Consultation -->
                <div class="why-choose-section">
                    <h3><i class="fas fa-star"></i> <?php echo esc_html($consultation['why_title']); ?></h3>
                    <div class="why-features">
                        <?php foreach ($consultation['why_features'] as $feature) : ?>
                        <div class="why-feature">
                            <i class="<?php echo esc_attr($feature['feature_icon']); ?>"></i>
                            <div>
                                <strong><?php echo esc_html($feature['feature_title']); ?></strong>
                                <p><?php echo esc_html($feature['feature_description']); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="consultation-form-section">
                <div class="form-header">
                    <h4><?php echo esc_html($consultation['form_title']); ?></h4>
                    <p><?php echo esc_html($consultation['form_subtitle']); ?></p>
                </div>
                <form class="contact-form consultation-form" id="consultationForm" method="post">
                    <input type="hidden" name="action" value="mydefenselaw_contact_form">
                    <input type="hidden" name="form_source" value="free_consultation_page">
                    <?php wp_nonce_field('mydefenselaw_contact', 'contact_nonce'); ?>

                    <!-- Honeypot field for spam protection -->
                    <div style="position: absolute; left: -9999px;" aria-hidden="true">
                        <input type="text" name="website_url" tabindex="-1" autocomplete="off">
                    </div>

                    <input type="text" name="fullName" placeholder="Full Name *" required>
                    <input type="email" name="email" placeholder="Email Address *" required>
                    <input type="tel" name="phone" placeholder="Phone Number *" required>

                    <select name="legalIssue" required>
                        <option value="">Type of Legal Issue *</option>
                        <option value="civil-litigation">Civil Defense Litigation</option>
                        <option value="consumer-protection">Consumer Protection</option>
                        <option value="bankruptcy">Bankruptcy Law</option>
                        <option value="contract-law">Contract Law</option>
                        <option value="family-law">Family Law</option>
                        <option value="real-estate">Real Estate Law</option>
                        <option value="landlord-tenant">Landlord/Tenant Issues</option>
                        <option value="intellectual-property">Intellectual Property</option>
                        <option value="traffic-tickets">Traffic Tickets</option>
                        <option value="other">Other</option>
                    </select>

                    <select name="urgency">
                        <option value="">Urgency Level</option>
                        <option value="immediate">Immediate (within 24 hours)</option>
                        <option value="urgent">Urgent (within 1 week)</option>
                        <option value="normal">Normal (within 2 weeks)</option>
                        <option value="planning">Planning Ahead</option>
                    </select>

                    <textarea name="message" placeholder="Describe Your Legal Issue *" rows="5" required></textarea>

                    <label class="checkbox-label disclaimer-checkbox">
                        <input type="checkbox" name="agreement" required>
                        <span class="checkmark"></span>
                        <span class="checkbox-text"><?php echo esc_html($consultation['form_disclaimer']); ?></span>
                    </label>

                    <div class="form-response" id="formResponse"></div>

                    <div class="form-buttons">
                        <button type="submit" class="btn btn-primary btn-full">
                            <i class="fas fa-paper-plane"></i>
                            <?php echo esc_html($consultation['form_button']); ?>
                        </button>
                        <a href="tel:<?php echo esc_attr($consultation['phone_raw']); ?>" class="btn btn-outline btn-full">
                            <i class="fas fa-phone"></i>
                            Call Now: <?php echo esc_html($consultation['phone']); ?>
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($show_practice_areas && !empty($consultation['practice_areas'])) : ?>
        <!-- Practice Areas Section -->
        <div class="consultation-practice-areas">
            <h3><?php echo esc_html($consultation['practice_areas_title']); ?></h3>
            <div class="practice-areas-grid">
                <?php foreach ($consultation['practice_areas'] as $area) : ?>
                <a href="<?php echo esc_url($area['area_link']); ?>" class="practice-area-card-mini">
                    <i class="<?php echo esc_attr($area['area_icon']); ?>"></i>
                    <span><?php echo esc_html($area['area_title']); ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Contact Sidebar -->
        <div class="quick-contact-bar">
            <div class="quick-contact-item">
                <i class="fas fa-calendar-check"></i>
                <span>Free Consultation</span>
            </div>
            <div class="quick-contact-item">
                <i class="fas fa-user-lock"></i>
                <span>Client Login</span>
            </div>
            <div class="quick-contact-item">
                <i class="fas fa-newspaper"></i>
                <span>Latest News</span>
            </div>
        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('consultationForm');
    const responseDiv = document.getElementById('formResponse');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
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
