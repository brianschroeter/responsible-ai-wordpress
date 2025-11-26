<?php
/**
 * News Grid Section
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get latest blog posts
$posts = responsibleai_get_blog_posts(3);

// Get section heading from ACF or default
$section_title = responsibleai_get_field('news_section_title', false, __('Latest News & Insights', 'responsible-ai'));
$section_subtitle = responsibleai_get_field('news_section_subtitle', false, __('Stay informed about the latest developments in responsible AI', 'responsible-ai'));

// Early return if no posts
if (empty($posts)) {
    return;
}
?>

<section class="news-grid-section" id="latest-news">
    <div class="container">
        <!-- Section Header -->
        <div class="section-header" data-animate>
            <h2 class="section-title"><?php echo esc_html($section_title); ?></h2>
            <?php if (!empty($section_subtitle)) : ?>
                <p class="section-subtitle"><?php echo esc_html($section_subtitle); ?></p>
            <?php endif; ?>
        </div>

        <!-- News Cards Grid -->
        <div class="news-grid">
            <?php foreach ($posts as $index => $post) : ?>
                <article class="news-card"
                         data-animate
                         style="animation-delay: <?php echo esc_attr($index * 0.1); ?>s;">

                    <!-- Featured Image -->
                    <?php if (!empty($post['thumbnail'])) : ?>
                        <a href="<?php echo esc_url($post['permalink']); ?>"
                           class="news-card__image-link"
                           aria-label="<?php echo esc_attr(sprintf(__('Read article: %s', 'responsible-ai'), $post['title'])); ?>">
                            <div class="news-card__image">
                                <img src="<?php echo esc_url($post['thumbnail']); ?>"
                                     alt="<?php echo esc_attr($post['title']); ?>"
                                     loading="lazy">
                            </div>
                        </a>
                    <?php else : ?>
                        <div class="news-card__image news-card__image--placeholder">
                            <i class="fa-solid fa-newspaper" aria-hidden="true"></i>
                        </div>
                    <?php endif; ?>

                    <!-- Card Content -->
                    <div class="news-card__content">
                        <!-- Date & Categories -->
                        <div class="news-card__meta">
                            <time datetime="<?php echo esc_attr(mysql2date('c', $post['date'])); ?>"
                                  class="news-card__date">
                                <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                <?php echo esc_html($post['date']); ?>
                            </time>

                            <?php if (!empty($post['categories'])) : ?>
                                <span class="news-card__category">
                                    <i class="fa-solid fa-tag" aria-hidden="true"></i>
                                    <?php echo esc_html($post['categories'][0]->name); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Title -->
                        <h3 class="news-card__title">
                            <a href="<?php echo esc_url($post['permalink']); ?>">
                                <?php echo esc_html($post['title']); ?>
                            </a>
                        </h3>

                        <!-- Excerpt -->
                        <?php if (!empty($post['excerpt'])) : ?>
                            <p class="news-card__excerpt">
                                <?php echo esc_html($post['excerpt']); ?>
                            </p>
                        <?php endif; ?>

                        <!-- Read More Link -->
                        <a href="<?php echo esc_url($post['permalink']); ?>"
                           class="news-card__link">
                            <?php esc_html_e('Read More', 'responsible-ai'); ?>
                            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- View All News Button -->
        <div class="news-grid__actions" data-animate>
            <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>"
               class="btn btn-cta btn-lg">
                <?php esc_html_e('View All News', 'responsible-ai'); ?>
                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</section>

<style>
/* ==========================================================================
   News Grid Section
   ========================================================================== */

.news-grid-section {
    padding: var(--space-16) 0;
    background: var(--color-background-elevated);
    position: relative;
}

.news-grid-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(
        90deg,
        transparent 0%,
        var(--color-border) 20%,
        var(--color-border) 80%,
        transparent 100%
    );
}

/* Section Header */
.news-grid-section .section-header {
    text-align: center;
    max-width: 800px;
    margin: 0 auto var(--space-12);
}

.news-grid-section .section-title {
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: var(--font-weight-bold);
    line-height: 1.2;
    margin-bottom: var(--space-4);
    background: linear-gradient(135deg, var(--color-foreground) 0%, var(--color-primary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.news-grid-section .section-subtitle {
    font-size: clamp(1rem, 2vw, 1.25rem);
    color: var(--color-foreground-secondary);
    line-height: 1.6;
}

/* News Grid */
.news-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--space-8);
    max-width: 1200px;
    margin: 0 auto var(--space-10);
}

/* News Card */
.news-card {
    background: var(--color-background);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: all var(--duration-default) var(--ease-default);
    position: relative;
}

.news-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.4);
    border-color: var(--color-border-hover);
}

.news-card:focus-within {
    outline: 2px solid var(--color-primary);
    outline-offset: 4px;
}

/* Card Image */
.news-card__image-link {
    display: block;
    overflow: hidden;
}

.news-card__image {
    aspect-ratio: 16 / 9;
    overflow: hidden;
    position: relative;
    background: var(--color-background-hover);
}

.news-card__image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--duration-slow) var(--ease-default);
}

.news-card:hover .news-card__image img {
    transform: scale(1.05);
}

.news-card__image--placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-foreground-muted);
    font-size: 3rem;
}

/* Card Content */
.news-card__content {
    padding: var(--space-6);
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

/* Meta Information */
.news-card__meta {
    display: flex;
    align-items: center;
    gap: var(--space-4);
    margin-bottom: var(--space-4);
    font-size: var(--font-size-sm);
    color: var(--color-foreground-muted);
    flex-wrap: wrap;
}

.news-card__date,
.news-card__category {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
}

.news-card__category {
    color: var(--color-primary);
    font-weight: var(--font-weight-medium);
}

/* Card Title */
.news-card__title {
    font-size: var(--font-size-xl);
    font-weight: var(--font-weight-bold);
    line-height: var(--line-height-tight);
    margin-bottom: var(--space-4);
}

.news-card__title a {
    color: var(--color-foreground);
    text-decoration: none;
    transition: color var(--duration-fast) var(--ease-default);
}

.news-card__title a:hover {
    color: var(--color-primary);
}

.news-card__title a:focus {
    outline: 2px solid var(--color-primary);
    outline-offset: 2px;
    border-radius: var(--radius-sm);
}

/* Card Excerpt */
.news-card__excerpt {
    font-size: var(--font-size-base);
    color: var(--color-foreground-secondary);
    line-height: var(--line-height-relaxed);
    margin-bottom: var(--space-6);
    flex-grow: 1;
}

/* Read More Link */
.news-card__link {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    color: var(--color-primary);
    font-size: var(--font-size-base);
    font-weight: var(--font-weight-semibold);
    text-decoration: none;
    transition: all var(--duration-fast) var(--ease-default);
    margin-top: auto;
}

.news-card__link:hover {
    color: var(--color-primary-light);
    gap: var(--space-3);
}

.news-card__link:focus {
    outline: 2px solid var(--color-primary);
    outline-offset: 4px;
    border-radius: var(--radius-sm);
}

.news-card__link i {
    transition: transform var(--duration-fast) var(--ease-default);
}

.news-card__link:hover i {
    transform: translateX(4px);
}

/* View All Button */
.news-grid__actions {
    text-align: center;
    padding-top: var(--space-4);
}

.news-grid__actions .btn {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
}

.news-grid__actions .btn i {
    transition: transform var(--duration-fast) var(--ease-default);
}

.news-grid__actions .btn:hover i {
    transform: translateX(4px);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .news-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: var(--space-6);
    }
}

@media (max-width: 768px) {
    .news-grid-section {
        padding: var(--space-12) 0;
    }

    .news-grid {
        grid-template-columns: 1fr;
        gap: var(--space-6);
    }

    .news-card {
        max-width: 500px;
        margin: 0 auto;
    }

    .news-card__content {
        padding: var(--space-5);
    }

    .news-card__title {
        font-size: var(--font-size-lg);
    }
}

@media (max-width: 480px) {
    .news-card__content {
        padding: var(--space-4);
    }

    .news-card__meta {
        flex-direction: column;
        align-items: flex-start;
        gap: var(--space-2);
    }

    .news-card__title {
        font-size: var(--font-size-base);
    }

    .news-card__excerpt {
        font-size: var(--font-size-sm);
    }
}

/* Accessibility: Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    .news-card,
    .news-card__image img,
    .news-card__title a,
    .news-card__link,
    .news-card__link i,
    .news-grid__actions .btn i {
        transition: none;
    }

    .news-card:hover {
        transform: none;
    }

    .news-card:hover .news-card__image img {
        transform: none;
    }

    .news-card__link:hover,
    .news-grid__actions .btn:hover i {
        gap: var(--space-2);
    }

    .news-card__link:hover i,
    .news-grid__actions .btn:hover i {
        transform: none;
    }
}

/* Animation for scroll reveal */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.news-card[data-animate],
.news-grid__actions[data-animate] {
    opacity: 0;
}

.news-card[data-animate].is-visible,
.news-grid__actions[data-animate].is-visible {
    animation: fadeInUp 0.6s var(--ease-default) forwards;
}

/* Print Styles */
@media print {
    .news-grid-section {
        background: white;
        color: black;
    }

    .news-card {
        border: 1px solid #ddd;
        page-break-inside: avoid;
    }

    .news-card:hover {
        transform: none;
        box-shadow: none;
    }

    .news-card__link,
    .news-grid__actions {
        display: none;
    }

    .news-card__image img {
        filter: grayscale(100%);
    }
}
</style>
