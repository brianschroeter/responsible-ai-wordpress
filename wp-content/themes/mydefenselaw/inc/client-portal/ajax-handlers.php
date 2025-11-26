<?php
/**
 * Client Portal AJAX Handlers
 *
 * Handles AJAX requests for portal functionality with comprehensive security.
 * All handlers verify nonces, authentication, and user permissions.
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Initialize AJAX handlers
 *
 * Registers all AJAX action hooks for portal functionality.
 * Called during portal initialization.
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_init_ajax() {
	// Document operations
	add_action('wp_ajax_portal_upload_document', 'mydefenselaw_portal_ajax_upload_document');
	add_action('wp_ajax_portal_download_document', 'mydefenselaw_portal_ajax_download_document');
	add_action('wp_ajax_portal_delete_document', 'mydefenselaw_portal_ajax_delete_document');
	add_action('wp_ajax_portal_create_folder', 'mydefenselaw_portal_ajax_create_folder');
	add_action('wp_ajax_portal_move_document', 'mydefenselaw_portal_ajax_move_document');

	// Message operations
	add_action('wp_ajax_portal_send_message', 'mydefenselaw_portal_ajax_send_message');
	add_action('wp_ajax_portal_send_reply', 'mydefenselaw_portal_ajax_send_reply');
	add_action('wp_ajax_portal_get_conversation', 'mydefenselaw_portal_ajax_get_conversation');
	add_action('wp_ajax_portal_mark_read', 'mydefenselaw_portal_ajax_mark_read');
	add_action('wp_ajax_portal_get_messages', 'mydefenselaw_portal_ajax_get_messages');
	add_action('wp_ajax_portal_get_unread_count', 'mydefenselaw_portal_ajax_get_unread_count');

	// Profile operations
	add_action('wp_ajax_portal_update_profile', 'mydefenselaw_portal_ajax_update_profile');
	add_action('wp_ajax_portal_change_password', 'mydefenselaw_portal_ajax_change_password');
	add_action('wp_ajax_portal_update_notifications', 'mydefenselaw_portal_ajax_update_notifications');

	// Dashboard data
	add_action('wp_ajax_portal_get_dashboard_stats', 'mydefenselaw_portal_ajax_get_dashboard_stats');
}

// ==================== DOCUMENT OPERATIONS ====================

/**
 * Upload document via AJAX
 *
 * Security: Verifies nonce, authentication, case access, file validation
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_ajax_upload_document() {
	// 1. Verify nonce
	if (!isset($_POST['nonce']) || !mydefenselaw_portal_verify_nonce($_POST['nonce'], 'upload_document')) {
		wp_send_json_error(array('message' => __('Security check failed', 'mydefenselaw')));
	}

	// 2. Verify user is logged in and is client
	if (!is_user_logged_in()) {
		wp_send_json_error(array('message' => __('Please log in', 'mydefenselaw')));
	}

	$user_id = get_current_user_id();
	$user = wp_get_current_user();

	if (!in_array('client', (array) $user->roles)) {
		wp_send_json_error(array('message' => __('Access denied', 'mydefenselaw')));
	}

	// 3. Check for file
	if (empty($_FILES['document'])) {
		wp_send_json_error(array('message' => __('No file uploaded', 'mydefenselaw')));
	}

	// 4. Get optional parameters
	$case_id = isset($_POST['case_id']) ? absint($_POST['case_id']) : 0;
	$folder_id = isset($_POST['folder_id']) ? absint($_POST['folder_id']) : 0;

	// 5. Verify user can access case if specified
	if ($case_id && !mydefenselaw_portal_can_access_case($user_id, $case_id)) {
		wp_send_json_error(array('message' => __('Access denied to this case', 'mydefenselaw')));
	}

	// 6. Upload document using document-handler.php function
	$result = mydefenselaw_portal_upload_document($_FILES['document'], $user_id, $case_id, $folder_id);

	if (is_wp_error($result)) {
		wp_send_json_error(array('message' => $result->get_error_message()));
	}

	wp_send_json_success(array(
		'message' => __('Document uploaded successfully', 'mydefenselaw'),
		'document_id' => $result,
	));
}

/**
 * Download document (fallback for AJAX calls)
 *
 * Primary download handling is via rewrite rule in document-handler.php
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_ajax_download_document() {
	$document_id = isset($_GET['document_id']) ? absint($_GET['document_id']) : 0;

	if (!$document_id) {
		wp_die(__('Invalid document', 'mydefenselaw'));
	}

	// Use the main download function from document-handler.php
	mydefenselaw_portal_download_document($document_id);
}

/**
 * Delete document
 *
 * Security: Verifies nonce, authentication, ownership
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_ajax_delete_document() {
	// 1. Verify nonce
	if (!isset($_POST['nonce']) || !mydefenselaw_portal_verify_nonce($_POST['nonce'], 'delete_document')) {
		wp_send_json_error(array('message' => __('Security check failed', 'mydefenselaw')));
	}

	// 2. Verify authentication
	if (!is_user_logged_in()) {
		wp_send_json_error(array('message' => __('Please log in', 'mydefenselaw')));
	}

	$user_id = get_current_user_id();

	// 3. Get document ID
	$document_id = isset($_POST['document_id']) ? absint($_POST['document_id']) : 0;

	if (!$document_id) {
		wp_send_json_error(array('message' => __('Invalid document ID', 'mydefenselaw')));
	}

	// 4. Verify document exists
	$document = get_post($document_id);
	if (!$document || $document->post_type !== 'client_document') {
		wp_send_json_error(array('message' => __('Document not found', 'mydefenselaw')));
	}

	// 5. Delete document (function handles permission checks)
	$result = mydefenselaw_portal_delete_document($document_id, $user_id);

	if (is_wp_error($result)) {
		wp_send_json_error(array('message' => $result->get_error_message()));
	}

	wp_send_json_success(array(
		'message' => __('Document deleted successfully', 'mydefenselaw'),
	));
}

/**
 * Create folder
 *
 * Security: Verifies nonce, authentication
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_ajax_create_folder() {
	// 1. Verify nonce
	if (!isset($_POST['nonce']) || !mydefenselaw_portal_verify_nonce($_POST['nonce'], 'create_folder')) {
		wp_send_json_error(array('message' => __('Security check failed', 'mydefenselaw')));
	}

	// 2. Verify authentication
	if (!is_user_logged_in()) {
		wp_send_json_error(array('message' => __('Please log in', 'mydefenselaw')));
	}

	$user_id = get_current_user_id();
	$user = wp_get_current_user();

	if (!in_array('client', (array) $user->roles)) {
		wp_send_json_error(array('message' => __('Access denied', 'mydefenselaw')));
	}

	// 3. Get folder name
	$folder_name = isset($_POST['folder_name']) ? sanitize_text_field($_POST['folder_name']) : '';
	$parent_id = isset($_POST['parent_id']) ? absint($_POST['parent_id']) : 0;

	if (empty($folder_name)) {
		wp_send_json_error(array('message' => __('Folder name is required', 'mydefenselaw')));
	}

	// 4. Create folder
	$result = mydefenselaw_portal_create_folder($folder_name, $user_id, $parent_id);

	if (is_wp_error($result)) {
		wp_send_json_error(array('message' => $result->get_error_message()));
	}

	wp_send_json_success(array(
		'message' => __('Folder created successfully', 'mydefenselaw'),
		'folder_id' => $result,
	));
}

/**
 * Move document to folder
 *
 * Security: Verifies nonce, authentication, ownership
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_ajax_move_document() {
	// 1. Verify nonce
	if (!isset($_POST['nonce']) || !mydefenselaw_portal_verify_nonce($_POST['nonce'], 'move_document')) {
		wp_send_json_error(array('message' => __('Security check failed', 'mydefenselaw')));
	}

	// 2. Verify authentication
	if (!is_user_logged_in()) {
		wp_send_json_error(array('message' => __('Please log in', 'mydefenselaw')));
	}

	$user_id = get_current_user_id();

	// 3. Get parameters
	$document_id = isset($_POST['document_id']) ? absint($_POST['document_id']) : 0;
	$folder_id = isset($_POST['folder_id']) ? absint($_POST['folder_id']) : 0;

	if (!$document_id) {
		wp_send_json_error(array('message' => __('Invalid document ID', 'mydefenselaw')));
	}

	// 4. Verify user can access document
	if (!mydefenselaw_portal_can_access_document($user_id, $document_id)) {
		wp_send_json_error(array('message' => __('Access denied', 'mydefenselaw')));
	}

	// 5. Move document
	$result = mydefenselaw_portal_move_document($document_id, $folder_id);

	if (is_wp_error($result)) {
		wp_send_json_error(array('message' => $result->get_error_message()));
	}

	wp_send_json_success(array(
		'message' => __('Document moved successfully', 'mydefenselaw'),
	));
}

// ==================== MESSAGE OPERATIONS ====================

/**
 * Send new message
 *
 * Security: Verifies nonce, authentication, case access
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_ajax_send_message() {
	// 1. Verify nonce
	if (!isset($_POST['nonce']) || !mydefenselaw_portal_verify_nonce($_POST['nonce'], 'send_message')) {
		wp_send_json_error(array('message' => __('Security check failed', 'mydefenselaw')));
	}

	// 2. Verify authentication
	if (!is_user_logged_in()) {
		wp_send_json_error(array('message' => __('Please log in', 'mydefenselaw')));
	}

	$user_id = get_current_user_id();
	$user = get_userdata($user_id);

	// 3. Get and sanitize message data
	$subject = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';
	$content = isset($_POST['message']) ? wp_kses_post($_POST['message']) : '';
	$case_id = isset($_POST['case_id']) ? absint($_POST['case_id']) : 0;
	$parent_id = isset($_POST['parent_message_id']) ? absint($_POST['parent_message_id']) : 0;

	// 4. Validate
	if (empty($subject) || empty($content)) {
		wp_send_json_error(array('message' => __('Subject and message are required', 'mydefenselaw')));
	}

	// 5. Verify case access if specified
	if ($case_id && !mydefenselaw_portal_can_access_case($user_id, $case_id)) {
		wp_send_json_error(array('message' => __('Access denied', 'mydefenselaw')));
	}

	// 6. Create message post
	$message_id = wp_insert_post(array(
		'post_type' => 'client_message',
		'post_status' => 'publish',
		'post_title' => $subject,
		'post_content' => $content,
		'post_author' => $user_id,
	));

	if (is_wp_error($message_id)) {
		wp_send_json_error(array('message' => __('Failed to send message', 'mydefenselaw')));
	}

	// 7. Save metadata
	update_post_meta($message_id, '_sender_id', $user_id);
	update_post_meta($message_id, '_is_from_client', true);
	update_post_meta($message_id, '_is_read', false);
	update_post_meta($message_id, '_sent_date', current_time('mysql'));

	if ($case_id) {
		update_post_meta($message_id, '_case_id', $case_id);
	}
	if ($parent_id) {
		update_post_meta($message_id, '_parent_message_id', $parent_id);
	}

	// 8. Save ACF fields if available
	if (function_exists('update_field')) {
		update_field('message_client', $user_id, $message_id);
		update_field('is_from_client', true, $message_id);
		update_field('is_read', false, $message_id);
		if ($case_id) {
			update_field('related_case', $case_id, $message_id);
		}
		if ($parent_id) {
			update_field('parent_message', $parent_id, $message_id);
		}
	}

	// 9. Log activity
	mydefenselaw_portal_log_activity('message_sent', 'message', $message_id);

	// 10. Send notification to attorneys (if function exists)
	if (function_exists('mydefenselaw_portal_send_notification')) {
		mydefenselaw_portal_send_notification(
			'new_message',
			$message_id,
			array(
				'client_name' => $user->display_name,
				'subject' => $subject,
			)
		);
	}

	wp_send_json_success(array(
		'message' => __('Message sent successfully', 'mydefenselaw'),
		'message_id' => $message_id,
	));
}

/**
 * Get conversation/thread details
 *
 * Security: Verifies nonce, authentication, thread access
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_ajax_get_conversation() {
	// 1. Verify nonce
	if (!isset($_POST['nonce']) || !mydefenselaw_portal_verify_nonce($_POST['nonce'], 'get_conversation')) {
		wp_send_json_error(array('message' => __('Security check failed', 'mydefenselaw')));
	}

	// 2. Verify authentication
	if (!is_user_logged_in()) {
		wp_send_json_error(array('message' => __('Please log in', 'mydefenselaw')));
	}

	$user_id = get_current_user_id();

	// 3. Get thread ID
	$thread_id = isset($_POST['thread_id']) ? absint($_POST['thread_id']) : 0;

	if (!$thread_id) {
		wp_send_json_error(array('message' => __('Invalid thread ID', 'mydefenselaw')));
	}

	// 4. Verify thread exists
	$thread = get_post($thread_id);
	if (!$thread || $thread->post_type !== 'client_message') {
		wp_send_json_error(array('message' => __('Thread not found', 'mydefenselaw')));
	}

	// 5. Verify user can access this thread
	if (!mydefenselaw_portal_can_access_message($user_id, $thread_id)) {
		wp_send_json_error(array('message' => __('Access denied', 'mydefenselaw')));
	}

	// 6. Get thread subject
	$subject = $thread->post_title;

	// 7. Get thread messages (main thread + replies)
	$messages = array();

	// Add main message
	$is_from_client = get_post_meta($thread_id, '_is_from_client', true);
	$sender_id = get_post_meta($thread_id, '_sender_id', true);
	$sender = get_userdata($sender_id);

	$messages[] = array(
		'id' => $thread_id,
		'sender_type' => $is_from_client ? 'client' : 'staff',
		'sender_name' => $sender ? $sender->display_name : __('Unknown', 'mydefenselaw'),
		'content' => wpautop($thread->post_content),
		'time' => get_the_date('M j, Y g:i a', $thread),
		'timestamp' => strtotime($thread->post_date),
	);

	// Get replies (messages with this thread as parent)
	$replies = get_posts(array(
		'post_type' => 'client_message',
		'posts_per_page' => -1,
		'orderby' => 'date',
		'order' => 'ASC',
		'meta_query' => array(
			array(
				'key' => '_parent_message_id',
				'value' => $thread_id,
				'compare' => '=',
			),
		),
	));

	foreach ($replies as $reply) {
		$reply_is_from_client = get_post_meta($reply->ID, '_is_from_client', true);
		$reply_sender_id = get_post_meta($reply->ID, '_sender_id', true);
		$reply_sender = get_userdata($reply_sender_id);

		$messages[] = array(
			'id' => $reply->ID,
			'sender_type' => $reply_is_from_client ? 'client' : 'staff',
			'sender_name' => $reply_sender ? $reply_sender->display_name : __('Unknown', 'mydefenselaw'),
			'content' => wpautop($reply->post_content),
			'time' => get_the_date('M j, Y g:i a', $reply),
			'timestamp' => strtotime($reply->post_date),
		);
	}

	// 8. Get participants
	$participants = array();
	$client_name = get_userdata($user_id)->display_name;
	$participants[] = $client_name;

	// Add attorney/staff names if available
	$case_id = get_post_meta($thread_id, '_case_id', true);
	if ($case_id && function_exists('get_field')) {
		$assigned_attorney = get_field('assigned_attorney', $case_id);
		if ($assigned_attorney) {
			$attorney = get_userdata($assigned_attorney);
			if ($attorney) {
				$participants[] = $attorney->display_name;
			}
		}
	}

	// 9. Return conversation data
	wp_send_json_success(array(
		'subject' => $subject,
		'participants' => $participants,
		'message_count' => count($messages),
		'messages' => $messages,
	));
}

/**
 * Send reply to existing thread
 *
 * Security: Verifies nonce, authentication, thread access
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_ajax_send_reply() {
	// 1. Verify nonce
	if (!isset($_POST['nonce']) || !mydefenselaw_portal_verify_nonce($_POST['nonce'], 'send_reply')) {
		wp_send_json_error(array('message' => __('Security check failed', 'mydefenselaw')));
	}

	// 2. Verify authentication
	if (!is_user_logged_in()) {
		wp_send_json_error(array('message' => __('Please log in', 'mydefenselaw')));
	}

	$user_id = get_current_user_id();
	$user = get_userdata($user_id);

	// 3. Get and sanitize data
	$thread_id = isset($_POST['thread_id']) ? absint($_POST['thread_id']) : 0;
	$message = isset($_POST['message']) ? wp_kses_post($_POST['message']) : '';

	// 4. Validate
	if (!$thread_id || empty($message)) {
		wp_send_json_error(array('message' => __('Thread ID and message are required', 'mydefenselaw')));
	}

	// 5. Verify thread exists
	$thread = get_post($thread_id);
	if (!$thread || $thread->post_type !== 'client_message') {
		wp_send_json_error(array('message' => __('Thread not found', 'mydefenselaw')));
	}

	// 6. Verify user can access thread
	if (!mydefenselaw_portal_can_access_message($user_id, $thread_id)) {
		wp_send_json_error(array('message' => __('Access denied', 'mydefenselaw')));
	}

	// 7. Create reply as new message post
	$reply_id = wp_insert_post(array(
		'post_type' => 'client_message',
		'post_status' => 'publish',
		'post_title' => 'Re: ' . $thread->post_title,
		'post_content' => $message,
		'post_author' => $user_id,
	));

	if (is_wp_error($reply_id)) {
		wp_send_json_error(array('message' => __('Failed to send reply', 'mydefenselaw')));
	}

	// 8. Save metadata
	update_post_meta($reply_id, '_sender_id', $user_id);
	update_post_meta($reply_id, '_is_from_client', true);
	update_post_meta($reply_id, '_is_read', false);
	update_post_meta($reply_id, '_sent_date', current_time('mysql'));
	update_post_meta($reply_id, '_parent_message_id', $thread_id);

	// Copy case_id from parent thread
	$case_id = get_post_meta($thread_id, '_case_id', true);
	if ($case_id) {
		update_post_meta($reply_id, '_case_id', $case_id);
	}

	// 9. Save ACF fields if available
	if (function_exists('update_field')) {
		update_field('message_client', $user_id, $reply_id);
		update_field('is_from_client', true, $reply_id);
		update_field('is_read', false, $reply_id);
		update_field('parent_message', $thread_id, $reply_id);
		if ($case_id) {
			update_field('related_case', $case_id, $reply_id);
		}
	}

	// 10. Log activity
	mydefenselaw_portal_log_activity('reply_sent', 'message', $reply_id);

	// 11. Send notification to attorneys (if function exists)
	if (function_exists('mydefenselaw_portal_send_notification')) {
		mydefenselaw_portal_send_notification(
			'new_reply',
			$reply_id,
			array(
				'client_name' => $user->display_name,
				'thread_subject' => $thread->post_title,
			)
		);
	}

	wp_send_json_success(array(
		'message' => __('Reply sent successfully', 'mydefenselaw'),
		'reply_id' => $reply_id,
	));
}

/**
 * Mark message/thread as read
 *
 * Security: Verifies nonce, authentication, message access
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_ajax_mark_read() {
	// 1. Verify nonce
	if (!isset($_POST['nonce']) || !mydefenselaw_portal_verify_nonce($_POST['nonce'], 'mark_read')) {
		wp_send_json_error(array('message' => __('Security check failed', 'mydefenselaw')));
	}

	// 2. Verify authentication
	if (!is_user_logged_in()) {
		wp_send_json_error(array('message' => __('Please log in', 'mydefenselaw')));
	}

	$user_id = get_current_user_id();

	// 3. Get thread ID (or message ID for backwards compatibility)
	$thread_id = isset($_POST['thread_id']) ? absint($_POST['thread_id']) : 0;
	if (!$thread_id) {
		$thread_id = isset($_POST['message_id']) ? absint($_POST['message_id']) : 0;
	}

	if (!$thread_id) {
		wp_send_json_error(array('message' => __('Invalid thread ID', 'mydefenselaw')));
	}

	// 4. Verify thread exists
	$thread = get_post($thread_id);
	if (!$thread || $thread->post_type !== 'client_message') {
		wp_send_json_error(array('message' => __('Thread not found', 'mydefenselaw')));
	}

	// 5. Verify user can access this thread
	if (!mydefenselaw_portal_can_access_message($user_id, $thread_id)) {
		wp_send_json_error(array('message' => __('Access denied', 'mydefenselaw')));
	}

	// 6. Mark thread as read
	update_post_meta($thread_id, '_is_read', true);
	update_post_meta($thread_id, '_read_at', current_time('mysql'));
	update_post_meta($thread_id, '_read_by', $user_id);

	if (function_exists('update_field')) {
		update_field('is_read', true, $thread_id);
		update_field('read_at', current_time('Y-m-d H:i:s'), $thread_id);
	}

	// 7. Log activity
	mydefenselaw_portal_log_activity('message_read', 'message', $thread_id);

	wp_send_json_success(array(
		'message' => __('Thread marked as read', 'mydefenselaw'),
	));
}

/**
 * Get messages (for AJAX refresh)
 *
 * Security: Verifies nonce, authentication
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_ajax_get_messages() {
	// 1. Verify nonce
	if (!isset($_POST['nonce']) || !mydefenselaw_portal_verify_nonce($_POST['nonce'], 'get_messages')) {
		wp_send_json_error(array('message' => __('Security check failed', 'mydefenselaw')));
	}

	// 2. Verify authentication
	if (!is_user_logged_in()) {
		wp_send_json_error(array('message' => __('Please log in', 'mydefenselaw')));
	}

	$user_id = get_current_user_id();

	// 3. Get parameters
	$case_id = isset($_POST['case_id']) ? absint($_POST['case_id']) : 0;
	$limit = isset($_POST['limit']) ? absint($_POST['limit']) : 10;
	$offset = isset($_POST['offset']) ? absint($_POST['offset']) : 0;

	// 4. Get messages for user
	$message_ids = mydefenselaw_portal_get_client_messages($user_id, $case_id);

	// Apply limit and offset
	$message_ids = array_slice($message_ids, $offset, $limit);

	// 5. Format messages
	$messages = array();
	foreach ($message_ids as $msg_id) {
		$message = get_post($msg_id);
		if ($message) {
			$messages[] = array(
				'id' => $msg_id,
				'subject' => $message->post_title,
				'excerpt' => wp_trim_words($message->post_content, 20),
				'date' => get_the_date('', $message),
				'is_read' => get_post_meta($msg_id, '_is_read', true),
				'is_from_client' => get_post_meta($msg_id, '_is_from_client', true),
			);
		}
	}

	wp_send_json_success(array(
		'messages' => $messages,
		'total' => count($message_ids),
	));
}

/**
 * Get unread message count (for polling)
 *
 * Security: Verifies authentication
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_ajax_get_unread_count() {
	// 1. Verify authentication
	if (!is_user_logged_in()) {
		wp_send_json_error(array('message' => __('Please log in', 'mydefenselaw')));
	}

	$user_id = get_current_user_id();

	// 2. Get all messages for user
	$message_ids = mydefenselaw_portal_get_client_messages($user_id);

	// 3. Count unread
	$unread = 0;
	foreach ($message_ids as $msg_id) {
		$is_read = get_post_meta($msg_id, '_is_read', true);
		$is_from_client = get_post_meta($msg_id, '_is_from_client', true);

		// Only count messages TO the client (not from them) that are unread
		if (!$is_read && !$is_from_client) {
			$unread++;
		}
	}

	wp_send_json_success(array(
		'unread_count' => $unread,
	));
}

// ==================== PROFILE OPERATIONS ====================

/**
 * Update profile
 *
 * Security: Verifies nonce, authentication
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_ajax_update_profile() {
	// 1. Verify nonce
	if (!isset($_POST['nonce']) || !mydefenselaw_portal_verify_nonce($_POST['nonce'], 'update_profile')) {
		wp_send_json_error(array('message' => __('Security check failed', 'mydefenselaw')));
	}

	// 2. Verify authentication
	if (!is_user_logged_in()) {
		wp_send_json_error(array('message' => __('Please log in', 'mydefenselaw')));
	}

	$user_id = get_current_user_id();

	// 3. Get and sanitize profile data
	$first_name = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
	$last_name = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
	$phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
	$address = isset($_POST['address']) ? sanitize_textarea_field($_POST['address']) : '';

	// 4. Update user data
	wp_update_user(array(
		'ID' => $user_id,
		'first_name' => $first_name,
		'last_name' => $last_name,
	));

	// 5. Update user meta
	update_user_meta($user_id, 'client_phone', $phone);
	update_user_meta($user_id, 'client_address', $address);

	// 6. Update ACF fields if available
	if (function_exists('update_field')) {
		update_field('client_phone', $phone, 'user_' . $user_id);
		update_field('client_address', $address, 'user_' . $user_id);
	}

	// 7. Log activity
	mydefenselaw_portal_log_activity('profile_updated', 'user', $user_id);

	wp_send_json_success(array(
		'message' => __('Profile updated successfully', 'mydefenselaw'),
	));
}

/**
 * Change password
 *
 * Security: Verifies nonce, authentication, current password
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_ajax_change_password() {
	// 1. Verify nonce
	if (!isset($_POST['nonce']) || !mydefenselaw_portal_verify_nonce($_POST['nonce'], 'change_password')) {
		wp_send_json_error(array('message' => __('Security check failed', 'mydefenselaw')));
	}

	// 2. Verify authentication
	if (!is_user_logged_in()) {
		wp_send_json_error(array('message' => __('Please log in', 'mydefenselaw')));
	}

	$user_id = get_current_user_id();
	$user = get_userdata($user_id);

	// 3. Get password data
	$current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
	$new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
	$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

	// 4. Verify current password
	if (!wp_check_password($current_password, $user->user_pass, $user_id)) {
		wp_send_json_error(array('message' => __('Current password is incorrect', 'mydefenselaw')));
	}

	// 5. Validate new password
	if (strlen($new_password) < 8) {
		wp_send_json_error(array('message' => __('Password must be at least 8 characters', 'mydefenselaw')));
	}

	if ($new_password !== $confirm_password) {
		wp_send_json_error(array('message' => __('Passwords do not match', 'mydefenselaw')));
	}

	// 6. Update password
	wp_set_password($new_password, $user_id);

	// 7. Log activity
	mydefenselaw_portal_log_activity('password_changed', 'user', $user_id);

	// 8. Send email notification (if function exists)
	if (function_exists('mydefenselaw_portal_send_notification')) {
		mydefenselaw_portal_send_notification(
			'password_changed',
			$user_id,
			array(
				'user_email' => $user->user_email,
			)
		);
	}

	wp_send_json_success(array(
		'message' => __('Password changed successfully. Please log in again.', 'mydefenselaw'),
		'redirect' => wp_login_url(home_url('/portal/')),
	));
}

/**
 * Update notification preferences
 *
 * Security: Verifies nonce, authentication
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_ajax_update_notifications() {
	// 1. Verify nonce
	if (!isset($_POST['nonce']) || !mydefenselaw_portal_verify_nonce($_POST['nonce'], 'update_notifications')) {
		wp_send_json_error(array('message' => __('Security check failed', 'mydefenselaw')));
	}

	// 2. Verify authentication
	if (!is_user_logged_in()) {
		wp_send_json_error(array('message' => __('Please log in', 'mydefenselaw')));
	}

	$user_id = get_current_user_id();

	// 3. Get notification preferences
	$email_messages = isset($_POST['email_messages']) ? (bool) $_POST['email_messages'] : false;
	$email_documents = isset($_POST['email_documents']) ? (bool) $_POST['email_documents'] : false;
	$email_case_updates = isset($_POST['email_case_updates']) ? (bool) $_POST['email_case_updates'] : false;

	// 4. Build preferences array
	$preferences = array(
		'email_messages' => $email_messages,
		'email_documents' => $email_documents,
		'email_case_updates' => $email_case_updates,
	);

	// 5. Save to user meta
	update_user_meta($user_id, '_notification_preferences', $preferences);

	// 6. Save to ACF if available
	if (function_exists('update_field')) {
		update_field('notification_preferences', $preferences, 'user_' . $user_id);
	}

	// 7. Log activity
	mydefenselaw_portal_log_activity('notification_preferences_updated', 'user', $user_id);

	wp_send_json_success(array(
		'message' => __('Notification preferences updated', 'mydefenselaw'),
	));
}

// ==================== DASHBOARD DATA ====================

/**
 * Get dashboard stats (for refresh)
 *
 * Security: Verifies authentication
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_ajax_get_dashboard_stats() {
	// 1. Verify authentication
	if (!is_user_logged_in()) {
		wp_send_json_error(array('message' => __('Please log in', 'mydefenselaw')));
	}

	$user_id = get_current_user_id();

	// 2. Get counts
	$cases = mydefenselaw_portal_get_client_cases($user_id);
	$documents = mydefenselaw_portal_get_client_documents($user_id);
	$messages = mydefenselaw_portal_get_client_messages($user_id);

	// Count unread messages
	$unread = 0;
	foreach ($messages as $msg_id) {
		$is_read = get_post_meta($msg_id, '_is_read', true);
		$is_from_client = get_post_meta($msg_id, '_is_from_client', true);
		if (!$is_read && !$is_from_client) {
			$unread++;
		}
	}

	// Get upcoming dates (if custom field exists)
	$upcoming_dates = array();
	if (function_exists('get_field')) {
		foreach ($cases as $case_id) {
			$important_dates = get_field('important_dates', $case_id);
			if (is_array($important_dates)) {
				foreach ($important_dates as $date) {
					if (isset($date['date']) && strtotime($date['date']) >= time()) {
						$upcoming_dates[] = array(
							'date' => $date['date'],
							'description' => isset($date['description']) ? $date['description'] : '',
							'case_id' => $case_id,
						);
					}
				}
			}
		}

		// Sort by date
		usort($upcoming_dates, function ($a, $b) {
			return strtotime($a['date']) - strtotime($b['date']);
		});

		// Limit to next 5
		$upcoming_dates = array_slice($upcoming_dates, 0, 5);
	}

	// 3. Return stats
	wp_send_json_success(array(
		'cases_count' => count($cases),
		'documents_count' => count($documents),
		'messages_count' => count($messages),
		'unread_count' => $unread,
		'upcoming_dates' => $upcoming_dates,
	));
}
