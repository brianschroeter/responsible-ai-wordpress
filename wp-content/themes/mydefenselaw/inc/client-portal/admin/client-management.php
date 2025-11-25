<?php
/**
 * Client Portal Client Management
 *
 * Admin interface for managing client users with WP_List_Table,
 * create/edit forms, case assignment, and full client profiles.
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

// Load WP_List_Table if not loaded
if (!class_exists('WP_List_Table')) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Portal Client List Table
 *
 * Displays all client users in a table with filtering, search, and bulk actions.
 */
class Portal_Client_List_Table extends WP_List_Table {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(array(
			'singular' => __('Client', 'mydefenselaw'),
			'plural'   => __('Clients', 'mydefenselaw'),
			'ajax'     => false,
		));
	}

	/**
	 * Get columns
	 */
	public function get_columns() {
		return array(
			'cb'          => '<input type="checkbox" />',
			'name'        => __('Name', 'mydefenselaw'),
			'email'       => __('Email', 'mydefenselaw'),
			'phone'       => __('Phone', 'mydefenselaw'),
			'cases'       => __('Cases', 'mydefenselaw'),
			'documents'   => __('Documents', 'mydefenselaw'),
			'last_login'  => __('Last Login', 'mydefenselaw'),
			'actions'     => __('Actions', 'mydefenselaw'),
		);
	}

	/**
	 * Get sortable columns
	 */
	public function get_sortable_columns() {
		return array(
			'name'       => array('name', false),
			'email'      => array('email', false),
			'last_login' => array('last_login', true),
		);
	}

	/**
	 * Get bulk actions
	 */
	public function get_bulk_actions() {
		return array(
			'delete'       => __('Delete', 'mydefenselaw'),
			'send_message' => __('Send Message', 'mydefenselaw'),
		);
	}

	/**
	 * Column default
	 */
	public function column_default($item, $column_name) {
		return isset($item[$column_name]) ? $item[$column_name] : '';
	}

	/**
	 * Column checkbox
	 */
	public function column_cb($item) {
		return sprintf('<input type="checkbox" name="clients[]" value="%s" />', $item['ID']);
	}

	/**
	 * Column name
	 */
	public function column_name($item) {
		$view_url = admin_url('admin.php?page=client-portal-clients&action=view&client_id=' . $item['ID']);
		$edit_url = admin_url('user-edit.php?user_id=' . $item['ID']);
		$delete_url = wp_nonce_url(
			admin_url('admin.php?page=client-portal-clients&action=delete&client_id=' . $item['ID']),
			'delete_client_' . $item['ID']
		);

		$actions = array(
			'view'   => sprintf('<a href="%s">%s</a>', esc_url($view_url), __('View', 'mydefenselaw')),
			'edit'   => sprintf('<a href="%s">%s</a>', esc_url($edit_url), __('Edit', 'mydefenselaw')),
			'delete' => sprintf('<a href="%s" onclick="return confirm(\'%s\');">%s</a>',
				esc_url($delete_url),
				esc_js(__('Are you sure you want to delete this client?', 'mydefenselaw')),
				__('Delete', 'mydefenselaw')
			),
		);

		return sprintf(
			'<strong><a href="%s">%s</a></strong>%s',
			esc_url($view_url),
			esc_html($item['name']),
			$this->row_actions($actions)
		);
	}

	/**
	 * Column email
	 */
	public function column_email($item) {
		return sprintf('<a href="mailto:%s">%s</a>', esc_attr($item['email']), esc_html($item['email']));
	}

	/**
	 * Column phone
	 */
	public function column_phone($item) {
		if (empty($item['phone'])) {
			return '<span style="color:#999;">—</span>';
		}
		$phone_clean = preg_replace('/[^0-9+]/', '', $item['phone']);
		return sprintf('<a href="tel:%s">%s</a>', esc_attr($phone_clean), esc_html($item['phone']));
	}

	/**
	 * Column cases
	 */
	public function column_cases($item) {
		$count = intval($item['cases']);
		if ($count === 0) {
			return '<span style="color:#999;">0</span>';
		}
		$url = admin_url('edit.php?post_type=client_case&client_id=' . $item['ID']);
		return sprintf(
			'<a href="%s"><span class="count-badge" style="background:#2271b1;color:#fff;padding:2px 8px;border-radius:10px;font-size:12px;">%d</span></a>',
			esc_url($url),
			$count
		);
	}

	/**
	 * Column documents
	 */
	public function column_documents($item) {
		$count = intval($item['documents']);
		if ($count === 0) {
			return '<span style="color:#999;">0</span>';
		}
		$url = admin_url('edit.php?post_type=client_document&client_id=' . $item['ID']);
		return sprintf(
			'<a href="%s"><span class="count-badge" style="background:#d63638;color:#fff;padding:2px 8px;border-radius:10px;font-size:12px;">%d</span></a>',
			esc_url($url),
			$count
		);
	}

	/**
	 * Column last login
	 */
	public function column_last_login($item) {
		if (empty($item['last_login'])) {
			return '<span style="color:#999;">' . __('Never', 'mydefenselaw') . '</span>';
		}
		$time_ago = human_time_diff(strtotime($item['last_login']), current_time('timestamp'));
		return sprintf(
			'<span title="%s">%s ago</span>',
			esc_attr(date('F j, Y g:i a', strtotime($item['last_login']))),
			esc_html($time_ago)
		);
	}

	/**
	 * Column actions
	 */
	public function column_actions($item) {
		$view_url = admin_url('admin.php?page=client-portal-clients&action=view&client_id=' . $item['ID']);
		$message_url = admin_url('post-new.php?post_type=client_message&client_id=' . $item['ID']);
		$assign_case_url = admin_url('admin.php?page=client-portal-clients&action=assign_case&client_id=' . $item['ID']);

		return sprintf(
			'<a href="%s" class="button button-small">%s</a> ' .
			'<a href="%s" class="button button-small">%s</a> ' .
			'<a href="%s" class="button button-small">%s</a>',
			esc_url($view_url),
			__('View', 'mydefenselaw'),
			esc_url($message_url),
			__('Message', 'mydefenselaw'),
			esc_url($assign_case_url),
			__('Assign Case', 'mydefenselaw')
		);
	}

	/**
	 * Extra table navigation
	 */
	public function extra_tablenav($which) {
		if ($which !== 'top') {
			return;
		}
		?>
		<div class="alignleft actions">
			<select name="filter_cases">
				<option value=""><?php esc_html_e('All Clients', 'mydefenselaw'); ?></option>
				<option value="has_active_case" <?php selected(isset($_GET['filter_cases']) && $_GET['filter_cases'] === 'has_active_case'); ?>>
					<?php esc_html_e('Has Active Case', 'mydefenselaw'); ?>
				</option>
				<option value="no_cases" <?php selected(isset($_GET['filter_cases']) && $_GET['filter_cases'] === 'no_cases'); ?>>
					<?php esc_html_e('No Cases', 'mydefenselaw'); ?>
				</option>
				<option value="recently_active" <?php selected(isset($_GET['filter_cases']) && $_GET['filter_cases'] === 'recently_active'); ?>>
					<?php esc_html_e('Recently Active', 'mydefenselaw'); ?>
				</option>
			</select>
			<?php submit_button(__('Filter', 'mydefenselaw'), '', 'filter_action', false); ?>
		</div>
		<?php
	}

	/**
	 * Prepare items
	 */
	public function prepare_items() {
		// Set columns
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		// Handle bulk actions
		$this->process_bulk_action();

		// Get parameters
		$per_page = 20;
		$current_page = $this->get_pagenum();
		$orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'name';
		$order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'ASC';
		$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

		// Get users with 'client' role
		$args = array(
			'role'    => 'client',
			'orderby' => 'display_name',
			'order'   => 'ASC',
			'number'  => $per_page,
			'offset'  => ($current_page - 1) * $per_page,
		);

		// Handle search
		if (!empty($search)) {
			$args['search'] = '*' . $search . '*';
			$args['search_columns'] = array('user_login', 'user_email', 'display_name');
		}

		// Handle ordering
		switch ($orderby) {
			case 'email':
				$args['orderby'] = 'user_email';
				break;
			case 'last_login':
				$args['orderby'] = 'meta_value';
				$args['meta_key'] = '_last_login';
				break;
			default:
				$args['orderby'] = 'display_name';
		}

		$args['order'] = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

		// Get users
		$user_query = new WP_User_Query($args);
		$users = $user_query->get_results();
		$total_items = $user_query->get_total();

		// Apply filters
		if (isset($_GET['filter_cases']) && !empty($_GET['filter_cases'])) {
			$users = $this->apply_filters($users, $_GET['filter_cases']);
			$total_items = count($users);
		}

		// Format data
		$data = array();
		foreach ($users as $user) {
			// Get case count
			$case_count = get_posts(array(
				'post_type'      => 'client_case',
				'post_status'    => 'publish',
				'meta_key'       => '_client_id',
				'meta_value'     => $user->ID,
				'posts_per_page' => -1,
				'fields'         => 'ids',
			));

			// Get document count
			$document_count = get_posts(array(
				'post_type'      => 'client_document',
				'post_status'    => 'publish',
				'meta_key'       => '_client_id',
				'meta_value'     => $user->ID,
				'posts_per_page' => -1,
				'fields'         => 'ids',
			));

			// Get last login
			$last_login = get_user_meta($user->ID, '_last_login', true);

			// Get phone
			$phone = get_user_meta($user->ID, 'phone', true);

			$data[] = array(
				'ID'         => $user->ID,
				'name'       => $user->display_name,
				'email'      => $user->user_email,
				'phone'      => $phone,
				'cases'      => count($case_count),
				'documents'  => count($document_count),
				'last_login' => $last_login,
			);
		}

		$this->items = $data;

		// Set pagination
		$this->set_pagination_args(array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil($total_items / $per_page),
		));
	}

	/**
	 * Apply filters
	 */
	private function apply_filters($users, $filter) {
		$filtered = array();

		foreach ($users as $user) {
			$include = false;

			switch ($filter) {
				case 'has_active_case':
					// Check for active cases
					$active_cases = get_posts(array(
						'post_type'      => 'client_case',
						'post_status'    => 'publish',
						'meta_key'       => '_client_id',
						'meta_value'     => $user->ID,
						'tax_query'      => array(
							array(
								'taxonomy' => 'case_status',
								'field'    => 'slug',
								'terms'    => 'active',
							),
						),
						'posts_per_page' => 1,
						'fields'         => 'ids',
					));
					$include = !empty($active_cases);
					break;

				case 'no_cases':
					// Check for no cases
					$cases = get_posts(array(
						'post_type'      => 'client_case',
						'post_status'    => 'publish',
						'meta_key'       => '_client_id',
						'meta_value'     => $user->ID,
						'posts_per_page' => 1,
						'fields'         => 'ids',
					));
					$include = empty($cases);
					break;

				case 'recently_active':
					// Check for login in last 30 days
					$last_login = get_user_meta($user->ID, '_last_login', true);
					if ($last_login) {
						$thirty_days_ago = strtotime('-30 days');
						$include = strtotime($last_login) > $thirty_days_ago;
					}
					break;
			}

			if ($include) {
				$filtered[] = $user;
			}
		}

		return $filtered;
	}

	/**
	 * Process bulk actions
	 */
	public function process_bulk_action() {
		// Handle delete action
		if ($this->current_action() === 'delete') {
			if (isset($_GET['client_id'])) {
				$client_id = intval($_GET['client_id']);
				check_admin_referer('delete_client_' . $client_id);

				if (current_user_can('delete_users')) {
					wp_delete_user($client_id);
					add_action('admin_notices', function() {
						echo '<div class="notice notice-success is-dismissible"><p>' .
							 __('Client deleted successfully.', 'mydefenselaw') .
							 '</p></div>';
					});
				}
			}
		}

		// Handle bulk delete
		if (isset($_POST['action']) && $_POST['action'] === 'delete') {
			if (isset($_POST['clients']) && is_array($_POST['clients'])) {
				foreach ($_POST['clients'] as $client_id) {
					$client_id = intval($client_id);
					if (current_user_can('delete_users')) {
						wp_delete_user($client_id);
					}
				}

				add_action('admin_notices', function() {
					$count = count($_POST['clients']);
					echo '<div class="notice notice-success is-dismissible"><p>' .
						 sprintf(_n('%d client deleted.', '%d clients deleted.', $count, 'mydefenselaw'), $count) .
						 '</p></div>';
				});
			}
		}
	}
}

/**
 * Render single client view page
 *
 * @param int $user_id User ID
 */
function mydefenselaw_portal_render_client_view($user_id = null) {
	if (!$user_id && isset($_GET['client_id'])) {
		$user_id = intval($_GET['client_id']);
	}

	if (!$user_id) {
		wp_die(__('Invalid client ID.', 'mydefenselaw'));
	}

	$user = get_userdata($user_id);

	if (!$user || !in_array('client', $user->roles)) {
		wp_die(__('Client not found.', 'mydefenselaw'));
	}

	// Get user meta
	$phone = get_user_meta($user_id, 'phone', true);
	$address = get_user_meta($user_id, 'address', true);
	$city = get_user_meta($user_id, 'city', true);
	$state = get_user_meta($user_id, 'state', true);
	$zip = get_user_meta($user_id, 'zip', true);
	$last_login = get_user_meta($user_id, '_last_login', true);

	// Get cases
	$cases = get_posts(array(
		'post_type'      => 'client_case',
		'post_status'    => 'publish',
		'meta_key'       => '_client_id',
		'meta_value'     => $user_id,
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	));

	// Get documents
	$documents = get_posts(array(
		'post_type'      => 'client_document',
		'post_status'    => 'publish',
		'meta_key'       => '_client_id',
		'meta_value'     => $user_id,
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	));

	// Get messages
	$messages = get_posts(array(
		'post_type'      => 'client_message',
		'post_status'    => 'publish',
		'meta_key'       => '_client_id',
		'meta_value'     => $user_id,
		'posts_per_page' => 10,
		'orderby'        => 'date',
		'order'          => 'DESC',
	));

	// Get recent activity
	$activity = get_posts(array(
		'post_type'      => 'portal_activity',
		'post_status'    => 'publish',
		'meta_key'       => '_client_id',
		'meta_value'     => $user_id,
		'posts_per_page' => 10,
		'orderby'        => 'date',
		'order'          => 'DESC',
	));

	?>
	<div class="wrap">
		<h1 class="wp-heading-inline">
			<span class="dashicons dashicons-admin-users" style="font-size: 28px; vertical-align: middle; margin-right: 10px;"></span>
			<?php echo esc_html($user->display_name); ?>
		</h1>
		<a href="<?php echo esc_url(admin_url('user-edit.php?user_id=' . $user_id)); ?>" class="page-title-action">
			<?php esc_html_e('Edit Profile', 'mydefenselaw'); ?>
		</a>
		<a href="<?php echo esc_url(admin_url('admin.php?page=client-portal-clients')); ?>" class="page-title-action">
			<?php esc_html_e('Back to All Clients', 'mydefenselaw'); ?>
		</a>

		<hr class="wp-header-end">

		<!-- Two Column Layout -->
		<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-top: 20px;">

			<!-- Left Sidebar: Client Info -->
			<div>
				<div style="background: white; padding: 20px; border: 1px solid #c3c4c7; border-radius: 4px; margin-bottom: 20px;">
					<h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #e0e0e0;">
						<?php esc_html_e('Contact Information', 'mydefenselaw'); ?>
					</h2>

					<div style="margin-top: 15px;">
						<div style="margin-bottom: 15px;">
							<strong style="display: block; margin-bottom: 5px; color: #646970;"><?php esc_html_e('Email', 'mydefenselaw'); ?></strong>
							<a href="mailto:<?php echo esc_attr($user->user_email); ?>" style="text-decoration: none;">
								<?php echo esc_html($user->user_email); ?>
							</a>
						</div>

						<?php if ($phone) : ?>
						<div style="margin-bottom: 15px;">
							<strong style="display: block; margin-bottom: 5px; color: #646970;"><?php esc_html_e('Phone', 'mydefenselaw'); ?></strong>
							<a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>" style="text-decoration: none;">
								<?php echo esc_html($phone); ?>
							</a>
						</div>
						<?php endif; ?>

						<?php if ($address || $city || $state || $zip) : ?>
						<div style="margin-bottom: 15px;">
							<strong style="display: block; margin-bottom: 5px; color: #646970;"><?php esc_html_e('Address', 'mydefenselaw'); ?></strong>
							<div>
								<?php
								if ($address) echo esc_html($address) . '<br>';
								if ($city) echo esc_html($city);
								if ($state) echo ', ' . esc_html($state);
								if ($zip) echo ' ' . esc_html($zip);
								?>
							</div>
						</div>
						<?php endif; ?>

						<div style="margin-bottom: 15px;">
							<strong style="display: block; margin-bottom: 5px; color: #646970;"><?php esc_html_e('Last Login', 'mydefenselaw'); ?></strong>
							<?php
							if ($last_login) {
								echo esc_html(human_time_diff(strtotime($last_login), current_time('timestamp'))) . ' ago';
							} else {
								echo '<span style="color:#999;">' . __('Never', 'mydefenselaw') . '</span>';
							}
							?>
						</div>

						<div>
							<strong style="display: block; margin-bottom: 5px; color: #646970;"><?php esc_html_e('Member Since', 'mydefenselaw'); ?></strong>
							<?php echo esc_html(date('F j, Y', strtotime($user->user_registered))); ?>
						</div>
					</div>
				</div>

				<!-- Quick Actions -->
				<div style="background: white; padding: 20px; border: 1px solid #c3c4c7; border-radius: 4px;">
					<h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #e0e0e0;">
						<?php esc_html_e('Quick Actions', 'mydefenselaw'); ?>
					</h2>

					<div style="margin-top: 15px;">
						<a href="<?php echo esc_url(admin_url('post-new.php?post_type=client_case&client_id=' . $user_id)); ?>" class="button" style="display: block; text-align: center; margin-bottom: 10px; width: 100%;">
							<span class="dashicons dashicons-portfolio" style="margin-top: 3px;"></span>
							<?php esc_html_e('Create Case', 'mydefenselaw'); ?>
						</a>
						<a href="<?php echo esc_url(admin_url('post-new.php?post_type=client_document&client_id=' . $user_id)); ?>" class="button" style="display: block; text-align: center; margin-bottom: 10px; width: 100%;">
							<span class="dashicons dashicons-media-document" style="margin-top: 3px;"></span>
							<?php esc_html_e('Upload Document', 'mydefenselaw'); ?>
						</a>
						<a href="<?php echo esc_url(admin_url('post-new.php?post_type=client_message&client_id=' . $user_id)); ?>" class="button" style="display: block; text-align: center; margin-bottom: 10px; width: 100%;">
							<span class="dashicons dashicons-email-alt" style="margin-top: 3px;"></span>
							<?php esc_html_e('Send Message', 'mydefenselaw'); ?>
						</a>
					</div>
				</div>
			</div>

			<!-- Right Content: Cases, Documents, Messages, Activity -->
			<div>
				<!-- Cases -->
				<div style="background: white; padding: 20px; border: 1px solid #c3c4c7; border-radius: 4px; margin-bottom: 20px;">
					<h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #e0e0e0;">
						<span class="dashicons dashicons-portfolio" style="vertical-align: middle; margin-right: 5px;"></span>
						<?php esc_html_e('Cases', 'mydefenselaw'); ?>
						<span style="font-weight: normal; font-size: 14px; color: #646970;">(<?php echo count($cases); ?>)</span>
					</h2>

					<?php if (!empty($cases)) : ?>
						<table class="widefat fixed striped" style="margin-top: 15px;">
							<thead>
								<tr>
									<th><?php esc_html_e('Case Title', 'mydefenselaw'); ?></th>
									<th><?php esc_html_e('Status', 'mydefenselaw'); ?></th>
									<th><?php esc_html_e('Date', 'mydefenselaw'); ?></th>
									<th><?php esc_html_e('Actions', 'mydefenselaw'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($cases as $case) :
									$terms = get_the_terms($case->ID, 'case_status');
									$status = $terms && !is_wp_error($terms) ? $terms[0]->name : '—';
								?>
								<tr>
									<td>
										<strong>
											<a href="<?php echo esc_url(get_edit_post_link($case->ID)); ?>">
												<?php echo esc_html($case->post_title); ?>
											</a>
										</strong>
									</td>
									<td><?php echo esc_html($status); ?></td>
									<td><?php echo esc_html(get_the_date('M j, Y', $case)); ?></td>
									<td>
										<a href="<?php echo esc_url(get_edit_post_link($case->ID)); ?>" class="button button-small">
											<?php esc_html_e('View', 'mydefenselaw'); ?>
										</a>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else : ?>
						<p style="color: #646970; margin-top: 15px;"><?php esc_html_e('No cases assigned to this client.', 'mydefenselaw'); ?></p>
					<?php endif; ?>
				</div>

				<!-- Documents -->
				<div style="background: white; padding: 20px; border: 1px solid #c3c4c7; border-radius: 4px; margin-bottom: 20px;">
					<h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #e0e0e0;">
						<span class="dashicons dashicons-media-document" style="vertical-align: middle; margin-right: 5px;"></span>
						<?php esc_html_e('Documents', 'mydefenselaw'); ?>
						<span style="font-weight: normal; font-size: 14px; color: #646970;">(<?php echo count($documents); ?>)</span>
					</h2>

					<?php if (!empty($documents)) : ?>
						<table class="widefat fixed striped" style="margin-top: 15px;">
							<thead>
								<tr>
									<th><?php esc_html_e('Document Name', 'mydefenselaw'); ?></th>
									<th><?php esc_html_e('Type', 'mydefenselaw'); ?></th>
									<th><?php esc_html_e('Date', 'mydefenselaw'); ?></th>
									<th><?php esc_html_e('Actions', 'mydefenselaw'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($documents as $document) :
									$terms = get_the_terms($document->ID, 'document_type');
									$type = $terms && !is_wp_error($terms) ? $terms[0]->name : '—';
								?>
								<tr>
									<td>
										<strong>
											<a href="<?php echo esc_url(get_edit_post_link($document->ID)); ?>">
												<?php echo esc_html($document->post_title); ?>
											</a>
										</strong>
									</td>
									<td><?php echo esc_html($type); ?></td>
									<td><?php echo esc_html(get_the_date('M j, Y', $document)); ?></td>
									<td>
										<a href="<?php echo esc_url(get_edit_post_link($document->ID)); ?>" class="button button-small">
											<?php esc_html_e('View', 'mydefenselaw'); ?>
										</a>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else : ?>
						<p style="color: #646970; margin-top: 15px;"><?php esc_html_e('No documents for this client.', 'mydefenselaw'); ?></p>
					<?php endif; ?>
				</div>

				<!-- Messages -->
				<div style="background: white; padding: 20px; border: 1px solid #c3c4c7; border-radius: 4px; margin-bottom: 20px;">
					<h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #e0e0e0;">
						<span class="dashicons dashicons-email" style="vertical-align: middle; margin-right: 5px;"></span>
						<?php esc_html_e('Recent Messages', 'mydefenselaw'); ?>
					</h2>

					<?php if (!empty($messages)) : ?>
						<div style="margin-top: 15px;">
							<?php foreach ($messages as $message) :
								$is_read = get_post_meta($message->ID, '_is_read', true);
							?>
							<div style="padding: 12px; border-bottom: 1px solid #f0f0f0; <?php if (!$is_read) echo 'background: #f0f6fc;'; ?>">
								<div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
									<strong>
										<a href="<?php echo esc_url(get_edit_post_link($message->ID)); ?>">
											<?php echo esc_html($message->post_title); ?>
										</a>
									</strong>
									<span style="font-size: 12px; color: #646970;">
										<?php echo esc_html(get_the_date('M j, Y', $message)); ?>
									</span>
								</div>
								<div style="font-size: 13px; color: #646970;">
									<?php echo esc_html(wp_trim_words($message->post_content, 20)); ?>
								</div>
							</div>
							<?php endforeach; ?>
						</div>
					<?php else : ?>
						<p style="color: #646970; margin-top: 15px;"><?php esc_html_e('No messages for this client.', 'mydefenselaw'); ?></p>
					<?php endif; ?>
				</div>

				<!-- Activity Log -->
				<div style="background: white; padding: 20px; border: 1px solid #c3c4c7; border-radius: 4px;">
					<h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #e0e0e0;">
						<span class="dashicons dashicons-visibility" style="vertical-align: middle; margin-right: 5px;"></span>
						<?php esc_html_e('Recent Activity', 'mydefenselaw'); ?>
					</h2>

					<?php if (!empty($activity)) : ?>
						<div style="margin-top: 15px;">
							<?php foreach ($activity as $log) :
								$activity_type = get_post_meta($log->ID, '_activity_type', true);
								$time_ago = human_time_diff(get_the_time('U', $log), current_time('timestamp')) . ' ago';
							?>
							<div style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
								<div style="font-weight: 500; margin-bottom: 3px;">
									<?php echo esc_html($log->post_title); ?>
								</div>
								<div style="font-size: 12px; color: #646970;">
									<?php echo esc_html($time_ago); ?>
								</div>
							</div>
							<?php endforeach; ?>
						</div>
					<?php else : ?>
						<p style="color: #646970; margin-top: 15px;"><?php esc_html_e('No recent activity.', 'mydefenselaw'); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Render create client form
 */
function mydefenselaw_portal_render_create_client() {
	// Handle form submission
	if (isset($_POST['create_client_nonce']) && wp_verify_nonce($_POST['create_client_nonce'], 'create_client')) {
		$result = mydefenselaw_portal_create_client($_POST);

		if (is_wp_error($result)) {
			echo '<div class="notice notice-error"><p>' . esc_html($result->get_error_message()) . '</p></div>';
		} else {
			echo '<div class="notice notice-success"><p>' .
				 sprintf(__('Client created successfully! <a href="%s">View client profile</a>', 'mydefenselaw'),
					 admin_url('admin.php?page=client-portal-clients&action=view&client_id=' . $result)
				 ) .
				 '</p></div>';
		}
	}

	?>
	<div class="wrap">
		<h1><?php esc_html_e('Add New Client', 'mydefenselaw'); ?></h1>

		<form method="post" action="" style="max-width: 800px;">
			<?php wp_nonce_field('create_client', 'create_client_nonce'); ?>

			<div style="background: white; padding: 20px; border: 1px solid #c3c4c7; border-radius: 4px; margin-top: 20px;">
				<h2 style="margin-top: 0;"><?php esc_html_e('Client Information', 'mydefenselaw'); ?></h2>

				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row">
								<label for="first_name"><?php esc_html_e('First Name', 'mydefenselaw'); ?> <span class="description">(required)</span></label>
							</th>
							<td>
								<input type="text" name="first_name" id="first_name" class="regular-text" required>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="last_name"><?php esc_html_e('Last Name', 'mydefenselaw'); ?> <span class="description">(required)</span></label>
							</th>
							<td>
								<input type="text" name="last_name" id="last_name" class="regular-text" required>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="email"><?php esc_html_e('Email', 'mydefenselaw'); ?> <span class="description">(required)</span></label>
							</th>
							<td>
								<input type="email" name="email" id="email" class="regular-text" required>
								<p class="description"><?php esc_html_e('Used for login and communication.', 'mydefenselaw'); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="phone"><?php esc_html_e('Phone', 'mydefenselaw'); ?></label>
							</th>
							<td>
								<input type="tel" name="phone" id="phone" class="regular-text">
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="address"><?php esc_html_e('Address', 'mydefenselaw'); ?></label>
							</th>
							<td>
								<input type="text" name="address" id="address" class="regular-text">
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="city"><?php esc_html_e('City', 'mydefenselaw'); ?></label>
							</th>
							<td>
								<input type="text" name="city" id="city" class="regular-text">
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="state"><?php esc_html_e('State', 'mydefenselaw'); ?></label>
							</th>
							<td>
								<input type="text" name="state" id="state" class="regular-text">
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="zip"><?php esc_html_e('ZIP Code', 'mydefenselaw'); ?></label>
							</th>
							<td>
								<input type="text" name="zip" id="zip" class="regular-text">
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="send_credentials"><?php esc_html_e('Login Credentials', 'mydefenselaw'); ?></label>
							</th>
							<td>
								<label>
									<input type="checkbox" name="send_credentials" id="send_credentials" value="1" checked>
									<?php esc_html_e('Send login credentials via email', 'mydefenselaw'); ?>
								</label>
								<p class="description"><?php esc_html_e('An auto-generated password will be sent to the client.', 'mydefenselaw'); ?></p>
							</td>
						</tr>
					</tbody>
				</table>

				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Create Client', 'mydefenselaw'); ?>">
					<a href="<?php echo esc_url(admin_url('admin.php?page=client-portal-clients')); ?>" class="button">
						<?php esc_html_e('Cancel', 'mydefenselaw'); ?>
					</a>
				</p>
			</div>
		</form>
	</div>
	<?php
}

/**
 * Create new client user
 *
 * @param array $data Form data
 * @return int|WP_Error User ID on success, WP_Error on failure
 */
function mydefenselaw_portal_create_client($data) {
	// Sanitize inputs
	$first_name = sanitize_text_field($data['first_name'] ?? '');
	$last_name = sanitize_text_field($data['last_name'] ?? '');
	$email = sanitize_email($data['email'] ?? '');
	$phone = sanitize_text_field($data['phone'] ?? '');
	$address = sanitize_text_field($data['address'] ?? '');
	$city = sanitize_text_field($data['city'] ?? '');
	$state = sanitize_text_field($data['state'] ?? '');
	$zip = sanitize_text_field($data['zip'] ?? '');
	$send_credentials = !empty($data['send_credentials']);

	// Validate required fields
	if (empty($first_name) || empty($last_name) || empty($email)) {
		return new WP_Error('missing_fields', __('First name, last name, and email are required.', 'mydefenselaw'));
	}

	// Check if email exists
	if (email_exists($email)) {
		return new WP_Error('email_exists', __('This email is already registered.', 'mydefenselaw'));
	}

	// Generate username
	$username = strtolower($first_name . '.' . $last_name);
	$username = sanitize_user($username, true);

	// Make username unique
	$username_base = $username;
	$counter = 1;
	while (username_exists($username)) {
		$username = $username_base . $counter;
		$counter++;
	}

	// Generate password
	$password = wp_generate_password(12, false);

	// Create user
	$user_id = wp_create_user($username, $password, $email);

	if (is_wp_error($user_id)) {
		return $user_id;
	}

	// Set role
	$user = new WP_User($user_id);
	$user->set_role('client');

	// Update user meta
	wp_update_user(array(
		'ID'           => $user_id,
		'first_name'   => $first_name,
		'last_name'    => $last_name,
		'display_name' => $first_name . ' ' . $last_name,
	));

	// Save additional meta
	update_user_meta($user_id, 'phone', $phone);
	update_user_meta($user_id, 'address', $address);
	update_user_meta($user_id, 'city', $city);
	update_user_meta($user_id, 'state', $state);
	update_user_meta($user_id, 'zip', $zip);

	// Send credentials email
	if ($send_credentials) {
		wp_new_user_notification($user_id, null, 'both');
	}

	// Log activity
	if (function_exists('mydefenselaw_portal_log_activity')) {
		mydefenselaw_portal_log_activity(
			'client_created',
			sprintf(__('New client %s was created', 'mydefenselaw'), $first_name . ' ' . $last_name),
			$user_id
		);
	}

	return $user_id;
}

/**
 * Render assign case form
 */
function mydefenselaw_portal_assign_case_form() {
	if (!isset($_GET['client_id'])) {
		wp_die(__('Invalid client ID.', 'mydefenselaw'));
	}

	$client_id = intval($_GET['client_id']);
	$user = get_userdata($client_id);

	if (!$user || !in_array('client', $user->roles)) {
		wp_die(__('Client not found.', 'mydefenselaw'));
	}

	// Handle form submission
	if (isset($_POST['assign_case_nonce']) && wp_verify_nonce($_POST['assign_case_nonce'], 'assign_case')) {
		$case_id = intval($_POST['case_id']);

		if ($case_id > 0) {
			update_post_meta($case_id, '_client_id', $client_id);
			update_post_meta($case_id, '_client_name', $user->display_name);

			echo '<div class="notice notice-success"><p>' .
				 sprintf(__('Case assigned to %s successfully!', 'mydefenselaw'), esc_html($user->display_name)) .
				 '</p></div>';
		}
	}

	// Get all cases
	$cases = get_posts(array(
		'post_type'      => 'client_case',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	));

	?>
	<div class="wrap">
		<h1><?php printf(__('Assign Case to %s', 'mydefenselaw'), esc_html($user->display_name)); ?></h1>

		<form method="post" action="" style="max-width: 600px;">
			<?php wp_nonce_field('assign_case', 'assign_case_nonce'); ?>

			<div style="background: white; padding: 20px; border: 1px solid #c3c4c7; border-radius: 4px; margin-top: 20px;">
				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row">
								<label for="case_id"><?php esc_html_e('Select Case', 'mydefenselaw'); ?></label>
							</th>
							<td>
								<select name="case_id" id="case_id" class="regular-text" required>
									<option value=""><?php esc_html_e('— Select Case —', 'mydefenselaw'); ?></option>
									<?php foreach ($cases as $case) : ?>
									<option value="<?php echo esc_attr($case->ID); ?>">
										<?php echo esc_html($case->post_title); ?>
									</option>
									<?php endforeach; ?>
								</select>
								<p class="description">
									<?php esc_html_e('Or', 'mydefenselaw'); ?>
									<a href="<?php echo esc_url(admin_url('post-new.php?post_type=client_case&client_id=' . $client_id)); ?>">
										<?php esc_html_e('create a new case', 'mydefenselaw'); ?>
									</a>
								</p>
							</td>
						</tr>
					</tbody>
				</table>

				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Assign Case', 'mydefenselaw'); ?>">
					<a href="<?php echo esc_url(admin_url('admin.php?page=client-portal-clients&action=view&client_id=' . $client_id)); ?>" class="button">
						<?php esc_html_e('Cancel', 'mydefenselaw'); ?>
					</a>
				</p>
			</div>
		</form>
	</div>
	<?php
}

/**
 * Handle actions for client management page
 */
function mydefenselaw_portal_handle_client_actions() {
	if (!isset($_GET['page']) || $_GET['page'] !== 'client-portal-clients') {
		return;
	}

	if (!isset($_GET['action'])) {
		return;
	}

	$action = sanitize_text_field($_GET['action']);

	switch ($action) {
		case 'view':
			if (isset($_GET['client_id'])) {
				mydefenselaw_portal_render_client_view(intval($_GET['client_id']));
				exit;
			}
			break;

		case 'assign_case':
			mydefenselaw_portal_assign_case_form();
			exit;
			break;
	}
}
add_action('admin_init', 'mydefenselaw_portal_handle_client_actions');
