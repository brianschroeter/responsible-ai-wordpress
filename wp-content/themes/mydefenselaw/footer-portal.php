<?php
/**
 * Portal Footer Template
 * Minimal footer for client portal pages
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

$phone = get_theme_mod('contact_phone', '888.444.0253');
$phone_raw = preg_replace('/[^0-9]/', '', $phone);
?>

    </main><!-- #portal-main-content -->

    <!-- Portal Footer -->
    <footer class="portal-footer">
        <div class="container">
            <div class="portal-footer-content">
                <div class="footer-contact">
                    <p>
                        <i class="fas fa-phone"></i>
                        <strong>Need Help?</strong>
                        <a href="tel:<?php echo esc_attr($phone_raw); ?>"><?php echo esc_html($phone); ?></a>
                    </p>
                </div>
                <div class="footer-copyright">
                    <p>&copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?>. <?php esc_html_e('All rights reserved.', 'mydefenselaw'); ?></p>
                    <p class="footer-legal-notice">
                        <?php esc_html_e('Confidential client communications. Unauthorized access prohibited.', 'mydefenselaw'); ?>
                    </p>
                </div>
                <div class="footer-links">
                    <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>"><?php esc_html_e('Privacy Policy', 'mydefenselaw'); ?></a>
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact', 'mydefenselaw'); ?></a>
                </div>
            </div>
        </div>
    </footer>

</div><!-- #page -->

<!-- Session Timeout Warning Script -->
<script>
(function() {
    'use strict';

    // Session timeout: 30 minutes of inactivity
    const TIMEOUT_DURATION = 30 * 60 * 1000; // 30 minutes in milliseconds
    const WARNING_DURATION = 5 * 60 * 1000;  // Show warning 5 minutes before timeout

    let timeoutTimer;
    let warningTimer;
    let warningShown = false;

    // Reset timers on user activity
    function resetTimers() {
        clearTimeout(timeoutTimer);
        clearTimeout(warningTimer);

        // Hide warning if shown
        if (warningShown) {
            hideWarning();
        }

        // Set warning timer (25 minutes)
        warningTimer = setTimeout(showWarning, TIMEOUT_DURATION - WARNING_DURATION);

        // Set timeout timer (30 minutes)
        timeoutTimer = setTimeout(handleTimeout, TIMEOUT_DURATION);
    }

    // Show session timeout warning
    function showWarning() {
        if (warningShown) return;

        warningShown = true;

        // Create warning modal
        const modal = document.createElement('div');
        modal.id = 'sessionWarningModal';
        modal.className = 'portal-modal';
        modal.innerHTML = `
            <div class="portal-modal-content">
                <div class="portal-modal-header">
                    <i class="fas fa-clock" style="color: #f59e0b;"></i>
                    <h3>Session Expiring Soon</h3>
                </div>
                <div class="portal-modal-body">
                    <p>Your session will expire in <strong>5 minutes</strong> due to inactivity.</p>
                    <p>Click "Stay Logged In" to continue your session, or you will be automatically logged out.</p>
                </div>
                <div class="portal-modal-footer">
                    <button class="btn btn-secondary" onclick="window.location.href='<?php echo esc_url(wp_logout_url(home_url('/portal/login/'))); ?>'">Logout Now</button>
                    <button class="btn btn-primary" id="stayLoggedInBtn">Stay Logged In</button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Add event listener to "Stay Logged In" button
        document.getElementById('stayLoggedInBtn').addEventListener('click', function() {
            hideWarning();
            resetTimers();
        });

        // Show modal
        setTimeout(function() {
            modal.classList.add('active');
        }, 10);
    }

    // Hide warning modal
    function hideWarning() {
        const modal = document.getElementById('sessionWarningModal');
        if (modal) {
            modal.classList.remove('active');
            setTimeout(function() {
                modal.remove();
            }, 300);
        }
        warningShown = false;
    }

    // Handle session timeout
    function handleTimeout() {
        // Redirect to login with session expired message
        window.location.href = '<?php echo esc_url(add_query_arg('expired', '1', home_url('/portal/login/'))); ?>';
    }

    // Activity event listeners
    const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
    events.forEach(function(event) {
        document.addEventListener(event, resetTimers, true);
    });

    // Initialize timers on page load
    resetTimers();
})();
</script>

<!-- Portal Mobile Menu Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileBtn = document.getElementById('portalMobileMenuBtn');
    const portalMenu = document.getElementById('portalMenu');

    if (mobileBtn && portalMenu) {
        mobileBtn.addEventListener('click', function() {
            const isExpanded = mobileBtn.getAttribute('aria-expanded') === 'true';
            mobileBtn.setAttribute('aria-expanded', !isExpanded);
            mobileBtn.classList.toggle('active');
            portalMenu.classList.toggle('mobile-active');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!mobileBtn.contains(e.target) && !portalMenu.contains(e.target)) {
                mobileBtn.setAttribute('aria-expanded', 'false');
                mobileBtn.classList.remove('active');
                portalMenu.classList.remove('mobile-active');
            }
        });
    }
});
</script>

<?php wp_footer(); ?>

</body>
</html>
