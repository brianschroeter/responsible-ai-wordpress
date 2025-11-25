<?php
/**
 * Consultation Modal Template Part
 * Pixel-perfect match with source
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */
?>

<div id="consultationModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-title"><?php esc_html_e('Request Your Free Consultation', 'mydefenselaw'); ?></h3>
            <p><?php esc_html_e('All information is confidential and protected by attorney-client privilege.', 'mydefenselaw'); ?></p>
            <button class="modal-close" id="modalClose" aria-label="<?php esc_attr_e('Close modal', 'mydefenselaw'); ?>">&times;</button>
        </div>
        <div class="modal-body">
            <form class="modal-form" id="modalContactForm" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
                <?php wp_nonce_field('mydefenselaw_contact', 'contact_nonce'); ?>
                <input type="hidden" name="action" value="mydefenselaw_contact_form">
                <input type="hidden" name="form_source" value="modal">
                <!-- Honeypot for spam protection -->
                <input type="text" name="website_url" class="screen-reader-text" tabindex="-1" autocomplete="off" aria-hidden="true">

                <div class="form-row">
                    <div class="form-field">
                        <label for="modal-firstName" class="screen-reader-text"><?php esc_html_e('First Name (required)', 'mydefenselaw'); ?></label>
                        <input type="text" id="modal-firstName" name="firstName" placeholder="<?php esc_attr_e('First Name *', 'mydefenselaw'); ?>" required aria-required="true">
                    </div>
                    <div class="form-field">
                        <label for="modal-lastName" class="screen-reader-text"><?php esc_html_e('Last Name (required)', 'mydefenselaw'); ?></label>
                        <input type="text" id="modal-lastName" name="lastName" placeholder="<?php esc_attr_e('Last Name *', 'mydefenselaw'); ?>" required aria-required="true">
                    </div>
                </div>
                <div class="form-field">
                    <label for="modal-phone" class="screen-reader-text"><?php esc_html_e('Phone Number (required)', 'mydefenselaw'); ?></label>
                    <input type="tel" id="modal-phone" name="phone" placeholder="<?php esc_attr_e('Phone Number *', 'mydefenselaw'); ?>" required aria-required="true">
                </div>
                <div class="form-field">
                    <label for="modal-email" class="screen-reader-text"><?php esc_html_e('Email Address (required)', 'mydefenselaw'); ?></label>
                    <input type="email" id="modal-email" name="email" placeholder="<?php esc_attr_e('Email Address *', 'mydefenselaw'); ?>" required aria-required="true">
                </div>
                <div class="form-field">
                    <label for="modal-legalIssue" class="screen-reader-text"><?php esc_html_e('Type of legal issue (required)', 'mydefenselaw'); ?></label>
                    <select id="modal-legalIssue" name="legalIssue" required aria-required="true">
                    <option value=""><?php esc_html_e('What type of legal issue? *', 'mydefenselaw'); ?></option>
                    <option value="civil-litigation"><?php esc_html_e('Civil Defense Litigation', 'mydefenselaw'); ?></option>
                    <option value="consumer-protection"><?php esc_html_e('Consumer Protection', 'mydefenselaw'); ?></option>
                    <option value="family-law"><?php esc_html_e('Family Law', 'mydefenselaw'); ?></option>
                    <option value="bankruptcy"><?php esc_html_e('Bankruptcy', 'mydefenselaw'); ?></option>
                    <option value="contract-dispute"><?php esc_html_e('Contract Law', 'mydefenselaw'); ?></option>
                    <option value="real-estate"><?php esc_html_e('Real Estate Law', 'mydefenselaw'); ?></option>
                    <option value="landlord-tenant"><?php esc_html_e('Landlord/Tenant Issues', 'mydefenselaw'); ?></option>
                    <option value="intellectual-property"><?php esc_html_e('Intellectual Property', 'mydefenselaw'); ?></option>
                    <option value="traffic-tickets"><?php esc_html_e('Traffic Tickets', 'mydefenselaw'); ?></option>
                    <option value="other"><?php esc_html_e('Other', 'mydefenselaw'); ?></option>
                    </select>
                </div>
                <div class="form-field">
                    <label for="modal-message" class="screen-reader-text"><?php esc_html_e('Describe your situation', 'mydefenselaw'); ?></label>
                    <textarea id="modal-message" name="message" placeholder="<?php esc_attr_e('Briefly describe your situation...', 'mydefenselaw'); ?>" rows="4"></textarea>
                </div>
                <label class="modal-checkbox-label">
                    <input type="checkbox" name="urgent">
                    <span class="checkmark"></span>
                    <?php esc_html_e('This is urgent - I need immediate assistance', 'mydefenselaw'); ?>
                </label>
                <label class="modal-checkbox-label">
                    <input type="checkbox" name="agreement" required>
                    <span class="checkmark"></span>
                    <?php esc_html_e('I understand that Defense Lawyers, P.A. has not yet agreed to represent me, and that submitting this form does not constitute a contract.', 'mydefenselaw'); ?>
                </label>
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fas fa-paper-plane"></i>
                    <?php esc_html_e('Request Free Consultation', 'mydefenselaw'); ?>
                </button>
            </form>
            <div id="modalFormResponse" class="form-response" style="display: none;" role="alert" aria-live="polite"></div>
        </div>
    </div>
</div>
