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
                <span> / </span>
                <span aria-current="page"><?php echo esc_html(get_the_title()); ?></span>
            </nav>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="about-page-content">
    <div class="container">
        <div class="content-columns">
            <!-- Main Content Column -->
            <div class="about-main-content">

                <!-- Firm Overview Section -->
                <section class="firm-overview">
                    <h2>Experienced Legal Representation You Can Trust</h2>
                    <p class="lead-text">
                        Defense Lawyers, P.A. is dedicated to providing our clients with motivated, experienced legal counsel.
                        Our team of skilled attorneys is committed to delivering prompt, efficient, and high-quality service
                        to every client we represent.
                    </p>
                    <p>
                        With decades of combined experience, we understand the challenges our clients face and work tirelessly
                        to protect their rights and achieve the best possible outcomes. Whether you're facing a complex legal
                        matter or need guidance on a straightforward issue, our team is here to help.
                    </p>
                </section>

                <!-- Mission Statement Section -->
                <section class="mission-statement">
                    <h2>Our Commitment to Excellence</h2>
                    <p>
                        At Defense Lawyers, P.A., we are guided by core values that shape every aspect of our practice:
                    </p>

                    <div class="core-values">
                        <div class="value-item">
                            <div class="value-icon">
                                <i class="fas fa-balance-scale"></i>
                            </div>
                            <div class="value-content">
                                <h3>Experience & Knowledge</h3>
                                <p>
                                    Our attorneys bring decades of combined legal experience to every case. We stay current
                                    with the latest legal developments and leverage our deep understanding of the law to
                                    provide superior representation.
                                </p>
                            </div>
                        </div>

                        <div class="value-item">
                            <div class="value-icon">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <div class="value-content">
                                <h3>Drive & Dedication</h3>
                                <p>
                                    We are passionate about defending our clients' rights. Our team works tirelessly to
                                    provide effective, efficient, and quality legal services that exceed expectations.
                                </p>
                            </div>
                        </div>

                        <div class="value-item">
                            <div class="value-icon">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <div class="value-content">
                                <h3>Courteous Service</h3>
                                <p>
                                    Our experienced and professional staff treats every client with respect and dignity.
                                    We understand that legal matters can be stressful, and we strive to make the process
                                    as smooth as possible.
                                </p>
                            </div>
                        </div>

                        <div class="value-item">
                            <div class="value-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="value-content">
                                <h3>Client-Focused Approach</h3>
                                <p>
                                    Your goals are our goals. We take the time to understand your unique situation and
                                    work collaboratively with you to develop strategies aimed at acquiring your desired results.
                                </p>
                            </div>
                        </div>

                        <div class="value-item">
                            <div class="value-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="value-content">
                                <h3>Results-Oriented Track Record</h3>
                                <p>
                                    We measure our success by the outcomes we achieve for our clients. Our proven track
                                    record demonstrates our ability to deliver favorable results across a wide range of
                                    legal matters.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Meet The Attorneys Section -->
                <section class="meet-attorneys">
                    <h2>Meet Our Attorneys</h2>
                    <p>
                        Our team consists of highly skilled and experienced attorneys who are dedicated to providing
                        exceptional legal representation.
                    </p>

                    <div class="attorney-cards">
                        <!-- Attorney 1: Lee Stein -->
                        <div class="attorney-card">
                            <div class="attorney-info">
                                <h3>Lee Stein, Esq.</h3>
                                <div class="attorney-credentials">
                                    <p class="bar-admission">
                                        <i class="fas fa-gavel"></i>
                                        <strong>Bar Admission:</strong> Florida
                                    </p>
                                    <p class="experience">
                                        <i class="fas fa-briefcase"></i>
                                        <strong>Experience:</strong> 20+ Years
                                    </p>
                                    <p class="education">
                                        <i class="fas fa-graduation-cap"></i>
                                        <strong>Education:</strong> University of Florida College of Law
                                    </p>
                                </div>
                                <p class="attorney-bio">
                                    Lee Stein brings over two decades of legal expertise to Defense Lawyers, P.A.
                                    His extensive experience in various areas of law allows him to provide comprehensive
                                    legal counsel to clients facing complex legal challenges.
                                </p>
                            </div>
                        </div>

                        <!-- Attorney 2: Andre Sailers -->
                        <div class="attorney-card">
                            <div class="attorney-info">
                                <h3>Andre Sailers, Esq.</h3>
                                <div class="attorney-credentials">
                                    <p class="bar-admission">
                                        <i class="fas fa-gavel"></i>
                                        <strong>Bar Admission:</strong> Georgia
                                    </p>
                                    <p class="experience">
                                        <i class="fas fa-briefcase"></i>
                                        <strong>Experience:</strong> 32+ Years
                                    </p>
                                    <p class="education">
                                        <i class="fas fa-graduation-cap"></i>
                                        <strong>Education:</strong> University of Iowa
                                    </p>
                                </div>
                                <p class="attorney-bio">
                                    With more than three decades of legal practice, Andre Sailers is one of the most
                                    experienced attorneys on our team. His deep understanding of the law and commitment
                                    to client service make him an invaluable asset to our firm.
                                </p>
                            </div>
                        </div>

                        <!-- Attorney 3: Wardell Huff -->
                        <div class="attorney-card">
                            <div class="attorney-info">
                                <h3>Wardell Huff, Esq.</h3>
                                <div class="attorney-credentials">
                                    <p class="bar-admission">
                                        <i class="fas fa-gavel"></i>
                                        <strong>Bar Admission:</strong> DC, NJ, NY, MD
                                    </p>
                                    <p class="experience">
                                        <i class="fas fa-briefcase"></i>
                                        <strong>Experience:</strong> 20+ Years
                                    </p>
                                    <p class="education">
                                        <i class="fas fa-graduation-cap"></i>
                                        <strong>Education:</strong> Michigan State University College of Law
                                    </p>
                                </div>
                                <p class="attorney-bio">
                                    Wardell Huff is admitted to practice in multiple jurisdictions, bringing over 20 years
                                    of diverse legal experience. His multi-state practice allows him to serve clients
                                    across various regions with exceptional legal representation.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Contact CTA Section -->
                <section class="about-cta">
                    <div class="cta-content">
                        <h2>Ready to Get Started?</h2>
                        <p>
                            Contact us today for a free consultation. Our experienced attorneys are ready to review
                            your case and provide you with the guidance you need.
                        </p>
                        <div class="cta-buttons">
                            <a href="tel:<?php echo esc_attr($phone_raw); ?>" class="btn btn-primary">
                                <i class="fas fa-phone"></i> Call <?php echo esc_html($phone); ?>
                            </a>
                            <button class="btn btn-outline consultation-trigger" id="modalTriggerAbout">
                                <i class="fas fa-calendar-check"></i> Free Consultation
                            </button>
                        </div>
                    </div>
                </section>

            </div>

            <!-- Sidebar -->
            <aside class="about-sidebar">
                <div class="sidebar-widget practice-areas-widget">
                    <h3>Practice Areas</h3>
                    <ul class="practice-areas-list">
                        <li>
                            <a href="<?php echo esc_url(home_url('/practice-areas/civil-defense-litigation/')); ?>">
                                <i class="fas fa-chevron-right"></i> Civil Defense Litigation
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/practice-areas/consumer-protection/')); ?>">
                                <i class="fas fa-chevron-right"></i> Consumer Protection
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/practice-areas/bankruptcy-law/')); ?>">
                                <i class="fas fa-chevron-right"></i> Bankruptcy Law
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/practice-areas/contract-law/')); ?>">
                                <i class="fas fa-chevron-right"></i> Contract Law
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/practice-areas/family-law/')); ?>">
                                <i class="fas fa-chevron-right"></i> Family Law
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/practice-areas/real-estate-law/')); ?>">
                                <i class="fas fa-chevron-right"></i> Real Estate Law
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/practice-areas/landlord-tenant/')); ?>">
                                <i class="fas fa-chevron-right"></i> Landlord/Tenant Issues
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/practice-areas/intellectual-property/')); ?>">
                                <i class="fas fa-chevron-right"></i> Intellectual Property
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url(home_url('/practice-areas/traffic-tickets/')); ?>">
                                <i class="fas fa-chevron-right"></i> Traffic Tickets
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="sidebar-widget contact-widget">
                    <h3>Contact Us</h3>
                    <div class="contact-info">
                        <p class="widget-phone">
                            <i class="fas fa-phone"></i>
                            <a href="tel:<?php echo esc_attr($phone_raw); ?>"><?php echo esc_html($phone); ?></a>
                        </p>
                        <p class="widget-location">
                            <i class="fas fa-map-marker-alt"></i>
                            Boca Raton, FL
                        </p>
                        <p class="widget-hours">
                            <i class="fas fa-clock"></i>
                            Available 24/7
                        </p>
                    </div>
                    <button class="btn btn-primary btn-block consultation-trigger">
                        Free Consultation
                    </button>
                </div>

                <div class="sidebar-widget emergency-widget">
                    <div class="emergency-content">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h4>Emergency?</h4>
                        <p>Call now for immediate help</p>
                        <a href="tel:<?php echo esc_attr($phone_raw); ?>" class="emergency-phone">
                            <?php echo esc_html($phone); ?>
                        </a>
                    </div>
                </div>
            </aside>

        </div>
    </div>
</div>

<style>
/* About Page Specific Styles */
.about-page-content {
    padding: 3rem 0;
    background: #f8fafc;
}

.about-main-content {
    background: white;
    padding: 3rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

/* Firm Overview */
.firm-overview {
    margin-bottom: 3rem;
}

.firm-overview h2 {
    color: #1a365d;
    font-size: 2.25rem;
    margin-bottom: 1.5rem;
    border-bottom: 3px solid #dc2626;
    padding-bottom: 0.75rem;
}

.firm-overview .lead-text {
    font-size: 1.15rem;
    color: #1a365d;
    font-weight: 600;
    margin-bottom: 1.5rem;
    line-height: 1.8;
}

.firm-overview p {
    color: #374151;
    line-height: 1.8;
    margin-bottom: 1rem;
}

/* Mission Statement */
.mission-statement {
    margin-bottom: 3rem;
}

.mission-statement h2 {
    color: #1a365d;
    font-size: 2.25rem;
    margin-bottom: 1.5rem;
    border-bottom: 3px solid #dc2626;
    padding-bottom: 0.75rem;
}

.mission-statement > p {
    color: #374151;
    font-size: 1.05rem;
    margin-bottom: 2rem;
    line-height: 1.8;
}

/* Core Values */
.core-values {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.value-item {
    display: flex;
    gap: 1.5rem;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 8px;
    border-left: 4px solid #2563eb;
    transition: all 0.3s ease;
}

.value-item:hover {
    background: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transform: translateX(5px);
}

.value-icon {
    flex-shrink: 0;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #1a365d 0%, #2563eb 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.value-icon i {
    font-size: 1.5rem;
    color: white;
}

.value-content h3 {
    color: #1a365d;
    font-size: 1.35rem;
    margin-bottom: 0.75rem;
}

.value-content p {
    color: #374151;
    line-height: 1.7;
    margin: 0;
}

/* Meet Attorneys Section */
.meet-attorneys {
    margin-bottom: 3rem;
}

.meet-attorneys h2 {
    color: #1a365d;
    font-size: 2.25rem;
    margin-bottom: 1.5rem;
    border-bottom: 3px solid #dc2626;
    padding-bottom: 0.75rem;
}

.meet-attorneys > p {
    color: #374151;
    font-size: 1.05rem;
    margin-bottom: 2rem;
    line-height: 1.8;
}

.attorney-cards {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.attorney-card {
    background: #f8fafc;
    border-radius: 8px;
    padding: 2rem;
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
}

.attorney-card:hover {
    background: white;
    border-color: #2563eb;
    box-shadow: 0 4px 20px rgba(37, 99, 235, 0.15);
}

.attorney-info h3 {
    color: #1a365d;
    font-size: 1.75rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.attorney-credentials {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: white;
    border-radius: 6px;
}

.attorney-credentials p {
    margin: 0;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.95rem;
}

.attorney-credentials i {
    color: #dc2626;
    width: 20px;
    text-align: center;
}

.attorney-credentials strong {
    color: #1a365d;
    min-width: 120px;
}

.attorney-bio {
    color: #374151;
    line-height: 1.7;
    margin: 0;
}

/* About CTA Section */
.about-cta {
    background: linear-gradient(135deg, #1a365d 0%, #2563eb 100%);
    padding: 3rem;
    border-radius: 8px;
    text-align: center;
    color: white;
}

.about-cta h2 {
    color: white;
    font-size: 2rem;
    margin-bottom: 1rem;
    border: none;
    padding: 0;
}

.about-cta p {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    opacity: 0.95;
}

.cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

/* Sidebar Styles */
.about-sidebar {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.sidebar-widget {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.sidebar-widget h3 {
    color: #1a365d;
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #dc2626;
}

/* Practice Areas List */
.practice-areas-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.practice-areas-list li {
    margin-bottom: 0.75rem;
}

.practice-areas-list a {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    color: #374151;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.practice-areas-list a:hover {
    background: #f8fafc;
    color: #dc2626;
    transform: translateX(5px);
}

.practice-areas-list i {
    color: #dc2626;
    font-size: 0.75rem;
}

/* Contact Widget */
.contact-widget .contact-info {
    margin-bottom: 1.5rem;
}

.contact-widget p {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    color: #374151;
}

.contact-widget i {
    color: #dc2626;
    width: 20px;
    text-align: center;
}

.contact-widget a {
    color: #1a365d;
    text-decoration: none;
    font-weight: 600;
}

.contact-widget a:hover {
    color: #dc2626;
}

.btn-block {
    width: 100%;
}

/* Emergency Widget */
.emergency-widget {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
    text-align: center;
}

.emergency-widget h3 {
    color: white;
    border: none;
}

.emergency-content i {
    font-size: 2.5rem;
    color: #fbbf24;
    margin-bottom: 1rem;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.6;
    }
}

.emergency-content h4 {
    color: white;
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.emergency-content p {
    margin-bottom: 1rem;
    opacity: 0.95;
}

.emergency-phone {
    display: block;
    font-size: 1.75rem;
    font-weight: 700;
    color: #fbbf24;
    text-decoration: none;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    transition: all 0.3s ease;
}

.emergency-phone:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.05);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .content-columns {
        grid-template-columns: 1fr;
        gap: 3rem;
    }

    .about-main-content {
        padding: 2rem;
    }

    .about-sidebar {
        order: 2;
    }
}

@media (max-width: 768px) {
    .about-main-content {
        padding: 1.5rem;
    }

    .firm-overview h2,
    .mission-statement h2,
    .meet-attorneys h2 {
        font-size: 1.75rem;
    }

    .value-item {
        flex-direction: column;
        text-align: center;
    }

    .value-icon {
        margin: 0 auto;
    }

    .attorney-credentials {
        flex-direction: column;
    }

    .attorney-credentials strong {
        min-width: auto;
    }

    .cta-buttons {
        flex-direction: column;
    }

    .sidebar-widget {
        padding: 1.5rem;
    }

    .about-cta {
        padding: 2rem 1.5rem;
    }
}

@media (max-width: 480px) {
    .page-header {
        padding: 3rem 0 1.5rem;
    }

    .page-header-content h1 {
        font-size: 1.75rem;
    }

    .about-page-content {
        padding: 2rem 0;
    }

    .firm-overview h2,
    .mission-statement h2,
    .meet-attorneys h2 {
        font-size: 1.5rem;
    }

    .about-cta h2 {
        font-size: 1.5rem;
    }

    .emergency-phone {
        font-size: 1.5rem;
    }
}
</style>

<?php
get_footer();
