<?php
/**
 * Template Name: About The Firm
 *
 * Custom page template for the About The Firm page
 * Matches the live site at mydefenselaw.com/about_the_firm.php
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

get_header();

// Get phone number from theme options
$phone = get_theme_mod('contact_phone', '888.444.0253');
$phone_raw = preg_replace('/[^0-9]/', '', $phone);
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1><?php echo esc_html(get_the_title()); ?></h1>
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
                <span aria-hidden="true"> / </span>
                <span aria-current="page"><?php echo esc_html(get_the_title()); ?></span>
            </nav>
        </div>
    </div>
</div>

<!-- Main Content -->
<main class="main-content">
    <div class="container">
        <div class="about-section">

            <!-- About Defense Lawyers, P.A. -->
            <h2>About Defense Lawyers, P.A.</h2>

            <p>
                Defense Lawyers, P.A. is a full service civil firm based out of Boca Raton, Florida.
                For more information regarding our firm please <a href="<?php echo esc_url(home_url('/contact/')); ?>">contact</a> us.
            </p>

            <!-- Mission Statement -->
            <h3>Mission Statement</h3>

            <p>
                Defense Lawyers, P.A. is committed to providing our clients with professional, motivated,
                experienced legal counsel while offering prompt and effective service. We have a passion for
                practicing law and our attorneys are dedicated to pursuing justice in our many practice areas.
            </p>

            <p>Defense Lawyers, P.A. prides itself on the following qualities:</p>

            <ul>
                <li>
                    <strong>Experience &amp; Knowledge</strong> - Our attorneys are passionate about the law with decades of combined experience
                </li>
                <li>
                    <strong>Drive &amp; Dedication</strong> - We are committed to delivering effective, efficient, and quality legal services
                </li>
                <li>
                    <strong>Courteous Service</strong> - Defense Lawyers, P.A. has an experienced and courteous staff to take you through the entire legal process
                </li>
                <li>
                    <strong>Focus on the client's specific needs</strong> - We focus on acquiring the results that our clients want
                </li>
                <li>
                    <strong>Acquiring the results that our clients want</strong> - Our track record speaks for itself
                </li>
            </ul>

            <!-- Attorneys Section -->
            <h3>Our Attorneys</h3>

            <div class="attorney">
                <h4>Lee Stein, Esq. - Admitted in FL</h4>
                <p>
                    Attorney Lee Stein has been an attorney for over 20 years. He graduated the University of
                    Florida College of Law and is committed to providing professionalism, experience, dedication,
                    service, and results for the firm's clients.
                </p>
            </div>

            <div class="attorney">
                <h4>Andre Sailers, Esq. - Admitted in GA</h4>
                <p>
                    Attorney Andre Sailers has been an attorney for over 32 years. He graduated the University
                    of Iowa and holds firm to the creed of "pursuing justice while offering the highest quality
                    legal representation and superior client satisfaction."
                </p>
            </div>

            <div class="attorney">
                <h4>Wardell Huff, Esq. - Admitted in DC, NJ, NY, Dist. of MD</h4>
                <p>
                    Attorney Wardell Huff has been an attorney for over 20 years. He is graduate of Michigan
                    State University College of Law and has held membership within the National Association of
                    Consumer Bankruptcy Attorneys.
                </p>
            </div>

            <!-- Our Attorneys Bring Passion Section -->
            <h3>Our Attorneys Bring Passion, Experience and Results</h3>

            <p>
                The attorneys of Defense Lawyers, P.A. bring decades of combined legal experience in various
                fields of civil law. Our attorneys are committed to providing our clients with professional
                services, dedication, and results.
            </p>

            <!-- CTA Section -->
            <div class="about-cta-section">
                <h3>Experience, Dedication, Service, and Results</h3>

                <p>
                    Defense Lawyers, P.A. prides itself on professionalism, experience, dedication, service,
                    and results.
                </p>

                <div class="cta-buttons">
                    <a href="tel:<?php echo esc_attr($phone_raw); ?>" class="btn btn-primary">
                        <i class="fas fa-phone"></i> Call <?php echo esc_html($phone); ?>
                    </a>
                    <button class="btn btn-outline consultation-trigger" id="modalTriggerAbout" aria-label="Request a free legal consultation">
                        <i class="fas fa-calendar-check"></i> Free Consultation
                    </button>
                </div>
            </div>

        </div>
    </div>
</main>

<?php
get_footer();
