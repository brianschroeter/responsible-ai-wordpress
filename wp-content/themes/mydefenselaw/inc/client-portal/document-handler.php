<?php
/**
 * Client Portal Document Handler
 *
 * Handles secure document uploads, downloads, and management.
 * Implements private upload directory, file validation, and access logging.
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 * Initialize document handler
 *
 * Sets up upload directory, rewrite rules, and download handler.
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_init_document_handler() {
	// Setup private upload directory on theme activation
	add_action('after_switch_theme', 'mydefenselaw_portal_setup_upload_directory');

	// Add rewrite rules for secure downloads
	add_action('init', 'mydefenselaw_portal_add_download_rewrite');

	// Handle download requests
	add_action('template_redirect', 'mydefenselaw_portal_handle_download_request');

	// Cleanup on document deletion
	add_action('before_delete_post', 'mydefenselaw_portal_cleanup_document_file');
}
add_action('init', 'mydefenselaw_portal_init_document_handler');

/**
 * Create private upload directory on theme activation
 *
 * Creates directory structure:
 * wp-content/uploads/client-portal/
 * ├── .htaccess (blocks direct access)
 * ├── index.php (additional security)
 * └── {client_id}/
 *     └── documents
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_setup_upload_directory() {
	$upload_dir = wp_upload_dir();
	$portal_dir = $upload_dir['basedir'] . '/client-portal';

	// Create main portal directory
	if (!file_exists($portal_dir)) {
		wp_mkdir_p($portal_dir);
	}

	// Create .htaccess to block direct access
	$htaccess = $portal_dir . '/.htaccess';
	if (!file_exists($htaccess)) {
		$htaccess_content = "# MyDefenseLaw Client Portal - Deny direct access\n";
		$htaccess_content .= "Order deny,allow\n";
		$htaccess_content .= "Deny from all\n";
		file_put_contents($htaccess, $htaccess_content);
	}

	// Create index.php for additional security
	$index = $portal_dir . '/index.php';
	if (!file_exists($index)) {
		file_put_contents($index, '<?php // Silence is golden.');
	}
}

/**
 * Get portal upload directory path
 *
 * @since 1.0.0
 * @return string Absolute path to client-portal directory
 */
function mydefenselaw_portal_get_upload_dir() {
	$upload_dir = wp_upload_dir();
	return $upload_dir['basedir'] . '/client-portal';
}

/**
 * Add rewrite rule for secure document downloads
 *
 * Creates URL: /portal/download/{document_id}/
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_add_download_rewrite() {
	add_rewrite_rule(
		'^portal/download/([0-9]+)/?$',
		'index.php?portal_download=$matches[1]',
		'top'
	);
	add_rewrite_tag('%portal_download%', '([0-9]+)');
}

/**
 * Handle secure document download request
 *
 * Intercepts download URLs and streams files securely.
 *
 * @since 1.0.0
 */
function mydefenselaw_portal_handle_download_request() {
	$document_id = get_query_var('portal_download');

	if ($document_id) {
		mydefenselaw_portal_download_document(intval($document_id));
	}
}

/**
 * Handle document upload via AJAX
 *
 * Validates file, moves to private directory, creates post, logs activity.
 *
 * @since 1.0.0
 * @param array $file $_FILES array element
 * @param int   $client_id User ID of client
 * @param int   $case_id Optional related case ID
 * @param int   $folder_id Optional folder ID
 * @return int|WP_Error Document post ID or error
 */
function mydefenselaw_portal_upload_document($file, $client_id, $case_id = 0, $folder_id = 0) {
	// 1. Validate file
	$validation = mydefenselaw_portal_validate_file($file);
	if (is_wp_error($validation)) {
		return $validation;
	}

	// 2. Generate unique filename
	$filename = mydefenselaw_portal_generate_filename($file['name'], $client_id);

	// 3. Move file to private directory (organized by client ID)
	$upload_path = mydefenselaw_portal_get_upload_dir() . '/' . $client_id;
	if (!file_exists($upload_path)) {
		wp_mkdir_p($upload_path);
		// Add index.php to client directory
		file_put_contents($upload_path . '/index.php', '<?php // Silence is golden.');
	}

	$filepath = $upload_path . '/' . $filename;
	if (!move_uploaded_file($file['tmp_name'], $filepath)) {
		return new WP_Error('upload_failed', __('Failed to save file to server', 'mydefenselaw'));
	}

	// 4. Create client_document post
	$doc_id = wp_insert_post(array(
		'post_type'   => 'client_document',
		'post_status' => 'publish',
		'post_title'  => sanitize_file_name($file['name']),
		'post_author' => get_current_user_id(),
	));

	if (is_wp_error($doc_id)) {
		// Cleanup file if post creation failed
		unlink($filepath);
		return $doc_id;
	}

	// 5. Save metadata
	// ACF fields (will be set via ACF)
	if (function_exists('update_field')) {
		update_field('document_client', $client_id, $doc_id);
		if ($case_id) {
			update_field('related_case', $case_id, $doc_id);
		}
		if ($folder_id) {
			update_field('document_folder', $folder_id, $doc_id);
		}
		update_field('is_client_upload', true, $doc_id);
		update_field('upload_date', current_time('Y-m-d H:i:s'), $doc_id);
	}

	// Standard post meta (not ACF - used for file handling)
	update_post_meta($doc_id, '_file_path', $filepath);
	update_post_meta($doc_id, '_file_type', $file['type']);
	update_post_meta($doc_id, '_file_size', $file['size']);
	update_post_meta($doc_id, '_original_filename', $file['name']);
	update_post_meta($doc_id, '_client_id', $client_id);
	if ($case_id) {
		update_post_meta($doc_id, '_case_id', $case_id);
	}

	// Get client name for display
	$client = get_user_by('id', $client_id);
	if ($client) {
		update_post_meta($doc_id, '_client_name', $client->display_name);
	}

	// 6. Log activity
	mydefenselaw_portal_log_activity('document_uploaded', 'document', $doc_id);

	return $doc_id;
}

/**
 * Validate uploaded file
 *
 * Checks file size, MIME type, and extension against whitelist.
 *
 * @since 1.0.0
 * @param array $file $_FILES array element
 * @return bool|WP_Error True if valid, WP_Error if invalid
 */
function mydefenselaw_portal_validate_file($file) {
	// Check for upload errors
	if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
		$error_messages = array(
			UPLOAD_ERR_INI_SIZE   => __('File exceeds server upload limit', 'mydefenselaw'),
			UPLOAD_ERR_FORM_SIZE  => __('File exceeds form upload limit', 'mydefenselaw'),
			UPLOAD_ERR_PARTIAL    => __('File was only partially uploaded', 'mydefenselaw'),
			UPLOAD_ERR_NO_FILE    => __('No file was uploaded', 'mydefenselaw'),
			UPLOAD_ERR_NO_TMP_DIR => __('Missing temporary upload folder', 'mydefenselaw'),
			UPLOAD_ERR_CANT_WRITE => __('Failed to write file to disk', 'mydefenselaw'),
			UPLOAD_ERR_EXTENSION  => __('File upload stopped by extension', 'mydefenselaw'),
		);
		$message = isset($error_messages[$file['error']])
			? $error_messages[$file['error']]
			: __('Unknown upload error', 'mydefenselaw');
		return new WP_Error('upload_error', $message);
	}

	// Check file size (default 10MB, configurable via ACF options)
	$max_size = 10 * 1024 * 1024; // 10MB default
	if (function_exists('get_field')) {
		$setting = get_field('portal_max_file_size', 'option');
		if ($setting && is_numeric($setting)) {
			$max_size = intval($setting) * 1024 * 1024; // Convert MB to bytes
		}
	}

	if ($file['size'] > $max_size) {
		$max_mb = round($max_size / 1024 / 1024);
		return new WP_Error(
			'file_too_large',
			sprintf(__('File exceeds maximum size of %d MB', 'mydefenselaw'), $max_mb)
		);
	}

	// Validate MIME type using finfo (more secure than trusting $_FILES['type'])
	if (!file_exists($file['tmp_name'])) {
		return new WP_Error('invalid_file', __('Uploaded file not found', 'mydefenselaw'));
	}

	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$mime_type = finfo_file($finfo, $file['tmp_name']);
	finfo_close($finfo);

	// Allowed MIME types
	$allowed_mimes = array(
		'application/pdf',
		'application/msword',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'application/vnd.ms-excel',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'image/jpeg',
		'image/png',
		'image/gif',
		'text/plain',
	);

	// Allow customization via filter
	$allowed_mimes = apply_filters('mydefenselaw_portal_allowed_mimes', $allowed_mimes);

	if (!in_array($mime_type, $allowed_mimes)) {
		return new WP_Error(
			'invalid_type',
			__('File type not allowed. Allowed types: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF, TXT', 'mydefenselaw')
		);
	}

	// Validate extension (double-check)
	$allowed_extensions = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'txt');
	$allowed_extensions = apply_filters('mydefenselaw_portal_allowed_extensions', $allowed_extensions);

	$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
	if (!in_array($ext, $allowed_extensions)) {
		return new WP_Error(
			'invalid_extension',
			__('File extension not allowed', 'mydefenselaw')
		);
	}

	// Additional security: check for double extensions (e.g., file.php.pdf)
	$name_parts = explode('.', $file['name']);
	if (count($name_parts) > 2) {
		// Check if any part before the last extension is a dangerous extension
		$dangerous_exts = array('php', 'phtml', 'php3', 'php4', 'php5', 'pl', 'py', 'jsp', 'asp', 'aspx', 'cgi', 'exe', 'sh');
		array_pop($name_parts); // Remove last (legitimate) extension

		foreach ($name_parts as $part) {
			if (in_array(strtolower($part), $dangerous_exts)) {
				return new WP_Error(
					'suspicious_filename',
					__('Suspicious filename detected', 'mydefenselaw')
				);
			}
		}
	}

	return true;
}

/**
 * Generate unique secure filename
 *
 * Format: doc_{client_id}_{uuid}.{ext}
 *
 * @since 1.0.0
 * @param string $original_name Original filename
 * @param int    $client_id Client user ID
 * @return string Unique filename
 */
function mydefenselaw_portal_generate_filename($original_name, $client_id) {
	$ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

	// Generate UUID-like string
	$uuid = sprintf(
		'%04x%04x%04x',
		mt_rand(0, 0xffff),
		mt_rand(0, 0xffff),
		mt_rand(0, 0xffff)
	);

	// Add timestamp for additional uniqueness
	$timestamp = time();

	return sprintf('doc_%d_%s_%d.%s', $client_id, $uuid, $timestamp, $ext);
}

/**
 * Handle secure file download
 *
 * Verifies ownership, logs access, streams file with proper headers.
 *
 * @since 1.0.0
 * @param int $document_id Document post ID
 * @return void Streams file or dies with error
 */
function mydefenselaw_portal_download_document($document_id) {
	// 1. Verify user is logged in
	if (!is_user_logged_in()) {
		wp_die(
			__('You must be logged in to download documents.', 'mydefenselaw'),
			__('Unauthorized', 'mydefenselaw'),
			array('response' => 401)
		);
	}

	$user_id = get_current_user_id();

	// 2. Verify document exists
	$document = get_post($document_id);
	if (!$document || $document->post_type !== 'client_document') {
		wp_die(
			__('Document not found.', 'mydefenselaw'),
			__('Not Found', 'mydefenselaw'),
			array('response' => 404)
		);
	}

	// 3. Verify user can access this document
	if (!mydefenselaw_portal_can_access_document($user_id, $document_id)) {
		mydefenselaw_portal_log_activity('unauthorized_access_attempt', 'document', $document_id);
		wp_die(
			__('You do not have permission to access this document.', 'mydefenselaw'),
			__('Access Denied', 'mydefenselaw'),
			array('response' => 403)
		);
	}

	// 4. Get file path
	$filepath = get_post_meta($document_id, '_file_path', true);

	if (!$filepath) {
		wp_die(
			__('File path not found.', 'mydefenselaw'),
			__('Error', 'mydefenselaw'),
			array('response' => 500)
		);
	}

	// 5. Validate file path (prevent directory traversal attacks)
	$real_path = realpath($filepath);
	$upload_dir = realpath(mydefenselaw_portal_get_upload_dir());

	// Ensure file is within the upload directory
	if (!$real_path || strpos($real_path, $upload_dir) !== 0) {
		mydefenselaw_portal_log_activity('suspicious_download_attempt', 'document', $document_id);
		wp_die(
			__('Invalid file path.', 'mydefenselaw'),
			__('Security Error', 'mydefenselaw'),
			array('response' => 403)
		);
	}

	// 6. Verify file exists
	if (!file_exists($real_path)) {
		wp_die(
			__('File not found on server.', 'mydefenselaw'),
			__('File Not Found', 'mydefenselaw'),
			array('response' => 404)
		);
	}

	// 7. Log download activity
	mydefenselaw_portal_log_activity('document_downloaded', 'document', $document_id);

	// 8. Update access log in ACF repeater
	mydefenselaw_portal_log_document_access($document_id, 'downloaded');

	// 9. Stream file with proper headers
	$filename = get_post_meta($document_id, '_original_filename', true);
	if (!$filename) {
		$filename = basename($filepath);
	}

	$mime_type = get_post_meta($document_id, '_file_type', true);
	if (!$mime_type) {
		$mime_type = 'application/octet-stream';
	}

	// Security headers
	header('Content-Type: ' . $mime_type);
	header('Content-Disposition: attachment; filename="' . sanitize_file_name($filename) . '"');
	header('Content-Length: ' . filesize($real_path));
	header('Cache-Control: no-cache, no-store, must-revalidate');
	header('Pragma: no-cache');
	header('Expires: 0');
	header('X-Content-Type-Options: nosniff');
	header('X-Frame-Options: DENY');

	// Disable output buffering for large files
	if (ob_get_level()) {
		ob_end_clean();
	}

	// Stream file
	readfile($real_path);
	exit;
}

/**
 * Check if user can access a document
 *
 * Clients can only access their own documents.
 * Administrators and attorneys can access all documents.
 *
 * @since 1.0.0
 * @param int $user_id User ID
 * @param int $document_id Document post ID
 * @return bool True if user can access, false otherwise
 */
if (!function_exists('mydefenselaw_portal_can_access_document')) {
function mydefenselaw_portal_can_access_document($user_id, $document_id) {
	$user = get_user_by('id', $user_id);
	if (!$user) {
		return false;
	}

	// Administrators can access all documents
	if (in_array('administrator', $user->roles)) {
		return true;
	}

	// Attorneys can access all documents
	if (in_array('attorney', $user->roles)) {
		return true;
	}

	// Clients can only access their own documents
	if (in_array('client', $user->roles)) {
		$doc_client_id = get_post_meta($document_id, '_client_id', true);
		return intval($doc_client_id) === $user_id;
	}

	return false;
}
}

/**
 * Log document access to ACF repeater field
 *
 * Maintains last 100 access entries per document.
 *
 * @since 1.0.0
 * @param int    $document_id Document post ID
 * @param string $action Action performed (viewed, downloaded, etc.)
 */
function mydefenselaw_portal_log_document_access($document_id, $action) {
	if (!function_exists('get_field') || !function_exists('update_field')) {
		return;
	}

	$access_log = get_field('access_log', $document_id);
	if (!is_array($access_log)) {
		$access_log = array();
	}

	// Limit to last 100 entries
	if (count($access_log) >= 100) {
		array_shift($access_log);
	}

	// Add new entry
	$access_log[] = array(
		'access_date' => current_time('Y-m-d H:i:s'),
		'access_ip'   => mydefenselaw_portal_get_ip(),
		'user_id'     => get_current_user_id(),
		'action'      => sanitize_text_field($action),
	);

	update_field('access_log', $access_log, $document_id);

	// Update last accessed timestamp
	update_field('last_accessed', current_time('Y-m-d H:i:s'), $document_id);
}

/**
 * Get client IP address
 *
 * Handles proxies and load balancers.
 *
 * @since 1.0.0
 * @return string IP address
 */
if (!function_exists('mydefenselaw_portal_get_ip')) {
function mydefenselaw_portal_get_ip() {
	$ip_keys = array(
		'HTTP_CLIENT_IP',
		'HTTP_X_FORWARDED_FOR',
		'HTTP_X_FORWARDED',
		'HTTP_X_CLUSTER_CLIENT_IP',
		'HTTP_FORWARDED_FOR',
		'HTTP_FORWARDED',
		'REMOTE_ADDR'
	);

	foreach ($ip_keys as $key) {
		if (array_key_exists($key, $_SERVER) === true) {
			foreach (explode(',', $_SERVER[$key]) as $ip) {
				$ip = trim($ip);

				// Validate IP
				if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
					return $ip;
				}
			}
		}
	}

	return 'Unknown';
}
}

/**
 * Create document folder
 *
 * @since 1.0.0
 * @param string $name Folder name
 * @param int    $client_id Client user ID
 * @param int    $parent_id Optional parent folder ID
 * @return int|WP_Error Folder post ID or error
 */
function mydefenselaw_portal_create_folder($name, $client_id, $parent_id = 0) {
	// Verify client owns parent folder if specified
	if ($parent_id) {
		$parent_client = get_post_meta($parent_id, '_client_id', true);
		if (intval($parent_client) !== $client_id) {
			return new WP_Error('invalid_parent', __('Invalid parent folder', 'mydefenselaw'));
		}
	}

	// Create client_folder post
	$folder_id = wp_insert_post(array(
		'post_type'   => 'client_folder',
		'post_status' => 'publish',
		'post_title'  => sanitize_text_field($name),
		'post_author' => get_current_user_id(),
	));

	if (is_wp_error($folder_id)) {
		return $folder_id;
	}

	// Save metadata
	update_post_meta($folder_id, '_client_id', $client_id);
	if ($parent_id) {
		update_post_meta($folder_id, '_parent_folder', $parent_id);
	}

	// ACF fields
	if (function_exists('update_field')) {
		update_field('folder_client', $client_id, $folder_id);
		if ($parent_id) {
			update_field('parent_folder', $parent_id, $folder_id);
		}
	}

	// Log activity
	mydefenselaw_portal_log_activity('folder_created', 'folder', $folder_id);

	return $folder_id;
}

/**
 * Get folders for client
 *
 * @since 1.0.0
 * @param int $client_id Client user ID
 * @param int $parent_id Optional parent folder ID (0 for root)
 * @return array Array of folder posts
 */
function mydefenselaw_portal_get_client_folders($client_id, $parent_id = 0) {
	$args = array(
		'post_type'      => 'client_folder',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'meta_query'     => array(
			array(
				'key'   => '_client_id',
				'value' => $client_id,
				'type'  => 'NUMERIC',
			),
		),
		'orderby'        => 'title',
		'order'          => 'ASC',
	);

	// Filter by parent
	if ($parent_id) {
		$args['meta_query'][] = array(
			'key'   => '_parent_folder',
			'value' => $parent_id,
			'type'  => 'NUMERIC',
		);
	} else {
		$args['meta_query'][] = array(
			'key'     => '_parent_folder',
			'compare' => 'NOT EXISTS',
		);
	}

	return get_posts($args);
}

/**
 * Move document to folder
 *
 * @since 1.0.0
 * @param int $document_id Document post ID
 * @param int $folder_id Folder post ID (0 to move to root)
 * @return bool|WP_Error True on success, WP_Error on failure
 */
function mydefenselaw_portal_move_document($document_id, $folder_id) {
	// Verify document exists
	$document = get_post($document_id);
	if (!$document || $document->post_type !== 'client_document') {
		return new WP_Error('invalid_document', __('Invalid document', 'mydefenselaw'));
	}

	// Get document's client
	$doc_client_id = get_post_meta($document_id, '_client_id', true);

	// If moving to folder, verify folder exists and belongs to same client
	if ($folder_id) {
		$folder = get_post($folder_id);
		if (!$folder || $folder->post_type !== 'client_folder') {
			return new WP_Error('invalid_folder', __('Invalid folder', 'mydefenselaw'));
		}

		$folder_client_id = get_post_meta($folder_id, '_client_id', true);
		if (intval($folder_client_id) !== intval($doc_client_id)) {
			return new WP_Error('client_mismatch', __('Document and folder belong to different clients', 'mydefenselaw'));
		}

		update_post_meta($document_id, '_folder_id', $folder_id);

		if (function_exists('update_field')) {
			update_field('document_folder', $folder_id, $document_id);
		}
	} else {
		// Move to root (remove folder)
		delete_post_meta($document_id, '_folder_id');

		if (function_exists('update_field')) {
			update_field('document_folder', null, $document_id);
		}
	}

	// Log activity
	mydefenselaw_portal_log_activity('document_moved', 'document', $document_id);

	return true;
}

/**
 * Delete document (soft delete - moves to trash)
 *
 * Only allows deletion of client's own uploads by clients.
 * Attorneys and admins can delete any document.
 *
 * @since 1.0.0
 * @param int $document_id Document post ID
 * @param int $user_id User requesting deletion
 * @return bool|WP_Error True on success, WP_Error on failure
 */
function mydefenselaw_portal_delete_document($document_id, $user_id) {
	// Verify document exists
	$document = get_post($document_id);
	if (!$document || $document->post_type !== 'client_document') {
		return new WP_Error('invalid_document', __('Invalid document', 'mydefenselaw'));
	}

	$user = get_user_by('id', $user_id);
	if (!$user) {
		return new WP_Error('invalid_user', __('Invalid user', 'mydefenselaw'));
	}

	// Check permissions
	$can_delete = false;

	if (in_array('administrator', $user->roles) || in_array('attorney', $user->roles)) {
		// Admins and attorneys can delete any document
		$can_delete = true;
	} elseif (in_array('client', $user->roles)) {
		// Clients can only delete their own uploads
		$doc_client_id = get_post_meta($document_id, '_client_id', true);
		$is_client_upload = get_post_meta($document_id, '_is_client_upload', true);

		if (function_exists('get_field')) {
			$is_client_upload = get_field('is_client_upload', $document_id);
		}

		if (intval($doc_client_id) === $user_id && $is_client_upload) {
			$can_delete = true;
		}
	}

	if (!$can_delete) {
		return new WP_Error('permission_denied', __('You do not have permission to delete this document', 'mydefenselaw'));
	}

	// Log activity before deletion
	mydefenselaw_portal_log_activity('document_deleted', 'document', $document_id);

	// Move to trash (soft delete)
	$result = wp_trash_post($document_id);

	if (!$result) {
		return new WP_Error('delete_failed', __('Failed to delete document', 'mydefenselaw'));
	}

	return true;
}

/**
 * Cleanup document file when post is permanently deleted
 *
 * @since 1.0.0
 * @param int $post_id Post ID being deleted
 */
function mydefenselaw_portal_cleanup_document_file($post_id) {
	$post = get_post($post_id);

	// Only handle client_document posts
	if (!$post || $post->post_type !== 'client_document') {
		return;
	}

	// Only delete file if post is being permanently deleted (not just trashed)
	if ($post->post_status === 'trash') {
		return;
	}

	// Get file path
	$filepath = get_post_meta($post_id, '_file_path', true);

	if ($filepath && file_exists($filepath)) {
		// Verify file is in the portal directory (security check)
		$real_path = realpath($filepath);
		$upload_dir = realpath(mydefenselaw_portal_get_upload_dir());

		if ($real_path && strpos($real_path, $upload_dir) === 0) {
			unlink($real_path);
		}
	}
}

/**
 * Get document file size formatted
 *
 * @since 1.0.0
 * @param int $document_id Document post ID
 * @return string Formatted file size
 */
function mydefenselaw_portal_get_document_size($document_id) {
	$size = get_post_meta($document_id, '_file_size', true);

	if (!$size) {
		return __('Unknown', 'mydefenselaw');
	}

	return size_format($size, 2);
}

/**
 * Get document download count
 *
 * @since 1.0.0
 * @param int $document_id Document post ID
 * @return int Download count
 */
function mydefenselaw_portal_get_download_count($document_id) {
	if (!function_exists('get_field')) {
		return 0;
	}

	$access_log = get_field('access_log', $document_id);
	if (!is_array($access_log)) {
		return 0;
	}

	$count = 0;
	foreach ($access_log as $entry) {
		if (isset($entry['action']) && $entry['action'] === 'downloaded') {
			$count++;
		}
	}

	return $count;
}
