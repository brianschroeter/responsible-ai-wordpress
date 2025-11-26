<?php
/**
 * Partner Logos Section
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get partner logos from ACF
$logos = responsibleai_get_field('partner_logos', false, array());

// If no logos, show default placeholders
if (empty($logos)) {
    $logos = array(
        array(
            'logo_image' => null,
            'logo_url' => '#',
            'logo_alt' => __('Partner 1', 'responsible-ai'),
        ),
        array(
            'logo_image' => null,
            'logo_url' => '#',
            'logo_alt' => __('Partner 2', 'responsible-ai'),
        ),
        array(
            'logo_image' => null,
            'logo_url' => '#',
            'logo_alt' => __('Partner 3', 'responsible-ai'),
        ),
        array(
            'logo_image' => null,
            'logo_url' => '#',
            'logo_alt' => __('Partner 4', 'responsible-ai'),
        ),
        array(
            'logo_image' => null,
            'logo_url' => '#',
            'logo_alt' => __('Partner 5', 'responsible-ai'),
        ),
        array(
            'logo_image' => null,
            'logo_url' => '#',
            'logo_alt' => __('Partner 6', 'responsible-ai'),
        ),
    );
}
?>

<section class="partner-logos-section">
    <div class="container">
        <div class="partner-logos__header" data-animate>
            <h2 class="partner-logos__title"><?php echo esc_html__('Trusted By Leading Organizations', 'responsible-ai'); ?></h2>
        </div>

        <div class="logo-wall swiper">
            <div class="swiper-wrapper">
                <?php foreach ($logos as $logo) : ?>
                    <div class="swiper-slide partner-logo">
                        <?php if (!empty($logo['logo_url']) && $logo['logo_url'] !== '#') : ?>
                            <a href="<?php echo esc_url($logo['logo_url']); ?>"
                               class="partner-logo__link"
                               target="_blank"
                               rel="noopener noreferrer"
                               aria-label="<?php echo esc_attr($logo['logo_alt'] ?? __('Partner website', 'responsible-ai')); ?>">
                        <?php endif; ?>

                        <?php if (!empty($logo['logo_image'])) : ?>
                            <img src="<?php echo esc_url(responsibleai_get_image_url($logo['logo_image'], 'medium')); ?>"
                                 alt="<?php echo esc_attr($logo['logo_alt'] ?? ''); ?>"
                                 class="partner-logo__image">
                        <?php else : ?>
                            <div class="partner-logo__placeholder">
                                <svg viewBox="0 0 200 80" class="partner-logo__svg">
                                    <rect x="10" y="20" width="180" height="40" rx="4" fill="currentColor" opacity="0.1"/>
                                    <text x="100" y="50"
                                          text-anchor="middle"
                                          font-size="14"
                                          fill="currentColor"
                                          opacity="0.3">
                                        <?php echo esc_html($logo['logo_alt'] ?? __('Partner Logo', 'responsible-ai')); ?>
                                    </text>
                                </svg>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($logo['logo_url']) && $logo['logo_url'] !== '#') : ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<style>
.partner-logos-section {
    padding: var(--space-20) 0;
    background: var(--color-background);
    position: relative;
}

.partner-logos__header {
    text-align: center;
    margin-bottom: var(--space-12);
}

.partner-logos__title {
    font-size: clamp(1.75rem, 3vw, 2.5rem);
    font-weight: var(--font-weight-bold);
    color: var(--color-foreground);
    line-height: 1.2;
    margin: 0;
}

.logo-wall {
    overflow: hidden;
}

.partner-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--space-6);
    min-height: 120px;
}

.partner-logo__link {
    display: block;
    width: 100%;
    height: 100%;
    transition: transform var(--duration-fast) var(--ease-default),
                opacity var(--duration-fast) var(--ease-default);
}

.partner-logo__link:hover {
    transform: scale(1.05);
    opacity: 0.8;
}

.partner-logo__link:focus {
    outline: 2px solid var(--color-cta);
    outline-offset: 4px;
    border-radius: var(--radius-md);
}

.partner-logo__image {
    width: 100%;
    height: auto;
    max-width: 180px;
    max-height: 80px;
    object-fit: contain;
    filter: grayscale(100%) brightness(0) invert(0.7);
    transition: filter var(--duration-default) var(--ease-default);
}

.partner-logo__link:hover .partner-logo__image {
    filter: grayscale(0%) brightness(1) invert(0);
}

.partner-logo__placeholder {
    width: 100%;
    max-width: 200px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.partner-logo__svg {
    width: 100%;
    height: 100%;
    color: var(--color-foreground-secondary);
}

/* Swiper customization for logo wall */
.logo-wall .swiper-wrapper {
    align-items: center;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .partner-logos-section {
        padding: var(--space-16) 0;
    }

    .partner-logos__header {
        margin-bottom: var(--space-8);
    }

    .partner-logo {
        padding: var(--space-4);
        min-height: 100px;
    }

    .partner-logo__image {
        max-width: 140px;
        max-height: 60px;
    }
}

@media (prefers-color-scheme: light) {
    .partner-logo__image {
        filter: grayscale(100%) brightness(0) invert(0.3);
    }

    .partner-logo__link:hover .partner-logo__image {
        filter: grayscale(0%) brightness(1) invert(0);
    }
}

/* Animation support */
[data-animate].is-visible .partner-logos__title {
    animation: fadeInUp 0.6s var(--ease-out) forwards;
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
</style>
