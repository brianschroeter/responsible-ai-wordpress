<?php
/**
 * Stats Counter Section
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get section visibility and content from ACF
$section_enabled = responsibleai_get_field('enable_stats_section', false, true);

if (!$section_enabled) {
    return;
}

// Get section settings
$section_title = responsibleai_get_field('stats_section_title', false, __('Our Impact in Numbers', 'responsible-ai'));
$section_subtitle = responsibleai_get_field('stats_section_subtitle', false, __('Driving responsible AI practices globally through certification, education, and community engagement.', 'responsible-ai'));

// Get stats items from ACF (repeater field with fallback to defaults)
$stats_items = responsibleai_get_field('stats_items', false, array());

// If no stats defined, use defaults
if (empty($stats_items)) {
    $stats_items = array(
        array(
            'number' => '150',
            'suffix' => '+',
            'label'  => __('Companies', 'responsible-ai'),
            'description' => __('Organizations certified worldwide', 'responsible-ai'),
        ),
        array(
            'number' => '50',
            'suffix' => '+',
            'label'  => __('Countries', 'responsible-ai'),
            'description' => __('Global reach and impact', 'responsible-ai'),
        ),
        array(
            'number' => '25',
            'suffix' => '+',
            'label'  => __('Standards', 'responsible-ai'),
            'description' => __('Frameworks and certifications', 'responsible-ai'),
        ),
        array(
            'number' => '5000',
            'suffix' => '+',
            'label'  => __('Practitioners', 'responsible-ai'),
            'description' => __('Certified AI professionals', 'responsible-ai'),
        ),
    );
}

// Ensure we have at least one stat
if (empty($stats_items)) {
    return;
}
?>

<section class="stats-section" id="stats">
    <div class="container">
        <!-- Section Header -->
        <div class="stats-section__header" data-animate>
            <?php if (!empty($section_title)) : ?>
                <h2 class="stats-section__title"><?php echo esc_html($section_title); ?></h2>
            <?php endif; ?>

            <?php if (!empty($section_subtitle)) : ?>
                <p class="stats-section__subtitle"><?php echo esc_html($section_subtitle); ?></p>
            <?php endif; ?>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <?php foreach ($stats_items as $index => $stat) :
                $number = isset($stat['number']) ? $stat['number'] : '0';
                $suffix = isset($stat['suffix']) ? $stat['suffix'] : '';
                $prefix = isset($stat['prefix']) ? $stat['prefix'] : '';
                $label = isset($stat['label']) ? $stat['label'] : '';
                $description = isset($stat['description']) ? $stat['description'] : '';
                $animation_delay = $index * 100; // Stagger animation
            ?>
                <div class="stat-card" data-animate style="animation-delay: <?php echo esc_attr($animation_delay); ?>ms;">
                    <div class="stat-card__inner">
                        <div class="stat-card__number"
                             data-counter
                             data-target="<?php echo esc_attr($number); ?>"
                             data-prefix="<?php echo esc_attr($prefix); ?>"
                             data-suffix="<?php echo esc_attr($suffix); ?>">
                            <span class="stat-card__prefix"><?php echo esc_html($prefix); ?></span>
                            <span class="stat-card__value">0</span>
                            <span class="stat-card__suffix"><?php echo esc_html($suffix); ?></span>
                        </div>

                        <?php if (!empty($label)) : ?>
                            <h3 class="stat-card__label"><?php echo esc_html($label); ?></h3>
                        <?php endif; ?>

                        <?php if (!empty($description)) : ?>
                            <p class="stat-card__description"><?php echo esc_html($description); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
/* ==========================================================================
   Stats Section Styles
   ========================================================================== */

.stats-section {
    position: relative;
    padding: var(--space-24) 0;
    background: var(--color-background-secondary);
    overflow: hidden;
}

/* Decorative background gradient */
.stats-section::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 50%;
    height: 100%;
    background: linear-gradient(
        135deg,
        transparent 0%,
        var(--color-primary-muted) 100%
    );
    opacity: 0.05;
    pointer-events: none;
}

/* Section Header */
.stats-section__header {
    text-align: center;
    max-width: 800px;
    margin: 0 auto var(--space-16);
    opacity: 0;
    transform: translateY(30px);
}

.stats-section__header.is-visible {
    animation: fadeUp var(--duration-slow) var(--ease-out) forwards;
}

.stats-section__title {
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: var(--font-weight-bold);
    line-height: var(--line-height-tight);
    margin-bottom: var(--space-4);
    background: linear-gradient(135deg, var(--color-foreground), var(--color-primary-light));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stats-section__subtitle {
    font-size: clamp(1rem, 2vw, 1.25rem);
    color: var(--color-foreground-secondary);
    line-height: var(--line-height-relaxed);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--space-8);
    position: relative;
}

@media (min-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .stats-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: var(--space-6);
    }
}

/* Stat Card */
.stat-card {
    position: relative;
    opacity: 0;
    transform: translateY(30px) scale(0.95);
}

.stat-card.is-visible {
    animation: statCardEnter var(--duration-slow) var(--ease-out) forwards;
}

@keyframes statCardEnter {
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.stat-card__inner {
    position: relative;
    padding: var(--space-8);
    background: var(--color-background-elevated);
    border: 1px solid var(--color-border);
    border-radius: var(--border-radius-lg);
    text-align: center;
    transition: all var(--duration-normal) var(--ease-default);
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.stat-card__inner::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(
        135deg,
        var(--color-primary) 0%,
        var(--color-cta) 100%
    );
    opacity: 0;
    border-radius: var(--border-radius-lg);
    transition: opacity var(--duration-normal) var(--ease-default);
    z-index: -1;
}

.stat-card:hover .stat-card__inner {
    transform: translateY(-4px);
    border-color: var(--color-primary);
    box-shadow: var(--shadow-glow);
}

.stat-card:hover .stat-card__inner::before {
    opacity: 0.1;
}

/* Stat Number */
.stat-card__number {
    font-size: clamp(2.5rem, 5vw, 4rem);
    font-weight: var(--font-weight-bold);
    line-height: 1;
    margin-bottom: var(--space-4);
    color: var(--color-primary-light);
    display: flex;
    align-items: baseline;
    justify-content: center;
    gap: 0.25rem;
}

.stat-card__prefix,
.stat-card__suffix {
    font-size: 0.6em;
    font-weight: var(--font-weight-semibold);
    color: var(--color-cta);
}

.stat-card__value {
    font-family: var(--font-family-primary);
    letter-spacing: var(--letter-spacing-tight);
}

/* Counter animation class */
.stat-card__number.counting {
    color: var(--color-cta);
    text-shadow: 0 0 20px rgba(245, 158, 11, 0.4);
}

/* Stat Label */
.stat-card__label {
    font-size: var(--font-size-xl);
    font-weight: var(--font-weight-semibold);
    line-height: var(--line-height-tight);
    margin-bottom: var(--space-2);
    color: var(--color-foreground);
}

/* Stat Description */
.stat-card__description {
    font-size: var(--font-size-sm);
    color: var(--color-foreground-muted);
    line-height: var(--line-height-relaxed);
    margin: 0;
}

/* Responsive Adjustments */
@media (max-width: 767px) {
    .stats-section {
        padding: var(--space-16) 0;
    }

    .stats-grid {
        gap: var(--space-6);
    }

    .stat-card__inner {
        padding: var(--space-6);
    }

    .stat-card__number {
        font-size: clamp(2rem, 8vw, 3rem);
    }

    .stat-card__label {
        font-size: var(--font-size-lg);
    }
}

/* Accessibility - Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    .stat-card {
        animation: none !important;
        opacity: 1 !important;
        transform: none !important;
    }

    .stats-section__header {
        animation: none !important;
        opacity: 1 !important;
        transform: none !important;
    }

    .stat-card:hover .stat-card__inner {
        transform: none;
    }
}
</style>

<script>
/**
 * Stats Counter Animation
 * Animates numbers from 0 to target value when scrolled into view
 */
(function() {
    'use strict';

    const StatsCounter = {
        counters: [],
        hasAnimated: false,
        observer: null,

        init() {
            this.counters = document.querySelectorAll('[data-counter]');
            if (!this.counters.length) return;

            // Use Intersection Observer to trigger animation
            this.observer = new IntersectionObserver(
                (entries) => this.handleIntersection(entries),
                {
                    threshold: 0.5,
                    rootMargin: '0px 0px -100px 0px'
                }
            );

            // Observe the first counter (they should all be in same section)
            if (this.counters[0]) {
                this.observer.observe(this.counters[0].closest('.stats-section'));
            }
        },

        handleIntersection(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting && !this.hasAnimated) {
                    this.hasAnimated = true;
                    this.animateCounters();
                    this.observer.disconnect();
                }
            });
        },

        animateCounters() {
            this.counters.forEach(counter => {
                this.animateCounter(counter);
            });
        },

        animateCounter(counter) {
            const target = parseInt(counter.dataset.target) || 0;
            const prefix = counter.dataset.prefix || '';
            const suffix = counter.dataset.suffix || '';
            const duration = 2000; // 2 seconds
            const steps = 60;
            const increment = target / steps;
            const stepDuration = duration / steps;

            let current = 0;
            const valueElement = counter.querySelector('.stat-card__value');

            if (!valueElement) return;

            // Add counting class for visual effect
            counter.classList.add('counting');

            const timer = setInterval(() => {
                current += increment;

                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                    counter.classList.remove('counting');
                }

                // Format number with commas for thousands
                const formatted = Math.floor(current).toLocaleString('en-US');
                valueElement.textContent = formatted;
            }, stepDuration);
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => StatsCounter.init());
    } else {
        StatsCounter.init();
    }
})();
</script>
