<?php
/**
 * Template Name: Latest Legal News
 *
 * Custom page template for the Latest Legal News page
 * Static content displaying recent legal updates and developments
 *
 * @package MyDefenseLaw
 * @since 1.0.0
 */

get_header();
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

        <div class="news-section">
            <h2>Latest Legal News</h2>

            <p>Stay informed with the latest legal developments, court decisions, and regulatory changes that may affect your legal matters. Defense Lawyers, P.A. keeps you updated on important legal news and trends.</p>

            <div class="news-grid">

                <article class="news-item">
                    <div class="news-meta">
                        <span><i class="fas fa-calendar" aria-hidden="true"></i> Recent Legal Updates</span>
                    </div>
                    <h3>Consumer Protection Laws</h3>
                    <p>Recent updates to consumer protection regulations provide additional safeguards for consumers against unfair business practices. These changes affect debt collection, warranty claims, and financial services.</p>
                </article>

                <article class="news-item">
                    <div class="news-meta">
                        <span><i class="fas fa-calendar" aria-hidden="true"></i> Bankruptcy Law Changes</span>
                    </div>
                    <h3>Federal Bankruptcy Updates</h3>
                    <p>New federal guidelines for bankruptcy proceedings provide clearer paths for debt relief while maintaining protections for creditors. These changes affect both individual and business bankruptcy cases.</p>
                </article>

                <article class="news-item">
                    <div class="news-meta">
                        <span><i class="fas fa-calendar" aria-hidden="true"></i> Family Law Developments</span>
                    </div>
                    <h3>Child Custody Guidelines</h3>
                    <p>Updated state guidelines for child custody determinations emphasize the best interests of the child while providing clearer standards for custody arrangements and modifications.</p>
                </article>

                <article class="news-item">
                    <div class="news-meta">
                        <span><i class="fas fa-calendar" aria-hidden="true"></i> Real Estate Law</span>
                    </div>
                    <h3>Property Rights Updates</h3>
                    <p>Recent court decisions have clarified property rights in landlord-tenant disputes, providing better guidance for both property owners and renters in resolving conflicts.</p>
                </article>

                <article class="news-item">
                    <div class="news-meta">
                        <span><i class="fas fa-calendar" aria-hidden="true"></i> Contract Law</span>
                    </div>
                    <h3>Commercial Contract Standards</h3>
                    <p>New standards for commercial contracts provide clearer guidelines for dispute resolution and performance obligations, affecting how businesses structure their agreements.</p>
                </article>

                <article class="news-item">
                    <div class="news-meta">
                        <span><i class="fas fa-calendar" aria-hidden="true"></i> Civil Litigation</span>
                    </div>
                    <h3>Court Procedure Changes</h3>
                    <p>Updates to civil court procedures streamline the litigation process while maintaining due process protections. These changes affect filing requirements and discovery procedures.</p>
                </article>

            </div>

            <div class="news-cta-banner">
                <h3>Stay Informed About Legal Changes</h3>
                <p>Legal developments can directly impact your rights and obligations. Our experienced attorneys stay current with all legal changes to provide you with the most up-to-date advice.</p>
                <div class="news-cta-buttons">
                    <a href="tel:8884440253" class="btn btn-primary">
                        <i class="fas fa-phone" aria-hidden="true"></i> Call for Legal Updates
                    </a>
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-secondary">
                        <i class="fas fa-calendar-check" aria-hidden="true"></i> Schedule Consultation
                    </a>
                </div>
            </div>

            <h3>Why Legal News Matters</h3>

            <div class="news-reasons-grid">
                <div class="news-reason">
                    <h4><i class="fas fa-gavel" aria-hidden="true"></i> Changing Regulations</h4>
                    <p>Laws and regulations change frequently. Staying informed helps you understand how these changes might affect your legal situation.</p>
                </div>
                <div class="news-reason">
                    <h4><i class="fas fa-shield-alt" aria-hidden="true"></i> Know Your Rights</h4>
                    <p>Understanding legal developments helps you recognize when your rights may be affected and when to seek legal counsel.</p>
                </div>
                <div class="news-reason">
                    <h4><i class="fas fa-clock" aria-hidden="true"></i> Timely Action</h4>
                    <p>Many legal matters have time limits. Being aware of legal changes helps ensure you take action within required deadlines.</p>
                </div>
                <div class="news-reason">
                    <h4><i class="fas fa-lightbulb" aria-hidden="true"></i> Better Decisions</h4>
                    <p>Understanding the current legal landscape helps you make more informed decisions about your personal and business matters.</p>
                </div>
            </div>

            <div class="cta-section" style="margin-top: 3rem;">
                <h3>Need Legal Advice About Recent Changes?</h3>
                <p>Our experienced attorneys stay current with all legal developments and can help you understand how they affect your situation.</p>
                <div class="cta-buttons">
                    <a href="tel:8884440253" class="btn btn-primary">
                        <i class="fas fa-phone" aria-hidden="true"></i> Call 888.444.0253
                    </a>
                    <button class="btn btn-outline consultation-trigger" id="modalTriggerNews" aria-label="Request a free legal consultation">
                        <i class="fas fa-calendar-check" aria-hidden="true"></i> Free Consultation
                    </button>
                </div>
            </div>

        </div>

    </div>
</main>

<?php
get_footer();
