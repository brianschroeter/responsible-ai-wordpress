<?php
/**
 * ACF Sync Helper Functions
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Auto-sync ACF field groups from JSON
 * This ensures field groups are automatically imported when theme is activated
 */
function responsibleai_acf_auto_sync() {
    if (!function_exists('acf_get_field_groups') || !function_exists('acf_synchronise_field_group')) {
        return;
    }

    // Get all field groups from JSON files
    $groups = acf_get_field_groups();
    $sync = array();

    foreach ($groups as $group) {
        // Check if this group needs syncing (local version differs from database)
        if (!empty($group['local'])) {
            $sync[$group['key']] = $group;
        }
    }

    // Sync each group
    if (!empty($sync)) {
        foreach ($sync as $key => $group) {
            if (empty($group['ID'])) {
                acf_import_field_group($group);
            }
        }
    }
}
add_action('admin_init', 'responsibleai_acf_auto_sync');

/**
 * Get ACF field with fallback
 *
 * @param string $field_name Field name
 * @param mixed  $post_id    Post ID or 'option' for options page
 * @param mixed  $default    Default value if field is empty
 * @return mixed Field value or default
 */
function responsibleai_get_field($field_name, $post_id = false, $default = '') {
    if (!function_exists('get_field')) {
        return $default;
    }

    $value = get_field($field_name, $post_id);

    return $value !== null && $value !== '' && $value !== false ? $value : $default;
}

/**
 * Get ACF repeater field with fallback
 *
 * @param string $field_name Field name
 * @param mixed  $post_id    Post ID or 'option' for options page
 * @return array Repeater rows or empty array
 */
function responsibleai_get_repeater($field_name, $post_id = false) {
    if (!function_exists('get_field')) {
        return array();
    }

    $value = get_field($field_name, $post_id);

    return is_array($value) ? $value : array();
}

/**
 * Check if ACF field has value
 *
 * @param string $field_name Field name
 * @param mixed  $post_id    Post ID or 'option' for options page
 * @return bool True if field has value
 */
function responsibleai_has_field($field_name, $post_id = false) {
    if (!function_exists('get_field')) {
        return false;
    }

    $value = get_field($field_name, $post_id);

    if (is_array($value)) {
        return !empty($value);
    }

    return $value !== null && $value !== '' && $value !== false;
}

/**
 * Get ACF image URL with size
 *
 * @param string|array $image     Image field value (array or ID)
 * @param string       $size      Image size name
 * @param string       $fallback  Fallback URL if no image
 * @return string Image URL
 */
function responsibleai_get_image_url($image, $size = 'full', $fallback = '') {
    if (empty($image)) {
        return $fallback;
    }

    // If it's an array (return format: array)
    if (is_array($image)) {
        if (isset($image['sizes'][$size])) {
            return $image['sizes'][$size];
        }
        return isset($image['url']) ? $image['url'] : $fallback;
    }

    // If it's an ID (return format: id)
    if (is_numeric($image)) {
        $url = wp_get_attachment_image_url($image, $size);
        return $url ? $url : $fallback;
    }

    // If it's a URL (return format: url)
    if (is_string($image) && filter_var($image, FILTER_VALIDATE_URL)) {
        return $image;
    }

    return $fallback;
}

/**
 * Output ACF textarea with auto paragraphs
 *
 * @param string $content Textarea content
 * @return string Content with paragraphs
 */
function responsibleai_format_textarea($content) {
    if (empty($content)) {
        return '';
    }
    return wpautop($content);
}
