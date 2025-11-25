<?php
/**
 * Why Choose Us Section Template Part
 * Pixel-perfect match of source site
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

// Get all Why Choose Us data from ACF helper
$data = mydefenselaw_get_why_choose_us_fields();
?>

<section class="why-choose-us">
    <div class="container">
        <div class="content-columns">
            <div class="why-content">
                <h2><?php echo esc_html($data['title']); ?></h2>
                <p class="lead-text">
                    <?php echo esc_html($data['lead_text']); ?>
                </p>

                <div class="why-features">
                    <?php foreach ($data['features'] as $feature) : ?>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="<?php echo esc_attr($feature['icon']); ?>"></i>
                        </div>
                        <div class="feature-content">
                            <h4><?php echo esc_html($feature['title']); ?></h4>
                            <p><?php echo esc_html($feature['description']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="cta-section">
                    <h3><?php echo esc_html($data['cta']['title']); ?></h3>
                    <p><?php echo esc_html($data['cta']['text']); ?></p>
                    <div class="cta-buttons">
                        <a href="tel:<?php echo esc_attr($data['phone_raw']); ?>" class="btn btn-primary">Call <?php echo esc_html($data['phone']); ?></a>
                        <a href="<?php echo esc_url(home_url('/free-consultation/')); ?>" class="btn btn-outline consultation-trigger">Free Consultation</a>
                    </div>
                </div>
            </div>

            <?php if (!empty($data['attorneys'])) : ?>
            <div class="results-sidebar">
                <h3><?php echo esc_html($data['sidebar_title']); ?></h3>
                <div class="results-list">
                    <?php foreach ($data['attorneys'] as $attorney) : ?>
                    <div class="result-item">
                        <div class="result-amount"><?php echo esc_html($attorney['name']); ?></div>
                        <div class="result-case"><?php echo esc_html($attorney['credentials'] ?: $attorney['title']); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="testimonial-slider">
                    <?php if (!empty($data['testimonials'])) : ?>
                        <?php foreach ($data['testimonials'] as $index => $testimonial) : ?>
                        <div class="testimonial-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                            <div class="quote-icon">
                                <i class="fas fa-quote-left"></i>
                            </div>
                            <blockquote>
                                <?php echo wp_kses_post(wpautop($testimonial['quote'])); ?>
                            </blockquote>
                            <cite>- <?php echo esc_html($testimonial['author']); ?></cite>
                            <div class="stars">
                                <?php for ($i = 0; $i < (int)$testimonial['rating']; $i++) : ?>
                                <i class="fas fa-star"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <?php if (count($data['testimonials']) > 1) : ?>
                        <div class="testimonial-controls">
                            <button class="testimonial-prev" aria-label="Previous testimonial">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <div class="testimonial-dots">
                                <?php foreach ($data['testimonials'] as $index => $testimonial) : ?>
                                <span class="dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>"></span>
                                <?php endforeach; ?>
                            </div>
                            <button class="testimonial-next" aria-label="Next testimonial">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
