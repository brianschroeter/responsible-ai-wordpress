<?php
/**
 * Client Portal User Roles
 *
 * Handles registration and management of the client user role.
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Register client portal roles
 *
 * Called during portal initialization.
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_register_roles() {
	// Hook to block admin access for clients
	add_action('admin_init', 'mydefenselaw_portal_block_client_admin_access');

	// Hook to redirect after login
	add_filter('login_redirect', 'mydefenselaw_portal_login_redirect', 10, 3);
}

/**
 * Create client user role
 *
 * Creates the client role with appropriate capabilities.
 * Called on theme activation.
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_create_client_role() {
	// Remove role if it exists to ensure clean creation
	remove_role('client');

	// Add client role with basic capabilities
	add_role(
		'client',
		__('Client', 'mydefenselaw'),
		array(
			// Basic WordPress capabilities
			'read'                   => true,  // Basic read access to WordPress

			// Upload capabilities
			'upload_files'           => true,  // Allow file uploads

			// Custom portal capabilities - these will be mapped to CPTs later
			'read_portal_case'       => true,  // View their own case
			'read_portal_document'   => true,  // View their own documents
			'read_portal_message'    => true,  // View their own messages
			'edit_portal_message'    => true,  // Create new messages
			'read_portal_invoice'    => true,  // View their own invoices
			'read_portal_event'      => true,  // View their own calendar events
		)
	);
}

/**
 * Remove client user role
 *
 * Removes the client role. Called on theme deactivation (optional).
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_remove_client_role() {
	remove_role('client');
}

/**
 * Block admin access for client role
 *
 * Redirects clients to the portal dashboard if they try to access wp-admin.
 * Allows access to admin-ajax.php for AJAX functionality.
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_block_client_admin_access() {
	// Check if user is a client
	if (!current_user_can('client')) {
		return;
	}

	// Allow access to admin-ajax.php
	if (defined('DOING_AJAX') && DOING_AJAX) {
		return;
	}

	// Redirect to portal dashboard
	wp_redirect(home_url('/client-portal/'));
	exit;
}

/**
 * Redirect clients to portal after login
 *
 * Redirects client role users to the portal dashboard after login.
 * Other user roles are unaffected.
 *
 * @since 1.0.0
 * @param string $redirect_to The redirect destination URL.
 * @param string $request The requested redirect destination URL passed as a parameter.
 * @param WP_User|WP_Error $user WP_User object if login was successful, WP_Error object otherwise.
 * @return string The redirect URL.
 */
function mydefenselaw_portal_login_redirect($redirect_to, $request, $user) {
	// Check if user is valid and has client role
	if (isset($user->roles) && is_array($user->roles) && in_array('client', $user->roles)) {
		// Redirect to portal dashboard
		return home_url('/client-portal/');
	}

	// Return default redirect for other roles
	return $redirect_to;
}

/**
 * Check if current user is a client
 *
 * Helper function to check if the current user has the client role.
 *
 * @since 1.0.0
 * @return bool True if current user is a client, false otherwise.
 */
function mydefenselaw_portal_is_client() {
	return current_user_can('client');
}

/**
 * Get client's attorney ID
 *
 * Gets the assigned attorney ID for a client user.
 *
 * @since 1.0.0
 * @param int $client_id Optional. Client user ID. Defaults to current user.
 * @return int|false Attorney user ID or false if not assigned.
 */
function mydefenselaw_portal_get_client_attorney($client_id = 0) {
	if (empty($client_id)) {
		$client_id = get_current_user_id();
	}

	$attorney_id = get_user_meta($client_id, '_assigned_attorney', true);

	return !empty($attorney_id) ? (int) $attorney_id : false;
}

/**
 * Assign attorney to client
 *
 * Assigns an attorney to a client user.
 *
 * @since 1.0.0
 * @param int $client_id Client user ID.
 * @param int $attorney_id Attorney user ID.
 * @return bool True on success, false on failure.
 */
function mydefenselaw_portal_assign_attorney($client_id, $attorney_id) {
	return update_user_meta($client_id, '_assigned_attorney', $attorney_id);
}

/**
 * Get all clients
 *
 * Retrieves all users with the client role.
 *
 * @since 1.0.0
 * @param array $args Optional. Additional WP_User_Query arguments.
 * @return array Array of WP_User objects.
 */
function mydefenselaw_portal_get_clients($args = array()) {
	$defaults = array(
		'role'    => 'client',
		'orderby' => 'display_name',
		'order'   => 'ASC',
	);

	$args = wp_parse_args($args, $defaults);

	$user_query = new WP_User_Query($args);

	return $user_query->get_results();
}
