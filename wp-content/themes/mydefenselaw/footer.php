<?php
/**
 * The footer template
 * Pixel-perfect match with source
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

$phone = get_theme_mod('contact_phone', '888.444.0253');
$phone_raw = preg_replace('/[^0-9]/', '', $phone);
?>

    </main><!-- #main-content -->

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-main">
                    <div class="footer-logo">
                        <div class="logo">
                            <a href="<?php echo esc_url(home_url('/')); ?>">
                                <?php if (has_custom_logo()) : ?>
                                    <?php
                                    $custom_logo_id = get_theme_mod('custom_logo');
                                    $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                                    ?>
                                    <img src="<?php echo esc_url($logo[0]); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="footer-logo-image" width="200" height="80" loading="lazy">
                                <?php else : ?>
                                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/logo.png" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="footer-logo-image" width="200" height="80" loading="lazy">
                                <?php endif; ?>
                            </a>
                        </div>
                        <p><?php esc_html_e('Protecting your rights and fighting for your future for over 25 years.', 'mydefenselaw'); ?></p>
                    </div>

                    <div class="footer-links">
                        <div class="footer-column">
                            <h4><?php esc_html_e('Practice Areas', 'mydefenselaw'); ?></h4>
                            <?php
                            wp_nav_menu(array(
                                'theme_location' => 'footer-legal',
                                'menu_class'     => '',
                                'container'      => 'ul',
                                'fallback_cb'    => 'mydefenselaw_footer_legal_fallback',
                            ));
                            ?>
                        </div>

                        <div class="footer-column">
                            <h4><?php esc_html_e('About', 'mydefenselaw'); ?></h4>
                            <?php
                            wp_nav_menu(array(
                                'theme_location' => 'footer-about',
                                'menu_class'     => '',
                                'container'      => 'ul',
                                'fallback_cb'    => 'mydefenselaw_footer_about_fallback',
                            ));
                            ?>
                        </div>

                        <div class="footer-column" itemscope itemtype="https://schema.org/LegalService">
                            <h4><?php esc_html_e('Contact', 'mydefenselaw'); ?></h4>
                            <meta itemprop="name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
                            <ul>
                                <li><i class="fas fa-phone"></i> <a href="tel:<?php echo esc_attr($phone_raw); ?>" itemprop="telephone"><?php echo esc_html($phone); ?></a></li>
                                <li itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span itemprop="streetAddress">1515 N Federal Hwy, Suite 300</span><br>
                                    <span itemprop="addressLocality">Boca Raton</span>,
                                    <span itemprop="addressRegion">FL</span>
                                    <span itemprop="postalCode">33432</span>
                                </li>
                                <li><i class="fas fa-clock"></i> <?php esc_html_e('Available 24/7', 'mydefenselaw'); ?></li>
                                <li><a href="<?php echo esc_url(home_url('/free-consultation/')); ?>"><?php esc_html_e('Free Consultation', 'mydefenselaw'); ?></a></li>
                                <li><a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact Us', 'mydefenselaw'); ?></a></li>
                            </ul>
                            <meta itemprop="priceRange" content="$$">
                            <link itemprop="url" href="<?php echo esc_url(home_url('/')); ?>">
                        </div>
                    </div>
                </div>

                <div class="footer-bottom">
                    <div class="disclaimer">
                        <p><strong><?php esc_html_e('DISCLOSURES:', 'mydefenselaw'); ?></strong> <?php esc_html_e('If you have already retained a lawyer, please disregard this communication. The hiring of a lawyer is an important decision that should not be based solely upon advertisements. Before you choose a law firm, take the time to research them with their state\'s Bar Association. You should also ask for written information about their experience and qualifications. Past results are not indicative of future performance. The images throughout this website are not the attorneys that work for Defense Lawyers, P.A. Rather, they are hired models.', 'mydefenselaw'); ?></p>
                    </div>
                    <div class="copyright">
                        <p>&copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?>. <?php esc_html_e('All rights reserved.', 'mydefenselaw'); ?> | <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>"><?php esc_html_e('Privacy Policy', 'mydefenselaw'); ?></a> | <a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact', 'mydefenselaw'); ?></a></p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <?php
    // Mobile Sticky Bar
    get_template_part('template-parts/global/mobile-sticky-bar');

    // Consultation Modal
    get_template_part('template-parts/global/consultation-modal');
    ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
