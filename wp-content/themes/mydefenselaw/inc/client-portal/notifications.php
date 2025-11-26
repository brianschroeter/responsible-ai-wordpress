<?php
/**
 * Client Portal Notifications
 *
 * Handles email and in-portal notifications for client portal system.
 * Sends notifications for:
 * - New messages (client and staff)
 * - Case status updates
 * - Document sharing
 * - Court date reminders
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Initialize notification system
 *
 * Sets up WP Cron schedules and notification hooks
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_init_notifications() {
	// Schedule court date reminder checker
	mydefenselaw_portal_schedule_reminders();

	// Register action hooks for notifications
	add_action('acf/save_post', 'mydefenselaw_portal_handle_message_save', 20);
	add_action('acf/save_post', 'mydefenselaw_portal_handle_document_save', 20);
	add_action('set_object_terms', 'mydefenselaw_portal_handle_case_status_change', 10, 6);
}

// ==========================================
// CLIENT NOTIFICATIONS
// ==========================================

/**
 * Send notification when client receives a new message
 *
 * @since 1.0.0
 * @param int $message_id Message post ID
 */
function mydefenselaw_portal_notify_new_message($message_id) {
	$message = get_post($message_id);
	if (!$message) {
		return;
	}

	$client_id = get_post_meta($message_id, '_client_id', true);
	$from_type = get_post_meta($message_id, '_from_type', true);

	// Don't notify if from client themselves
	if (!$client_id || $from_type === 'client') {
		return;
	}

	$client = get_userdata($client_id);
	if (!$client || !mydefenselaw_portal_client_wants_notification($client_id, 'new_message')) {
		return;
	}

	$sender_name = get_post_meta($message_id, '_from_name', true) ?: 'Your Legal Team';
	$subject = 'New Message from ' . $sender_name . ' - MyDefenseLaw';

	$body = mydefenselaw_portal_get_email_template('new_message', array(
		'client_name' => $client->first_name ?: $client->display_name,
		'sender_name' => $sender_name,
		'message_subject' => $message->post_title,
		'message_preview' => wp_trim_words($message->post_content, 50),
		'portal_url' => home_url('/portal/messages/'),
	));

	mydefenselaw_portal_send_email($client->user_email, $subject, $body);
}

/**
 * Send notification when case status changes
 *
 * @since 1.0.0
 * @param int    $case_id     Case post ID
 * @param string $old_status  Previous status slug
 * @param string $new_status  New status slug
 */
function mydefenselaw_portal_notify_case_update($case_id, $old_status, $new_status) {
	$client_id = get_post_meta($case_id, '_client_id', true);
	if (!$client_id) {
		return;
	}

	$client = get_userdata($client_id);
	if (!$client || !mydefenselaw_portal_client_wants_notification($client_id, 'case_update')) {
		return;
	}

	$case_number = get_post_meta($case_id, '_case_number', true) ?: get_the_title($case_id);
	$subject = 'Case Status Update - ' . $case_number;

	$body = mydefenselaw_portal_get_email_template('case_update', array(
		'client_name' => $client->first_name ?: $client->display_name,
		'case_number' => $case_number,
		'old_status' => ucfirst($old_status),
		'new_status' => ucfirst($new_status),
		'portal_url' => home_url('/portal/cases/'),
	));

	mydefenselaw_portal_send_email($client->user_email, $subject, $body);
}

/**
 * Send notification when new document is shared with client
 *
 * @since 1.0.0
 * @param int $document_id Document post ID
 */
function mydefenselaw_portal_notify_document_shared($document_id) {
	$client_id = get_post_meta($document_id, '_client_id', true);
	$is_client_upload = get_post_meta($document_id, '_is_client_upload', true);

	// Don't notify for client's own uploads
	if (!$client_id || $is_client_upload) {
		return;
	}

	$client = get_userdata($client_id);
	if (!$client || !mydefenselaw_portal_client_wants_notification($client_id, 'document_shared')) {
		return;
	}

	$document = get_post($document_id);
	$subject = 'New Document Available - MyDefenseLaw';

	$body = mydefenselaw_portal_get_email_template('document_shared', array(
		'client_name' => $client->first_name ?: $client->display_name,
		'document_name' => $document->post_title,
		'portal_url' => home_url('/portal/documents/'),
	));

	mydefenselaw_portal_send_email($client->user_email, $subject, $body);
}

/**
 * Send court date reminder (scheduled via WP Cron)
 *
 * @since 1.0.0
 * @param int $case_id      Case post ID
 * @param int $days_until   Number of days until court date
 */
function mydefenselaw_portal_notify_court_reminder($case_id, $days_until) {
	$client_id = get_post_meta($case_id, '_client_id', true);
	if (!$client_id) {
		return;
	}

	$client = get_userdata($client_id);
	if (!$client || !mydefenselaw_portal_client_wants_notification($client_id, 'court_reminder')) {
		return;
	}

	$case_number = get_post_meta($case_id, '_case_number', true) ?: get_the_title($case_id);
	$court_date = get_post_meta($case_id, '_next_court_date', true);
	$court_location = get_post_meta($case_id, '_court_location', true) ?: 'TBD';

	if (!$court_date) {
		return;
	}

	$subject = 'Court Date Reminder - ' . $days_until . ' Days Away';

	$body = mydefenselaw_portal_get_email_template('court_reminder', array(
		'client_name' => $client->first_name ?: $client->display_name,
		'case_number' => $case_number,
		'court_date' => date('l, F j, Y', strtotime($court_date)),
		'court_location' => $court_location,
		'days_until' => $days_until,
		'portal_url' => home_url('/portal/cases/'),
	));

	mydefenselaw_portal_send_email($client->user_email, $subject, $body);
}

// ==========================================
// STAFF NOTIFICATIONS
// ==========================================

/**
 * Notify staff when client sends a message
 *
 * @since 1.0.0
 * @param int $message_id Message post ID
 */
function mydefenselaw_portal_notify_staff_new_message($message_id) {
	$from_type = get_post_meta($message_id, '_from_type', true);

	// Only notify for client messages
	if ($from_type !== 'client') {
		return;
	}

	$client_id = get_post_meta($message_id, '_client_id', true);
	$client = get_userdata($client_id);
	$message = get_post($message_id);

	if (!$client || !$message) {
		return;
	}

	// Get notification email from portal settings or fallback to admin email
	$admin_email = get_option('admin_email');
	if (function_exists('get_field')) {
		$portal_email = get_field('portal_notification_email', 'option');
		if ($portal_email) {
			$admin_email = $portal_email;
		}
	}

	$subject = 'New Portal Message from ' . $client->display_name;

	$body = mydefenselaw_portal_get_email_template('staff_new_message', array(
		'client_name' => $client->display_name,
		'client_email' => $client->user_email,
		'message_subject' => $message->post_title,
		'message_content' => wp_kses_post($message->post_content),
		'admin_url' => admin_url('post.php?post=' . $message_id . '&action=edit'),
	));

	mydefenselaw_portal_send_email($admin_email, $subject, $body);
}

/**
 * Notify staff when client uploads a document
 *
 * @since 1.0.0
 * @param int $document_id Document post ID
 */
function mydefenselaw_portal_notify_staff_document_upload($document_id) {
	$is_client_upload = get_post_meta($document_id, '_is_client_upload', true);

	// Only notify for client uploads
	if (!$is_client_upload) {
		return;
	}

	$client_id = get_post_meta($document_id, '_client_id', true);
	$client = get_userdata($client_id);
	$document = get_post($document_id);

	if (!$client || !$document) {
		return;
	}

	// Get notification email from portal settings or fallback to admin email
	$admin_email = get_option('admin_email');
	if (function_exists('get_field')) {
		$portal_email = get_field('portal_notification_email', 'option');
		if ($portal_email) {
			$admin_email = $portal_email;
		}
	}

	$file_path = get_post_meta($document_id, '_file_path', true);
	$file_type = $file_path ? strtoupper(pathinfo($file_path, PATHINFO_EXTENSION)) : 'Unknown';

	$subject = 'New Document Uploaded by ' . $client->display_name;

	$body = mydefenselaw_portal_get_email_template('staff_document_upload', array(
		'client_name' => $client->display_name,
		'client_email' => $client->user_email,
		'document_name' => $document->post_title,
		'file_type' => $file_type,
		'admin_url' => admin_url('post.php?post=' . $document_id . '&action=edit'),
	));

	mydefenselaw_portal_send_email($admin_email, $subject, $body);
}

// ==========================================
// EMAIL INFRASTRUCTURE
// ==========================================

/**
 * Send email using wp_mail with HTML template
 *
 * @since 1.0.0
 * @param string $to      Recipient email address
 * @param string $subject Email subject
 * @param string $body    HTML email body
 * @return bool           True on success, false on failure
 */
function mydefenselaw_portal_send_email($to, $subject, $body) {
	// Check if notifications are enabled
	if (function_exists('get_field')) {
		$enabled = get_field('portal_email_notifications', 'option');
		if ($enabled === false) {
			return false;
		}
	}

	$headers = array(
		'Content-Type: text/html; charset=UTF-8',
		'From: MyDefenseLaw <noreply@' . parse_url(home_url(), PHP_URL_HOST) . '>',
	);

	return wp_mail($to, $subject, $body, $headers);
}

/**
 * Get email template with variables replaced
 *
 * @since 1.0.0
 * @param string $template_name Template identifier
 * @param array  $vars          Variables to replace in template
 * @return string               Complete HTML email
 */
function mydefenselaw_portal_get_email_template($template_name, $vars = array()) {
	// Base HTML email wrapper
	$wrapper = mydefenselaw_portal_email_wrapper();

	// Get template content
	$content = mydefenselaw_portal_email_content($template_name, $vars);

	return str_replace('{{CONTENT}}', $content, $wrapper);
}

/**
 * HTML email wrapper with branding
 *
 * Uses centralized email template system for consistent styling.
 *
 * @since 1.0.0
 * @return string HTML email wrapper template
 */
function mydefenselaw_portal_email_wrapper() {
	// Return a template string with {{CONTENT}} placeholder
	// This will be replaced by mydefenselaw_portal_get_email_template()
	// We use the centralized wrapper but need to return a format compatible with existing code
	$phone = function_exists('mydefenselaw_get_phone') ? mydefenselaw_get_phone() : '888.444.0253';
	$site_name = get_bloginfo('name');
	$year = date('Y');

	return '<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="x-apple-disable-message-reformatting">
	<meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no">
	<title>' . esc_html($site_name) . '</title>
	<!--[if mso]>
	<noscript>
		<xml>
			<o:OfficeDocumentSettings>
				<o:PixelsPerInch>96</o:PixelsPerInch>
			</o:OfficeDocumentSettings>
		</xml>
	</noscript>
	<![endif]-->
	<style>
		@import url(\'https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700;900&family=Open+Sans:wght@400;600;700&display=swap\');
		body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
		table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
		img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
		body { margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #f4f4f4; }
		a { color: #2563eb; }
		a:hover { color: #1d4ed8; }
		.button-primary:hover { background-color: #b91c1c !important; }
		.button-secondary:hover { background-color: #152a4a !important; }
		@media screen and (max-width: 600px) {
			.email-container { width: 100% !important; }
			.mobile-padding { padding-left: 20px !important; padding-right: 20px !important; }
		}
	</style>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f4;font-family:\'Open Sans\',Arial,sans-serif;-webkit-font-smoothing:antialiased;">

	<div style="display:none;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;">
		Defense Lawyers, P.A. - Client Portal Notification
	</div>

	<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f4f4f4;">
		<tr>
			<td align="center" style="padding:30px 15px;">
				<table role="presentation" class="email-container" cellpadding="0" cellspacing="0" width="600" style="max-width:600px;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">

					<!-- Top Gold Accent Bar -->
					<tr>
						<td style="background-color:#fbbf24;height:4px;font-size:0;line-height:0;">&nbsp;</td>
					</tr>

					<!-- Header -->
					<tr>
						<td style="background:linear-gradient(135deg,#1a365d 0%,#2d4a7c 100%);padding:30px 40px;text-align:center;">
							<h1 style="color:#ffffff;margin:0;font-family:\'Merriweather\',Georgia,serif;font-size:28px;font-weight:700;">Defense Lawyers, P.A.</h1>
							<p style="color:#94a3b8;margin:8px 0 0 0;font-family:\'Open Sans\',Arial,sans-serif;font-size:14px;letter-spacing:0.5px;">Client Portal</p>
						</td>
					</tr>

					<!-- Content -->
					<tr>
						<td class="mobile-padding" style="padding:40px;">
							{{CONTENT}}
						</td>
					</tr>

					<!-- Divider -->
					<tr>
						<td style="padding:0 40px;">
							<table role="presentation" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td style="border-top:1px solid #e2e8f0;"></td>
								</tr>
							</table>
						</td>
					</tr>

					<!-- Footer -->
					<tr>
						<td style="background-color:#f8fafc;padding:30px 40px;">
							<table role="presentation" cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td style="text-align:center;padding-bottom:20px;">
										<p style="margin:0 0 8px 0;font-family:\'Open Sans\',Arial,sans-serif;font-size:14px;color:#1a365d;font-weight:600;">Questions? We\'re Here to Help</p>
										<p style="margin:0;">
											<a href="tel:' . esc_attr(preg_replace('/[^0-9]/', '', $phone)) . '" style="color:#dc2626;font-family:\'Open Sans\',Arial,sans-serif;font-size:18px;font-weight:700;text-decoration:none;">' . esc_html($phone) . '</a>
										</p>
									</td>
								</tr>
								<tr>
									<td style="text-align:center;padding-top:15px;border-top:1px solid #e2e8f0;">
										<p style="margin:0 0 5px 0;font-family:\'Open Sans\',Arial,sans-serif;font-size:12px;color:#64748b;">
											&copy; ' . esc_html($year) . ' Defense Lawyers, P.A. All rights reserved.
										</p>
										<p style="margin:0;font-family:\'Open Sans\',Arial,sans-serif;font-size:11px;color:#94a3b8;">
											This is an automated message from your Client Portal.
										</p>
									</td>
								</tr>
							</table>
						</td>
					</tr>

					<!-- Bottom Navy Bar -->
					<tr>
						<td style="background-color:#1a365d;height:6px;font-size:0;line-height:0;">&nbsp;</td>
					</tr>

				</table>
			</td>
		</tr>
	</table>

</body>
</html>';
}

/**
 * Email content templates
 *
 * Uses Merriweather for headings and Open Sans for body text
 * to match the frontend design system.
 *
 * @since 1.0.0
 * @param string $template Template identifier
 * @param array  $vars     Variables to replace
 * @return string          Template content with variables replaced
 */
function mydefenselaw_portal_email_content($template, $vars) {
	$templates = array(
		'new_message' => '
			<h2 style="font-family:\'Merriweather\',Georgia,serif;color:#1a365d;margin:0 0 20px 0;font-size:24px;font-weight:700;">New Message</h2>
			<p style="font-family:\'Open Sans\',Arial,sans-serif;color:#333333;line-height:1.7;margin:0 0 15px 0;font-size:16px;">Hello {{client_name}},</p>
			<p style="font-family:\'Open Sans\',Arial,sans-serif;color:#333333;line-height:1.7;margin:0 0 15px 0;font-size:16px;">You have received a new message from <strong style="color:#1a365d;">{{sender_name}}</strong>.</p>
			<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:20px 0;">
				<tr>
					<td style="background:#f0f9ff;padding:20px;border-radius:0 8px 8px 0;border-left:4px solid #2563eb;">
						<p style="margin:0 0 10px 0;font-family:\'Open Sans\',Arial,sans-serif;font-weight:700;color:#1a365d;font-size:16px;">{{message_subject}}</p>
						<p style="margin:0;font-family:\'Open Sans\',Arial,sans-serif;color:#64748b;font-size:15px;line-height:1.6;">{{message_preview}}...</p>
					</td>
				</tr>
			</table>
			<table role="presentation" cellpadding="0" cellspacing="0" style="margin:30px auto 0 auto;">
				<tr>
					<td style="border-radius:6px;background-color:#dc2626;">
						<a href="{{portal_url}}" class="button-primary" style="display:inline-block;background:#dc2626;color:#ffffff;padding:14px 32px;text-decoration:none;border-radius:6px;font-family:\'Open Sans\',Arial,sans-serif;font-weight:600;font-size:16px;">View Message</a>
					</td>
				</tr>
			</table>',

		'case_update' => '
			<h2 style="font-family:\'Merriweather\',Georgia,serif;color:#1a365d;margin:0 0 20px 0;font-size:24px;font-weight:700;">Case Status Update</h2>
			<p style="font-family:\'Open Sans\',Arial,sans-serif;color:#333333;line-height:1.7;margin:0 0 15px 0;font-size:16px;">Hello {{client_name}},</p>
			<p style="font-family:\'Open Sans\',Arial,sans-serif;color:#333333;line-height:1.7;margin:0 0 15px 0;font-size:16px;">The status of your case <strong style="color:#1a365d;">{{case_number}}</strong> has been updated.</p>
			<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:20px 0;">
				<tr>
					<td style="background:#f8fafc;padding:25px;border-radius:8px;text-align:center;">
						<span style="font-family:\'Open Sans\',Arial,sans-serif;color:#94a3b8;text-decoration:line-through;font-size:16px;">{{old_status}}</span>
						<span style="margin:0 20px;color:#fbbf24;font-size:24px;">→</span>
						<span style="font-family:\'Open Sans\',Arial,sans-serif;color:#10b981;font-weight:700;font-size:18px;">{{new_status}}</span>
					</td>
				</tr>
			</table>
			<table role="presentation" cellpadding="0" cellspacing="0" style="margin:30px auto 0 auto;">
				<tr>
					<td style="border-radius:6px;background-color:#dc2626;">
						<a href="{{portal_url}}" class="button-primary" style="display:inline-block;background:#dc2626;color:#ffffff;padding:14px 32px;text-decoration:none;border-radius:6px;font-family:\'Open Sans\',Arial,sans-serif;font-weight:600;font-size:16px;">View Case Details</a>
					</td>
				</tr>
			</table>',

		'document_shared' => '
			<h2 style="font-family:\'Merriweather\',Georgia,serif;color:#1a365d;margin:0 0 20px 0;font-size:24px;font-weight:700;">New Document Available</h2>
			<p style="font-family:\'Open Sans\',Arial,sans-serif;color:#333333;line-height:1.7;margin:0 0 15px 0;font-size:16px;">Hello {{client_name}},</p>
			<p style="font-family:\'Open Sans\',Arial,sans-serif;color:#333333;line-height:1.7;margin:0 0 15px 0;font-size:16px;">A new document has been shared with you:</p>
			<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:20px 0;">
				<tr>
					<td style="background:#f8fafc;padding:20px;border-radius:8px;text-align:center;">
						<p style="margin:0;font-family:\'Open Sans\',Arial,sans-serif;font-weight:700;color:#1a365d;font-size:18px;">📄 {{document_name}}</p>
					</td>
				</tr>
			</table>
			<table role="presentation" cellpadding="0" cellspacing="0" style="margin:30px auto 0 auto;">
				<tr>
					<td style="border-radius:6px;background-color:#dc2626;">
						<a href="{{portal_url}}" class="button-primary" style="display:inline-block;background:#dc2626;color:#ffffff;padding:14px 32px;text-decoration:none;border-radius:6px;font-family:\'Open Sans\',Arial,sans-serif;font-weight:600;font-size:16px;">View Documents</a>
					</td>
				</tr>
			</table>',

		'court_reminder' => '
			<h2 style="font-family:\'Merriweather\',Georgia,serif;color:#1a365d;margin:0 0 20px 0;font-size:24px;font-weight:700;">⚠️ Court Date Reminder</h2>
			<p style="font-family:\'Open Sans\',Arial,sans-serif;color:#333333;line-height:1.7;margin:0 0 15px 0;font-size:16px;">Hello {{client_name}},</p>
			<p style="font-family:\'Open Sans\',Arial,sans-serif;color:#333333;line-height:1.7;margin:0 0 15px 0;font-size:16px;">This is a reminder that you have an upcoming court date in <strong style="color:#dc2626;">{{days_until}} days</strong>.</p>
			<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:20px 0;">
				<tr>
					<td style="background:#fef3c7;padding:20px;border-radius:0 8px 8px 0;border-left:4px solid #f59e0b;">
						<p style="margin:0 0 12px 0;font-family:\'Open Sans\',Arial,sans-serif;font-weight:700;color:#92400e;font-size:16px;">Case: {{case_number}}</p>
						<p style="margin:0 0 8px 0;font-family:\'Open Sans\',Arial,sans-serif;color:#78350f;font-size:15px;">📅 <strong>{{court_date}}</strong></p>
						<p style="margin:0;font-family:\'Open Sans\',Arial,sans-serif;color:#78350f;font-size:15px;">📍 {{court_location}}</p>
					</td>
				</tr>
			</table>
			<table role="presentation" cellpadding="0" cellspacing="0" style="margin:30px auto 0 auto;">
				<tr>
					<td style="border-radius:6px;background-color:#dc2626;">
						<a href="{{portal_url}}" class="button-primary" style="display:inline-block;background:#dc2626;color:#ffffff;padding:14px 32px;text-decoration:none;border-radius:6px;font-family:\'Open Sans\',Arial,sans-serif;font-weight:600;font-size:16px;">View Case Details</a>
					</td>
				</tr>
			</table>',

		'staff_new_message' => '
			<h2 style="font-family:\'Merriweather\',Georgia,serif;color:#1a365d;margin:0 0 20px 0;font-size:24px;font-weight:700;">New Portal Message</h2>
			<p style="font-family:\'Open Sans\',Arial,sans-serif;color:#333333;line-height:1.7;margin:0 0 15px 0;font-size:16px;">A client has sent a new message through the portal.</p>
			<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:20px 0;">
				<tr>
					<td style="background:#f8fafc;padding:20px;border-radius:8px;">
						<p style="margin:0 0 10px 0;font-family:\'Open Sans\',Arial,sans-serif;font-size:15px;color:#333333;"><strong style="color:#64748b;">From:</strong> {{client_name}} ({{client_email}})</p>
						<p style="margin:0 0 15px 0;font-family:\'Open Sans\',Arial,sans-serif;font-size:15px;color:#333333;"><strong style="color:#64748b;">Subject:</strong> {{message_subject}}</p>
						<hr style="border:none;border-top:1px solid #e2e8f0;margin:15px 0;">
						<div style="font-family:\'Open Sans\',Arial,sans-serif;color:#333333;font-size:15px;line-height:1.7;">{{message_content}}</div>
					</td>
				</tr>
			</table>
			<table role="presentation" cellpadding="0" cellspacing="0" style="margin:30px auto 0 auto;">
				<tr>
					<td style="border-radius:6px;background-color:#1a365d;">
						<a href="{{admin_url}}" class="button-secondary" style="display:inline-block;background:#1a365d;color:#ffffff;padding:14px 32px;text-decoration:none;border-radius:6px;font-family:\'Open Sans\',Arial,sans-serif;font-weight:600;font-size:16px;">View in Admin</a>
					</td>
				</tr>
			</table>',

		'staff_document_upload' => '
			<h2 style="font-family:\'Merriweather\',Georgia,serif;color:#1a365d;margin:0 0 20px 0;font-size:24px;font-weight:700;">New Document Uploaded</h2>
			<p style="font-family:\'Open Sans\',Arial,sans-serif;color:#333333;line-height:1.7;margin:0 0 15px 0;font-size:16px;">A client has uploaded a new document to the portal.</p>
			<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:20px 0;">
				<tr>
					<td style="background:#f8fafc;padding:20px;border-radius:8px;">
						<p style="margin:0 0 10px 0;font-family:\'Open Sans\',Arial,sans-serif;font-size:15px;color:#333333;"><strong style="color:#64748b;">From:</strong> {{client_name}} ({{client_email}})</p>
						<p style="margin:0 0 10px 0;font-family:\'Open Sans\',Arial,sans-serif;font-size:15px;color:#333333;"><strong style="color:#64748b;">Document:</strong> {{document_name}}</p>
						<p style="margin:0;font-family:\'Open Sans\',Arial,sans-serif;font-size:15px;color:#333333;"><strong style="color:#64748b;">File Type:</strong> {{file_type}}</p>
					</td>
				</tr>
			</table>
			<table role="presentation" cellpadding="0" cellspacing="0" style="margin:30px auto 0 auto;">
				<tr>
					<td style="border-radius:6px;background-color:#1a365d;">
						<a href="{{admin_url}}" class="button-secondary" style="display:inline-block;background:#1a365d;color:#ffffff;padding:14px 32px;text-decoration:none;border-radius:6px;font-family:\'Open Sans\',Arial,sans-serif;font-weight:600;font-size:16px;">View in Admin</a>
					</td>
				</tr>
			</table>',
	);

	$content = isset($templates[$template]) ? $templates[$template] : '';

	// Replace all variables
	foreach ($vars as $key => $value) {
		$content = str_replace('{{' . $key . '}}', esc_html($value), $content);
	}

	return $content;
}

/**
 * Check if client wants specific notification type
 *
 * @since 1.0.0
 * @param int    $client_id Client user ID
 * @param string $type      Notification type
 * @return bool             True if client wants notification
 */
function mydefenselaw_portal_client_wants_notification($client_id, $type) {
	if (!function_exists('get_field')) {
		return true; // Default to sending if ACF not available
	}

	$preferences = get_field('notification_preferences', 'user_' . $client_id);

	if (empty($preferences)) {
		return true; // Default to all notifications
	}

	return in_array($type, $preferences);
}

// ==========================================
// WP CRON FOR COURT REMINDERS
// ==========================================

/**
 * Schedule court date reminders
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_schedule_reminders() {
	if (!wp_next_scheduled('mydefenselaw_portal_check_court_dates')) {
		wp_schedule_event(time(), 'daily', 'mydefenselaw_portal_check_court_dates');
	}
}
add_action('wp', 'mydefenselaw_portal_schedule_reminders');

/**
 * Check for upcoming court dates and send reminders
 * Sends at 7 days and 1 day before
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_check_court_dates() {
	$reminder_days = array(7, 1);

	foreach ($reminder_days as $days) {
		$target_date = date('Ymd', strtotime('+' . $days . ' days'));

		$cases = get_posts(array(
			'post_type' => 'client_case',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => '_next_court_date',
					'value' => $target_date,
					'compare' => '=',
				),
			),
		));

		foreach ($cases as $case) {
			// Check if reminder already sent for this case and days
			$reminder_key = '_court_reminder_sent_' . $days;
			$reminder_sent = get_post_meta($case->ID, $reminder_key, true);

			if (!$reminder_sent) {
				mydefenselaw_portal_notify_court_reminder($case->ID, $days);
				update_post_meta($case->ID, $reminder_key, current_time('mysql'));
			}
		}
	}
}
add_action('mydefenselaw_portal_check_court_dates', 'mydefenselaw_portal_check_court_dates');

// ==========================================
// HOOK REGISTRATION
// ==========================================

/**
 * Handle message save and trigger notifications
 *
 * @since 1.0.0
 * @param int $post_id Post ID
 */
function mydefenselaw_portal_handle_message_save($post_id) {
	if (get_post_type($post_id) !== 'client_message') {
		return;
	}

	// Check if this is a new message (not update)
	if (get_post_meta($post_id, '_notification_sent', true)) {
		return;
	}

	// Send appropriate notifications based on message type
	mydefenselaw_portal_notify_new_message($post_id);
	mydefenselaw_portal_notify_staff_new_message($post_id);

	// Mark notification as sent
	update_post_meta($post_id, '_notification_sent', '1');
}

/**
 * Handle document save and trigger notifications
 *
 * @since 1.0.0
 * @param int $post_id Post ID
 */
function mydefenselaw_portal_handle_document_save($post_id) {
	if (get_post_type($post_id) !== 'client_document') {
		return;
	}

	// Check if this is a new document (not update)
	if (get_post_meta($post_id, '_notification_sent', true)) {
		return;
	}

	// Send appropriate notifications based on document type
	mydefenselaw_portal_notify_document_shared($post_id);
	mydefenselaw_portal_notify_staff_document_upload($post_id);

	// Mark notification as sent
	update_post_meta($post_id, '_notification_sent', '1');
}

/**
 * Handle case status change and trigger notification
 *
 * @since 1.0.0
 * @param int    $object_id  Object ID
 * @param array  $terms      Term IDs
 * @param array  $tt_ids     Term taxonomy IDs
 * @param string $taxonomy   Taxonomy slug
 * @param bool   $append     Whether to append terms
 * @param array  $old_tt_ids Old term taxonomy IDs
 */
function mydefenselaw_portal_handle_case_status_change($object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids) {
	// Only proceed for case_status taxonomy
	if ($taxonomy !== 'case_status') {
		return;
	}

	// Only proceed if status actually changed
	if ($tt_ids === $old_tt_ids) {
		return;
	}

	// Get old and new status terms
	$old_terms = array();
	foreach ($old_tt_ids as $tt_id) {
		$term = get_term_by('term_taxonomy_id', $tt_id, 'case_status');
		if ($term) {
			$old_terms[] = $term->slug;
		}
	}

	$new_terms = array();
	foreach ($tt_ids as $tt_id) {
		$term = get_term_by('term_taxonomy_id', $tt_id, 'case_status');
		if ($term) {
			$new_terms[] = $term->slug;
		}
	}

	$old_status = !empty($old_terms) ? $old_terms[0] : 'unknown';
	$new_status = !empty($new_terms) ? $new_terms[0] : 'unknown';

	// Send notification
	mydefenselaw_portal_notify_case_update($object_id, $old_status, $new_status);
}
