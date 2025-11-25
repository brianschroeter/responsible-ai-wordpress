<?php
/**
 * SEO Schema Markup for MyDefenseLaw
 *
 * Implements JSON-LD structured data for:
 * - LocalBusiness (Law Firm)
 * - Attorney profiles
 * - LegalService offerings
 * - Service Area (Boca Raton, Palm Beach County, South Florida)
 * - Organization
 * - BreadcrumbList
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get firm contact information from theme settings
 */
function mydefenselaw_get_firm_info() {
    return array(
        'name'           => get_bloginfo('name') ?: 'My Defense Law',
        'legal_name'     => 'My Defense Law, P.A.',
        'description'    => 'Experienced criminal defense attorneys serving Boca Raton, FL and Palm Beach County. Available 24/7 for DUI, drug crimes, domestic violence, and all criminal defense matters.',
        'phone'          => get_theme_mod('contact_phone', '888.444.0253'),
        'email'          => get_theme_mod('contact_email', 'info@mydefenselaw.com'),
        'address'        => array(
            'street'     => get_theme_mod('contact_address_street', '1515 N Federal Hwy'),
            'suite'      => get_theme_mod('contact_address_suite', 'Suite 300'),
            'city'       => 'Boca Raton',
            'state'      => 'FL',
            'state_full' => 'Florida',
            'zip'        => '33432',
            'country'    => 'US',
        ),
        'geo'            => array(
            'latitude'   => '26.3683',
            'longitude'  => '-80.0831',
        ),
        'hours'          => 'Mo-Fr 08:00-18:00, Sa-Su by appointment',
        'price_range'    => '$$',
        'founding_year'  => '2010',
        'url'            => home_url('/'),
        'logo'           => get_template_directory_uri() . '/assets/images/logo.png',
        'social'         => array(
            'facebook'   => get_theme_mod('social_facebook', ''),
            'twitter'    => get_theme_mod('social_twitter', ''),
            'linkedin'   => get_theme_mod('social_linkedin', ''),
            'instagram'  => get_theme_mod('social_instagram', ''),
            'youtube'    => get_theme_mod('social_youtube', ''),
        ),
        'service_areas'  => array(
            'Boca Raton',
            'Delray Beach',
            'Boynton Beach',
            'Palm Beach Gardens',
            'West Palm Beach',
            'Jupiter',
            'Wellington',
            'Lake Worth',
            'Pompano Beach',
            'Fort Lauderdale',
        ),
        'practice_areas' => array(
            'Criminal Defense',
            'DUI Defense',
            'Drug Crimes',
            'Domestic Violence',
            'Assault & Battery',
            'Theft Crimes',
            'White Collar Crimes',
            'Federal Crimes',
            'Juvenile Defense',
            'Expungement',
            'Traffic Violations',
            'Sex Crimes Defense',
        ),
    );
}

/**
 * Output Organization Schema
 */
function mydefenselaw_schema_organization() {
    $firm = mydefenselaw_get_firm_info();
    $phone_raw = preg_replace('/[^0-9]/', '', $firm['phone']);

    $schema = array(
        '@context'    => 'https://schema.org',
        '@type'       => 'Organization',
        '@id'         => $firm['url'] . '#organization',
        'name'        => $firm['name'],
        'legalName'   => $firm['legal_name'],
        'description' => $firm['description'],
        'url'         => $firm['url'],
        'logo'        => array(
            '@type'  => 'ImageObject',
            'url'    => $firm['logo'],
            'width'  => 200,
            'height' => 80,
        ),
        'image'       => $firm['logo'],
        'telephone'   => '+1-' . $phone_raw,
        'email'       => $firm['email'],
        'address'     => array(
            '@type'           => 'PostalAddress',
            'streetAddress'   => $firm['address']['street'] . ' ' . $firm['address']['suite'],
            'addressLocality' => $firm['address']['city'],
            'addressRegion'   => $firm['address']['state'],
            'postalCode'      => $firm['address']['zip'],
            'addressCountry'  => $firm['address']['country'],
        ),
        'geo'         => array(
            '@type'     => 'GeoCoordinates',
            'latitude'  => $firm['geo']['latitude'],
            'longitude' => $firm['geo']['longitude'],
        ),
        'foundingDate' => $firm['founding_year'],
        'areaServed'  => array_map(function($area) {
            return array(
                '@type' => 'City',
                'name'  => $area . ', FL',
            );
        }, $firm['service_areas']),
    );

    // Add social profiles if available
    $social_profiles = array_filter($firm['social']);
    if (!empty($social_profiles)) {
        $schema['sameAs'] = array_values($social_profiles);
    }

    return $schema;
}

/**
 * Output LocalBusiness Schema (LegalService / Attorney)
 */
function mydefenselaw_schema_local_business() {
    $firm = mydefenselaw_get_firm_info();
    $phone_raw = preg_replace('/[^0-9]/', '', $firm['phone']);

    $schema = array(
        '@context'         => 'https://schema.org',
        '@type'            => array('LegalService', 'LocalBusiness', 'Attorney'),
        '@id'              => $firm['url'] . '#localbusiness',
        'name'             => $firm['name'],
        'description'      => $firm['description'],
        'url'              => $firm['url'],
        'logo'             => $firm['logo'],
        'image'            => $firm['logo'],
        'telephone'        => '+1-' . $phone_raw,
        'email'            => $firm['email'],
        'priceRange'       => $firm['price_range'],
        'currenciesAccepted' => 'USD',
        'paymentAccepted'  => 'Cash, Credit Card, Check',
        'address'          => array(
            '@type'           => 'PostalAddress',
            'streetAddress'   => $firm['address']['street'] . ' ' . $firm['address']['suite'],
            'addressLocality' => $firm['address']['city'],
            'addressRegion'   => $firm['address']['state'],
            'postalCode'      => $firm['address']['zip'],
            'addressCountry'  => $firm['address']['country'],
        ),
        'geo'              => array(
            '@type'     => 'GeoCoordinates',
            'latitude'  => $firm['geo']['latitude'],
            'longitude' => $firm['geo']['longitude'],
        ),
        'hasMap'           => 'https://www.google.com/maps?q=' . urlencode($firm['address']['street'] . ', ' . $firm['address']['city'] . ', ' . $firm['address']['state'] . ' ' . $firm['address']['zip']),
        'openingHours'     => $firm['hours'],
        'openingHoursSpecification' => array(
            array(
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'),
                'opens'     => '08:00',
                'closes'    => '18:00',
            ),
            array(
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => array('Saturday', 'Sunday'),
                'opens'     => '00:00',
                'closes'    => '00:00',
                'description' => 'By appointment only',
            ),
        ),
        'areaServed'       => array(
            array(
                '@type' => 'City',
                'name'  => 'Boca Raton',
                'containedInPlace' => array(
                    '@type' => 'AdministrativeArea',
                    'name'  => 'Palm Beach County, Florida',
                ),
            ),
            array(
                '@type' => 'AdministrativeArea',
                'name'  => 'Palm Beach County, Florida',
            ),
            array(
                '@type' => 'AdministrativeArea',
                'name'  => 'Broward County, Florida',
            ),
            array(
                '@type' => 'State',
                'name'  => 'Florida',
            ),
        ),
        'knowsAbout'       => $firm['practice_areas'],
        'slogan'           => 'Aggressive Criminal Defense When You Need It Most',
        'hasOfferCatalog'  => array(
            '@type' => 'OfferCatalog',
            'name'  => 'Criminal Defense Legal Services',
            'itemListElement' => array_map(function($service, $index) use ($firm) {
                return array(
                    '@type'    => 'Offer',
                    'position' => $index + 1,
                    'itemOffered' => array(
                        '@type'       => 'Service',
                        'name'        => $service,
                        'description' => $service . ' legal services in Boca Raton, FL and South Florida',
                        'provider'    => array(
                            '@type' => 'LegalService',
                            'name'  => $firm['name'],
                        ),
                        'areaServed'  => array(
                            '@type' => 'City',
                            'name'  => 'Boca Raton, FL',
                        ),
                    ),
                );
            }, $firm['practice_areas'], array_keys($firm['practice_areas'])),
        ),
    );

    // Add aggregate rating if we have testimonials
    $testimonials_count = wp_count_posts('testimonial');
    if ($testimonials_count && $testimonials_count->publish > 0) {
        $schema['aggregateRating'] = array(
            '@type'       => 'AggregateRating',
            'ratingValue' => '4.9',
            'bestRating'  => '5',
            'worstRating' => '1',
            'ratingCount' => $testimonials_count->publish,
            'reviewCount' => $testimonials_count->publish,
        );
    }

    return $schema;
}

/**
 * Output Attorney Schema for individual attorney pages
 */
function mydefenselaw_schema_attorney($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    if (get_post_type($post_id) !== 'attorney') {
        return null;
    }

    $firm = mydefenselaw_get_firm_info();

    // Get attorney ACF fields
    $name = get_the_title($post_id);
    $bio = get_field('attorney_bio', $post_id);
    $title = get_field('attorney_title', $post_id) ?: 'Attorney at Law';
    $education = get_field('attorney_education', $post_id);
    $bar_admissions = get_field('attorney_bar_admissions', $post_id);
    $photo = get_field('attorney_photo', $post_id);
    $photo_url = $photo ? $photo['url'] : $firm['logo'];

    $schema = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'Attorney',
        '@id'             => get_permalink($post_id) . '#attorney',
        'name'            => $name,
        'jobTitle'        => $title,
        'description'     => wp_trim_words(wp_strip_all_tags($bio), 50),
        'image'           => $photo_url,
        'url'             => get_permalink($post_id),
        'telephone'       => '+1-' . preg_replace('/[^0-9]/', '', $firm['phone']),
        'email'           => $firm['email'],
        'address'         => array(
            '@type'           => 'PostalAddress',
            'streetAddress'   => $firm['address']['street'] . ' ' . $firm['address']['suite'],
            'addressLocality' => $firm['address']['city'],
            'addressRegion'   => $firm['address']['state'],
            'postalCode'      => $firm['address']['zip'],
            'addressCountry'  => $firm['address']['country'],
        ),
        'worksFor'        => array(
            '@type' => 'LegalService',
            '@id'   => $firm['url'] . '#localbusiness',
            'name'  => $firm['name'],
        ),
        'knowsAbout'      => $firm['practice_areas'],
        'areaServed'      => array(
            '@type' => 'City',
            'name'  => 'Boca Raton, FL',
        ),
    );

    // Add education if available
    if ($education) {
        $schema['alumniOf'] = array(
            '@type' => 'EducationalOrganization',
            'name'  => $education,
        );
    }

    return $schema;
}

/**
 * Output Practice Area Schema
 */
function mydefenselaw_schema_practice_area($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    if (get_post_type($post_id) !== 'practice_area') {
        return null;
    }

    $firm = mydefenselaw_get_firm_info();
    $title = get_the_title($post_id);
    $description = get_field('practice_area_description', $post_id) ?: get_the_excerpt($post_id);

    $schema = array(
        '@context'    => 'https://schema.org',
        '@type'       => 'Service',
        '@id'         => get_permalink($post_id) . '#service',
        'name'        => $title . ' Attorney Boca Raton',
        'description' => wp_trim_words(wp_strip_all_tags($description), 75) . ' Our Boca Raton criminal defense attorneys provide aggressive legal representation.',
        'url'         => get_permalink($post_id),
        'provider'    => array(
            '@type' => 'LegalService',
            '@id'   => $firm['url'] . '#localbusiness',
            'name'  => $firm['name'],
        ),
        'areaServed'  => array(
            array(
                '@type' => 'City',
                'name'  => 'Boca Raton, FL',
            ),
            array(
                '@type' => 'AdministrativeArea',
                'name'  => 'Palm Beach County, Florida',
            ),
        ),
        'serviceType' => $title,
    );

    return $schema;
}

/**
 * Output Breadcrumb Schema
 */
function mydefenselaw_schema_breadcrumb() {
    if (is_front_page()) {
        return null;
    }

    $breadcrumbs = array();
    $position = 1;

    // Home
    $breadcrumbs[] = array(
        '@type'    => 'ListItem',
        'position' => $position++,
        'name'     => 'Home',
        'item'     => home_url('/'),
    );

    // Practice Areas archive
    if (is_post_type_archive('practice_area') || is_singular('practice_area')) {
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => 'Practice Areas',
            'item'     => get_post_type_archive_link('practice_area'),
        );
    }

    // Attorneys archive
    if (is_post_type_archive('attorney') || is_singular('attorney')) {
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => 'Our Attorneys',
            'item'     => get_post_type_archive_link('attorney'),
        );
    }

    // Single post/page
    if (is_singular()) {
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        );
    }

    // Archive pages
    if (is_archive() && !is_singular()) {
        $breadcrumbs[] = array(
            '@type'    => 'ListItem',
            'position' => $position++,
            'name'     => get_the_archive_title(),
            'item'     => get_pagenum_link(1),
        );
    }

    $schema = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $breadcrumbs,
    );

    return $schema;
}

/**
 * Output WebSite Schema with SearchAction
 */
function mydefenselaw_schema_website() {
    $schema = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'WebSite',
        '@id'             => home_url('/') . '#website',
        'name'            => get_bloginfo('name'),
        'description'     => get_bloginfo('description'),
        'url'             => home_url('/'),
        'inLanguage'      => 'en-US',
        'publisher'       => array(
            '@type' => 'Organization',
            '@id'   => home_url('/') . '#organization',
        ),
        'potentialAction' => array(
            '@type'       => 'SearchAction',
            'target'      => array(
                '@type'       => 'EntryPoint',
                'urlTemplate' => home_url('/') . '?s={search_term_string}',
            ),
            'query-input' => 'required name=search_term_string',
        ),
    );

    return $schema;
}

/**
 * Output FAQ Schema (for FAQ pages)
 */
function mydefenselaw_schema_faq($faqs = array()) {
    if (empty($faqs)) {
        return null;
    }

    $schema = array(
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => array_map(function($faq) {
            return array(
                '@type'          => 'Question',
                'name'           => $faq['question'],
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text'  => $faq['answer'],
                ),
            );
        }, $faqs),
    );

    return $schema;
}

/**
 * Main function to output all schema markup
 */
function mydefenselaw_output_schema() {
    $schemas = array();

    // Always include these schemas
    $schemas[] = mydefenselaw_schema_organization();
    $schemas[] = mydefenselaw_schema_website();

    // Include LocalBusiness on homepage and contact pages
    if (is_front_page() || is_page(array('contact', 'about'))) {
        $schemas[] = mydefenselaw_schema_local_business();
    }

    // Include breadcrumbs except on homepage
    $breadcrumb = mydefenselaw_schema_breadcrumb();
    if ($breadcrumb) {
        $schemas[] = $breadcrumb;
    }

    // Include attorney schema on attorney pages
    if (is_singular('attorney')) {
        $attorney_schema = mydefenselaw_schema_attorney();
        if ($attorney_schema) {
            $schemas[] = $attorney_schema;
        }
    }

    // Include practice area schema on practice area pages
    if (is_singular('practice_area')) {
        $practice_schema = mydefenselaw_schema_practice_area();
        if ($practice_schema) {
            $schemas[] = $practice_schema;
        }
    }

    // Output schemas
    foreach ($schemas as $schema) {
        if ($schema) {
            echo '<script type="application/ld+json">' . PHP_EOL;
            echo wp_json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            echo PHP_EOL . '</script>' . PHP_EOL;
        }
    }
}
add_action('wp_head', 'mydefenselaw_output_schema', 5);

/**
 * Add Open Graph and Twitter Card meta tags
 */
function mydefenselaw_social_meta_tags() {
    // Skip if Yoast or other SEO plugin handles this
    if (defined('WPSEO_VERSION')) {
        return;
    }

    $firm = mydefenselaw_get_firm_info();

    // Default values
    $og_title = get_bloginfo('name') . ' | Criminal Defense Attorneys Boca Raton, FL';
    $og_description = $firm['description'];
    $og_image = $firm['logo'];
    $og_url = home_url('/');
    $og_type = 'website';

    // Customize for single posts/pages
    if (is_singular()) {
        $og_title = get_the_title() . ' | ' . get_bloginfo('name');
        $og_description = get_the_excerpt() ?: wp_trim_words(get_the_content(), 30);
        $og_url = get_permalink();
        $og_type = 'article';

        if (has_post_thumbnail()) {
            $og_image = get_the_post_thumbnail_url(get_the_ID(), 'large');
        }
    }

    // Practice area pages
    if (is_singular('practice_area')) {
        $og_title = get_the_title() . ' Attorney | ' . $firm['address']['city'] . ', ' . $firm['address']['state'];
    }

    // Attorney pages
    if (is_singular('attorney')) {
        $photo = get_field('attorney_photo');
        if ($photo) {
            $og_image = $photo['url'];
        }
    }

    // Output Open Graph tags
    echo '<meta property="og:locale" content="en_US" />' . PHP_EOL;
    echo '<meta property="og:type" content="' . esc_attr($og_type) . '" />' . PHP_EOL;
    echo '<meta property="og:title" content="' . esc_attr($og_title) . '" />' . PHP_EOL;
    echo '<meta property="og:description" content="' . esc_attr($og_description) . '" />' . PHP_EOL;
    echo '<meta property="og:url" content="' . esc_url($og_url) . '" />' . PHP_EOL;
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '" />' . PHP_EOL;
    echo '<meta property="og:image" content="' . esc_url($og_image) . '" />' . PHP_EOL;

    // Output Twitter Card tags
    echo '<meta name="twitter:card" content="summary_large_image" />' . PHP_EOL;
    echo '<meta name="twitter:title" content="' . esc_attr($og_title) . '" />' . PHP_EOL;
    echo '<meta name="twitter:description" content="' . esc_attr($og_description) . '" />' . PHP_EOL;
    echo '<meta name="twitter:image" content="' . esc_url($og_image) . '" />' . PHP_EOL;
}
add_action('wp_head', 'mydefenselaw_social_meta_tags', 6);

/**
 * Add geo meta tags for local SEO
 */
function mydefenselaw_geo_meta_tags() {
    $firm = mydefenselaw_get_firm_info();

    echo '<meta name="geo.region" content="US-FL" />' . PHP_EOL;
    echo '<meta name="geo.placename" content="' . esc_attr($firm['address']['city']) . '" />' . PHP_EOL;
    echo '<meta name="geo.position" content="' . esc_attr($firm['geo']['latitude']) . ';' . esc_attr($firm['geo']['longitude']) . '" />' . PHP_EOL;
    echo '<meta name="ICBM" content="' . esc_attr($firm['geo']['latitude']) . ', ' . esc_attr($firm['geo']['longitude']) . '" />' . PHP_EOL;
}
add_action('wp_head', 'mydefenselaw_geo_meta_tags', 7);

/**
 * Optimize document title for local SEO
 */
function mydefenselaw_local_seo_title($title) {
    $firm = mydefenselaw_get_firm_info();
    $location = $firm['address']['city'] . ', ' . $firm['address']['state'];

    // Homepage
    if (is_front_page()) {
        return $firm['name'] . ' | Criminal Defense Lawyer ' . $location . ' | Available 24/7';
    }

    // Practice area pages - add location
    if (is_singular('practice_area')) {
        $title['title'] = get_the_title() . ' Lawyer ' . $location;
    }

    // Practice areas archive
    if (is_post_type_archive('practice_area')) {
        $title['title'] = 'Criminal Defense Practice Areas | ' . $location;
    }

    // Attorney pages
    if (is_singular('attorney')) {
        $attorney_title = get_field('attorney_title') ?: 'Criminal Defense Attorney';
        $title['title'] = get_the_title() . ' | ' . $attorney_title . ' ' . $location;
    }

    // Attorneys archive
    if (is_post_type_archive('attorney')) {
        $title['title'] = 'Criminal Defense Attorneys | ' . $firm['name'] . ' | ' . $location;
    }

    // Contact page
    if (is_page('contact')) {
        $title['title'] = 'Contact Us | Free Consultation | Criminal Defense Lawyer ' . $location;
    }

    // About page
    if (is_page('about')) {
        $title['title'] = 'About Our Firm | ' . $firm['name'] . ' | ' . $location;
    }

    return $title;
}
add_filter('document_title_parts', 'mydefenselaw_local_seo_title', 20);

/**
 * Add canonical URL if not handled by SEO plugin
 */
function mydefenselaw_canonical_url() {
    // Skip if Yoast handles this
    if (defined('WPSEO_VERSION')) {
        return;
    }

    if (is_singular()) {
        $canonical = get_permalink();
    } elseif (is_front_page()) {
        $canonical = home_url('/');
    } elseif (is_home()) {
        $canonical = get_permalink(get_option('page_for_posts'));
    } elseif (is_archive()) {
        $canonical = get_pagenum_link(1);
    } else {
        $canonical = home_url(add_query_arg(array()));
    }

    echo '<link rel="canonical" href="' . esc_url($canonical) . '" />' . PHP_EOL;
}
add_action('wp_head', 'mydefenselaw_canonical_url', 8);
