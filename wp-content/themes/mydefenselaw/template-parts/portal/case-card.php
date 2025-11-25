<?php
/**
 * Template Part: Case Card
 * Card component for displaying a case in grid/list views
 *
 * Expected variable: $case (WP_Post object)
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

// Ensure we have a case object
if (!isset($case) || !is_object($case)) {
	return;
}

// Get case metadata
$case_id = $case->ID;
$case_number = get_post_meta($case_id, '_case_number', true) ?: sprintf('#%d', $case_id);
$case_status = get_post_meta($case_id, '_case_status', true) ?: 'pending';
$case_type = get_post_meta($case_id, '_case_type', true) ?: __('General', 'mydefenselaw');
$assigned_attorney_id = get_post_meta($case_id, '_assigned_attorney', true);
$next_court_date = get_post_meta($case_id, '_next_court_date', true);
$case_description = get_the_excerpt($case);

// Get attorney info
$attorney_name = __('Not Assigned', 'mydefenselaw');
$attorney_photo = '';
if ($assigned_attorney_id) {
	$attorney = get_userdata($assigned_attorney_id);
	if ($attorney) {
		$attorney_name = $attorney->display_name;
		$attorney_photo = get_avatar_url($assigned_attorney_id, array('size' => 48));
	}
}

// Status badge helper
$status_class = sanitize_html_class($case_status);
$status_label = ucwords(str_replace('-', ' ', $case_status));

// Check if court date is urgent (within 7 days)
$is_urgent = false;
if ($next_court_date) {
	$days_until = floor((strtotime($next_court_date) - time()) / (60 * 60 * 24));
	$is_urgent = ($days_until >= 0 && $days_until <= 7);
}
?>

<div class="case-card portal-case-card status-<?php echo esc_attr($status_class); ?> animate-fade-in"
     data-case-id="<?php echo esc_attr($case_id); ?>"
     data-status="<?php echo esc_attr($case_status); ?>"
     data-type="<?php echo esc_attr($case_type); ?>">

	<div class="case-card-header">
		<div class="case-card-title-group">
			<h3 class="case-number"><?php echo esc_html($case_number); ?></h3>
			<p class="case-title"><?php echo esc_html($case->post_title); ?></p>
		</div>
		<span class="case-status-badge <?php echo esc_attr($status_class); ?>">
			<?php echo esc_html($status_label); ?>
		</span>
	</div>

	<div class="case-details">
		<!-- Case Type -->
		<div class="case-detail-item">
			<i class="fas fa-tag"></i>
			<span><?php echo esc_html($case_type); ?></span>
		</div>

		<!-- Assigned Attorney -->
		<div class="case-detail-item">
			<i class="fas fa-user-tie"></i>
			<span class="case-attorney">
				<?php if ($attorney_photo) : ?>
					<img src="<?php echo esc_url($attorney_photo); ?>"
					     alt="<?php echo esc_attr($attorney_name); ?>"
					     class="attorney-avatar"
					     style="width: 24px; height: 24px; border-radius: 50%; vertical-align: middle; margin-right: 0.5rem;">
				<?php endif; ?>
				<?php echo esc_html($attorney_name); ?>
			</span>
		</div>

		<!-- Next Court Date -->
		<?php if ($next_court_date) : ?>
			<div class="case-detail-item">
				<i class="fas fa-calendar-alt"></i>
				<span class="case-court-date <?php echo $is_urgent ? 'urgent' : ''; ?>">
					<?php echo esc_html(date_i18n(get_option('date_format'), strtotime($next_court_date))); ?>
					<?php if ($is_urgent) : ?>
						<i class="fas fa-exclamation-circle" style="color: var(--color-red); margin-left: 0.25rem;" title="<?php esc_attr_e('Upcoming court date', 'mydefenselaw'); ?>"></i>
					<?php endif; ?>
				</span>
			</div>
		<?php else : ?>
			<div class="case-detail-item">
				<i class="fas fa-calendar-alt"></i>
				<span class="case-court-date" style="color: var(--color-text-light);">
					<?php esc_html_e('No date scheduled', 'mydefenselaw'); ?>
				</span>
			</div>
		<?php endif; ?>

		<!-- Case Description -->
		<?php if ($case_description) : ?>
			<div class="case-description" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--color-border); color: var(--color-text-light); font-size: 0.9rem;">
				<?php echo esc_html(wp_trim_words($case_description, 20)); ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="case-actions">
		<a href="<?php echo esc_url(home_url('/portal/cases/?case_id=' . $case_id)); ?>"
		   class="case-action-btn portal-btn-primary">
			<i class="fas fa-eye"></i>
			<?php esc_html_e('View Details', 'mydefenselaw'); ?>
		</a>
		<a href="<?php echo esc_url(home_url('/portal/messages/?case_id=' . $case_id)); ?>"
		   class="case-action-btn portal-btn-secondary">
			<i class="fas fa-envelope"></i>
			<?php esc_html_e('Message', 'mydefenselaw'); ?>
		</a>
	</div>
</div>
