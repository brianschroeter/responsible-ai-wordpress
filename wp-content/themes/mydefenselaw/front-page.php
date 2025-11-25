<?php
/**
 * Homepage Template
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" class="site-main front-page">
    <?php
    // Get section visibility settings (with backward-compatible defaults)
    $show_hero = get_field('enable_hero') !== false; // Default: true
    $show_practice_areas = get_field('enable_practice_areas') !== false; // Default: true
    $show_attorneys_section = get_field('enable_attorneys_section') === true; // Default: false (matches live site)
    $show_why_choose = get_field('enable_why_choose_us') !== false; // Default: true
    $show_contact = get_field('enable_contact_form') !== false; // Default: true

    // Hero Section
    if ($show_hero) {
        get_template_part('template-parts/sections/hero');
    }

    // Practice Areas Section
    if ($show_practice_areas) {
        get_template_part('template-parts/sections/practice-areas');
    }

    // Meet the Attorneys Section (dedicated section - typically disabled to match live site)
    if ($show_attorneys_section) {
        get_template_part('template-parts/sections/attorneys');
    }

    // Why Choose Us Section (includes attorney sidebar)
    if ($show_why_choose) {
        get_template_part('template-parts/sections/why-choose-us');
    }

    // Contact Section
    if ($show_contact) {
        get_template_part('template-parts/sections/contact');
    }
    ?>
</main>

<?php
get_footer();
