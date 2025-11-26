<?php
/**
 * Resources Archive Template
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Get current taxonomy filter
$current_type = get_query_var('resource_type');
$search_query = get_search_query();

// Get all resource types
$resource_types = get_terms(array(
    'taxonomy'   => 'resource_type',
    'hide_empty' => true,
));
?>

<!-- Hero Section -->
<section class="page-hero page-hero--resources">
    <div class="container">
        <div class="page-hero__content">
            <h1 class="page-hero__title">
                <?php esc_html_e('Resources', 'responsible-ai'); ?>
            </h1>
            <p class="page-hero__description">
                <?php esc_html_e('Tools, guides, and resources to help you build responsible AI systems. Download frameworks, access best practices, and learn from industry experts.', 'responsible-ai'); ?>
            </p>
        </div>
    </div>
</section>

<!-- Filter Bar -->
<section class="resources-filters section-compact">
    <div class="container">
        <form class="filter-bar" method="get" action="<?php echo esc_url(get_post_type_archive_link('rai_resource')); ?>">
            <div class="filter-bar__inner">
                <!-- Resource Type Filter -->
                <div class="filter-group">
                    <label for="resource-type-filter" class="sr-only">
                        <?php esc_html_e('Filter by Type', 'responsible-ai'); ?>
                    </label>
                    <select name="resource_type" id="resource-type-filter" class="filter-select">
                        <option value="">
                            <?php esc_html_e('All Types', 'responsible-ai'); ?>
                        </option>
                        <?php if (!empty($resource_types) && !is_wp_error($resource_types)) : ?>
                            <?php foreach ($resource_types as $type) : ?>
                                <option value="<?php echo esc_attr($type->slug); ?>"
                                        <?php selected($current_type, $type->slug); ?>>
                                    <?php echo esc_html($type->name); ?> (<?php echo absint($type->count); ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Search Input -->
                <div class="filter-group filter-group--search">
                    <label for="resource-search" class="sr-only">
                        <?php esc_html_e('Search Resources', 'responsible-ai'); ?>
                    </label>
                    <div class="search-input">
                        <i class="fas fa-search search-input__icon"></i>
                        <input type="search"
                               id="resource-search"
                               name="s"
                               class="search-input__field"
                               placeholder="<?php esc_attr_e('Search resources...', 'responsible-ai'); ?>"
                               value="<?php echo esc_attr($search_query); ?>">
                    </div>
                </div>

                <!-- Filter Button -->
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i>
                    <?php esc_html_e('Filter', 'responsible-ai'); ?>
                </button>

                <!-- Clear Filters -->
                <?php if ($current_type || $search_query) : ?>
                    <a href="<?php echo esc_url(get_post_type_archive_link('rai_resource')); ?>"
                       class="btn btn-outline">
                        <i class="fas fa-times"></i>
                        <?php esc_html_e('Clear', 'responsible-ai'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <!-- Active Filters Display -->
        <?php if ($current_type || $search_query) : ?>
            <div class="active-filters">
                <span class="active-filters__label">
                    <?php esc_html_e('Active filters:', 'responsible-ai'); ?>
                </span>
                <?php if ($current_type) :
                    $term = get_term_by('slug', $current_type, 'resource_type');
                    if ($term) :
                ?>
                    <span class="filter-tag">
                        <?php echo esc_html($term->name); ?>
                        <a href="<?php echo esc_url(remove_query_arg('resource_type')); ?>"
                           class="filter-tag__remove"
                           aria-label="<?php esc_attr_e('Remove filter', 'responsible-ai'); ?>">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                <?php
                    endif;
                endif;
                ?>
                <?php if ($search_query) : ?>
                    <span class="filter-tag">
                        <?php
                        /* translators: %s: search query */
                        printf(esc_html__('Search: %s', 'responsible-ai'), esc_html($search_query));
                        ?>
                        <a href="<?php echo esc_url(remove_query_arg('s')); ?>"
                           class="filter-tag__remove"
                           aria-label="<?php esc_attr_e('Remove search', 'responsible-ai'); ?>">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Resources Grid with Sidebar -->
<section class="section resources-section">
    <div class="container">
        <div class="resources-layout">
            <!-- Main Content -->
            <div class="resources-main">
                <?php if (have_posts()) : ?>
                    <!-- Results Count -->
                    <div class="resources-header">
                        <p class="results-count">
                            <?php
                            global $wp_query;
                            $total = $wp_query->found_posts;
                            /* translators: %s: number of resources found */
                            printf(
                                esc_html(_n('%s resource found', '%s resources found', $total, 'responsible-ai')),
                                '<strong>' . number_format_i18n($total) . '</strong>'
                            );
                            ?>
                        </p>
                    </div>

                    <!-- Resources Grid -->
                    <div class="resources-grid grid grid-cols-2">
                        <?php while (have_posts()) : the_post(); ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class('resource-card card hover-lift'); ?>>
                                <?php
                                // Get resource type badge
                                $types = get_the_terms(get_the_ID(), 'resource_type');
                                $resource_format = get_field('resource_format');
                                $resource_file = get_field('resource_file');
                                $external_url = get_field('external_url');
                                ?>

                                <!-- Resource Image -->
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="resource-card__image">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('resource-thumb', array('class' => 'card__image')); ?>
                                        </a>
                                        <?php if ($types && !is_wp_error($types)) : ?>
                                            <span class="resource-card__badge badge badge--<?php echo esc_attr($types[0]->slug); ?>">
                                                <?php echo esc_html($types[0]->name); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="resource-card__content">
                                    <!-- Resource Format -->
                                    <?php if ($resource_format) : ?>
                                        <div class="resource-card__format">
                                            <i class="fas fa-file-alt"></i>
                                            <span><?php echo esc_html($resource_format); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Title -->
                                    <h3 class="resource-card__title">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_title(); ?>
                                        </a>
                                    </h3>

                                    <!-- Excerpt -->
                                    <?php if (has_excerpt()) : ?>
                                        <div class="resource-card__excerpt">
                                            <?php the_excerpt(); ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Action Button -->
                                    <div class="resource-card__actions">
                                        <?php if ($resource_file) : ?>
                                            <a href="<?php echo esc_url($resource_file['url']); ?>"
                                               class="btn btn-outline btn-sm"
                                               download
                                               target="_blank"
                                               rel="noopener">
                                                <i class="fas fa-download"></i>
                                                <?php esc_html_e('Download', 'responsible-ai'); ?>
                                            </a>
                                        <?php elseif ($external_url) : ?>
                                            <a href="<?php echo esc_url($external_url); ?>"
                                               class="btn btn-outline btn-sm"
                                               target="_blank"
                                               rel="noopener">
                                                <i class="fas fa-external-link-alt"></i>
                                                <?php esc_html_e('View', 'responsible-ai'); ?>
                                            </a>
                                        <?php else : ?>
                                            <a href="<?php the_permalink(); ?>"
                                               class="btn btn-outline btn-sm">
                                                <i class="fas fa-arrow-right"></i>
                                                <?php esc_html_e('Read More', 'responsible-ai'); ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>

                    <!-- Pagination -->
                    <?php
                    $pagination_args = array(
                        'mid_size'  => 2,
                        'prev_text' => '<i class="fas fa-arrow-left"></i> ' . __('Previous', 'responsible-ai'),
                        'next_text' => __('Next', 'responsible-ai') . ' <i class="fas fa-arrow-right"></i>',
                        'class'     => 'resources-pagination',
                    );

                    the_posts_pagination($pagination_args);
                    ?>

                <?php else : ?>
                    <!-- Empty State -->
                    <div class="no-resources text-center">
                        <div class="no-resources__icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h2 class="no-resources__title">
                            <?php esc_html_e('No Resources Found', 'responsible-ai'); ?>
                        </h2>
                        <p class="no-resources__text">
                            <?php
                            if ($search_query || $current_type) {
                                esc_html_e('Try adjusting your filters or search terms.', 'responsible-ai');
                            } else {
                                esc_html_e('No resources have been published yet. Check back soon!', 'responsible-ai');
                            }
                            ?>
                        </p>
                        <?php if ($search_query || $current_type) : ?>
                            <a href="<?php echo esc_url(get_post_type_archive_link('rai_resource')); ?>"
                               class="btn btn-primary">
                                <i class="fas fa-list"></i>
                                <?php esc_html_e('View All Resources', 'responsible-ai'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside class="resources-sidebar">
                <!-- Resource Types Widget -->
                <div class="sidebar-widget">
                    <h3 class="sidebar-widget__title">
                        <?php esc_html_e('Resource Types', 'responsible-ai'); ?>
                    </h3>
                    <?php if (!empty($resource_types) && !is_wp_error($resource_types)) : ?>
                        <ul class="resource-types-list">
                            <li class="<?php echo empty($current_type) ? 'active' : ''; ?>">
                                <a href="<?php echo esc_url(get_post_type_archive_link('rai_resource')); ?>">
                                    <span class="type-name">
                                        <?php esc_html_e('All Resources', 'responsible-ai'); ?>
                                    </span>
                                    <span class="type-count">
                                        <?php
                                        $total_count = wp_count_posts('rai_resource');
                                        echo absint($total_count->publish);
                                        ?>
                                    </span>
                                </a>
                            </li>
                            <?php foreach ($resource_types as $type) : ?>
                                <li class="<?php echo ($current_type === $type->slug) ? 'active' : ''; ?>">
                                    <a href="<?php echo esc_url(get_term_link($type)); ?>">
                                        <span class="type-name"><?php echo esc_html($type->name); ?></span>
                                        <span class="type-count"><?php echo absint($type->count); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        <p class="text-muted">
                            <?php esc_html_e('No resource types available.', 'responsible-ai'); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Featured Resources Widget -->
                <?php
                $featured_resources = get_posts(array(
                    'post_type'      => 'rai_resource',
                    'posts_per_page' => 3,
                    'post_status'    => 'publish',
                    'meta_query'     => array(
                        array(
                            'key'     => 'featured',
                            'value'   => '1',
                            'compare' => '=',
                        ),
                    ),
                ));

                if ($featured_resources) :
                ?>
                    <div class="sidebar-widget">
                        <h3 class="sidebar-widget__title">
                            <?php esc_html_e('Featured Resources', 'responsible-ai'); ?>
                        </h3>
                        <div class="featured-resources">
                            <?php foreach ($featured_resources as $featured) : ?>
                                <div class="featured-resource">
                                    <?php if (has_post_thumbnail($featured->ID)) : ?>
                                        <div class="featured-resource__image">
                                            <a href="<?php echo esc_url(get_permalink($featured->ID)); ?>">
                                                <?php echo get_the_post_thumbnail($featured->ID, 'thumbnail'); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <div class="featured-resource__content">
                                        <h4 class="featured-resource__title">
                                            <a href="<?php echo esc_url(get_permalink($featured->ID)); ?>">
                                                <?php echo esc_html(get_the_title($featured->ID)); ?>
                                            </a>
                                        </h4>
                                        <?php
                                        $featured_types = get_the_terms($featured->ID, 'resource_type');
                                        if ($featured_types && !is_wp_error($featured_types)) :
                                        ?>
                                            <span class="featured-resource__type text-muted">
                                                <?php echo esc_html($featured_types[0]->name); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php
                    wp_reset_postdata();
                endif;
                ?>

                <!-- Topics Widget -->
                <?php
                $topics = get_terms(array(
                    'taxonomy'   => 'resource_topic',
                    'hide_empty' => true,
                    'number'     => 10,
                ));

                if ($topics && !is_wp_error($topics)) :
                ?>
                    <div class="sidebar-widget">
                        <h3 class="sidebar-widget__title">
                            <?php esc_html_e('Popular Topics', 'responsible-ai'); ?>
                        </h3>
                        <div class="topic-tags">
                            <?php foreach ($topics as $topic) : ?>
                                <a href="<?php echo esc_url(get_term_link($topic)); ?>"
                                   class="topic-tag">
                                    <?php echo esc_html($topic->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Help Widget -->
                <div class="sidebar-widget sidebar-widget--help">
                    <div class="help-box">
                        <div class="help-box__icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <h4 class="help-box__title">
                            <?php esc_html_e('Need Help?', 'responsible-ai'); ?>
                        </h4>
                        <p class="help-box__text">
                            <?php esc_html_e('Can\'t find what you\'re looking for? Contact our team for assistance.', 'responsible-ai'); ?>
                        </p>
                        <a href="<?php echo esc_url(home_url('/contact/')); ?>"
                           class="btn btn-cta btn-sm btn-block">
                            <?php esc_html_e('Contact Us', 'responsible-ai'); ?>
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php get_footer(); ?>
