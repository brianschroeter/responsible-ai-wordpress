<?php
/**
 * Attorneys Section Template Part
 * Displays attorney team members on homepage
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

// Get section settings from ACF
$front_page_id = get_option('page_on_front');

$section_title = 'Meet Our Attorneys';
$section_subtitle = 'Our team of experienced attorneys is dedicated to protecting your rights and achieving the best possible outcome for your case.';
$display_mode = 'all';
$limit = 3;
$cta_text = 'Meet The Team';
$cta_link = '/about/#attorneys';

if (function_exists('get_field')) {
    $acf_title = get_field('attorneys_section_title', $front_page_id);
    $acf_subtitle = get_field('attorneys_section_subtitle', $front_page_id);
    $acf_mode = get_field('attorneys_display_mode', $front_page_id);
    $acf_limit = get_field('attorneys_limit', $front_page_id);
    $acf_cta_text = get_field('attorneys_cta_text', $front_page_id);
    $acf_cta_link = get_field('attorneys_cta_link', $front_page_id);

    if ($acf_title) $section_title = $acf_title;
    if ($acf_subtitle) $section_subtitle = $acf_subtitle;
    if ($acf_mode) $display_mode = $acf_mode;
    if ($acf_limit) $limit = intval($acf_limit);
    if ($acf_cta_text) $cta_text = $acf_cta_text;
    if ($acf_cta_link) $cta_link = $acf_cta_link;
}

// Get attorneys based on display mode
$attorneys = array();

if ($display_mode === 'select' && function_exists('get_field')) {
    // Use selected attorneys from relationship field
    $selected = get_field('attorneys_selected', $front_page_id);
    if ($selected) {
        foreach ($selected as $attorney) {
            $attorneys[] = $attorney;
        }
    }
} else {
    // Query attorneys from CPT
    $args = array(
        'post_type' => 'attorney',
        'post_status' => 'publish',
        'posts_per_page' => ($display_mode === 'limit') ? $limit : -1,
        'orderby' => 'menu_order',
        'order' => 'ASC',
    );

    $query = new WP_Query($args);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $attorneys[] = get_post();
        }
        wp_reset_postdata();
    }
}

// Don't display section if no attorneys
if (empty($attorneys)) {
    return;
}
?>

<section class="attorneys-section" id="attorneys">
    <div class="container">
        <div class="section-header">
            <h2><?php echo esc_html($section_title); ?></h2>
            <p><?php echo esc_html($section_subtitle); ?></p>
        </div>

        <div class="attorneys-grid">
            <?php foreach ($attorneys as $attorney) :
                $attorney_id = $attorney->ID;
                $name = get_the_title($attorney_id);
                $title = get_field('attorney_title', $attorney_id) ?: 'Attorney';
                $short_bio = get_field('short_bio', $attorney_id);
                $photo = get_field('photo', $attorney_id);
                $email = get_field('email', $attorney_id);
                $linkedin = get_field('linkedin', $attorney_id);
                $permalink = get_permalink($attorney_id);

                // Default placeholder image
                $photo_url = $photo ? $photo['url'] : 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=500&fit=crop';
            ?>
            <div class="attorney-card">
                <div class="attorney-photo">
                    <img src="<?php echo esc_url($photo_url); ?>" alt="<?php echo esc_attr($name); ?>">
                    <div class="attorney-overlay">
                        <div class="attorney-social">
                            <?php if ($email) : ?>
                            <a href="mailto:<?php echo esc_attr($email); ?>" title="Email <?php echo esc_attr($name); ?>">
                                <i class="fas fa-envelope"></i>
                            </a>
                            <?php endif; ?>
                            <?php if ($linkedin) : ?>
                            <a href="<?php echo esc_url($linkedin); ?>" target="_blank" rel="noopener" title="LinkedIn Profile">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <?php endif; ?>
                            <a href="<?php echo esc_url($permalink); ?>" title="View Profile">
                                <i class="fas fa-user"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="attorney-info">
                    <h3 class="attorney-name">
                        <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($name); ?></a>
                    </h3>
                    <p class="attorney-title"><?php echo esc_html($title); ?></p>
                    <?php if ($short_bio) : ?>
                    <p class="attorney-bio"><?php echo esc_html(wp_trim_words($short_bio, 20, '...')); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($cta_link) : ?>
        <div class="section-cta">
            <a href="<?php echo esc_url($cta_link); ?>" class="btn btn-outline">
                <?php echo esc_html($cta_text); ?>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>
