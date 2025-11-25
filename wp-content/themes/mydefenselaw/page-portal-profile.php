<?php
/**
 * Template Name: Portal - Profile
 * User profile and account settings page
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

// Require authentication
if (function_exists('mydefenselaw_portal_require_auth')) {
    mydefenselaw_portal_require_auth();
}

// Get current client
$client = null;
if (function_exists('mydefenselaw_portal_get_current_client')) {
    $client = mydefenselaw_portal_get_current_client();
}

if (!$client) {
    wp_redirect(home_url('/portal/login/'));
    exit;
}

get_header('portal');

$user = wp_get_current_user();
$user_id = $user->ID;

// Get user meta
$first_name = get_user_meta($user_id, 'first_name', true);
$last_name = get_user_meta($user_id, 'last_name', true);
$phone = get_user_meta($user_id, 'phone', true);
$created = $user->user_registered;
$last_login = get_user_meta($user_id, 'last_login', true);

// Get notification preferences
$notification_prefs = get_user_meta($user_id, 'portal_notification_preferences', true);
if (!is_array($notification_prefs)) {
    $notification_prefs = array(
        'email_new_message' => true,
        'email_case_update' => true,
        'email_document_upload' => true,
        'email_court_date_reminder' => true,
    );
}
?>

<!-- Portal Profile Page -->
<div class="portal-content">
    <div class="container">
        <!-- Page Header -->
        <div class="portal-page-header">
            <div class="page-header-content">
                <h1><i class="fas fa-user"></i> <?php esc_html_e('My Profile', 'mydefenselaw'); ?></h1>
                <p><?php esc_html_e('Manage your account information and preferences', 'mydefenselaw'); ?></p>
            </div>
        </div>

        <!-- Profile Layout -->
        <div class="profile-layout">
            <!-- Profile Sidebar -->
            <aside class="profile-sidebar">
                <div class="profile-card">
                    <div class="profile-avatar">
                        <?php echo get_avatar($user_id, 120, '', '', array('class' => 'avatar-large')); ?>
                    </div>
                    <h3><?php echo esc_html($first_name . ' ' . $last_name); ?></h3>
                    <p class="profile-email"><?php echo esc_html($user->user_email); ?></p>
                    <div class="profile-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar-plus"></i>
                            <span><?php printf(esc_html__('Member since %s', 'mydefenselaw'), date('M Y', strtotime($created))); ?></span>
                        </div>
                        <?php if ($last_login) : ?>
                            <div class="meta-item">
                                <i class="fas fa-clock"></i>
                                <span><?php printf(esc_html__('Last login: %s', 'mydefenselaw'), date('M d, Y', strtotime($last_login))); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="profile-menu">
                    <button class="profile-menu-item active" data-section="personal">
                        <i class="fas fa-user-edit"></i>
                        <?php esc_html_e('Personal Information', 'mydefenselaw'); ?>
                    </button>
                    <button class="profile-menu-item" data-section="security">
                        <i class="fas fa-lock"></i>
                        <?php esc_html_e('Security', 'mydefenselaw'); ?>
                    </button>
                    <button class="profile-menu-item" data-section="notifications">
                        <i class="fas fa-bell"></i>
                        <?php esc_html_e('Notifications', 'mydefenselaw'); ?>
                    </button>
                    <button class="profile-menu-item" data-section="account">
                        <i class="fas fa-cog"></i>
                        <?php esc_html_e('Account Info', 'mydefenselaw'); ?>
                    </button>
                </div>
            </aside>

            <!-- Profile Content -->
            <main class="profile-main">
                <!-- Personal Information Section -->
                <div class="profile-section active" id="sectionPersonal">
                    <div class="section-header">
                        <h2><i class="fas fa-user-edit"></i> <?php esc_html_e('Personal Information', 'mydefenselaw'); ?></h2>
                        <p><?php esc_html_e('Update your basic account information', 'mydefenselaw'); ?></p>
                    </div>

                    <form id="personalInfoForm" class="profile-form">
                        <?php wp_nonce_field('update_profile', 'profile_nonce'); ?>
                        <input type="hidden" name="action" value="mydefenselaw_portal_update_profile">

                        <div class="form-row">
                            <div class="form-group">
                                <label for="firstName">
                                    <?php esc_html_e('First Name', 'mydefenselaw'); ?>
                                    <span class="required">*</span>
                                </label>
                                <input type="text" name="first_name" id="firstName" class="form-control" value="<?php echo esc_attr($first_name); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="lastName">
                                    <?php esc_html_e('Last Name', 'mydefenselaw'); ?>
                                    <span class="required">*</span>
                                </label>
                                <input type="text" name="last_name" id="lastName" class="form-control" value="<?php echo esc_attr($last_name); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="userEmail">
                                <?php esc_html_e('Email Address', 'mydefenselaw'); ?>
                                <span class="badge badge-info"><?php esc_html_e('Read-only', 'mydefenselaw'); ?></span>
                            </label>
                            <input type="email" id="userEmail" class="form-control" value="<?php echo esc_attr($user->user_email); ?>" disabled>
                            <p class="form-help-text"><?php esc_html_e('Contact us to change your email address', 'mydefenselaw'); ?></p>
                        </div>

                        <div class="form-group">
                            <label for="userPhone">
                                <?php esc_html_e('Phone Number', 'mydefenselaw'); ?>
                            </label>
                            <input type="tel" name="phone" id="userPhone" class="form-control" value="<?php echo esc_attr($phone); ?>" placeholder="(555) 123-4567">
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                <?php esc_html_e('Save Changes', 'mydefenselaw'); ?>
                            </button>
                        </div>

                        <div id="personalInfoResponse" class="form-response"></div>
                    </form>
                </div>

                <!-- Security Section -->
                <div class="profile-section" id="sectionSecurity" style="display: none;">
                    <div class="section-header">
                        <h2><i class="fas fa-lock"></i> <?php esc_html_e('Security', 'mydefenselaw'); ?></h2>
                        <p><?php esc_html_e('Change your password and manage security settings', 'mydefenselaw'); ?></p>
                    </div>

                    <form id="changePasswordForm" class="profile-form">
                        <?php wp_nonce_field('change_password', 'password_nonce'); ?>
                        <input type="hidden" name="action" value="mydefenselaw_portal_change_password">

                        <div class="form-group">
                            <label for="currentPassword">
                                <?php esc_html_e('Current Password', 'mydefenselaw'); ?>
                                <span class="required">*</span>
                            </label>
                            <div class="password-input-wrapper">
                                <input type="password" name="current_password" id="currentPassword" class="form-control" required autocomplete="current-password">
                                <button type="button" class="btn-password-toggle" data-target="currentPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="newPassword">
                                <?php esc_html_e('New Password', 'mydefenselaw'); ?>
                                <span class="required">*</span>
                            </label>
                            <div class="password-input-wrapper">
                                <input type="password" name="new_password" id="newPassword" class="form-control" required autocomplete="new-password" minlength="8">
                                <button type="button" class="btn-password-toggle" data-target="newPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength-meter" id="passwordStrengthMeter"></div>
                            <p class="form-help-text"><?php esc_html_e('Password must be at least 8 characters long', 'mydefenselaw'); ?></p>
                        </div>

                        <div class="form-group">
                            <label for="confirmPassword">
                                <?php esc_html_e('Confirm New Password', 'mydefenselaw'); ?>
                                <span class="required">*</span>
                            </label>
                            <div class="password-input-wrapper">
                                <input type="password" name="confirm_password" id="confirmPassword" class="form-control" required autocomplete="new-password" minlength="8">
                                <button type="button" class="btn-password-toggle" data-target="confirmPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key"></i>
                                <?php esc_html_e('Change Password', 'mydefenselaw'); ?>
                            </button>
                        </div>

                        <div id="passwordResponse" class="form-response"></div>
                    </form>
                </div>

                <!-- Notifications Section -->
                <div class="profile-section" id="sectionNotifications" style="display: none;">
                    <div class="section-header">
                        <h2><i class="fas fa-bell"></i> <?php esc_html_e('Notification Preferences', 'mydefenselaw'); ?></h2>
                        <p><?php esc_html_e('Choose how you want to be notified about updates', 'mydefenselaw'); ?></p>
                    </div>

                    <form id="notificationsForm" class="profile-form">
                        <?php wp_nonce_field('update_notifications', 'notifications_nonce'); ?>
                        <input type="hidden" name="action" value="mydefenselaw_portal_update_notifications">

                        <div class="notification-settings">
                            <h3><?php esc_html_e('Email Notifications', 'mydefenselaw'); ?></h3>

                            <label class="checkbox-label notification-item">
                                <input type="checkbox" name="notifications[email_new_message]" value="1" <?php checked($notification_prefs['email_new_message'] ?? false); ?>>
                                <span class="checkmark"></span>
                                <div class="notification-info">
                                    <strong><?php esc_html_e('New Messages', 'mydefenselaw'); ?></strong>
                                    <p><?php esc_html_e('Receive email notifications when you have new messages from your legal team', 'mydefenselaw'); ?></p>
                                </div>
                            </label>

                            <label class="checkbox-label notification-item">
                                <input type="checkbox" name="notifications[email_case_update]" value="1" <?php checked($notification_prefs['email_case_update'] ?? false); ?>>
                                <span class="checkmark"></span>
                                <div class="notification-info">
                                    <strong><?php esc_html_e('Case Updates', 'mydefenselaw'); ?></strong>
                                    <p><?php esc_html_e('Get notified about important updates to your cases', 'mydefenselaw'); ?></p>
                                </div>
                            </label>

                            <label class="checkbox-label notification-item">
                                <input type="checkbox" name="notifications[email_document_upload]" value="1" <?php checked($notification_prefs['email_document_upload'] ?? false); ?>>
                                <span class="checkmark"></span>
                                <div class="notification-info">
                                    <strong><?php esc_html_e('New Documents', 'mydefenselaw'); ?></strong>
                                    <p><?php esc_html_e('Be notified when new documents are added to your cases', 'mydefenselaw'); ?></p>
                                </div>
                            </label>

                            <label class="checkbox-label notification-item">
                                <input type="checkbox" name="notifications[email_court_date_reminder]" value="1" <?php checked($notification_prefs['email_court_date_reminder'] ?? false); ?>>
                                <span class="checkmark"></span>
                                <div class="notification-info">
                                    <strong><?php esc_html_e('Court Date Reminders', 'mydefenselaw'); ?></strong>
                                    <p><?php esc_html_e('Receive reminders about upcoming court dates', 'mydefenselaw'); ?></p>
                                </div>
                            </label>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                <?php esc_html_e('Save Preferences', 'mydefenselaw'); ?>
                            </button>
                        </div>

                        <div id="notificationsResponse" class="form-response"></div>
                    </form>
                </div>

                <!-- Account Info Section -->
                <div class="profile-section" id="sectionAccount" style="display: none;">
                    <div class="section-header">
                        <h2><i class="fas fa-cog"></i> <?php esc_html_e('Account Information', 'mydefenselaw'); ?></h2>
                        <p><?php esc_html_e('View your account details and activity', 'mydefenselaw'); ?></p>
                    </div>

                    <div class="account-info-grid">
                        <div class="info-card">
                            <i class="fas fa-id-card"></i>
                            <h3><?php esc_html_e('Account ID', 'mydefenselaw'); ?></h3>
                            <p class="info-value"><?php echo esc_html($user_id); ?></p>
                        </div>

                        <div class="info-card">
                            <i class="fas fa-calendar-plus"></i>
                            <h3><?php esc_html_e('Account Created', 'mydefenselaw'); ?></h3>
                            <p class="info-value"><?php echo esc_html(date('F d, Y', strtotime($created))); ?></p>
                        </div>

                        <?php if ($last_login) : ?>
                            <div class="info-card">
                                <i class="fas fa-sign-in-alt"></i>
                                <h3><?php esc_html_e('Last Login', 'mydefenselaw'); ?></h3>
                                <p class="info-value"><?php echo esc_html(date('F d, Y g:i A', strtotime($last_login))); ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="info-card">
                            <i class="fas fa-user-tag"></i>
                            <h3><?php esc_html_e('Account Type', 'mydefenselaw'); ?></h3>
                            <p class="info-value"><?php esc_html_e('Client Portal Access', 'mydefenselaw'); ?></p>
                        </div>
                    </div>

                    <div class="account-actions">
                        <h3><?php esc_html_e('Need Help?', 'mydefenselaw'); ?></h3>
                        <p><?php esc_html_e('If you need assistance with your account or have questions about the portal, please contact us.', 'mydefenselaw'); ?></p>
                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9]/', '', get_theme_mod('contact_phone', '888.444.0253'))); ?>" class="btn btn-primary">
                            <i class="fas fa-phone"></i>
                            <?php printf(esc_html__('Call %s', 'mydefenselaw'), get_theme_mod('contact_phone', '888.444.0253')); ?>
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<script>
// Profile page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Section navigation
    document.querySelectorAll('.profile-menu-item').forEach(item => {
        item.addEventListener('click', function() {
            // Update active menu item
            document.querySelectorAll('.profile-menu-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');

            // Show corresponding section
            const section = this.dataset.section;
            document.querySelectorAll('.profile-section').forEach(s => {
                s.style.display = 'none';
                s.classList.remove('active');
            });

            const targetSection = document.getElementById('section' + section.charAt(0).toUpperCase() + section.slice(1));
            if (targetSection) {
                targetSection.style.display = 'block';
                targetSection.classList.add('active');
            }
        });
    });

    // Password visibility toggle
    document.querySelectorAll('.btn-password-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        });
    });

    // Password strength meter
    const newPasswordInput = document.getElementById('newPassword');
    const strengthMeter = document.getElementById('passwordStrengthMeter');

    if (newPasswordInput && strengthMeter) {
        newPasswordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;

            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z\d]/.test(password)) strength++;

            const labels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
            const colors = ['#dc2626', '#f59e0b', '#eab308', '#22c55e', '#10b981'];

            strengthMeter.innerHTML = `
                <div class="strength-bar" style="width: ${strength * 20}%; background-color: ${colors[strength - 1] || '#dc2626'};"></div>
                <span class="strength-label">${labels[strength - 1] || 'Very Weak'}</span>
            `;
        });
    }

    // Form submissions
    const forms = {
        'personalInfoForm': 'personalInfoResponse',
        'changePasswordForm': 'passwordResponse',
        'notificationsForm': 'notificationsResponse'
    };

    Object.keys(forms).forEach(formId => {
        const form = document.getElementById(formId);
        const responseDiv = document.getElementById(forms[formId]);

        if (form && responseDiv) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?php esc_html_e('Saving...', 'mydefenselaw'); ?>';
                submitBtn.disabled = true;

                const formData = new FormData(this);

                fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    responseDiv.innerHTML = data.data.message;
                    responseDiv.className = 'form-response ' + (data.success ? 'success' : 'error');

                    if (data.success && formId === 'changePasswordForm') {
                        form.reset();
                    }

                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;

                    responseDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                })
                .catch(error => {
                    responseDiv.innerHTML = '<?php esc_html_e('An error occurred. Please try again.', 'mydefenselaw'); ?>';
                    responseDiv.className = 'form-response error';
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            });
        }
    });
});
</script>

<?php
get_footer('portal');
?>
