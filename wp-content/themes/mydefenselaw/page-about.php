<?php
/**
 * Template Name: About The Firm
 *
 * Custom page template for the About The Firm page
 * Content is managed via ACF fields with section visibility toggles.
 * Matches the live site at mydefenselaw.com/about_the_firm.php
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

get_header();

// Get ACF fields with defaults
$about = mydefenselaw_get_about_fields();

// Section visibility toggles (default to showing if field not set)
$show_intro = get_field('enable_about_intro') !== false;
$show_mission = get_field('enable_about_mission') !== false;
$show_attorneys = get_field('enable_about_attorneys') !== false;
$show_passion = get_field('enable_about_passion') !== false;
$show_cta = get_field('enable_about_cta') !== false;
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

        <div class="about-section">

            <?php if ($show_intro) : ?>
            <!-- Intro Section -->
            <h2><?php echo esc_html($about['intro_title']); ?></h2>
            <?php echo wp_kses_post($about['intro_content']); ?>
            <?php endif; ?>

            <?php if ($show_mission) : ?>
            <!-- Mission Statement -->
            <h3><?php echo esc_html($about['mission_title']); ?></h3>
            <p><?php echo esc_html($about['mission_intro']); ?></p>

            <?php if (!empty($about['mission_principles'])) : ?>
            <ul>
                <?php foreach ($about['mission_principles'] as $principle) : ?>
                <li><strong><?php echo esc_html($principle['principle_title']); ?></strong> - <?php echo esc_html($principle['principle_description']); ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            <?php endif; ?>

            <?php if ($show_attorneys) : ?>
            <!-- Meet The Attorneys -->
            <h3 id="attorneys"><?php echo esc_html($about['attorneys_title']); ?></h3>

            <?php if (!empty($about['attorneys'])) : ?>
                <?php foreach ($about['attorneys'] as $attorney) : ?>
                <div class="attorney">
                    <h4><?php echo esc_html($attorney['name']); ?><?php if (!empty($attorney['bar_admissions'])) : ?> - <?php echo esc_html($attorney['bar_admissions']); ?><?php endif; ?></h4>
                    <p><?php echo esc_html($attorney['short_bio']); ?></p>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php endif; ?>

            <?php if ($show_passion) : ?>
            <!-- Passion Section -->
            <h3><?php echo esc_html($about['passion_title']); ?></h3>
            <p><?php echo esc_html($about['passion_content']); ?></p>
            <?php endif; ?>

            <?php if ($show_cta) : ?>
            <!-- CTA Section -->
            <div class="cta-section" style="margin-top: 3rem;">
                <h3><?php echo esc_html($about['cta_title']); ?></h3>
                <p><?php echo esc_html($about['cta_tagline']); ?></p>
                <div class="cta-buttons">
                    <a href="tel:<?php echo esc_attr($about['phone_raw']); ?>" class="btn btn-primary">
                        <i class="fas fa-phone" aria-hidden="true"></i> Call <?php echo esc_html($about['phone']); ?>
                    </a>
                    <button class="btn btn-outline consultation-trigger" id="modalTriggerAbout" aria-label="Request a free legal consultation">
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
