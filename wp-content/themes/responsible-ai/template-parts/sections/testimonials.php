<?php
/**
 * Testimonials Carousel Section
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get testimonials from ACF repeater
$testimonials = responsibleai_get_repeater('testimonials');

// If no testimonials, show default testimonials from AI/tech executives
if (empty($testimonials)) {
    $testimonials = array(
        array(
            'quote' => __('Responsible AI certification has transformed how we approach AI development. The standards provide clear guidance while allowing for innovation. Our stakeholders have increased confidence in our AI systems.', 'responsible-ai'),
            'author_name' => __('Sarah Chen', 'responsible-ai'),
            'author_title' => __('Chief Technology Officer', 'responsible-ai'),
            'author_company' => __('TechVision AI', 'responsible-ai'),
            'author_image' => null,
        ),
        array(
            'quote' => __('The certification process helped us identify and address ethical considerations we hadn\'t fully considered. It\'s become an essential part of our AI governance framework and competitive advantage.', 'responsible-ai'),
            'author_name' => __('Michael Rodriguez', 'responsible-ai'),
            'author_title' => __('VP of AI Ethics & Compliance', 'responsible-ai'),
            'author_company' => __('DataSphere Corporation', 'responsible-ai'),
            'author_image' => null,
        ),
        array(
            'quote' => __('Achieving RAI certification demonstrated our commitment to responsible innovation. The framework is comprehensive yet practical, helping us build AI systems that are both powerful and trustworthy.', 'responsible-ai'),
            'author_name' => __('Dr. Emily Watkins', 'responsible-ai'),
            'author_title' => __('Director of AI Research', 'responsible-ai'),
            'author_company' => __('Quantum Innovations Lab', 'responsible-ai'),
            'author_image' => null,
        ),
    );
}
?>

<section class="testimonials-section">
    <div class="container">
        <div class="testimonials__header" data-animate>
            <h2 class="testimonials__title"><?php echo esc_html__('What Leaders Are Saying', 'responsible-ai'); ?></h2>
            <p class="testimonials__subtitle"><?php echo esc_html__('Trusted by organizations committed to responsible AI', 'responsible-ai'); ?></p>
        </div>

        <div class="testimonials-carousel swiper" data-animate>
            <div class="swiper-wrapper">
                <?php foreach ($testimonials as $testimonial) : ?>
                    <div class="swiper-slide testimonial-card">
                        <div class="testimonial-card__inner">
                            <!-- Quote Icon -->
                            <div class="testimonial-card__quote-icon" aria-hidden="true">
                                <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                                    <path d="M14.5 21.5V33.5H21.5V26.5H18.5C18.5 22.634 20.634 20.5 24.5 20.5V14.5C17.3203 14.5 14.5 17.3203 14.5 21.5ZM26.5 21.5V33.5H33.5V26.5H30.5C30.5 22.634 32.634 20.5 36.5 20.5V14.5C29.3203 14.5 26.5 17.3203 26.5 21.5Z" fill="currentColor" opacity="0.2"/>
                                </svg>
                            </div>

                            <!-- Quote Text -->
                            <?php if (!empty($testimonial['quote'])) : ?>
                                <blockquote class="testimonial-card__quote">
                                    <?php echo esc_html($testimonial['quote']); ?>
                                </blockquote>
                            <?php endif; ?>

                            <!-- Author Info -->
                            <div class="testimonial-card__author">
                                <?php if (!empty($testimonial['author_image'])) : ?>
                                    <div class="testimonial-card__avatar">
                                        <img src="<?php echo esc_url(responsibleai_get_image_url($testimonial['author_image'], 'thumbnail')); ?>"
                                             alt="<?php echo esc_attr($testimonial['author_name'] ?? ''); ?>"
                                             class="testimonial-card__avatar-img">
                                    </div>
                                <?php else : ?>
                                    <div class="testimonial-card__avatar testimonial-card__avatar--placeholder">
                                        <svg viewBox="0 0 64 64" fill="none">
                                            <circle cx="32" cy="32" r="32" fill="currentColor" opacity="0.1"/>
                                            <path d="M32 28C35.3137 28 38 25.3137 38 22C38 18.6863 35.3137 16 32 16C28.6863 16 26 18.6863 26 22C26 25.3137 28.6863 28 32 28ZM32 32C26.6667 32 16 34.6667 16 40V44C16 45.1046 16.8954 46 18 46H46C47.1046 46 48 45.1046 48 44V40C48 34.6667 37.3333 32 32 32Z" fill="currentColor" opacity="0.3"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>

                                <div class="testimonial-card__author-info">
                                    <?php if (!empty($testimonial['author_name'])) : ?>
                                        <div class="testimonial-card__author-name">
                                            <?php echo esc_html($testimonial['author_name']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($testimonial['author_title']) || !empty($testimonial['author_company'])) : ?>
                                        <div class="testimonial-card__author-meta">
                                            <?php if (!empty($testimonial['author_title'])) : ?>
                                                <span class="testimonial-card__author-title">
                                                    <?php echo esc_html($testimonial['author_title']); ?>
                                                </span>
                                            <?php endif; ?>

                                            <?php if (!empty($testimonial['author_title']) && !empty($testimonial['author_company'])) : ?>
                                                <span class="testimonial-card__separator" aria-hidden="true"> • </span>
                                            <?php endif; ?>

                                            <?php if (!empty($testimonial['author_company'])) : ?>
                                                <span class="testimonial-card__author-company">
                                                    <?php echo esc_html($testimonial['author_company']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if (count($testimonials) > 1) : ?>
                <div class="swiper-pagination"></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
.testimonials-section {
    padding: var(--space-20) 0;
    background: var(--color-background-elevated);
    position: relative;
    overflow: hidden;
}

.testimonials-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 200%;
    height: 100%;
    background: radial-gradient(
        ellipse at center,
        hsla(217, 91%, 60%, 0.05) 0%,
        transparent 50%
    );
    pointer-events: none;
}

.testimonials__header {
    text-align: center;
    margin-bottom: var(--space-12);
    position: relative;
    z-index: 1;
}

.testimonials__title {
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: var(--font-weight-bold);
    color: var(--color-foreground);
    line-height: 1.2;
    margin: 0 0 var(--space-4) 0;
}

.testimonials__subtitle {
    font-size: clamp(1rem, 2vw, 1.25rem);
    color: var(--color-foreground-secondary);
    line-height: 1.5;
    margin: 0;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.testimonials-carousel {
    position: relative;
    z-index: 1;
    padding-bottom: var(--space-12);
}

.testimonial-card {
    height: auto;
    display: flex;
}

.testimonial-card__inner {
    background: var(--color-background);
    border-radius: var(--radius-lg);
    padding: var(--space-8);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
                0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: transform var(--duration-default) var(--ease-default),
                box-shadow var(--duration-default) var(--ease-default);
    height: 100%;
    display: flex;
    flex-direction: column;
    border: 1px solid hsla(217, 91%, 60%, 0.1);
}

.testimonial-card__inner:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2),
                0 4px 6px -2px rgba(0, 0, 0, 0.1),
                0 0 0 1px hsla(217, 91%, 60%, 0.2);
}

.testimonial-card__quote-icon {
    color: var(--color-cta);
    margin-bottom: var(--space-4);
    width: 48px;
    height: 48px;
}

.testimonial-card__quote {
    font-size: clamp(1rem, 1.5vw, 1.125rem);
    line-height: 1.7;
    color: var(--color-foreground);
    margin: 0 0 var(--space-6) 0;
    flex-grow: 1;
    font-style: italic;
    position: relative;
}

.testimonial-card__author {
    display: flex;
    align-items: center;
    gap: var(--space-4);
    padding-top: var(--space-6);
    border-top: 1px solid hsla(217, 91%, 60%, 0.1);
}

.testimonial-card__avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
    background: var(--color-background-elevated);
    display: flex;
    align-items: center;
    justify-content: center;
}

.testimonial-card__avatar--placeholder {
    color: var(--color-foreground-secondary);
}

.testimonial-card__avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.testimonial-card__author-info {
    flex-grow: 1;
    min-width: 0;
}

.testimonial-card__author-name {
    font-size: 1.0625rem;
    font-weight: var(--font-weight-semibold);
    color: var(--color-foreground);
    line-height: 1.4;
    margin-bottom: var(--space-1);
}

.testimonial-card__author-meta {
    font-size: 0.875rem;
    color: var(--color-foreground-secondary);
    line-height: 1.5;
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-1);
    align-items: center;
}

.testimonial-card__author-title {
    font-weight: var(--font-weight-medium);
}

.testimonial-card__separator {
    color: var(--color-foreground-tertiary);
}

/* Swiper customization */
.testimonials-carousel .swiper-pagination {
    bottom: 0;
}

.testimonials-carousel .swiper-pagination-bullet {
    width: 10px;
    height: 10px;
    background: var(--color-foreground-secondary);
    opacity: 0.3;
    transition: opacity var(--duration-fast) var(--ease-default),
                transform var(--duration-fast) var(--ease-default);
}

.testimonials-carousel .swiper-pagination-bullet-active {
    background: var(--color-cta);
    opacity: 1;
    transform: scale(1.2);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .testimonials-section {
        padding: var(--space-16) 0;
    }

    .testimonials__header {
        margin-bottom: var(--space-8);
    }

    .testimonial-card__inner {
        padding: var(--space-6);
    }

    .testimonial-card__quote {
        font-size: 1rem;
    }

    .testimonial-card__avatar {
        width: 48px;
        height: 48px;
    }

    .testimonial-card__author-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 0;
    }

    .testimonial-card__separator {
        display: none;
    }
}

@media (max-width: 640px) {
    .testimonial-card__inner {
        padding: var(--space-5);
    }

    .testimonial-card__quote-icon {
        width: 40px;
        height: 40px;
    }
}

/* Animation support */
[data-animate].is-visible .testimonials__title {
    animation: fadeInUp 0.6s var(--ease-out) forwards;
}

[data-animate].is-visible .testimonials__subtitle {
    animation: fadeInUp 0.6s var(--ease-out) 0.1s forwards;
    opacity: 0;
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

/* Light mode adjustments */
@media (prefers-color-scheme: light) {
    .testimonials-section {
        background: var(--color-background);
    }

    .testimonial-card__inner {
        background: var(--color-background-elevated);
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1),
                    0 1px 2px 0 rgba(0, 0, 0, 0.06);
        border: 1px solid hsla(217, 91%, 60%, 0.15);
    }

    .testimonial-card__inner:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
                    0 2px 4px -1px rgba(0, 0, 0, 0.06),
                    0 0 0 1px hsla(217, 91%, 60%, 0.25);
    }
}

/* Print styles */
@media print {
    .testimonials-section {
        padding: var(--space-8) 0;
    }

    .swiper-pagination {
        display: none;
    }

    .testimonial-card__inner {
        break-inside: avoid;
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
</style>
