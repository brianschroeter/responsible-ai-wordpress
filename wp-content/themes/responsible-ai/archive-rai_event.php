<?php
/**
 * Archive Template for Events (rai_event)
 *
 * Displays a filterable archive of events with multiple view options
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get filter parameters from URL
$event_filter = isset($_GET['event_filter']) ? sanitize_text_field($_GET['event_filter']) : 'upcoming';
$event_type_filter = isset($_GET['event_type']) ? sanitize_text_field($_GET['event_type']) : '';
$view_mode = isset($_GET['view']) ? sanitize_text_field($_GET['view']) : 'list';
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// Build query arguments
$current_date = current_time('Y-m-d H:i:s');
$args = array(
    'post_type'      => 'rai_event',
    'posts_per_page' => 12,
    'paged'          => $paged,
    'meta_key'       => 'event_start_date',
    'orderby'        => 'meta_value',
);

// Filter by time period
if ($event_filter === 'upcoming') {
    $args['meta_query'] = array(
        array(
            'key'     => 'event_start_date',
            'value'   => $current_date,
            'compare' => '>=',
            'type'    => 'DATETIME',
        ),
    );
    $args['order'] = 'ASC';
} elseif ($event_filter === 'past') {
    $args['meta_query'] = array(
        array(
            'key'     => 'event_start_date',
            'value'   => $current_date,
            'compare' => '<',
            'type'    => 'DATETIME',
        ),
    );
    $args['order'] = 'DESC';
} else {
    // All events
    $args['order'] = 'DESC';
}

// Filter by event type
if (!empty($event_type_filter)) {
    if (!isset($args['meta_query'])) {
        $args['meta_query'] = array();
    }
    $args['meta_query'][] = array(
        'key'     => 'event_type',
        'value'   => $event_type_filter,
        'compare' => '=',
    );
}

// Query events
$events_query = new WP_Query($args);

// Get featured events (for upcoming filter only)
$featured_events = array();
if ($event_filter === 'upcoming') {
    $featured_args = array(
        'post_type'      => 'rai_event',
        'posts_per_page' => 3,
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => 'event_start_date',
                'value'   => $current_date,
                'compare' => '>=',
                'type'    => 'DATETIME',
            ),
            array(
                'key'     => 'event_is_featured',
                'value'   => '1',
                'compare' => '=',
            ),
        ),
        'meta_key'       => 'event_start_date',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
    );
    $featured_query = new WP_Query($featured_args);
    if ($featured_query->have_posts()) {
        while ($featured_query->have_posts()) {
            $featured_query->the_post();
            $featured_events[] = get_the_ID();
        }
    }
    wp_reset_postdata();
}
?>

<div class="events-archive">
    <!-- Hero Section -->
    <section class="page-hero page-hero--events">
        <div class="container">
            <div class="page-hero__content">
                <h1 class="page-hero__title">
                    <?php esc_html_e('Events', 'responsible-ai'); ?>
                </h1>
                <p class="page-hero__subtitle">
                    <?php esc_html_e('Join us for webinars, conferences, workshops, and community events focused on responsible AI development and deployment.', 'responsible-ai'); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Filters Section -->
    <section class="events-filters">
        <div class="container">
            <div class="events-filters__wrapper">
                <!-- Time Filter Tabs -->
                <div class="events-filters__tabs">
                    <a href="<?php echo esc_url(add_query_arg(array('event_filter' => 'upcoming', 'event_type' => $event_type_filter, 'view' => $view_mode), get_post_type_archive_link('rai_event'))); ?>"
                       class="events-filter-tab <?php echo $event_filter === 'upcoming' ? 'is-active' : ''; ?>">
                        <i class="far fa-calendar-check"></i>
                        <?php esc_html_e('Upcoming', 'responsible-ai'); ?>
                    </a>
                    <a href="<?php echo esc_url(add_query_arg(array('event_filter' => 'past', 'event_type' => $event_type_filter, 'view' => $view_mode), get_post_type_archive_link('rai_event'))); ?>"
                       class="events-filter-tab <?php echo $event_filter === 'past' ? 'is-active' : ''; ?>">
                        <i class="far fa-calendar"></i>
                        <?php esc_html_e('Past', 'responsible-ai'); ?>
                    </a>
                    <a href="<?php echo esc_url(add_query_arg(array('event_filter' => 'all', 'event_type' => $event_type_filter, 'view' => $view_mode), get_post_type_archive_link('rai_event'))); ?>"
                       class="events-filter-tab <?php echo $event_filter === 'all' ? 'is-active' : ''; ?>">
                        <i class="far fa-calendar-alt"></i>
                        <?php esc_html_e('All', 'responsible-ai'); ?>
                    </a>
                </div>

                <!-- Type Filter & View Mode -->
                <div class="events-filters__controls">
                    <!-- Event Type Dropdown -->
                    <div class="events-filter-dropdown">
                        <select name="event_type" id="event-type-filter" class="events-filter-select">
                            <option value=""><?php esc_html_e('All Types', 'responsible-ai'); ?></option>
                            <option value="webinar" <?php selected($event_type_filter, 'webinar'); ?>><?php esc_html_e('Webinar', 'responsible-ai'); ?></option>
                            <option value="conference" <?php selected($event_type_filter, 'conference'); ?>><?php esc_html_e('Conference', 'responsible-ai'); ?></option>
                            <option value="workshop" <?php selected($event_type_filter, 'workshop'); ?>><?php esc_html_e('Workshop', 'responsible-ai'); ?></option>
                            <option value="meetup" <?php selected($event_type_filter, 'meetup'); ?>><?php esc_html_e('Meetup', 'responsible-ai'); ?></option>
                            <option value="training" <?php selected($event_type_filter, 'training'); ?>><?php esc_html_e('Training', 'responsible-ai'); ?></option>
                        </select>
                    </div>

                    <!-- View Mode Toggle -->
                    <div class="events-view-toggle">
                        <a href="<?php echo esc_url(add_query_arg(array('event_filter' => $event_filter, 'event_type' => $event_type_filter, 'view' => 'list'), get_post_type_archive_link('rai_event'))); ?>"
                           class="view-toggle-btn <?php echo $view_mode === 'list' ? 'is-active' : ''; ?>"
                           aria-label="<?php esc_attr_e('List view', 'responsible-ai'); ?>">
                            <i class="fas fa-list"></i>
                        </a>
                        <a href="<?php echo esc_url(add_query_arg(array('event_filter' => $event_filter, 'event_type' => $event_type_filter, 'view' => 'grid'), get_post_type_archive_link('rai_event'))); ?>"
                           class="view-toggle-btn <?php echo $view_mode === 'grid' ? 'is-active' : ''; ?>"
                           aria-label="<?php esc_attr_e('Grid view', 'responsible-ai'); ?>">
                            <i class="fas fa-th"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($featured_events) && $event_filter === 'upcoming') : ?>
    <!-- Featured Events Section -->
    <section class="events-featured">
        <div class="container">
            <div class="section-header">
                <h2 class="section-header__title">
                    <i class="fas fa-star"></i>
                    <?php esc_html_e('Featured Events', 'responsible-ai'); ?>
                </h2>
            </div>
            <div class="events-featured__grid">
                <?php foreach ($featured_events as $event_id) :
                    $event_type = get_field('event_type', $event_id);
                    $event_format = get_field('event_format', $event_id);
                    $start_date = get_field('event_start_date', $event_id);
                    $end_date = get_field('event_end_date', $event_id);
                    $registration_url = get_field('event_registration_url', $event_id);
                    $venue_name = get_field('event_venue_name', $event_id);
                    $virtual_link = get_field('event_virtual_link', $event_id);

                    // Format dates
                    $start_timestamp = strtotime($start_date);
                    $end_timestamp = strtotime($end_date);
                    $is_same_day = date('Y-m-d', $start_timestamp) === date('Y-m-d', $end_timestamp);
                ?>
                <article class="event-card event-card--featured" data-event-id="<?php echo esc_attr($event_id); ?>">
                    <div class="event-card__badge-wrapper">
                        <span class="event-badge event-badge--<?php echo esc_attr($event_type); ?>">
                            <?php echo esc_html(ucfirst($event_type)); ?>
                        </span>
                        <span class="event-badge event-badge--featured">
                            <i class="fas fa-star"></i> <?php esc_html_e('Featured', 'responsible-ai'); ?>
                        </span>
                    </div>

                    <div class="event-card__date">
                        <div class="event-date">
                            <span class="event-date__month"><?php echo date_i18n('M', $start_timestamp); ?></span>
                            <span class="event-date__day"><?php echo date_i18n('d', $start_timestamp); ?></span>
                        </div>
                    </div>

                    <div class="event-card__content">
                        <h3 class="event-card__title">
                            <a href="<?php echo esc_url(get_permalink($event_id)); ?>">
                                <?php echo esc_html(get_the_title($event_id)); ?>
                            </a>
                        </h3>

                        <div class="event-card__meta">
                            <div class="event-meta-item">
                                <i class="far fa-clock"></i>
                                <span>
                                    <?php if ($is_same_day) : ?>
                                        <?php echo date_i18n('g:i a', $start_timestamp); ?> - <?php echo date_i18n('g:i a', $end_timestamp); ?>
                                    <?php else : ?>
                                        <?php echo date_i18n('M j, g:i a', $start_timestamp); ?> - <?php echo date_i18n('M j, g:i a', $end_timestamp); ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="event-meta-item">
                                <?php if ($event_format === 'virtual') : ?>
                                    <i class="fas fa-video"></i>
                                    <span><?php esc_html_e('Virtual Event', 'responsible-ai'); ?></span>
                                <?php elseif ($event_format === 'in-person') : ?>
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo esc_html($venue_name ?: __('In-Person', 'responsible-ai')); ?></span>
                                <?php else : ?>
                                    <i class="fas fa-globe"></i>
                                    <span><?php esc_html_e('Hybrid Event', 'responsible-ai'); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="event-card__excerpt">
                            <?php echo wp_trim_words(get_the_excerpt($event_id), 20); ?>
                        </div>

                        <?php if ($registration_url) : ?>
                        <a href="<?php echo esc_url($registration_url); ?>" class="btn btn-primary btn-sm" target="_blank" rel="noopener">
                            <?php esc_html_e('Register Now', 'responsible-ai'); ?>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        <?php else : ?>
                        <a href="<?php echo esc_url(get_permalink($event_id)); ?>" class="btn btn-secondary btn-sm">
                            <?php esc_html_e('View Details', 'responsible-ai'); ?>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Events Listing -->
    <section class="events-listing">
        <div class="container">
            <?php if ($events_query->have_posts()) : ?>
                <div class="events-grid events-grid--<?php echo esc_attr($view_mode); ?>">
                    <?php
                    while ($events_query->have_posts()) : $events_query->the_post();
                        $event_id = get_the_ID();

                        // Skip featured events in listing if showing upcoming
                        if ($event_filter === 'upcoming' && in_array($event_id, $featured_events)) {
                            continue;
                        }

                        $event_type = get_field('event_type');
                        $event_format = get_field('event_format');
                        $start_date = get_field('event_start_date');
                        $end_date = get_field('event_end_date');
                        $registration_url = get_field('event_registration_url');
                        $venue_name = get_field('event_venue_name');
                        $virtual_link = get_field('event_virtual_link');

                        // Format dates
                        $start_timestamp = strtotime($start_date);
                        $end_timestamp = strtotime($end_date);
                        $is_same_day = date('Y-m-d', $start_timestamp) === date('Y-m-d', $end_timestamp);
                        $is_past = $start_timestamp < strtotime($current_date);
                    ?>
                    <article class="event-card <?php echo $is_past ? 'event-card--past' : ''; ?>" data-event-id="<?php echo esc_attr($event_id); ?>">
                        <div class="event-card__badge-wrapper">
                            <span class="event-badge event-badge--<?php echo esc_attr($event_type); ?>">
                                <?php echo esc_html(ucfirst($event_type)); ?>
                            </span>
                        </div>

                        <div class="event-card__date">
                            <div class="event-date">
                                <span class="event-date__month"><?php echo date_i18n('M', $start_timestamp); ?></span>
                                <span class="event-date__day"><?php echo date_i18n('d', $start_timestamp); ?></span>
                            </div>
                        </div>

                        <div class="event-card__content">
                            <h3 class="event-card__title">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h3>

                            <div class="event-card__meta">
                                <div class="event-meta-item">
                                    <i class="far fa-clock"></i>
                                    <span>
                                        <?php if ($is_same_day) : ?>
                                            <?php echo date_i18n('g:i a', $start_timestamp); ?> - <?php echo date_i18n('g:i a', $end_timestamp); ?>
                                        <?php else : ?>
                                            <?php echo date_i18n('M j, g:i a', $start_timestamp); ?> - <?php echo date_i18n('M j, g:i a', $end_timestamp); ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="event-meta-item">
                                    <?php if ($event_format === 'virtual') : ?>
                                        <i class="fas fa-video"></i>
                                        <span><?php esc_html_e('Virtual Event', 'responsible-ai'); ?></span>
                                    <?php elseif ($event_format === 'in-person') : ?>
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?php echo esc_html($venue_name ?: __('In-Person', 'responsible-ai')); ?></span>
                                    <?php else : ?>
                                        <i class="fas fa-globe"></i>
                                        <span><?php esc_html_e('Hybrid Event', 'responsible-ai'); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="event-card__excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                            </div>

                            <?php if (!$is_past) : ?>
                                <?php if ($registration_url) : ?>
                                <a href="<?php echo esc_url($registration_url); ?>" class="btn btn-primary btn-sm" target="_blank" rel="noopener">
                                    <?php esc_html_e('Register', 'responsible-ai'); ?>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                                <?php else : ?>
                                <a href="<?php the_permalink(); ?>" class="btn btn-secondary btn-sm">
                                    <?php esc_html_e('Learn More', 'responsible-ai'); ?>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                                <?php endif; ?>
                            <?php else : ?>
                                <a href="<?php the_permalink(); ?>" class="btn btn-outline btn-sm">
                                    <?php esc_html_e('View Summary', 'responsible-ai'); ?>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <?php if ($events_query->max_num_pages > 1) : ?>
                <div class="pagination-wrapper">
                    <?php
                    echo paginate_links(array(
                        'base'      => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                        'format'    => '?paged=%#%',
                        'current'   => max(1, $paged),
                        'total'     => $events_query->max_num_pages,
                        'prev_text' => '<i class="fas fa-chevron-left"></i> ' . __('Previous', 'responsible-ai'),
                        'next_text' => __('Next', 'responsible-ai') . ' <i class="fas fa-chevron-right"></i>',
                        'mid_size'  => 2,
                    ));
                    ?>
                </div>
                <?php endif; ?>

            <?php else : ?>
                <!-- Empty State -->
                <div class="events-empty-state">
                    <div class="empty-state">
                        <div class="empty-state__icon">
                            <i class="far fa-calendar-times"></i>
                        </div>
                        <h3 class="empty-state__title">
                            <?php
                            if ($event_filter === 'upcoming') {
                                esc_html_e('No Upcoming Events', 'responsible-ai');
                            } elseif ($event_filter === 'past') {
                                esc_html_e('No Past Events', 'responsible-ai');
                            } else {
                                esc_html_e('No Events Found', 'responsible-ai');
                            }
                            ?>
                        </h3>
                        <p class="empty-state__text">
                            <?php
                            if (!empty($event_type_filter)) {
                                esc_html_e('Try adjusting your filters or check back later for new events.', 'responsible-ai');
                            } else {
                                esc_html_e('Check back soon for upcoming events, or subscribe to our newsletter to stay informed.', 'responsible-ai');
                            }
                            ?>
                        </p>
                        <?php if (!empty($event_type_filter)) : ?>
                        <a href="<?php echo esc_url(get_post_type_archive_link('rai_event')); ?>" class="btn btn-primary">
                            <?php esc_html_e('View All Events', 'responsible-ai'); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php wp_reset_postdata(); ?>
        </div>
    </section>
</div>

<style>
/* Events Archive Styles */
.events-archive {
    background: var(--color-background);
    color: var(--color-foreground);
}

/* Page Hero */
.page-hero--events {
    background: linear-gradient(135deg, var(--color-background-secondary) 0%, var(--color-background) 100%);
    padding: var(--space-20) 0;
    border-bottom: 1px solid var(--color-border);
}

.page-hero__content {
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}

.page-hero__title {
    font-size: var(--font-size-5xl);
    font-weight: var(--font-weight-bold);
    color: var(--color-foreground);
    margin-bottom: var(--space-4);
}

.page-hero__subtitle {
    font-size: var(--font-size-lg);
    color: var(--color-foreground-secondary);
    line-height: var(--line-height-relaxed);
}

/* Events Filters */
.events-filters {
    background: var(--color-background-secondary);
    padding: var(--space-6) 0;
    border-bottom: 1px solid var(--color-border);
    position: sticky;
    top: 0;
    z-index: 100;
}

.events-filters__wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-4);
    align-items: center;
    justify-content: space-between;
}

.events-filters__tabs {
    display: flex;
    gap: var(--space-2);
    flex-wrap: wrap;
}

.events-filter-tab {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    padding: var(--space-3) var(--space-6);
    background: var(--color-background);
    color: var(--color-foreground-secondary);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    text-decoration: none;
    font-weight: var(--font-weight-medium);
    transition: all 0.2s ease;
}

.events-filter-tab:hover {
    background: var(--color-background-hover);
    color: var(--color-foreground);
    border-color: var(--color-border-hover);
}

.events-filter-tab.is-active {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
}

.events-filters__controls {
    display: flex;
    gap: var(--space-3);
    align-items: center;
}

.events-filter-select {
    padding: var(--space-3) var(--space-4);
    background: var(--color-background);
    color: var(--color-foreground);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    font-size: var(--font-size-base);
    cursor: pointer;
    min-width: 180px;
}

.events-filter-select:hover {
    border-color: var(--color-border-hover);
}

.events-view-toggle {
    display: flex;
    gap: var(--space-1);
    background: var(--color-background);
    padding: var(--space-1);
    border-radius: var(--radius-md);
    border: 1px solid var(--color-border);
}

.view-toggle-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: transparent;
    color: var(--color-foreground-secondary);
    border: none;
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.view-toggle-btn:hover {
    background: var(--color-background-hover);
    color: var(--color-foreground);
}

.view-toggle-btn.is-active {
    background: var(--color-primary);
    color: white;
}

/* Featured Events */
.events-featured {
    padding: var(--space-16) 0;
    background: var(--color-background-secondary);
    border-bottom: 1px solid var(--color-border);
}

.section-header {
    margin-bottom: var(--space-10);
    text-align: center;
}

.section-header__title {
    font-size: var(--font-size-3xl);
    font-weight: var(--font-weight-bold);
    color: var(--color-foreground);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-3);
}

.section-header__title i {
    color: var(--color-cta);
}

.events-featured__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: var(--space-6);
}

/* Events Listing */
.events-listing {
    padding: var(--space-16) 0;
}

.events-grid {
    display: grid;
    gap: var(--space-6);
}

.events-grid--list {
    grid-template-columns: 1fr;
}

.events-grid--grid {
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
}

/* Event Card */
.event-card {
    background: var(--color-background-elevated);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.event-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
    border-color: var(--color-border-hover);
}

.event-card--featured {
    border-color: var(--color-cta);
    position: relative;
}

.event-card--featured::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--color-cta), var(--color-primary));
}

.event-card--past {
    opacity: 0.7;
}

.event-card__badge-wrapper {
    padding: var(--space-4);
    display: flex;
    gap: var(--space-2);
    flex-wrap: wrap;
}

.event-badge {
    display: inline-block;
    padding: var(--space-1) var(--space-3);
    background: var(--color-background-secondary);
    color: var(--color-foreground-secondary);
    border-radius: var(--radius-full);
    font-size: var(--font-size-xs);
    font-weight: var(--font-weight-semibold);
    text-transform: uppercase;
    letter-spacing: var(--letter-spacing-wide);
}

.event-badge--webinar {
    background: hsl(217, 91%, 60%, 0.2);
    color: var(--color-primary-light);
}

.event-badge--conference {
    background: hsl(38, 92%, 50%, 0.2);
    color: var(--color-cta-light);
}

.event-badge--workshop {
    background: hsl(142, 76%, 36%, 0.2);
    color: var(--color-success);
}

.event-badge--meetup {
    background: hsl(280, 80%, 60%, 0.2);
    color: hsl(280, 80%, 80%);
}

.event-badge--training {
    background: hsl(0, 84%, 60%, 0.2);
    color: var(--color-error);
}

.event-badge--featured {
    background: var(--color-cta);
    color: var(--color-background);
}

.event-card__date {
    padding: 0 var(--space-4);
    margin-bottom: var(--space-4);
}

.event-date {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    background: var(--color-primary);
    padding: var(--space-3) var(--space-4);
    border-radius: var(--radius-md);
    min-width: 80px;
}

.event-date__month {
    font-size: var(--font-size-sm);
    font-weight: var(--font-weight-semibold);
    text-transform: uppercase;
    color: white;
    opacity: 0.9;
}

.event-date__day {
    font-size: var(--font-size-3xl);
    font-weight: var(--font-weight-bold);
    color: white;
    line-height: 1;
}

.event-card__content {
    padding: 0 var(--space-4) var(--space-4);
    flex: 1;
    display: flex;
    flex-direction: column;
}

.event-card__title {
    font-size: var(--font-size-xl);
    font-weight: var(--font-weight-bold);
    margin-bottom: var(--space-3);
}

.event-card__title a {
    color: var(--color-foreground);
    text-decoration: none;
    transition: color 0.2s ease;
}

.event-card__title a:hover {
    color: var(--color-primary);
}

.event-card__meta {
    display: flex;
    flex-direction: column;
    gap: var(--space-2);
    margin-bottom: var(--space-4);
}

.event-meta-item {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    color: var(--color-foreground-secondary);
    font-size: var(--font-size-sm);
}

.event-meta-item i {
    color: var(--color-primary);
    width: 16px;
    text-align: center;
}

.event-card__excerpt {
    color: var(--color-foreground-muted);
    line-height: var(--line-height-relaxed);
    margin-bottom: var(--space-4);
    flex: 1;
}

/* Empty State */
.events-empty-state {
    padding: var(--space-20) 0;
}

.empty-state {
    text-align: center;
    max-width: 500px;
    margin: 0 auto;
}

.empty-state__icon {
    font-size: 80px;
    color: var(--color-foreground-muted);
    margin-bottom: var(--space-6);
}

.empty-state__title {
    font-size: var(--font-size-3xl);
    font-weight: var(--font-weight-bold);
    color: var(--color-foreground);
    margin-bottom: var(--space-4);
}

.empty-state__text {
    font-size: var(--font-size-lg);
    color: var(--color-foreground-secondary);
    line-height: var(--line-height-relaxed);
    margin-bottom: var(--space-6);
}

/* Pagination */
.pagination-wrapper {
    margin-top: var(--space-12);
    display: flex;
    justify-content: center;
}

.pagination-wrapper .page-numbers {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: var(--space-2) var(--space-3);
    background: var(--color-background-elevated);
    color: var(--color-foreground-secondary);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    text-decoration: none;
    font-weight: var(--font-weight-medium);
    margin: 0 var(--space-1);
    transition: all 0.2s ease;
}

.pagination-wrapper .page-numbers:hover {
    background: var(--color-background-hover);
    color: var(--color-foreground);
    border-color: var(--color-border-hover);
}

.pagination-wrapper .page-numbers.current {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
}

/* Responsive */
@media (max-width: 768px) {
    .page-hero__title {
        font-size: var(--font-size-3xl);
    }

    .events-filters__wrapper {
        flex-direction: column;
        align-items: stretch;
    }

    .events-filters__tabs {
        justify-content: center;
    }

    .events-filters__controls {
        justify-content: center;
    }

    .events-featured__grid,
    .events-grid--grid {
        grid-template-columns: 1fr;
    }

    .events-filter-tab {
        flex: 1;
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Event type filter change handler
    const eventTypeFilter = document.getElementById('event-type-filter');
    if (eventTypeFilter) {
        eventTypeFilter.addEventListener('change', function() {
            const currentUrl = new URL(window.location.href);
            const eventType = this.value;

            if (eventType) {
                currentUrl.searchParams.set('event_type', eventType);
            } else {
                currentUrl.searchParams.delete('event_type');
            }

            window.location.href = currentUrl.toString();
        });
    }
});
</script>

<?php get_footer(); ?>
