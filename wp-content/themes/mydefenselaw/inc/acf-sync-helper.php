<?php
/**
 * ACF Sync Helper
 * Provides tools for syncing ACF field groups and creating sample content
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add admin notice to sync ACF field groups
 */
function mydefenselaw_acf_sync_notice() {
    if (!function_exists('acf_get_field_groups')) {
        return;
    }

    // Check if any groups need syncing
    $groups = acf_get_field_groups();
    $sync_needed = array();

    foreach ($groups as $group) {
        $local = acf_maybe_get($group, 'local');
        $modified = acf_maybe_get($group, 'modified');
        $private = acf_maybe_get($group, 'private');

        if ($local === 'json' && !$private) {
            $json_path = get_stylesheet_directory() . '/acf-json/' . $group['key'] . '.json';
            if (file_exists($json_path)) {
                $json_group = json_decode(file_get_contents($json_path), true);
                if ($json_group && !isset($group['ID'])) {
                    $sync_needed[] = $group['title'];
                }
            }
        }
    }

    if (!empty($sync_needed)) {
        $url = admin_url('edit.php?post_type=acf-field-group&post_status=sync');
        echo '<div class="notice notice-warning is-dismissible">';
        echo '<p><strong>My Defense Law Theme:</strong> ' . count($sync_needed) . ' ACF field groups need to be synced. ';
        echo '<a href="' . esc_url($url) . '">Sync Now</a></p>';
        echo '</div>';
    }
}
add_action('admin_notices', 'mydefenselaw_acf_sync_notice');

/**
 * Auto-sync ACF field groups from JSON on theme activation
 */
function mydefenselaw_auto_sync_acf() {
    if (!function_exists('acf_import_field_group')) {
        return;
    }

    $json_dir = get_stylesheet_directory() . '/acf-json';

    if (!is_dir($json_dir)) {
        return;
    }

    $files = glob($json_dir . '/*.json');

    foreach ($files as $file) {
        $json = file_get_contents($file);
        $group = json_decode($json, true);

        if (!$group || !isset($group['key'])) {
            continue;
        }

        // Check if group already exists
        $existing = acf_get_field_group($group['key']);

        if (!$existing) {
            // Import the field group
            acf_import_field_group($group);
            error_log('ACF Sync: Imported field group - ' . $group['title']);
        }
    }
}
add_action('after_switch_theme', 'mydefenselaw_auto_sync_acf', 20);

/**
 * Admin page to manually trigger sync and create sample content
 */
function mydefenselaw_add_setup_page() {
    add_theme_page(
        __('Theme Setup', 'mydefenselaw'),
        __('Theme Setup', 'mydefenselaw'),
        'manage_options',
        'mydefenselaw-setup',
        'mydefenselaw_setup_page'
    );
}
add_action('admin_menu', 'mydefenselaw_add_setup_page');

/**
 * Theme setup page content
 */
function mydefenselaw_setup_page() {
    // Handle form submissions
    if (isset($_POST['mydefenselaw_sync_acf']) && check_admin_referer('mydefenselaw_setup')) {
        mydefenselaw_force_sync_acf();
        echo '<div class="notice notice-success"><p>ACF field groups synced successfully!</p></div>';
    }

    if (isset($_POST['mydefenselaw_create_sample']) && check_admin_referer('mydefenselaw_setup')) {
        $result = mydefenselaw_create_sample_content();
        echo '<div class="notice notice-success"><p>' . esc_html($result) . '</p></div>';
    }

    if (isset($_POST['mydefenselaw_setup_options']) && check_admin_referer('mydefenselaw_setup')) {
        mydefenselaw_setup_default_options();
        echo '<div class="notice notice-success"><p>Default theme options configured!</p></div>';
    }

    ?>
    <div class="wrap">
        <h1><?php esc_html_e('My Defense Law - Theme Setup', 'mydefenselaw'); ?></h1>

        <div class="card" style="max-width: 800px; padding: 20px; margin-top: 20px;">
            <h2><?php esc_html_e('Setup Status', 'mydefenselaw'); ?></h2>

            <table class="widefat" style="margin-top: 15px;">
                <tbody>
                    <tr>
                        <td><strong>ACF Pro</strong></td>
                        <td>
                            <?php if (function_exists('acf_get_field_groups')) : ?>
                                <span style="color: green;">✓ Installed & Active</span>
                            <?php else : ?>
                                <span style="color: red;">✗ Not Active</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>ACF Field Groups</strong></td>
                        <td>
                            <?php
                            $json_files = glob(get_stylesheet_directory() . '/acf-json/*.json');
                            $synced_count = 0;
                            if (function_exists('acf_get_field_groups')) {
                                $groups = acf_get_field_groups();
                                $synced_count = count(array_filter($groups, function($g) {
                                    return isset($g['ID']) && $g['ID'] > 0;
                                }));
                            }
                            echo esc_html($synced_count . ' of ' . count($json_files) . ' synced');
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Practice Areas CPT</strong></td>
                        <td>
                            <?php
                            $pa_count = wp_count_posts('practice_area');
                            echo esc_html(($pa_count->publish ?? 0) . ' published');
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Attorneys CPT</strong></td>
                        <td>
                            <?php
                            $att_count = wp_count_posts('attorney');
                            echo esc_html(($att_count->publish ?? 0) . ' published');
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Theme Options</strong></td>
                        <td>
                            <?php
                            if (function_exists('get_field')) {
                                $phone = get_field('phone_primary', 'option');
                                echo $phone ? '<span style="color: green;">✓ Configured</span>' : '<span style="color: orange;">○ Not configured</span>';
                            } else {
                                echo '<span style="color: gray;">— ACF required</span>';
                            }
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card" style="max-width: 800px; padding: 20px; margin-top: 20px;">
            <h2><?php esc_html_e('Quick Actions', 'mydefenselaw'); ?></h2>

            <form method="post" style="margin-top: 15px;">
                <?php wp_nonce_field('mydefenselaw_setup'); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e('Sync ACF Field Groups', 'mydefenselaw'); ?></th>
                        <td>
                            <button type="submit" name="mydefenselaw_sync_acf" class="button button-secondary">
                                <?php esc_html_e('Force Sync from JSON', 'mydefenselaw'); ?>
                            </button>
                            <p class="description"><?php esc_html_e('Import all field groups from acf-json folder', 'mydefenselaw'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Setup Default Options', 'mydefenselaw'); ?></th>
                        <td>
                            <button type="submit" name="mydefenselaw_setup_options" class="button button-secondary">
                                <?php esc_html_e('Configure Defaults', 'mydefenselaw'); ?>
                            </button>
                            <p class="description"><?php esc_html_e('Set default phone, address, and other theme options', 'mydefenselaw'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e('Create Sample Content', 'mydefenselaw'); ?></th>
                        <td>
                            <button type="submit" name="mydefenselaw_create_sample" class="button button-primary">
                                <?php esc_html_e('Create Sample Practice Areas & Attorneys', 'mydefenselaw'); ?>
                            </button>
                            <p class="description"><?php esc_html_e('Creates sample content to preview the theme', 'mydefenselaw'); ?></p>
                        </td>
                    </tr>
                </table>
            </form>
        </div>

        <div class="card" style="max-width: 800px; padding: 20px; margin-top: 20px;">
            <h2><?php esc_html_e('Quick Links', 'mydefenselaw'); ?></h2>
            <ul style="margin-top: 15px;">
                <?php if (function_exists('acf_get_field_groups')) : ?>
                    <li><a href="<?php echo admin_url('edit.php?post_type=acf-field-group'); ?>">→ ACF Field Groups</a></li>
                    <li><a href="<?php echo admin_url('admin.php?page=theme-settings'); ?>">→ Theme Settings (Contact Info)</a></li>
                <?php endif; ?>
                <li><a href="<?php echo admin_url('edit.php?post_type=practice_area'); ?>">→ Practice Areas</a></li>
                <li><a href="<?php echo admin_url('edit.php?post_type=attorney'); ?>">→ Attorneys</a></li>
                <li><a href="<?php echo admin_url('customize.php'); ?>">→ Theme Customizer</a></li>
                <li><a href="<?php echo admin_url('nav-menus.php'); ?>">→ Navigation Menus</a></li>
            </ul>
        </div>
    </div>
    <?php
}

/**
 * Force sync all ACF field groups from JSON
 */
function mydefenselaw_force_sync_acf() {
    if (!function_exists('acf_import_field_group')) {
        return false;
    }

    $json_dir = get_stylesheet_directory() . '/acf-json';
    $files = glob($json_dir . '/*.json');
    $imported = 0;

    foreach ($files as $file) {
        $json = file_get_contents($file);
        $group = json_decode($json, true);

        if (!$group || !isset($group['key'])) {
            continue;
        }

        // Delete existing if present
        $existing = acf_get_field_group($group['key']);
        if ($existing && isset($existing['ID'])) {
            wp_delete_post($existing['ID'], true);
        }

        // Import fresh
        acf_import_field_group($group);
        $imported++;
    }

    return $imported;
}

/**
 * Setup default theme options
 */
function mydefenselaw_setup_default_options() {
    if (!function_exists('update_field')) {
        return;
    }

    // Default contact info
    update_field('phone_primary', '888.444.0253', 'option');
    update_field('address_city', 'Boca Raton', 'option');
    update_field('address_state', 'FL', 'option');
    update_field('hours_display', 'Available 24/7', 'option');
    update_field('footer_tagline', 'Protecting your rights and fighting for your future for over 25 years.', 'option');
    update_field('footer_disclaimer', 'DISCLOSURES: If you have already retained a lawyer, please disregard this communication. The hiring of a lawyer is an important decision that should not be based solely upon advertisements. Before you choose a law firm, take the time to research them with their state\'s Bar Association. You should also ask for written information about their experience and qualifications. Past results are not indicative of future performance. The images throughout this website are not the attorneys that work for Defense Lawyers, P.A. Rather, they are hired models.', 'option');
}

/**
 * Create sample content
 */
function mydefenselaw_create_sample_content() {
    $created = array();

    // Sample Practice Areas
    $practice_areas = array(
        array(
            'title' => 'Civil Defense Litigation',
            'icon' => 'fas fa-balance-scale',
            'description' => 'Comprehensive legal defense for civil matters including personal injury defense, premises liability, and general litigation.',
        ),
        array(
            'title' => 'Consumer Protection',
            'icon' => 'fas fa-shield-alt',
            'description' => 'Defending businesses and individuals against consumer protection claims, FDCPA, and TCPA matters.',
        ),
        array(
            'title' => 'Bankruptcy Law',
            'icon' => 'fas fa-file-invoice-dollar',
            'description' => 'Expert guidance through Chapter 7, Chapter 13, and Chapter 11 bankruptcy proceedings.',
        ),
        array(
            'title' => 'Contract Law',
            'icon' => 'fas fa-file-contract',
            'description' => 'Contract drafting, review, negotiation, and dispute resolution for businesses of all sizes.',
        ),
        array(
            'title' => 'Family Law',
            'icon' => 'fas fa-users',
            'description' => 'Compassionate representation in divorce, custody, support, and other family matters.',
        ),
        array(
            'title' => 'Real Estate Law',
            'icon' => 'fas fa-home',
            'description' => 'Residential and commercial real estate transactions, disputes, and title issues.',
        ),
    );

    foreach ($practice_areas as $pa) {
        // Check if already exists
        $existing = get_page_by_title($pa['title'], OBJECT, 'practice_area');
        if ($existing) {
            continue;
        }

        $post_id = wp_insert_post(array(
            'post_title' => $pa['title'],
            'post_type' => 'practice_area',
            'post_status' => 'publish',
            'post_content' => '<p>' . $pa['description'] . '</p><p>Our experienced attorneys provide comprehensive legal services in this practice area. Contact us today for a free consultation to discuss your case.</p>',
        ));

        if ($post_id && !is_wp_error($post_id)) {
            if (function_exists('update_field')) {
                update_field('practice_area_icon', $pa['icon'], $post_id);
                update_field('short_description', $pa['description'], $post_id);
                update_field('cta_title', 'Ready to Discuss Your Case?', $post_id);
                update_field('cta_text', 'Contact our experienced attorneys today for a free consultation.', $post_id);
                update_field('cta_button_text', 'Schedule Free Consultation', $post_id);
            }
            $created[] = 'Practice Area: ' . $pa['title'];
        }
    }

    // Sample Attorneys
    $attorneys = array(
        array(
            'name' => 'John Smith, Esq.',
            'title' => 'Managing Partner',
            'credentials' => 'J.D., LL.M.',
            'bio' => 'John Smith is the Managing Partner of Defense Lawyers, P.A., with over 25 years of experience in civil litigation and consumer protection law.',
        ),
        array(
            'name' => 'Sarah Johnson, Esq.',
            'title' => 'Senior Associate',
            'credentials' => 'J.D.',
            'bio' => 'Sarah Johnson focuses her practice on family law and real estate matters, bringing compassionate and skilled representation to every case.',
        ),
    );

    foreach ($attorneys as $att) {
        // Check if already exists
        $existing = get_page_by_title($att['name'], OBJECT, 'attorney');
        if ($existing) {
            continue;
        }

        $post_id = wp_insert_post(array(
            'post_title' => $att['name'],
            'post_type' => 'attorney',
            'post_status' => 'publish',
            'post_content' => '<p>' . $att['bio'] . '</p>',
        ));

        if ($post_id && !is_wp_error($post_id)) {
            if (function_exists('update_field')) {
                update_field('attorney_title', $att['title'], $post_id);
                update_field('credentials', $att['credentials'], $post_id);
                update_field('short_bio', $att['bio'], $post_id);
            }
            $created[] = 'Attorney: ' . $att['name'];
        }
    }

    if (empty($created)) {
        return 'No new content created (items may already exist).';
    }

    return 'Created: ' . implode(', ', $created);
}
