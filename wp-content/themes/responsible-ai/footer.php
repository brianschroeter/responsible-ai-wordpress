<?php
/**
 * Footer Template
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$org_info = responsibleai_get_organization_info();
$social = responsibleai_get_social_links();
?>
</main><!-- #main-content -->

<footer class="site-footer">
    <div class="container">
        <!-- Footer Top -->
        <div class="footer-top">
            <div class="footer-grid">
                <!-- About Column -->
                <div class="footer-col footer-col--about">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="footer-logo">
                        <?php if (has_custom_logo()) : ?>
                            <?php the_custom_logo(); ?>
                        <?php else : ?>
                            <span class="footer-logo__text"><?php bloginfo('name'); ?></span>
                        <?php endif; ?>
                    </a>
                    <p class="footer-tagline">
                        <?php echo esc_html($org_info['tagline']); ?>
                    </p>
                    <!-- Social Links -->
                    <div class="footer-social">
                        <?php if ($social['linkedin']) : ?>
                            <a href="<?php echo esc_url($social['linkedin']); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('LinkedIn', 'responsible-ai'); ?>">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ($social['twitter']) : ?>
                            <a href="<?php echo esc_url($social['twitter']); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('Twitter', 'responsible-ai'); ?>">
                                <i class="fab fa-x-twitter"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ($social['youtube']) : ?>
                            <a href="<?php echo esc_url($social['youtube']); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('YouTube', 'responsible-ai'); ?>">
                                <i class="fab fa-youtube"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ($social['github']) : ?>
                            <a href="<?php echo esc_url($social['github']); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('GitHub', 'responsible-ai'); ?>">
                                <i class="fab fa-github"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- About Menu -->
                <div class="footer-col">
                    <h4 class="footer-col__title"><?php esc_html_e('About', 'responsible-ai'); ?></h4>
                    <?php
                    if (has_nav_menu('footer-about')) {
                        wp_nav_menu(array(
                            'theme_location' => 'footer-about',
                            'menu_class'     => 'footer-menu',
                            'container'      => false,
                            'depth'          => 1,
                            'fallback_cb'    => 'responsibleai_footer_about_fallback',
                        ));
                    } else {
                        responsibleai_footer_about_fallback();
                    }
                    ?>
                </div>

                <!-- Resources Menu -->
                <div class="footer-col">
                    <h4 class="footer-col__title"><?php esc_html_e('Resources', 'responsible-ai'); ?></h4>
                    <?php
                    if (has_nav_menu('footer-resources')) {
                        wp_nav_menu(array(
                            'theme_location' => 'footer-resources',
                            'menu_class'     => 'footer-menu',
                            'container'      => false,
                            'depth'          => 1,
                            'fallback_cb'    => 'responsibleai_footer_resources_fallback',
                        ));
                    } else {
                        responsibleai_footer_resources_fallback();
                    }
                    ?>
                </div>

                <!-- Community Menu -->
                <div class="footer-col">
                    <h4 class="footer-col__title"><?php esc_html_e('Community', 'responsible-ai'); ?></h4>
                    <?php
                    if (has_nav_menu('footer-community')) {
                        wp_nav_menu(array(
                            'theme_location' => 'footer-community',
                            'menu_class'     => 'footer-menu',
                            'container'      => false,
                            'depth'          => 1,
                            'fallback_cb'    => 'responsibleai_footer_community_fallback',
                        ));
                    } else {
                        responsibleai_footer_community_fallback();
                    }
                    ?>
                </div>

                <!-- Legal Menu -->
                <div class="footer-col">
                    <h4 class="footer-col__title"><?php esc_html_e('Legal', 'responsible-ai'); ?></h4>
                    <?php
                    if (has_nav_menu('footer-legal')) {
                        wp_nav_menu(array(
                            'theme_location' => 'footer-legal',
                            'menu_class'     => 'footer-menu',
                            'container'      => false,
                            'depth'          => 1,
                            'fallback_cb'    => 'responsibleai_footer_legal_fallback',
                        ));
                    } else {
                        responsibleai_footer_legal_fallback();
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="footer-bottom__inner">
                <p class="footer-copyright">
                    &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>.
                    <?php esc_html_e('All rights reserved.', 'responsible-ai'); ?>
                </p>
                <p class="footer-credits">
                    <?php esc_html_e('Building trust in AI systems through certification, standards, and community.', 'responsible-ai'); ?>
                </p>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
