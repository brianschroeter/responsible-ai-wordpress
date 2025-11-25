<?php
/**
 * Top Bar Template Part
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */
?>

<div class="top-bar">
    <div class="container">
        <div class="top-bar-inner">
            <div class="top-bar-left">
                <span class="top-bar-item">
                    <i class="fas fa-phone"></i>
                    <?php mydefenselaw_phone(); ?>
                </span>
                <span class="top-bar-item">
                    <i class="fas fa-envelope"></i>
                    <?php mydefenselaw_email(); ?>
                </span>
            </div>
            <div class="top-bar-right">
                <?php mydefenselaw_social_links(); ?>
            </div>
        </div>
    </div>
</div>
