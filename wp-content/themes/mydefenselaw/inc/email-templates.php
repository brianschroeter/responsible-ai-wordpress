<?php
/**
 * Email Templates
 *
 * Centralized email template system matching frontend design.
 * Provides consistent branding across all outbound emails.
 *
 * Design System:
 * - Primary: #1a365d (Navy)
 * - Accent: #dc2626 (Red)
 * - Gold: #fbbf24
 * - Text: #333333
 * - Text Light: #64748b
 * - Border: #e2e8f0
 * - Background: #f8fafc
 * - Fonts: Merriweather (headings), Open Sans (body)
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Get the base email wrapper matching frontend design
 *
 * @since 1.0.0
 * @param string $content The email content to wrap
 * @param array  $options Optional settings (show_footer_cta, footer_cta_text, footer_cta_url)
 * @return string Complete HTML email
 */
function mydefenselaw_email_wrapper($content, $options = array()) {
	$defaults = array(
		'show_footer_cta' => true,
		'footer_cta_text' => 'Visit Our Website',
		'footer_cta_url'  => home_url('/'),
	);
	$options = wp_parse_args($options, $defaults);

	$phone = function_exists('mydefenselaw_get_phone') ? mydefenselaw_get_phone() : '888.444.0253';
	$site_name = get_bloginfo('name');
	$logo_url = get_template_directory_uri() . '/assets/images/logo.png';
	$year = date('Y');

	// Check if logo exists, fallback to text
	$logo_path = get_template_directory() . '/assets/images/logo.png';
	$has_logo = file_exists($logo_path);

	$header_content = $has_logo
		? '<img src="' . esc_url($logo_url) . '" alt="' . esc_attr($site_name) . '" style="max-width:200px;height:auto;">'
		: '<h1 style="color:#ffffff;margin:0;font-family:\'Merriweather\',Georgia,serif;font-size:28px;font-weight:700;">Defense Lawyers, P.A.</h1>';

	$footer_cta = '';
	if ($options['show_footer_cta'] && !empty($options['footer_cta_url'])) {
		$footer_cta = '
		<tr>
			<td style="padding:0 40px 30px 40px;text-align:center;">
				<a href="' . esc_url($options['footer_cta_url']) . '" style="display:inline-block;background-color:#1a365d;color:#ffffff;font-family:\'Open Sans\',Arial,sans-serif;font-size:14px;font-weight:600;text-decoration:none;padding:12px 28px;border-radius:6px;">' . esc_html($options['footer_cta_text']) . '</a>
			</td>
		</tr>';
	}

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
		/* Google Fonts */
		@import url(\'https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700;900&family=Open+Sans:wght@400;600;700&display=swap\');

		/* Reset */
		body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
		table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
		img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }

		/* Base Styles */
		body {
			margin: 0 !important;
			padding: 0 !important;
			width: 100% !important;
			background-color: #f4f4f4;
		}

		/* Link Colors */
		a { color: #2563eb; }
		a:hover { color: #1d4ed8; }

		/* Button Hover */
		.button-primary:hover { background-color: #b91c1c !important; }
		.button-secondary:hover { background-color: #152a4a !important; }

		/* Responsive */
		@media screen and (max-width: 600px) {
			.email-container { width: 100% !important; }
			.mobile-padding { padding-left: 20px !important; padding-right: 20px !important; }
			.mobile-stack { display: block !important; width: 100% !important; }
		}
	</style>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f4;font-family:\'Open Sans\',Arial,sans-serif;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;">

	<!-- Preheader (hidden preview text) -->
	<div style="display:none;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;">
		Defense Lawyers, P.A. - Protecting Your Rights, Securing Your Future
	</div>

	<!-- Email Container -->
	<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f4f4f4;">
		<tr>
			<td align="center" style="padding:30px 15px;">

				<!-- Email Body -->
				<table role="presentation" class="email-container" cellpadding="0" cellspacing="0" width="600" style="max-width:600px;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1);">

					<!-- Top Gold Accent Bar -->
					<tr>
						<td style="background-color:#fbbf24;height:4px;font-size:0;line-height:0;">&nbsp;</td>
					</tr>

					<!-- Header -->
					<tr>
						<td style="background:linear-gradient(135deg,#1a365d 0%,#2d4a7c 100%);padding:30px 40px;text-align:center;">
							' . $header_content . '
							<p style="color:#94a3b8;margin:8px 0 0 0;font-family:\'Open Sans\',Arial,sans-serif;font-size:14px;letter-spacing:0.5px;">Protecting Your Rights, Securing Your Future</p>
						</td>
					</tr>

					<!-- Main Content -->
					<tr>
						<td class="mobile-padding" style="padding:40px;">
							' . $content . '
						</td>
					</tr>

					' . $footer_cta . '

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
							<!-- Contact Info -->
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
											Boca Raton, Florida | This is an automated message.
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
				<!-- End Email Body -->

			</td>
		</tr>
	</table>
	<!-- End Email Container -->

</body>
</html>';
}

/**
 * Generate a styled heading for emails
 *
 * @since 1.0.0
 * @param string $text Heading text
 * @param string $size Size: 'h1', 'h2', 'h3'
 * @return string HTML heading
 */
function mydefenselaw_email_heading($text, $size = 'h2') {
	$styles = array(
		'h1' => 'font-family:\'Merriweather\',Georgia,serif;font-size:28px;font-weight:700;color:#1a365d;margin:0 0 20px 0;line-height:1.3;',
		'h2' => 'font-family:\'Merriweather\',Georgia,serif;font-size:24px;font-weight:700;color:#1a365d;margin:0 0 16px 0;line-height:1.3;',
		'h3' => 'font-family:\'Merriweather\',Georgia,serif;font-size:20px;font-weight:600;color:#1a365d;margin:0 0 12px 0;line-height:1.3;',
	);

	$style = isset($styles[$size]) ? $styles[$size] : $styles['h2'];
	return '<' . $size . ' style="' . $style . '">' . esc_html($text) . '</' . $size . '>';
}

/**
 * Generate a styled paragraph for emails
 *
 * @since 1.0.0
 * @param string $text Paragraph text
 * @return string HTML paragraph
 */
function mydefenselaw_email_paragraph($text) {
	return '<p style="font-family:\'Open Sans\',Arial,sans-serif;font-size:16px;line-height:1.7;color:#333333;margin:0 0 16px 0;">' . wp_kses_post($text) . '</p>';
}

/**
 * Generate a styled button for emails
 *
 * @since 1.0.0
 * @param string $text Button text
 * @param string $url  Button URL
 * @param string $type Button type: 'primary' (red), 'secondary' (navy)
 * @return string HTML button
 */
function mydefenselaw_email_button($text, $url, $type = 'primary') {
	$bg_color = $type === 'primary' ? '#dc2626' : '#1a365d';
	$class = $type === 'primary' ? 'button-primary' : 'button-secondary';

	return '
	<table role="presentation" cellpadding="0" cellspacing="0" style="margin:25px 0;">
		<tr>
			<td style="border-radius:6px;background-color:' . $bg_color . ';">
				<a href="' . esc_url($url) . '" class="' . $class . '" style="display:inline-block;background-color:' . $bg_color . ';color:#ffffff;font-family:\'Open Sans\',Arial,sans-serif;font-size:16px;font-weight:600;text-decoration:none;padding:14px 32px;border-radius:6px;">' . esc_html($text) . '</a>
			</td>
		</tr>
	</table>';
}

/**
 * Generate a styled info box for emails
 *
 * @since 1.0.0
 * @param string $content Box content (HTML allowed)
 * @param string $type    Box type: 'info' (blue), 'warning' (gold), 'success' (green), 'urgent' (red)
 * @return string HTML info box
 */
function mydefenselaw_email_info_box($content, $type = 'info') {
	$colors = array(
		'info'    => array('bg' => '#f0f9ff', 'border' => '#2563eb', 'text' => '#1e40af'),
		'warning' => array('bg' => '#fef3c7', 'border' => '#f59e0b', 'text' => '#92400e'),
		'success' => array('bg' => '#d1fae5', 'border' => '#10b981', 'text' => '#065f46'),
		'urgent'  => array('bg' => '#fee2e2', 'border' => '#dc2626', 'text' => '#991b1b'),
	);

	$color = isset($colors[$type]) ? $colors[$type] : $colors['info'];

	return '
	<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:20px 0;">
		<tr>
			<td style="background-color:' . $color['bg'] . ';border-left:4px solid ' . $color['border'] . ';border-radius:0 6px 6px 0;padding:16px 20px;">
				<div style="font-family:\'Open Sans\',Arial,sans-serif;font-size:15px;line-height:1.6;color:' . $color['text'] . ';">
					' . wp_kses_post($content) . '
				</div>
			</td>
		</tr>
	</table>';
}

/**
 * Generate a styled data row for emails
 *
 * @since 1.0.0
 * @param string $label Row label
 * @param string $value Row value
 * @return string HTML table row
 */
function mydefenselaw_email_data_row($label, $value) {
	return '
	<tr>
		<td style="padding:10px 0;border-bottom:1px solid #e2e8f0;font-family:\'Open Sans\',Arial,sans-serif;">
			<strong style="color:#64748b;font-size:13px;text-transform:uppercase;letter-spacing:0.5px;">' . esc_html($label) . '</strong><br>
			<span style="color:#333333;font-size:16px;">' . esc_html($value) . '</span>
		</td>
	</tr>';
}

/**
 * Generate a styled data table for emails
 *
 * @since 1.0.0
 * @param array $rows Array of label => value pairs
 * @return string HTML data table
 */
function mydefenselaw_email_data_table($rows) {
	$html = '<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:20px 0;">';

	foreach ($rows as $label => $value) {
		$html .= mydefenselaw_email_data_row($label, $value);
	}

	$html .= '</table>';
	return $html;
}

/**
 * Generate admin notification email for contact form
 *
 * @since 1.0.0
 * @param array $data Form submission data
 * @return string Complete HTML email
 */
function mydefenselaw_email_contact_admin($data) {
	$is_urgent = !empty($data['is_urgent']);

	$content = '';

	// Urgent banner
	if ($is_urgent) {
		$content .= mydefenselaw_email_info_box(
			'<strong style="font-size:16px;">URGENT REQUEST</strong><br>This client has indicated this is an urgent matter requiring immediate attention.',
			'urgent'
		);
	}

	$content .= mydefenselaw_email_heading('New Consultation Request', 'h1');
	$content .= mydefenselaw_email_paragraph('A potential client has submitted a consultation request through the website.');

	// Contact Information
	$content .= mydefenselaw_email_heading('Contact Information', 'h3');
	$content .= mydefenselaw_email_data_table(array(
		'Name'  => $data['first_name'] . ' ' . $data['last_name'],
		'Phone' => $data['phone'],
		'Email' => $data['email'],
	));

	// Legal Issue
	$content .= mydefenselaw_email_heading('Legal Issue', 'h3');
	$content .= mydefenselaw_email_info_box(
		'<strong>Type:</strong> ' . esc_html($data['legal_issue']),
		'info'
	);

	// Message
	if (!empty($data['message'])) {
		$content .= mydefenselaw_email_heading('Client Message', 'h3');
		$content .= '
		<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:10px 0 20px 0;">
			<tr>
				<td style="background-color:#f8fafc;border-radius:8px;padding:20px;">
					<p style="font-family:\'Open Sans\',Arial,sans-serif;font-size:15px;line-height:1.7;color:#333333;margin:0;white-space:pre-wrap;">' . esc_html($data['message']) . '</p>
				</td>
			</tr>
		</table>';
	}

	// Metadata
	$content .= '
	<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin-top:30px;border-top:1px solid #e2e8f0;padding-top:20px;">
		<tr>
			<td style="font-family:\'Open Sans\',Arial,sans-serif;font-size:12px;color:#94a3b8;">
				<strong>Submitted:</strong> ' . esc_html(current_time('F j, Y \a\t g:i a')) . '<br>
				<strong>Source:</strong> ' . esc_html($data['form_source'] ?? 'Website Contact Form') . '<br>
				<strong>IP Address:</strong> ' . esc_html($data['ip_address'] ?? 'Unknown') . '
			</td>
		</tr>
	</table>';

	return mydefenselaw_email_wrapper($content, array(
		'show_footer_cta' => true,
		'footer_cta_text' => 'View in Admin',
		'footer_cta_url'  => admin_url('edit.php?post_type=form_submission'),
	));
}

/**
 * Generate client auto-response email for contact form
 *
 * @since 1.0.0
 * @param array $data Form submission data
 * @return string Complete HTML email
 */
function mydefenselaw_email_contact_client($data) {
	$is_urgent = !empty($data['is_urgent']);
	$phone = function_exists('mydefenselaw_get_phone') ? mydefenselaw_get_phone() : '888.444.0253';

	$content = '';

	$content .= mydefenselaw_email_heading('Thank You for Contacting Us', 'h1');

	$content .= mydefenselaw_email_paragraph(
		'Dear ' . esc_html($data['first_name']) . ','
	);

	$content .= mydefenselaw_email_paragraph(
		'Thank you for reaching out to Defense Lawyers, P.A. We have received your consultation request and our team will review your information promptly.'
	);

	if ($is_urgent) {
		$content .= mydefenselaw_email_info_box(
			'<strong>We understand this is an urgent matter.</strong><br>Your request has been flagged as a priority and our team will prioritize your case.',
			'warning'
		);
	}

	$content .= mydefenselaw_email_heading('What Happens Next?', 'h3');

	$content .= '
	<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:15px 0 25px 0;">
		<tr>
			<td style="padding:12px 0;border-bottom:1px solid #e2e8f0;">
				<table role="presentation" cellpadding="0" cellspacing="0">
					<tr>
						<td style="width:40px;vertical-align:top;">
							<div style="width:28px;height:28px;background-color:#1a365d;border-radius:50%;text-align:center;line-height:28px;color:#ffffff;font-family:\'Open Sans\',Arial,sans-serif;font-weight:700;font-size:14px;">1</div>
						</td>
						<td style="font-family:\'Open Sans\',Arial,sans-serif;font-size:15px;color:#333333;line-height:1.5;">
							<strong style="color:#1a365d;">Case Review</strong><br>
							<span style="color:#64748b;">Our legal team will review your submission</span>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="padding:12px 0;border-bottom:1px solid #e2e8f0;">
				<table role="presentation" cellpadding="0" cellspacing="0">
					<tr>
						<td style="width:40px;vertical-align:top;">
							<div style="width:28px;height:28px;background-color:#1a365d;border-radius:50%;text-align:center;line-height:28px;color:#ffffff;font-family:\'Open Sans\',Arial,sans-serif;font-weight:700;font-size:14px;">2</div>
						</td>
						<td style="font-family:\'Open Sans\',Arial,sans-serif;font-size:15px;color:#333333;line-height:1.5;">
							<strong style="color:#1a365d;">Personal Contact</strong><br>
							<span style="color:#64748b;">A member of our team will reach out within 24-48 hours</span>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="padding:12px 0;">
				<table role="presentation" cellpadding="0" cellspacing="0">
					<tr>
						<td style="width:40px;vertical-align:top;">
							<div style="width:28px;height:28px;background-color:#1a365d;border-radius:50%;text-align:center;line-height:28px;color:#ffffff;font-family:\'Open Sans\',Arial,sans-serif;font-weight:700;font-size:14px;">3</div>
						</td>
						<td style="font-family:\'Open Sans\',Arial,sans-serif;font-size:15px;color:#333333;line-height:1.5;">
							<strong style="color:#1a365d;">Free Consultation</strong><br>
							<span style="color:#64748b;">We\'ll schedule a time to discuss your case in detail</span>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>';

	$content .= mydefenselaw_email_info_box(
		'<strong>Need Immediate Assistance?</strong><br>If you have an urgent legal matter, please call us directly at <a href="tel:' . esc_attr(preg_replace('/[^0-9]/', '', $phone)) . '" style="color:#1e40af;font-weight:600;">' . esc_html($phone) . '</a>',
		'info'
	);

	$content .= mydefenselaw_email_paragraph(
		'We appreciate you considering Defense Lawyers, P.A. for your legal needs. Our experienced attorneys are committed to providing the highest quality legal representation.'
	);

	$content .= '
	<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin-top:25px;">
		<tr>
			<td style="font-family:\'Open Sans\',Arial,sans-serif;font-size:15px;color:#333333;line-height:1.7;">
				Best regards,<br><br>
				<strong style="color:#1a365d;font-size:16px;">Defense Lawyers, P.A.</strong><br>
				<span style="color:#64748b;">Boca Raton, Florida</span>
			</td>
		</tr>
	</table>';

	return mydefenselaw_email_wrapper($content, array(
		'show_footer_cta' => true,
		'footer_cta_text' => 'Learn More About Our Services',
		'footer_cta_url'  => home_url('/practice-areas/'),
	));
}

/**
 * Send styled HTML email
 *
 * @since 1.0.0
 * @param string $to      Recipient email
 * @param string $subject Email subject
 * @param string $body    HTML body (should be from mydefenselaw_email_wrapper)
 * @param array  $headers Additional headers (optional)
 * @return bool True on success
 */
function mydefenselaw_send_styled_email($to, $subject, $body, $headers = array()) {
	$site_name = get_bloginfo('name');
	$from_email = 'noreply@' . parse_url(home_url(), PHP_URL_HOST);

	$default_headers = array(
		'Content-Type: text/html; charset=UTF-8',
		'From: ' . $site_name . ' <' . $from_email . '>',
	);

	$headers = array_merge($default_headers, $headers);

	return wp_mail($to, $subject, $body, $headers);
}
