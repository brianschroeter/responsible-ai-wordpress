<?php
/**
 * Client Portal Initialization
 *
 * Main loader file for the client portal functionality.
 * Follows the theme's modular include pattern.
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Define portal version constant
 */
define('MYDEFENSELAW_PORTAL_VERSION', '1.0.0');

/**
 * Load portal modules
 */
require_once get_template_directory() . '/inc/client-portal/roles.php';
require_once get_template_directory() . '/inc/client-portal/security.php';
require_once get_template_directory() . '/inc/client-portal/post-types.php';
require_once get_template_directory() . '/inc/client-portal/document-handler.php';
require_once get_template_directory() . '/inc/client-portal/activity-logger.php';
require_once get_template_directory() . '/inc/client-portal/ajax-handlers.php';
require_once get_template_directory() . '/inc/client-portal/template-tags.php';
require_once get_template_directory() . '/inc/client-portal/notifications.php';
require_once get_template_directory() . '/inc/client-portal/admin/admin-menu.php';
require_once get_template_directory() . '/inc/client-portal/admin/client-management.php';

/**
 * Initialize Client Portal
 *
 * Runs after theme setup to ensure all WordPress functions are available.
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_init() {
	// Initialize roles
	mydefenselaw_portal_register_roles();

	// Initialize custom post types
	mydefenselaw_portal_register_post_types();

	// Initialize security measures
	mydefenselaw_portal_init_security();

	// Initialize admin features
	mydefenselaw_portal_init_admin();

	// Initialize AJAX handlers
	mydefenselaw_portal_init_ajax();

	// Initialize notifications
	mydefenselaw_portal_init_notifications();
}
add_action('after_setup_theme', 'mydefenselaw_portal_init');

/**
 * Activation hook - run on theme activation
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_activate() {
	// Create client role
	mydefenselaw_portal_create_client_role();

	// Flush rewrite rules after registering custom post types
	flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'mydefenselaw_portal_activate');

/**
 * Deactivation hook - run on theme deactivation
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_deactivate() {
	// Optionally remove client role
	// mydefenselaw_portal_remove_client_role();

	// Flush rewrite rules
	flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'mydefenselaw_portal_deactivate');
