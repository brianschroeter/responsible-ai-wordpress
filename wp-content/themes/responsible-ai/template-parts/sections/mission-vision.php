<?php
/**
 * Mission-Vision Section
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get section visibility and content from ACF
$section_enabled = responsibleai_get_field('enable_mission_vision_section', false, true);

if (!$section_enabled) {
    return;
}

// Get mission content
$mission_title = responsibleai_get_field('mission_title', false, __('Our Mission', 'responsible-ai'));
$mission_text = responsibleai_get_field('mission_text', false, __('Building Trust in AI - We certify, standardize, and support responsible AI practices worldwide', 'responsible-ai'));

// Get vision content
$vision_title = responsibleai_get_field('vision_title', false, __('Our Vision', 'responsible-ai'));
$vision_text = responsibleai_get_field('vision_text', false, __('A world where AI systems are trustworthy, transparent, and beneficial for all', 'responsible-ai'));
?>

<section class="mission-vision-section" id="mission-vision">
    <div class="container">
        <div class="mission-vision-grid">
            <!-- Mission Card -->
            <div class="mission-vision-card mission-card" data-animate>
                <div class="mission-vision-card__inner">
                    <div class="mission-vision-card__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="M9 12l2 2 4-4"/>
                        </svg>
                    </div>

                    <?php if (!empty($mission_title)) : ?>
                        <h2 class="mission-vision-card__title"><?php echo esc_html($mission_title); ?></h2>
                    <?php endif; ?>

                    <?php if (!empty($mission_text)) : ?>
                        <p class="mission-vision-card__text"><?php echo esc_html($mission_text); ?></p>
                    <?php endif; ?>

                    <!-- Decorative gradient background -->
                    <div class="mission-vision-card__gradient mission-gradient"></div>
                </div>
            </div>

            <!-- Vision Card -->
            <div class="mission-vision-card vision-card" data-animate>
                <div class="mission-vision-card__inner">
                    <div class="mission-vision-card__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 2a7 7 0 1 0 10 10"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>

                    <?php if (!empty($vision_title)) : ?>
                        <h2 class="mission-vision-card__title"><?php echo esc_html($vision_title); ?></h2>
                    <?php endif; ?>

                    <?php if (!empty($vision_text)) : ?>
                        <p class="mission-vision-card__text"><?php echo esc_html($vision_text); ?></p>
                    <?php endif; ?>

                    <!-- Decorative gradient background -->
                    <div class="mission-vision-card__gradient vision-gradient"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* ==========================================================================
   Mission-Vision Section Styles
   ========================================================================== */

.mission-vision-section {
    position: relative;
    padding: var(--space-24) 0;
    background: var(--color-background);
    overflow: hidden;
}

/* Decorative background pattern */
.mission-vision-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        radial-gradient(circle at 20% 30%, var(--color-primary-muted) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, var(--color-cta-muted) 0%, transparent 50%);
    opacity: 0.03;
    pointer-events: none;
}

/* Grid Layout */
.mission-vision-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: var(--space-8);
    position: relative;
    max-width: 1400px;
    margin: 0 auto;
}

@media (min-width: 768px) {
    .mission-vision-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: var(--space-12);
    }
}

/* Mission-Vision Card */
.mission-vision-card {
    position: relative;
    opacity: 0;
    transform: translateY(30px);
}

.mission-vision-card.is-visible {
    animation: fadeUp var(--duration-slow) var(--ease-out) forwards;
}

/* Stagger animation delay */
.mission-card.is-visible {
    animation-delay: 0ms;
}

.vision-card.is-visible {
    animation-delay: 150ms;
}

@keyframes fadeUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.mission-vision-card__inner {
    position: relative;
    padding: var(--space-12);
    background: var(--color-background-elevated);
    border: 1px solid var(--color-border);
    border-radius: var(--border-radius-xl);
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    transition: all var(--duration-normal) var(--ease-default);
    overflow: hidden;
}

.mission-vision-card:hover .mission-vision-card__inner {
    transform: translateY(-4px);
    border-color: var(--color-primary);
    box-shadow: var(--shadow-glow);
}

/* Icon */
.mission-vision-card__icon {
    width: 72px;
    height: 72px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: var(--space-6);
    color: var(--color-primary-light);
    position: relative;
    z-index: 2;
}

.mission-card .mission-vision-card__icon {
    color: var(--color-primary-light);
}

.vision-card .mission-vision-card__icon {
    color: var(--color-cta);
}

.mission-vision-card__icon svg {
    filter: drop-shadow(0 4px 12px rgba(59, 130, 246, 0.3));
    transition: transform var(--duration-normal) var(--ease-default);
}

.mission-vision-card:hover .mission-vision-card__icon svg {
    transform: scale(1.1) rotate(-5deg);
}

/* Title */
.mission-vision-card__title {
    font-size: clamp(1.75rem, 3vw, 2.25rem);
    font-weight: var(--font-weight-bold);
    line-height: var(--line-height-tight);
    margin-bottom: var(--space-4);
    position: relative;
    z-index: 2;
}

.mission-card .mission-vision-card__title {
    background: linear-gradient(135deg, var(--color-foreground), var(--color-primary-light));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.vision-card .mission-vision-card__title {
    background: linear-gradient(135deg, var(--color-foreground), var(--color-cta));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Text */
.mission-vision-card__text {
    font-size: clamp(1.125rem, 2vw, 1.375rem);
    color: var(--color-foreground-secondary);
    line-height: var(--line-height-relaxed);
    margin: 0;
    position: relative;
    z-index: 2;
}

/* Decorative Gradient Background */
.mission-vision-card__gradient {
    position: absolute;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0;
    transition: opacity var(--duration-slow) var(--ease-default);
    pointer-events: none;
    z-index: 1;
}

.mission-gradient {
    background: radial-gradient(
        circle,
        var(--color-primary) 0%,
        transparent 70%
    );
    top: -100px;
    right: -100px;
}

.vision-gradient {
    background: radial-gradient(
        circle,
        var(--color-cta) 0%,
        transparent 70%
    );
    bottom: -100px;
    left: -100px;
}

.mission-vision-card:hover .mission-vision-card__gradient {
    opacity: 0.15;
}

/* Mobile Adjustments */
@media (max-width: 767px) {
    .mission-vision-section {
        padding: var(--space-16) 0;
    }

    .mission-vision-grid {
        gap: var(--space-6);
    }

    .mission-vision-card__inner {
        padding: var(--space-8);
    }

    .mission-vision-card__icon {
        width: 56px;
        height: 56px;
        margin-bottom: var(--space-4);
    }

    .mission-vision-card__icon svg {
        width: 40px;
        height: 40px;
    }

    .mission-vision-card__gradient {
        width: 200px;
        height: 200px;
        filter: blur(60px);
    }
}

/* Tablet Adjustments */
@media (min-width: 768px) and (max-width: 1023px) {
    .mission-vision-card__inner {
        padding: var(--space-10);
    }
}

/* Accessibility - Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    .mission-vision-card {
        animation: none !important;
        opacity: 1 !important;
        transform: none !important;
    }

    .mission-vision-card:hover .mission-vision-card__inner {
        transform: none;
    }

    .mission-vision-card:hover .mission-vision-card__icon svg {
        transform: none;
    }

    .mission-vision-card__gradient {
        transition: none;
    }
}

/* Print Styles */
@media print {
    .mission-vision-section {
        padding: 2rem 0;
    }

    .mission-vision-card__inner {
        border: 1px solid #333;
        box-shadow: none;
    }

    .mission-vision-card__gradient {
        display: none;
    }
}
</style>
