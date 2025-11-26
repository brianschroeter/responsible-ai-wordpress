<?php
/**
 * Contact Form AJAX Handler
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handle Contact Form Submission
 */
function responsibleai_handle_contact_form() {
    // Verify nonce
    if (!isset($_POST['contact_nonce']) || !wp_verify_nonce($_POST['contact_nonce'], 'responsibleai_contact')) {
        wp_send_json_error(array(
            'message' => __('Security verification failed. Please refresh the page and try again.', 'responsible-ai'),
        ));
    }

    // Honeypot check (spam prevention)
    if (!empty($_POST['website_url'])) {
        // Log spam attempt but return success to not alert bots
        error_log('Spam attempt detected from IP: ' . $_SERVER['REMOTE_ADDR']);
        wp_send_json_success(array(
            'message' => __('Thank you for your message!', 'responsible-ai'),
        ));
    }

    // Sanitize input
    $name    = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $email   = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $company = isset($_POST['company']) ? sanitize_text_field($_POST['company']) : '';
    $subject = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
    $source  = isset($_POST['form_source']) ? sanitize_text_field($_POST['form_source']) : 'contact_page';

    // Validate required fields
    $errors = array();

    if (empty($name)) {
        $errors[] = __('Name is required.', 'responsible-ai');
    }

    if (empty($email)) {
        $errors[] = __('Email is required.', 'responsible-ai');
    } elseif (!is_email($email)) {
        $errors[] = __('Please enter a valid email address.', 'responsible-ai');
    }

    if (empty($message)) {
        $errors[] = __('Message is required.', 'responsible-ai');
    }

    if (!empty($errors)) {
        wp_send_json_error(array(
            'message' => implode(' ', $errors),
        ));
    }

    // Get organization info
    $org_info = responsibleai_get_organization_info();
    $admin_email = $org_info['email'];

    // Build email data
    $email_data = array(
        'name'    => $name,
        'email'   => $email,
        'company' => $company,
        'subject' => $subject,
        'message' => $message,
        'source'  => $source,
        'ip'      => $_SERVER['REMOTE_ADDR'],
        'date'    => current_time('mysql'),
    );

    // Send admin notification
    $admin_subject = sprintf(
        /* translators: %s: form source */
        __('[Responsible AI] New Contact Form Submission - %s', 'responsible-ai'),
        ucfirst(str_replace('_', ' ', $source))
    );

    $admin_body = responsibleai_email_contact_admin($email_data);

    $admin_headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    );

    $admin_sent = wp_mail($admin_email, $admin_subject, $admin_body, $admin_headers);

    // Send confirmation to user
    $user_subject = __('Thank you for contacting Responsible AI Institute', 'responsible-ai');
    $user_body = responsibleai_email_contact_user($email_data);

    $user_headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: Responsible AI Institute <' . $admin_email . '>',
    );

    wp_mail($email, $user_subject, $user_body, $user_headers);

    // Log submission (optional: could save to CPT)
    error_log('Contact form submitted by: ' . $email . ' from ' . $source);

    // Return success
    wp_send_json_success(array(
        'message' => __('Thank you for your message! We\'ll be in touch soon.', 'responsible-ai'),
    ));
}
add_action('wp_ajax_responsibleai_contact_form', 'responsibleai_handle_contact_form');
add_action('wp_ajax_nopriv_responsibleai_contact_form', 'responsibleai_handle_contact_form');

/**
 * Handle Newsletter Subscription
 */
function responsibleai_handle_newsletter() {
    // Verify nonce
    if (!isset($_POST['newsletter_nonce']) || !wp_verify_nonce($_POST['newsletter_nonce'], 'responsibleai_newsletter')) {
        wp_send_json_error(array(
            'message' => __('Security verification failed.', 'responsible-ai'),
        ));
    }

    // Sanitize input
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';

    // Validate email
    if (empty($email) || !is_email($email)) {
        wp_send_json_error(array(
            'message' => __('Please enter a valid email address.', 'responsible-ai'),
        ));
    }

    // Here you would integrate with your newsletter service
    // For example: Mailchimp, ConvertKit, SendGrid, etc.
    // For now, we'll just log it
    error_log('Newsletter subscription: ' . $email);

    // Return success
    wp_send_json_success(array(
        'message' => __('Thank you for subscribing! Please check your email to confirm your subscription.', 'responsible-ai'),
    ));
}
add_action('wp_ajax_responsibleai_newsletter', 'responsibleai_handle_newsletter');
add_action('wp_ajax_nopriv_responsibleai_newsletter', 'responsibleai_handle_newsletter');
