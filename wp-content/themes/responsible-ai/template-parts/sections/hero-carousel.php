<?php
/**
 * Hero Carousel Section
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get hero slides from ACF
$slides = responsibleai_get_field('hero_slides', false, array());

// If no slides, show default hero
if (empty($slides)) {
    $slides = array(
        array(
            'title'    => __('Building Trust in AI', 'responsible-ai'),
            'subtitle' => __('Certifying, standardizing, and supporting responsible AI practices worldwide.', 'responsible-ai'),
            'image'    => null,
            'cta_primary_text' => __('Get Started', 'responsible-ai'),
            'cta_primary_link' => home_url('/join/'),
            'cta_secondary_text' => __('Learn More', 'responsible-ai'),
            'cta_secondary_link' => home_url('/who-we-are/'),
        ),
    );
}
?>

<section class="hero-section">
    <div class="hero-carousel swiper">
        <div class="swiper-wrapper">
            <?php foreach ($slides as $index => $slide) : ?>
                <div class="swiper-slide hero-slide">
                    <!-- Background Image -->
                    <?php if (!empty($slide['image'])) : ?>
                        <div class="hero-slide__bg" style="background-image: url('<?php echo esc_url(responsibleai_get_image_url($slide['image'], 'hero-slide')); ?>');"></div>
                    <?php else : ?>
                        <div class="hero-slide__bg hero-slide__bg--gradient"></div>
                    <?php endif; ?>

                    <!-- Overlay -->
                    <div class="hero-slide__overlay"></div>

                    <!-- Content -->
                    <div class="hero-slide__content container">
                        <div class="hero-slide__inner" data-animate>
                            <?php if (!empty($slide['title'])) : ?>
                                <h1 class="hero-slide__title"><?php echo esc_html($slide['title']); ?></h1>
                            <?php endif; ?>

                            <?php if (!empty($slide['subtitle'])) : ?>
                                <p class="hero-slide__subtitle"><?php echo esc_html($slide['subtitle']); ?></p>
                            <?php endif; ?>

                            <div class="hero-slide__actions">
                                <?php if (!empty($slide['cta_primary_text']) && !empty($slide['cta_primary_link'])) : ?>
                                    <a href="<?php echo esc_url($slide['cta_primary_link']); ?>" class="btn btn-cta btn-lg">
                                        <?php echo esc_html($slide['cta_primary_text']); ?>
                                    </a>
                                <?php endif; ?>

                                <?php if (!empty($slide['cta_secondary_text']) && !empty($slide['cta_secondary_link'])) : ?>
                                    <a href="<?php echo esc_url($slide['cta_secondary_link']); ?>" class="btn btn-outline btn-lg">
                                        <?php echo esc_html($slide['cta_secondary_text']); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (count($slides) > 1) : ?>
            <!-- Pagination -->
            <div class="swiper-pagination"></div>

            <!-- Navigation -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        <?php endif; ?>
    </div>
</section>

<style>
.hero-section {
    position: relative;
    margin-top: calc(-1 * var(--admin-bar-height, 0px));
}

.hero-carousel,
.hero-slide {
    height: 100vh;
    min-height: 600px;
    max-height: 900px;
}

.hero-slide {
    position: relative;
    display: flex;
    align-items: center;
}

.hero-slide__bg {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
}

.hero-slide__bg--gradient {
    background: linear-gradient(135deg, var(--color-background) 0%, var(--color-background-elevated) 50%, var(--color-primary-muted) 100%);
}

.hero-slide__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        135deg,
        hsla(220, 45%, 10%, 0.9) 0%,
        hsla(220, 45%, 10%, 0.7) 50%,
        hsla(217, 91%, 60%, 0.3) 100%
    );
}

.hero-slide__content {
    position: relative;
    z-index: 1;
    width: 100%;
}

.hero-slide__inner {
    max-width: 800px;
}

.hero-slide__title {
    font-size: clamp(2.5rem, 5vw, 4.5rem);
    font-weight: var(--font-weight-bold);
    line-height: 1.1;
    margin-bottom: var(--space-6);
}

.hero-slide__subtitle {
    font-size: clamp(1.125rem, 2vw, 1.5rem);
    color: var(--color-foreground-secondary);
    line-height: 1.5;
    margin-bottom: var(--space-8);
    max-width: 600px;
}

.hero-slide__actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-4);
}

/* Swiper customization */
.hero-carousel .swiper-pagination-bullet {
    width: 12px;
    height: 12px;
    background: var(--color-foreground);
    opacity: 0.5;
}

.hero-carousel .swiper-pagination-bullet-active {
    background: var(--color-cta);
    opacity: 1;
}

.hero-carousel .swiper-button-prev,
.hero-carousel .swiper-button-next {
    color: var(--color-foreground);
    opacity: 0.7;
    transition: opacity var(--duration-fast) var(--ease-default);
}

.hero-carousel .swiper-button-prev:hover,
.hero-carousel .swiper-button-next:hover {
    opacity: 1;
}

@media (max-width: 768px) {
    .hero-slide__actions {
        flex-direction: column;
    }

    .hero-slide__actions .btn {
        width: 100%;
        justify-content: center;
    }

    .hero-carousel .swiper-button-prev,
    .hero-carousel .swiper-button-next {
        display: none;
    }
}
</style>
