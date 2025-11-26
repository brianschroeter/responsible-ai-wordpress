<?php
/**
 * Client Portal Template Tags
 *
 * Helper functions for use in portal templates.
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/* ==========================================================================
   Dashboard Helpers
   ========================================================================== */

/**
 * Get client statistics for dashboard
 *
 * Returns counts of cases, documents, and unread messages for a client.
 *
 * @param int $user_id User ID to get statistics for
 * @return array Array with keys: cases, documents, unread_messages
 */
function mydefenselaw_portal_get_client_stats($user_id) {
	$stats = array(
		'cases'            => 0,
		'documents'        => 0,
		'unread_messages'  => 0,
	);

	// Get case count
	$case_args = array(
		'post_type'      => 'client_case',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'fields'         => 'ids',
		'meta_query'     => array(
			array(
				'key'     => 'assigned_client',
				'value'   => $user_id,
				'compare' => '=',
			),
		),
	);

	$cases = new WP_Query($case_args);
	$stats['cases'] = $cases->found_posts;
	wp_reset_postdata();

	// Get document count (across all client's cases)
	$case_ids = $cases->posts;
	if (!empty($case_ids)) {
		$doc_args = array(
			'post_type'      => 'client_document',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'     => 'related_case',
					'value'   => $case_ids,
					'compare' => 'IN',
				),
			),
		);

		$documents = new WP_Query($doc_args);
		$stats['documents'] = $documents->found_posts;
		wp_reset_postdata();
	}

	// Get unread message count
	$stats['unread_messages'] = mydefenselaw_portal_get_unread_count($user_id);

	return $stats;
}

/**
 * Get recent activity for client dashboard
 *
 * Returns recent portal_activity posts for the client.
 *
 * @param int $user_id User ID to get activity for
 * @param int $limit   Number of activities to retrieve (default: 5)
 * @return array Array of activity post objects
 */
function mydefenselaw_portal_get_recent_activity($user_id, $limit = 5) {
	$args = array(
		'post_type'      => 'portal_activity',
		'posts_per_page' => $limit,
		'post_status'    => 'publish',
		'meta_query'     => array(
			array(
				'key'     => 'activity_user',
				'value'   => $user_id,
				'compare' => '=',
			),
		),
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	$query = new WP_Query($args);
	$activities = array();

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$post_id = get_the_ID();

			$activities[] = array(
				'id'          => $post_id,
				'title'       => get_the_title(),
				'description' => get_the_content(),
				'type'        => get_field('activity_type', $post_id),
				'timestamp'   => get_the_date('U'),
				'date'        => get_the_date(),
				'time'        => get_the_time(),
				'icon'        => mydefenselaw_portal_get_activity_icon(get_field('activity_type', $post_id)),
			);
		}
		wp_reset_postdata();
	}

	return $activities;
}

/**
 * Get upcoming court dates for client
 *
 * Returns cases with upcoming court dates sorted by date.
 *
 * @param int $user_id User ID to get court dates for
 * @param int $limit   Number of cases to retrieve (default: 5)
 * @return array Array of case objects with court date information
 */
function mydefenselaw_portal_get_upcoming_court_dates($user_id, $limit = 5) {
	$args = array(
		'post_type'      => 'client_case',
		'posts_per_page' => $limit,
		'post_status'    => 'publish',
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'     => 'assigned_client',
				'value'   => $user_id,
				'compare' => '=',
			),
			array(
				'key'     => 'court_date',
				'value'   => current_time('Ymd'),
				'compare' => '>=',
				'type'    => 'DATE',
			),
		),
		'meta_key'       => 'court_date',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
	);

	$query = new WP_Query($args);
	$upcoming = array();

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$post_id = get_the_ID();
			$court_date = get_field('court_date', $post_id);

			$upcoming[] = array(
				'id'          => $post_id,
				'title'       => get_the_title(),
				'case_number' => get_field('case_number', $post_id),
				'status'      => get_field('case_status', $post_id),
				'court_date'  => $court_date,
				'formatted_date' => mydefenselaw_portal_format_court_date($court_date),
				'location'    => get_field('court_location', $post_id),
				'time'        => get_field('court_time', $post_id),
				'permalink'   => get_permalink(),
			);
		}
		wp_reset_postdata();
	}

	return $upcoming;
}

/**
 * Get icon class for activity type
 *
 * @param string $activity_type Activity type identifier
 * @return string Font Awesome icon class
 */
function mydefenselaw_portal_get_activity_icon($activity_type) {
	$icons = array(
		'case_update'      => 'fas fa-briefcase',
		'document_upload'  => 'fas fa-file-upload',
		'message_received' => 'fas fa-envelope',
		'court_date'       => 'fas fa-calendar-alt',
		'payment'          => 'fas fa-dollar-sign',
		'login_success'    => 'fas fa-sign-in-alt',
		'logout'           => 'fas fa-sign-out-alt',
		'profile_update'   => 'fas fa-user-edit',
		'default'          => 'fas fa-info-circle',
	);

	return isset($icons[$activity_type]) ? $icons[$activity_type] : $icons['default'];
}

/* ==========================================================================
   Case Helpers
   ========================================================================== */

/**
 * Get client cases with optional filtering
 *
 * WP_Query wrapper for retrieving client's cases.
 *
 * @param int   $user_id User ID to get cases for
 * @param array $args    Optional WP_Query arguments to override defaults
 * @return WP_Query Query object with cases
 */
function mydefenselaw_portal_get_client_cases($user_id, $args = array()) {
	$defaults = array(
		'post_type'      => 'client_case',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'meta_query'     => array(
			array(
				'key'     => 'assigned_client',
				'value'   => $user_id,
				'compare' => '=',
			),
		),
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	$args = wp_parse_args($args, $defaults);
	return new WP_Query($args);
}

/**
 * Get case status badge HTML
 *
 * Returns styled badge HTML based on case status.
 *
 * @param int $case_id Case post ID
 * @return string HTML badge markup
 */
function mydefenselaw_portal_get_case_status_badge($case_id) {
	$status = get_field('case_status', $case_id);

	if (!$status) {
		$status = 'pending';
	}

	// Define status labels and colors
	$statuses = array(
		'active'      => array(
			'label' => __('Active', 'mydefenselaw'),
			'color' => 'success',
		),
		'pending'     => array(
			'label' => __('Pending', 'mydefenselaw'),
			'color' => 'warning',
		),
		'investigation' => array(
			'label' => __('Under Investigation', 'mydefenselaw'),
			'color' => 'info',
		),
		'litigation'  => array(
			'label' => __('In Litigation', 'mydefenselaw'),
			'color' => 'primary',
		),
		'settled'     => array(
			'label' => __('Settled', 'mydefenselaw'),
			'color' => 'success',
		),
		'closed'      => array(
			'label' => __('Closed', 'mydefenselaw'),
			'color' => 'secondary',
		),
		'on_hold'     => array(
			'label' => __('On Hold', 'mydefenselaw'),
			'color' => 'warning',
		),
	);

	$status_data = isset($statuses[$status]) ? $statuses[$status] : array(
		'label' => ucfirst($status),
		'color' => 'secondary',
	);

	return sprintf(
		'<span class="badge badge-%s">%s</span>',
		esc_attr($status_data['color']),
		esc_html($status_data['label'])
	);
}

/**
 * Get case attorney information
 *
 * Returns the attorney post object assigned to a case.
 *
 * @param int $case_id Case post ID
 * @return WP_Post|false Attorney post object or false if not assigned
 */
function mydefenselaw_portal_get_case_attorney($case_id) {
	$attorney = get_field('assigned_attorney', $case_id);

	if ($attorney && is_object($attorney)) {
		return $attorney;
	}

	return false;
}

/**
 * Format court date with relative time
 *
 * Formats date nicely with relative time information (e.g., "in 3 days").
 *
 * @param string $date Date string in any format strtotime() accepts
 * @return string Formatted date with relative time
 */
function mydefenselaw_portal_format_court_date($date) {
	if (empty($date)) {
		return __('Not scheduled', 'mydefenselaw');
	}

	$timestamp = is_numeric($date) ? $date : strtotime($date);
	$formatted = date_i18n(get_option('date_format'), $timestamp);
	$now = current_time('timestamp');
	$diff = $timestamp - $now;

	// Calculate days difference
	$days_diff = floor($diff / DAY_IN_SECONDS);

	if ($days_diff < 0) {
		// Past date
		$days_ago = abs($days_diff);
		if ($days_ago === 0) {
			$relative = __('Today', 'mydefenselaw');
		} elseif ($days_ago === 1) {
			$relative = __('Yesterday', 'mydefenselaw');
		} else {
			$relative = sprintf(_n('%d day ago', '%d days ago', $days_ago, 'mydefenselaw'), $days_ago);
		}
	} elseif ($days_diff === 0) {
		// Today
		$relative = __('Today', 'mydefenselaw');
	} elseif ($days_diff === 1) {
		// Tomorrow
		$relative = __('Tomorrow', 'mydefenselaw');
	} elseif ($days_diff <= 7) {
		// Within a week
		$relative = sprintf(_n('in %d day', 'in %d days', $days_diff, 'mydefenselaw'), $days_diff);
	} else {
		// More than a week
		$weeks = ceil($days_diff / 7);
		$relative = sprintf(_n('in %d week', 'in %d weeks', $weeks, 'mydefenselaw'), $weeks);
	}

	return sprintf('%s (%s)', $formatted, $relative);
}

/* ==========================================================================
   Document Helpers
   ========================================================================== */

/**
 * Get client documents with optional filtering
 *
 * WP_Query wrapper for retrieving client's documents.
 *
 * @param int   $user_id User ID to get documents for
 * @param array $args    Optional WP_Query arguments to override defaults
 * @return WP_Query Query object with documents
 */
function mydefenselaw_portal_get_client_documents($user_id, $args = array()) {
	// First get client's cases
	$case_ids = array();
	$cases_query = mydefenselaw_portal_get_client_cases($user_id, array('fields' => 'ids'));

	if ($cases_query->have_posts()) {
		$case_ids = $cases_query->posts;
	}
	wp_reset_postdata();

	// If no cases, return empty query
	if (empty($case_ids)) {
		return new WP_Query(array('post__in' => array(0)));
	}

	$defaults = array(
		'post_type'      => 'client_document',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'meta_query'     => array(
			array(
				'key'     => 'related_case',
				'value'   => $case_ids,
				'compare' => 'IN',
			),
		),
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	$args = wp_parse_args($args, $defaults);
	return new WP_Query($args);
}

/**
 * Get document icon based on file type
 *
 * Returns Font Awesome icon class for document file type.
 *
 * @param string $file_type File extension or mime type
 * @return string Font Awesome icon class
 */
function mydefenselaw_portal_get_document_icon($file_type) {
	// Clean up file type
	$file_type = strtolower(trim($file_type));

	// Map file types to icons
	$icon_map = array(
		'pdf'  => 'fas fa-file-pdf',
		'doc'  => 'fas fa-file-word',
		'docx' => 'fas fa-file-word',
		'xls'  => 'fas fa-file-excel',
		'xlsx' => 'fas fa-file-excel',
		'ppt'  => 'fas fa-file-powerpoint',
		'pptx' => 'fas fa-file-powerpoint',
		'jpg'  => 'fas fa-file-image',
		'jpeg' => 'fas fa-file-image',
		'png'  => 'fas fa-file-image',
		'gif'  => 'fas fa-file-image',
		'zip'  => 'fas fa-file-archive',
		'rar'  => 'fas fa-file-archive',
		'txt'  => 'fas fa-file-alt',
		'csv'  => 'fas fa-file-csv',
		'video' => 'fas fa-file-video',
		'audio' => 'fas fa-file-audio',
	);

	// Check for mime type patterns
	if (strpos($file_type, 'image') !== false) {
		return 'fas fa-file-image';
	}
	if (strpos($file_type, 'video') !== false) {
		return 'fas fa-file-video';
	}
	if (strpos($file_type, 'audio') !== false) {
		return 'fas fa-file-audio';
	}

	// Return mapped icon or default
	return isset($icon_map[$file_type]) ? $icon_map[$file_type] : 'fas fa-file';
}

/**
 * Format file size in human readable format
 *
 * Converts bytes to KB, MB, GB with appropriate unit.
 *
 * @param int $bytes File size in bytes
 * @return string Formatted file size
 */
function mydefenselaw_portal_format_file_size($bytes) {
	if ($bytes <= 0) {
		return '0 B';
	}

	$units = array('B', 'KB', 'MB', 'GB', 'TB');
	$power = floor(log($bytes, 1024));
	$power = min($power, count($units) - 1);

	$size = $bytes / pow(1024, $power);
	$unit = $units[$power];

	// Format with appropriate decimals
	if ($power === 0) {
		return sprintf('%d %s', $size, $unit);
	} else {
		return sprintf('%.2f %s', $size, $unit);
	}
}

/**
 * Get folder tree structure for documents
 *
 * Returns hierarchical folder structure for organizing documents.
 *
 * @param int $user_id User ID to get folders for
 * @return array Hierarchical array of folders
 */
function mydefenselaw_portal_get_folder_tree($user_id) {
	// Get all folders for user's cases
	$case_ids = array();
	$cases_query = mydefenselaw_portal_get_client_cases($user_id, array('fields' => 'ids'));

	if ($cases_query->have_posts()) {
		$case_ids = $cases_query->posts;
	}
	wp_reset_postdata();

	if (empty($case_ids)) {
		return array();
	}

	// Get unique folders from documents
	$args = array(
		'post_type'      => 'client_document',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'fields'         => 'ids',
		'meta_query'     => array(
			array(
				'key'     => 'related_case',
				'value'   => $case_ids,
				'compare' => 'IN',
			),
		),
	);

	$query = new WP_Query($args);
	$folders = array();

	if ($query->have_posts()) {
		foreach ($query->posts as $doc_id) {
			$folder = get_field('document_folder', $doc_id);
			if ($folder && !in_array($folder, $folders)) {
				$folders[] = $folder;
			}
		}
	}
	wp_reset_postdata();

	// Build hierarchical structure
	$tree = array();
	foreach ($folders as $folder) {
		$parts = explode('/', trim($folder, '/'));
		$current = &$tree;

		foreach ($parts as $part) {
			if (!isset($current[$part])) {
				$current[$part] = array();
			}
			$current = &$current[$part];
		}
	}

	return $tree;
}

/* ==========================================================================
   Message Helpers
   ========================================================================== */

/**
 * Get client messages with optional filtering
 *
 * WP_Query wrapper for retrieving client's messages.
 *
 * @param int   $user_id User ID to get messages for
 * @param array $args    Optional WP_Query arguments to override defaults
 * @return WP_Query Query object with messages
 */
function mydefenselaw_portal_get_client_messages($user_id, $args = array()) {
	$defaults = array(
		'post_type'      => 'client_message',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'meta_query'     => array(
			array(
				'key'     => 'message_client',
				'value'   => $user_id,
				'compare' => '=',
			),
		),
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	$args = wp_parse_args($args, $defaults);
	return new WP_Query($args);
}

/**
 * Get unread message count for client
 *
 * Returns count of messages marked as unread.
 *
 * @param int $user_id User ID to get unread count for
 * @return int Number of unread messages
 */
function mydefenselaw_portal_get_unread_count($user_id) {
	$args = array(
		'post_type'      => 'client_message',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'fields'         => 'ids',
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'     => 'message_client',
				'value'   => $user_id,
				'compare' => '=',
			),
			array(
				'key'     => 'is_read',
				'value'   => '1',
				'compare' => '!=',
			),
		),
	);

	$query = new WP_Query($args);
	$count = $query->found_posts;
	wp_reset_postdata();

	return $count;
}

/**
 * Get message threads grouped by parent
 *
 * Groups messages into conversation threads.
 *
 * @param int $user_id User ID to get threads for
 * @return array Array of message threads
 */
function mydefenselaw_portal_get_message_threads($user_id) {
	// Get all messages for user
	$messages_query = mydefenselaw_portal_get_client_messages($user_id);
	$threads = array();

	if ($messages_query->have_posts()) {
		while ($messages_query->have_posts()) {
			$messages_query->the_post();
			$post_id = get_the_ID();
			$parent_id = wp_get_post_parent_id($post_id);

			// Use parent ID as thread key, or own ID if no parent
			$thread_id = $parent_id ? $parent_id : $post_id;

			if (!isset($threads[$thread_id])) {
				$threads[$thread_id] = array(
					'id'       => $thread_id,
					'subject'  => get_the_title($thread_id),
					'messages' => array(),
					'unread'   => 0,
					'latest'   => 0,
				);
			}

			$is_read = get_field('is_read', $post_id);
			$timestamp = get_the_date('U');

			// Add message to thread
			$threads[$thread_id]['messages'][] = array(
				'id'        => $post_id,
				'title'     => get_the_title(),
				'content'   => get_the_content(),
				'sender'    => get_field('message_sender', $post_id),
				'timestamp' => $timestamp,
				'is_read'   => $is_read,
				'permalink' => get_permalink(),
			);

			// Update thread metadata
			if (!$is_read) {
				$threads[$thread_id]['unread']++;
			}

			if ($timestamp > $threads[$thread_id]['latest']) {
				$threads[$thread_id]['latest'] = $timestamp;
			}
		}
		wp_reset_postdata();
	}

	// Sort threads by latest message
	uasort($threads, function($a, $b) {
		return $b['latest'] - $a['latest'];
	});

	return $threads;
}

/**
 * Format message timestamp with relative time
 *
 * Formats timestamp for message display (e.g., "2 hours ago").
 *
 * @param int|string $timestamp Unix timestamp or date string
 * @return string Formatted relative time
 */
function mydefenselaw_portal_format_message_time($timestamp) {
	if (!is_numeric($timestamp)) {
		$timestamp = strtotime($timestamp);
	}

	$now = current_time('timestamp');
	$diff = $now - $timestamp;

	// Less than 1 minute
	if ($diff < MINUTE_IN_SECONDS) {
		return __('Just now', 'mydefenselaw');
	}

	// Less than 1 hour
	if ($diff < HOUR_IN_SECONDS) {
		$minutes = floor($diff / MINUTE_IN_SECONDS);
		return sprintf(_n('%d minute ago', '%d minutes ago', $minutes, 'mydefenselaw'), $minutes);
	}

	// Less than 1 day
	if ($diff < DAY_IN_SECONDS) {
		$hours = floor($diff / HOUR_IN_SECONDS);
		return sprintf(_n('%d hour ago', '%d hours ago', $hours, 'mydefenselaw'), $hours);
	}

	// Less than 1 week
	if ($diff < WEEK_IN_SECONDS) {
		$days = floor($diff / DAY_IN_SECONDS);
		return sprintf(_n('%d day ago', '%d days ago', $days, 'mydefenselaw'), $days);
	}

	// Older - show full date
	return date_i18n(get_option('date_format'), $timestamp);
}

/* ==========================================================================
   Profile Helpers
   ========================================================================== */

/**
 * Get client profile information
 *
 * Returns array of client profile data from user meta and ACF fields.
 *
 * @param int $user_id User ID to get profile for
 * @return array Client profile data
 */
function mydefenselaw_portal_get_client_profile($user_id) {
	$user = get_userdata($user_id);

	if (!$user) {
		return array();
	}

	$profile = array(
		'id'           => $user_id,
		'username'     => $user->user_login,
		'email'        => $user->user_email,
		'first_name'   => get_user_meta($user_id, 'first_name', true),
		'last_name'    => get_user_meta($user_id, 'last_name', true),
		'display_name' => $user->display_name,
		'phone'        => get_user_meta($user_id, 'phone', true),
		'address'      => get_user_meta($user_id, 'address', true),
		'city'         => get_user_meta($user_id, 'city', true),
		'state'        => get_user_meta($user_id, 'state', true),
		'zip'          => get_user_meta($user_id, 'zip', true),
		'registered'   => $user->user_registered,
	);

	// Add ACF fields if available
	if (function_exists('get_field')) {
		$profile['client_notes'] = get_field('client_notes', 'user_' . $user_id);
		$profile['preferred_contact'] = get_field('preferred_contact_method', 'user_' . $user_id);
	}

	return $profile;
}

/**
 * Get client notification preferences
 *
 * Returns notification settings for the client.
 *
 * @param int $user_id User ID to get preferences for
 * @return array Notification preferences
 */
function mydefenselaw_portal_get_notification_preferences($user_id) {
	$defaults = array(
		'email_case_updates'    => true,
		'email_new_messages'    => true,
		'email_new_documents'   => true,
		'email_court_reminders' => true,
		'sms_court_reminders'   => false,
		'sms_urgent_updates'    => false,
	);

	// Get saved preferences
	$saved = get_user_meta($user_id, '_portal_notifications', true);

	if (!is_array($saved)) {
		$saved = array();
	}

	return wp_parse_args($saved, $defaults);
}

/* ==========================================================================
   Display Helpers
   ========================================================================== */

/**
 * Render breadcrumb navigation
 *
 * Displays breadcrumb trail for portal navigation.
 *
 * @param array $items Array of breadcrumb items with 'title' and optional 'url'
 */
function mydefenselaw_portal_breadcrumbs($items) {
	if (empty($items) || !is_array($items)) {
		return;
	}

	echo '<nav class="portal-breadcrumbs" aria-label="' . esc_attr__('Breadcrumb', 'mydefenselaw') . '">';
	echo '<ol class="breadcrumb-list">';

	// Always start with portal home
	printf(
		'<li class="breadcrumb-item"><a href="%s"><i class="fas fa-home"></i> %s</a></li>',
		esc_url(home_url('/portal/')),
		esc_html__('Portal Home', 'mydefenselaw')
	);

	$count = count($items);
	$current = 0;

	foreach ($items as $item) {
		$current++;
		$is_last = ($current === $count);

		if ($is_last || empty($item['url'])) {
			// Current page - no link
			printf(
				'<li class="breadcrumb-item active" aria-current="page">%s</li>',
				esc_html($item['title'])
			);
		} else {
			// Link to page
			printf(
				'<li class="breadcrumb-item"><a href="%s">%s</a></li>',
				esc_url($item['url']),
				esc_html($item['title'])
			);
		}
	}

	echo '</ol>';
	echo '</nav>';
}

/**
 * Render empty state UI component
 *
 * Displays empty state with icon, title, description, and optional action.
 *
 * @param string      $icon        Font Awesome icon class
 * @param string      $title       Empty state title
 * @param string      $description Empty state description
 * @param array|null  $action      Optional action button array with 'text' and 'url'
 */
function mydefenselaw_portal_empty_state($icon, $title, $description, $action = null) {
	?>
	<div class="portal-empty-state">
		<div class="empty-state-icon">
			<i class="<?php echo esc_attr($icon); ?>"></i>
		</div>
		<h3 class="empty-state-title"><?php echo esc_html($title); ?></h3>
		<p class="empty-state-description"><?php echo esc_html($description); ?></p>
		<?php if ($action && isset($action['text']) && isset($action['url'])) : ?>
			<a href="<?php echo esc_url($action['url']); ?>" class="btn btn-primary empty-state-action">
				<?php echo esc_html($action['text']); ?>
			</a>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Get client avatar with fallback to initials
 *
 * Returns avatar HTML with fallback to initials circle if no avatar exists.
 *
 * @param int $user_id User ID to get avatar for
 * @param int $size    Avatar size in pixels (default: 48)
 * @return string Avatar HTML markup
 */
function mydefenselaw_portal_avatar($user_id, $size = 48) {
	$user = get_userdata($user_id);

	if (!$user) {
		return '';
	}

	// Try to get Gravatar
	$avatar = get_avatar($user_id, $size, '', '', array('class' => 'portal-avatar'));

	// Check if default avatar (no custom gravatar)
	$has_gravatar = validate_gravatar($user->user_email);

	if (!$has_gravatar) {
		// Generate initials
		$first_name = get_user_meta($user_id, 'first_name', true);
		$last_name = get_user_meta($user_id, 'last_name', true);

		$initials = '';
		if ($first_name) {
			$initials .= strtoupper(substr($first_name, 0, 1));
		}
		if ($last_name) {
			$initials .= strtoupper(substr($last_name, 0, 1));
		}

		// Fallback to username initial
		if (empty($initials)) {
			$initials = strtoupper(substr($user->user_login, 0, 1));
		}

		// Generate color based on user ID
		$hue = ($user_id * 137.508) % 360; // Golden angle approximation
		$color = "hsl({$hue}, 50%, 50%)";

		$avatar = sprintf(
			'<div class="portal-avatar portal-avatar-initials" style="width:%1$dpx;height:%1$dpx;background-color:%2$s;"><span>%3$s</span></div>',
			absint($size),
			esc_attr($color),
			esc_html($initials)
		);
	}

	return $avatar;
}

/**
 * Validate if email has a Gravatar
 *
 * Helper function to check if an email has a custom Gravatar.
 *
 * @param string $email Email address to check
 * @return bool True if has Gravatar, false otherwise
 */
function validate_gravatar($email) {
	$hash = md5(strtolower(trim($email)));
	$uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';
	$headers = @get_headers($uri);

	if (!preg_match('|200|', $headers[0])) {
		return false;
	}

	return true;
}

/* ==========================================================================
   Utility Helpers
   ========================================================================== */

/**
 * Check if user has unread notifications
 *
 * @param int $user_id User ID to check
 * @return bool True if has unread notifications, false otherwise
 */
function mydefenselaw_portal_has_unread_notifications($user_id) {
	return mydefenselaw_portal_get_unread_count($user_id) > 0;
}

/**
 * Get portal page URL
 *
 * Returns URL for a specific portal page.
 *
 * @param string $page Portal page slug (dashboard, cases, documents, messages, profile)
 * @return string Page URL
 */
function mydefenselaw_portal_get_page_url($page) {
	$base_url = home_url('/portal/');

	$pages = array(
		'dashboard' => $base_url,
		'cases'     => $base_url . 'cases/',
		'documents' => $base_url . 'documents/',
		'messages'  => $base_url . 'messages/',
		'profile'   => $base_url . 'profile/',
	);

	return isset($pages[$page]) ? $pages[$page] : $base_url;
}

/**
 * Truncate text to specified length
 *
 * @param string $text   Text to truncate
 * @param int    $length Maximum length (default: 100)
 * @param string $suffix Suffix to append if truncated (default: '...')
 * @return string Truncated text
 */
function mydefenselaw_portal_truncate($text, $length = 100, $suffix = '...') {
	if (mb_strlen($text) <= $length) {
		return $text;
	}

	return mb_substr($text, 0, $length) . $suffix;
}
