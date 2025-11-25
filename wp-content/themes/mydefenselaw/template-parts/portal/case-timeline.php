<?php
/**
 * Template Part: Case Timeline
 * Timeline view for a single case showing key dates and activities
 *
 * Expected variable: $case_id (int)
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

// Ensure we have a case ID
if (!isset($case_id) || !$case_id) {
	return;
}

$case = get_post($case_id);
if (!$case) {
	return;
}

// Security check - ensure user has access to this case
$user_id = get_current_user_id();
if (function_exists('mydefenselaw_portal_user_can_access_case')) {
	if (!mydefenselaw_portal_user_can_access_case($user_id, $case_id)) {
		return;
	}
}

// Get timeline events
$timeline_events = array();

// Add case creation event
$timeline_events[] = array(
	'date' => $case->post_date,
	'type' => 'case_opened',
	'title' => __('Case Opened', 'mydefenselaw'),
	'description' => sprintf(__('Case %s was opened and assigned to your attorney.', 'mydefenselaw'), get_post_meta($case_id, '_case_number', true) ?: '#' . $case_id),
	'icon' => 'fa-folder-open',
	'color' => 'blue',
	'expandable' => false,
);

// Get court dates
$court_dates = get_post_meta($case_id, '_court_dates', true);
if ($court_dates && is_array($court_dates)) {
	foreach ($court_dates as $court_date) {
		$timeline_events[] = array(
			'date' => $court_date['date'] ?? '',
			'type' => 'court_date',
			'title' => $court_date['title'] ?? __('Court Hearing', 'mydefenselaw'),
			'description' => $court_date['description'] ?? '',
			'location' => $court_date['location'] ?? '',
			'icon' => 'fa-gavel',
			'color' => 'red',
			'expandable' => !empty($court_date['description']) || !empty($court_date['location']),
		);
	}
}

// Get status changes from activity log
if (function_exists('mydefenselaw_portal_get_case_activities')) {
	$activities = mydefenselaw_portal_get_case_activities($case_id, array('status_change'));
	foreach ($activities as $activity) {
		$timeline_events[] = array(
			'date' => $activity['date'],
			'type' => 'status_change',
			'title' => __('Status Updated', 'mydefenselaw'),
			'description' => sprintf(__('Case status changed to %s', 'mydefenselaw'), $activity['new_status']),
			'icon' => 'fa-info-circle',
			'color' => 'gold',
			'expandable' => false,
		);
	}
}

// Get document activities
if (function_exists('mydefenselaw_portal_get_case_documents')) {
	$documents = mydefenselaw_portal_get_case_documents($case_id);
	foreach ($documents as $doc) {
		$timeline_events[] = array(
			'date' => $doc->post_date,
			'type' => 'document_added',
			'title' => __('Document Added', 'mydefenselaw'),
			'description' => sprintf(__('"%s" was added to your case', 'mydefenselaw'), $doc->post_title),
			'icon' => 'fa-file-alt',
			'color' => 'green',
			'expandable' => false,
			'link' => home_url('/portal/documents/?document_id=' . $doc->ID),
		);
	}
}

// Get message activities
if (function_exists('mydefenselaw_portal_get_case_messages')) {
	$messages = mydefenselaw_portal_get_case_messages($case_id, 10); // Limit to recent 10
	foreach ($messages as $message) {
		$timeline_events[] = array(
			'date' => $message['date'],
			'type' => 'message_sent',
			'title' => __('Message Exchange', 'mydefenselaw'),
			'description' => sprintf(__('Message from %s', 'mydefenselaw'), $message['sender_name']),
			'icon' => 'fa-envelope',
			'color' => 'blue',
			'expandable' => false,
			'link' => home_url('/portal/messages/?thread_id=' . $message['thread_id']),
		);
	}
}

// Sort events by date (newest first)
usort($timeline_events, function($a, $b) {
	return strtotime($b['date']) - strtotime($a['date']);
});

// If no events, show empty state
if (empty($timeline_events)) {
	?>
	<div class="timeline-empty" style="text-align: center; padding: 3rem 1rem; color: var(--color-text-light);">
		<i class="fas fa-clock" style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem;"></i>
		<p><?php esc_html_e('No timeline events yet.', 'mydefenselaw'); ?></p>
	</div>
	<?php
	return;
}
?>

<div class="case-timeline">
	<?php foreach ($timeline_events as $index => $event) :
		$event_type = $event['type'] ?? 'general';
		$event_color = $event['color'] ?? 'blue';
		$event_icon = $event['icon'] ?? 'fa-circle';
		$is_expandable = $event['expandable'] ?? false;
		$has_link = !empty($event['link']);
		?>

		<div class="timeline-item timeline-item-<?php echo esc_attr($event_type); ?> animate-fade-in"
		     style="animation-delay: <?php echo esc_attr($index * 0.05); ?>s;"
		     data-event-type="<?php echo esc_attr($event_type); ?>">

			<!-- Timeline marker -->
			<div class="timeline-marker timeline-marker-<?php echo esc_attr($event_color); ?>">
				<i class="fas <?php echo esc_attr($event_icon); ?>"></i>
			</div>

			<!-- Timeline content -->
			<div class="timeline-content">
				<div class="timeline-header">
					<div class="timeline-date">
						<?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($event['date']))); ?>
					</div>
					<?php if ($is_expandable) : ?>
						<button type="button"
						        class="timeline-expand-btn"
						        aria-label="<?php esc_attr_e('Expand details', 'mydefenselaw'); ?>">
							<i class="fas fa-chevron-down"></i>
						</button>
					<?php endif; ?>
				</div>

				<h4 class="timeline-title">
					<?php if ($has_link) : ?>
						<a href="<?php echo esc_url($event['link']); ?>">
							<?php echo esc_html($event['title']); ?>
							<i class="fas fa-external-link-alt" style="font-size: 0.8em; margin-left: 0.5rem;"></i>
						</a>
					<?php else : ?>
						<?php echo esc_html($event['title']); ?>
					<?php endif; ?>
				</h4>

				<?php if (!empty($event['description'])) : ?>
					<p class="timeline-description">
						<?php echo esc_html($event['description']); ?>
					</p>
				<?php endif; ?>

				<?php if ($is_expandable && !empty($event['location'])) : ?>
					<div class="timeline-details" style="display: none;">
						<div class="timeline-detail-item">
							<i class="fas fa-map-marker-alt"></i>
							<span><?php echo esc_html($event['location']); ?></span>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>

	<?php endforeach; ?>
</div>

<style>
/* Case Timeline Styles */
.case-timeline {
	position: relative;
	padding: 1rem 0;
}

.case-timeline::before {
	content: '';
	position: absolute;
	left: 30px;
	top: 0;
	bottom: 0;
	width: 2px;
	background: var(--color-border);
}

.timeline-item {
	position: relative;
	display: flex;
	gap: 1.5rem;
	margin-bottom: 2rem;
	padding-left: 0;
}

.timeline-marker {
	position: relative;
	z-index: 1;
	width: 60px;
	height: 60px;
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 1.5rem;
	color: white;
	flex-shrink: 0;
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.timeline-marker-blue {
	background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
}

.timeline-marker-green {
	background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.timeline-marker-gold {
	background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
}

.timeline-marker-red {
	background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
}

.timeline-content {
	flex: 1;
	background: white;
	padding: 1.5rem;
	border-radius: 12px;
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
	border-left: 4px solid var(--color-border);
}

.timeline-item-case_opened .timeline-content {
	border-left-color: #2563eb;
}

.timeline-item-court_date .timeline-content {
	border-left-color: #dc2626;
}

.timeline-item-status_change .timeline-content {
	border-left-color: #fbbf24;
}

.timeline-item-document_added .timeline-content {
	border-left-color: #10b981;
}

.timeline-item-message_sent .timeline-content {
	border-left-color: #8b5cf6;
}

.timeline-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 0.75rem;
}

.timeline-date {
	font-size: 0.85rem;
	color: var(--color-text-light);
	font-weight: 600;
}

.timeline-expand-btn {
	background: none;
	border: none;
	color: var(--color-text-light);
	cursor: pointer;
	padding: 0.25rem;
	transition: all 0.3s ease;
}

.timeline-expand-btn:hover {
	color: var(--color-navy);
	transform: scale(1.1);
}

.timeline-expand-btn.expanded i {
	transform: rotate(180deg);
}

.timeline-title {
	font-size: 1.1rem;
	color: var(--color-navy);
	margin-bottom: 0.5rem;
	font-weight: 700;
}

.timeline-title a {
	color: var(--color-navy);
	text-decoration: none;
	transition: color 0.3s ease;
}

.timeline-title a:hover {
	color: var(--color-blue);
}

.timeline-description {
	color: var(--color-text);
	font-size: 0.95rem;
	margin: 0;
	line-height: 1.6;
}

.timeline-details {
	margin-top: 1rem;
	padding-top: 1rem;
	border-top: 1px solid var(--color-border);
}

.timeline-detail-item {
	display: flex;
	align-items: center;
	gap: 0.75rem;
	color: var(--color-text);
	font-size: 0.9rem;
}

.timeline-detail-item i {
	color: var(--color-navy);
	width: 20px;
	text-align: center;
}

.timeline-empty {
	background: white;
	padding: 3rem;
	border-radius: 12px;
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

/* Mobile responsive */
@media (max-width: 768px) {
	.case-timeline::before {
		left: 20px;
	}

	.timeline-marker {
		width: 40px;
		height: 40px;
		font-size: 1rem;
	}

	.timeline-item {
		gap: 1rem;
	}

	.timeline-content {
		padding: 1rem;
	}
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
	// Handle expandable timeline items
	const expandButtons = document.querySelectorAll('.timeline-expand-btn');

	expandButtons.forEach(button => {
		button.addEventListener('click', function() {
			const timelineItem = this.closest('.timeline-item');
			const details = timelineItem.querySelector('.timeline-details');

			if (details) {
				const isExpanded = details.style.display === 'block';

				details.style.display = isExpanded ? 'none' : 'block';
				this.classList.toggle('expanded', !isExpanded);

				// Animate the details
				if (!isExpanded) {
					details.style.animation = 'slideDown 0.3s ease';
				}
			}
		});
	});
});
</script>
