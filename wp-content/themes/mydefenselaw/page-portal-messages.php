<?php
/**
 * Template Name: Portal - Messages
 * Gmail-style three-pane messaging interface
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

// Require authentication
if (function_exists('mydefenselaw_portal_require_auth')) {
    mydefenselaw_portal_require_auth();
}

// Get current client
$client = null;
if (function_exists('mydefenselaw_portal_get_current_client')) {
    $client = mydefenselaw_portal_get_current_client();
}

if (!$client) {
    wp_redirect(home_url('/portal/login/'));
    exit;
}

get_header('portal');

$user_id = get_current_user_id();

// Get message threads
$message_threads = function_exists('mydefenselaw_portal_get_message_threads')
    ? mydefenselaw_portal_get_message_threads($user_id)
    : array();

// Get user's cases for filtering
$user_cases = function_exists('mydefenselaw_portal_get_user_cases')
    ? mydefenselaw_portal_get_user_cases($user_id)
    : array();
?>

<!-- Portal Messages Page -->
<div class="portal-content">
    <div class="container-fluid">
        <!-- Messages Layout (Three-Pane Gmail Style) -->
        <div class="messages-layout">
            <!-- Left Sidebar - Threads List -->
            <aside class="messages-sidebar">
                <div class="messages-sidebar-header">
                    <h2><i class="fas fa-envelope"></i> <?php esc_html_e('Messages', 'mydefenselaw'); ?></h2>
                    <button class="btn btn-primary btn-sm" id="composeMessageBtn">
                        <i class="fas fa-plus"></i>
                        <?php esc_html_e('New Message', 'mydefenselaw'); ?>
                    </button>
                </div>

                <div class="messages-filters">
                    <select id="caseFilterMessages" class="form-control">
                        <option value=""><?php esc_html_e('All Cases', 'mydefenselaw'); ?></option>
                        <?php foreach ($user_cases as $case) : ?>
                            <option value="<?php echo esc_attr($case['id']); ?>">
                                <?php echo esc_html($case['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="messageSearch" placeholder="<?php esc_attr_e('Search messages...', 'mydefenselaw'); ?>">
                    </div>
                </div>

                <div class="messages-threads" id="messageThreads">
                    <?php if (!empty($message_threads)) : ?>
                        <?php foreach ($message_threads as $thread) : ?>
                            <?php get_template_part('template-parts/portal/message-thread', null, array('thread' => $thread)); ?>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p><?php esc_html_e('No messages yet', 'mydefenselaw'); ?></p>
                            <button class="btn btn-primary btn-sm" id="composeMessageBtnEmpty">
                                <i class="fas fa-plus"></i>
                                <?php esc_html_e('Send First Message', 'mydefenselaw'); ?>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </aside>

            <!-- Main Content - Selected Thread -->
            <main class="messages-main" id="messagesMain">
                <div class="messages-placeholder">
                    <i class="fas fa-comments"></i>
                    <h3><?php esc_html_e('Select a conversation', 'mydefenselaw'); ?></h3>
                    <p><?php esc_html_e('Choose a message thread from the left to view the conversation', 'mydefenselaw'); ?></p>
                </div>

                <!-- Thread Content (populated dynamically) -->
                <div class="thread-content" id="threadContent" style="display: none;">
                    <div class="thread-header">
                        <div class="thread-info">
                            <h3 id="threadSubject"></h3>
                            <div class="thread-meta">
                                <span id="threadCase"></span>
                                <span id="threadParticipants"></span>
                            </div>
                        </div>
                        <button class="btn-icon" id="refreshThread" title="<?php esc_attr_e('Refresh', 'mydefenselaw'); ?>">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>

                    <div class="thread-messages" id="threadMessages">
                        <!-- Messages loaded here via AJAX or template -->
                    </div>

                    <div class="thread-reply">
                        <form id="replyForm" class="reply-form">
                            <?php wp_nonce_field('portal_send_message', 'message_nonce'); ?>
                            <input type="hidden" name="thread_id" id="replyThreadId">
                            <input type="hidden" name="action" value="mydefenselaw_portal_send_message">

                            <div class="reply-input-wrapper">
                                <textarea
                                    name="message"
                                    id="replyMessage"
                                    rows="3"
                                    class="form-control"
                                    placeholder="<?php esc_attr_e('Type your message...', 'mydefenselaw'); ?>"
                                    required></textarea>
                            </div>

                            <div class="reply-actions">
                                <button type="button" class="btn-icon" id="attachFileBtn" title="<?php esc_attr_e('Attach file', 'mydefenselaw'); ?>">
                                    <i class="fas fa-paperclip"></i>
                                </button>
                                <div class="reply-actions-right">
                                    <button type="button" class="btn btn-secondary" id="clearReply">
                                        <?php esc_html_e('Clear', 'mydefenselaw'); ?>
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i>
                                        <?php esc_html_e('Send', 'mydefenselaw'); ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<!-- Compose New Message Modal -->
<div id="composeModal" class="portal-modal">
    <div class="portal-modal-content modal-large">
        <div class="portal-modal-header">
            <h3><i class="fas fa-edit"></i> <?php esc_html_e('New Message', 'mydefenselaw'); ?></h3>
            <button class="modal-close" id="closeComposeModal" aria-label="<?php esc_attr_e('Close', 'mydefenselaw'); ?>">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="portal-modal-body">
            <form id="composeForm" class="compose-form">
                <?php wp_nonce_field('portal_send_message', 'compose_nonce'); ?>
                <input type="hidden" name="action" value="mydefenselaw_portal_send_message">

                <div class="form-group">
                    <label for="composeCase">
                        <i class="fas fa-briefcase"></i>
                        <?php esc_html_e('Select Case', 'mydefenselaw'); ?>
                    </label>
                    <select name="case_id" id="composeCase" class="form-control" required>
                        <option value=""><?php esc_html_e('Choose a case...', 'mydefenselaw'); ?></option>
                        <?php foreach ($user_cases as $case) : ?>
                            <option value="<?php echo esc_attr($case['id']); ?>">
                                <?php echo esc_html($case['name']); ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="general"><?php esc_html_e('General Inquiry', 'mydefenselaw'); ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="composeSubject">
                        <i class="fas fa-heading"></i>
                        <?php esc_html_e('Subject', 'mydefenselaw'); ?>
                    </label>
                    <input type="text" name="subject" id="composeSubject" class="form-control" placeholder="<?php esc_attr_e('Message subject', 'mydefenselaw'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="composeMessage">
                        <i class="fas fa-comment"></i>
                        <?php esc_html_e('Message', 'mydefenselaw'); ?>
                    </label>
                    <textarea name="message" id="composeMessage" rows="8" class="form-control" placeholder="<?php esc_attr_e('Type your message...', 'mydefenselaw'); ?>" required></textarea>
                </div>

                <div class="form-group">
                    <label>
                        <i class="fas fa-paperclip"></i>
                        <?php esc_html_e('Attachments (optional)', 'mydefenselaw'); ?>
                    </label>
                    <div class="file-upload-zone" id="composeFileZone">
                        <input type="file" name="attachments[]" id="composeAttachments" multiple style="display: none;">
                        <button type="button" class="btn btn-secondary" id="selectFilesBtn">
                            <i class="fas fa-file"></i>
                            <?php esc_html_e('Select Files', 'mydefenselaw'); ?>
                        </button>
                        <p class="file-help-text"><?php esc_html_e('Max 10MB per file. Supported: PDF, DOC, DOCX, JPG, PNG', 'mydefenselaw'); ?></p>
                        <div id="selectedFiles" class="selected-files"></div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="cancelCompose">
                        <?php esc_html_e('Cancel', 'mydefenselaw'); ?>
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        <?php esc_html_e('Send Message', 'mydefenselaw'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Messages interface JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Compose modal
    const composeModal = document.getElementById('composeModal');
    const composeBtn = document.getElementById('composeMessageBtn');
    const composeBtnEmpty = document.getElementById('composeMessageBtnEmpty');
    const closeComposeModal = document.getElementById('closeComposeModal');
    const cancelCompose = document.getElementById('cancelCompose');

    [composeBtn, composeBtnEmpty].forEach(btn => {
        if (btn) {
            btn.addEventListener('click', () => {
                composeModal.classList.add('active');
            });
        }
    });

    [closeComposeModal, cancelCompose].forEach(btn => {
        if (btn) {
            btn.addEventListener('click', () => {
                composeModal.classList.remove('active');
            });
        }
    });

    // Thread selection
    document.addEventListener('click', function(e) {
        const threadItem = e.target.closest('.thread-item');
        if (threadItem) {
            // Remove active from all threads
            document.querySelectorAll('.thread-item').forEach(t => t.classList.remove('active'));

            // Add active to clicked thread
            threadItem.classList.add('active');

            // Load thread content
            const threadId = threadItem.dataset.threadId;
            loadThreadContent(threadId);

            // Mark as read
            threadItem.classList.remove('unread');
        }
    });

    // Load thread content
    function loadThreadContent(threadId) {
        const placeholder = document.querySelector('.messages-placeholder');
        const threadContent = document.getElementById('threadContent');

        // Hide placeholder, show content
        placeholder.style.display = 'none';
        threadContent.style.display = 'flex';

        // Set thread ID for reply form
        document.getElementById('replyThreadId').value = threadId;

        // Load messages via AJAX
        fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                action: 'mydefenselaw_portal_get_thread',
                thread_id: threadId,
                nonce: '<?php echo wp_create_nonce('portal_get_thread'); ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update thread header
                document.getElementById('threadSubject').textContent = data.data.subject;
                document.getElementById('threadCase').innerHTML = '<i class="fas fa-briefcase"></i> ' + data.data.case_name;
                document.getElementById('threadParticipants').innerHTML = '<i class="fas fa-users"></i> ' + data.data.participants;

                // Update messages
                document.getElementById('threadMessages').innerHTML = data.data.messages_html;

                // Scroll to bottom
                const messagesContainer = document.getElementById('threadMessages');
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        });
    }

    // Reply form submission
    const replyForm = document.getElementById('replyForm');
    if (replyForm) {
        replyForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + '<?php esc_html_e('Sending...', 'mydefenselaw'); ?>';
            submitBtn.disabled = true;

            const formData = new FormData(this);

            fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear message
                    document.getElementById('replyMessage').value = '';

                    // Reload thread
                    const threadId = document.getElementById('replyThreadId').value;
                    loadThreadContent(threadId);
                } else {
                    alert(data.data.message || '<?php esc_html_e('Failed to send message', 'mydefenselaw'); ?>');
                }

                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    // Compose form submission
    const composeForm = document.getElementById('composeForm');
    if (composeForm) {
        composeForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + '<?php esc_html_e('Sending...', 'mydefenselaw'); ?>';
            submitBtn.disabled = true;

            const formData = new FormData(this);

            fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal
                    composeModal.classList.remove('active');

                    // Clear form
                    composeForm.reset();

                    // Reload thread list
                    location.reload();
                } else {
                    alert(data.data.message || '<?php esc_html_e('Failed to send message', 'mydefenselaw'); ?>');
                }

                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    // File selection
    const selectFilesBtn = document.getElementById('selectFilesBtn');
    const composeAttachments = document.getElementById('composeAttachments');

    if (selectFilesBtn && composeAttachments) {
        selectFilesBtn.addEventListener('click', () => {
            composeAttachments.click();
        });

        composeAttachments.addEventListener('change', function() {
            const selectedFiles = document.getElementById('selectedFiles');
            selectedFiles.innerHTML = '';

            Array.from(this.files).forEach(file => {
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                fileItem.innerHTML = `
                    <i class="fas fa-file"></i>
                    <span>${file.name}</span>
                    <span class="file-size">(${(file.size / 1024).toFixed(1)} KB)</span>
                `;
                selectedFiles.appendChild(fileItem);
            });
        });
    }
});
</script>

<?php
get_footer('portal');
?>
