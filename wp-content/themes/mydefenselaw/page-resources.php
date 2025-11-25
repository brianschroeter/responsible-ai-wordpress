<?php
/**
 * Template Name: Legal Resources
 *
 * Custom page template for the Legal Resources page
 * Content is managed via ACF fields with section visibility toggles.
 * Provides educational resources and links for various areas of law
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

get_header();

// Get ACF fields with defaults
$resources = mydefenselaw_get_resources_fields();

// Section visibility toggles (default to showing if field not set)
$show_intro = get_field('enable_resources_intro') !== false;
$show_categories = get_field('enable_resources_categories') !== false;
$show_cta = get_field('enable_resources_cta') !== false;
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

        <div class="resources-section">

            <?php if ($show_intro) : ?>
            <!-- Intro Section -->
            <h2><?php echo esc_html($resources['intro_title']); ?></h2>
            <div class="intro-text">
                <?php echo wp_kses_post($resources['intro_content']); ?>
            </div>
            <?php endif; ?>

            <?php if ($show_categories && !empty($resources['categories'])) : ?>
            <!-- Resource Categories -->
            <?php foreach ($resources['categories'] as $category) : ?>
                <?php if (!empty($category['category_title'])) : ?>
                <div class="resource-category">
                    <h3>
                        <?php if (!empty($category['category_icon'])) : ?>
                        <i class="<?php echo esc_attr($category['category_icon']); ?>" aria-hidden="true"></i>
                        <?php endif; ?>
                        <?php echo esc_html($category['category_title']); ?>
                    </h3>

                    <?php if (!empty($category['category_links'])) : ?>
                    <ul class="resource-links">
                        <?php foreach ($category['category_links'] as $link) : ?>
                        <li>
                            <?php if (!empty($link['link_url'])) : ?>
                            <a href="<?php echo esc_url($link['link_url']); ?>" target="_blank" rel="noopener noreferrer">
                                <strong><?php echo esc_html($link['link_title']); ?></strong>
                            </a>
                            <?php else : ?>
                            <strong><?php echo esc_html($link['link_title']); ?></strong>
                            <?php endif; ?>
                            <?php if (!empty($link['link_description'])) : ?>
                             - <?php echo esc_html($link['link_description']); ?>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($show_cta) : ?>
            <!-- CTA Section -->
            <div class="cta-section" style="margin-top: 3rem;">
                <h3><?php echo esc_html($resources['cta_title']); ?></h3>
                <p><?php echo esc_html($resources['cta_content']); ?></p>
                <div class="cta-buttons">
                    <a href="tel:<?php echo esc_attr($resources['phone_raw']); ?>" class="btn btn-primary">
                        <i class="fas fa-phone" aria-hidden="true"></i> Call <?php echo esc_html($resources['phone']); ?>
                    </a>
                    <button class="btn btn-outline consultation-trigger" id="modalTriggerResources" aria-label="Request a free legal consultation">
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
