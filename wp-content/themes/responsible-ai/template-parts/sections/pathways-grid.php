<?php
/**
 * RAISE Certification Pathways Grid Section
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get pathways from ACF
$pathways = responsibleai_get_field('certification_pathways', false, array());

// If no pathways, show default data
if (empty($pathways)) {
    $pathways = array(
        array(
            'pathway_icon'        => 'fa-user-check',
            'pathway_title'       => __('RAISE Certified Practitioner', 'responsible-ai'),
            'pathway_description' => __('Individual certification for AI professionals demonstrating expertise in responsible AI principles, practices, and ethical implementation.', 'responsible-ai'),
            'pathway_link'        => home_url('/certification/practitioner/'),
        ),
        array(
            'pathway_icon'        => 'fa-building-shield',
            'pathway_title'       => __('RAISE Certified Organization', 'responsible-ai'),
            'pathway_description' => __('Enterprise certification validating organizational commitment to responsible AI governance, policies, and culture across all operations.', 'responsible-ai'),
            'pathway_link'        => home_url('/certification/organization/'),
        ),
        array(
            'pathway_icon'        => 'fa-microchip',
            'pathway_title'       => __('RAISE Certified Product', 'responsible-ai'),
            'pathway_description' => __('Product certification ensuring AI systems meet rigorous standards for safety, fairness, transparency, and accountability throughout their lifecycle.', 'responsible-ai'),
            'pathway_link'        => home_url('/certification/product/'),
        ),
        array(
            'pathway_icon'        => 'fa-handshake',
            'pathway_title'       => __('RAISE Certified Service', 'responsible-ai'),
            'pathway_description' => __('Service certification for AI-powered offerings demonstrating responsible practices in delivery, support, and continuous improvement.', 'responsible-ai'),
            'pathway_link'        => home_url('/certification/service/'),
        ),
    );
}

// Section heading from ACF or default
$section_title = responsibleai_get_field('pathways_section_title', false, __('RAISE Certification Pathways', 'responsible-ai'));
$section_subtitle = responsibleai_get_field('pathways_section_subtitle', false, __('Choose the certification path that aligns with your goals and commitment to responsible AI.', 'responsible-ai'));
?>

<section class="pathways-section" id="certification-pathways">
    <div class="container">
        <!-- Section Header -->
        <div class="section-header" data-animate>
            <h2 class="section-title"><?php echo esc_html($section_title); ?></h2>
            <?php if (!empty($section_subtitle)) : ?>
                <p class="section-subtitle"><?php echo esc_html($section_subtitle); ?></p>
            <?php endif; ?>
        </div>

        <!-- Pathways Grid -->
        <div class="pathways-grid">
            <?php foreach ($pathways as $index => $pathway) : ?>
                <div class="pathway-card"
                     data-animate
                     style="animation-delay: <?php echo esc_attr($index * 0.1); ?>s;">

                    <div class="pathway-card__inner">
                        <!-- Icon -->
                        <?php if (!empty($pathway['pathway_icon'])) : ?>
                            <div class="pathway-card__icon-wrapper" aria-hidden="true">
                                <div class="pathway-card__icon">
                                    <i class="fa-solid <?php echo esc_attr($pathway['pathway_icon']); ?>"></i>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Content -->
                        <div class="pathway-card__content">
                            <?php if (!empty($pathway['pathway_title'])) : ?>
                                <h3 class="pathway-card__title">
                                    <?php echo esc_html($pathway['pathway_title']); ?>
                                </h3>
                            <?php endif; ?>

                            <?php if (!empty($pathway['pathway_description'])) : ?>
                                <p class="pathway-card__description">
                                    <?php echo esc_html($pathway['pathway_description']); ?>
                                </p>
                            <?php endif; ?>

                            <!-- Call to Action -->
                            <?php if (!empty($pathway['pathway_link'])) : ?>
                                <a href="<?php echo esc_url($pathway['pathway_link']); ?>"
                                   class="pathway-card__link"
                                   aria-label="<?php echo esc_attr(sprintf(__('Learn more about %s', 'responsible-ai'), $pathway['pathway_title'])); ?>">
                                    <?php esc_html_e('Learn More', 'responsible-ai'); ?>
                                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
/* ==========================================================================
   RAISE Certification Pathways Section
   ========================================================================== */

.pathways-section {
    padding: var(--space-20) 0;
    background: var(--color-background);
    position: relative;
    overflow: hidden;
}

/* Decorative top border */
.pathways-section::before {
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
.pathways-section .section-header {
    text-align: center;
    max-width: 900px;
    margin: 0 auto var(--space-12);
}

.pathways-section .section-title {
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: var(--font-weight-bold);
    line-height: 1.2;
    margin-bottom: var(--space-4);
    background: linear-gradient(135deg, var(--color-foreground) 0%, var(--color-primary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.pathways-section .section-subtitle {
    font-size: clamp(1rem, 2vw, 1.25rem);
    color: var(--color-foreground-secondary);
    line-height: 1.6;
}

/* Pathways Grid */
.pathways-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: var(--space-6);
    max-width: 1400px;
    margin: 0 auto;
}

@media (min-width: 768px) {
    .pathways-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: var(--space-8);
    }
}

@media (min-width: 1200px) {
    .pathways-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* Pathway Card */
.pathway-card {
    opacity: 0;
    transform: translateY(30px);
    height: 100%;
}

.pathway-card.is-visible {
    animation: fadeInUp 0.6s var(--ease-default) forwards;
}

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

.pathway-card__inner {
    display: flex;
    flex-direction: column;
    height: 100%;
    padding: var(--space-8);
    background: var(--color-background-elevated);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    transition: all var(--duration-default) var(--ease-default);
    position: relative;
    overflow: hidden;
}

/* Glow effect on hover */
.pathway-card__inner::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(
        135deg,
        var(--color-primary) 0%,
        var(--color-cta) 100%
    );
    border-radius: var(--radius-lg);
    opacity: 0;
    z-index: -1;
    transition: opacity var(--duration-default) var(--ease-default);
}

.pathway-card:hover .pathway-card__inner::before {
    opacity: 1;
}

.pathway-card:hover .pathway-card__inner {
    transform: translateY(-4px);
    border-color: transparent;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

/* Icon Wrapper */
.pathway-card__icon-wrapper {
    margin-bottom: var(--space-6);
    display: flex;
    justify-content: flex-start;
}

.pathway-card__icon {
    width: 72px;
    height: 72px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--color-primary);
    background: var(--color-primary-muted);
    border-radius: var(--radius-lg);
    transition: all var(--duration-default) var(--ease-default);
    position: relative;
}

.pathway-card:hover .pathway-card__icon {
    transform: scale(1.1) rotate(-5deg);
    background: var(--color-primary);
    color: var(--color-background);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

/* Content */
.pathway-card__content {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.pathway-card__title {
    font-size: 1.25rem;
    font-weight: var(--font-weight-bold);
    color: var(--color-foreground);
    line-height: 1.3;
    margin-bottom: var(--space-4);
}

.pathway-card__description {
    font-size: 0.9375rem;
    color: var(--color-foreground-secondary);
    line-height: 1.6;
    margin-bottom: var(--space-6);
    flex-grow: 1;
}

/* Call to Action Link */
.pathway-card__link {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    font-size: 0.9375rem;
    font-weight: var(--font-weight-semibold);
    color: var(--color-primary);
    text-decoration: none;
    transition: all var(--duration-fast) var(--ease-default);
    align-self: flex-start;
    margin-top: auto;
}

.pathway-card__link:hover {
    color: var(--color-cta);
    gap: var(--space-3);
}

.pathway-card__link i {
    font-size: 0.875rem;
    transition: transform var(--duration-fast) var(--ease-default);
}

.pathway-card:hover .pathway-card__link i {
    transform: translateX(4px);
}

/* Focus States for Accessibility */
.pathway-card__inner:focus-within {
    outline: 2px solid var(--color-primary);
    outline-offset: 4px;
}

.pathway-card__link:focus-visible {
    outline: 2px solid var(--color-cta);
    outline-offset: 2px;
    border-radius: var(--radius-sm);
}

/* Responsive Design */
@media (max-width: 1199px) {
    .pathways-section {
        padding: var(--space-16) 0;
    }
}

@media (max-width: 767px) {
    .pathways-section {
        padding: var(--space-12) 0;
    }

    .pathways-grid {
        gap: var(--space-6);
    }

    .pathway-card__inner {
        padding: var(--space-6);
    }

    .pathway-card__icon {
        width: 64px;
        height: 64px;
        font-size: 1.75rem;
    }

    .pathway-card__title {
        font-size: 1.125rem;
    }

    .pathway-card__description {
        font-size: 0.875rem;
    }
}

@media (max-width: 480px) {
    .pathway-card__icon {
        width: 56px;
        height: 56px;
        font-size: 1.5rem;
    }
}

/* Accessibility: Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    .pathway-card,
    .pathway-card__inner,
    .pathway-card__icon,
    .pathway-card__link,
    .pathway-card__link i {
        transition: none;
    }

    .pathway-card:hover .pathway-card__inner,
    .pathway-card:hover .pathway-card__icon {
        transform: none;
    }

    .pathway-card.is-visible {
        animation: none;
        opacity: 1;
        transform: none;
    }

    /* Only allow color changes for reduced motion */
    .pathway-card__icon,
    .pathway-card__link {
        transition: color var(--duration-fast) var(--ease-default),
                    background-color var(--duration-fast) var(--ease-default);
    }
}

/* Print Styles */
@media print {
    .pathways-section {
        padding: var(--space-8) 0;
    }

    .pathways-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: var(--space-4);
    }

    .pathway-card__inner::before {
        display: none;
    }

    .pathway-card__link i {
        display: none;
    }
}
</style>
