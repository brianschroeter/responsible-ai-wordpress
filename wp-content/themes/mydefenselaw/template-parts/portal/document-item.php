<?php
/**
 * Template Part: Document Item
 * Document list/grid item component
 *
 * Expected variable: $document (WP_Post object)
 * Optional: $view_mode ('grid' or 'list', defaults to 'grid')
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

// Ensure we have a document object
if (!isset($document) || !is_object($document)) {
	return;
}

$view_mode = $view_mode ?? 'grid';
$document_id = $document->ID;
$user_id = get_current_user_id();

// Get document metadata
$file_url = get_post_meta($document_id, '_file_url', true);
$file_path = get_post_meta($document_id, '_file_path', true);
$file_size = get_post_meta($document_id, '_file_size', true);
$file_type = get_post_meta($document_id, '_file_type', true);
$custom_name = get_post_meta($document_id, '_custom_name', true);
$related_case_id = get_post_meta($document_id, '_related_case_id', true);
$uploader_id = get_post_meta($document_id, '_uploader_id', true);
$upload_date = get_the_date('', $document);

// Determine display name
$display_name = $custom_name ?: $document->post_title;

// Get file extension
$file_extension = '';
if ($file_url) {
	$file_extension = strtolower(pathinfo($file_url, PATHINFO_EXTENSION));
}

// Map extension to Font Awesome icon
$icon_map = array(
	'pdf'  => 'fa-file-pdf',
	'doc'  => 'fa-file-word',
	'docx' => 'fa-file-word',
	'xls'  => 'fa-file-excel',
	'xlsx' => 'fa-file-excel',
	'jpg'  => 'fa-file-image',
	'jpeg' => 'fa-file-image',
	'png'  => 'fa-file-image',
	'gif'  => 'fa-file-image',
	'zip'  => 'fa-file-archive',
	'rar'  => 'fa-file-archive',
	'txt'  => 'fa-file-alt',
);
$icon_class = $icon_map[$file_extension] ?? 'fa-file';

// Format file size
$formatted_size = '';
if ($file_size) {
	if ($file_size < 1024) {
		$formatted_size = $file_size . ' B';
	} elseif ($file_size < 1024 * 1024) {
		$formatted_size = round($file_size / 1024, 1) . ' KB';
	} else {
		$formatted_size = round($file_size / (1024 * 1024), 1) . ' MB';
	}
}

// Get related case name
$case_name = '';
if ($related_case_id) {
	$case = get_post($related_case_id);
	if ($case) {
		$case_number = get_post_meta($related_case_id, '_case_number', true);
		$case_name = $case_number ?: $case->post_title;
	}
}

// Check if user is owner (can delete)
$is_owner = ($uploader_id == $user_id) || current_user_can('manage_options');

// Generate secure download URL
$download_url = wp_nonce_url(
	add_query_arg(array(
		'action' => 'mydefenselaw_portal_download_document',
		'document_id' => $document_id,
	), admin_url('admin-ajax.php')),
	'download_document_' . $document_id
);

// View mode rendering
if ($view_mode === 'list') :
	// LIST VIEW
	?>
	<div class="document-list-item portal-document-item"
	     data-document-id="<?php echo esc_attr($document_id); ?>"
	     data-type="<?php echo esc_attr($file_extension); ?>">

		<div class="document-list-icon file-icon <?php echo esc_attr($file_extension); ?>">
			<i class="fas <?php echo esc_attr($icon_class); ?>"></i>
		</div>

		<div class="document-list-name">
			<strong><?php echo esc_html($display_name); ?></strong>
			<?php if ($case_name) : ?>
				<span style="display: block; font-size: 0.8rem; color: var(--color-text-light); margin-top: 0.25rem;">
					<i class="fas fa-briefcase"></i> <?php echo esc_html($case_name); ?>
				</span>
			<?php endif; ?>
		</div>

		<div class="document-list-size">
			<?php echo esc_html($formatted_size); ?>
		</div>

		<div class="document-list-date">
			<?php echo esc_html($upload_date); ?>
		</div>

		<div class="document-list-actions">
			<a href="<?php echo esc_url($download_url); ?>"
			   class="document-action-btn download portal-btn-icon"
			   title="<?php esc_attr_e('Download', 'mydefenselaw'); ?>"
			   download>
				<i class="fas fa-download"></i>
			</a>
			<?php if ($file_extension === 'pdf') : ?>
				<button type="button"
				        class="document-action-btn view portal-btn-icon"
				        data-document-url="<?php echo esc_attr($file_url); ?>"
				        title="<?php esc_attr_e('View', 'mydefenselaw'); ?>">
					<i class="fas fa-eye"></i>
				</button>
			<?php endif; ?>
			<?php if ($is_owner) : ?>
				<button type="button"
				        class="document-action-btn delete portal-btn-icon"
				        data-document-id="<?php echo esc_attr($document_id); ?>"
				        title="<?php esc_attr_e('Delete', 'mydefenselaw'); ?>">
					<i class="fas fa-trash"></i>
				</button>
			<?php endif; ?>
		</div>
	</div>
	<?php
else :
	// GRID VIEW (default)
	?>
	<div class="document-card portal-document-item"
	     data-document-id="<?php echo esc_attr($document_id); ?>"
	     data-type="<?php echo esc_attr($file_extension); ?>">

		<div class="document-card-icon file-icon <?php echo esc_attr($file_extension); ?>">
			<i class="fas <?php echo esc_attr($icon_class); ?>"></i>
		</div>

		<h4 class="document-card-name">
			<?php echo esc_html($display_name); ?>
		</h4>

		<div class="document-card-meta">
			<?php if ($formatted_size) : ?>
				<span><?php echo esc_html($formatted_size); ?></span>
				<span>•</span>
			<?php endif; ?>
			<span><?php echo esc_html($upload_date); ?></span>
		</div>

		<?php if ($case_name) : ?>
			<div class="document-card-case" style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--color-border); font-size: 0.8rem; color: var(--color-text-light);">
				<i class="fas fa-briefcase"></i> <?php echo esc_html($case_name); ?>
			</div>
		<?php endif; ?>

		<div class="document-card-actions">
			<a href="<?php echo esc_url($download_url); ?>"
			   class="document-action-btn download"
			   download>
				<i class="fas fa-download"></i>
				<?php esc_html_e('Download', 'mydefenselaw'); ?>
			</a>
			<?php if ($is_owner) : ?>
				<button type="button"
				        class="document-action-btn delete"
				        data-document-id="<?php echo esc_attr($document_id); ?>">
					<i class="fas fa-trash"></i>
					<?php esc_html_e('Delete', 'mydefenselaw'); ?>
				</button>
			<?php endif; ?>
		</div>
	</div>
	<?php
endif;
?>
