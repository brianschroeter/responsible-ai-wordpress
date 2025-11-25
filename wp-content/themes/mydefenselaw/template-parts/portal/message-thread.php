<?php
/**
 * Template Part: Message Thread
 * Single message thread item for sidebar
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

$thread = $args['thread'] ?? array();

if (empty($thread)) {
    return;
}

$is_unread = $thread['is_unread'] ?? false;
$has_attachment = $thread['has_attachment'] ?? false;
$last_message_preview = wp_trim_words($thread['last_message'], 10);
?>

<div class="thread-item <?php echo $is_unread ? 'unread' : ''; ?>"
     data-thread-id="<?php echo esc_attr($thread['id']); ?>"
     data-case-id="<?php echo esc_attr($thread['case_id'] ?? ''); ?>">

    <div class="thread-avatar">
        <?php echo get_avatar($thread['last_sender_id'], 48); ?>
        <?php if ($is_unread) : ?>
            <span class="unread-indicator"></span>
        <?php endif; ?>
    </div>

    <div class="thread-content">
        <div class="thread-header">
            <h4 class="thread-subject"><?php echo esc_html($thread['subject']); ?></h4>
            <span class="thread-time"><?php echo esc_html($thread['time_ago']); ?></span>
        </div>

        <?php if (!empty($thread['case_name'])) : ?>
            <span class="thread-case-label">
                <i class="fas fa-briefcase"></i>
                <?php echo esc_html($thread['case_name']); ?>
            </span>
        <?php endif; ?>

        <div class="thread-preview">
            <span class="thread-sender"><?php echo esc_html($thread['last_sender_name']); ?>:</span>
            <span class="thread-message"><?php echo esc_html($last_message_preview); ?></span>
        </div>

        <div class="thread-meta">
            <span class="thread-message-count">
                <i class="fas fa-comment"></i>
                <?php echo esc_html($thread['message_count']); ?>
            </span>
            <?php if ($has_attachment) : ?>
                <span class="thread-attachment">
                    <i class="fas fa-paperclip"></i>
                </span>
            <?php endif; ?>
        </div>
    </div>
</div>
