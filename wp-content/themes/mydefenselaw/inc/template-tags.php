<?php
/**
 * Custom Template Tags
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

/**
 * Get contact phone number
 */
function mydefenselaw_get_phone() {
    $phone = get_theme_mod('mydefenselaw_phone', '');

    if (function_exists('get_field')) {
        $acf_phone = get_field('phone_number', 'option');
        if ($acf_phone) {
            $phone = $acf_phone;
        }
    }

    return $phone;
}

/**
 * Display formatted phone number
 */
function mydefenselaw_phone() {
    $phone = mydefenselaw_get_phone();

    if (!empty($phone)) {
        $phone_clean = preg_replace('/[^0-9]/', '', $phone);
        printf(
            '<a href="tel:+1%s" class="phone-link">%s</a>',
            esc_attr($phone_clean),
            esc_html($phone)
        );
    }
}

/**
 * Get contact email
 */
function mydefenselaw_get_email() {
    $email = get_theme_mod('mydefenselaw_email', '');

    if (function_exists('get_field')) {
        $acf_email = get_field('email_address', 'option');
        if ($acf_email) {
            $email = $acf_email;
        }
    }

    return $email;
}

/**
 * Display formatted email
 */
function mydefenselaw_email() {
    $email = mydefenselaw_get_email();

    if (!empty($email)) {
        printf(
            '<a href="mailto:%s" class="email-link">%s</a>',
            esc_attr($email),
            esc_html($email)
        );
    }
}

/**
 * Get office address
 */
function mydefenselaw_get_address() {
    $address = get_theme_mod('mydefenselaw_address', '');

    if (function_exists('get_field')) {
        $acf_address = get_field('office_address', 'option');
        if ($acf_address) {
            $address = $acf_address;
        }
    }

    return $address;
}

/**
 * Display office address
 */
function mydefenselaw_address() {
    $address = mydefenselaw_get_address();

    if (!empty($address)) {
        echo '<address class="office-address">' . wp_kses_post(nl2br($address)) . '</address>';
    }
}

/**
 * Display social media links
 */
function mydefenselaw_social_links() {
    $social_links = array(
        'facebook'  => array(
            'url'  => get_theme_mod('mydefenselaw_facebook', ''),
            'icon' => 'fab fa-facebook-f',
            'name' => __('Facebook', 'mydefenselaw'),
        ),
        'twitter'   => array(
            'url'  => get_theme_mod('mydefenselaw_twitter', ''),
            'icon' => 'fab fa-twitter',
            'name' => __('Twitter', 'mydefenselaw'),
        ),
        'linkedin'  => array(
            'url'  => get_theme_mod('mydefenselaw_linkedin', ''),
            'icon' => 'fab fa-linkedin-in',
            'name' => __('LinkedIn', 'mydefenselaw'),
        ),
    );

    // Override with ACF if available
    if (function_exists('get_field')) {
        if ($fb = get_field('facebook_url', 'option')) {
            $social_links['facebook']['url'] = $fb;
        }
        if ($tw = get_field('twitter_url', 'option')) {
            $social_links['twitter']['url'] = $tw;
        }
        if ($li = get_field('linkedin_url', 'option')) {
            $social_links['linkedin']['url'] = $li;
        }
    }

    echo '<ul class="social-links-list">';
    foreach ($social_links as $key => $link) {
        if (!empty($link['url'])) {
            printf(
                '<li class="social-link-item"><a href="%s" target="_blank" rel="noopener noreferrer" class="social-link social-link-%s" aria-label="%s"><i class="%s"></i></a></li>',
                esc_url($link['url']),
                esc_attr($key),
                esc_attr($link['name']),
                esc_attr($link['icon'])
            );
        }
    }
    echo '</ul>';
}

/**
 * Get breadcrumbs
 */
function mydefenselaw_breadcrumbs() {
    if (is_front_page()) {
        return;
    }

    echo '<nav class="breadcrumbs" aria-label="' . esc_attr__('Breadcrumb', 'mydefenselaw') . '">';
    echo '<ol class="breadcrumb-list">';
    echo '<li class="breadcrumb-item"><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'mydefenselaw') . '</a></li>';

    if (is_category() || is_single()) {
        echo '<li class="breadcrumb-item">';
        the_category(' </li><li class="breadcrumb-item"> ');
        echo '</li>';
        if (is_single()) {
            echo '<li class="breadcrumb-item active" aria-current="page">' . get_the_title() . '</li>';
        }
    } elseif (is_page()) {
        echo '<li class="breadcrumb-item active" aria-current="page">' . get_the_title() . '</li>';
    }

    echo '</ol>';
    echo '</nav>';
}

/**
 * Display CTA button
 */
function mydefenselaw_cta_button($text = '', $class = 'btn-primary') {
    if (empty($text)) {
        $text = __('Free Consultation', 'mydefenselaw');
    }

    $phone = mydefenselaw_get_phone();
    $phone_clean = preg_replace('/[^0-9]/', '', $phone);

    if (!empty($phone_clean)) {
        printf(
            '<a href="tel:+1%s" class="btn %s"><i class="fas fa-phone"></i> %s</a>',
            esc_attr($phone_clean),
            esc_attr($class),
            esc_html($text)
        );
    }
}

/* ==========================================================================
   ACF Helper Functions for Content Management
   ========================================================================== */

/**
 * Get primary phone from ACF options (with fallback to customizer)
 */
function mydefenselaw_get_primary_phone() {
    $phone = '888.444.0253'; // Default

    if (function_exists('get_field')) {
        $acf_phone = get_field('phone_primary', 'option');
        if ($acf_phone) {
            return $acf_phone;
        }
    }

    $customizer_phone = get_theme_mod('contact_phone', '');
    if ($customizer_phone) {
        return $customizer_phone;
    }

    return $phone;
}

/**
 * Get footer tagline from ACF options
 */
function mydefenselaw_get_footer_tagline() {
    $default = __('Protecting your rights and fighting for your future for over 25 years.', 'mydefenselaw');

    if (function_exists('get_field')) {
        $tagline = get_field('footer_tagline', 'option');
        if ($tagline) {
            return $tagline;
        }
    }

    return $default;
}

/**
 * Get footer disclaimer from ACF options
 */
function mydefenselaw_get_footer_disclaimer() {
    $default = __('DISCLOSURES: If you have already retained a lawyer, please disregard this communication. The hiring of a lawyer is an important decision that should not be based solely upon advertisements. Before you choose a law firm, take the time to research them with their state\'s Bar Association. You should also ask for written information about their experience and qualifications. Past results are not indicative of future performance. The images throughout this website are not the attorneys that work for Defense Lawyers, P.A. Rather, they are hired models.', 'mydefenselaw');

    if (function_exists('get_field')) {
        $disclaimer = get_field('footer_disclaimer', 'option');
        if ($disclaimer) {
            return $disclaimer;
        }
    }

    return $default;
}

/**
 * Get business hours display text
 */
function mydefenselaw_get_hours() {
    $default = __('Available 24/7', 'mydefenselaw');

    if (function_exists('get_field')) {
        $hours = get_field('hours_display', 'option');
        if ($hours) {
            return $hours;
        }
    }

    return $default;
}

/**
 * Get city/state from ACF options
 */
function mydefenselaw_get_location() {
    $city = 'Boca Raton';
    $state = 'FL';

    if (function_exists('get_field')) {
        $acf_city = get_field('address_city', 'option');
        $acf_state = get_field('address_state', 'option');
        if ($acf_city) $city = $acf_city;
        if ($acf_state) $state = $acf_state;
    }

    return $city . ', ' . $state;
}

/**
 * Get hero section fields
 */
function mydefenselaw_get_hero_fields($post_id = null) {
    $defaults = array(
        'title' => __('Protecting Your Rights. Fighting for Your Future.', 'mydefenselaw'),
        'subtitle' => __('Trusted legal representation in South Florida for over 25 years. Our experienced attorneys are ready to defend your case.', 'mydefenselaw'),
        'background' => '',
        'cta_text' => __('Free Consultation', 'mydefenselaw'),
        'cta_phone_text' => __('Call Now', 'mydefenselaw'),
        'stats' => array(),
        'guarantees' => array(),
    );

    if (!function_exists('get_field')) {
        return $defaults;
    }

    $post_id = $post_id ?: get_the_ID();

    return array(
        'title' => get_field('hero_title', $post_id) ?: $defaults['title'],
        'subtitle' => get_field('hero_subtitle', $post_id) ?: $defaults['subtitle'],
        'background' => get_field('hero_background', $post_id) ?: $defaults['background'],
        'cta_text' => get_field('hero_cta_text', $post_id) ?: $defaults['cta_text'],
        'cta_phone_text' => get_field('hero_cta_phone_text', $post_id) ?: $defaults['cta_phone_text'],
        'stats' => get_field('hero_stats', $post_id) ?: $defaults['stats'],
        'guarantees' => get_field('hero_guarantees', $post_id) ?: $defaults['guarantees'],
    );
}

/**
 * Get practice areas from ACF or CPT
 */
function mydefenselaw_get_practice_areas($source = 'auto', $limit = -1) {
    $areas = array();

    // Try ACF repeater first
    if (($source === 'auto' || $source === 'acf') && function_exists('get_field')) {
        $acf_areas = get_field('practice_areas', get_option('page_on_front'));
        if ($acf_areas && is_array($acf_areas)) {
            foreach ($acf_areas as $area) {
                $areas[] = array(
                    'title' => $area['title'] ?? '',
                    'icon' => $area['icon'] ?? 'fas fa-gavel',
                    'description' => $area['description'] ?? '',
                    'link' => $area['link'] ?? '',
                    'link_text' => $area['link_text'] ?? __('Learn More', 'mydefenselaw'),
                );
            }
            if (!empty($areas)) {
                return $areas;
            }
        }
    }

    // Fallback to CPT
    if ($source === 'auto' || $source === 'cpt') {
        $query = new WP_Query(array(
            'post_type' => 'practice_area',
            'posts_per_page' => $limit,
            'orderby' => 'menu_order',
            'order' => 'ASC',
        ));

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $areas[] = array(
                    'title' => get_the_title(),
                    'icon' => get_field('practice_area_icon') ?: 'fas fa-gavel',
                    'description' => get_field('short_description') ?: get_the_excerpt(),
                    'link' => get_permalink(),
                    'link_text' => __('Learn More', 'mydefenselaw'),
                );
            }
            wp_reset_postdata();
        }
    }

    return $areas;
}

/**
 * Get attorneys from CPT
 */
function mydefenselaw_get_attorneys($limit = -1) {
    $attorneys = array();

    $query = new WP_Query(array(
        'post_type' => 'attorney',
        'posts_per_page' => $limit,
        'orderby' => 'menu_order',
        'order' => 'ASC',
    ));

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $photo = get_field('photo');
            $attorneys[] = array(
                'name' => get_the_title(),
                'title' => get_field('attorney_title') ?: '',
                'credentials' => get_field('credentials') ?: '',
                'photo' => $photo ? $photo['url'] : '',
                'photo_alt' => $photo ? $photo['alt'] : get_the_title(),
                'short_bio' => get_field('short_bio') ?: get_the_excerpt(),
                'email' => get_field('email') ?: '',
                'phone' => get_field('phone') ?: '',
                'linkedin' => get_field('linkedin') ?: '',
                'link' => get_permalink(),
            );
        }
        wp_reset_postdata();
    }

    return $attorneys;
}

/**
 * Get social media links from ACF options
 */
function mydefenselaw_get_social_links() {
    $links = array();

    if (!function_exists('get_field')) {
        return $links;
    }

    $social_fields = array(
        'facebook' => array('icon' => 'fab fa-facebook-f', 'name' => 'Facebook'),
        'twitter' => array('icon' => 'fab fa-x-twitter', 'name' => 'Twitter/X'),
        'linkedin' => array('icon' => 'fab fa-linkedin-in', 'name' => 'LinkedIn'),
        'youtube' => array('icon' => 'fab fa-youtube', 'name' => 'YouTube'),
        'instagram' => array('icon' => 'fab fa-instagram', 'name' => 'Instagram'),
    );

    foreach ($social_fields as $key => $config) {
        $url = get_field('social_' . $key, 'option');
        if ($url) {
            $links[] = array(
                'key' => $key,
                'url' => $url,
                'icon' => $config['icon'],
                'name' => $config['name'],
            );
        }
    }

    return $links;
}

/**
 * Display practice area card
 */
function mydefenselaw_practice_area_card($area) {
    ?>
    <div class="practice-area-card">
        <?php if (!empty($area['icon'])) : ?>
            <div class="card-icon">
                <i class="<?php echo esc_attr($area['icon']); ?>"></i>
            </div>
        <?php endif; ?>
        <h3 class="card-title"><?php echo esc_html($area['title']); ?></h3>
        <?php if (!empty($area['description'])) : ?>
            <p class="card-description"><?php echo esc_html($area['description']); ?></p>
        <?php endif; ?>
        <?php if (!empty($area['link'])) : ?>
            <a href="<?php echo esc_url($area['link']); ?>" class="card-link">
                <?php echo esc_html($area['link_text']); ?> <i class="fas fa-arrow-right"></i>
            </a>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Display attorney card
 */
function mydefenselaw_attorney_card($attorney) {
    ?>
    <div class="attorney-card">
        <?php if (!empty($attorney['photo'])) : ?>
            <div class="attorney-photo">
                <img src="<?php echo esc_url($attorney['photo']); ?>"
                     alt="<?php echo esc_attr($attorney['photo_alt']); ?>"
                     loading="lazy">
            </div>
        <?php endif; ?>
        <div class="attorney-info">
            <h3 class="attorney-name"><?php echo esc_html($attorney['name']); ?></h3>
            <?php if (!empty($attorney['title'])) : ?>
                <p class="attorney-title"><?php echo esc_html($attorney['title']); ?></p>
            <?php endif; ?>
            <?php if (!empty($attorney['short_bio'])) : ?>
                <p class="attorney-bio"><?php echo esc_html($attorney['short_bio']); ?></p>
            <?php endif; ?>
            <a href="<?php echo esc_url($attorney['link']); ?>" class="attorney-link">
                <?php esc_html_e('View Profile', 'mydefenselaw'); ?> <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    <?php
}

/**
 * Get Why Choose Us section fields
 *
 * @return array Section data with guaranteed keys
 */
function mydefenselaw_get_why_choose_us_fields() {
    $phone = mydefenselaw_get_primary_phone();
    $phone_raw = preg_replace('/[^0-9]/', '', $phone);

    // Default features
    $default_features = array(
        array('icon' => 'fas fa-trophy', 'title' => 'Experience & Knowledge', 'description' => 'Our attorneys are passionate about the law with decades of combined experience in complex litigation.'),
        array('icon' => 'fas fa-users', 'title' => 'Drive & Dedication', 'description' => 'We are committed to delivering effective, efficient, and quality legal services because we care about every client and every case.'),
        array('icon' => 'fas fa-handshake', 'title' => 'Courteous Service', 'description' => 'Defense Lawyers, P.A. has an experienced and courteous staff to take you through the entire legal process.'),
        array('icon' => 'fas fa-bullseye', 'title' => 'Focus on Client Needs', 'description' => 'We focus on the client\'s specific needs and acquiring the results that our clients want.'),
    );

    // Default attorneys (for sidebar)
    $default_attorneys = array(
        array('name' => 'Lee Stein, Esq.', 'credentials' => 'Admitted in FL - 20+ Years Experience'),
        array('name' => 'Andre Sailers, Esq.', 'credentials' => 'Admitted in GA - 32+ Years Experience'),
        array('name' => 'Wardell Huff, Esq.', 'credentials' => 'Admitted in DC, NJ, NY, MD - 20+ Years'),
    );

    $defaults = array(
        'title' => __('Why Choose Defense Lawyers, P.A.?', 'mydefenselaw'),
        'lead_text' => __('Defense Lawyers, P.A. is dedicated to providing affordable legal services for businesses and individuals who would have otherwise not been able to resolve their legal problems on their own. Our legal team provides the highest quality professional legal counsel.', 'mydefenselaw'),
        'features' => $default_features,
        'cta' => array(
            'title' => __('We Vigorously Advocate For Your Rights', 'mydefenselaw'),
            'text' => __('To achieve successful results in complex litigation, you need more than just the facts on your side. You need experienced counsel.', 'mydefenselaw'),
        ),
        'testimonial' => array(
            'quote' => __('Our attorneys are passionate about the law. We are committed to delivering effective, efficient, and quality legal services because we care about every client and every case.', 'mydefenselaw'),
            'author' => __('Defense Lawyers, P.A.', 'mydefenselaw'),
            'rating' => 5,
        ),
        'attorneys' => $default_attorneys,
        'phone' => $phone,
        'phone_raw' => $phone_raw,
    );

    // Return defaults if ACF not available
    if (!function_exists('get_field')) {
        return $defaults;
    }

    $front_page_id = get_option('page_on_front');

    // Get ACF fields with fallbacks
    $acf_features = get_field('why_features', $front_page_id);
    $features = (!empty($acf_features) && is_array($acf_features)) ? $acf_features : $default_features;

    // Get sidebar configuration from ACF
    $sidebar_title = get_field('sidebar_title', $front_page_id) ?: 'Meet The Attorneys';
    $sidebar_mode = get_field('sidebar_display_mode', $front_page_id) ?: 'auto';
    $sidebar_limit = get_field('sidebar_attorneys_limit', $front_page_id) ?: 3;

    // Get attorneys based on display mode
    $attorneys = array();

    if ($sidebar_mode === 'none') {
        // No attorneys in sidebar
        $attorneys = array();
    } elseif ($sidebar_mode === 'select' && function_exists('get_field')) {
        // Use specifically selected attorneys
        $selected = get_field('sidebar_attorneys_selected', $front_page_id);
        if ($selected && is_array($selected)) {
            foreach ($selected as $attorney_post) {
                $att_id = $attorney_post->ID;
                $photo = get_field('photo', $att_id);
                $attorneys[] = array(
                    'name' => get_the_title($att_id),
                    'title' => get_field('attorney_title', $att_id) ?: '',
                    'credentials' => get_field('credentials', $att_id) ?: '',
                    'photo' => $photo ? $photo['url'] : '',
                    'link' => get_permalink($att_id),
                );
            }
        }
    } else {
        // Auto mode: Get from CPT (existing logic)
        $attorneys = mydefenselaw_get_attorneys($sidebar_limit);
        if (empty($attorneys)) {
            // Map default attorneys to the expected format
            $attorneys = array();
            foreach ($default_attorneys as $att) {
                $attorneys[] = array(
                    'name' => $att['name'],
                    'title' => '',
                    'credentials' => $att['credentials'],
                    'photo' => '',
                    'link' => '#',
                );
            }
        }
    }

    // Get testimonials from CPT (with fallback to default)
    $testimonials = mydefenselaw_get_testimonials();
    if (empty($testimonials)) {
        // Fallback to default testimonial if no posts exist
        $testimonials = array($defaults['testimonial']);
    }

    return array(
        'title' => get_field('why_title', $front_page_id) ?: $defaults['title'],
        'lead_text' => get_field('why_lead_text', $front_page_id) ?: $defaults['lead_text'],
        'features' => $features,
        'cta' => array(
            'title' => get_field('why_cta_title', $front_page_id) ?: $defaults['cta']['title'],
            'text' => get_field('why_cta_text', $front_page_id) ?: $defaults['cta']['text'],
        ),
        'testimonials' => $testimonials,
        'sidebar_title' => $sidebar_title,
        'attorneys' => $attorneys,
        'phone' => $phone,
        'phone_raw' => $phone_raw,
    );
}

/**
 * Get testimonials from Testimonial CPT
 *
 * @param int $limit Number of testimonials to retrieve (default -1 for all)
 * @return array Array of testimonial data
 */
function mydefenselaw_get_testimonials($limit = -1) {
    $args = array(
        'post_type' => 'testimonial',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC',
    );

    $query = new WP_Query($args);
    $testimonials = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();

            $testimonials[] = array(
                'id' => $post_id,
                'quote' => get_the_content(),
                'author' => get_the_title(),
                'rating' => get_field('testimonial_rating', $post_id) ?: 5,
                'photo' => get_the_post_thumbnail_url($post_id, 'thumbnail'),
            );
        }
        wp_reset_postdata();
    }

    return $testimonials;
}

/**
 * Get contact section fields
 *
 * @return array Contact section data
 */
function mydefenselaw_get_contact_section_fields() {
    $phone = mydefenselaw_get_primary_phone();
    $phone_raw = preg_replace('/[^0-9]/', '', $phone);
    $location = mydefenselaw_get_location();
    $hours = mydefenselaw_get_hours();

    $defaults = array(
        'title' => __('Get Your Free Consultation Today', 'mydefenselaw'),
        'subtitle' => __('Don\'t face your legal challenges alone. Contact us now for expert guidance.', 'mydefenselaw'),
        'form_heading' => __('Request Your Free Consultation', 'mydefenselaw'),
        'form_subheading' => __('All information is confidential and protected by attorney-client privilege.', 'mydefenselaw'),
        'button_text' => __('Request Free Consultation', 'mydefenselaw'),
        'company_name' => __('Defense Lawyers, P.A.', 'mydefenselaw'),
        'location' => $location,
        'serving' => __('Serving All of Florida', 'mydefenselaw'),
        'phone' => $phone,
        'phone_raw' => $phone_raw,
        'hours' => $hours,
        'office_hours' => __('Mon-Fri 9AM-6PM', 'mydefenselaw'),
    );

    if (!function_exists('get_field')) {
        return $defaults;
    }

    $front_page_id = get_option('page_on_front');

    return array(
        'title' => get_field('contact_section_title', $front_page_id) ?: $defaults['title'],
        'subtitle' => get_field('contact_section_subtitle', $front_page_id) ?: $defaults['subtitle'],
        'form_heading' => get_field('contact_form_heading', $front_page_id) ?: $defaults['form_heading'],
        'form_subheading' => get_field('contact_form_subheading', $front_page_id) ?: $defaults['form_subheading'],
        'button_text' => get_field('contact_button_text', $front_page_id) ?: $defaults['button_text'],
        'company_name' => $defaults['company_name'],
        'location' => $location,
        'serving' => $defaults['serving'],
        'phone' => $phone,
        'phone_raw' => $phone_raw,
        'hours' => $hours,
        'office_hours' => $defaults['office_hours'],
    );
}
