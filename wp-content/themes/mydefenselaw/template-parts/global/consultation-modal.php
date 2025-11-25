<!-- Consultation Modal -->
<div id="consultationModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Request Your Free Consultation</h3>
            <p>All information is confidential and protected by attorney-client privilege.</p>
            <button class="modal-close" id="modalClose">&times;</button>
        </div>
        <div class="modal-body">
            <?php
            /**
             * Contact form will be integrated here
             * Options: Contact Form 7, WPForms, Gravity Forms, or custom form
             * For now, keeping placeholder structure matching original design
             */
            ?>
            <form class="modal-form" id="modalContactForm" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                <input type="hidden" name="action" value="consultation_request">
                <?php wp_nonce_field('consultation_request_action', 'consultation_request_nonce'); ?>

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
                <label class="modal-checkbox-label">
                    <input type="checkbox" name="urgent">
                    <span class="checkmark"></span>
                    This is urgent - I need immediate assistance
                </label>
                <label class="modal-checkbox-label">
                    <input type="checkbox" name="agreement" required>
                    <span class="checkmark"></span>
                    I understand that <?php bloginfo('name'); ?> has not yet agreed to represent me, and that submitting this form does not constitute a contract.
                </label>
                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fas fa-paper-plane"></i>
                    Request Free Consultation
                </button>
            </form>
        </div>
    </div>
</div>
