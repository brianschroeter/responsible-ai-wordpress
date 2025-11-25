<?php
/**
 * Client Portal Admin Menu
 *
 * Registers admin menu pages for portal management with dashboard, statistics,
 * and navigation for all portal features.
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Register admin menu pages
 */
function mydefenselaw_portal_admin_menu() {
	// Main menu page
	add_menu_page(
		__('Client Portal', 'mydefenselaw'),
		__('Client Portal', 'mydefenselaw'),
		'manage_options',
		'client-portal',
		'mydefenselaw_portal_dashboard_page',
		'dashicons-groups',
		30
	);

	// Dashboard (same as main page)
	add_submenu_page(
		'client-portal',
		__('Dashboard', 'mydefenselaw'),
		__('Dashboard', 'mydefenselaw'),
		'manage_options',
		'client-portal',
		'mydefenselaw_portal_dashboard_page'
	);

	// All Clients
	add_submenu_page(
		'client-portal',
		__('All Clients', 'mydefenselaw'),
		__('All Clients', 'mydefenselaw'),
		'manage_options',
		'client-portal-clients',
		'mydefenselaw_portal_clients_page'
	);

	// Add New Client
	add_submenu_page(
		'client-portal',
		__('Add New Client', 'mydefenselaw'),
		__('Add New Client', 'mydefenselaw'),
		'manage_options',
		'client-portal-add-client',
		'mydefenselaw_portal_add_client_page'
	);

	// Activity Log link (redirects to CPT)
	add_submenu_page(
		'client-portal',
		__('Activity Log', 'mydefenselaw'),
		__('Activity Log', 'mydefenselaw'),
		'manage_options',
		'edit.php?post_type=portal_activity'
	);

	// Portal Settings
	add_submenu_page(
		'client-portal',
		__('Portal Settings', 'mydefenselaw'),
		__('Portal Settings', 'mydefenselaw'),
		'manage_options',
		'client-portal-settings',
		'mydefenselaw_portal_settings_page'
	);
}
add_action('admin_menu', 'mydefenselaw_portal_admin_menu');

/**
 * Dashboard page
 */
function mydefenselaw_portal_dashboard_page() {
	// Check permissions
	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.', 'mydefenselaw'));
	}

	// Get statistics
	$stats = mydefenselaw_portal_get_stats();

	// Get recent activity
	$recent_activity = get_posts(array(
		'post_type'      => 'portal_activity',
		'posts_per_page' => 10,
		'orderby'        => 'date',
		'order'          => 'DESC',
	));

	?>
	<div class="wrap mydefenselaw-portal-dashboard">
		<h1 class="wp-heading-inline">
			<span class="dashicons dashicons-shield" style="font-size: 32px; vertical-align: middle; margin-right: 10px;"></span>
			<?php esc_html_e('Client Portal Dashboard', 'mydefenselaw'); ?>
		</h1>

		<hr class="wp-header-end">

		<!-- Statistics Grid -->
		<div class="portal-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0;">

			<!-- Total Clients -->
			<div class="portal-stat-card" style="background: linear-gradient(135deg, #2271b1 0%, #135e96 100%); color: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
				<div style="display: flex; align-items: center; margin-bottom: 10px;">
					<span class="dashicons dashicons-groups" style="font-size: 36px; margin-right: 15px; opacity: 0.9;"></span>
					<div>
						<div style="font-size: 32px; font-weight: 700; line-height: 1;"><?php echo esc_html($stats['total_clients']); ?></div>
						<div style="font-size: 14px; opacity: 0.9; margin-top: 5px;"><?php esc_html_e('Total Clients', 'mydefenselaw'); ?></div>
					</div>
				</div>
				<a href="<?php echo esc_url(admin_url('admin.php?page=client-portal-clients')); ?>" style="color: white; text-decoration: none; font-size: 13px; opacity: 0.9;">
					<?php esc_html_e('View All →', 'mydefenselaw'); ?>
				</a>
			</div>

			<!-- Active Cases -->
			<div class="portal-stat-card" style="background: linear-gradient(135deg, #00a32a 0%, #008a24 100%); color: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
				<div style="display: flex; align-items: center; margin-bottom: 10px;">
					<span class="dashicons dashicons-portfolio" style="font-size: 36px; margin-right: 15px; opacity: 0.9;"></span>
					<div>
						<div style="font-size: 32px; font-weight: 700; line-height: 1;"><?php echo esc_html($stats['active_cases']); ?></div>
						<div style="font-size: 14px; opacity: 0.9; margin-top: 5px;"><?php esc_html_e('Active Cases', 'mydefenselaw'); ?></div>
					</div>
				</div>
				<a href="<?php echo esc_url(admin_url('edit.php?post_type=client_case')); ?>" style="color: white; text-decoration: none; font-size: 13px; opacity: 0.9;">
					<?php esc_html_e('Manage Cases →', 'mydefenselaw'); ?>
				</a>
			</div>

			<!-- Total Documents -->
			<div class="portal-stat-card" style="background: linear-gradient(135deg, #d63638 0%, #b02a2c 100%); color: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
				<div style="display: flex; align-items: center; margin-bottom: 10px;">
					<span class="dashicons dashicons-media-document" style="font-size: 36px; margin-right: 15px; opacity: 0.9;"></span>
					<div>
						<div style="font-size: 32px; font-weight: 700; line-height: 1;"><?php echo esc_html($stats['total_documents']); ?></div>
						<div style="font-size: 14px; opacity: 0.9; margin-top: 5px;"><?php esc_html_e('Total Documents', 'mydefenselaw'); ?></div>
					</div>
				</div>
				<a href="<?php echo esc_url(admin_url('edit.php?post_type=client_document')); ?>" style="color: white; text-decoration: none; font-size: 13px; opacity: 0.9;">
					<?php esc_html_e('View Documents →', 'mydefenselaw'); ?>
				</a>
			</div>

			<!-- Unread Messages -->
			<div class="portal-stat-card" style="background: linear-gradient(135deg, #f0b849 0%, #d99e2c 100%); color: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
				<div style="display: flex; align-items: center; margin-bottom: 10px;">
					<span class="dashicons dashicons-email" style="font-size: 36px; margin-right: 15px; opacity: 0.9;"></span>
					<div>
						<div style="font-size: 32px; font-weight: 700; line-height: 1;"><?php echo esc_html($stats['unread_messages']); ?></div>
						<div style="font-size: 14px; opacity: 0.9; margin-top: 5px;"><?php esc_html_e('Unread Messages', 'mydefenselaw'); ?></div>
					</div>
				</div>
				<a href="<?php echo esc_url(admin_url('edit.php?post_type=client_message')); ?>" style="color: white; text-decoration: none; font-size: 13px; opacity: 0.9;">
					<?php esc_html_e('View Messages →', 'mydefenselaw'); ?>
				</a>
			</div>
		</div>

		<!-- Two Column Layout -->
		<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 30px;">

			<!-- Recent Activity -->
			<div class="portal-recent-activity" style="background: white; padding: 20px; border: 1px solid #c3c4c7; border-radius: 4px;">
				<h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #e0e0e0;">
					<span class="dashicons dashicons-visibility" style="font-size: 24px; vertical-align: middle; margin-right: 8px;"></span>
					<?php esc_html_e('Recent Activity', 'mydefenselaw'); ?>
				</h2>

				<?php if (!empty($recent_activity)) : ?>
					<div class="activity-list" style="margin-top: 15px;">
						<?php foreach ($recent_activity as $activity) :
							$activity_type = get_post_meta($activity->ID, '_activity_type', true);
							$client_name = get_post_meta($activity->ID, '_client_name', true);
							$user_name = get_post_meta($activity->ID, '_user_name', true);
							$time_ago = human_time_diff(get_the_time('U', $activity), current_time('timestamp')) . ' ago';

							// Icon and color based on activity type
							$icons = array(
								'login' => 'admin-network',
								'document_upload' => 'upload',
								'document_download' => 'download',
								'message_sent' => 'email-alt',
								'case_created' => 'portfolio',
								'case_updated' => 'edit',
								'client_created' => 'admin-users',
							);
							$icon = $icons[$activity_type] ?? 'marker';
						?>
						<div class="activity-item" style="padding: 12px 0; border-bottom: 1px solid #f0f0f0; display: flex; align-items: flex-start; gap: 12px;">
							<span class="dashicons dashicons-<?php echo esc_attr($icon); ?>" style="font-size: 20px; color: #2271b1; margin-top: 2px;"></span>
							<div style="flex: 1;">
								<div style="font-weight: 500; color: #1d2327; margin-bottom: 3px;">
									<?php echo esc_html($activity->post_title); ?>
								</div>
								<div style="font-size: 13px; color: #646970;">
									<?php
									if ($client_name) {
										echo esc_html($client_name);
									}
									if ($user_name) {
										echo ' by ' . esc_html($user_name);
									}
									?>
								</div>
							</div>
							<div style="font-size: 12px; color: #787c82; white-space: nowrap;">
								<?php echo esc_html($time_ago); ?>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
					<a href="<?php echo esc_url(admin_url('edit.php?post_type=portal_activity')); ?>" class="button" style="margin-top: 15px;">
						<?php esc_html_e('View Full Activity Log', 'mydefenselaw'); ?>
					</a>
				<?php else : ?>
					<div style="text-align: center; padding: 40px 20px; color: #787c82;">
						<span class="dashicons dashicons-visibility" style="font-size: 48px; opacity: 0.3;"></span>
						<p><?php esc_html_e('No recent activity', 'mydefenselaw'); ?></p>
					</div>
				<?php endif; ?>
			</div>

			<!-- Quick Actions -->
			<div class="portal-quick-actions" style="background: white; padding: 20px; border: 1px solid #c3c4c7; border-radius: 4px;">
				<h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #e0e0e0;">
					<span class="dashicons dashicons-admin-tools" style="font-size: 24px; vertical-align: middle; margin-right: 8px;"></span>
					<?php esc_html_e('Quick Actions', 'mydefenselaw'); ?>
				</h2>

				<div class="quick-actions-list" style="margin-top: 15px;">
					<a href="<?php echo esc_url(admin_url('admin.php?page=client-portal-add-client')); ?>" class="button button-primary" style="display: block; text-align: center; margin-bottom: 10px; width: 100%;">
						<span class="dashicons dashicons-plus-alt" style="margin-top: 3px;"></span>
						<?php esc_html_e('Create New Client', 'mydefenselaw'); ?>
					</a>
					<a href="<?php echo esc_url(admin_url('post-new.php?post_type=client_case')); ?>" class="button" style="display: block; text-align: center; margin-bottom: 10px; width: 100%;">
						<span class="dashicons dashicons-portfolio" style="margin-top: 3px;"></span>
						<?php esc_html_e('Create New Case', 'mydefenselaw'); ?>
					</a>
					<a href="<?php echo esc_url(admin_url('post-new.php?post_type=client_document')); ?>" class="button" style="display: block; text-align: center; margin-bottom: 10px; width: 100%;">
						<span class="dashicons dashicons-media-document" style="margin-top: 3px;"></span>
						<?php esc_html_e('Upload Document', 'mydefenselaw'); ?>
					</a>
					<a href="<?php echo esc_url(admin_url('post-new.php?post_type=client_message')); ?>" class="button" style="display: block; text-align: center; margin-bottom: 10px; width: 100%;">
						<span class="dashicons dashicons-email-alt" style="margin-top: 3px;"></span>
						<?php esc_html_e('Send Message', 'mydefenselaw'); ?>
					</a>
				</div>

				<hr style="margin: 20px 0; border: none; border-top: 1px solid #e0e0e0;">

				<h3 style="font-size: 14px; margin-bottom: 10px;"><?php esc_html_e('Quick Stats', 'mydefenselaw'); ?></h3>

				<div style="font-size: 13px; color: #646970;">
					<div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
						<span><?php esc_html_e('New this week:', 'mydefenselaw'); ?></span>
						<strong><?php echo esc_html($stats['new_clients_week']); ?> <?php esc_html_e('clients', 'mydefenselaw'); ?></strong>
					</div>
					<div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
						<span><?php esc_html_e('New this month:', 'mydefenselaw'); ?></span>
						<strong><?php echo esc_html($stats['new_clients_month']); ?> <?php esc_html_e('clients', 'mydefenselaw'); ?></strong>
					</div>
					<div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
						<span><?php esc_html_e('Closed cases:', 'mydefenselaw'); ?></span>
						<strong><?php echo esc_html($stats['closed_cases']); ?></strong>
					</div>
					<div style="display: flex; justify-content: space-between; padding: 8px 0;">
						<span><?php esc_html_e('Total activity:', 'mydefenselaw'); ?></span>
						<strong><?php echo esc_html($stats['total_activity']); ?> <?php esc_html_e('logs', 'mydefenselaw'); ?></strong>
					</div>
				</div>

				<hr style="margin: 20px 0; border: none; border-top: 1px solid #e0e0e0;">

				<!-- Usage Chart (CSS-based) -->
				<h3 style="font-size: 14px; margin-bottom: 15px;"><?php esc_html_e('Portal Usage (Last 7 Days)', 'mydefenselaw'); ?></h3>

				<?php
				$daily_logins = mydefenselaw_portal_get_daily_logins(7);
				$max_logins = max(array_merge($daily_logins, array(1))); // Prevent division by zero
				?>

				<div class="usage-chart" style="display: flex; align-items: flex-end; justify-content: space-between; height: 100px; gap: 4px;">
					<?php foreach ($daily_logins as $day => $count) :
						$height = ($count / $max_logins) * 100;
						$bar_color = '#2271b1';
					?>
					<div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
						<div style="width: 100%; background: <?php echo esc_attr($bar_color); ?>; height: <?php echo esc_attr($height); ?>%; border-radius: 3px 3px 0 0; min-height: 3px; position: relative; transition: all 0.3s;">
							<span style="position: absolute; top: -20px; left: 50%; transform: translateX(-50%); font-size: 11px; font-weight: 600; color: #1d2327;"><?php echo esc_html($count); ?></span>
						</div>
						<span style="font-size: 10px; color: #787c82; margin-top: 5px;"><?php echo esc_html(substr($day, 0, 3)); ?></span>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>

	<style>
		.portal-stat-card {
			transition: transform 0.2s, box-shadow 0.2s;
		}
		.portal-stat-card:hover {
			transform: translateY(-2px);
			box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
		}
		.activity-item:last-child {
			border-bottom: none !important;
		}
		.usage-chart > div:hover > div {
			opacity: 0.8;
		}
	</style>
	<?php
}

/**
 * All clients page
 */
function mydefenselaw_portal_clients_page() {
	// Check permissions
	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.', 'mydefenselaw'));
	}

	// Include client management functions
	require_once get_template_directory() . '/inc/client-portal/admin/client-management.php';

	// Create list table instance
	$clients_table = new Portal_Client_List_Table();
	$clients_table->prepare_items();

	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e('All Clients', 'mydefenselaw'); ?></h1>
		<a href="<?php echo esc_url(admin_url('admin.php?page=client-portal-add-client')); ?>" class="page-title-action">
			<?php esc_html_e('Add New', 'mydefenselaw'); ?>
		</a>

		<hr class="wp-header-end">

		<form method="get">
			<input type="hidden" name="page" value="client-portal-clients">
			<?php $clients_table->search_box(__('Search Clients', 'mydefenselaw'), 'client'); ?>
			<?php $clients_table->display(); ?>
		</form>
	</div>
	<?php
}

/**
 * Add new client page
 */
function mydefenselaw_portal_add_client_page() {
	// Check permissions
	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.', 'mydefenselaw'));
	}

	// Include client management functions
	require_once get_template_directory() . '/inc/client-portal/admin/client-management.php';

	// Render create client form
	mydefenselaw_portal_render_create_client();
}

/**
 * Portal settings page
 */
function mydefenselaw_portal_settings_page() {
	// Check permissions
	if (!current_user_can('manage_options')) {
		wp_die(__('You do not have sufficient permissions to access this page.', 'mydefenselaw'));
	}

	?>
	<div class="wrap">
		<h1><?php esc_html_e('Client Portal Settings', 'mydefenselaw'); ?></h1>

		<p><?php esc_html_e('Configure your client portal settings below. These settings control access, notifications, and display options.', 'mydefenselaw'); ?></p>

		<div style="background: white; padding: 20px; border: 1px solid #c3c4c7; margin-top: 20px;">
			<p><strong><?php esc_html_e('Note:', 'mydefenselaw'); ?></strong> <?php esc_html_e('Advanced portal settings are managed through ACF Options Pages. Access them here:', 'mydefenselaw'); ?></p>

			<div style="margin-top: 15px;">
				<a href="<?php echo esc_url(admin_url('admin.php?page=theme-settings')); ?>" class="button button-primary">
					<?php esc_html_e('Go to Theme Settings', 'mydefenselaw'); ?>
				</a>
			</div>
		</div>

		<h2 style="margin-top: 30px;"><?php esc_html_e('Quick Links', 'mydefenselaw'); ?></h2>

		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 15px;">
			<div style="background: white; padding: 20px; border: 1px solid #c3c4c7; border-radius: 4px;">
				<h3 style="margin-top: 0;"><?php esc_html_e('User Management', 'mydefenselaw'); ?></h3>
				<p><?php esc_html_e('Manage WordPress users and client roles.', 'mydefenselaw'); ?></p>
				<a href="<?php echo esc_url(admin_url('users.php')); ?>" class="button">
					<?php esc_html_e('Manage Users', 'mydefenselaw'); ?>
				</a>
			</div>

			<div style="background: white; padding: 20px; border: 1px solid #c3c4c7; border-radius: 4px;">
				<h3 style="margin-top: 0;"><?php esc_html_e('Email Templates', 'mydefenselaw'); ?></h3>
				<p><?php esc_html_e('Customize email notifications sent to clients.', 'mydefenselaw'); ?></p>
				<a href="<?php echo esc_url(admin_url('admin.php?page=theme-settings')); ?>" class="button">
					<?php esc_html_e('Edit Templates', 'mydefenselaw'); ?>
				</a>
			</div>

			<div style="background: white; padding: 20px; border: 1px solid #c3c4c7; border-radius: 4px;">
				<h3 style="margin-top: 0;"><?php esc_html_e('Security Settings', 'mydefenselaw'); ?></h3>
				<p><?php esc_html_e('Configure two-factor authentication and security options.', 'mydefenselaw'); ?></p>
				<a href="<?php echo esc_url(admin_url('admin.php?page=theme-settings')); ?>" class="button">
					<?php esc_html_e('Security Options', 'mydefenselaw'); ?>
				</a>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Get portal statistics
 *
 * @return array Statistics data
 */
function mydefenselaw_portal_get_stats() {
	// Count clients (users with 'client' role)
	$client_users = get_users(array('role' => 'client', 'fields' => 'ID'));
	$total_clients = count($client_users);

	// Count active cases
	$active_cases_query = new WP_Query(array(
		'post_type' => 'client_case',
		'post_status' => 'publish',
		'tax_query' => array(
			array(
				'taxonomy' => 'case_status',
				'field' => 'slug',
				'terms' => 'active',
			),
		),
		'fields' => 'ids',
		'posts_per_page' => -1,
	));
	$active_cases = $active_cases_query->post_count;

	// Count total documents
	$total_documents = wp_count_posts('client_document')->publish;

	// Count unread messages
	$unread_query = new WP_Query(array(
		'post_type' => 'client_message',
		'post_status' => 'publish',
		'meta_query' => array(
			array(
				'key' => '_is_read',
				'compare' => 'NOT EXISTS',
			),
		),
		'fields' => 'ids',
		'posts_per_page' => -1,
	));
	$unread_messages = $unread_query->post_count;

	// New clients this week
	$week_ago = date('Y-m-d H:i:s', strtotime('-7 days'));
	$new_clients_week = count(get_users(array(
		'role' => 'client',
		'date_query' => array(
			array(
				'after' => $week_ago,
			),
		),
		'fields' => 'ID',
	)));

	// New clients this month
	$month_ago = date('Y-m-d H:i:s', strtotime('-30 days'));
	$new_clients_month = count(get_users(array(
		'role' => 'client',
		'date_query' => array(
			array(
				'after' => $month_ago,
			),
		),
		'fields' => 'ID',
	)));

	// Closed cases
	$closed_cases_query = new WP_Query(array(
		'post_type' => 'client_case',
		'post_status' => 'publish',
		'tax_query' => array(
			array(
				'taxonomy' => 'case_status',
				'field' => 'slug',
				'terms' => 'closed',
			),
		),
		'fields' => 'ids',
		'posts_per_page' => -1,
	));
	$closed_cases = $closed_cases_query->post_count;

	// Total activity logs
	$total_activity = wp_count_posts('portal_activity')->publish;

	return array(
		'total_clients' => $total_clients,
		'active_cases' => $active_cases,
		'total_documents' => $total_documents,
		'unread_messages' => $unread_messages,
		'new_clients_week' => $new_clients_week,
		'new_clients_month' => $new_clients_month,
		'closed_cases' => $closed_cases,
		'total_activity' => $total_activity,
	);
}

/**
 * Get daily login counts for chart
 *
 * @param int $days Number of days to retrieve
 * @return array Daily login counts
 */
function mydefenselaw_portal_get_daily_logins($days = 7) {
	$daily_counts = array();

	for ($i = $days - 1; $i >= 0; $i--) {
		$date = date('Y-m-d', strtotime("-$i days"));
		$day_name = date('l', strtotime("-$i days"));

		// Query login activity for this day
		$count = get_posts(array(
			'post_type' => 'portal_activity',
			'meta_query' => array(
				array(
					'key' => '_activity_type',
					'value' => 'login',
				),
			),
			'date_query' => array(
				array(
					'year' => date('Y', strtotime($date)),
					'month' => date('m', strtotime($date)),
					'day' => date('d', strtotime($date)),
				),
			),
			'fields' => 'ids',
			'posts_per_page' => -1,
		));

		$daily_counts[$day_name] = count($count);
	}

	return $daily_counts;
}

/**
 * Initialize admin menu
 *
 * Called during portal initialization.
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_init_admin() {
	// Admin menu registered via add_action above
}
