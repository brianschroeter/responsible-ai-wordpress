<?php
/**
 * Single Event Template
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) : the_post();

    // Get ACF fields
    $event_date = get_field('event_date');
    $event_time = get_field('event_time');
    $event_end_time = get_field('event_end_time');
    $event_timezone = get_field('event_timezone') ?: 'UTC';
    $event_format = get_field('event_format'); // Virtual, In-Person, Hybrid
    $event_location = get_field('event_location');
    $event_address = get_field('event_address');
    $event_city = get_field('event_city');
    $event_state = get_field('event_state');
    $event_country = get_field('event_country');
    $event_price = get_field('event_price');
    $is_free = get_field('is_free');
    $registration_url = get_field('registration_url');
    $registration_deadline = get_field('registration_deadline');
    $event_capacity = get_field('event_capacity');
    $event_speakers = get_field('event_speakers'); // Repeater
    $full_description = get_field('full_description');
    $event_type = get_field('event_type'); // Taxonomy or select field

    // Format dates
    $formatted_date = $event_date ? date_i18n('F j, Y', strtotime($event_date)) : '';
    $formatted_day = $event_date ? date_i18n('D', strtotime($event_date)) : '';
    $formatted_month_day = $event_date ? date_i18n('M d', strtotime($event_date)) : '';

    // Build full address
    $full_address = '';
    if ($event_address) {
        $address_parts = array_filter([$event_address, $event_city, $event_state, $event_country]);
        $full_address = implode(', ', $address_parts);
    }

    // Determine if event is past
    $is_past_event = $event_date && strtotime($event_date) < current_time('timestamp');

    // Get event type badge
    $type_badge = $event_type ?: __('Event', 'responsible-ai');

    // Get related events
    $related_events = array();
    $related_args = array(
        'post_type' => 'rai_event',
        'posts_per_page' => 3,
        'post__not_in' => array(get_the_ID()),
        'post_status' => 'publish',
        'meta_key' => 'event_date',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'meta_query' => array(
            array(
                'key' => 'event_date',
                'value' => current_time('Y-m-d'),
                'compare' => '>=',
                'type' => 'DATE',
            ),
        ),
    );
    $related_query = new WP_Query($related_args);

    // Schema.org Event structured data
    $schema_data = array(
        '@context' => 'https://schema.org',
        '@type' => 'Event',
        'name' => get_the_title(),
        'description' => $full_description ?: get_the_excerpt(),
        'url' => get_permalink(),
    );

    if ($event_date) {
        $start_datetime = $event_date . ($event_time ? 'T' . $event_time : 'T00:00:00');
        $schema_data['startDate'] = date('c', strtotime($start_datetime));

        if ($event_end_time) {
            $end_datetime = $event_date . 'T' . $event_end_time;
            $schema_data['endDate'] = date('c', strtotime($end_datetime));
        }
    }

    if ($event_format === 'Virtual') {
        $schema_data['eventAttendanceMode'] = 'https://schema.org/OnlineEventAttendanceMode';
        $schema_data['location'] = array(
            '@type' => 'VirtualLocation',
            'url' => $registration_url ?: get_permalink(),
        );
    } elseif ($event_format === 'In-Person' && $full_address) {
        $schema_data['eventAttendanceMode'] = 'https://schema.org/OfflineEventAttendanceMode';
        $schema_data['location'] = array(
            '@type' => 'Place',
            'name' => $event_location,
            'address' => array(
                '@type' => 'PostalAddress',
                'streetAddress' => $event_address,
                'addressLocality' => $event_city,
                'addressRegion' => $event_state,
                'addressCountry' => $event_country,
            ),
        );
    } elseif ($event_format === 'Hybrid') {
        $schema_data['eventAttendanceMode'] = 'https://schema.org/MixedEventAttendanceMode';
    }

    if ($registration_url) {
        $schema_data['offers'] = array(
            '@type' => 'Offer',
            'url' => $registration_url,
            'price' => $is_free ? '0' : ($event_price ?: '0'),
            'priceCurrency' => 'USD',
            'availability' => $is_past_event ? 'https://schema.org/SoldOut' : 'https://schema.org/InStock',
            'validFrom' => date('c', strtotime(get_the_date('c'))),
        );
    }

    if (has_post_thumbnail()) {
        $schema_data['image'] = get_the_post_thumbnail_url(get_the_ID(), 'full');
    }

    if ($event_speakers && is_array($event_speakers)) {
        $performers = array();
        foreach ($event_speakers as $speaker) {
            if (!empty($speaker['speaker_name'])) {
                $performers[] = array(
                    '@type' => 'Person',
                    'name' => $speaker['speaker_name'],
                    'description' => $speaker['speaker_bio'] ?? '',
                );
            }
        }
        if (!empty($performers)) {
            $schema_data['performer'] = $performers;
        }
    }

    $schema_data['organizer'] = array(
        '@type' => 'Organization',
        'name' => get_bloginfo('name'),
        'url' => home_url('/'),
    );

    ?>

    <!-- Schema.org Structured Data -->
    <script type="application/ld+json">
    <?php echo wp_json_encode($schema_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?>
    </script>

    <article id="post-<?php the_ID(); ?>" <?php post_class('single-event'); ?>>

        <!-- Event Hero Section -->
        <section class="event-hero">
            <div class="container">
                <div class="event-hero__content">
                    <!-- Back to Events Link -->
                    <a href="<?php echo esc_url(get_post_type_archive_link('rai_event')); ?>" class="event-hero__back">
                        <i class="fas fa-arrow-left"></i>
                        <?php esc_html_e('Back to Events', 'responsible-ai'); ?>
                    </a>

                    <!-- Event Type Badge -->
                    <span class="event-badge event-badge--<?php echo esc_attr(sanitize_title($type_badge)); ?>">
                        <?php echo esc_html($type_badge); ?>
                    </span>

                    <!-- Event Title -->
                    <h1 class="event-hero__title"><?php the_title(); ?></h1>

                    <!-- Event Meta -->
                    <div class="event-hero__meta">
                        <?php if ($formatted_date) : ?>
                            <span class="event-hero__date">
                                <i class="far fa-calendar"></i>
                                <?php echo esc_html($formatted_date); ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($event_time) : ?>
                            <span class="event-hero__time">
                                <i class="far fa-clock"></i>
                                <?php
                                echo esc_html($event_time);
                                if ($event_end_time) {
                                    echo ' - ' . esc_html($event_end_time);
                                }
                                echo ' ' . esc_html($event_timezone);
                                ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($event_format) : ?>
                            <span class="event-hero__format">
                                <i class="fas fa-<?php echo $event_format === 'Virtual' ? 'video' : ($event_format === 'Hybrid' ? 'network-wired' : 'map-marker-alt'); ?>"></i>
                                <?php echo esc_html($event_format); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Event Content -->
        <section class="event-content">
            <div class="container">
                <div class="event-layout">

                    <!-- Main Content Column -->
                    <div class="event-layout__main">

                        <!-- Event Image -->
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="event-image">
                                <?php the_post_thumbnail('large', array('class' => 'event-image__img')); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Event Description -->
                        <?php if ($full_description) : ?>
                            <div class="event-description">
                                <h2><?php esc_html_e('About This Event', 'responsible-ai'); ?></h2>
                                <div class="event-description__content">
                                    <?php echo wp_kses_post(wpautop($full_description)); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Speakers Section -->
                        <?php if ($event_speakers && is_array($event_speakers) && count($event_speakers) > 0) : ?>
                            <div class="event-speakers">
                                <h2><?php esc_html_e('Featured Speakers', 'responsible-ai'); ?></h2>
                                <div class="event-speakers__grid">
                                    <?php foreach ($event_speakers as $speaker) :
                                        $speaker_name = $speaker['speaker_name'] ?? '';
                                        $speaker_title = $speaker['speaker_title'] ?? '';
                                        $speaker_org = $speaker['speaker_organization'] ?? '';
                                        $speaker_bio = $speaker['speaker_bio'] ?? '';
                                        $speaker_photo = $speaker['speaker_photo'] ?? null;
                                        $speaker_linkedin = $speaker['speaker_linkedin'] ?? '';

                                        if (!$speaker_name) continue;
                                    ?>
                                        <div class="speaker-card">
                                            <?php if ($speaker_photo) : ?>
                                                <div class="speaker-card__photo">
                                                    <img src="<?php echo esc_url($speaker_photo['sizes']['medium'] ?? $speaker_photo['url']); ?>"
                                                         alt="<?php echo esc_attr($speaker_name); ?>"
                                                         loading="lazy">
                                                </div>
                                            <?php else : ?>
                                                <div class="speaker-card__photo speaker-card__photo--placeholder">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            <?php endif; ?>

                                            <div class="speaker-card__content">
                                                <h3 class="speaker-card__name"><?php echo esc_html($speaker_name); ?></h3>
                                                <?php if ($speaker_title) : ?>
                                                    <p class="speaker-card__title"><?php echo esc_html($speaker_title); ?></p>
                                                <?php endif; ?>
                                                <?php if ($speaker_org) : ?>
                                                    <p class="speaker-card__org"><?php echo esc_html($speaker_org); ?></p>
                                                <?php endif; ?>
                                                <?php if ($speaker_bio) : ?>
                                                    <p class="speaker-card__bio"><?php echo esc_html($speaker_bio); ?></p>
                                                <?php endif; ?>
                                                <?php if ($speaker_linkedin) : ?>
                                                    <a href="<?php echo esc_url($speaker_linkedin); ?>"
                                                       class="speaker-card__linkedin"
                                                       target="_blank"
                                                       rel="noopener"
                                                       aria-label="<?php echo esc_attr(sprintf(__('View %s on LinkedIn', 'responsible-ai'), $speaker_name)); ?>">
                                                        <i class="fab fa-linkedin"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Location Section -->
                        <?php if ($event_format !== 'Virtual' && $full_address) : ?>
                            <div class="event-location-section">
                                <h2><?php esc_html_e('Event Location', 'responsible-ai'); ?></h2>
                                <div class="event-location-section__content">
                                    <?php if ($event_location) : ?>
                                        <h3 class="event-location-section__name"><?php echo esc_html($event_location); ?></h3>
                                    <?php endif; ?>
                                    <p class="event-location-section__address">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo esc_html($full_address); ?>
                                    </p>

                                    <!-- Map Placeholder -->
                                    <div class="event-location-section__map">
                                        <div class="map-placeholder">
                                            <i class="fas fa-map-marked-alt"></i>
                                            <p><?php esc_html_e('Map view available upon registration', 'responsible-ai'); ?></p>
                                            <?php if ($full_address) : ?>
                                                <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($full_address); ?>"
                                                   target="_blank"
                                                   rel="noopener"
                                                   class="btn btn-secondary btn-sm">
                                                    <?php esc_html_e('View on Google Maps', 'responsible-ai'); ?>
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>

                    <!-- Sidebar Column -->
                    <aside class="event-layout__sidebar">

                        <!-- Event Details Card -->
                        <div class="event-details-card">
                            <h3 class="event-details-card__title"><?php esc_html_e('Event Details', 'responsible-ai'); ?></h3>

                            <div class="event-details-card__body">

                                <!-- Date & Time -->
                                <?php if ($formatted_date) : ?>
                                    <div class="event-detail">
                                        <span class="event-detail__icon">
                                            <i class="far fa-calendar"></i>
                                        </span>
                                        <div class="event-detail__content">
                                            <span class="event-detail__label"><?php esc_html_e('Date', 'responsible-ai'); ?></span>
                                            <span class="event-detail__value"><?php echo esc_html($formatted_date); ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($event_time) : ?>
                                    <div class="event-detail">
                                        <span class="event-detail__icon">
                                            <i class="far fa-clock"></i>
                                        </span>
                                        <div class="event-detail__content">
                                            <span class="event-detail__label"><?php esc_html_e('Time', 'responsible-ai'); ?></span>
                                            <span class="event-detail__value">
                                                <?php
                                                echo esc_html($event_time);
                                                if ($event_end_time) {
                                                    echo ' - ' . esc_html($event_end_time);
                                                }
                                                ?>
                                                <br>
                                                <span class="event-detail__timezone"><?php echo esc_html($event_timezone); ?></span>
                                            </span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Format -->
                                <?php if ($event_format) : ?>
                                    <div class="event-detail">
                                        <span class="event-detail__icon">
                                            <i class="fas fa-<?php echo $event_format === 'Virtual' ? 'video' : ($event_format === 'Hybrid' ? 'network-wired' : 'map-marker-alt'); ?>"></i>
                                        </span>
                                        <div class="event-detail__content">
                                            <span class="event-detail__label"><?php esc_html_e('Format', 'responsible-ai'); ?></span>
                                            <span class="event-detail__value"><?php echo esc_html($event_format); ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Location -->
                                <?php if ($event_location) : ?>
                                    <div class="event-detail">
                                        <span class="event-detail__icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        <div class="event-detail__content">
                                            <span class="event-detail__label"><?php esc_html_e('Location', 'responsible-ai'); ?></span>
                                            <span class="event-detail__value">
                                                <?php echo esc_html($event_location); ?>
                                                <?php if ($event_city) : ?>
                                                    <br><?php echo esc_html($event_city); ?>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Price -->
                                <div class="event-detail">
                                    <span class="event-detail__icon">
                                        <i class="fas fa-ticket-alt"></i>
                                    </span>
                                    <div class="event-detail__content">
                                        <span class="event-detail__label"><?php esc_html_e('Price', 'responsible-ai'); ?></span>
                                        <span class="event-detail__value event-detail__value--price">
                                            <?php
                                            if ($is_free) {
                                                esc_html_e('Free', 'responsible-ai');
                                            } elseif ($event_price) {
                                                echo '$' . esc_html($event_price);
                                            } else {
                                                esc_html_e('Free', 'responsible-ai');
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Capacity -->
                                <?php if ($event_capacity) : ?>
                                    <div class="event-detail">
                                        <span class="event-detail__icon">
                                            <i class="fas fa-users"></i>
                                        </span>
                                        <div class="event-detail__content">
                                            <span class="event-detail__label"><?php esc_html_e('Capacity', 'responsible-ai'); ?></span>
                                            <span class="event-detail__value"><?php echo esc_html($event_capacity); ?> <?php esc_html_e('attendees', 'responsible-ai'); ?></span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            </div>

                            <!-- Register Button -->
                            <?php if (!$is_past_event && $registration_url) : ?>
                                <div class="event-details-card__footer">
                                    <a href="<?php echo esc_url($registration_url); ?>"
                                       class="btn btn-cta btn-block"
                                       target="_blank"
                                       rel="noopener">
                                        <?php esc_html_e('Register Now', 'responsible-ai'); ?>
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                    <?php if ($registration_deadline) : ?>
                                        <p class="event-details-card__deadline">
                                            <?php echo sprintf(
                                                esc_html__('Registration deadline: %s', 'responsible-ai'),
                                                date_i18n('F j, Y', strtotime($registration_deadline))
                                            ); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            <?php elseif ($is_past_event) : ?>
                                <div class="event-details-card__footer">
                                    <div class="event-details-card__past">
                                        <i class="fas fa-info-circle"></i>
                                        <?php esc_html_e('This event has ended', 'responsible-ai'); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Add to Calendar -->
                        <?php if (!$is_past_event && $event_date) : ?>
                            <div class="event-calendar-card">
                                <h3 class="event-calendar-card__title">
                                    <i class="far fa-calendar-plus"></i>
                                    <?php esc_html_e('Add to Calendar', 'responsible-ai'); ?>
                                </h3>
                                <div class="event-calendar-card__links">
                                    <?php
                                    // Build calendar URLs
                                    $event_title_encoded = urlencode(get_the_title());
                                    $event_description_encoded = urlencode(wp_strip_all_tags($full_description ?: get_the_excerpt()));
                                    $event_location_encoded = urlencode($full_address ?: $event_location ?: 'Online');

                                    $start_time = $event_time ? str_replace(':', '', $event_time) : '000000';
                                    $end_time = $event_end_time ? str_replace(':', '', $event_end_time) : '235959';
                                    $date_formatted = date('Ymd', strtotime($event_date));

                                    // Google Calendar
                                    $google_url = 'https://www.google.com/calendar/render?action=TEMPLATE';
                                    $google_url .= '&text=' . $event_title_encoded;
                                    $google_url .= '&dates=' . $date_formatted . 'T' . $start_time . '/' . $date_formatted . 'T' . $end_time;
                                    $google_url .= '&details=' . $event_description_encoded;
                                    $google_url .= '&location=' . $event_location_encoded;

                                    // Outlook/iCal (.ics file)
                                    $ics_url = add_query_arg(array(
                                        'ics' => '1',
                                        'event_id' => get_the_ID(),
                                    ), get_permalink());
                                    ?>

                                    <a href="<?php echo esc_url($google_url); ?>"
                                       class="event-calendar-link"
                                       target="_blank"
                                       rel="noopener">
                                        <i class="fab fa-google"></i>
                                        <?php esc_html_e('Google Calendar', 'responsible-ai'); ?>
                                    </a>

                                    <a href="<?php echo esc_url($ics_url); ?>"
                                       class="event-calendar-link"
                                       download>
                                        <i class="far fa-calendar"></i>
                                        <?php esc_html_e('Outlook / iCal', 'responsible-ai'); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Social Sharing -->
                        <div class="event-share-card">
                            <h3 class="event-share-card__title">
                                <i class="fas fa-share-alt"></i>
                                <?php esc_html_e('Share This Event', 'responsible-ai'); ?>
                            </h3>
                            <div class="event-share-card__links">
                                <?php
                                $share_url = urlencode(get_permalink());
                                $share_title = urlencode(get_the_title());
                                ?>

                                <a href="https://twitter.com/intent/tweet?url=<?php echo $share_url; ?>&text=<?php echo $share_title; ?>"
                                   class="event-share-link event-share-link--twitter"
                                   target="_blank"
                                   rel="noopener"
                                   aria-label="<?php esc_attr_e('Share on Twitter', 'responsible-ai'); ?>">
                                    <i class="fab fa-twitter"></i>
                                </a>

                                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $share_url; ?>"
                                   class="event-share-link event-share-link--linkedin"
                                   target="_blank"
                                   rel="noopener"
                                   aria-label="<?php esc_attr_e('Share on LinkedIn', 'responsible-ai'); ?>">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>

                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>"
                                   class="event-share-link event-share-link--facebook"
                                   target="_blank"
                                   rel="noopener"
                                   aria-label="<?php esc_attr_e('Share on Facebook', 'responsible-ai'); ?>">
                                    <i class="fab fa-facebook-f"></i>
                                </a>

                                <button class="event-share-link event-share-link--copy js-copy-link"
                                        data-url="<?php echo esc_url(get_permalink()); ?>"
                                        aria-label="<?php esc_attr_e('Copy link', 'responsible-ai'); ?>">
                                    <i class="fas fa-link"></i>
                                </button>
                            </div>
                        </div>

                    </aside>

                </div>
            </div>
        </section>

        <!-- Related Events Section -->
        <?php if ($related_query->have_posts()) : ?>
            <section class="related-events">
                <div class="container">
                    <h2 class="related-events__title"><?php esc_html_e('Upcoming Events', 'responsible-ai'); ?></h2>
                    <div class="related-events__grid">
                        <?php
                        while ($related_query->have_posts()) : $related_query->the_post();
                            $rel_date = get_field('event_date');
                            $rel_format = get_field('event_format');
                            $rel_location = get_field('event_location');
                        ?>
                            <article class="event-card">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="event-card__image">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium', array('loading' => 'lazy')); ?>
                                        </a>
                                        <?php if ($rel_date) : ?>
                                            <div class="event-card__date-badge">
                                                <span class="event-card__month"><?php echo date_i18n('M', strtotime($rel_date)); ?></span>
                                                <span class="event-card__day"><?php echo date_i18n('d', strtotime($rel_date)); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="event-card__content">
                                    <?php if ($rel_format) : ?>
                                        <span class="event-card__format"><?php echo esc_html($rel_format); ?></span>
                                    <?php endif; ?>

                                    <h3 class="event-card__title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>

                                    <?php if (has_excerpt()) : ?>
                                        <p class="event-card__excerpt"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                                    <?php endif; ?>

                                    <div class="event-card__meta">
                                        <?php if ($rel_date) : ?>
                                            <span class="event-card__meta-item">
                                                <i class="far fa-calendar"></i>
                                                <?php echo date_i18n('M j, Y', strtotime($rel_date)); ?>
                                            </span>
                                        <?php endif; ?>

                                        <?php if ($rel_location) : ?>
                                            <span class="event-card__meta-item">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <?php echo esc_html($rel_location); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <a href="<?php the_permalink(); ?>" class="event-card__link">
                                        <?php esc_html_e('Learn More', 'responsible-ai'); ?>
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </article>
                        <?php endwhile; ?>
                        <?php wp_reset_postdata(); ?>
                    </div>

                    <div class="related-events__footer">
                        <a href="<?php echo esc_url(get_post_type_archive_link('rai_event')); ?>" class="btn btn-secondary">
                            <?php esc_html_e('View All Events', 'responsible-ai'); ?>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </section>
        <?php endif; ?>

    </article>

    <style>
    /* Single Event Styles */
    .single-event {
        background: var(--color-background);
        color: var(--color-foreground);
    }

    /* Event Hero */
    .event-hero {
        background: var(--color-background-secondary);
        padding: var(--space-12) 0 var(--space-8);
        border-bottom: 1px solid var(--color-border);
    }

    .event-hero__back {
        display: inline-flex;
        align-items: center;
        gap: var(--space-2);
        color: var(--color-foreground-muted);
        text-decoration: none;
        font-size: var(--font-size-sm);
        margin-bottom: var(--space-4);
        transition: color 0.3s var(--ease-default);
    }

    .event-hero__back:hover {
        color: var(--color-primary);
    }

    .event-badge {
        display: inline-block;
        padding: var(--space-1) var(--space-3);
        background: var(--color-primary-muted);
        color: var(--color-foreground);
        font-size: var(--font-size-xs);
        font-weight: var(--font-weight-semibold);
        text-transform: uppercase;
        letter-spacing: var(--letter-spacing-wide);
        border-radius: var(--border-radius-full);
        margin-bottom: var(--space-4);
    }

    .event-hero__title {
        font-size: var(--font-size-4xl);
        font-weight: var(--font-weight-bold);
        line-height: var(--line-height-tight);
        margin-bottom: var(--space-4);
        color: var(--color-foreground);
    }

    .event-hero__meta {
        display: flex;
        flex-wrap: wrap;
        gap: var(--space-6);
        font-size: var(--font-size-base);
        color: var(--color-foreground-muted);
    }

    .event-hero__meta span {
        display: flex;
        align-items: center;
        gap: var(--space-2);
    }

    .event-hero__meta i {
        color: var(--color-primary);
    }

    /* Event Layout */
    .event-content {
        padding: var(--space-12) 0;
    }

    .event-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: var(--space-10);
    }

    @media (min-width: 1024px) {
        .event-layout {
            grid-template-columns: 2fr 1fr;
        }
    }

    /* Event Image */
    .event-image {
        margin-bottom: var(--space-8);
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-lg);
    }

    .event-image__img {
        width: 100%;
        height: auto;
        display: block;
    }

    /* Event Description */
    .event-description {
        margin-bottom: var(--space-10);
    }

    .event-description h2 {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        margin-bottom: var(--space-4);
        color: var(--color-foreground);
    }

    .event-description__content {
        font-size: var(--font-size-base);
        line-height: var(--line-height-relaxed);
        color: var(--color-foreground-secondary);
    }

    .event-description__content p {
        margin-bottom: var(--space-4);
    }

    /* Event Speakers */
    .event-speakers {
        margin-bottom: var(--space-10);
    }

    .event-speakers h2 {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        margin-bottom: var(--space-6);
        color: var(--color-foreground);
    }

    .event-speakers__grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: var(--space-6);
    }

    @media (min-width: 768px) {
        .event-speakers__grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .speaker-card {
        display: flex;
        gap: var(--space-4);
        padding: var(--space-6);
        background: var(--color-background-elevated);
        border: 1px solid var(--color-border);
        border-radius: var(--border-radius-lg);
        transition: transform 0.3s var(--ease-default), box-shadow 0.3s var(--ease-default);
    }

    .speaker-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .speaker-card__photo {
        flex-shrink: 0;
        width: 80px;
        height: 80px;
        border-radius: var(--border-radius-full);
        overflow: hidden;
    }

    .speaker-card__photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .speaker-card__photo--placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--color-secondary);
        color: var(--color-foreground-muted);
        font-size: var(--font-size-2xl);
    }

    .speaker-card__content {
        flex: 1;
        min-width: 0;
    }

    .speaker-card__name {
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-semibold);
        margin-bottom: var(--space-1);
        color: var(--color-foreground);
    }

    .speaker-card__title {
        font-size: var(--font-size-sm);
        color: var(--color-primary);
        margin-bottom: var(--space-1);
    }

    .speaker-card__org {
        font-size: var(--font-size-sm);
        color: var(--color-foreground-muted);
        margin-bottom: var(--space-2);
    }

    .speaker-card__bio {
        font-size: var(--font-size-sm);
        line-height: var(--line-height-relaxed);
        color: var(--color-foreground-secondary);
        margin-bottom: var(--space-2);
    }

    .speaker-card__linkedin {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        background: var(--color-primary);
        color: var(--color-foreground);
        border-radius: var(--border-radius-full);
        transition: background 0.3s var(--ease-default);
    }

    .speaker-card__linkedin:hover {
        background: var(--color-primary-hover);
    }

    /* Event Location Section */
    .event-location-section {
        margin-bottom: var(--space-10);
    }

    .event-location-section h2 {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        margin-bottom: var(--space-4);
        color: var(--color-foreground);
    }

    .event-location-section__name {
        font-size: var(--font-size-xl);
        font-weight: var(--font-weight-semibold);
        margin-bottom: var(--space-2);
        color: var(--color-foreground);
    }

    .event-location-section__address {
        display: flex;
        align-items: center;
        gap: var(--space-2);
        font-size: var(--font-size-base);
        color: var(--color-foreground-muted);
        margin-bottom: var(--space-6);
    }

    .event-location-section__address i {
        color: var(--color-primary);
    }

    .map-placeholder {
        padding: var(--space-12) var(--space-6);
        background: var(--color-background-elevated);
        border: 2px dashed var(--color-border);
        border-radius: var(--border-radius-lg);
        text-align: center;
    }

    .map-placeholder i {
        font-size: var(--font-size-5xl);
        color: var(--color-foreground-muted);
        margin-bottom: var(--space-4);
    }

    .map-placeholder p {
        font-size: var(--font-size-base);
        color: var(--color-foreground-muted);
        margin-bottom: var(--space-4);
    }

    /* Event Details Card */
    .event-details-card {
        background: var(--color-background-elevated);
        border: 1px solid var(--color-border);
        border-radius: var(--border-radius-lg);
        padding: var(--space-6);
        margin-bottom: var(--space-6);
        position: sticky;
        top: var(--space-6);
    }

    .event-details-card__title {
        font-size: var(--font-size-xl);
        font-weight: var(--font-weight-bold);
        margin-bottom: var(--space-6);
        padding-bottom: var(--space-4);
        border-bottom: 1px solid var(--color-border);
        color: var(--color-foreground);
    }

    .event-detail {
        display: flex;
        gap: var(--space-3);
        margin-bottom: var(--space-5);
    }

    .event-detail:last-child {
        margin-bottom: 0;
    }

    .event-detail__icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--color-primary-muted);
        color: var(--color-primary-light);
        border-radius: var(--border-radius-md);
    }

    .event-detail__content {
        flex: 1;
        min-width: 0;
    }

    .event-detail__label {
        display: block;
        font-size: var(--font-size-xs);
        font-weight: var(--font-weight-semibold);
        text-transform: uppercase;
        letter-spacing: var(--letter-spacing-wide);
        color: var(--color-foreground-muted);
        margin-bottom: var(--space-1);
    }

    .event-detail__value {
        display: block;
        font-size: var(--font-size-base);
        color: var(--color-foreground);
        line-height: var(--line-height-snug);
    }

    .event-detail__value--price {
        font-size: var(--font-size-xl);
        font-weight: var(--font-weight-bold);
        color: var(--color-cta);
    }

    .event-detail__timezone {
        font-size: var(--font-size-sm);
        color: var(--color-foreground-muted);
    }

    .event-details-card__footer {
        padding-top: var(--space-6);
        border-top: 1px solid var(--color-border);
        margin-top: var(--space-6);
    }

    .event-details-card__deadline {
        font-size: var(--font-size-xs);
        color: var(--color-foreground-muted);
        text-align: center;
        margin-top: var(--space-3);
    }

    .event-details-card__past {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: var(--space-2);
        padding: var(--space-4);
        background: var(--color-secondary);
        color: var(--color-foreground-muted);
        border-radius: var(--border-radius-md);
        font-size: var(--font-size-sm);
        text-align: center;
    }

    /* Calendar Card */
    .event-calendar-card {
        background: var(--color-background-elevated);
        border: 1px solid var(--color-border);
        border-radius: var(--border-radius-lg);
        padding: var(--space-6);
        margin-bottom: var(--space-6);
    }

    .event-calendar-card__title {
        font-size: var(--font-size-base);
        font-weight: var(--font-weight-semibold);
        margin-bottom: var(--space-4);
        color: var(--color-foreground);
        display: flex;
        align-items: center;
        gap: var(--space-2);
    }

    .event-calendar-card__title i {
        color: var(--color-primary);
    }

    .event-calendar-card__links {
        display: flex;
        flex-direction: column;
        gap: var(--space-2);
    }

    .event-calendar-link {
        display: flex;
        align-items: center;
        gap: var(--space-2);
        padding: var(--space-3);
        background: var(--color-background-hover);
        color: var(--color-foreground);
        text-decoration: none;
        border-radius: var(--border-radius-md);
        font-size: var(--font-size-sm);
        transition: background 0.3s var(--ease-default);
    }

    .event-calendar-link:hover {
        background: var(--color-secondary);
    }

    .event-calendar-link i {
        color: var(--color-primary);
    }

    /* Share Card */
    .event-share-card {
        background: var(--color-background-elevated);
        border: 1px solid var(--color-border);
        border-radius: var(--border-radius-lg);
        padding: var(--space-6);
    }

    .event-share-card__title {
        font-size: var(--font-size-base);
        font-weight: var(--font-weight-semibold);
        margin-bottom: var(--space-4);
        color: var(--color-foreground);
        display: flex;
        align-items: center;
        gap: var(--space-2);
    }

    .event-share-card__title i {
        color: var(--color-primary);
    }

    .event-share-card__links {
        display: flex;
        gap: var(--space-2);
    }

    .event-share-link {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        background: var(--color-background-hover);
        color: var(--color-foreground);
        text-decoration: none;
        border: none;
        border-radius: var(--border-radius-md);
        font-size: var(--font-size-lg);
        cursor: pointer;
        transition: background 0.3s var(--ease-default), transform 0.3s var(--ease-default);
    }

    .event-share-link:hover {
        transform: translateY(-2px);
    }

    .event-share-link--twitter:hover {
        background: #1DA1F2;
    }

    .event-share-link--linkedin:hover {
        background: #0077B5;
    }

    .event-share-link--facebook:hover {
        background: #1877F2;
    }

    .event-share-link--copy:hover {
        background: var(--color-primary);
    }

    /* Related Events */
    .related-events {
        padding: var(--space-16) 0;
        background: var(--color-background-secondary);
        border-top: 1px solid var(--color-border);
    }

    .related-events__title {
        font-size: var(--font-size-3xl);
        font-weight: var(--font-weight-bold);
        text-align: center;
        margin-bottom: var(--space-10);
        color: var(--color-foreground);
    }

    .related-events__grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: var(--space-6);
        margin-bottom: var(--space-10);
    }

    @media (min-width: 768px) {
        .related-events__grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .related-events__grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .event-card {
        background: var(--color-background-elevated);
        border: 1px solid var(--color-border);
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        transition: transform 0.3s var(--ease-default), box-shadow 0.3s var(--ease-default);
    }

    .event-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-xl);
    }

    .event-card__image {
        position: relative;
        aspect-ratio: 16 / 9;
        overflow: hidden;
    }

    .event-card__image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s var(--ease-default);
    }

    .event-card:hover .event-card__image img {
        transform: scale(1.05);
    }

    .event-card__date-badge {
        position: absolute;
        top: var(--space-4);
        left: var(--space-4);
        background: var(--color-cta);
        color: var(--color-background);
        border-radius: var(--border-radius-md);
        padding: var(--space-2);
        text-align: center;
        min-width: 60px;
        box-shadow: var(--shadow-lg);
    }

    .event-card__month {
        display: block;
        font-size: var(--font-size-xs);
        font-weight: var(--font-weight-bold);
        text-transform: uppercase;
    }

    .event-card__day {
        display: block;
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        line-height: 1;
    }

    .event-card__content {
        padding: var(--space-6);
    }

    .event-card__format {
        display: inline-block;
        padding: var(--space-1) var(--space-2);
        background: var(--color-primary-muted);
        color: var(--color-primary-light);
        font-size: var(--font-size-xs);
        font-weight: var(--font-weight-semibold);
        text-transform: uppercase;
        border-radius: var(--border-radius-sm);
        margin-bottom: var(--space-3);
    }

    .event-card__title {
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-semibold);
        margin-bottom: var(--space-3);
        line-height: var(--line-height-snug);
    }

    .event-card__title a {
        color: var(--color-foreground);
        text-decoration: none;
        transition: color 0.3s var(--ease-default);
    }

    .event-card__title a:hover {
        color: var(--color-primary);
    }

    .event-card__excerpt {
        font-size: var(--font-size-sm);
        color: var(--color-foreground-secondary);
        line-height: var(--line-height-relaxed);
        margin-bottom: var(--space-4);
    }

    .event-card__meta {
        display: flex;
        flex-wrap: wrap;
        gap: var(--space-4);
        font-size: var(--font-size-xs);
        color: var(--color-foreground-muted);
        margin-bottom: var(--space-4);
        padding-bottom: var(--space-4);
        border-bottom: 1px solid var(--color-border);
    }

    .event-card__meta-item {
        display: flex;
        align-items: center;
        gap: var(--space-1);
    }

    .event-card__meta-item i {
        color: var(--color-primary);
    }

    .event-card__link {
        display: inline-flex;
        align-items: center;
        gap: var(--space-2);
        color: var(--color-primary);
        text-decoration: none;
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        transition: gap 0.3s var(--ease-default);
    }

    .event-card__link:hover {
        gap: var(--space-3);
    }

    .related-events__footer {
        text-align: center;
    }

    /* Button Utilities */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: var(--space-2);
        padding: var(--space-3) var(--space-6);
        border: none;
        border-radius: var(--border-radius-md);
        font-size: var(--font-size-base);
        font-weight: var(--font-weight-semibold);
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s var(--ease-default);
    }

    .btn-cta {
        background: var(--color-cta);
        color: var(--color-background);
        box-shadow: var(--shadow-cta);
    }

    .btn-cta:hover {
        background: var(--color-cta-hover);
        box-shadow: var(--shadow-cta-lg);
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: var(--color-secondary);
        color: var(--color-foreground);
    }

    .btn-secondary:hover {
        background: var(--color-secondary-hover);
    }

    .btn-block {
        display: flex;
        width: 100%;
    }

    .btn-sm {
        padding: var(--space-2) var(--space-4);
        font-size: var(--font-size-sm);
    }

    /* Responsive */
    @media (max-width: 1023px) {
        .event-hero__title {
            font-size: var(--font-size-3xl);
        }

        .event-details-card {
            position: static;
        }
    }

    @media (max-width: 767px) {
        .event-hero {
            padding: var(--space-8) 0 var(--space-6);
        }

        .event-hero__title {
            font-size: var(--font-size-2xl);
        }

        .event-hero__meta {
            flex-direction: column;
            gap: var(--space-3);
        }

        .event-content {
            padding: var(--space-8) 0;
        }

        .event-layout {
            gap: var(--space-6);
        }

        .speaker-card {
            flex-direction: column;
            text-align: center;
        }

        .speaker-card__photo {
            margin: 0 auto;
        }
    }
    </style>

    <script>
    // Copy link functionality
    document.addEventListener('DOMContentLoaded', function() {
        const copyButtons = document.querySelectorAll('.js-copy-link');

        copyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const url = this.getAttribute('data-url');

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(() => {
                        // Change icon temporarily
                        const icon = this.querySelector('i');
                        const originalClass = icon.className;
                        icon.className = 'fas fa-check';

                        setTimeout(() => {
                            icon.className = originalClass;
                        }, 2000);
                    }).catch(err => {
                        console.error('Failed to copy:', err);
                    });
                } else {
                    // Fallback for older browsers
                    const textArea = document.createElement('textarea');
                    textArea.value = url;
                    textArea.style.position = 'fixed';
                    textArea.style.left = '-999999px';
                    document.body.appendChild(textArea);
                    textArea.select();

                    try {
                        document.execCommand('copy');
                        const icon = this.querySelector('i');
                        const originalClass = icon.className;
                        icon.className = 'fas fa-check';

                        setTimeout(() => {
                            icon.className = originalClass;
                        }, 2000);
                    } catch (err) {
                        console.error('Failed to copy:', err);
                    }

                    document.body.removeChild(textArea);
                }
            });
        });
    });
    </script>

    <?php
endwhile;

get_footer();
