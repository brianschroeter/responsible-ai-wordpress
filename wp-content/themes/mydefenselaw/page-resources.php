<?php
/**
 * Template Name: Legal Resources
 *
 * Custom page template for the Legal Resources page
 * Provides educational resources and links for various areas of law
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

        <div class="resources-section">
            <h2>Legal Resources</h2>

            <p class="intro-text">Defense Lawyers, P.A. provides these legal resources to help our clients and the public understand various areas of law. Please note that this information is for educational purposes only and does not constitute legal advice.</p>

            <h3>Civil Defense Litigation Resources:</h3>
            <ul>
                <li><a href="http://www.law.cornell.edu/rules/frcp/overview.htm" target="_blank" rel="noopener noreferrer"><strong>Federal Rules of Civil Procedure</strong></a> - The process of resolving disputes by filing or answering a complaint through the public court system is regulated by the Federal Rules of Civil Procedure (along with state versions). The nature of this complaint (including the probable outcome for each side) becomes the basis for any settlement negotiations.</li>
                <li><a href="http://www.law.cornell.edu/rules/fre/overview.html" target="_blank" rel="noopener noreferrer"><strong>Federal Rules of Evidence</strong></a> - If a case is brought to trial, the Federal Rules of Evidence (along with state versions) govern the introduction of all evidence into the trial.</li>
            </ul>

            <h3>Debt Collection Resources:</h3>
            <ul>
                <li><a href="http://www.treasurydirect.gov/NP/BPDLogin?application=np" target="_blank" rel="noopener noreferrer"><strong>Bureau of the Public Debt</strong></a> - The Bureau of the Public Debt Provides Monthly Updates on the Ever Increasing United States Public Debt Figures.</li>
                <li><a href="http://www.ftc.gov/bcp/conline/pubs/credit/fdc.pdf" target="_blank" rel="noopener noreferrer"><strong>The Fair Debt Collection Practices Act</strong></a> - The United States has enacted laws to protect consumers and to prohibit abusive practices by debt collectors.</li>
                <li><a href="http://www.ftc.gov/bcp/conline/pubs/credit/crdright.pdf" target="_blank" rel="noopener noreferrer"><strong>Credit and Your Consumer Rights</strong></a> - Learn about credit ratings and your rights.</li>
                <li><a href="http://www.ftc.gov/opa/2007/03/camco.htm" target="_blank" rel="noopener noreferrer"><strong>Article – Debt Collector Settles with FTC for Abusive Practices</strong></a> - An article about illegal debt collection practices.</li>
                <li><a href="http://www.fdic.gov/regulations/laws/rules/6500-200.html" target="_blank" rel="noopener noreferrer"><strong>The Truth in Lending Act</strong></a> - The FDIC has created laws, regulations, and related acts in order to protect consumers.</li>
                <li><a href="http://www.federalreserve.gov/" target="_blank" rel="noopener noreferrer"><strong>The Federal Reserve System</strong></a> - Helpful information regarding the Federal Reserve System</li>
                <li><a href="http://www.ftc.gov/bcp/conline/edcams/freereports/index.html" target="_blank" rel="noopener noreferrer"><strong>Free Credit Report – Obtain Yours Today</strong></a> - A recent amendment to the federal Fair Credit Reporting Act requires each of the nationwide consumer reporting companies – Equifax, Experian, and TransUnion – to provide you with a free copy of your credit report, at your request, once every 12 months.</li>
            </ul>

            <h3>Consumer Protection Resources:</h3>
            <ul>
                <li><a href="http://www.classactionlitigation.com/" target="_blank" rel="noopener noreferrer"><strong>Class Action Litigation Information</strong></a> - A free service to assist consumers in understanding class action lawsuits, government, consumer issues and the legal system.</li>
                <li><a href="http://www.lemonlawamerica.com/" target="_blank" rel="noopener noreferrer"><strong>Lemon Law America</strong></a> - Site is resource for consumers with defective vehicles or products. A visit to this site familiarizes you with the lemon statutes in your state and offers tips on how to proceed if you think you've got a "lemon".</li>
            </ul>

            <h3>Bankruptcy Resources:</h3>
            <ul>
                <li><a href="http://bankruptcy.about.com/od/Bankruptcy-Resources/a/History-Of-Bankruptcy-In-The-United-States.htm" target="_blank" rel="noopener noreferrer"><strong>History of Bankruptcy in the United States</strong></a> - Bankruptcy laws in the United States have varied greatly over time. Learn more about the interesting history of bankruptcy.</li>
                <li><a href="http://www.uscourts.gov/FederalCourts/Bankruptcy/BankruptcyBasics/Process.aspx" target="_blank" rel="noopener noreferrer"><strong>The Bankruptcy Process</strong></a> - The procedural aspects of the bankruptcy process are governed by the Federal Rules of Bankruptcy Procedure (often called the "Bankruptcy Rules") and local rules of each bankruptcy court.</li>
            </ul>

            <h3>Contract Law Resources:</h3>
            <ul>
                <li><a href="http://www.law.cornell.edu/ucc/ucc.table.html" target="_blank" rel="noopener noreferrer"><strong>Uniform Commercial Code</strong></a> - Contract law includes the concepts of formation, offer, acceptance, and consideration; performance and excuse for nonperformance; breach and damages; third party beneficiaries; assignment of rights and delegation of duties; statute of frauds; contract integration rule; illegal contracts and public policy; unconscionability; and discharge. One major portion of this area of law is in the Uniform Commercial Code (UCC).</li>
            </ul>

            <h3>Family Law Resources:</h3>
            <ul>
                <li><a href="http://www.abanet.org/family/familylaw/tables.html" target="_blank" rel="noopener noreferrer"><strong>Family Law in the Fifty States</strong></a> - Tables providing a quick view of various aspects of family law for the fifty states in the areas of alimony/spousal support factors, custody criteria, child support guidelines, grounds for divorce and residency requirements, property division, and third-party visitation.</li>
            </ul>

            <h3>Real Estate Law Resources:</h3>
            <ul>
                <li><a href="http://real-estate.lawyers.com/residential-real-estate/Problems-When-Purchasing-Residential-Real-Estate.html" target="_blank" rel="noopener noreferrer"><strong>Problems When Purchasing Residential Real Estate</strong></a> - The purchase of residential real estate usually involves three parties: the buyer, the seller, and the lender. This can make things complex. Plus, the law that governs real estate transactions is different from the law governing other kinds of purchases.</li>
                <li><a href="http://real-estate.lawyers.com/commercial-real-estate/Buying-and-Selling-Commercial-Real-Estate.html" target="_blank" rel="noopener noreferrer"><strong>Buying and Selling Commercial Real Estate</strong></a> - Commercial real estate transactions are typically more complex than residential transactions. Usually, they involve large sums of money and increased liability for both parties.</li>
            </ul>

            <div class="cta-section" style="margin-top: 3rem;">
                <h3>Need Legal Guidance?</h3>
                <p>While these resources provide helpful information, every legal situation is unique. Contact us for personalized legal advice.</p>
                <div class="cta-buttons">
                    <a href="tel:<?php echo esc_attr($phone_raw); ?>" class="btn btn-primary">
                        <i class="fas fa-phone" aria-hidden="true"></i> Call <?php echo esc_html($phone); ?>
                    </a>
                    <button class="btn btn-outline consultation-trigger" id="modalTriggerResources" aria-label="Request a free legal consultation">
                        <i class="fas fa-calendar-check" aria-hidden="true"></i> Free Consultation
                    </button>
                </div>
            </div>
        </div>

    </div>
</main>

<?php
get_footer();
