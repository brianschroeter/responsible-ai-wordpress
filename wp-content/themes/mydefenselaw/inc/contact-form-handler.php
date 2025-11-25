<?php
/**
 * Contact Form AJAX Handler
 * Custom PHP/AJAX form submission handling
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handle AJAX form submission
 */
function mydefenselaw_handle_contact_form() {
    // Verify nonce
    if (!isset($_POST['contact_nonce']) || !wp_verify_nonce($_POST['contact_nonce'], 'mydefenselaw_contact')) {
        wp_send_json_error(array(
            'message' => 'Security verification failed. Please refresh the page and try again.'
        ));
    }

    // Honeypot spam check - this field should be empty
    if (!empty($_POST['website_url'])) {
        // Log potential spam attempt but return success to not alert bots
        error_log('Spam attempt detected from IP: ' . sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'] ?? '')));
        wp_send_json_success(array(
            'message' => 'Thank you! Your consultation request has been submitted successfully.'
        ));
    }

    // Sanitize and validate input
    $first_name = sanitize_text_field($_POST['firstName'] ?? '');
    $last_name = sanitize_text_field($_POST['lastName'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $legal_issue = sanitize_text_field($_POST['legalIssue'] ?? '');
    $message = sanitize_textarea_field($_POST['message'] ?? '');
    $is_urgent = isset($_POST['urgent']) && $_POST['urgent'] === 'on';
    $agreement = isset($_POST['agreement']) && $_POST['agreement'] === 'on';

    // Validation
    $errors = array();

    if (empty($first_name)) {
        $errors[] = 'First name is required.';
    }

    if (empty($last_name)) {
        $errors[] = 'Last name is required.';
    }

    if (empty($phone)) {
        $errors[] = 'Phone number is required.';
    }

    if (empty($email) || !is_email($email)) {
        $errors[] = 'A valid email address is required.';
    }

    if (empty($legal_issue)) {
        $errors[] = 'Please select a legal issue type.';
    }

    if (!$agreement) {
        $errors[] = 'You must agree to the terms to submit this form.';
    }

    if (!empty($errors)) {
        wp_send_json_error(array(
            'message' => implode(' ', $errors)
        ));
    }

    // Prepare email content
    $admin_email = get_option('admin_email');
    $site_name = get_bloginfo('name');

    $subject = ($is_urgent ? '[URGENT] ' : '') . "New Consultation Request from {$first_name} {$last_name}";

    $email_body = "
New consultation request received from the website.

" . ($is_urgent ? "*** THIS IS AN URGENT REQUEST ***\n\n" : "") . "
CONTACT INFORMATION
-------------------
Name: {$first_name} {$last_name}
Phone: {$phone}
Email: {$email}

LEGAL ISSUE
-----------
Type: {$legal_issue}

MESSAGE
-------
{$message}

---
This message was sent from the {$site_name} website contact form.
Submitted on: " . current_time('F j, Y \a\t g:i a') . "
IP Address: " . sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'] ?? '')) . "
";

    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        "From: {$site_name} <wordpress@" . parse_url(home_url(), PHP_URL_HOST) . ">",
        "Reply-To: {$first_name} {$last_name} <{$email}>"
    );

    // Send email
    $sent = wp_mail($admin_email, $subject, $email_body, $headers);

    if ($sent) {
        // Optionally send confirmation to user
        $user_subject = "Thank you for contacting Defense Lawyers, P.A.";
        $user_body = "
Dear {$first_name},

Thank you for contacting Defense Lawyers, P.A. We have received your consultation request and will review your information promptly.

" . ($is_urgent ? "We understand this is an urgent matter and will prioritize your request.\n\n" : "") . "
A member of our legal team will contact you within 24-48 business hours. If you have an urgent matter and need immediate assistance, please call us directly at 888.444.0253.

Best regards,
Defense Lawyers, P.A.
Boca Raton, FL

---
This is an automated response. Please do not reply to this email.
";

        wp_mail($email, $user_subject, $user_body, $headers);

        wp_send_json_success(array(
            'message' => 'Thank you! Your consultation request has been submitted successfully. We will contact you within 24-48 hours.'
        ));
    } else {
        wp_send_json_error(array(
            'message' => 'There was a problem sending your request. Please try again or call us directly at 888.444.0253.'
        ));
    }
}

// Register AJAX handlers
add_action('wp_ajax_mydefenselaw_contact_form', 'mydefenselaw_handle_contact_form');
add_action('wp_ajax_nopriv_mydefenselaw_contact_form', 'mydefenselaw_handle_contact_form');
