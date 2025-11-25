<?php
/**
 * Template Name: Portal - Dashboard
 * Client portal dashboard with stats, activities, and quick actions
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

// Get dashboard data
$user_id = get_current_user_id();
$first_name = get_user_meta($user_id, 'first_name', true) ?: wp_get_current_user()->display_name;

// Get stats (use helper functions if they exist, otherwise use placeholders)
$active_cases = function_exists('mydefenselaw_portal_get_user_cases') ? count(mydefenselaw_portal_get_user_cases($user_id, 'active')) : 0;
$total_documents = function_exists('mydefenselaw_portal_get_user_documents_count') ? mydefenselaw_portal_get_user_documents_count($user_id) : 0;
$unread_messages = function_exists('mydefenselaw_portal_get_unread_message_count') ? mydefenselaw_portal_get_unread_message_count($user_id) : 0;
$upcoming_dates = function_exists('mydefenselaw_portal_get_upcoming_court_dates') ? count(mydefenselaw_portal_get_upcoming_court_dates($user_id, 30)) : 0;
?>

<!-- Portal Dashboard -->
<div class="portal-content">
    <div class="container">
        <!-- Welcome Banner -->
        <div class="portal-welcome-banner">
            <div class="welcome-content">
                <h1><?php printf(esc_html__('Welcome back, %s', 'mydefenselaw'), esc_html($first_name)); ?></h1>
                <p><?php esc_html_e('Here\'s an overview of your legal matters', 'mydefenselaw'); ?></p>
            </div>
            <div class="welcome-actions">
                <button class="btn btn-primary" data-action="new-message">
                    <i class="fas fa-envelope"></i>
                    <?php esc_html_e('Send Message', 'mydefenselaw'); ?>
                </button>
                <button class="btn btn-secondary" data-action="upload-document">
                    <i class="fas fa-upload"></i>
                    <?php esc_html_e('Upload Document', 'mydefenselaw'); ?>
                </button>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        <div class="portal-stats-grid">
            <div class="stat-card stat-primary">
                <div class="stat-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label"><?php esc_html_e('Active Cases', 'mydefenselaw'); ?></span>
                    <span class="stat-value" data-stat="cases"><?php echo esc_html($active_cases); ?></span>
                </div>
                <a href="<?php echo esc_url(home_url('/portal/cases/')); ?>" class="stat-link">
                    <?php esc_html_e('View Cases', 'mydefenselaw'); ?>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="stat-card stat-info">
                <div class="stat-icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label"><?php esc_html_e('Documents', 'mydefenselaw'); ?></span>
                    <span class="stat-value" data-stat="documents"><?php echo esc_html($total_documents); ?></span>
                </div>
                <a href="<?php echo esc_url(home_url('/portal/documents/')); ?>" class="stat-link">
                    <?php esc_html_e('View Documents', 'mydefenselaw'); ?>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="stat-card stat-warning <?php echo $unread_messages > 0 ? 'has-badge' : ''; ?>">
                <div class="stat-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label"><?php esc_html_e('Unread Messages', 'mydefenselaw'); ?></span>
                    <span class="stat-value" data-stat="messages"><?php echo esc_html($unread_messages); ?></span>
                </div>
                <a href="<?php echo esc_url(home_url('/portal/messages/')); ?>" class="stat-link">
                    <?php esc_html_e('View Messages', 'mydefenselaw'); ?>
                    <i class="fas fa-arrow-right"></i>
                </a>
                <?php if ($unread_messages > 0) : ?>
                    <span class="stat-badge"><?php echo esc_html($unread_messages); ?></span>
                <?php endif; ?>
            </div>

            <div class="stat-card stat-success">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label"><?php esc_html_e('Upcoming Dates', 'mydefenselaw'); ?></span>
                    <span class="stat-value" data-stat="dates"><?php echo esc_html($upcoming_dates); ?></span>
                </div>
                <a href="<?php echo esc_url(home_url('/portal/cases/')); ?>" class="stat-link">
                    <?php esc_html_e('View Calendar', 'mydefenselaw'); ?>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="portal-dashboard-grid">
            <!-- Recent Activity -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2><i class="fas fa-history"></i> <?php esc_html_e('Recent Activity', 'mydefenselaw'); ?></h2>
                </div>
                <div class="card-body">
                    <?php
                    // Get recent activity
                    $recent_activity = function_exists('mydefenselaw_portal_get_recent_activity')
                        ? mydefenselaw_portal_get_recent_activity($user_id, 10)
                        : array();

                    if (!empty($recent_activity)) :
                    ?>
                        <ul class="activity-list">
                            <?php foreach ($recent_activity as $activity) : ?>
                                <li class="activity-item" data-type="<?php echo esc_attr($activity['type']); ?>">
                                    <div class="activity-icon">
                                        <i class="fas fa-<?php echo esc_attr($activity['icon'] ?? 'circle'); ?>"></i>
                                    </div>
                                    <div class="activity-content">
                                        <p class="activity-text"><?php echo wp_kses_post($activity['text']); ?></p>
                                        <span class="activity-time"><?php echo esc_html($activity['time_ago']); ?></span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p><?php esc_html_e('No recent activity', 'mydefenselaw'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Upcoming Court Dates -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2><i class="fas fa-calendar-check"></i> <?php esc_html_e('Upcoming Court Dates', 'mydefenselaw'); ?></h2>
                </div>
                <div class="card-body">
                    <?php
                    $court_dates = function_exists('mydefenselaw_portal_get_upcoming_court_dates')
                        ? mydefenselaw_portal_get_upcoming_court_dates($user_id, 60)
                        : array();

                    if (!empty($court_dates)) :
                    ?>
                        <ul class="court-dates-list">
                            <?php foreach ($court_dates as $date) :
                                $days_until = $date['days_until'];
                                $is_urgent = $days_until <= 7;
                            ?>
                                <li class="court-date-item <?php echo $is_urgent ? 'urgent' : ''; ?>">
                                    <div class="date-badge">
                                        <span class="date-day"><?php echo esc_html(date('d', strtotime($date['date']))); ?></span>
                                        <span class="date-month"><?php echo esc_html(date('M', strtotime($date['date']))); ?></span>
                                    </div>
                                    <div class="date-details">
                                        <h4><?php echo esc_html($date['title']); ?></h4>
                                        <p><?php echo esc_html($date['case_name']); ?></p>
                                        <span class="date-countdown">
                                            <?php printf(esc_html__('In %d days', 'mydefenselaw'), $days_until); ?>
                                        </span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar"></i>
                            <p><?php esc_html_e('No upcoming court dates', 'mydefenselaw'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Documents -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2><i class="fas fa-file-alt"></i> <?php esc_html_e('Recent Documents', 'mydefenselaw'); ?></h2>
                    <a href="<?php echo esc_url(home_url('/portal/documents/')); ?>" class="card-action">
                        <?php esc_html_e('View All', 'mydefenselaw'); ?>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <?php
                    $recent_docs = function_exists('mydefenselaw_portal_get_recent_documents')
                        ? mydefenselaw_portal_get_recent_documents($user_id, 5)
                        : array();

                    if (!empty($recent_docs)) :
                    ?>
                        <ul class="documents-list">
                            <?php foreach ($recent_docs as $doc) : ?>
                                <li class="document-item">
                                    <div class="document-icon">
                                        <i class="fas fa-file-<?php echo esc_attr($doc['icon'] ?? 'alt'); ?>"></i>
                                    </div>
                                    <div class="document-info">
                                        <h4><?php echo esc_html($doc['name']); ?></h4>
                                        <p><?php echo esc_html($doc['case_name'] ?? __('General', 'mydefenselaw')); ?> &bull; <?php echo esc_html($doc['date']); ?></p>
                                    </div>
                                    <a href="<?php echo esc_url($doc['download_url']); ?>" class="document-download" download>
                                        <i class="fas fa-download"></i>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <p><?php esc_html_e('No documents yet', 'mydefenselaw'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Messages -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2><i class="fas fa-comments"></i> <?php esc_html_e('Recent Messages', 'mydefenselaw'); ?></h2>
                    <a href="<?php echo esc_url(home_url('/portal/messages/')); ?>" class="card-action">
                        <?php esc_html_e('View All', 'mydefenselaw'); ?>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <?php
                    $recent_messages = function_exists('mydefenselaw_portal_get_recent_messages')
                        ? mydefenselaw_portal_get_recent_messages($user_id, 5)
                        : array();

                    if (!empty($recent_messages)) :
                    ?>
                        <ul class="messages-list">
                            <?php foreach ($recent_messages as $message) : ?>
                                <li class="message-item <?php echo $message['is_unread'] ? 'unread' : ''; ?>">
                                    <div class="message-avatar">
                                        <?php echo get_avatar($message['sender_id'], 40); ?>
                                    </div>
                                    <div class="message-content">
                                        <h4>
                                            <?php echo esc_html($message['sender_name']); ?>
                                            <?php if ($message['is_unread']) : ?>
                                                <span class="unread-badge"><?php esc_html_e('New', 'mydefenselaw'); ?></span>
                                            <?php endif; ?>
                                        </h4>
                                        <p><?php echo esc_html(wp_trim_words($message['message'], 15)); ?></p>
                                        <span class="message-time"><?php echo esc_html($message['time_ago']); ?></span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p><?php esc_html_e('No messages yet', 'mydefenselaw'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer('portal');
?>
