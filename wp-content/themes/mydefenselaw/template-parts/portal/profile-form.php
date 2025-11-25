<?php
/**
 * Template Part: Profile Form
 * Profile editing form with sections for personal info, preferences, and password
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

$user_id = get_current_user_id();
$user = get_userdata($user_id);

if (!$user) {
	return;
}

// Get user meta
$first_name = get_user_meta($user_id, 'first_name', true);
$last_name = get_user_meta($user_id, 'last_name', true);
$phone = get_user_meta($user_id, 'phone', true);
$preferred_contact = get_user_meta($user_id, 'preferred_contact_method', true) ?: 'email';
$best_time = get_user_meta($user_id, 'best_time_to_call', true) ?: 'morning';

// Notification preferences
$email_case_updates = get_user_meta($user_id, 'notify_case_updates', true) !== '0';
$email_new_documents = get_user_meta($user_id, 'notify_new_documents', true) !== '0';
$email_new_messages = get_user_meta($user_id, 'notify_new_messages', true) !== '0';
$email_court_reminders = get_user_meta($user_id, 'notify_court_reminders', true) !== '0';
?>

<div class="profile-container">
	<!-- Personal Information Section -->
	<div class="profile-section">
		<h3><?php esc_html_e('Personal Information', 'mydefenselaw'); ?></h3>

		<form id="profilePersonalForm" class="profile-form">
			<?php wp_nonce_field('update_profile', 'profile_nonce'); ?>
			<input type="hidden" name="action" value="mydefenselaw_portal_update_profile">
			<input type="hidden" name="section" value="personal">

			<div class="profile-field">
				<label for="firstName" class="profile-field-label">
					<?php esc_html_e('First Name', 'mydefenselaw'); ?>
					<span style="color: var(--color-red);">*</span>
				</label>
				<input type="text"
				       id="firstName"
				       name="first_name"
				       class="profile-field-value portal-input"
				       value="<?php echo esc_attr($first_name); ?>"
				       required>
			</div>

			<div class="profile-field">
				<label for="lastName" class="profile-field-label">
					<?php esc_html_e('Last Name', 'mydefenselaw'); ?>
					<span style="color: var(--color-red);">*</span>
				</label>
				<input type="text"
				       id="lastName"
				       name="last_name"
				       class="profile-field-value portal-input"
				       value="<?php echo esc_attr($last_name); ?>"
				       required>
			</div>

			<div class="profile-field">
				<label for="userEmail" class="profile-field-label">
					<?php esc_html_e('Email Address', 'mydefenselaw'); ?>
				</label>
				<input type="email"
				       id="userEmail"
				       class="profile-field-value portal-input"
				       value="<?php echo esc_attr($user->user_email); ?>"
				       disabled
				       title="<?php esc_attr_e('Email cannot be changed. Contact support to update.', 'mydefenselaw'); ?>">
				<p style="font-size: 0.85rem; color: var(--color-text-light); margin-top: 0.5rem;">
					<?php esc_html_e('To change your email, please contact our office.', 'mydefenselaw'); ?>
				</p>
			</div>

			<div class="profile-field">
				<label for="userPhone" class="profile-field-label">
					<?php esc_html_e('Phone Number', 'mydefenselaw'); ?>
				</label>
				<input type="tel"
				       id="userPhone"
				       name="phone"
				       class="profile-field-value portal-input"
				       value="<?php echo esc_attr($phone); ?>"
				       placeholder="(555) 123-4567">
			</div>

			<div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
				<button type="submit" class="portal-btn portal-btn-primary" id="savePersonalBtn">
					<i class="fas fa-save"></i>
					<?php esc_html_e('Save Changes', 'mydefenselaw'); ?>
				</button>
			</div>

			<div id="personalResponse" class="form-response" style="margin-top: 1rem;"></div>
		</form>
	</div>

	<!-- Contact Preferences Section -->
	<div class="profile-section">
		<h3><?php esc_html_e('Contact Preferences', 'mydefenselaw'); ?></h3>

		<form id="profilePreferencesForm" class="profile-form">
			<?php wp_nonce_field('update_profile', 'preferences_nonce'); ?>
			<input type="hidden" name="action" value="mydefenselaw_portal_update_profile">
			<input type="hidden" name="section" value="preferences">

			<div class="profile-field">
				<label for="preferredContact" class="profile-field-label">
					<?php esc_html_e('Preferred Contact Method', 'mydefenselaw'); ?>
				</label>
				<select id="preferredContact" name="preferred_contact_method" class="profile-field-value portal-select">
					<option value="email" <?php selected($preferred_contact, 'email'); ?>>
						<?php esc_html_e('Email', 'mydefenselaw'); ?>
					</option>
					<option value="phone" <?php selected($preferred_contact, 'phone'); ?>>
						<?php esc_html_e('Phone', 'mydefenselaw'); ?>
					</option>
					<option value="text" <?php selected($preferred_contact, 'text'); ?>>
						<?php esc_html_e('Text Message', 'mydefenselaw'); ?>
					</option>
				</select>
			</div>

			<div class="profile-field">
				<label for="bestTime" class="profile-field-label">
					<?php esc_html_e('Best Time to Call', 'mydefenselaw'); ?>
				</label>
				<select id="bestTime" name="best_time_to_call" class="profile-field-value portal-select">
					<option value="morning" <?php selected($best_time, 'morning'); ?>>
						<?php esc_html_e('Morning (8AM - 12PM)', 'mydefenselaw'); ?>
					</option>
					<option value="afternoon" <?php selected($best_time, 'afternoon'); ?>>
						<?php esc_html_e('Afternoon (12PM - 5PM)', 'mydefenselaw'); ?>
					</option>
					<option value="evening" <?php selected($best_time, 'evening'); ?>>
						<?php esc_html_e('Evening (5PM - 8PM)', 'mydefenselaw'); ?>
					</option>
					<option value="anytime" <?php selected($best_time, 'anytime'); ?>>
						<?php esc_html_e('Anytime', 'mydefenselaw'); ?>
					</option>
				</select>
			</div>

			<div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
				<button type="submit" class="portal-btn portal-btn-primary" id="savePreferencesBtn">
					<i class="fas fa-save"></i>
					<?php esc_html_e('Save Preferences', 'mydefenselaw'); ?>
				</button>
			</div>

			<div id="preferencesResponse" class="form-response" style="margin-top: 1rem;"></div>
		</form>
	</div>

	<!-- Notification Settings Section -->
	<div class="profile-section">
		<h3><?php esc_html_e('Email Notification Settings', 'mydefenselaw'); ?></h3>

		<form id="profileNotificationsForm" class="profile-form">
			<?php wp_nonce_field('update_profile', 'notifications_nonce'); ?>
			<input type="hidden" name="action" value="mydefenselaw_portal_update_profile">
			<input type="hidden" name="section" value="notifications">

			<div class="notification-prefs">
				<label class="notification-checkbox">
					<input type="checkbox"
					       name="notify_case_updates"
					       value="1"
					       <?php checked($email_case_updates, true); ?>>
					<div class="notification-checkbox-label">
						<span class="notification-checkbox-title">
							<?php esc_html_e('Case Updates', 'mydefenselaw'); ?>
						</span>
						<span class="notification-checkbox-desc">
							<?php esc_html_e('Receive emails when there are updates to your cases', 'mydefenselaw'); ?>
						</span>
					</div>
				</label>

				<label class="notification-checkbox">
					<input type="checkbox"
					       name="notify_new_documents"
					       value="1"
					       <?php checked($email_new_documents, true); ?>>
					<div class="notification-checkbox-label">
						<span class="notification-checkbox-title">
							<?php esc_html_e('New Documents', 'mydefenselaw'); ?>
						</span>
						<span class="notification-checkbox-desc">
							<?php esc_html_e('Get notified when new documents are added to your case', 'mydefenselaw'); ?>
						</span>
					</div>
				</label>

				<label class="notification-checkbox">
					<input type="checkbox"
					       name="notify_new_messages"
					       value="1"
					       <?php checked($email_new_messages, true); ?>>
					<div class="notification-checkbox-label">
						<span class="notification-checkbox-title">
							<?php esc_html_e('New Messages', 'mydefenselaw'); ?>
						</span>
						<span class="notification-checkbox-desc">
							<?php esc_html_e('Be alerted when you receive new messages from your attorney', 'mydefenselaw'); ?>
						</span>
					</div>
				</label>

				<label class="notification-checkbox">
					<input type="checkbox"
					       name="notify_court_reminders"
					       value="1"
					       <?php checked($email_court_reminders, true); ?>>
					<div class="notification-checkbox-label">
						<span class="notification-checkbox-title">
							<?php esc_html_e('Court Date Reminders', 'mydefenselaw'); ?>
						</span>
						<span class="notification-checkbox-desc">
							<?php esc_html_e('Receive reminders about upcoming court dates and deadlines', 'mydefenselaw'); ?>
						</span>
					</div>
				</label>
			</div>

			<div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
				<button type="submit" class="portal-btn portal-btn-primary" id="saveNotificationsBtn">
					<i class="fas fa-save"></i>
					<?php esc_html_e('Save Settings', 'mydefenselaw'); ?>
				</button>
			</div>

			<div id="notificationsResponse" class="form-response" style="margin-top: 1rem;"></div>
		</form>
	</div>

	<!-- Password Change Section -->
	<div class="profile-section password-section">
		<h3><?php esc_html_e('Change Password', 'mydefenselaw'); ?></h3>

		<form id="profilePasswordForm" class="profile-form">
			<?php wp_nonce_field('update_password', 'password_nonce'); ?>
			<input type="hidden" name="action" value="mydefenselaw_portal_update_password">

			<div class="profile-field">
				<label for="currentPassword" class="profile-field-label">
					<?php esc_html_e('Current Password', 'mydefenselaw'); ?>
					<span style="color: var(--color-red);">*</span>
				</label>
				<input type="password"
				       id="currentPassword"
				       name="current_password"
				       class="profile-field-value portal-input"
				       required
				       autocomplete="current-password">
			</div>

			<div class="profile-field">
				<label for="newPassword" class="profile-field-label">
					<?php esc_html_e('New Password', 'mydefenselaw'); ?>
					<span style="color: var(--color-red);">*</span>
				</label>
				<input type="password"
				       id="newPassword"
				       name="new_password"
				       class="profile-field-value portal-input"
				       required
				       autocomplete="new-password"
				       minlength="8">
				<div class="password-strength" style="display: none;">
					<div class="password-strength-bar"></div>
				</div>
				<p style="font-size: 0.85rem; color: var(--color-text-light); margin-top: 0.5rem;">
					<?php esc_html_e('Password must be at least 8 characters long', 'mydefenselaw'); ?>
				</p>
			</div>

			<div class="profile-field">
				<label for="confirmPassword" class="profile-field-label">
					<?php esc_html_e('Confirm New Password', 'mydefenselaw'); ?>
					<span style="color: var(--color-red);">*</span>
				</label>
				<input type="password"
				       id="confirmPassword"
				       name="confirm_password"
				       class="profile-field-value portal-input"
				       required
				       autocomplete="new-password"
				       minlength="8">
			</div>

			<div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem;">
				<button type="button" class="portal-btn portal-btn-secondary" id="cancelPasswordBtn">
					<?php esc_html_e('Cancel', 'mydefenselaw'); ?>
				</button>
				<button type="submit" class="portal-btn portal-btn-danger" id="savePasswordBtn">
					<i class="fas fa-lock"></i>
					<?php esc_html_e('Change Password', 'mydefenselaw'); ?>
				</button>
			</div>

			<div id="passwordResponse" class="form-response" style="margin-top: 1rem;"></div>
		</form>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	// Personal Info Form
	const personalForm = document.getElementById('profilePersonalForm');
	if (personalForm) {
		personalForm.addEventListener('submit', handleFormSubmit);
	}

	// Preferences Form
	const preferencesForm = document.getElementById('profilePreferencesForm');
	if (preferencesForm) {
		preferencesForm.addEventListener('submit', handleFormSubmit);
	}

	// Notifications Form
	const notificationsForm = document.getElementById('profileNotificationsForm');
	if (notificationsForm) {
		notificationsForm.addEventListener('submit', handleFormSubmit);
	}

	// Password Form
	const passwordForm = document.getElementById('profilePasswordForm');
	if (passwordForm) {
		passwordForm.addEventListener('submit', handlePasswordSubmit);
	}

	// Password strength indicator
	const newPassword = document.getElementById('newPassword');
	if (newPassword) {
		newPassword.addEventListener('input', updatePasswordStrength);
	}

	// Password confirmation validation
	const confirmPassword = document.getElementById('confirmPassword');
	if (confirmPassword) {
		confirmPassword.addEventListener('input', validatePasswordMatch);
	}

	// Cancel password change
	const cancelPasswordBtn = document.getElementById('cancelPasswordBtn');
	if (cancelPasswordBtn) {
		cancelPasswordBtn.addEventListener('click', function() {
			passwordForm.reset();
			document.querySelector('.password-strength').style.display = 'none';
		});
	}

	// Generic form submission handler
	function handleFormSubmit(e) {
		e.preventDefault();
		const form = e.target;
		const formData = new FormData(form);
		const submitBtn = form.querySelector('[type="submit"]');
		const responseDiv = form.querySelector('.form-response');
		const section = formData.get('section');

		submitBtn.disabled = true;
		submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

		fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
			method: 'POST',
			body: formData
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				responseDiv.innerHTML = '<div class="success"><i class="fas fa-check-circle"></i> ' + data.data.message + '</div>';
				responseDiv.style.display = 'block';
			} else {
				responseDiv.innerHTML = '<div class="error"><i class="fas fa-exclamation-circle"></i> ' + (data.data.message || 'Update failed') + '</div>';
				responseDiv.style.display = 'block';
			}

			submitBtn.disabled = false;
			submitBtn.innerHTML = '<i class="fas fa-save"></i> ' + submitBtn.textContent.replace('...', '').trim();

			setTimeout(() => {
				responseDiv.style.display = 'none';
			}, 5000);
		})
		.catch(error => {
			responseDiv.innerHTML = '<div class="error"><i class="fas fa-exclamation-circle"></i> An error occurred</div>';
			responseDiv.style.display = 'block';
			submitBtn.disabled = false;
		});
	}

	// Password form submission
	function handlePasswordSubmit(e) {
		e.preventDefault();
		const form = e.target;
		const newPass = document.getElementById('newPassword').value;
		const confirmPass = document.getElementById('confirmPassword').value;

		if (newPass !== confirmPass) {
			document.getElementById('passwordResponse').innerHTML = '<div class="error"><i class="fas fa-exclamation-circle"></i> Passwords do not match</div>';
			return;
		}

		handleFormSubmit(e);
	}

	// Password strength checker
	function updatePasswordStrength() {
		const password = this.value;
		const strengthBar = document.querySelector('.password-strength-bar');
		const strengthContainer = document.querySelector('.password-strength');

		if (password.length === 0) {
			strengthContainer.style.display = 'none';
			return;
		}

		strengthContainer.style.display = 'block';

		let strength = 0;
		if (password.length >= 8) strength++;
		if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
		if (password.match(/[0-9]/)) strength++;
		if (password.match(/[^a-zA-Z0-9]/)) strength++;

		strengthBar.className = 'password-strength-bar';
		if (strength <= 1) {
			strengthBar.classList.add('weak');
		} else if (strength <= 2) {
			strengthBar.classList.add('medium');
		} else {
			strengthBar.classList.add('strong');
		}
	}

	// Validate password match
	function validatePasswordMatch() {
		const newPass = document.getElementById('newPassword').value;
		const confirmPass = this.value;
		const responseDiv = document.getElementById('passwordResponse');

		if (confirmPass && newPass !== confirmPass) {
			this.setCustomValidity('Passwords do not match');
		} else {
			this.setCustomValidity('');
		}
	}
});
</script>
