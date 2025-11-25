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
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1><?php echo esc_html(get_the_title()); ?></h1>
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>">Home</a> / <span aria-current="page"><?php echo esc_html(get_the_title()); ?></span>
            </nav>
        </div>
    </div>
</section>

<!-- Main Content -->
<main class="main-content">
    <div class="container">

        <div class="about-section">
            <h2>About Defense Lawyers, P.A.</h2>

            <p>Defense Lawyers, P.A. offers motivated, experienced legal counsel and representation in a variety of practice areas. Our approach has resulted in stable, long-term relationships with our clients, which are based on the firm's prompt, efficient and high quality service to reach shared objectives. To employ the wisdom and skill of our practiced attorneys for your legal needs, <a href="<?php echo esc_url(home_url('/contact/')); ?>">contact us</a> today!</p>

            <h3>Mission Statement</h3>

            <p>Defense Lawyers, P.A. is committed to providing the absolute highest quality of legal advice and advocacy. We recognize that our success must be earned every day to maintain the firm and long standing relationships that we enjoy with our clients. To achieve this end, our firm is committed to the following principles:</p>

            <ul>
                <li><strong>Experience &amp; Knowledge</strong> - Our attorneys are passionate about the law with decades of combined experience</li>
                <li><strong>Drive &amp; Dedication</strong> - We are committed to delivering effective, efficient, and quality legal services</li>
                <li><strong>Courteous Service</strong> - Defense Lawyers, P.A. has an experienced and courteous staff to take you through the entire legal process</li>
                <li><strong>Focus on the client's specific needs</strong> - We focus on acquiring the results that our clients want</li>
                <li><strong>Acquiring the results that our clients want</strong> - Our track record speaks for itself</li>
            </ul>

            <h3 id="attorneys">Meet The Attorneys</h3>

            <div class="attorney">
                <h4>Lee Stein, Esq. - Admitted in FL</h4>
                <p>Attorney Lee Stein has been an attorney for over 20 years. He graduated the University of Florida College of Law and is committed to providing professionalism, experience, dedication, service, and results for the firm's clients.</p>
            </div>

            <div class="attorney">
                <h4>Andre Sailers, Esq. - Admitted in GA</h4>
                <p>Attorney Andre Sailers has been an attorney for over 32 years. He graduated the University of Iowa and holds firm to the creed of "pursuing justice while offering the highest quality legal representation and superior client satisfaction."</p>
            </div>

            <div class="attorney">
                <h4>Wardell Huff, Esq. - Admitted in DC, NJ, NY, Dist. of MD</h4>
                <p>Attorney Wardell Huff has been an attorney for over 20 years. He is graduate of Michigan State University College of Law and has held membership within the National Association of Consumer Bankruptcy Attorneys.</p>
            </div>

            <h3>Our Attorneys Bring Passion and Dedication to Each Case</h3>

            <p>Our lawyers bring unique specialties and the capacity to excel to every client we represent and to every case we take. At Defense Lawyers, P.A., we have a reputation among our clients and our peers for committed service and highly effective litigation. Because of our success in the courtroom, we receive a lot of referrals from former clients, as well as other legal firms. We enjoy our work and are truly proud when we help our clients achieve the results they desire.</p>

            <div class="cta-section" style="margin-top: 3rem;">
                <h3>Experience, Dedication, Service, and Results</h3>
                <p>Defense Lawyers, P.A. prides itself on professionalism, experience, dedication, service, and results.</p>
                <div class="cta-buttons">
                    <a href="tel:<?php echo esc_attr($phone_raw); ?>" class="btn btn-primary">
                        <i class="fas fa-phone" aria-hidden="true"></i> Call <?php echo esc_html($phone); ?>
                    </a>
                    <button class="btn btn-outline consultation-trigger" id="modalTriggerAbout" aria-label="Request a free legal consultation">
                        <i class="fas fa-calendar-check" aria-hidden="true"></i> Free Consultation
                    </button>
                </div>
            </div>
        </div>

    </div>
</main>

<?php
get_footer();
