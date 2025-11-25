<?php
/**
 * Template Name: Latest Legal News
 *
 * Custom page template for the Latest Legal News page
 * Content is managed via ACF fields with section visibility toggles.
 * Displays recent legal updates and developments
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

get_header();

// Get ACF fields with defaults
$news = mydefenselaw_get_latest_news_fields();

// Section visibility toggles (default to showing if field not set)
$show_intro = get_field('enable_news_intro') !== false;
$show_news_items = get_field('enable_news_items') !== false;
$show_cta = get_field('enable_news_cta') !== false;
$show_reasons = get_field('enable_news_reasons') !== false;
$show_final_cta = get_field('enable_news_final_cta') !== false;
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1><?php echo esc_html(get_the_title()); ?></h1>
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>">Home</a> / <span aria-current="page"><?php echo esc_html(get_the_title()); ?></span>
            </nav>
        </div>
    </div>
</section>

<!-- Main Content -->
<main class="main-content">
    <div class="container">

        <div class="news-section">

            <?php if ($show_intro) : ?>
            <!-- Intro Section -->
            <h2><?php echo esc_html($news['intro_title']); ?></h2>
            <div class="intro-text">
                <?php echo wp_kses_post($news['intro_content']); ?>
            </div>
            <?php endif; ?>

            <?php if ($show_news_items && !empty($news['news_items'])) : ?>
            <!-- News Items Grid -->
            <div class="news-grid">
                <?php foreach ($news['news_items'] as $item) : ?>
                    <?php if (!empty($item['news_title'])) : ?>
                    <article class="news-item">
                        <?php if (!empty($item['news_category'])) : ?>
                        <div class="news-meta">
                            <span><i class="fas fa-calendar" aria-hidden="true"></i> <?php echo esc_html($item['news_category']); ?></span>
                        </div>
                        <?php endif; ?>
                        <h3><?php echo esc_html($item['news_title']); ?></h3>
                        <?php if (!empty($item['news_content'])) : ?>
                        <p><?php echo esc_html($item['news_content']); ?></p>
                        <?php endif; ?>
                    </article>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ($show_cta) : ?>
            <!-- CTA Banner -->
            <div class="news-cta-banner">
                <h3><?php echo esc_html($news['cta_title']); ?></h3>
                <p><?php echo esc_html($news['cta_content']); ?></p>
                <div class="news-cta-buttons">
                    <a href="tel:<?php echo esc_attr($news['phone_raw']); ?>" class="btn btn-primary">
                        <i class="fas fa-phone" aria-hidden="true"></i> Call for Legal Updates
                    </a>
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-secondary">
                        <i class="fas fa-calendar-check" aria-hidden="true"></i> Schedule Consultation
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($show_reasons) : ?>
            <!-- Why Legal News Matters -->
            <h3><?php echo esc_html($news['reasons_title']); ?></h3>

            <?php if (!empty($news['reasons'])) : ?>
            <div class="news-reasons-grid">
                <?php foreach ($news['reasons'] as $reason) : ?>
                    <?php if (!empty($reason['reason_title'])) : ?>
                    <div class="news-reason">
                        <h4>
                            <?php if (!empty($reason['reason_icon'])) : ?>
                            <i class="<?php echo esc_attr($reason['reason_icon']); ?>" aria-hidden="true"></i>
                            <?php endif; ?>
                            <?php echo esc_html($reason['reason_title']); ?>
                        </h4>
                        <?php if (!empty($reason['reason_content'])) : ?>
                        <p><?php echo esc_html($reason['reason_content']); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>

            <?php if ($show_final_cta) : ?>
            <!-- Final CTA Section -->
            <div class="cta-section" style="margin-top: 3rem;">
                <h3><?php echo esc_html($news['final_cta_title']); ?></h3>
                <p><?php echo esc_html($news['final_cta_content']); ?></p>
                <div class="cta-buttons">
                    <a href="tel:<?php echo esc_attr($news['phone_raw']); ?>" class="btn btn-primary">
                        <i class="fas fa-phone" aria-hidden="true"></i> Call <?php echo esc_html($news['phone']); ?>
                    </a>
                    <button class="btn btn-outline consultation-trigger" id="modalTriggerNews" aria-label="Request a free legal consultation">
                        <i class="fas fa-calendar-check" aria-hidden="true"></i> Free Consultation
                    </button>
                </div>
            </div>
            <?php endif; ?>

        </div>

    </div>
</main>

<?php
get_footer();
