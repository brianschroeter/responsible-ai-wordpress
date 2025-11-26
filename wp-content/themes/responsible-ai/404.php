<?php
/**
 * 404 Page Template
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<section class="section error-404">
    <div class="container">
        <div class="error-404__content text-center">
            <h1 class="error-404__title">404</h1>
            <h2 class="error-404__subtitle"><?php esc_html_e('Page Not Found', 'responsible-ai'); ?></h2>
            <p class="error-404__description">
                <?php esc_html_e('The page you\'re looking for doesn\'t exist or has been moved.', 'responsible-ai'); ?>
            </p>
            <div class="error-404__actions">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                    <i class="fas fa-home"></i>
                    <?php esc_html_e('Back to Home', 'responsible-ai'); ?>
                </a>
                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-outline">
                    <?php esc_html_e('Contact Us', 'responsible-ai'); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<style>
.error-404 {
    min-height: 60vh;
    display: flex;
    align-items: center;
}
.error-404__title {
    font-size: clamp(6rem, 20vw, 12rem);
    font-weight: var(--font-weight-bold);
    color: var(--color-primary);
    line-height: 1;
    margin-bottom: var(--space-4);
    opacity: 0.3;
}
.error-404__subtitle {
    font-size: var(--font-size-3xl);
    margin-bottom: var(--space-4);
}
.error-404__description {
    color: var(--color-foreground-muted);
    font-size: var(--font-size-lg);
    max-width: 500px;
    margin: 0 auto var(--space-8);
}
.error-404__actions {
    display: flex;
    gap: var(--space-4);
    justify-content: center;
    flex-wrap: wrap;
}
</style>

<?php get_footer(); ?>
