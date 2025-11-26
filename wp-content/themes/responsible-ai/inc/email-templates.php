<?php
/**
 * Email Templates
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get email wrapper with header and footer
 *
 * @param string $content Email body content
 * @param string $title   Email title for preheader
 * @return string Complete HTML email
 */
function responsibleai_email_wrapper($content, $title = '') {
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($title); ?></title>
        <style>
            body { margin: 0; padding: 0; background-color: #0a1628; font-family: 'Roboto', Arial, sans-serif; }
            .email-container { max-width: 600px; margin: 0 auto; background-color: #0f1d32; }
            .email-header { background: linear-gradient(135deg, #0f1d32 0%, #1a2d4a 100%); padding: 40px 30px; text-align: center; }
            .email-logo { max-width: 200px; height: auto; }
            .email-body { padding: 40px 30px; color: #e2e8f0; }
            .email-body h1 { color: #ffffff; font-size: 24px; margin: 0 0 20px; }
            .email-body h2 { color: #2563eb; font-size: 18px; margin: 20px 0 10px; }
            .email-body p { color: #e2e8f0; line-height: 1.6; margin: 0 0 15px; }
            .email-body a { color: #2563eb; text-decoration: none; }
            .email-button { display: inline-block; background-color: #f59e0b; color: #0a1628 !important; padding: 14px 28px; text-decoration: none; border-radius: 6px; font-weight: 600; margin: 20px 0; }
            .email-button:hover { background-color: #d97706; }
            .email-footer { background-color: #060d18; padding: 30px; text-align: center; color: #64748b; font-size: 12px; }
            .email-footer a { color: #2563eb; }
            .info-box { background-color: #1a2d4a; border-left: 4px solid #2563eb; padding: 15px 20px; margin: 20px 0; }
            .data-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            .data-table th { text-align: left; color: #94a3b8; font-size: 12px; text-transform: uppercase; padding: 10px 15px; border-bottom: 1px solid #334155; }
            .data-table td { padding: 12px 15px; color: #e2e8f0; border-bottom: 1px solid #1e293b; }
            .social-links { margin: 20px 0; }
            .social-links a { display: inline-block; margin: 0 10px; color: #64748b; }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="email-header">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.svg'); ?>" alt="Responsible AI" class="email-logo">
            </div>
            <div class="email-body">
                <?php echo $content; ?>
            </div>
            <div class="email-footer">
                <p>&copy; <?php echo date('Y'); ?> Responsible AI Institute. All rights reserved.</p>
                <p>
                    <a href="<?php echo esc_url(home_url('/')); ?>">Website</a> |
                    <a href="<?php echo esc_url(home_url('/privacy/')); ?>">Privacy Policy</a>
                </p>
                <div class="social-links">
                    <a href="https://linkedin.com/company/responsibleai">LinkedIn</a>
                    <a href="https://twitter.com/responsibleai">Twitter</a>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    return ob_get_clean();
}

/**
 * Admin notification email for contact form
 *
 * @param array $data Form submission data
 * @return string HTML email content
 */
function responsibleai_email_contact_admin($data) {
    ob_start();
    ?>
    <h1><?php esc_html_e('New Contact Form Submission', 'responsible-ai'); ?></h1>

    <p><?php esc_html_e('You have received a new message through the website contact form.', 'responsible-ai'); ?></p>

    <table class="data-table">
        <tr>
            <th><?php esc_html_e('Field', 'responsible-ai'); ?></th>
            <th><?php esc_html_e('Value', 'responsible-ai'); ?></th>
        </tr>
        <tr>
            <td><?php esc_html_e('Name', 'responsible-ai'); ?></td>
            <td><?php echo esc_html($data['name']); ?></td>
        </tr>
        <tr>
            <td><?php esc_html_e('Email', 'responsible-ai'); ?></td>
            <td><a href="mailto:<?php echo esc_attr($data['email']); ?>"><?php echo esc_html($data['email']); ?></a></td>
        </tr>
        <?php if (!empty($data['company'])) : ?>
        <tr>
            <td><?php esc_html_e('Company', 'responsible-ai'); ?></td>
            <td><?php echo esc_html($data['company']); ?></td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($data['subject'])) : ?>
        <tr>
            <td><?php esc_html_e('Subject', 'responsible-ai'); ?></td>
            <td><?php echo esc_html($data['subject']); ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td><?php esc_html_e('Source', 'responsible-ai'); ?></td>
            <td><?php echo esc_html(ucfirst(str_replace('_', ' ', $data['source']))); ?></td>
        </tr>
        <tr>
            <td><?php esc_html_e('Date', 'responsible-ai'); ?></td>
            <td><?php echo esc_html($data['date']); ?></td>
        </tr>
        <tr>
            <td><?php esc_html_e('IP Address', 'responsible-ai'); ?></td>
            <td><?php echo esc_html($data['ip']); ?></td>
        </tr>
    </table>

    <div class="info-box">
        <h2><?php esc_html_e('Message', 'responsible-ai'); ?></h2>
        <p><?php echo nl2br(esc_html($data['message'])); ?></p>
    </div>

    <a href="mailto:<?php echo esc_attr($data['email']); ?>" class="email-button">
        <?php esc_html_e('Reply to Message', 'responsible-ai'); ?>
    </a>
    <?php
    $content = ob_get_clean();

    return responsibleai_email_wrapper($content, __('New Contact Form Submission', 'responsible-ai'));
}

/**
 * User confirmation email for contact form
 *
 * @param array $data Form submission data
 * @return string HTML email content
 */
function responsibleai_email_contact_user($data) {
    ob_start();
    ?>
    <h1><?php esc_html_e('Thank You for Contacting Us', 'responsible-ai'); ?></h1>

    <p><?php printf(
        /* translators: %s: user name */
        esc_html__('Dear %s,', 'responsible-ai'),
        esc_html($data['name'])
    ); ?></p>

    <p><?php esc_html_e('Thank you for reaching out to Responsible AI Institute. We have received your message and will respond as soon as possible.', 'responsible-ai'); ?></p>

    <div class="info-box">
        <h2><?php esc_html_e('Your Message', 'responsible-ai'); ?></h2>
        <p><?php echo nl2br(esc_html($data['message'])); ?></p>
    </div>

    <p><?php esc_html_e('In the meantime, explore our resources:', 'responsible-ai'); ?></p>

    <ul>
        <li><a href="<?php echo esc_url(home_url('/raise-pathways/')); ?>"><?php esc_html_e('RAISE Certification Pathways', 'responsible-ai'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/tools-guides/')); ?>"><?php esc_html_e('Tools & Guides', 'responsible-ai'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/community/')); ?>"><?php esc_html_e('Join Our Community', 'responsible-ai'); ?></a></li>
    </ul>

    <p><?php esc_html_e('Best regards,', 'responsible-ai'); ?><br>
    <strong><?php esc_html_e('The Responsible AI Institute Team', 'responsible-ai'); ?></strong></p>
    <?php
    $content = ob_get_clean();

    return responsibleai_email_wrapper($content, __('Thank You for Contacting Responsible AI', 'responsible-ai'));
}
