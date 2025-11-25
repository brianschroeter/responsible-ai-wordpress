<?php
/**
 * Template Part: Dashboard Stats
 * Stats grid showing case overview and activity metrics
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

$user_id = get_current_user_id();

// Get user stats (with fallback defaults)
$active_cases = 0;
$total_documents = 0;
$unread_messages = 0;
$next_court_date = null;

// Retrieve actual stats if functions exist
if (function_exists('mydefenselaw_portal_get_user_stats')) {
	$stats = mydefenselaw_portal_get_user_stats($user_id);
	$active_cases = $stats['active_cases'] ?? 0;
	$total_documents = $stats['total_documents'] ?? 0;
	$unread_messages = $stats['unread_messages'] ?? 0;
	$next_court_date = $stats['next_court_date'] ?? null;
}
?>

<div class="dashboard-stats portal-stats-grid">
	<!-- Active Cases Stat -->
	<a href="<?php echo esc_url(home_url('/portal/cases/')); ?>" class="stat-card portal-stat-card animate-fade-in" style="animation-delay: 0.1s;">
		<div class="stat-card-icon blue">
			<i class="fas fa-briefcase"></i>
		</div>
		<div class="stat-card-content">
			<div class="stat-card-value" data-count="<?php echo esc_attr($active_cases); ?>">
				<?php echo esc_html($active_cases); ?>
			</div>
			<div class="stat-card-label">
				<?php esc_html_e('Active Cases', 'mydefenselaw'); ?>
			</div>
		</div>
	</a>

	<!-- Documents Stat -->
	<a href="<?php echo esc_url(home_url('/portal/documents/')); ?>" class="stat-card portal-stat-card animate-fade-in" style="animation-delay: 0.2s;">
		<div class="stat-card-icon green">
			<i class="fas fa-file-alt"></i>
		</div>
		<div class="stat-card-content">
			<div class="stat-card-value" data-count="<?php echo esc_attr($total_documents); ?>">
				<?php echo esc_html($total_documents); ?>
			</div>
			<div class="stat-card-label">
				<?php esc_html_e('Documents', 'mydefenselaw'); ?>
			</div>
		</div>
	</a>

	<!-- Messages Stat -->
	<a href="<?php echo esc_url(home_url('/portal/messages/')); ?>" class="stat-card portal-stat-card animate-fade-in" style="animation-delay: 0.3s;">
		<div class="stat-card-icon gold">
			<i class="fas fa-envelope"></i>
		</div>
		<div class="stat-card-content">
			<div class="stat-card-value" data-count="<?php echo esc_attr($unread_messages); ?>">
				<?php echo esc_html($unread_messages); ?>
			</div>
			<div class="stat-card-label">
				<?php esc_html_e('Unread Messages', 'mydefenselaw'); ?>
			</div>
			<?php if ($unread_messages > 0) : ?>
				<span class="portal-nav-badge"><?php echo esc_html($unread_messages); ?></span>
			<?php endif; ?>
		</div>
	</a>

	<!-- Next Court Date Stat -->
	<div class="stat-card portal-stat-card animate-fade-in" style="animation-delay: 0.4s;">
		<div class="stat-card-icon red">
			<i class="fas fa-calendar-alt"></i>
		</div>
		<div class="stat-card-content">
			<?php if ($next_court_date) : ?>
				<div class="stat-card-value" style="font-size: 1.25rem;">
					<?php echo esc_html(date_i18n('M j', strtotime($next_court_date))); ?>
				</div>
				<div class="stat-card-label">
					<?php esc_html_e('Next Court Date', 'mydefenselaw'); ?>
				</div>
			<?php else : ?>
				<div class="stat-card-value" style="font-size: 1.25rem;">
					—
				</div>
				<div class="stat-card-label">
					<?php esc_html_e('No Upcoming Dates', 'mydefenselaw'); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<script>
// Animated counter effect
document.addEventListener('DOMContentLoaded', function() {
	const counters = document.querySelectorAll('.stat-card-value[data-count]');

	counters.forEach(counter => {
		const target = parseInt(counter.getAttribute('data-count'));
		const duration = 1000;
		const increment = target / (duration / 16);
		let current = 0;

		const updateCounter = () => {
			current += increment;
			if (current < target) {
				counter.textContent = Math.floor(current);
				requestAnimationFrame(updateCounter);
			} else {
				counter.textContent = target;
			}
		};

		// Start animation when element is visible
		const observer = new IntersectionObserver((entries) => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					updateCounter();
					observer.unobserve(entry.target);
				}
			});
		});

		observer.observe(counter.parentElement.parentElement);
	});
});
</script>
