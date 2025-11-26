<?php
/**
 * Front Page Template (Homepage)
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get homepage fields from ACF
$fields = responsibleai_get_homepage_fields();

get_header();
?>

<?php
// Hero Section
if ($fields['enable_hero']) {
    get_template_part('template-parts/sections/hero-carousel');
}
?>

<?php
// Mission Section
if ($fields['enable_mission']) {
    get_template_part('template-parts/sections/mission-vision');
}
?>

<?php
// Stats Section
if ($fields['enable_stats']) {
    get_template_part('template-parts/sections/stats-counter');
}
?>

<?php
// RAISE Pathways Section
if ($fields['enable_pathways']) {
    get_template_part('template-parts/sections/pathways-grid');
}
?>

<?php
// AI Agents Section (Flip Cards)
if ($fields['enable_agents']) {
    get_template_part('template-parts/sections/ai-agents');
}
?>

<?php
// Partner Logos Section
if ($fields['enable_partners']) {
    get_template_part('template-parts/sections/partner-logos');
}
?>

<?php
// Featured Resources Section
if ($fields['enable_resources']) {
    get_template_part('template-parts/sections/resource-grid');
}
?>

<?php
// Testimonials Section
if ($fields['enable_testimonials']) {
    get_template_part('template-parts/sections/testimonials');
}
?>

<?php
// Latest News Section
if ($fields['enable_news']) {
    get_template_part('template-parts/sections/news-grid');
}
?>

<?php
// Newsletter Section
if ($fields['enable_newsletter']) {
    get_template_part('template-parts/sections/newsletter');
}
?>

<?php
// CTA Section
if ($fields['enable_cta']) {
    get_template_part('template-parts/sections/cta-section');
}
?>

<?php get_footer(); ?>
