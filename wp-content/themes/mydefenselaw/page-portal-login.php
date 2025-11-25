<?php
/**
 * Template Name: Portal - Login
 * Login page for client portal
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

// If already logged in and is client, redirect to dashboard
if (is_user_logged_in()) {
    $user = wp_get_current_user();
    if (in_array('client', $user->roles)) {
        wp_redirect(home_url('/portal/dashboard/'));
        exit;
    }
}

// Get URL parameters for messages
$rate_limited = isset($_GET['limited']) && $_GET['limited'] === '1';
$session_expired = isset($_GET['expired']) && $_GET['expired'] === '1';
$logout_success = isset($_GET['logout']) && $_GET['logout'] === 'success';
$login_error = isset($_GET['login']) && $_GET['login'] === 'failed';

get_header();
?>

<!-- Portal Login Page -->
<section class="portal-login-section">
    <div class="container">
        <div class="login-wrapper">
            <div class="login-card">
                <div class="login-header">
                    <div class="logo-login">
                        <?php if (has_custom_logo()) : ?>
                            <?php
                            $custom_logo_id = get_theme_mod('custom_logo');
                            $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                            ?>
                            <img src="<?php echo esc_url($logo[0]); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" width="200" height="80">
                        <?php else : ?>
                            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/logo.png" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" width="200" height="80">
                        <?php endif; ?>
                    </div>
                    <h1><?php esc_html_e('Client Portal Login', 'mydefenselaw'); ?></h1>
                    <p class="login-subtitle"><?php esc_html_e('Access your case information securely', 'mydefenselaw'); ?></p>
                </div>

                <?php if ($rate_limited) : ?>
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong><?php esc_html_e('Too Many Login Attempts', 'mydefenselaw'); ?></strong>
                        <p><?php esc_html_e('For security purposes, your account has been temporarily locked. Please wait 15 minutes before trying again, or contact us for assistance.', 'mydefenselaw'); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($session_expired) : ?>
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle"></i>
                        <strong><?php esc_html_e('Session Expired', 'mydefenselaw'); ?></strong>
                        <p><?php esc_html_e('Your session has expired due to inactivity. Please log in again.', 'mydefenselaw'); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($logout_success) : ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle"></i>
                        <strong><?php esc_html_e('Logged Out Successfully', 'mydefenselaw'); ?></strong>
                        <p><?php esc_html_e('You have been securely logged out.', 'mydefenselaw'); ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($login_error) : ?>
                    <div class="alert alert-error" role="alert">
                        <i class="fas fa-times-circle"></i>
                        <strong><?php esc_html_e('Login Failed', 'mydefenselaw'); ?></strong>
                        <p><?php esc_html_e('Invalid email or password. Please try again.', 'mydefenselaw'); ?></p>
                    </div>
                <?php endif; ?>

                <div class="login-form-wrapper">
                    <form name="loginform" id="loginform" action="<?php echo esc_url(wp_login_url()); ?>" method="post">
                        <div class="form-group">
                            <label for="user_login">
                                <i class="fas fa-envelope"></i>
                                <?php esc_html_e('Email Address', 'mydefenselaw'); ?>
                            </label>
                            <input type="email" name="log" id="user_login" class="form-control" placeholder="<?php esc_attr_e('Enter your email', 'mydefenselaw'); ?>" required autocomplete="username">
                        </div>

                        <div class="form-group">
                            <label for="user_pass">
                                <i class="fas fa-lock"></i>
                                <?php esc_html_e('Password', 'mydefenselaw'); ?>
                            </label>
                            <input type="password" name="pwd" id="user_pass" class="form-control" placeholder="<?php esc_attr_e('Enter your password', 'mydefenselaw'); ?>" required autocomplete="current-password">
                        </div>

                        <div class="form-group form-options">
                            <label class="checkbox-label">
                                <input type="checkbox" name="rememberme" id="rememberme" value="forever">
                                <span class="checkmark"></span>
                                <?php esc_html_e('Remember me', 'mydefenselaw'); ?>
                            </label>
                            <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="forgot-password">
                                <?php esc_html_e('Forgot password?', 'mydefenselaw'); ?>
                            </a>
                        </div>

                        <input type="hidden" name="redirect_to" value="<?php echo esc_attr(home_url('/portal/dashboard/')); ?>">

                        <button type="submit" name="wp-submit" id="wp-submit" class="btn btn-primary btn-full">
                            <i class="fas fa-sign-in-alt"></i>
                            <?php esc_html_e('Login to Portal', 'mydefenselaw'); ?>
                        </button>
                    </form>
                </div>

                <div class="login-footer">
                    <p>
                        <i class="fas fa-shield-alt"></i>
                        <?php esc_html_e('Your connection is secure and encrypted', 'mydefenselaw'); ?>
                    </p>
                </div>

                <div class="login-help">
                    <h3><?php esc_html_e('Need Help?', 'mydefenselaw'); ?></h3>
                    <p><?php esc_html_e('If you\'re having trouble logging in or need to set up your portal access, please contact us:', 'mydefenselaw'); ?></p>
                    <p>
                        <i class="fas fa-phone"></i>
                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9]/', '', get_theme_mod('contact_phone', '888.444.0253'))); ?>">
                            <?php echo esc_html(get_theme_mod('contact_phone', '888.444.0253')); ?>
                        </a>
                    </p>
                </div>
            </div>

            <div class="login-info-panel">
                <h2><?php esc_html_e('Welcome to Your Client Portal', 'mydefenselaw'); ?></h2>
                <div class="info-feature">
                    <i class="fas fa-folder-open"></i>
                    <h3><?php esc_html_e('Access Documents', 'mydefenselaw'); ?></h3>
                    <p><?php esc_html_e('View and download case documents, contracts, and legal filings anytime.', 'mydefenselaw'); ?></p>
                </div>
                <div class="info-feature">
                    <i class="fas fa-comments"></i>
                    <h3><?php esc_html_e('Secure Messaging', 'mydefenselaw'); ?></h3>
                    <p><?php esc_html_e('Communicate directly with your legal team through encrypted messages.', 'mydefenselaw'); ?></p>
                </div>
                <div class="info-feature">
                    <i class="fas fa-briefcase"></i>
                    <h3><?php esc_html_e('Case Updates', 'mydefenselaw'); ?></h3>
                    <p><?php esc_html_e('Stay informed with real-time updates on your case status and court dates.', 'mydefenselaw'); ?></p>
                </div>
                <div class="info-feature">
                    <i class="fas fa-calendar-alt"></i>
                    <h3><?php esc_html_e('Court Calendar', 'mydefenselaw'); ?></h3>
                    <p><?php esc_html_e('Never miss an important date with your personalized legal calendar.', 'mydefenselaw'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
?>
