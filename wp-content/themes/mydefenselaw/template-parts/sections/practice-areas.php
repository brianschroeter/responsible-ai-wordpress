<?php
/**
 * Practice Areas Section Template Part
 * Queries Practice Area CPT with homepage toggle
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

// Get section header fields from ACF (with defaults)
$section_title = 'How We Can Defend You';
$section_subtitle = 'Our experienced attorneys handle a wide range of legal matters with proven results.';

if (function_exists('get_field')) {
    $front_page_id = get_option('page_on_front');
    $acf_title = get_field('practice_areas_title', $front_page_id);
    $acf_subtitle = get_field('practice_areas_subtitle', $front_page_id);
    if ($acf_title) $section_title = $acf_title;
    if ($acf_subtitle) $section_subtitle = $acf_subtitle;
}

// Initialize practice areas array
$practice_areas = array();

// Try to get from CPT with homepage toggle
$query = new WP_Query(array(
    'post_type' => 'practice_area',
    'posts_per_page' => 6,
    'meta_query' => array(
        array(
            'key' => 'show_on_homepage',
            'value' => '1',
            'compare' => '='
        )
    ),
    'meta_key' => 'homepage_order',
    'orderby' => 'meta_value_num',
    'order' => 'ASC',
));

if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        $practice_areas[] = array(
            'icon' => get_field('practice_area_icon') ?: 'fas fa-gavel',
            'title' => get_the_title(),
            'description' => get_field('homepage_description_override') ?: get_field('short_description') ?: get_the_excerpt(),
            'link' => get_permalink(),
        );
    }
    wp_reset_postdata();
}

// Fallback to hard-coded defaults if no CPT posts
if (empty($practice_areas)) {
    $practice_areas = array(
        array(
            'icon' => 'fas fa-gavel',
            'title' => 'Civil Defense Litigation',
            'description' => 'Civil litigation is a legal dispute between two or more parties that seek money damages or specific performance rather than criminal sanctions.',
            'link' => home_url('/civil-defense-litigation/')
        ),
        array(
            'icon' => 'fas fa-shield-alt',
            'title' => 'Consumer Protection',
            'description' => 'Consumer Protection encompasses a large body of laws enacted by the government to protect consumers by regulating business transactions.',
            'link' => home_url('/consumer-protection/')
        ),
        array(
            'icon' => 'fas fa-file-invoice-dollar',
            'title' => 'Bankruptcy Law',
            'description' => 'Bankruptcy laws help people who can no longer pay their creditors get a fresh start – by liquidating assets to pay their debts or by creating a repayment plan.',
            'link' => home_url('/bankruptcy-law/')
        ),
        array(
            'icon' => 'fas fa-handshake',
            'title' => 'Contract Law',
            'description' => 'Contract law includes the concepts of formation, offer, acceptance, and consideration; performance and excuse for nonperformance; breach and damages.',
            'link' => home_url('/contract-law/')
        ),
        array(
            'icon' => 'fas fa-home',
            'title' => 'Family Law',
            'description' => 'Family law covers rules for living together, prenuptial agreements, marriage, divorce, alimony, and mediation, along with laws on domestic violence.',
            'link' => home_url('/family-law/')
        ),
        array(
            'icon' => 'fas fa-building',
            'title' => 'Real Estate Law',
            'description' => 'Property disputes and real estate transactions. Commercial real estate transactions are typically more complex than residential transactions.',
            'link' => home_url('/real-estate-law/')
        ),
    );
}
?>

<section class="practice-areas-section" id="practice-areas">
    <div class="container">
        <div class="section-header">
            <h2><?php echo esc_html($section_title); ?></h2>
            <p><?php echo esc_html($section_subtitle); ?></p>
        </div>

        <div class="practice-areas-grid">
            <?php foreach ($practice_areas as $area) : ?>
            <div class="practice-area-card">
                <div class="card-icon">
                    <i class="<?php echo esc_attr($area['icon']); ?>"></i>
                </div>
                <h3><?php echo esc_html($area['title']); ?></h3>
                <p><?php echo esc_html($area['description']); ?></p>
                <a href="<?php echo esc_url($area['link']); ?>" class="card-link">
                    Learn More <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
