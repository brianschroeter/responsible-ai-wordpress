<?php
/**
 * Mobile Sticky Bar Template Part
 * Pixel-perfect match with source
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

$phone = get_theme_mod('contact_phone', '888.444.0253');
$phone_raw = preg_replace('/[^0-9]/', '', $phone);
?>

<div class="mobile-sticky-bar">
    <a href="tel:<?php echo esc_attr($phone_raw); ?>" class="sticky-btn call-btn">
        <i class="fas fa-phone"></i>
        <span><?php esc_html_e('Call Now', 'mydefenselaw'); ?></span>
    </a>
    <button class="sticky-btn consult-btn consultation-trigger" id="modalTriggerSticky">
        <i class="fas fa-comments"></i>
        <span><?php esc_html_e('Free Consult', 'mydefenselaw'); ?></span>
    </button>
</div>
