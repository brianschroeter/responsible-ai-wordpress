<?php
/**
 * Template Name: RAISE Certification Pathways
 *
 * Page template for displaying RAISE Certification Pathways
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get ACF fields with defaults
$enable_hero = get_field('enable_hero') !== false;
$enable_intro = get_field('enable_intro') !== false;
$enable_pathways = get_field('enable_pathways') !== false;
$enable_comparison = get_field('enable_comparison') !== false;
$enable_faq = get_field('enable_faq') !== false;
$enable_cta = get_field('enable_cta') !== false;

// Hero fields
$hero_title = get_field('hero_title') ?: __('RAISE Certification Pathways', 'responsible-ai');
$hero_subtitle = get_field('hero_subtitle') ?: __('Choose your path to responsible AI excellence', 'responsible-ai');
$hero_description = get_field('hero_description') ?: __('RAISE certification provides a structured pathway for individuals, organizations, products, and services to demonstrate their commitment to responsible AI practices.', 'responsible-ai');

// Introduction fields
$intro_title = get_field('intro_title') ?: __('Understanding RAISE Certification', 'responsible-ai');
$intro_content = get_field('intro_content') ?: __('The RAISE (Responsible AI Standards & Ethics) certification framework offers four distinct pathways, each designed to meet the unique needs of different stakeholders in the AI ecosystem. Our rigorous certification process ensures that certified entities meet the highest standards of responsible AI development and deployment.', 'responsible-ai');

// Pathway defaults
$pathways = get_field('pathways') ?: array(
    array(
        'icon' => 'fas fa-user-graduate',
        'title' => __('Practitioner Pathway', 'responsible-ai'),
        'description' => __('For AI professionals seeking to demonstrate expertise in responsible AI practices', 'responsible-ai'),
        'benefits' => array(
            __('Industry-recognized credential', 'responsible-ai'),
            __('Enhanced career opportunities', 'responsible-ai'),
            __('Access to exclusive community', 'responsible-ai'),
            __('Continuing education resources', 'responsible-ai'),
        ),
        'requirements' => __('Complete training modules, pass assessment, maintain active status', 'responsible-ai'),
        'cta_text' => __('Start Your Journey', 'responsible-ai'),
        'cta_link' => home_url('/certification/practitioner/'),
    ),
    array(
        'icon' => 'fas fa-building',
        'title' => __('Organization Pathway', 'responsible-ai'),
        'description' => __('For companies committed to embedding responsible AI practices across operations', 'responsible-ai'),
        'benefits' => array(
            __('Enhanced brand reputation', 'responsible-ai'),
            __('Risk mitigation framework', 'responsible-ai'),
            __('Stakeholder confidence', 'responsible-ai'),
            __('Competitive differentiation', 'responsible-ai'),
        ),
        'requirements' => __('Policy audit, staff training, ongoing monitoring, annual review', 'responsible-ai'),
        'cta_text' => __('Certify Your Organization', 'responsible-ai'),
        'cta_link' => home_url('/certification/organization/'),
    ),
    array(
        'icon' => 'fas fa-robot',
        'title' => __('Product Pathway', 'responsible-ai'),
        'description' => __('For AI products designed with responsibility, transparency, and ethics at their core', 'responsible-ai'),
        'benefits' => array(
            __('Market differentiation', 'responsible-ai'),
            __('User trust building', 'responsible-ai'),
            __('Regulatory compliance', 'responsible-ai'),
            __('Quality assurance seal', 'responsible-ai'),
        ),
        'requirements' => __('Technical audit, documentation review, testing protocol, ongoing updates', 'responsible-ai'),
        'cta_text' => __('Certify Your Product', 'responsible-ai'),
        'cta_link' => home_url('/certification/product/'),
    ),
    array(
        'icon' => 'fas fa-handshake',
        'title' => __('Service Pathway', 'responsible-ai'),
        'description' => __('For AI service providers delivering responsible solutions to clients', 'responsible-ai'),
        'benefits' => array(
            __('Client confidence boost', 'responsible-ai'),
            __('Process standardization', 'responsible-ai'),
            __('Quality benchmarking', 'responsible-ai'),
            __('Partnership opportunities', 'responsible-ai'),
        ),
        'requirements' => __('Service audit, case studies, client references, continuous improvement', 'responsible-ai'),
        'cta_text' => __('Certify Your Service', 'responsible-ai'),
        'cta_link' => home_url('/certification/service/'),
    ),
);

// Comparison table data
$comparison_title = get_field('comparison_title') ?: __('Pathway Comparison', 'responsible-ai');
$comparison_rows = get_field('comparison_rows') ?: array(
    array(
        'feature' => __('Target Audience', 'responsible-ai'),
        'practitioner' => __('Individuals', 'responsible-ai'),
        'organization' => __('Companies', 'responsible-ai'),
        'product' => __('AI Products', 'responsible-ai'),
        'service' => __('Service Providers', 'responsible-ai'),
    ),
    array(
        'feature' => __('Duration', 'responsible-ai'),
        'practitioner' => __('3-6 months', 'responsible-ai'),
        'organization' => __('6-12 months', 'responsible-ai'),
        'product' => __('3-9 months', 'responsible-ai'),
        'service' => __('6-12 months', 'responsible-ai'),
    ),
    array(
        'feature' => __('Renewal Period', 'responsible-ai'),
        'practitioner' => __('2 years', 'responsible-ai'),
        'organization' => __('Annual', 'responsible-ai'),
        'product' => __('Per version', 'responsible-ai'),
        'service' => __('Annual', 'responsible-ai'),
    ),
    array(
        'feature' => __('Investment Level', 'responsible-ai'),
        'practitioner' => __('Individual', 'responsible-ai'),
        'organization' => __('Enterprise', 'responsible-ai'),
        'product' => __('Project-based', 'responsible-ai'),
        'service' => __('Enterprise', 'responsible-ai'),
    ),
);

// FAQ data
$faq_title = get_field('faq_title') ?: __('Frequently Asked Questions', 'responsible-ai');
$faq_items = get_field('faq_items') ?: array(
    array(
        'question' => __('What is RAISE certification?', 'responsible-ai'),
        'answer' => __('RAISE (Responsible AI Standards & Ethics) certification is a comprehensive framework that validates commitment to responsible AI practices. It provides structured pathways for individuals, organizations, products, and services to demonstrate adherence to the highest standards of AI ethics, transparency, and accountability.', 'responsible-ai'),
    ),
    array(
        'question' => __('How long does the certification process take?', 'responsible-ai'),
        'answer' => __('The timeline varies by pathway. Practitioner certification typically takes 3-6 months, while organization and service pathways may require 6-12 months. Product certification depends on the complexity and can range from 3-9 months. All timelines include training, assessment, and review periods.', 'responsible-ai'),
    ),
    array(
        'question' => __('What are the renewal requirements?', 'responsible-ai'),
        'answer' => __('Renewal requirements vary by pathway. Practitioners renew every 2 years with continuing education. Organizations and services undergo annual audits. Product certifications are version-specific and require recertification for major updates. All pathways require demonstration of continued adherence to RAISE standards.', 'responsible-ai'),
    ),
    array(
        'question' => __('Can I pursue multiple pathways?', 'responsible-ai'),
        'answer' => __('Yes, pathways are designed to be complementary. Many organizations pursue both organizational and product certifications, while individual practitioners within certified organizations gain additional credentials through the practitioner pathway. We offer bundled assessment options for multiple pathways.', 'responsible-ai'),
    ),
    array(
        'question' => __('What support is available during certification?', 'responsible-ai'),
        'answer' => __('All certification candidates receive comprehensive support including documentation templates, training materials, access to expert advisors, peer community forums, and regular check-in sessions. Our team is committed to helping you succeed in your certification journey.', 'responsible-ai'),
    ),
);

// Final CTA fields
$cta_title = get_field('cta_title') ?: __('Ready to Start Your Certification Journey?', 'responsible-ai');
$cta_description = get_field('cta_description') ?: __('Join leading organizations and professionals committed to responsible AI. Begin your certification process today.', 'responsible-ai');
$cta_primary_text = get_field('cta_primary_text') ?: __('Get Started', 'responsible-ai');
$cta_primary_link = get_field('cta_primary_link') ?: home_url('/certification/apply/');
$cta_secondary_text = get_field('cta_secondary_text') ?: __('Schedule Consultation', 'responsible-ai');
$cta_secondary_link = get_field('cta_secondary_link') ?: home_url('/contact/');

get_header();
?>

<?php if ($enable_hero) : ?>
<!-- Hero Section -->
<section class="raise-hero">
    <div class="container">
        <div class="raise-hero__content">
            <h1 class="raise-hero__title"><?php echo esc_html($hero_title); ?></h1>
            <p class="raise-hero__subtitle"><?php echo esc_html($hero_subtitle); ?></p>
            <p class="raise-hero__description"><?php echo esc_html($hero_description); ?></p>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($enable_intro) : ?>
<!-- Introduction Section -->
<section class="raise-intro section-padding">
    <div class="container">
        <div class="raise-intro__content">
            <h2 class="section-title"><?php echo esc_html($intro_title); ?></h2>
            <div class="raise-intro__text">
                <?php echo wpautop(wp_kses_post($intro_content)); ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($enable_pathways && !empty($pathways)) : ?>
<!-- Pathways Section -->
<section class="raise-pathways section-padding bg-dark">
    <div class="container">
        <div class="pathways-grid">
            <?php foreach ($pathways as $pathway) : ?>
                <div class="pathway-card">
                    <div class="pathway-card__header">
                        <div class="pathway-card__icon">
                            <i class="<?php echo esc_attr($pathway['icon']); ?>"></i>
                        </div>
                        <h3 class="pathway-card__title"><?php echo esc_html($pathway['title']); ?></h3>
                        <p class="pathway-card__description"><?php echo esc_html($pathway['description']); ?></p>
                    </div>

                    <div class="pathway-card__body">
                        <?php if (!empty($pathway['benefits'])) : ?>
                            <div class="pathway-card__section">
                                <h4 class="pathway-card__section-title"><?php esc_html_e('Benefits', 'responsible-ai'); ?></h4>
                                <ul class="pathway-card__list">
                                    <?php foreach ($pathway['benefits'] as $benefit) : ?>
                                        <li><i class="fas fa-check"></i> <?php echo esc_html($benefit); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($pathway['requirements'])) : ?>
                            <div class="pathway-card__section">
                                <h4 class="pathway-card__section-title"><?php esc_html_e('Requirements', 'responsible-ai'); ?></h4>
                                <p class="pathway-card__requirements"><?php echo esc_html($pathway['requirements']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="pathway-card__footer">
                        <a href="<?php echo esc_url($pathway['cta_link']); ?>" class="btn btn-cta btn-block">
                            <?php echo esc_html($pathway['cta_text']); ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($enable_comparison && !empty($comparison_rows)) : ?>
<!-- Comparison Table Section -->
<section class="raise-comparison section-padding">
    <div class="container">
        <h2 class="section-title text-center"><?php echo esc_html($comparison_title); ?></h2>

        <div class="comparison-table-wrapper">
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Feature', 'responsible-ai'); ?></th>
                        <th><?php esc_html_e('Practitioner', 'responsible-ai'); ?></th>
                        <th><?php esc_html_e('Organization', 'responsible-ai'); ?></th>
                        <th><?php esc_html_e('Product', 'responsible-ai'); ?></th>
                        <th><?php esc_html_e('Service', 'responsible-ai'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comparison_rows as $row) : ?>
                        <tr>
                            <td class="comparison-table__feature"><?php echo esc_html($row['feature']); ?></td>
                            <td><?php echo esc_html($row['practitioner']); ?></td>
                            <td><?php echo esc_html($row['organization']); ?></td>
                            <td><?php echo esc_html($row['product']); ?></td>
                            <td><?php echo esc_html($row['service']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($enable_faq && !empty($faq_items)) : ?>
<!-- FAQ Section -->
<section class="raise-faq section-padding bg-dark">
    <div class="container">
        <h2 class="section-title text-center"><?php echo esc_html($faq_title); ?></h2>

        <div class="faq-accordion">
            <?php foreach ($faq_items as $index => $item) : ?>
                <div class="faq-item">
                    <button class="faq-item__question"
                            aria-expanded="false"
                            aria-controls="faq-answer-<?php echo esc_attr($index); ?>">
                        <span class="faq-item__question-text"><?php echo esc_html($item['question']); ?></span>
                        <i class="faq-item__icon fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-item__answer" id="faq-answer-<?php echo esc_attr($index); ?>" hidden>
                        <div class="faq-item__answer-content">
                            <?php echo wpautop(wp_kses_post($item['answer'])); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($enable_cta) : ?>
<!-- Final CTA Section -->
<section class="raise-cta section-padding">
    <div class="container">
        <div class="cta-box">
            <h2 class="cta-box__title"><?php echo esc_html($cta_title); ?></h2>
            <p class="cta-box__description"><?php echo esc_html($cta_description); ?></p>
            <div class="cta-box__actions">
                <a href="<?php echo esc_url($cta_primary_link); ?>" class="btn btn-cta btn-lg">
                    <?php echo esc_html($cta_primary_text); ?>
                </a>
                <a href="<?php echo esc_url($cta_secondary_link); ?>" class="btn btn-secondary btn-lg">
                    <?php echo esc_html($cta_secondary_text); ?>
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<style>
/* RAISE Pathways Page Styles */

/* Hero Section */
.raise-hero {
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
    padding: 6rem 0 4rem;
    text-align: center;
    color: var(--color-light);
}

.raise-hero__title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: var(--color-light);
}

.raise-hero__subtitle {
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    opacity: 0.95;
}

.raise-hero__description {
    font-size: 1.125rem;
    max-width: 800px;
    margin: 0 auto;
    opacity: 0.9;
}

/* Introduction Section */
.raise-intro {
    background: var(--color-bg);
}

.raise-intro__content {
    max-width: 900px;
    margin: 0 auto;
    text-align: center;
}

.raise-intro__text {
    font-size: 1.125rem;
    line-height: 1.8;
    color: var(--color-text-secondary);
}

/* Pathways Grid */
.raise-pathways {
    background: var(--color-bg-dark);
}

.pathways-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.pathway-card {
    background: var(--color-bg);
    border-radius: 12px;
    padding: 2rem;
    display: flex;
    flex-direction: column;
    border: 1px solid var(--color-border);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.pathway-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
}

.pathway-card__header {
    text-align: center;
    margin-bottom: 2rem;
}

.pathway-card__icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--color-light);
}

.pathway-card__title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--color-text);
}

.pathway-card__description {
    font-size: 1rem;
    color: var(--color-text-secondary);
    line-height: 1.6;
}

.pathway-card__body {
    flex: 1;
    margin-bottom: 2rem;
}

.pathway-card__section {
    margin-bottom: 1.5rem;
}

.pathway-card__section-title {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    color: var(--color-text);
}

.pathway-card__list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.pathway-card__list li {
    padding: 0.5rem 0;
    color: var(--color-text-secondary);
    display: flex;
    align-items: flex-start;
}

.pathway-card__list li i {
    color: var(--color-primary);
    margin-right: 0.75rem;
    margin-top: 0.25rem;
    flex-shrink: 0;
}

.pathway-card__requirements {
    color: var(--color-text-secondary);
    line-height: 1.6;
    font-size: 0.9375rem;
}

.pathway-card__footer {
    margin-top: auto;
}

/* Comparison Table */
.raise-comparison {
    background: var(--color-bg);
}

.comparison-table-wrapper {
    overflow-x: auto;
    margin-top: 3rem;
    border-radius: 12px;
    border: 1px solid var(--color-border);
}

.comparison-table {
    width: 100%;
    border-collapse: collapse;
    background: var(--color-bg-dark);
}

.comparison-table thead {
    background: var(--color-primary);
    color: var(--color-light);
}

.comparison-table th {
    padding: 1.25rem 1rem;
    text-align: left;
    font-weight: 600;
    font-size: 1rem;
    white-space: nowrap;
}

.comparison-table td {
    padding: 1.25rem 1rem;
    border-top: 1px solid var(--color-border);
    color: var(--color-text-secondary);
}

.comparison-table__feature {
    font-weight: 600;
    color: var(--color-text);
}

.comparison-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.02);
}

/* FAQ Section */
.raise-faq {
    background: var(--color-bg-dark);
}

.faq-accordion {
    max-width: 900px;
    margin: 3rem auto 0;
}

.faq-item {
    margin-bottom: 1rem;
    border: 1px solid var(--color-border);
    border-radius: 8px;
    overflow: hidden;
    background: var(--color-bg);
}

.faq-item__question {
    width: 100%;
    padding: 1.5rem;
    background: transparent;
    border: none;
    text-align: left;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: background 0.2s ease;
    color: var(--color-text);
    font-size: 1.125rem;
    font-weight: 600;
}

.faq-item__question:hover {
    background: rgba(255, 255, 255, 0.03);
}

.faq-item__question[aria-expanded="true"] {
    background: rgba(255, 255, 255, 0.05);
}

.faq-item__question[aria-expanded="true"] .faq-item__icon {
    transform: rotate(180deg);
}

.faq-item__icon {
    color: var(--color-primary);
    transition: transform 0.3s ease;
    font-size: 1rem;
}

.faq-item__answer {
    overflow: hidden;
}

.faq-item__answer-content {
    padding: 0 1.5rem 1.5rem;
    color: var(--color-text-secondary);
    line-height: 1.8;
}

/* CTA Section */
.raise-cta {
    background: var(--color-bg);
}

.cta-box {
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
    padding: 4rem 2rem;
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
    border-radius: 12px;
    color: var(--color-light);
}

.cta-box__title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: var(--color-light);
}

.cta-box__description {
    font-size: 1.125rem;
    margin-bottom: 2rem;
    opacity: 0.95;
}

.cta-box__actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

/* Utility Classes */
.section-padding {
    padding: 5rem 0;
}

.bg-dark {
    background: var(--color-bg-dark);
}

.text-center {
    text-align: center;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: var(--color-text);
}

.btn-block {
    width: 100%;
}

/* Responsive Design */
@media (max-width: 768px) {
    .raise-hero {
        padding: 4rem 0 3rem;
    }

    .raise-hero__title {
        font-size: 2rem;
    }

    .raise-hero__subtitle {
        font-size: 1.25rem;
    }

    .pathways-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .comparison-table th,
    .comparison-table td {
        padding: 1rem 0.75rem;
        font-size: 0.875rem;
    }

    .section-title {
        font-size: 1.75rem;
    }

    .cta-box {
        padding: 3rem 1.5rem;
    }

    .cta-box__title {
        font-size: 1.5rem;
    }

    .cta-box__actions {
        flex-direction: column;
    }

    .cta-box__actions .btn {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .section-padding {
        padding: 3rem 0;
    }

    .comparison-table {
        font-size: 0.8125rem;
    }

    .pathway-card {
        padding: 1.5rem;
    }
}
</style>

<script>
// FAQ Accordion functionality
document.addEventListener('DOMContentLoaded', function() {
    const faqButtons = document.querySelectorAll('.faq-item__question');

    faqButtons.forEach(button => {
        button.addEventListener('click', function() {
            const expanded = this.getAttribute('aria-expanded') === 'true';
            const answer = this.nextElementSibling;

            // Close all other FAQ items
            faqButtons.forEach(btn => {
                if (btn !== button) {
                    btn.setAttribute('aria-expanded', 'false');
                    btn.nextElementSibling.hidden = true;
                }
            });

            // Toggle current item
            this.setAttribute('aria-expanded', !expanded);
            answer.hidden = expanded;
        });

        // Keyboard support
        button.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });
});
</script>

<?php get_footer(); ?>
