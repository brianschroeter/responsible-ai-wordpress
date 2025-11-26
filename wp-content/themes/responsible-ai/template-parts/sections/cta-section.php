<?php
/**
 * CTA Section
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get section visibility from ACF
$section_enabled = responsibleai_get_field('enable_cta_section', false, true);

if (!$section_enabled) {
    return;
}

// Get CTA content from ACF with defaults
$cta_title = responsibleai_get_field('cta_title', false, __('Ready to Build Trustworthy AI?', 'responsible-ai'));
$cta_text = responsibleai_get_field('cta_text', false, __('Join leading organizations worldwide in establishing ethical AI practices. Get certified, access standards, and become part of the responsible AI movement.', 'responsible-ai'));

// Primary CTA button
$primary_button_text = responsibleai_get_field('cta_primary_button_text', false, __('Get Certified', 'responsible-ai'));
$primary_button_link = responsibleai_get_field('cta_primary_button_link', false, home_url('/join/'));

// Secondary CTA button
$secondary_button_text = responsibleai_get_field('cta_secondary_button_text', false, __('Contact Us', 'responsible-ai'));
$secondary_button_link = responsibleai_get_field('cta_secondary_button_link', false, home_url('/contact/'));
?>

<section class="cta-section" id="cta">
    <div class="cta-section__gradient"></div>

    <div class="container">
        <div class="cta-section__content" data-animate>
            <?php if (!empty($cta_title)) : ?>
                <h2 class="cta-section__title"><?php echo esc_html($cta_title); ?></h2>
            <?php endif; ?>

            <?php if (!empty($cta_text)) : ?>
                <p class="cta-section__text"><?php echo esc_html($cta_text); ?></p>
            <?php endif; ?>

            <div class="cta-section__actions">
                <?php if (!empty($primary_button_text) && !empty($primary_button_link)) : ?>
                    <a href="<?php echo esc_url($primary_button_link); ?>" class="btn btn-cta btn-lg">
                        <?php echo esc_html($primary_button_text); ?>
                    </a>
                <?php endif; ?>

                <?php if (!empty($secondary_button_text) && !empty($secondary_button_link)) : ?>
                    <a href="<?php echo esc_url($secondary_button_link); ?>" class="btn btn-outline btn-lg">
                        <?php echo esc_html($secondary_button_text); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
/* ==========================================================================
   CTA Section Styles
   ========================================================================== */

.cta-section {
    position: relative;
    padding: var(--space-24) 0;
    background: var(--color-background-secondary);
    overflow: hidden;
}

/* Gradient Background */
.cta-section__gradient {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        135deg,
        var(--color-primary) 0%,
        var(--color-primary-light) 50%,
        var(--color-cta) 100%
    );
    opacity: 0.1;
    pointer-events: none;
}

/* Add animated gradient movement */
.cta-section__gradient::before {
    content: '';
    position: absolute;
    inset: -50%;
    background: radial-gradient(
        circle at center,
        var(--color-cta) 0%,
        transparent 70%
    );
    opacity: 0.3;
    animation: gradientPulse 8s ease-in-out infinite;
}

@keyframes gradientPulse {
    0%, 100% {
        transform: translate(0, 0) scale(1);
        opacity: 0.3;
    }
    50% {
        transform: translate(10%, -10%) scale(1.2);
        opacity: 0.5;
    }
}

/* CTA Content Container */
.cta-section__content {
    position: relative;
    z-index: 1;
    text-align: center;
    max-width: 900px;
    margin: 0 auto;
    padding: var(--space-16) var(--space-8);
    opacity: 0;
    transform: translateY(30px);
}

.cta-section__content.is-visible {
    animation: fadeUp var(--duration-slow) var(--ease-out) forwards;
}

@keyframes fadeUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* CTA Title with Gradient */
.cta-section__title {
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: var(--font-weight-bold);
    line-height: var(--line-height-tight);
    margin-bottom: var(--space-6);
    background: linear-gradient(
        135deg,
        var(--color-foreground) 0%,
        var(--color-primary-light) 50%,
        var(--color-cta) 100%
    );
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    background-size: 200% 200%;
    animation: gradientShift 6s ease-in-out infinite;
}

@keyframes gradientShift {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

/* CTA Text */
.cta-section__text {
    font-size: clamp(1.125rem, 2vw, 1.375rem);
    color: var(--color-foreground-secondary);
    line-height: var(--line-height-relaxed);
    margin-bottom: var(--space-8);
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}

/* CTA Actions */
.cta-section__actions {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-4);
    justify-content: center;
    align-items: center;
}

/* Button Enhancements for CTA Section */
.cta-section .btn {
    min-width: 200px;
    position: relative;
    overflow: hidden;
}

/* Primary CTA Button - Amber with glow effect */
.cta-section .btn-cta {
    background: linear-gradient(135deg, var(--color-cta) 0%, var(--color-cta-hover) 100%);
    color: var(--color-background);
    border: none;
    box-shadow: 0 4px 20px rgba(245, 158, 11, 0.3);
    transition: all var(--duration-normal) var(--ease-default);
}

.cta-section .btn-cta::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, var(--color-cta-hover) 0%, var(--color-cta) 100%);
    opacity: 0;
    transition: opacity var(--duration-normal) var(--ease-default);
}

.cta-section .btn-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(245, 158, 11, 0.5);
}

.cta-section .btn-cta:hover::before {
    opacity: 1;
}

.cta-section .btn-cta > * {
    position: relative;
    z-index: 1;
}

/* Secondary CTA Button - Outline style */
.cta-section .btn-outline {
    background: transparent;
    color: var(--color-foreground);
    border: 2px solid var(--color-border-strong);
    transition: all var(--duration-normal) var(--ease-default);
}

.cta-section .btn-outline:hover {
    background: var(--color-background-elevated);
    border-color: var(--color-primary);
    color: var(--color-primary-light);
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(59, 130, 246, 0.2);
}

/* Responsive Adjustments */
@media (max-width: 767px) {
    .cta-section {
        padding: var(--space-16) 0;
    }

    .cta-section__content {
        padding: var(--space-12) var(--space-4);
    }

    .cta-section__title {
        font-size: clamp(2rem, 8vw, 2.5rem);
        margin-bottom: var(--space-4);
    }

    .cta-section__text {
        font-size: var(--font-size-base);
        margin-bottom: var(--space-6);
    }

    .cta-section__actions {
        flex-direction: column;
        width: 100%;
    }

    .cta-section .btn {
        width: 100%;
        min-width: 0;
        justify-content: center;
    }
}

@media (min-width: 768px) and (max-width: 1023px) {
    .cta-section__title {
        font-size: clamp(2.5rem, 4vw, 3.5rem);
    }
}

/* Dark Theme Optimizations */
@media (prefers-color-scheme: dark) {
    .cta-section__gradient {
        opacity: 0.15;
    }

    .cta-section .btn-cta {
        box-shadow: 0 4px 20px rgba(245, 158, 11, 0.4);
    }

    .cta-section .btn-cta:hover {
        box-shadow: 0 8px 30px rgba(245, 158, 11, 0.6);
    }
}

/* Accessibility - Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    .cta-section__gradient::before,
    .cta-section__title {
        animation: none !important;
    }

    .cta-section__content {
        animation: none !important;
        opacity: 1 !important;
        transform: none !important;
    }

    .cta-section .btn:hover {
        transform: none;
    }
}

/* Print Styles */
@media print {
    .cta-section__gradient,
    .cta-section__gradient::before {
        display: none;
    }

    .cta-section {
        background: white;
        color: black;
    }

    .cta-section__title {
        -webkit-text-fill-color: initial;
        color: black;
    }
}
</style>
