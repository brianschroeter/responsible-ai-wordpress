<?php
/**
 * Hero Section Template Part
 * Pixel-perfect match of source site
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

// Get hero data from ACF helper function
$hero = mydefenselaw_get_hero_fields();
$phone = mydefenselaw_get_primary_phone();
$phone_raw = preg_replace('/[^0-9]/', '', $phone);

// Get highlight text and apply it to title
$highlight_text = '';
if (function_exists('get_field')) {
    $front_page_id = get_option('page_on_front');
    $highlight_text = get_field('hero_title_highlight', $front_page_id);
}

// Apply highlight span to title if highlight text is set
$hero_title = $hero['title'];
if (!empty($highlight_text) && strpos($hero_title, $highlight_text) !== false) {
    $hero_title = str_replace(
        $highlight_text,
        '<span class="title-emphasis">' . esc_html($highlight_text) . '</span>',
        $hero_title
    );
}

// Background with default
$hero_bg = !empty($hero['background']) ? $hero['background'] : 'https://images.unsplash.com/photo-1479142506502-19b3a3b7ff33?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80';

// Stats with defaults
$stats = !empty($hero['stats']) ? $hero['stats'] : array(
    array('number' => '1000+', 'label' => 'Cases Won'),
    array('number' => '25+', 'label' => 'Years Experience'),
    array('number' => '24/7', 'label' => 'Available'),
);

// Guarantees with defaults
$guarantees = !empty($hero['guarantees']) ? $hero['guarantees'] : array(
    array('icon' => 'fas fa-lock', 'text' => 'Confidential'),
    array('icon' => 'fas fa-handshake', 'text' => 'Experienced Legal Team'),
    array('icon' => 'fas fa-clock', 'text' => 'Quick Response'),
);
?>

<section class="hero" id="home">
    <div class="hero-background">
        <img src="<?php echo esc_url($hero_bg); ?>" alt="Courthouse steps" class="hero-bg-image">
        <div class="hero-overlay"></div>
    </div>

    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="fas fa-award"></i>
                <span><?php echo esc_html(function_exists('get_field') && get_field('hero_badge', 'option') ? get_field('hero_badge', 'option') : '25+ Years of Experience'); ?></span>
            </div>

            <h1 class="hero-title">
                <?php echo wp_kses_post($hero_title); ?>
            </h1>

            <p class="hero-description">
                <?php echo esc_html($hero['subtitle']); ?>
            </p>

            <div class="hero-stats">
                <?php foreach ($stats as $stat) : ?>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo esc_html($stat['number']); ?></div>
                        <div class="stat-label"><?php echo esc_html($stat['label']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="hero-actions">
                <a href="tel:<?php echo esc_attr($phone_raw); ?>" class="btn btn-primary">
                    <i class="fas fa-phone"></i>
                    <?php echo esc_html($hero['cta_phone_text']); ?>
                </a>
                <a href="<?php echo esc_url(home_url('/free-consultation/')); ?>" class="btn btn-secondary consultation-trigger">
                    <i class="fas fa-calendar-check"></i>
                    <?php echo esc_html($hero['cta_text']); ?>
                </a>
            </div>

            <div class="hero-guarantees">
                <?php foreach ($guarantees as $guarantee) : ?>
                    <div class="guarantee-item">
                        <i class="<?php echo esc_attr($guarantee['icon']); ?>"></i>
                        <span><?php echo esc_html($guarantee['text']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
