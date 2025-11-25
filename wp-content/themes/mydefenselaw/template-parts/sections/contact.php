<?php
/**
 * Contact Section Template Part
 * Pixel-perfect match of source site with custom AJAX form
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

$contact = mydefenselaw_get_contact_section_fields();
?>

<section class="contact-section" id="contact">
    <div class="container">
        <div class="section-header">
            <h2><?php echo esc_html($contact['title']); ?></h2>
            <p><?php echo esc_html($contact['subtitle']); ?></p>
        </div>

        <div class="contact-grid">
            <div class="contact-info">
                <h3><?php echo esc_html($contact['company_name']); ?></h3>
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
                            <p><?php echo esc_html($contact['hours']); ?> Emergency Line<br>Office Hours: <?php echo esc_html($contact['office_hours']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact-form-section">
                <div class="form-header">
                    <h4><?php echo esc_html($contact['form_heading']); ?></h4>
                    <p><?php echo esc_html($contact['form_subheading']); ?></p>
                </div>
                <form class="contact-form" id="contactForm" method="post">
                    <?php wp_nonce_field('mydefenselaw_contact', 'contact_nonce'); ?>
                    <input type="hidden" name="action" value="mydefenselaw_contact_form">

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
                    <button type="submit" class="btn btn-primary btn-full">
                        <i class="fas fa-paper-plane"></i>
                        <?php echo esc_html($contact['button_text']); ?>
                    </button>
                </form>
                <div id="formResponse" class="form-response" style="display: none;"></div>
            </div>
        </div>
    </div>
</section>
