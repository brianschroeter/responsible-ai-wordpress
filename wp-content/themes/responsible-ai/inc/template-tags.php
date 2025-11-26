<?php
/**
 * Template Tags - Helper functions for templates
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get organization info from Theme Options
 *
 * @return array Organization information with defaults
 */
function responsibleai_get_organization_info() {
    $defaults = array(
        'name'    => get_bloginfo('name'),
        'email'   => 'info@responsible.ai',
        'phone'   => '',
        'address' => '',
        'tagline' => __('Building trust in AI systems', 'responsible-ai'),
    );

    if (!function_exists('get_field')) {
        return $defaults;
    }

    return array(
        'name'    => get_field('organization_name', 'option') ?: $defaults['name'],
        'email'   => get_field('organization_email', 'option') ?: $defaults['email'],
        'phone'   => get_field('organization_phone', 'option') ?: $defaults['phone'],
        'address' => get_field('organization_address', 'option') ?: $defaults['address'],
        'tagline' => get_field('organization_tagline', 'option') ?: $defaults['tagline'],
    );
}

/**
 * Get social media links from Theme Options
 *
 * @return array Social media URLs
 */
function responsibleai_get_social_links() {
    $defaults = array(
        'linkedin' => 'https://linkedin.com/company/responsibleai',
        'twitter'  => 'https://twitter.com/responsibleai',
        'youtube'  => '',
        'github'   => 'https://github.com/responsibleai',
    );

    if (!function_exists('get_field')) {
        return $defaults;
    }

    return array(
        'linkedin' => get_field('social_linkedin', 'option') ?: $defaults['linkedin'],
        'twitter'  => get_field('social_twitter', 'option') ?: $defaults['twitter'],
        'youtube'  => get_field('social_youtube', 'option') ?: $defaults['youtube'],
        'github'   => get_field('social_github', 'option') ?: $defaults['github'],
    );
}

/**
 * Get homepage fields
 *
 * @return array Homepage section data with defaults
 */
function responsibleai_get_homepage_fields() {
    $defaults = array(
        'enable_hero'        => true,
        'enable_mission'     => true,
        'enable_pathways'    => true,
        'enable_agents'      => true,
        'enable_partners'    => true,
        'enable_stats'       => true,
        'enable_resources'   => true,
        'enable_testimonials'=> true,
        'enable_news'        => true,
        'enable_newsletter'  => true,
        'enable_cta'         => true,
        'hero_slides'        => array(),
        'mission_title'      => __('Our Mission', 'responsible-ai'),
        'mission_content'    => '',
        'stats'              => array(),
        'pathways'           => array(),
        'ai_agents'          => array(),
        'partner_logos'      => array(),
        'testimonials'       => array(),
    );

    if (!function_exists('get_field')) {
        return $defaults;
    }

    return array(
        'enable_hero'        => get_field('enable_hero') !== false,
        'enable_mission'     => get_field('enable_mission') !== false,
        'enable_pathways'    => get_field('enable_pathways') !== false,
        'enable_agents'      => get_field('enable_agents') !== false,
        'enable_partners'    => get_field('enable_partners') !== false,
        'enable_stats'       => get_field('enable_stats') !== false,
        'enable_resources'   => get_field('enable_resources') !== false,
        'enable_testimonials'=> get_field('enable_testimonials') !== false,
        'enable_news'        => get_field('enable_news') !== false,
        'enable_newsletter'  => get_field('enable_newsletter') !== false,
        'enable_cta'         => get_field('enable_cta') !== false,
        'hero_slides'        => get_field('hero_slides') ?: $defaults['hero_slides'],
        'mission_title'      => get_field('mission_title') ?: $defaults['mission_title'],
        'mission_content'    => get_field('mission_content') ?: $defaults['mission_content'],
        'stats'              => get_field('stats') ?: $defaults['stats'],
        'pathways'           => get_field('pathways') ?: $defaults['pathways'],
        'ai_agents'          => get_field('ai_agents') ?: $defaults['ai_agents'],
        'partner_logos'      => get_field('partner_logos') ?: $defaults['partner_logos'],
        'testimonials'       => get_field('testimonials') ?: $defaults['testimonials'],
    );
}

/**
 * Get Who We Are page fields
 *
 * @return array Page section data with defaults
 */
function responsibleai_get_who_we_are_fields() {
    $defaults = array(
        'enable_story'   => true,
        'enable_mission' => true,
        'enable_values'  => true,
        'enable_team'    => true,
        'story_title'    => __('Our Story', 'responsible-ai'),
        'story_content'  => '',
        'mission_text'   => '',
        'vision_text'    => '',
        'values'         => array(),
        'team_board'     => array(),
        'team_fellows'   => array(),
        'team_advisors'  => array(),
    );

    if (!function_exists('get_field')) {
        return $defaults;
    }

    return array(
        'enable_story'   => get_field('enable_story') !== false,
        'enable_mission' => get_field('enable_mission_vision') !== false,
        'enable_values'  => get_field('enable_values') !== false,
        'enable_team'    => get_field('enable_team') !== false,
        'story_title'    => get_field('story_title') ?: $defaults['story_title'],
        'story_content'  => get_field('story_content') ?: $defaults['story_content'],
        'story_image'    => get_field('story_image') ?: null,
        'mission_text'   => get_field('mission_text') ?: $defaults['mission_text'],
        'vision_text'    => get_field('vision_text') ?: $defaults['vision_text'],
        'values'         => get_field('values') ?: $defaults['values'],
        'team_board'     => get_field('team_board') ?: $defaults['team_board'],
        'team_fellows'   => get_field('team_fellows') ?: $defaults['team_fellows'],
        'team_advisors'  => get_field('team_advisors') ?: $defaults['team_advisors'],
    );
}

/**
 * Get events
 *
 * @param int    $limit    Number of events to retrieve
 * @param string $status   'upcoming', 'past', or 'all'
 * @return array Array of event data
 */
function responsibleai_get_events($limit = -1, $status = 'upcoming') {
    $events = array();

    $args = array(
        'post_type'      => 'rai_event',
        'posts_per_page' => $limit,
        'post_status'    => 'publish',
        'meta_key'       => 'event_date',
        'orderby'        => 'meta_value',
        'order'          => $status === 'past' ? 'DESC' : 'ASC',
    );

    // Filter by date
    if ($status === 'upcoming') {
        $args['meta_query'] = array(
            array(
                'key'     => 'event_date',
                'value'   => current_time('Y-m-d'),
                'compare' => '>=',
                'type'    => 'DATE',
            ),
        );
    } elseif ($status === 'past') {
        $args['meta_query'] = array(
            array(
                'key'     => 'event_date',
                'value'   => current_time('Y-m-d'),
                'compare' => '<',
                'type'    => 'DATE',
            ),
        );
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $events[] = array(
                'id'          => get_the_ID(),
                'title'       => get_the_title(),
                'permalink'   => get_permalink(),
                'excerpt'     => get_the_excerpt(),
                'thumbnail'   => get_the_post_thumbnail_url(get_the_ID(), 'blog-card'),
                'event_date'  => get_field('event_date'),
                'event_type'  => get_field('event_format'),
                'location'    => get_field('event_location'),
                'reg_url'     => get_field('registration_url'),
            );
        }
        wp_reset_postdata();
    }

    return $events;
}

/**
 * Get resources
 *
 * @param int    $limit    Number of resources to retrieve
 * @param string $type     Resource type taxonomy term slug
 * @return array Array of resource data
 */
function responsibleai_get_resources($limit = -1, $type = '') {
    $resources = array();

    $args = array(
        'post_type'      => 'rai_resource',
        'posts_per_page' => $limit,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    if (!empty($type)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'resource_type',
                'field'    => 'slug',
                'terms'    => $type,
            ),
        );
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $resources[] = array(
                'id'          => get_the_ID(),
                'title'       => get_the_title(),
                'permalink'   => get_permalink(),
                'excerpt'     => get_the_excerpt(),
                'thumbnail'   => get_the_post_thumbnail_url(get_the_ID(), 'resource-thumb'),
                'file'        => get_field('resource_file'),
                'format'      => get_field('resource_format'),
                'featured'    => get_field('featured'),
            );
        }
        wp_reset_postdata();
    }

    return $resources;
}

/**
 * Get case studies
 *
 * @param int $limit Number of case studies to retrieve
 * @return array Array of case study data
 */
function responsibleai_get_case_studies($limit = -1) {
    $case_studies = array();

    $args = array(
        'post_type'      => 'case_study',
        'posts_per_page' => $limit,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $case_studies[] = array(
                'id'         => get_the_ID(),
                'title'      => get_the_title(),
                'permalink'  => get_permalink(),
                'excerpt'    => get_the_excerpt(),
                'thumbnail'  => get_the_post_thumbnail_url(get_the_ID(), 'blog-card'),
                'client'     => get_field('client_name'),
                'industry'   => get_the_terms(get_the_ID(), 'industry'),
                'metrics'    => get_field('key_metrics'),
            );
        }
        wp_reset_postdata();
    }

    return $case_studies;
}

/**
 * Get recent blog posts
 *
 * @param int $limit Number of posts to retrieve
 * @return array Array of post data
 */
function responsibleai_get_blog_posts($limit = 3) {
    $posts_data = array();

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => $limit,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $posts_data[] = array(
                'id'         => get_the_ID(),
                'title'      => get_the_title(),
                'permalink'  => get_permalink(),
                'excerpt'    => get_the_excerpt(),
                'thumbnail'  => get_the_post_thumbnail_url(get_the_ID(), 'blog-card'),
                'date'       => get_the_date(),
                'author'     => get_the_author(),
                'categories' => get_the_category(),
            );
        }
        wp_reset_postdata();
    }

    return $posts_data;
}

/**
 * Output team member card
 *
 * @param array $member Team member data from ACF repeater
 */
function responsibleai_team_card($member) {
    if (empty($member)) {
        return;
    }

    $name = isset($member['name']) ? $member['name'] : '';
    $title = isset($member['job_title']) ? $member['job_title'] : '';
    $org = isset($member['organization']) ? $member['organization'] : '';
    $photo = isset($member['photo']) ? $member['photo'] : null;
    $bio = isset($member['short_bio']) ? $member['short_bio'] : '';
    $linkedin = isset($member['linkedin']) ? $member['linkedin'] : '';

    ?>
    <div class="team-card">
        <div class="team-card__image">
            <?php if ($photo) : ?>
                <img src="<?php echo esc_url($photo['sizes']['team-photo'] ?? $photo['url']); ?>"
                     alt="<?php echo esc_attr($name); ?>"
                     loading="lazy">
            <?php else : ?>
                <div class="team-card__placeholder">
                    <i class="fas fa-user"></i>
                </div>
            <?php endif; ?>
        </div>
        <div class="team-card__content">
            <h3 class="team-card__name"><?php echo esc_html($name); ?></h3>
            <?php if ($title) : ?>
                <p class="team-card__title"><?php echo esc_html($title); ?></p>
            <?php endif; ?>
            <?php if ($org) : ?>
                <p class="team-card__org"><?php echo esc_html($org); ?></p>
            <?php endif; ?>
            <?php if ($bio) : ?>
                <p class="team-card__bio"><?php echo esc_html($bio); ?></p>
            <?php endif; ?>
            <?php if ($linkedin) : ?>
                <a href="<?php echo esc_url($linkedin); ?>" class="team-card__linkedin" target="_blank" rel="noopener">
                    <i class="fab fa-linkedin"></i>
                    <span class="sr-only"><?php esc_html_e('LinkedIn Profile', 'responsible-ai'); ?></span>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/**
 * Output flip card (for AI Agents section)
 *
 * @param array $data Card data with front and back content
 */
function responsibleai_flip_card($data) {
    if (empty($data)) {
        return;
    }

    $name = isset($data['agent_name']) ? $data['agent_name'] : '';
    $tagline = isset($data['agent_tagline']) ? $data['agent_tagline'] : '';
    $description = isset($data['agent_description']) ? $data['agent_description'] : '';
    $icon = isset($data['agent_icon']) ? $data['agent_icon'] : null;
    $link = isset($data['agent_link']) ? $data['agent_link'] : '';

    ?>
    <div class="flip-card" tabindex="0">
        <div class="flip-card__inner">
            <div class="flip-card__front">
                <?php if ($icon) : ?>
                    <div class="flip-card__icon">
                        <img src="<?php echo esc_url($icon['url']); ?>" alt="<?php echo esc_attr($name); ?>">
                    </div>
                <?php endif; ?>
                <h3 class="flip-card__title"><?php echo esc_html($name); ?></h3>
                <p class="flip-card__tagline"><?php echo esc_html($tagline); ?></p>
            </div>
            <div class="flip-card__back">
                <h3 class="flip-card__title"><?php echo esc_html($name); ?></h3>
                <p class="flip-card__description"><?php echo esc_html($description); ?></p>
                <?php if ($link) : ?>
                    <a href="<?php echo esc_url($link); ?>" class="flip-card__link">
                        <?php esc_html_e('Learn More', 'responsible-ai'); ?>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Output resource card
 *
 * @param array $resource Resource data
 */
function responsibleai_resource_card($resource) {
    if (empty($resource)) {
        return;
    }
    ?>
    <div class="resource-card">
        <?php if (!empty($resource['thumbnail'])) : ?>
            <div class="resource-card__image">
                <img src="<?php echo esc_url($resource['thumbnail']); ?>"
                     alt="<?php echo esc_attr($resource['title']); ?>"
                     loading="lazy">
            </div>
        <?php endif; ?>
        <div class="resource-card__content">
            <h3 class="resource-card__title">
                <a href="<?php echo esc_url($resource['permalink']); ?>">
                    <?php echo esc_html($resource['title']); ?>
                </a>
            </h3>
            <?php if (!empty($resource['excerpt'])) : ?>
                <p class="resource-card__excerpt"><?php echo esc_html($resource['excerpt']); ?></p>
            <?php endif; ?>
            <?php if (!empty($resource['format'])) : ?>
                <span class="resource-card__format"><?php echo esc_html($resource['format']); ?></span>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/**
 * Output blog/news card
 *
 * @param array $post Post data
 */
function responsibleai_blog_card($post) {
    if (empty($post)) {
        return;
    }
    ?>
    <article class="blog-card">
        <?php if (!empty($post['thumbnail'])) : ?>
            <div class="blog-card__image">
                <a href="<?php echo esc_url($post['permalink']); ?>">
                    <img src="<?php echo esc_url($post['thumbnail']); ?>"
                         alt="<?php echo esc_attr($post['title']); ?>"
                         loading="lazy">
                </a>
            </div>
        <?php endif; ?>
        <div class="blog-card__content">
            <?php if (!empty($post['categories'])) : ?>
                <div class="blog-card__categories">
                    <?php foreach ($post['categories'] as $cat) : ?>
                        <span class="blog-card__category"><?php echo esc_html($cat->name); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <h3 class="blog-card__title">
                <a href="<?php echo esc_url($post['permalink']); ?>">
                    <?php echo esc_html($post['title']); ?>
                </a>
            </h3>
            <?php if (!empty($post['excerpt'])) : ?>
                <p class="blog-card__excerpt"><?php echo esc_html($post['excerpt']); ?></p>
            <?php endif; ?>
            <div class="blog-card__meta">
                <span class="blog-card__date"><?php echo esc_html($post['date']); ?></span>
            </div>
        </div>
    </article>
    <?php
}

/**
 * Output stat counter
 *
 * @param string $number The stat number (e.g., "500+")
 * @param string $label  The stat label
 */
function responsibleai_stat_counter($number, $label) {
    ?>
    <div class="stat-counter">
        <span class="stat-counter__number" data-target="<?php echo esc_attr($number); ?>">
            <?php echo esc_html($number); ?>
        </span>
        <span class="stat-counter__label"><?php echo esc_html($label); ?></span>
    </div>
    <?php
}
