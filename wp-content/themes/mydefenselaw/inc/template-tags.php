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

/**
 * Get About The Firm page fields
 *
 * Returns all ACF fields for the About page with fallback defaults.
 * Includes section visibility toggles and Attorney CPT integration.
 *
 * @return array About page data
 */
function mydefenselaw_get_about_fields() {
    $phone = mydefenselaw_get_primary_phone();
    $phone_raw = preg_replace('/[^0-9]/', '', $phone);

    // Default mission principles
    $default_principles = array(
        array('principle_title' => 'Experience & Knowledge', 'principle_description' => 'Our attorneys are passionate about the law with decades of combined experience'),
        array('principle_title' => 'Drive & Dedication', 'principle_description' => 'We are committed to delivering effective, efficient, and quality legal services'),
        array('principle_title' => 'Courteous Service', 'principle_description' => 'Defense Lawyers, P.A. has an experienced and courteous staff to take you through the entire legal process'),
        array('principle_title' => 'Focus on the client\'s specific needs', 'principle_description' => 'We focus on acquiring the results that our clients want'),
        array('principle_title' => 'Acquiring the results that our clients want', 'principle_description' => 'Our track record speaks for itself'),
    );

    // Default attorneys (fallback if no CPT data)
    $default_attorneys = array(
        array(
            'name' => 'Lee Stein, Esq.',
            'bar_admissions' => 'Admitted in FL',
            'short_bio' => 'Attorney Lee Stein has been an attorney for over 20 years. He graduated the University of Florida College of Law and is committed to providing professionalism, experience, dedication, service, and results for the firm\'s clients.',
        ),
        array(
            'name' => 'Andre Sailers, Esq.',
            'bar_admissions' => 'Admitted in GA',
            'short_bio' => 'Attorney Andre Sailers has been an attorney for over 32 years. He graduated the University of Iowa and holds firm to the creed of "pursuing justice while offering the highest quality legal representation and superior client satisfaction."',
        ),
        array(
            'name' => 'Wardell Huff, Esq.',
            'bar_admissions' => 'Admitted in DC, NJ, NY, Dist. of MD',
            'short_bio' => 'Attorney Wardell Huff has been an attorney for over 20 years. He is graduate of Michigan State University College of Law and has held membership within the National Association of Consumer Bankruptcy Attorneys.',
        ),
    );

    $defaults = array(
        'intro_title' => __('About Defense Lawyers, P.A.', 'mydefenselaw'),
        'intro_content' => '<p>Defense Lawyers, P.A. offers motivated, experienced legal counsel and representation in a variety of practice areas. Our approach has resulted in stable, long-term relationships with our clients, which are based on the firm\'s prompt, efficient and high quality service to reach shared objectives. To employ the wisdom and skill of our practiced attorneys for your legal needs, <a href="' . esc_url(home_url('/contact/')) . '">contact us</a> today!</p>',
        'mission_title' => __('Mission Statement', 'mydefenselaw'),
        'mission_intro' => __('Defense Lawyers, P.A. is committed to providing the absolute highest quality of legal advice and advocacy. We recognize that our success must be earned every day to maintain the firm and long standing relationships that we enjoy with our clients. To achieve this end, our firm is committed to the following principles:', 'mydefenselaw'),
        'mission_principles' => $default_principles,
        'attorneys_title' => __('Meet The Attorneys', 'mydefenselaw'),
        'attorneys' => $default_attorneys,
        'passion_title' => __('Our Attorneys Bring Passion and Dedication to Each Case', 'mydefenselaw'),
        'passion_content' => __('Our lawyers bring unique specialties and the capacity to excel to every client we represent and to every case we take. At Defense Lawyers, P.A., we have a reputation among our clients and our peers for committed service and highly effective litigation. Because of our success in the courtroom, we receive a lot of referrals from former clients, as well as other legal firms. We enjoy our work and are truly proud when we help our clients achieve the results they desire.', 'mydefenselaw'),
        'cta_title' => __('Experience, Dedication, Service, and Results', 'mydefenselaw'),
        'cta_tagline' => __('Defense Lawyers, P.A. prides itself on professionalism, experience, dedication, service, and results.', 'mydefenselaw'),
        'phone' => $phone,
        'phone_raw' => $phone_raw,
    );

    // Return defaults if ACF not available
    if (!function_exists('get_field')) {
        return $defaults;
    }

    // Get ACF fields
    $acf_principles = get_field('about_mission_principles');
    $principles = (!empty($acf_principles) && is_array($acf_principles)) ? $acf_principles : $default_principles;

    // Get attorneys based on display mode
    $display_mode = get_field('about_attorneys_display_mode') ?: 'all';
    $attorneys = array();

    if ($display_mode === 'select') {
        // Use specifically selected attorneys from relationship field
        $selected = get_field('about_attorneys_selected');
        if ($selected && is_array($selected)) {
            foreach ($selected as $attorney_post) {
                $att_id = $attorney_post->ID;
                $bar_admissions = get_field('bar_admissions', $att_id);
                $admissions_str = '';
                if ($bar_admissions && is_array($bar_admissions)) {
                    $states = array_column($bar_admissions, 'state');
                    $admissions_str = 'Admitted in ' . implode(', ', $states);
                }
                $attorneys[] = array(
                    'name' => get_the_title($att_id),
                    'bar_admissions' => $admissions_str,
                    'short_bio' => get_field('short_bio', $att_id) ?: '',
                );
            }
        }
    } elseif ($display_mode === 'limit') {
        // Show limited number from CPT
        $limit = get_field('about_attorneys_limit') ?: 3;
        $attorneys = mydefenselaw_get_about_attorneys_from_cpt($limit);
    } else {
        // Show all attorneys from CPT
        $attorneys = mydefenselaw_get_about_attorneys_from_cpt(-1);
    }

    // Fallback to defaults if no attorneys found
    if (empty($attorneys)) {
        $attorneys = $default_attorneys;
    }

    return array(
        'intro_title' => get_field('about_intro_title') ?: $defaults['intro_title'],
        'intro_content' => get_field('about_intro_content') ?: $defaults['intro_content'],
        'mission_title' => get_field('about_mission_title') ?: $defaults['mission_title'],
        'mission_intro' => get_field('about_mission_intro') ?: $defaults['mission_intro'],
        'mission_principles' => $principles,
        'attorneys_title' => get_field('about_attorneys_title') ?: $defaults['attorneys_title'],
        'attorneys' => $attorneys,
        'passion_title' => get_field('about_passion_title') ?: $defaults['passion_title'],
        'passion_content' => get_field('about_passion_content') ?: $defaults['passion_content'],
        'cta_title' => get_field('about_cta_title') ?: $defaults['cta_title'],
        'cta_tagline' => get_field('about_cta_tagline') ?: $defaults['cta_tagline'],
        'phone' => $phone,
        'phone_raw' => $phone_raw,
    );
}

/**
 * Get attorneys from CPT for About page
 *
 * @param int $limit Number of attorneys to retrieve
 * @return array Array of attorney data formatted for About page
 */
function mydefenselaw_get_about_attorneys_from_cpt($limit = -1) {
    $attorneys = array();

    $query = new WP_Query(array(
        'post_type' => 'attorney',
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'orderby' => 'menu_order',
        'order' => 'ASC',
    ));

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $att_id = get_the_ID();

            // Build bar admissions string
            $bar_admissions = get_field('bar_admissions', $att_id);
            $admissions_str = '';
            if ($bar_admissions && is_array($bar_admissions)) {
                $states = array_column($bar_admissions, 'state');
                $admissions_str = 'Admitted in ' . implode(', ', $states);
            }

            $attorneys[] = array(
                'name' => get_the_title(),
                'bar_admissions' => $admissions_str,
                'short_bio' => get_field('short_bio', $att_id) ?: '',
            );
        }
        wp_reset_postdata();
    }

    return $attorneys;
}

/**
 * Get Legal Resources page fields
 *
 * Returns all ACF fields for the Resources page with fallback defaults.
 * Includes section visibility toggles and resource categories.
 *
 * @return array Resources page data
 */
function mydefenselaw_get_resources_fields() {
    $phone = mydefenselaw_get_primary_phone();
    $phone_raw = preg_replace('/[^0-9]/', '', $phone);

    // Default resource categories with links
    $default_categories = array(
        array(
            'category_title' => 'Civil Defense Litigation Resources',
            'category_icon' => 'fas fa-balance-scale',
            'category_links' => array(
                array(
                    'link_title' => 'Federal Rules of Civil Procedure',
                    'link_url' => 'https://www.law.cornell.edu/rules/frcp',
                    'link_description' => 'The process of resolving disputes by filing or answering a complaint through the public court system is regulated by the Federal Rules of Civil Procedure (along with state versions). The nature of this complaint (including the probable outcome for each side) becomes the basis for any settlement negotiations.',
                ),
                array(
                    'link_title' => 'Federal Rules of Evidence',
                    'link_url' => 'https://www.law.cornell.edu/rules/fre',
                    'link_description' => 'If a case is brought to trial, the Federal Rules of Evidence (along with state versions) govern the introduction of all evidence into the trial.',
                ),
            ),
        ),
        array(
            'category_title' => 'Debt Collection Resources',
            'category_icon' => 'fas fa-file-invoice-dollar',
            'category_links' => array(
                array(
                    'link_title' => 'Bureau of the Public Debt',
                    'link_url' => 'https://www.treasurydirect.gov/',
                    'link_description' => 'The Bureau of the Public Debt Provides Monthly Updates on the Ever Increasing United States Public Debt Figures.',
                ),
                array(
                    'link_title' => 'The Fair Debt Collection Practices Act',
                    'link_url' => 'https://www.ftc.gov/legal-library/browse/rules/fair-debt-collection-practices-act-text',
                    'link_description' => 'The United States has enacted laws to protect consumers and to prohibit abusive practices by debt collectors.',
                ),
                array(
                    'link_title' => 'Credit and Your Consumer Rights',
                    'link_url' => 'https://www.ftc.gov/news-events/topics/consumer-finance/credit',
                    'link_description' => 'Learn about credit ratings and your rights.',
                ),
                array(
                    'link_title' => 'The Truth in Lending Act',
                    'link_url' => 'https://www.fdic.gov/regulations/laws/rules/6500-200.html',
                    'link_description' => 'The FDIC has created laws, regulations, and related acts in order to protect consumers.',
                ),
                array(
                    'link_title' => 'The Federal Reserve System',
                    'link_url' => 'https://www.federalreserve.gov/',
                    'link_description' => 'Helpful information regarding the Federal Reserve System.',
                ),
                array(
                    'link_title' => 'Free Credit Report',
                    'link_url' => 'https://www.annualcreditreport.com/',
                    'link_description' => 'A recent amendment to the federal Fair Credit Reporting Act requires each of the nationwide consumer reporting companies – Equifax, Experian, and TransUnion – to provide you with a free copy of your credit report, at your request, once every 12 months.',
                ),
            ),
        ),
        array(
            'category_title' => 'Consumer Protection Resources',
            'category_icon' => 'fas fa-shield-alt',
            'category_links' => array(
                array(
                    'link_title' => 'Class Action Litigation Information',
                    'link_url' => 'https://www.classaction.org/',
                    'link_description' => 'A free service to assist consumers in understanding class action lawsuits, government, consumer issues and the legal system.',
                ),
                array(
                    'link_title' => 'Lemon Law America',
                    'link_url' => 'https://www.lemonlawamerica.com/',
                    'link_description' => 'Site is resource for consumers with defective vehicles or products. A visit to this site familiarizes you with the lemon statutes in your state and offers tips on how to proceed if you think you\'ve got a "lemon".',
                ),
            ),
        ),
        array(
            'category_title' => 'Bankruptcy Resources',
            'category_icon' => 'fas fa-landmark',
            'category_links' => array(
                array(
                    'link_title' => 'History of Bankruptcy in the United States',
                    'link_url' => 'https://www.uscourts.gov/services-forms/bankruptcy',
                    'link_description' => 'Bankruptcy laws in the United States have varied greatly over time. Learn more about the interesting history of bankruptcy.',
                ),
                array(
                    'link_title' => 'The Bankruptcy Process',
                    'link_url' => 'https://www.uscourts.gov/services-forms/bankruptcy/bankruptcy-basics',
                    'link_description' => 'The procedural aspects of the bankruptcy process are governed by the Federal Rules of Bankruptcy Procedure (often called the "Bankruptcy Rules") and local rules of each bankruptcy court.',
                ),
            ),
        ),
        array(
            'category_title' => 'Contract Law Resources',
            'category_icon' => 'fas fa-file-contract',
            'category_links' => array(
                array(
                    'link_title' => 'Uniform Commercial Code',
                    'link_url' => 'https://www.law.cornell.edu/ucc',
                    'link_description' => 'Contract law includes the concepts of formation, offer, acceptance, and consideration; performance and excuse for nonperformance; breach and damages; third party beneficiaries; assignment of rights and delegation of duties; statute of frauds; contract integration rule; illegal contracts and public policy; unconscionability; and discharge. One major portion of this area of law is in the Uniform Commercial Code (UCC).',
                ),
            ),
        ),
        array(
            'category_title' => 'Family Law Resources',
            'category_icon' => 'fas fa-users',
            'category_links' => array(
                array(
                    'link_title' => 'Family Law in the Fifty States',
                    'link_url' => 'https://www.americanbar.org/groups/family_law/',
                    'link_description' => 'Tables providing a quick view of various aspects of family law for the fifty states in the areas of alimony/spousal support factors, custody criteria, child support guidelines, grounds for divorce and residency requirements, property division, and third-party visitation.',
                ),
            ),
        ),
        array(
            'category_title' => 'Real Estate Law Resources',
            'category_icon' => 'fas fa-home',
            'category_links' => array(
                array(
                    'link_title' => 'Problems When Purchasing Residential Real Estate',
                    'link_url' => 'https://www.lawyers.com/legal-info/real-estate/',
                    'link_description' => 'The purchase of residential real estate usually involves three parties: the buyer, the seller, and the lender. This can make things complex. Plus, the law that governs real estate transactions is different from the law governing other kinds of purchases.',
                ),
                array(
                    'link_title' => 'Buying and Selling Commercial Real Estate',
                    'link_url' => 'https://www.lawyers.com/legal-info/real-estate/commercial-real-estate/',
                    'link_description' => 'Commercial real estate transactions are typically more complex than residential transactions. Usually, they involve large sums of money and increased liability for both parties.',
                ),
            ),
        ),
    );

    $defaults = array(
        'intro_title' => __('Legal Resources', 'mydefenselaw'),
        'intro_content' => '<p>Defense Lawyers, P.A. provides these legal resources to help our clients and the public understand various areas of law. Please note that this information is for educational purposes only and does not constitute legal advice.</p>',
        'categories' => $default_categories,
        'cta_title' => __('Need Legal Guidance?', 'mydefenselaw'),
        'cta_content' => __('While these resources provide helpful information, every legal situation is unique. Contact us for personalized legal advice.', 'mydefenselaw'),
        'phone' => $phone,
        'phone_raw' => $phone_raw,
    );

    // Return defaults if ACF not available
    if (!function_exists('get_field')) {
        return $defaults;
    }

    // Get ACF fields
    $acf_categories = get_field('resources_categories');
    $categories = (!empty($acf_categories) && is_array($acf_categories)) ? $acf_categories : $default_categories;

    return array(
        'intro_title' => get_field('resources_intro_title') ?: $defaults['intro_title'],
        'intro_content' => get_field('resources_intro_content') ?: $defaults['intro_content'],
        'categories' => $categories,
        'cta_title' => get_field('resources_cta_title') ?: $defaults['cta_title'],
        'cta_content' => get_field('resources_cta_content') ?: $defaults['cta_content'],
        'phone' => $phone,
        'phone_raw' => $phone_raw,
    );
}

/**
 * Get Contact Us page fields
 *
 * Returns all ACF fields for the Contact Us page with fallback defaults.
 * Includes section visibility toggles, contact info, why choose us, form settings, and process steps.
 *
 * @return array Contact page data
 */
function mydefenselaw_get_contact_page_fields() {
    $phone = mydefenselaw_get_primary_phone();
    $phone_raw = preg_replace('/[^0-9]/', '', $phone);
    $location = mydefenselaw_get_location();

    // Default why choose features
    $default_features = array(
        array(
            'feature_icon' => 'fas fa-check-circle',
            'feature_title' => '25+ Years of Experience',
            'feature_description' => 'Proven track record in complex legal matters',
        ),
        array(
            'feature_icon' => 'fas fa-check-circle',
            'feature_title' => 'Available 24/7',
            'feature_description' => 'Legal emergencies don\'t wait, neither do we',
        ),
        array(
            'feature_icon' => 'fas fa-check-circle',
            'feature_title' => 'Free Consultation',
            'feature_description' => 'Understand your options without cost',
        ),
        array(
            'feature_icon' => 'fas fa-check-circle',
            'feature_title' => 'Dedicated Service',
            'feature_description' => 'Personalized attention to your case',
        ),
    );

    // Default process steps
    $default_process_steps = array(
        array(
            'step_icon' => 'fas fa-calendar-check',
            'step_title' => 'Consultation',
            'step_description' => 'We\'ll review your case and discuss your legal options during a free, confidential consultation.',
        ),
        array(
            'step_icon' => 'fas fa-file-alt',
            'step_title' => 'Case Review',
            'step_description' => 'Our experienced attorneys will thoroughly analyze your situation and develop a strategic approach.',
        ),
        array(
            'step_icon' => 'fas fa-gavel',
            'step_title' => 'Representation',
            'step_description' => 'We\'ll vigorously defend your rights and work tirelessly to achieve the best possible outcome.',
        ),
    );

    $defaults = array(
        'hero_title' => __('Get Your Free Consultation Today', 'mydefenselaw'),
        'hero_subtitle' => __('Don\'t face your legal challenges alone. Contact us now for expert guidance.', 'mydefenselaw'),
        'hero_badge' => __('Emergency? Call Now for Immediate Help', 'mydefenselaw'),
        'info_title' => __('Defense Lawyers, P.A.', 'mydefenselaw'),
        'location' => $location,
        'serving' => __('Serving All of Florida', 'mydefenselaw'),
        'hours_label' => __('24/7 Emergency Line', 'mydefenselaw'),
        'office_hours' => __('Office Hours: Mon-Fri 9AM-6PM', 'mydefenselaw'),
        'why_title' => __('Why Choose Defense Lawyers, P.A.?', 'mydefenselaw'),
        'why_features' => $default_features,
        'form_title' => __('Request Your Free Consultation', 'mydefenselaw'),
        'form_subtitle' => __('All information is confidential and protected by attorney-client privilege.', 'mydefenselaw'),
        'form_button' => __('Request Free Consultation', 'mydefenselaw'),
        'form_disclaimer' => __('By submitting this form, you acknowledge that no attorney-client relationship has been formed. Representation will not be agreed upon unless and until Defense Lawyers, P.A. agrees to take you on as a client.', 'mydefenselaw'),
        'process_title' => __('What to Expect', 'mydefenselaw'),
        'process_steps' => $default_process_steps,
        'phone' => $phone,
        'phone_raw' => $phone_raw,
    );

    // Return defaults if ACF not available
    if (!function_exists('get_field')) {
        return $defaults;
    }

    // Get ACF fields - validate that repeater has actual content
    $acf_features = get_field('contact_why_features');
    $features = $default_features;
    if (!empty($acf_features) && is_array($acf_features)) {
        // Validate first item has required fields
        $first_feature = reset($acf_features);
        if (!empty($first_feature['feature_title'])) {
            $features = $acf_features;
        }
    }

    $acf_steps = get_field('contact_process_steps');
    $process_steps = $default_process_steps;
    if (!empty($acf_steps) && is_array($acf_steps)) {
        // Validate first item has required fields
        $first_step = reset($acf_steps);
        if (!empty($first_step['step_title'])) {
            $process_steps = $acf_steps;
        }
    }

    return array(
        'hero_title' => get_field('contact_hero_title') ?: $defaults['hero_title'],
        'hero_subtitle' => get_field('contact_hero_subtitle') ?: $defaults['hero_subtitle'],
        'hero_badge' => get_field('contact_hero_badge') ?: $defaults['hero_badge'],
        'info_title' => get_field('contact_info_title') ?: $defaults['info_title'],
        'location' => $location,
        'serving' => get_field('contact_info_serving') ?: $defaults['serving'],
        'hours_label' => get_field('contact_info_hours_label') ?: $defaults['hours_label'],
        'office_hours' => get_field('contact_info_office_hours') ?: $defaults['office_hours'],
        'why_title' => get_field('contact_why_title') ?: $defaults['why_title'],
        'why_features' => $features,
        'form_title' => get_field('contact_form_title') ?: $defaults['form_title'],
        'form_subtitle' => get_field('contact_form_subtitle') ?: $defaults['form_subtitle'],
        'form_button' => get_field('contact_form_button') ?: $defaults['form_button'],
        'form_disclaimer' => get_field('contact_form_disclaimer') ?: $defaults['form_disclaimer'],
        'process_title' => get_field('contact_process_title') ?: $defaults['process_title'],
        'process_steps' => $process_steps,
        'phone' => $phone,
        'phone_raw' => $phone_raw,
    );
}

/**
 * Get Practice Area single page fields
 *
 * Returns all ACF fields for a single practice area page with fallback defaults.
 * Used by single-practice_area.php template.
 *
 * @param int|null $post_id Post ID (optional, defaults to current post)
 * @return array Practice area page data
 */
function mydefenselaw_get_practice_area_fields($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    $phone = mydefenselaw_get_primary_phone();
    $phone_raw = preg_replace('/[^0-9]/', '', $phone);

    $defaults = array(
        'title' => get_the_title($post_id),
        'short_description' => '',
        'hero_image' => '',
        'page_content' => '',
        'services' => array(),
        'faqs' => array(),
        'cta_title' => __('Ready to Discuss Your Case?', 'mydefenselaw'),
        'cta_text' => __('Contact our experienced attorneys today for a free consultation.', 'mydefenselaw'),
        'cta_button_text' => __('Schedule Free Consultation', 'mydefenselaw'),
        'phone' => $phone,
        'phone_raw' => $phone_raw,
    );

    // Return defaults if ACF not available
    if (!function_exists('get_field')) {
        return $defaults;
    }

    // Get ACF fields
    $services = get_field('services', $post_id);
    $faqs = get_field('faqs', $post_id);

    return array(
        'title' => get_the_title($post_id),
        'short_description' => get_field('short_description', $post_id) ?: $defaults['short_description'],
        'hero_image' => get_field('hero_image', $post_id) ?: $defaults['hero_image'],
        'page_content' => get_field('page_content', $post_id) ?: $defaults['page_content'],
        'services' => (!empty($services) && is_array($services)) ? $services : $defaults['services'],
        'faqs' => (!empty($faqs) && is_array($faqs)) ? $faqs : $defaults['faqs'],
        'cta_title' => get_field('cta_title', $post_id) ?: $defaults['cta_title'],
        'cta_text' => get_field('cta_text', $post_id) ?: $defaults['cta_text'],
        'cta_button_text' => get_field('cta_button_text', $post_id) ?: $defaults['cta_button_text'],
        'phone' => $phone,
        'phone_raw' => $phone_raw,
    );
}

/**
 * Get Latest Legal News page fields
 *
 * Returns all ACF fields for the Latest Legal News page with fallback defaults.
 * Includes section visibility toggles, intro, news items, CTA sections, and reasons.
 *
 * @return array Latest News page data
 */
function mydefenselaw_get_latest_news_fields() {
    $phone = mydefenselaw_get_primary_phone();
    $phone_raw = preg_replace('/[^0-9]/', '', $phone);

    // Default news items (matching live site content)
    $default_news_items = array(
        array(
            'news_category' => 'Recent Legal Updates',
            'news_title' => 'Consumer Protection Laws',
            'news_content' => 'Updates to regulations safeguard consumers against unfair business practices, affecting debt collection, warranty claims, and financial services.',
        ),
        array(
            'news_category' => 'Recent Legal Updates',
            'news_title' => 'Federal Bankruptcy Updates',
            'news_content' => 'New federal guidelines clarify debt relief paths while protecting creditors in both individual and business bankruptcy scenarios.',
        ),
        array(
            'news_category' => 'Recent Legal Updates',
            'news_title' => 'Child Custody Guidelines',
            'news_content' => 'State guidelines emphasize child welfare with clearer standards for custody determinations and modifications.',
        ),
        array(
            'news_category' => 'Recent Legal Updates',
            'news_title' => 'Property Rights Updates',
            'news_content' => 'Court decisions clarified property rights in landlord-tenant disputes, benefiting both owners and renters.',
        ),
        array(
            'news_category' => 'Recent Legal Updates',
            'news_title' => 'Commercial Contract Standards',
            'news_content' => 'Standards provide guidance for dispute resolution and performance obligations in business agreements.',
        ),
        array(
            'news_category' => 'Recent Legal Updates',
            'news_title' => 'Court Procedure Changes',
            'news_content' => 'Updates streamline litigation while maintaining due process protections, affecting filing and discovery procedures.',
        ),
    );

    // Default reasons why legal news matters (matching live site content)
    $default_reasons = array(
        array(
            'reason_icon' => 'fas fa-gavel',
            'reason_title' => 'Changing Regulations',
            'reason_content' => 'Laws evolve frequently; awareness helps you grasp potential impacts.',
        ),
        array(
            'reason_icon' => 'fas fa-shield-alt',
            'reason_title' => 'Know Your Rights',
            'reason_content' => 'Understanding developments helps identify affected rights and timing for legal counsel.',
        ),
        array(
            'reason_icon' => 'fas fa-clock',
            'reason_title' => 'Timely Action',
            'reason_content' => 'Legal matters have deadlines; awareness ensures compliance with required timeframes.',
        ),
        array(
            'reason_icon' => 'fas fa-lightbulb',
            'reason_title' => 'Better Decisions',
            'reason_content' => 'Current legal knowledge supports informed choices regarding personal and business matters.',
        ),
    );

    $defaults = array(
        'intro_title' => __('Latest Legal News', 'mydefenselaw'),
        'intro_content' => '<p>Stay informed with the latest legal developments, court decisions, and regulatory changes that may affect your legal matters. Defense Lawyers, P.A. keeps you updated on important legal news and trends.</p>',
        'news_items' => $default_news_items,
        'cta_title' => __('Stay Informed About Legal Changes', 'mydefenselaw'),
        'cta_content' => __('Legal developments can directly impact your rights and obligations. Our experienced attorneys stay current with all legal changes to provide you with the most up-to-date advice.', 'mydefenselaw'),
        'reasons_title' => __('Why Legal News Matters', 'mydefenselaw'),
        'reasons' => $default_reasons,
        'final_cta_title' => __('Need Legal Advice About Recent Changes?', 'mydefenselaw'),
        'final_cta_content' => __('Our experienced attorneys stay current with all legal developments and can help you understand how they affect your situation.', 'mydefenselaw'),
        'phone' => $phone,
        'phone_raw' => $phone_raw,
    );

    // Return defaults if ACF not available
    if (!function_exists('get_field')) {
        return $defaults;
    }

    // Get ACF fields - validate that repeaters have actual content
    $acf_news_items = get_field('news_items');
    $news_items = $default_news_items;
    if (!empty($acf_news_items) && is_array($acf_news_items)) {
        $first_item = reset($acf_news_items);
        if (!empty($first_item['news_title'])) {
            $news_items = $acf_news_items;
        }
    }

    $acf_reasons = get_field('news_reasons');
    $reasons = $default_reasons;
    if (!empty($acf_reasons) && is_array($acf_reasons)) {
        $first_reason = reset($acf_reasons);
        if (!empty($first_reason['reason_title'])) {
            $reasons = $acf_reasons;
        }
    }

    return array(
        'intro_title' => get_field('news_intro_title') ?: $defaults['intro_title'],
        'intro_content' => get_field('news_intro_content') ?: $defaults['intro_content'],
        'news_items' => $news_items,
        'cta_title' => get_field('news_cta_title') ?: $defaults['cta_title'],
        'cta_content' => get_field('news_cta_content') ?: $defaults['cta_content'],
        'reasons_title' => get_field('news_reasons_title') ?: $defaults['reasons_title'],
        'reasons' => $reasons,
        'final_cta_title' => get_field('news_final_cta_title') ?: $defaults['final_cta_title'],
        'final_cta_content' => get_field('news_final_cta_content') ?: $defaults['final_cta_content'],
        'phone' => $phone,
        'phone_raw' => $phone_raw,
    );
}
