<?php
/**
 * Template Name: Portal - Cases
 * View and manage client cases with timeline and updates
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

// Get user's cases
$user_cases = function_exists('mydefenselaw_portal_get_user_cases')
    ? mydefenselaw_portal_get_user_cases($user_id)
    : array();
?>

<!-- Portal Cases Page -->
<div class="portal-content">
    <div class="container">
        <!-- Page Header -->
        <div class="portal-page-header">
            <div class="page-header-content">
                <h1><i class="fas fa-briefcase"></i> <?php esc_html_e('My Cases', 'mydefenselaw'); ?></h1>
                <p><?php esc_html_e('View your active and past legal matters', 'mydefenselaw'); ?></p>
            </div>
        </div>

        <!-- Cases Filter Tabs -->
        <div class="cases-filter-tabs">
            <button class="filter-tab active" data-status="all">
                <?php esc_html_e('All Cases', 'mydefenselaw'); ?>
                <span class="tab-count"><?php echo count($user_cases); ?></span>
            </button>
            <button class="filter-tab" data-status="active">
                <?php esc_html_e('Active', 'mydefenselaw'); ?>
                <span class="tab-count">
                    <?php echo count(array_filter($user_cases, function($c) { return $c['status'] === 'active'; })); ?>
                </span>
            </button>
            <button class="filter-tab" data-status="pending">
                <?php esc_html_e('Pending', 'mydefenselaw'); ?>
                <span class="tab-count">
                    <?php echo count(array_filter($user_cases, function($c) { return $c['status'] === 'pending'; })); ?>
                </span>
            </button>
            <button class="filter-tab" data-status="closed">
                <?php esc_html_e('Closed', 'mydefenselaw'); ?>
                <span class="tab-count">
                    <?php echo count(array_filter($user_cases, function($c) { return $c['status'] === 'closed'; })); ?>
                </span>
            </button>
        </div>

        <!-- Cases Grid -->
        <div class="cases-grid" id="casesGrid">
            <?php if (!empty($user_cases)) : ?>
                <?php foreach ($user_cases as $case) :
                    // Determine status badge color
                    $status_class = 'status-' . strtolower($case['status']);

                    // Check if court date is within 7 days
                    $has_upcoming_date = false;
                    $days_until_court = null;
                    if (!empty($case['next_court_date'])) {
                        $court_date = strtotime($case['next_court_date']);
                        $now = time();
                        $days_until_court = floor(($court_date - $now) / (60 * 60 * 24));
                        $has_upcoming_date = $days_until_court <= 7 && $days_until_court >= 0;
                    }
                ?>
                    <div class="case-card" data-case-id="<?php echo esc_attr($case['id']); ?>" data-status="<?php echo esc_attr($case['status']); ?>">
                        <div class="case-card-header">
                            <div class="case-title-section">
                                <h3 class="case-title"><?php echo esc_html($case['name']); ?></h3>
                                <span class="case-number"><?php printf(esc_html__('Case #%s', 'mydefenselaw'), $case['number']); ?></span>
                            </div>
                            <span class="case-status-badge <?php echo esc_attr($status_class); ?>">
                                <?php echo esc_html(ucfirst($case['status'])); ?>
                            </span>
                        </div>

                        <div class="case-card-body">
                            <div class="case-meta-grid">
                                <div class="case-meta-item">
                                    <i class="fas fa-scale-balanced"></i>
                                    <div>
                                        <span class="meta-label"><?php esc_html_e('Case Type', 'mydefenselaw'); ?></span>
                                        <span class="meta-value"><?php echo esc_html($case['type']); ?></span>
                                    </div>
                                </div>

                                <div class="case-meta-item">
                                    <i class="fas fa-user-tie"></i>
                                    <div>
                                        <span class="meta-label"><?php esc_html_e('Attorney', 'mydefenselaw'); ?></span>
                                        <span class="meta-value"><?php echo esc_html($case['attorney_name'] ?? __('Unassigned', 'mydefenselaw')); ?></span>
                                    </div>
                                </div>

                                <div class="case-meta-item">
                                    <i class="fas fa-calendar-plus"></i>
                                    <div>
                                        <span class="meta-label"><?php esc_html_e('Filed Date', 'mydefenselaw'); ?></span>
                                        <span class="meta-value"><?php echo esc_html($case['filed_date']); ?></span>
                                    </div>
                                </div>

                                <?php if (!empty($case['next_court_date'])) : ?>
                                    <div class="case-meta-item <?php echo $has_upcoming_date ? 'highlight-urgent' : ''; ?>">
                                        <i class="fas fa-gavel"></i>
                                        <div>
                                            <span class="meta-label"><?php esc_html_e('Next Court Date', 'mydefenselaw'); ?></span>
                                            <span class="meta-value">
                                                <?php echo esc_html(date('M d, Y', strtotime($case['next_court_date']))); ?>
                                                <?php if ($has_upcoming_date) : ?>
                                                    <span class="countdown-badge"><?php printf(esc_html__('In %d days', 'mydefenselaw'), $days_until_court); ?></span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="case-stats">
                                <div class="case-stat">
                                    <i class="fas fa-folder"></i>
                                    <span><?php echo esc_html($case['document_count'] ?? 0); ?> <?php esc_html_e('Documents', 'mydefenselaw'); ?></span>
                                </div>
                                <div class="case-stat">
                                    <i class="fas fa-comments"></i>
                                    <span><?php echo esc_html($case['message_count'] ?? 0); ?> <?php esc_html_e('Messages', 'mydefenselaw'); ?></span>
                                </div>
                            </div>

                            <button class="btn-expand-case" data-case-id="<?php echo esc_attr($case['id']); ?>">
                                <i class="fas fa-chevron-down"></i>
                                <?php esc_html_e('View Details', 'mydefenselaw'); ?>
                            </button>
                        </div>

                        <!-- Expandable Case Details -->
                        <div class="case-details" id="caseDetails<?php echo esc_attr($case['id']); ?>" style="display: none;">
                            <div class="case-details-tabs">
                                <button class="details-tab active" data-tab="timeline" data-case-id="<?php echo esc_attr($case['id']); ?>">
                                    <i class="fas fa-clock"></i>
                                    <?php esc_html_e('Timeline', 'mydefenselaw'); ?>
                                </button>
                                <button class="details-tab" data-tab="documents" data-case-id="<?php echo esc_attr($case['id']); ?>">
                                    <i class="fas fa-folder-open"></i>
                                    <?php esc_html_e('Documents', 'mydefenselaw'); ?>
                                </button>
                                <button class="details-tab" data-tab="messages" data-case-id="<?php echo esc_attr($case['id']); ?>">
                                    <i class="fas fa-envelope"></i>
                                    <?php esc_html_e('Messages', 'mydefenselaw'); ?>
                                </button>
                                <button class="details-tab" data-tab="info" data-case-id="<?php echo esc_attr($case['id']); ?>">
                                    <i class="fas fa-info-circle"></i>
                                    <?php esc_html_e('Case Info', 'mydefenselaw'); ?>
                                </button>
                            </div>

                            <div class="case-details-content">
                                <!-- Timeline Tab -->
                                <div class="tab-content active" id="timeline<?php echo esc_attr($case['id']); ?>">
                                    <?php
                                    $timeline = $case['timeline'] ?? array();
                                    if (!empty($timeline)) :
                                    ?>
                                        <div class="case-timeline">
                                            <?php foreach ($timeline as $event) : ?>
                                                <div class="timeline-item">
                                                    <div class="timeline-marker">
                                                        <i class="fas fa-<?php echo esc_attr($event['icon'] ?? 'circle'); ?>"></i>
                                                    </div>
                                                    <div class="timeline-content">
                                                        <div class="timeline-header">
                                                            <h4><?php echo esc_html($event['title']); ?></h4>
                                                            <span class="timeline-date"><?php echo esc_html($event['date']); ?></span>
                                                        </div>
                                                        <p><?php echo esc_html($event['description']); ?></p>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else : ?>
                                        <div class="empty-state">
                                            <i class="fas fa-clock"></i>
                                            <p><?php esc_html_e('No timeline events yet', 'mydefenselaw'); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Documents Tab -->
                                <div class="tab-content" id="documents<?php echo esc_attr($case['id']); ?>" style="display: none;">
                                    <?php
                                    $case_docs = $case['documents'] ?? array();
                                    if (!empty($case_docs)) :
                                    ?>
                                        <ul class="case-documents-list">
                                            <?php foreach ($case_docs as $doc) : ?>
                                                <li class="case-document-item">
                                                    <i class="fas fa-file-<?php echo esc_attr($doc['icon'] ?? 'alt'); ?>"></i>
                                                    <span><?php echo esc_html($doc['name']); ?></span>
                                                    <span class="doc-date"><?php echo esc_html($doc['date']); ?></span>
                                                    <a href="<?php echo esc_url($doc['download_url']); ?>" class="btn-icon" download>
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else : ?>
                                        <div class="empty-state">
                                            <i class="fas fa-folder-open"></i>
                                            <p><?php esc_html_e('No documents for this case', 'mydefenselaw'); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Messages Tab -->
                                <div class="tab-content" id="messages<?php echo esc_attr($case['id']); ?>" style="display: none;">
                                    <div class="case-messages-preview">
                                        <p><?php esc_html_e('View all messages related to this case', 'mydefenselaw'); ?></p>
                                        <a href="<?php echo esc_url(add_query_arg('case', $case['id'], home_url('/portal/messages/'))); ?>" class="btn btn-primary">
                                            <i class="fas fa-envelope"></i>
                                            <?php esc_html_e('Go to Messages', 'mydefenselaw'); ?>
                                        </a>
                                    </div>
                                </div>

                                <!-- Case Info Tab -->
                                <div class="tab-content" id="info<?php echo esc_attr($case['id']); ?>" style="display: none;">
                                    <div class="case-info-grid">
                                        <?php if (!empty($case['description'])) : ?>
                                            <div class="info-section">
                                                <h4><?php esc_html_e('Case Description', 'mydefenselaw'); ?></h4>
                                                <p><?php echo wp_kses_post($case['description']); ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($case['court_info'])) : ?>
                                            <div class="info-section">
                                                <h4><?php esc_html_e('Court Information', 'mydefenselaw'); ?></h4>
                                                <p><?php echo esc_html($case['court_info']); ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($case['opposing_counsel'])) : ?>
                                            <div class="info-section">
                                                <h4><?php esc_html_e('Opposing Counsel', 'mydefenselaw'); ?></h4>
                                                <p><?php echo esc_html($case['opposing_counsel']); ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="empty-state-large">
                    <i class="fas fa-briefcase"></i>
                    <h3><?php esc_html_e('No Cases Yet', 'mydefenselaw'); ?></h3>
                    <p><?php esc_html_e('You don\'t have any cases assigned yet. Contact us if you have questions.', 'mydefenselaw'); ?></p>
                    <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9]/', '', get_theme_mod('contact_phone', '888.444.0253'))); ?>" class="btn btn-primary">
                        <i class="fas fa-phone"></i>
                        <?php esc_html_e('Contact Us', 'mydefenselaw'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Cases page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Filter tabs
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            // Update active tab
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const status = this.dataset.status;

            // Filter cases
            document.querySelectorAll('.case-card').forEach(card => {
                if (status === 'all' || card.dataset.status === status) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Expand/collapse case details
    document.querySelectorAll('.btn-expand-case').forEach(btn => {
        btn.addEventListener('click', function() {
            const caseId = this.dataset.caseId;
            const details = document.getElementById('caseDetails' + caseId);
            const icon = this.querySelector('i');

            if (details.style.display === 'none') {
                details.style.display = 'block';
                icon.className = 'fas fa-chevron-up';
                this.innerHTML = '<i class="fas fa-chevron-up"></i> <?php esc_html_e('Hide Details', 'mydefenselaw'); ?>';
            } else {
                details.style.display = 'none';
                icon.className = 'fas fa-chevron-down';
                this.innerHTML = '<i class="fas fa-chevron-down"></i> <?php esc_html_e('View Details', 'mydefenselaw'); ?>';
            }
        });
    });

    // Details tabs
    document.querySelectorAll('.details-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const caseId = this.dataset.caseId;
            const tabName = this.dataset.tab;

            // Update active tab
            this.closest('.case-details-tabs').querySelectorAll('.details-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Show corresponding content
            const detailsContainer = this.closest('.case-details');
            detailsContainer.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
                content.classList.remove('active');
            });

            const targetContent = document.getElementById(tabName + caseId);
            if (targetContent) {
                targetContent.style.display = 'block';
                targetContent.classList.add('active');
            }
        });
    });
});
</script>

<?php
get_footer('portal');
?>
