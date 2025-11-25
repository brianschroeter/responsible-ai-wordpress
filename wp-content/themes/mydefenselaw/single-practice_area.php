<?php
/**
 * Single Practice Area Template
 *
 * Template for displaying individual practice area pages with content and sidebar.
 * Uses ACF fields for content management with fallback defaults.
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

get_header();

// Get practice area data
$practice_area = mydefenselaw_get_practice_area_fields();
$phone = mydefenselaw_get_primary_phone();
$phone_raw = preg_replace('/[^0-9]/', '', $phone);
?>

<main id="main" class="site-main practice-area-single">

    <!-- Page Header -->
    <section class="page-header practice-area-header">
        <div class="container">
            <h1 class="page-title"><?php echo esc_html($practice_area['title']); ?></h1>
            <?php if (!empty($practice_area['short_description'])) : ?>
                <p class="page-subtitle"><?php echo esc_html($practice_area['short_description']); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Main Content Area -->
    <section class="practice-area-content-section">
        <div class="container">
            <div class="practice-area-layout">

                <!-- Main Content Column -->
                <div class="practice-area-main">
                    <article class="practice-area-article">
                        <?php if (!empty($practice_area['page_content'])) : ?>
                            <div class="practice-area-content">
                                <?php echo wp_kses_post($practice_area['page_content']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($practice_area['services'])) : ?>
                            <div class="practice-area-services">
                                <h3><?php esc_html_e('Our Services', 'mydefenselaw'); ?></h3>
                                <ul class="services-list">
                                    <?php foreach ($practice_area['services'] as $service) : ?>
                                        <li>
                                            <strong><?php echo esc_html($service['name']); ?></strong>
                                            <?php if (!empty($service['description'])) : ?>
                                                <p><?php echo esc_html($service['description']); ?></p>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($practice_area['faqs'])) : ?>
                            <div class="practice-area-faqs">
                                <h3><?php esc_html_e('Frequently Asked Questions', 'mydefenselaw'); ?></h3>
                                <div class="faq-list">
                                    <?php foreach ($practice_area['faqs'] as $faq) : ?>
                                        <div class="faq-item">
                                            <h4 class="faq-question"><?php echo esc_html($faq['question']); ?></h4>
                                            <div class="faq-answer">
                                                <?php echo wp_kses_post($faq['answer']); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- CTA Section -->
                        <div class="practice-area-cta">
                            <h3><?php echo esc_html($practice_area['cta_title']); ?></h3>
                            <p><?php echo esc_html($practice_area['cta_text']); ?></p>
                            <a href="tel:+1<?php echo esc_attr($phone_raw); ?>" class="btn btn-primary">
                                <i class="fas fa-phone"></i>
                                <?php echo esc_html($practice_area['cta_button_text']); ?>
                            </a>
                        </div>
                    </article>
                </div>

                <!-- Sidebar -->
                <aside class="practice-area-sidebar">
                    <!-- Quick Contact Card -->
                    <div class="sidebar-card contact-card">
                        <h3><?php esc_html_e('Free Consultation', 'mydefenselaw'); ?></h3>
                        <p><?php esc_html_e('Speak with an experienced attorney today. We\'re available 24/7 to discuss your case.', 'mydefenselaw'); ?></p>
                        <a href="tel:+1<?php echo esc_attr($phone_raw); ?>" class="btn btn-primary btn-block">
                            <i class="fas fa-phone"></i> <?php echo esc_html($phone); ?>
                        </a>
                        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-outline btn-block">
                            <i class="fas fa-envelope"></i> <?php esc_html_e('Contact Form', 'mydefenselaw'); ?>
                        </a>
                    </div>

                    <!-- Practice Areas Navigation -->
                    <div class="sidebar-card practice-areas-nav">
                        <h3><?php esc_html_e('Practice Areas', 'mydefenselaw'); ?></h3>
                        <ul class="practice-areas-list">
                            <?php
                            $practice_areas = get_posts(array(
                                'post_type' => 'practice_area',
                                'posts_per_page' => -1,
                                'orderby' => 'menu_order',
                                'order' => 'ASC',
                            ));
                            foreach ($practice_areas as $pa) :
                                $is_current = ($pa->ID === get_the_ID());
                                $icon = get_field('practice_area_icon', $pa->ID) ?: 'fas fa-gavel';
                            ?>
                                <li class="<?php echo $is_current ? 'current' : ''; ?>">
                                    <a href="<?php echo esc_url(get_permalink($pa->ID)); ?>">
                                        <i class="<?php echo esc_attr($icon); ?>"></i>
                                        <?php echo esc_html($pa->post_title); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Why Choose Us Card -->
                    <div class="sidebar-card why-choose-card">
                        <h3><?php esc_html_e('Why Choose Us?', 'mydefenselaw'); ?></h3>
                        <ul class="why-list">
                            <li><i class="fas fa-check"></i> <?php esc_html_e('25+ Years Experience', 'mydefenselaw'); ?></li>
                            <li><i class="fas fa-check"></i> <?php esc_html_e('Free Consultation', 'mydefenselaw'); ?></li>
                            <li><i class="fas fa-check"></i> <?php esc_html_e('Available 24/7', 'mydefenselaw'); ?></li>
                            <li><i class="fas fa-check"></i> <?php esc_html_e('Proven Results', 'mydefenselaw'); ?></li>
                        </ul>
                    </div>
                </aside>

            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>
