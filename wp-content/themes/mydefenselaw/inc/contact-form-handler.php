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
            'message' => __('Security verification failed. Please refresh the page and try again.', 'mydefenselaw')
        ));
    }

    // Honeypot spam check - this field should be empty
    if (!empty($_POST['website_url'])) {
        // Log potential spam attempt but return success to not alert bots
        error_log('Spam attempt detected from IP: ' . sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'] ?? '')));
        wp_send_json_success(array(
            'message' => __('Thank you! Your consultation request has been submitted successfully.', 'mydefenselaw')
        ));
    }

    // Sanitize and validate input
    $first_name = sanitize_text_field($_POST['firstName'] ?? '');
    $last_name = sanitize_text_field($_POST['lastName'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $legal_issue = sanitize_text_field($_POST['legalIssue'] ?? '');
    $urgency = sanitize_text_field($_POST['urgency'] ?? '');
    $message = sanitize_textarea_field($_POST['message'] ?? '');
    $is_urgent = isset($_POST['urgent']) && $_POST['urgent'] === 'on';
    $agreement = isset($_POST['agreement']) && $_POST['agreement'] === 'on';
    $form_source = sanitize_text_field($_POST['form_source'] ?? 'unknown');

    // Validation
    $errors = array();

    if (empty($first_name)) {
        $errors[] = __('First name is required.', 'mydefenselaw');
    }

    if (empty($last_name)) {
        $errors[] = __('Last name is required.', 'mydefenselaw');
    }

    if (empty($phone)) {
        $errors[] = __('Phone number is required.', 'mydefenselaw');
    }

    if (empty($email) || !is_email($email)) {
        $errors[] = __('A valid email address is required.', 'mydefenselaw');
    }

    if (empty($legal_issue)) {
        $errors[] = __('Please select a legal issue type.', 'mydefenselaw');
    }

    if (!$agreement) {
        $errors[] = __('You must agree to the terms to submit this form.', 'mydefenselaw');
    }

    if (!empty($errors)) {
        wp_send_json_error(array(
            'message' => implode(' ', $errors)
        ));
    }

    // Prepare email content
    $admin_email = get_option('admin_email');
    $site_name = get_bloginfo('name');

    $subject = ($is_urgent ? __('[URGENT]', 'mydefenselaw') . ' ' : '') . sprintf(
        __('New Consultation Request from %1$s %2$s', 'mydefenselaw'),
        $first_name,
        $last_name
    );

    // Prepare email data for styled templates
    $email_data = array(
        'first_name'  => $first_name,
        'last_name'   => $last_name,
        'email'       => $email,
        'phone'       => $phone,
        'legal_issue' => $legal_issue,
        'message'     => $message,
        'is_urgent'   => $is_urgent,
        'form_source' => $form_source,
        'ip_address'  => sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'] ?? '')),
    );

    // Generate styled HTML email for admin
    $email_body = mydefenselaw_email_contact_admin($email_data);

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        "From: {$site_name} <noreply@" . parse_url(home_url(), PHP_URL_HOST) . ">",
        "Reply-To: {$first_name} {$last_name} <{$email}>"
    );

    // Send email
    $sent = wp_mail($admin_email, $subject, $email_body, $headers);

    // Save submission to CPT regardless of email status
    $submission_data = array(
        'first_name'  => $first_name,
        'last_name'   => $last_name,
        'email'       => $email,
        'phone'       => $phone,
        'legal_issue' => $legal_issue,
        'urgency'     => $urgency,
        'message'     => $message,
        'is_urgent'   => $is_urgent,
        'form_source' => $form_source,
        'ip_address'  => sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'] ?? '')),
        'user_agent'  => sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'] ?? '')),
        'page_url'    => wp_get_referer(),
    );

    // Create the form submission CPT entry
    if (function_exists('mydefenselaw_create_form_submission')) {
        $submission_id = mydefenselaw_create_form_submission($submission_data);

        if (is_wp_error($submission_id)) {
            error_log('Failed to save form submission: ' . $submission_id->get_error_message());
        }
    }

    if ($sent) {
        // Send styled confirmation to user
        $user_subject = __('Thank you for contacting Defense Lawyers, P.A.', 'mydefenselaw');
        $user_body = mydefenselaw_email_contact_client($email_data);

        // Update headers for client email (no reply-to needed)
        $client_headers = array(
            'Content-Type: text/html; charset=UTF-8',
            "From: {$site_name} <noreply@" . parse_url(home_url(), PHP_URL_HOST) . ">",
        );

        wp_mail($email, $user_subject, $user_body, $client_headers);

        wp_send_json_success(array(
            'message' => __('Thank you! Your consultation request has been submitted successfully. We will contact you within 24-48 hours.', 'mydefenselaw')
        ));
    } else {
        wp_send_json_error(array(
            'message' => __('There was a problem sending your request. Please try again or call us directly at 888.444.0253.', 'mydefenselaw')
        ));
    }
}

// Register AJAX handlers
add_action('wp_ajax_mydefenselaw_contact_form', 'mydefenselaw_handle_contact_form');
add_action('wp_ajax_nopriv_mydefenselaw_contact_form', 'mydefenselaw_handle_contact_form');
