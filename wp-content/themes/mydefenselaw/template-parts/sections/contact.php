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
                            <strong><?php esc_html_e('Office Location', 'mydefenselaw'); ?></strong>
                            <p><?php echo esc_html($contact['location']); ?><br><?php echo esc_html($contact['serving']); ?></p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <strong><?php esc_html_e('Phone', 'mydefenselaw'); ?></strong>
                            <p><a href="tel:<?php echo esc_attr($contact['phone_raw']); ?>"><?php echo esc_html($contact['phone']); ?></a></p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong><?php esc_html_e('Availability', 'mydefenselaw'); ?></strong>
                            <p><?php echo esc_html($contact['hours']); ?> <?php esc_html_e('Emergency Line', 'mydefenselaw'); ?><br><?php esc_html_e('Office Hours:', 'mydefenselaw'); ?> <?php echo esc_html($contact['office_hours']); ?></p>
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
                    <input type="hidden" name="form_source" value="homepage">

                    <div class="form-row">
                        <input type="text" name="firstName" placeholder="<?php echo esc_attr__('First Name *', 'mydefenselaw'); ?>" required>
                        <input type="text" name="lastName" placeholder="<?php echo esc_attr__('Last Name *', 'mydefenselaw'); ?>" required>
                    </div>
                    <input type="tel" name="phone" placeholder="<?php echo esc_attr__('Phone Number *', 'mydefenselaw'); ?>" required>
                    <input type="email" name="email" placeholder="<?php echo esc_attr__('Email Address *', 'mydefenselaw'); ?>" required>
                    <select name="legalIssue" required>
                        <option value=""><?php echo esc_html__('What type of legal issue? *', 'mydefenselaw'); ?></option>
                        <option value="civil-litigation"><?php echo esc_html__('Civil Defense Litigation', 'mydefenselaw'); ?></option>
                        <option value="consumer-protection"><?php echo esc_html__('Consumer Protection', 'mydefenselaw'); ?></option>
                        <option value="family-law"><?php echo esc_html__('Family Law', 'mydefenselaw'); ?></option>
                        <option value="bankruptcy"><?php echo esc_html__('Bankruptcy', 'mydefenselaw'); ?></option>
                        <option value="contract-dispute"><?php echo esc_html__('Contract Law', 'mydefenselaw'); ?></option>
                        <option value="real-estate"><?php echo esc_html__('Real Estate Law', 'mydefenselaw'); ?></option>
                        <option value="landlord-tenant"><?php echo esc_html__('Landlord/Tenant Issues', 'mydefenselaw'); ?></option>
                        <option value="intellectual-property"><?php echo esc_html__('Intellectual Property', 'mydefenselaw'); ?></option>
                        <option value="traffic-tickets"><?php echo esc_html__('Traffic Tickets', 'mydefenselaw'); ?></option>
                        <option value="other"><?php echo esc_html__('Other', 'mydefenselaw'); ?></option>
                    </select>
                    <textarea name="message" placeholder="<?php echo esc_attr__('Briefly describe your situation...', 'mydefenselaw'); ?>" rows="4"></textarea>
                    <label class="checkbox-label">
                        <input type="checkbox" name="urgent">
                        <span class="checkmark"></span>
                        <?php esc_html_e('This is urgent - I need immediate assistance', 'mydefenselaw'); ?>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="agreement" required>
                        <span class="checkmark"></span>
                        <?php esc_html_e('I understand that Defense Lawyers, P.A. has not yet agreed to represent me, and that submitting this form does not constitute a contract.', 'mydefenselaw'); ?>
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
