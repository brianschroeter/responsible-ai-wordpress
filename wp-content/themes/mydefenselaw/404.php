<?php
/**
 * 404 Error Page Template
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" class="site-main error-404">
    <div class="container">
        <div class="error-404-content">
            <h1 class="page-title"><?php esc_html_e('404', 'mydefenselaw'); ?></h1>
            <h2 class="error-title"><?php esc_html_e('Page Not Found', 'mydefenselaw'); ?></h2>
            <p class="error-message">
                <?php esc_html_e('The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'mydefenselaw'); ?>
            </p>
            <div class="error-actions">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                    <?php esc_html_e('Go to Homepage', 'mydefenselaw'); ?>
                </a>
                <a href="tel:+1234567890" class="btn btn-secondary">
                    <?php esc_html_e('Call Us', 'mydefenselaw'); ?>
                </a>
            </div>
            <div class="search-form-wrapper">
                <?php get_search_form(); ?>
            </div>
        </div>
    </div>
</main>

<?php
get_footer();
