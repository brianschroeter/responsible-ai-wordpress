<?php
/**
 * Resource Grid Section
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get featured resources
$featured_resources = responsibleai_get_resources(6, '');

// If no published resources, show placeholder data
if (empty($featured_resources)) {
    $featured_resources = array(
        array(
            'id'        => 0,
            'title'     => __('AI Ethics Handbook', 'responsible-ai'),
            'excerpt'   => __('Comprehensive guide to ethical considerations in AI development and deployment.', 'responsible-ai'),
            'permalink' => '#',
            'thumbnail' => '',
            'file'      => null,
            'format'    => __('Handbook', 'responsible-ai'),
            'featured'  => true,
        ),
        array(
            'id'        => 0,
            'title'     => __('Responsible AI Framework', 'responsible-ai'),
            'excerpt'   => __('Practical framework for implementing responsible AI practices in your organization.', 'responsible-ai'),
            'permalink' => '#',
            'thumbnail' => '',
            'file'      => null,
            'format'    => __('Framework', 'responsible-ai'),
            'featured'  => true,
        ),
        array(
            'id'        => 0,
            'title'     => __('Bias Detection Toolkit', 'responsible-ai'),
            'excerpt'   => __('Tools and techniques for identifying and mitigating bias in AI systems.', 'responsible-ai'),
            'permalink' => '#',
            'thumbnail' => '',
            'file'      => null,
            'format'    => __('Toolkit', 'responsible-ai'),
            'featured'  => true,
        ),
        array(
            'id'        => 0,
            'title'     => __('AI Transparency Whitepaper', 'responsible-ai'),
            'excerpt'   => __('Best practices for achieving transparency and explainability in AI models.', 'responsible-ai'),
            'permalink' => '#',
            'thumbnail' => '',
            'file'      => null,
            'format'    => __('Whitepaper', 'responsible-ai'),
            'featured'  => true,
        ),
        array(
            'id'        => 0,
            'title'     => __('Governance Standards Guide', 'responsible-ai'),
            'excerpt'   => __('Industry standards and regulations for AI governance and compliance.', 'responsible-ai'),
            'permalink' => '#',
            'thumbnail' => '',
            'file'      => null,
            'format'    => __('Handbook', 'responsible-ai'),
            'featured'  => true,
        ),
        array(
            'id'        => 0,
            'title'     => __('AI Safety Assessment Tool', 'responsible-ai'),
            'excerpt'   => __('Risk assessment framework for evaluating AI system safety and security.', 'responsible-ai'),
            'permalink' => '#',
            'thumbnail' => '',
            'file'      => null,
            'format'    => __('Toolkit', 'responsible-ai'),
            'featured'  => true,
        ),
    );
}

// Section heading from ACF or default
$section_title = responsibleai_get_field('resource_grid_title', false, __('Featured Resources', 'responsible-ai'));
$section_subtitle = responsibleai_get_field('resource_grid_subtitle', false, __('Download our comprehensive guides, frameworks, and toolkits', 'responsible-ai'));
$resources_archive_url = get_post_type_archive_link('rai_resource') ?: home_url('/resources/');
?>

<section class="resource-grid-section" id="resources">
    <div class="container">
        <!-- Section Header -->
        <div class="section-header" data-animate>
            <h2 class="section-title"><?php echo esc_html($section_title); ?></h2>
            <?php if (!empty($section_subtitle)) : ?>
                <p class="section-subtitle"><?php echo esc_html($section_subtitle); ?></p>
            <?php endif; ?>
        </div>

        <!-- Resource Grid -->
        <div class="resource-grid">
            <?php foreach ($featured_resources as $index => $resource) : ?>
                <article class="resource-card"
                         data-animate
                         style="animation-delay: <?php echo esc_attr($index * 0.1); ?>s;">

                    <!-- Resource Thumbnail (optional) -->
                    <?php if (!empty($resource['thumbnail'])) : ?>
                        <div class="resource-card__image">
                            <a href="<?php echo esc_url($resource['permalink']); ?>"
                               aria-label="<?php echo esc_attr(sprintf(__('View %s', 'responsible-ai'), $resource['title'])); ?>">
                                <img src="<?php echo esc_url($resource['thumbnail']); ?>"
                                     alt="<?php echo esc_attr($resource['title']); ?>"
                                     loading="lazy">
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="resource-card__content">
                        <!-- Resource Type Badge -->
                        <?php if (!empty($resource['format'])) : ?>
                            <span class="resource-card__badge" aria-label="<?php echo esc_attr(sprintf(__('Resource type: %s', 'responsible-ai'), $resource['format'])); ?>">
                                <i class="fa-solid fa-file-lines" aria-hidden="true"></i>
                                <?php echo esc_html($resource['format']); ?>
                            </span>
                        <?php endif; ?>

                        <!-- Resource Title -->
                        <h3 class="resource-card__title">
                            <a href="<?php echo esc_url($resource['permalink']); ?>">
                                <?php echo esc_html($resource['title']); ?>
                            </a>
                        </h3>

                        <!-- Resource Excerpt -->
                        <?php if (!empty($resource['excerpt'])) : ?>
                            <p class="resource-card__excerpt">
                                <?php echo esc_html($resource['excerpt']); ?>
                            </p>
                        <?php endif; ?>

                        <!-- Resource Action -->
                        <div class="resource-card__action">
                            <?php if (!empty($resource['file'])) : ?>
                                <a href="<?php echo esc_url($resource['file']['url']); ?>"
                                   class="btn btn-sm btn-outline"
                                   download
                                   aria-label="<?php echo esc_attr(sprintf(__('Download %s', 'responsible-ai'), $resource['title'])); ?>">
                                    <i class="fa-solid fa-download" aria-hidden="true"></i>
                                    <?php esc_html_e('Download', 'responsible-ai'); ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php echo esc_url($resource['permalink']); ?>"
                                   class="btn btn-sm btn-outline"
                                   aria-label="<?php echo esc_attr(sprintf(__('Read more about %s', 'responsible-ai'), $resource['title'])); ?>">
                                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                    <?php esc_html_e('Read More', 'responsible-ai'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- View All Resources Button -->
        <div class="resource-grid__footer" data-animate>
            <a href="<?php echo esc_url($resources_archive_url); ?>"
               class="btn btn-primary btn-lg">
                <?php esc_html_e('View All Resources', 'responsible-ai'); ?>
                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</section>

<style>
/* ==========================================================================
   Resource Grid Section
   ========================================================================== */

.resource-grid-section {
    padding: var(--space-16) 0;
    background: var(--color-background);
    position: relative;
    overflow: hidden;
}

.resource-grid-section::before {
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
.resource-grid-section .section-header {
    text-align: center;
    max-width: 800px;
    margin: 0 auto var(--space-12);
}

.resource-grid-section .section-title {
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: var(--font-weight-bold);
    line-height: 1.2;
    margin-bottom: var(--space-4);
    background: linear-gradient(135deg, var(--color-foreground) 0%, var(--color-primary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.resource-grid-section .section-subtitle {
    font-size: clamp(1rem, 2vw, 1.25rem);
    color: var(--color-foreground-secondary);
    line-height: 1.6;
}

/* Resource Grid Layout */
.resource-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: var(--space-8);
    max-width: 1200px;
    margin: 0 auto var(--space-12);
}

/* Resource Card */
.resource-card {
    background: var(--color-background-elevated);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    transition: all var(--duration-default) var(--ease-default);
    display: flex;
    flex-direction: column;
    height: 100%;
}

.resource-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    border-color: var(--color-primary);
}

.resource-card:focus-within {
    outline: 2px solid var(--color-primary);
    outline-offset: 2px;
}

/* Resource Card Image */
.resource-card__image {
    position: relative;
    width: 100%;
    height: 200px;
    overflow: hidden;
    background: var(--color-background);
}

.resource-card__image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--duration-slow) var(--ease-default);
}

.resource-card:hover .resource-card__image img {
    transform: scale(1.05);
}

/* Resource Card Content */
.resource-card__content {
    padding: var(--space-6);
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

/* Resource Type Badge */
.resource-card__badge {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    font-size: 0.75rem;
    font-weight: var(--font-weight-semibold);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--color-primary);
    background: var(--color-primary-muted);
    padding: var(--space-2) var(--space-3);
    border-radius: var(--radius-full);
    margin-bottom: var(--space-4);
    width: fit-content;
}

.resource-card__badge i {
    font-size: 0.875rem;
}

/* Resource Title */
.resource-card__title {
    font-size: 1.25rem;
    font-weight: var(--font-weight-bold);
    line-height: 1.3;
    margin-bottom: var(--space-3);
}

.resource-card__title a {
    color: var(--color-foreground);
    text-decoration: none;
    transition: color var(--duration-fast) var(--ease-default);
}

.resource-card__title a:hover {
    color: var(--color-primary);
}

.resource-card__title a:focus {
    outline: 2px solid var(--color-primary);
    outline-offset: 2px;
    border-radius: var(--radius-sm);
}

/* Resource Excerpt */
.resource-card__excerpt {
    font-size: 0.9375rem;
    line-height: 1.6;
    color: var(--color-foreground-secondary);
    margin-bottom: var(--space-6);
    flex-grow: 1;
}

/* Resource Action Button */
.resource-card__action {
    margin-top: auto;
}

.resource-card__action .btn {
    width: 100%;
    justify-content: center;
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    font-weight: var(--font-weight-semibold);
}

.resource-card__action .btn i {
    font-size: 0.875rem;
    transition: transform var(--duration-fast) var(--ease-default);
}

.resource-card__action .btn:hover i {
    transform: translateX(4px);
}

/* View All Resources Footer */
.resource-grid__footer {
    text-align: center;
    padding-top: var(--space-4);
}

.resource-grid__footer .btn {
    display: inline-flex;
    align-items: center;
    gap: var(--space-3);
    font-weight: var(--font-weight-semibold);
    font-size: 1.125rem;
}

.resource-grid__footer .btn i {
    transition: transform var(--duration-fast) var(--ease-default);
}

.resource-grid__footer .btn:hover i {
    transform: translateX(4px);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .resource-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: var(--space-6);
    }
}

@media (max-width: 768px) {
    .resource-grid-section {
        padding: var(--space-12) 0;
    }

    .resource-grid {
        grid-template-columns: 1fr;
        gap: var(--space-6);
        margin-bottom: var(--space-8);
    }

    .resource-card__image {
        height: 180px;
    }

    .resource-card__content {
        padding: var(--space-5);
    }

    .resource-card__title {
        font-size: 1.125rem;
    }

    .resource-card__excerpt {
        font-size: 0.875rem;
    }

    .resource-grid__footer .btn {
        font-size: 1rem;
        padding: var(--space-4) var(--space-6);
    }
}

@media (max-width: 480px) {
    .resource-card__image {
        height: 160px;
    }

    .resource-card__content {
        padding: var(--space-4);
    }
}

/* Accessibility: Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    .resource-card,
    .resource-card__image img,
    .resource-card__action .btn i,
    .resource-grid__footer .btn i {
        transition: none;
    }

    .resource-card:hover {
        transform: none;
    }

    .resource-card:hover .resource-card__image img {
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

.resource-card[data-animate] {
    opacity: 0;
}

.resource-card[data-animate].is-visible {
    animation: fadeInUp 0.6s var(--ease-default) forwards;
}

.resource-grid__footer[data-animate] {
    opacity: 0;
}

.resource-grid__footer[data-animate].is-visible {
    animation: fadeInUp 0.6s var(--ease-default) forwards;
}

/* Print Styles */
@media print {
    .resource-grid-section {
        padding: var(--space-8) 0;
    }

    .resource-card {
        break-inside: avoid;
        box-shadow: none;
        border: 1px solid #ddd;
    }

    .resource-card:hover {
        transform: none;
        box-shadow: none;
    }

    .resource-card__action {
        display: none;
    }

    .resource-grid__footer {
        display: none;
    }
}
</style>
