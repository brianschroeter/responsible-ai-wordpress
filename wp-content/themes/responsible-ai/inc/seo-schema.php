<?php
/**
 * SEO Schema Markup (JSON-LD)
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Output Organization Schema
 */
function responsibleai_organization_schema() {
    $org_info = responsibleai_get_organization_info();
    $social = responsibleai_get_social_links();

    $schema = array(
        '@context'       => 'https://schema.org',
        '@type'          => 'Organization',
        'name'           => $org_info['name'],
        'url'            => home_url('/'),
        'logo'           => get_template_directory_uri() . '/assets/images/logo.svg',
        'description'    => $org_info['tagline'],
        'foundingDate'   => '2019',
        'sameAs'         => array_values(array_filter($social)),
    );

    if (!empty($org_info['email'])) {
        $schema['email'] = $org_info['email'];
    }

    if (!empty($org_info['address'])) {
        $schema['address'] = array(
            '@type'           => 'PostalAddress',
            'streetAddress'   => $org_info['address'],
        );
    }

    // Output schema
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}
add_action('wp_head', 'responsibleai_organization_schema', 5);

/**
 * Output WebSite Schema
 */
function responsibleai_website_schema() {
    $schema = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'WebSite',
        'name'            => get_bloginfo('name'),
        'url'             => home_url('/'),
        'potentialAction' => array(
            '@type'       => 'SearchAction',
            'target'      => home_url('/?s={search_term_string}'),
            'query-input' => 'required name=search_term_string',
        ),
    );

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}
add_action('wp_head', 'responsibleai_website_schema', 5);

/**
 * Output BreadcrumbList Schema
 */
function responsibleai_breadcrumb_schema() {
    if (is_front_page()) {
        return;
    }

    $breadcrumbs = array();
    $position = 1;

    // Home
    $breadcrumbs[] = array(
        '@type'    => 'ListItem',
        'position' => $position++,
        'name'     => __('Home', 'responsible-ai'),
        'item'     => home_url('/'),
    );

    // Build breadcrumb based on page type
    if (is_singular('rai_event')) {
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => __('Events', 'responsible-ai'),
            'item'     => get_post_type_archive_link('rai_event'),
        );
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        );
    } elseif (is_singular('rai_resource')) {
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => __('Resources', 'responsible-ai'),
            'item'     => get_post_type_archive_link('rai_resource'),
        );
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        );
    } elseif (is_singular('case_study')) {
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => __('Case Studies', 'responsible-ai'),
            'item'     => get_post_type_archive_link('case_study'),
        );
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        );
    } elseif (is_page()) {
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        );
    } elseif (is_single()) {
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => __('Blog', 'responsible-ai'),
            'item'     => home_url('/blog/'),
        );
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        );
    }

    if (count($breadcrumbs) > 1) {
        $schema = array(
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $breadcrumbs,
        );

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
    }
}
add_action('wp_head', 'responsibleai_breadcrumb_schema', 5);

/**
 * Output Event Schema for single events
 */
function responsibleai_event_schema() {
    if (!is_singular('rai_event')) {
        return;
    }

    $event_date = get_field('event_date');
    $event_end = get_field('event_end_date');
    $location = get_field('event_location');
    $format = get_field('event_format');
    $reg_url = get_field('registration_url');

    if (empty($event_date)) {
        return;
    }

    $schema = array(
        '@context'    => 'https://schema.org',
        '@type'       => 'Event',
        'name'        => get_the_title(),
        'description' => get_the_excerpt(),
        'startDate'   => date('c', strtotime($event_date)),
        'url'         => get_permalink(),
        'image'       => get_the_post_thumbnail_url(get_the_ID(), 'full'),
        'organizer'   => array(
            '@type' => 'Organization',
            'name'  => get_bloginfo('name'),
            'url'   => home_url('/'),
        ),
    );

    if ($event_end) {
        $schema['endDate'] = date('c', strtotime($event_end));
    }

    // Event attendance mode
    if ($format === 'Virtual') {
        $schema['eventAttendanceMode'] = 'https://schema.org/OnlineEventAttendanceMode';
        $schema['location'] = array(
            '@type' => 'VirtualLocation',
            'url'   => $reg_url ?: get_permalink(),
        );
    } elseif ($format === 'In-Person' && $location) {
        $schema['eventAttendanceMode'] = 'https://schema.org/OfflineEventAttendanceMode';
        $schema['location'] = array(
            '@type'   => 'Place',
            'name'    => $location,
            'address' => $location,
        );
    } elseif ($format === 'Hybrid') {
        $schema['eventAttendanceMode'] = 'https://schema.org/MixedEventAttendanceMode';
    }

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}
add_action('wp_head', 'responsibleai_event_schema', 5);
