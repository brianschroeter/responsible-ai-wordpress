<?php
/**
 * AI Agents Flip Cards Section
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get AI agents from ACF
$agents = responsibleai_get_field('ai_agents', false, array());

// If no agents, show default data
if (empty($agents)) {
    $agents = array(
        array(
            'agent_icon'        => 'fa-shield-heart',
            'agent_title'       => __('AI Ethics', 'responsible-ai'),
            'agent_tagline'     => __('Ensuring fairness and accountability', 'responsible-ai'),
            'agent_description' => __('Our AI Ethics agent monitors and evaluates AI systems for bias, fairness, and ethical considerations. It ensures that AI decisions align with human values and societal norms, promoting responsible development practices.', 'responsible-ai'),
            'agent_link'        => home_url('/ai-ethics/'),
        ),
        array(
            'agent_icon'        => 'fa-lock-keyhole',
            'agent_title'       => __('AI Safety', 'responsible-ai'),
            'agent_tagline'     => __('Protecting users and systems', 'responsible-ai'),
            'agent_description' => __('The AI Safety agent focuses on risk assessment, threat detection, and security monitoring. It implements safeguards to prevent harmful outcomes and ensures AI systems operate within safe parameters at all times.', 'responsible-ai'),
            'agent_link'        => home_url('/ai-safety/'),
        ),
        array(
            'agent_icon'        => 'fa-eye',
            'agent_title'       => __('AI Transparency', 'responsible-ai'),
            'agent_tagline'     => __('Clear, explainable AI decisions', 'responsible-ai'),
            'agent_description' => __('Our Transparency agent provides clear explanations of AI decision-making processes. It generates audit trails, visualizes model behavior, and ensures stakeholders understand how and why AI systems reach their conclusions.', 'responsible-ai'),
            'agent_link'        => home_url('/ai-transparency/'),
        ),
        array(
            'agent_icon'        => 'fa-scale-balanced',
            'agent_title'       => __('AI Accountability', 'responsible-ai'),
            'agent_tagline'     => __('Governance and compliance', 'responsible-ai'),
            'agent_description' => __('The Accountability agent tracks AI system performance, maintains compliance records, and ensures proper governance structures. It provides oversight mechanisms and reporting tools for responsible AI deployment.', 'responsible-ai'),
            'agent_link'        => home_url('/ai-accountability/'),
        ),
    );
}

// Section heading from ACF or default
$section_title = responsibleai_get_field('ai_agents_title', false, __('Our AI Guardian Agents', 'responsible-ai'));
$section_subtitle = responsibleai_get_field('ai_agents_subtitle', false, __('Meet the intelligent systems ensuring responsible AI practices', 'responsible-ai'));
?>

<section class="ai-agents-section" id="ai-agents">
    <div class="container">
        <!-- Section Header -->
        <div class="section-header" data-animate>
            <h2 class="section-title"><?php echo esc_html($section_title); ?></h2>
            <?php if (!empty($section_subtitle)) : ?>
                <p class="section-subtitle"><?php echo esc_html($section_subtitle); ?></p>
            <?php endif; ?>
        </div>

        <!-- Flip Cards Grid -->
        <div class="flip-cards-grid">
            <?php foreach ($agents as $index => $agent) : ?>
                <div class="flip-card"
                     data-animate
                     tabindex="0"
                     role="button"
                     aria-label="<?php echo esc_attr(sprintf(__('Flip card: %s', 'responsible-ai'), $agent['agent_title'])); ?>"
                     style="animation-delay: <?php echo esc_attr($index * 0.1); ?>s;">

                    <div class="flip-card__inner">
                        <!-- Front Side -->
                        <div class="flip-card__front">
                            <div class="flip-card__content">
                                <?php if (!empty($agent['agent_icon'])) : ?>
                                    <div class="flip-card__icon" aria-hidden="true">
                                        <i class="fa-solid <?php echo esc_attr($agent['agent_icon']); ?>"></i>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($agent['agent_title'])) : ?>
                                    <h3 class="flip-card__title">
                                        <?php echo esc_html($agent['agent_title']); ?>
                                    </h3>
                                <?php endif; ?>

                                <?php if (!empty($agent['agent_tagline'])) : ?>
                                    <p class="flip-card__tagline">
                                        <?php echo esc_html($agent['agent_tagline']); ?>
                                    </p>
                                <?php endif; ?>

                                <span class="flip-card__hint" aria-hidden="true">
                                    <i class="fa-solid fa-repeat"></i>
                                    <?php esc_html_e('Click to learn more', 'responsible-ai'); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Back Side -->
                        <div class="flip-card__back">
                            <div class="flip-card__content">
                                <?php if (!empty($agent['agent_description'])) : ?>
                                    <p class="flip-card__description">
                                        <?php echo esc_html($agent['agent_description']); ?>
                                    </p>
                                <?php endif; ?>

                                <?php if (!empty($agent['agent_link'])) : ?>
                                    <a href="<?php echo esc_url($agent['agent_link']); ?>"
                                       class="btn btn-outline btn-sm"
                                       onclick="event.stopPropagation();">
                                        <?php esc_html_e('Learn More', 'responsible-ai'); ?>
                                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
/* ==========================================================================
   AI Agents Section
   ========================================================================== */

.ai-agents-section {
    padding: var(--space-16) 0;
    background: var(--color-background);
    position: relative;
    overflow: hidden;
}

.ai-agents-section::before {
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
.ai-agents-section .section-header {
    text-align: center;
    max-width: 800px;
    margin: 0 auto var(--space-12);
}

.ai-agents-section .section-title {
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: var(--font-weight-bold);
    line-height: 1.2;
    margin-bottom: var(--space-4);
    background: linear-gradient(135deg, var(--color-foreground) 0%, var(--color-primary) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.ai-agents-section .section-subtitle {
    font-size: clamp(1rem, 2vw, 1.25rem);
    color: var(--color-foreground-secondary);
    line-height: 1.6;
}

/* Flip Cards Grid */
.flip-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: var(--space-8);
    max-width: 1200px;
    margin: 0 auto;
}

/* Flip Card Container */
.flip-card {
    perspective: 1000px;
    height: 360px;
    cursor: pointer;
    outline: none;
    position: relative;
    transition: transform var(--duration-default) var(--ease-default);
}

.flip-card:hover {
    transform: translateY(-4px);
}

.flip-card:focus-visible {
    outline: 2px solid var(--color-primary);
    outline-offset: 4px;
    border-radius: var(--radius-lg);
}

/* Inner Container for 3D Flip */
.flip-card__inner {
    position: relative;
    width: 100%;
    height: 100%;
    transition: transform 0.6s cubic-bezier(0.4, 0.0, 0.2, 1);
    transform-style: preserve-3d;
}

.flip-card.is-flipped .flip-card__inner,
.flip-card:hover .flip-card__inner {
    transform: rotateY(180deg);
}

/* Prevent flip on hover for mobile/touch devices */
@media (hover: none) {
    .flip-card:hover .flip-card__inner {
        transform: rotateY(0deg);
    }
}

/* Front and Back Sides */
.flip-card__front,
.flip-card__back {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
    border-radius: var(--radius-lg);
    padding: var(--space-8);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Front Side Styling */
.flip-card__front {
    background: linear-gradient(
        135deg,
        var(--color-background-elevated) 0%,
        var(--color-background) 100%
    );
    border: 1px solid var(--color-border);
}

.flip-card__front .flip-card__content {
    text-align: center;
}

.flip-card__icon {
    width: 80px;
    height: 80px;
    margin: 0 auto var(--space-6);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: var(--color-primary);
    background: var(--color-primary-muted);
    border-radius: var(--radius-full);
    transition: all var(--duration-fast) var(--ease-default);
}

.flip-card:hover .flip-card__icon {
    transform: scale(1.1) rotate(5deg);
    background: var(--color-primary);
    color: var(--color-background);
}

.flip-card__title {
    font-size: 1.5rem;
    font-weight: var(--font-weight-bold);
    color: var(--color-foreground);
    margin-bottom: var(--space-3);
    line-height: 1.3;
}

.flip-card__tagline {
    font-size: 1rem;
    color: var(--color-foreground-secondary);
    line-height: 1.5;
    margin-bottom: var(--space-6);
}

.flip-card__hint {
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
    font-size: 0.875rem;
    color: var(--color-primary);
    font-weight: var(--font-weight-medium);
}

.flip-card__hint i {
    font-size: 0.75rem;
}

/* Back Side Styling */
.flip-card__back {
    background: linear-gradient(
        135deg,
        var(--color-primary) 0%,
        var(--color-primary-dark, hsl(217, 91%, 45%)) 100%
    );
    color: var(--color-background);
    transform: rotateY(180deg);
}

.flip-card__back .flip-card__content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
}

.flip-card__description {
    font-size: 0.9375rem;
    line-height: 1.6;
    margin-bottom: var(--space-6);
    color: rgba(255, 255, 255, 0.95);
}

.flip-card__back .btn {
    background: var(--color-background);
    color: var(--color-primary);
    border-color: var(--color-background);
    font-weight: var(--font-weight-semibold);
    display: inline-flex;
    align-items: center;
    gap: var(--space-2);
}

.flip-card__back .btn:hover {
    background: var(--color-foreground);
    color: var(--color-background);
    border-color: var(--color-foreground);
    transform: translateX(4px);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .flip-cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .ai-agents-section {
        padding: var(--space-12) 0;
    }

    .flip-cards-grid {
        grid-template-columns: 1fr;
        gap: var(--space-6);
    }

    .flip-card {
        height: 320px;
    }

    .flip-card__front,
    .flip-card__back {
        padding: var(--space-6);
    }

    .flip-card__icon {
        width: 64px;
        height: 64px;
        font-size: 2rem;
    }

    .flip-card__title {
        font-size: 1.25rem;
    }

    /* Auto-flip on mobile is handled by JavaScript click handler */
    .flip-card__hint {
        font-size: 0.8125rem;
    }
}

@media (max-width: 480px) {
    .flip-card {
        height: 300px;
    }

    .flip-card__description {
        font-size: 0.875rem;
    }
}

/* Accessibility: Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    .flip-card,
    .flip-card__inner,
    .flip-card__icon,
    .flip-card__back .btn {
        transition: none;
    }

    .flip-card:hover {
        transform: none;
    }

    .flip-card__icon {
        transition: color var(--duration-fast) var(--ease-default);
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

.flip-card[data-animate] {
    opacity: 0;
}

.flip-card[data-animate].is-visible {
    animation: fadeInUp 0.6s var(--ease-default) forwards;
}

/* Print Styles */
@media print {
    .flip-card__back {
        position: relative;
        transform: none;
        margin-top: var(--space-4);
    }

    .flip-card__hint {
        display: none;
    }
}
</style>
