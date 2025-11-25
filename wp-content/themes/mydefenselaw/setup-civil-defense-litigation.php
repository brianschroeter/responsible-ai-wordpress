<?php
/**
 * Setup Civil Defense Litigation Practice Area
 *
 * Run with: make wp cmd="eval-file wp-content/themes/mydefenselaw/setup-civil-defense-litigation.php"
 *
 * @package MyDefenseLaw
 */

// Ensure this is being run via WP-CLI
if (!defined('WP_CLI')) {
    exit('This script must be run via WP-CLI');
}

// Civil Defense Litigation content (matching the live site)
$title = 'Civil Defense Litigation';
$slug = 'civil-defense-litigation';

// Check if post already exists
$existing = get_posts(array(
    'post_type' => 'practice_area',
    'name' => $slug,
    'posts_per_page' => 1,
));

if (!empty($existing)) {
    $post_id = $existing[0]->ID;
    WP_CLI::log("Found existing Civil Defense Litigation post (ID: {$post_id}). Updating...");
} else {
    // Create new practice area post
    $post_id = wp_insert_post(array(
        'post_title' => $title,
        'post_name' => $slug,
        'post_type' => 'practice_area',
        'post_status' => 'publish',
        'menu_order' => 1,
    ));

    if (is_wp_error($post_id)) {
        WP_CLI::error("Failed to create post: " . $post_id->get_error_message());
    }

    WP_CLI::log("Created new Civil Defense Litigation post (ID: {$post_id})");
}

// Page content HTML (matching the live site structure)
$page_content = '<p>Civil defense litigation is the representation of defendants in civil legal disputes, covering everything from contract disagreements to personal injury claims. Defense attorneys guide their clients through every stage of litigation—from initial pleadings to trial or settlement—with the goal of protecting their rights and achieving the best possible outcome. In a world where lawsuits can arise unexpectedly, having knowledgeable counsel can mean the difference between a manageable resolution and a costly legal battle.</p>

<p>At Defense Lawyers, P.A., we understand that facing a lawsuit is stressful and uncertain. That\'s why we approach each case with a commitment to clear communication, thorough preparation, and strategic advocacy. Our attorneys are well-versed in diverse areas of civil law and have helped clients navigate complex disputes with professionalism and tenacity.</p>

<h3>Our Expertise</h3>

<p>Our experienced attorneys provide defense services in a wide range of civil matters, including:</p>

<ul>
    <li>Contract disputes and breach of contract claims</li>
    <li>Business and commercial litigation</li>
    <li>Personal injury defense</li>
    <li>Professional liability and malpractice defense</li>
    <li>Real estate and property disputes</li>
    <li>Employment and labor law disputes</li>
    <li>Consumer protection litigation</li>
    <li>Insurance defense</li>
</ul>

<p>If you or your business is facing civil litigation, don\'t wait to seek legal counsel. <a href="/contact/">Contact Defense Lawyers, P.A. today</a> for a free consultation and let us help you build a strong defense.</p>';

// Short description for listing pages
$short_description = 'Expert representation in civil legal disputes including contract disagreements, business litigation, and personal injury defense.';

// Update ACF fields
if (function_exists('update_field')) {
    // Icon class
    update_field('practice_area_icon', 'fas fa-balance-scale', $post_id);

    // Short description
    update_field('short_description', $short_description, $post_id);

    // Page content (the main WYSIWYG field)
    update_field('page_content', $page_content, $post_id);

    // CTA fields
    update_field('cta_title', 'Need Civil Defense Representation?', $post_id);
    update_field('cta_text', 'Our experienced attorneys are ready to defend your rights. Contact us today for a free consultation.', $post_id);
    update_field('cta_button_text', 'Schedule Free Consultation', $post_id);

    // Homepage display
    update_field('show_on_homepage', true, $post_id);
    update_field('homepage_order', 1, $post_id);

    WP_CLI::success("ACF fields updated successfully!");
} else {
    WP_CLI::warning("ACF not available. Post created but fields not populated.");
}

// Output the permalink
$permalink = get_permalink($post_id);
WP_CLI::success("Civil Defense Litigation page is now available at: {$permalink}");
