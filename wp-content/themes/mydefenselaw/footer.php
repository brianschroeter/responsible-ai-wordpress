<?php if (!is_front_page() && !is_home()) : ?></div></main><?php endif; ?>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-main">
                <div class="footer-logo">
                    <div class="logo">
                        <?php if (has_custom_logo()) : ?>
                            <a href="<?php echo esc_url(home_url('/')); ?>">
                                <?php
                                $custom_logo_id = get_theme_mod('custom_logo');
                                $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                                if (has_custom_logo()) {
                                    echo '<img src="' . esc_url($logo[0]) . '" alt="' . get_bloginfo('name') . ' - Attorneys at Law" class="footer-logo-image">';
                                }
                                ?>
                            </a>
                        <?php else : ?>
                            <a href="<?php echo esc_url(home_url('/')); ?>">
                                <img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="<?php bloginfo('name'); ?> - Attorneys at Law" class="footer-logo-image">
                            </a>
                        <?php endif; ?>
                    </div>
                    <p>Protecting your rights and fighting for your future for over 25 years.</p>
                </div>

                <div class="footer-links">
                    <div class="footer-column">
                        <h4>Practice Areas</h4>
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'footer-practice-areas',
                            'container'      => false,
                            'menu_class'     => '',
                            'fallback_cb'    => false,
                        ));
                        ?>
                    </div>

                    <div class="footer-column">
                        <h4>About</h4>
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'footer-about',
                            'container'      => false,
                            'menu_class'     => '',
                            'fallback_cb'    => false,
                        ));
                        ?>
                    </div>

                    <div class="footer-column">
                        <h4>Contact</h4>
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'footer-contact',
                            'container'      => false,
                            'menu_class'     => '',
                            'fallback_cb'    => false,
                            'walker'         => new class extends Walker_Nav_Menu {
                                function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
                                    $output .= '<li>';

                                    // Add icons for specific items
                                    $icon = '';
                                    if (strpos($item->url, 'tel:') === 0) {
                                        $icon = '<i class="fas fa-phone"></i> ';
                                    } elseif (strpos($item->title, 'Boca Raton') !== false) {
                                        $icon = '<i class="fas fa-map-marker-alt"></i> ';
                                    }

                                    $attributes = !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

                                    $output .= $icon;
                                    if (!empty($item->url)) {
                                        $output .= '<a' . $attributes . '>';
                                    }
                                    $output .= apply_filters('the_title', $item->title, $item->ID);
                                    if (!empty($item->url)) {
                                        $output .= '</a>';
                                    }
                                }
                            }
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="disclaimer">
                    <p><strong>DISCLOSURES:</strong> If you have already retained a lawyer, please disregard this communication. The hiring of a lawyer is an important decision that should not be based solely upon advertisements. Before you choose a law firm, take the time to research them with their state's Bar Association. You should also ask for written information about their experience and qualifications. Past results are not indicative of future performance. The images throughout this website are not the attorneys that work for Defense Lawyers, P.A. Rather, they are hired models.</p>
                </div>
                <div class="copyright">
                    <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved. | <a href="<?php echo get_privacy_policy_url(); ?>">Privacy Policy</a> | <a href="<?php echo esc_url(home_url('/contact-us')); ?>">Contact</a></p>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php get_template_part('template-parts/global/mobile-sticky-bar'); ?>
<?php get_template_part('template-parts/global/consultation-modal'); ?>

<?php wp_footer(); ?>
</body>
</html>
