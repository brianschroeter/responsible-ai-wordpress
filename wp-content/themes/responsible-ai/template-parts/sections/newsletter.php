<?php
/**
 * Newsletter Signup Section
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get newsletter content from ACF
$headline = responsibleai_get_field('newsletter_headline', false, __('Stay Informed', 'responsible-ai'));
$subtext = responsibleai_get_field('newsletter_subtext', false, __('Subscribe to our newsletter for the latest updates on responsible AI practices, certifications, and industry insights.', 'responsible-ai'));
$button_text = responsibleai_get_field('newsletter_button_text', false, __('Subscribe', 'responsible-ai'));
$privacy_text = responsibleai_get_field('newsletter_privacy_text', false, __('We respect your privacy. Unsubscribe at any time.', 'responsible-ai'));

// Generate nonce for form security
$nonce = wp_create_nonce('responsibleai_newsletter');
?>

<section class="newsletter-section">
    <div class="container">
        <div class="newsletter-wrapper" data-animate>
            <div class="newsletter__content">
                <h2 class="newsletter__headline"><?php echo esc_html($headline); ?></h2>
                <?php if (!empty($subtext)) : ?>
                    <p class="newsletter__subtext"><?php echo esc_html($subtext); ?></p>
                <?php endif; ?>
            </div>

            <form class="newsletter-form" method="post" action="" novalidate>
                <!-- Honeypot field for spam protection -->
                <input type="text"
                       name="website_url"
                       class="newsletter-form__honeypot"
                       tabindex="-1"
                       autocomplete="off"
                       aria-hidden="true">

                <!-- Nonce field for security -->
                <input type="hidden" name="newsletter_nonce" value="<?php echo esc_attr($nonce); ?>">

                <div class="newsletter-form__group">
                    <label for="newsletter-email" class="sr-only">
                        <?php echo esc_html__('Email Address', 'responsible-ai'); ?>
                    </label>
                    <input type="email"
                           id="newsletter-email"
                           name="email"
                           class="newsletter-form__input"
                           placeholder="<?php echo esc_attr__('Enter your email address', 'responsible-ai'); ?>"
                           required
                           aria-required="true">

                    <button type="submit" class="newsletter-form__submit btn btn-cta">
                        <?php echo esc_html($button_text); ?>
                    </button>
                </div>

                <!-- Message display area -->
                <div class="newsletter-form__messages" role="status" aria-live="polite"></div>

                <?php if (!empty($privacy_text)) : ?>
                    <p class="newsletter-form__privacy"><?php echo esc_html($privacy_text); ?></p>
                <?php endif; ?>
            </form>
        </div>
    </div>
</section>

<style>
.newsletter-section {
    padding: var(--space-20) 0;
    background: linear-gradient(
        135deg,
        var(--color-background) 0%,
        var(--color-background-elevated) 50%,
        var(--color-primary-muted) 100%
    );
    position: relative;
    overflow: hidden;
}

.newsletter-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(
        circle at 50% 50%,
        var(--color-cta) 0%,
        transparent 70%
    );
    opacity: 0.05;
    pointer-events: none;
}

.newsletter-wrapper {
    position: relative;
    z-index: 1;
    background: var(--color-background-elevated);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    padding: var(--space-12);
    text-align: center;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3),
                0 2px 4px -1px rgba(0, 0, 0, 0.24);
}

.newsletter__content {
    max-width: 700px;
    margin: 0 auto var(--space-8);
}

.newsletter__headline {
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: var(--font-weight-bold);
    color: var(--color-foreground);
    line-height: 1.2;
    margin-bottom: var(--space-4);
    background: linear-gradient(135deg, var(--color-foreground) 0%, var(--color-cta-light) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.newsletter__subtext {
    font-size: clamp(1rem, 1.5vw, 1.125rem);
    color: var(--color-foreground-secondary);
    line-height: 1.6;
    margin: 0;
}

.newsletter-form {
    max-width: 600px;
    margin: 0 auto;
}

.newsletter-form__honeypot {
    position: absolute;
    left: -9999px;
    width: 1px;
    height: 1px;
    opacity: 0;
    pointer-events: none;
}

.newsletter-form__group {
    display: flex;
    gap: var(--space-3);
    margin-bottom: var(--space-4);
}

.newsletter-form__input {
    flex: 1;
    padding: var(--space-4) var(--space-5);
    background: var(--color-background);
    border: 2px solid var(--color-border);
    border-radius: var(--radius-md);
    color: var(--color-foreground);
    font-size: var(--font-size-base);
    font-family: var(--font-family-primary);
    transition: all var(--duration-default) var(--ease-default);
}

.newsletter-form__input::placeholder {
    color: var(--color-foreground-muted);
}

.newsletter-form__input:focus {
    outline: none;
    border-color: var(--color-cta);
    box-shadow: 0 0 0 3px hsla(38, 92%, 50%, 0.2);
}

.newsletter-form__input:hover {
    border-color: var(--color-border-hover);
}

.newsletter-form__input.error {
    border-color: var(--color-error);
}

.newsletter-form__submit {
    padding: var(--space-4) var(--space-6);
    white-space: nowrap;
    font-weight: var(--font-weight-semibold);
    transition: all var(--duration-default) var(--ease-default);
}

.newsletter-form__submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.newsletter-form__messages {
    min-height: 24px;
    margin-bottom: var(--space-3);
}

.form-message {
    padding: var(--space-4) var(--space-5);
    border-radius: var(--radius-md);
    font-size: var(--font-size-sm);
    line-height: 1.5;
    margin: 0;
    animation: slideInDown 0.3s var(--ease-out);
}

.form-message--success {
    background: hsla(142, 76%, 36%, 0.1);
    border: 1px solid var(--color-success);
    color: var(--color-success);
}

.form-message--error {
    background: hsla(0, 84%, 60%, 0.1);
    border: 1px solid var(--color-error);
    color: var(--color-error);
}

.newsletter-form__privacy {
    font-size: var(--font-size-xs);
    color: var(--color-foreground-muted);
    margin: 0;
    line-height: 1.5;
}

/* Screen reader only */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}

/* Animations */
@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Animation support for scroll trigger */
[data-animate].is-visible .newsletter-wrapper {
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

/* Responsive adjustments */
@media (max-width: 768px) {
    .newsletter-section {
        padding: var(--space-16) 0;
    }

    .newsletter-wrapper {
        padding: var(--space-8);
    }

    .newsletter__content {
        margin-bottom: var(--space-6);
    }

    .newsletter-form__group {
        flex-direction: column;
        gap: var(--space-3);
    }

    .newsletter-form__submit {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .newsletter-wrapper {
        padding: var(--space-6);
    }

    .newsletter__headline {
        font-size: 1.75rem;
    }

    .newsletter__subtext {
        font-size: 0.9375rem;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .newsletter-form__input {
        border-width: 3px;
    }

    .newsletter-wrapper {
        border-width: 2px;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .newsletter-wrapper,
    .form-message {
        animation: none;
    }

    .newsletter-form__input,
    .newsletter-form__submit {
        transition: none;
    }
}

/* Print styles */
@media print {
    .newsletter-section {
        display: none;
    }
}
</style>
