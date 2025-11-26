<?php
/**
 * Template Name: Who We Are
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get page fields from ACF
$fields = responsibleai_get_who_we_are_fields();

get_header();
?>

<article id="who-we-are" class="page-who-we-are">

    <?php
    // Hero Section
    ?>
    <section class="page-hero">
        <div class="container">
            <div class="page-hero__content">
                <h1 class="page-hero__title">
                    <?php the_title(); ?>
                </h1>
                <?php if (has_excerpt()) : ?>
                    <p class="page-hero__subtitle">
                        <?php the_excerpt(); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php
    // About/Story Section
    if ($fields['enable_story'] && !empty($fields['story_content'])) :
    ?>
    <section class="about-section">
        <div class="container">
            <div class="about-grid">
                <div class="about-grid__content">
                    <h2 class="section-title">
                        <?php echo esc_html($fields['story_title']); ?>
                    </h2>
                    <div class="content-wysiwyg">
                        <?php echo wp_kses_post($fields['story_content']); ?>
                    </div>
                </div>
                <?php if ($fields['story_image']) : ?>
                    <div class="about-grid__image">
                        <img src="<?php echo esc_url($fields['story_image']['url']); ?>"
                             alt="<?php echo esc_attr($fields['story_image']['alt'] ?: $fields['story_title']); ?>"
                             loading="lazy">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php
    // Mission & Vision Section
    if ($fields['enable_mission'] && (!empty($fields['mission_text']) || !empty($fields['vision_text']))) :
    ?>
    <section class="mission-vision-section">
        <div class="container">
            <div class="mission-vision-grid">
                <?php if (!empty($fields['mission_text'])) : ?>
                    <div class="mission-vision-card">
                        <div class="mission-vision-card__icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h3 class="mission-vision-card__title">
                            <?php esc_html_e('Our Mission', 'responsible-ai'); ?>
                        </h3>
                        <div class="mission-vision-card__content">
                            <?php echo wp_kses_post($fields['mission_text']); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($fields['vision_text'])) : ?>
                    <div class="mission-vision-card">
                        <div class="mission-vision-card__icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3 class="mission-vision-card__title">
                            <?php esc_html_e('Our Vision', 'responsible-ai'); ?>
                        </h3>
                        <div class="mission-vision-card__content">
                            <?php echo wp_kses_post($fields['vision_text']); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php
    // Values Section
    if ($fields['enable_values'] && !empty($fields['values'])) :
    ?>
    <section class="values-section">
        <div class="container">
            <h2 class="section-title text-center">
                <?php esc_html_e('Our Values', 'responsible-ai'); ?>
            </h2>
            <div class="values-grid">
                <?php foreach ($fields['values'] as $value) : ?>
                    <div class="value-card">
                        <?php if (!empty($value['icon'])) : ?>
                            <div class="value-card__icon">
                                <i class="<?php echo esc_attr($value['icon']); ?>"></i>
                            </div>
                        <?php endif; ?>
                        <h3 class="value-card__title">
                            <?php echo esc_html($value['title']); ?>
                        </h3>
                        <?php if (!empty($value['description'])) : ?>
                            <p class="value-card__description">
                                <?php echo esc_html($value['description']); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php
    // Team Section with Tabs
    if ($fields['enable_team'] && (!empty($fields['team_board']) || !empty($fields['team_fellows']) || !empty($fields['team_advisors']))) :
    ?>
    <section class="team-section">
        <div class="container">
            <h2 class="section-title text-center">
                <?php esc_html_e('Meet Our Team', 'responsible-ai'); ?>
            </h2>

            <div class="team-tabs">
                <!-- Tab Navigation -->
                <div class="team-tabs__nav" role="tablist" aria-label="<?php esc_attr_e('Team Categories', 'responsible-ai'); ?>">
                    <?php if (!empty($fields['team_board'])) : ?>
                        <button class="team-tabs__button active"
                                role="tab"
                                aria-selected="true"
                                aria-controls="tab-board"
                                id="btn-board"
                                data-tab="board">
                            <?php esc_html_e('RAI Board', 'responsible-ai'); ?>
                        </button>
                    <?php endif; ?>

                    <?php if (!empty($fields['team_fellows'])) : ?>
                        <button class="team-tabs__button <?php echo empty($fields['team_board']) ? 'active' : ''; ?>"
                                role="tab"
                                aria-selected="<?php echo empty($fields['team_board']) ? 'true' : 'false'; ?>"
                                aria-controls="tab-fellows"
                                id="btn-fellows"
                                data-tab="fellows">
                            <?php esc_html_e('RAI Fellows', 'responsible-ai'); ?>
                        </button>
                    <?php endif; ?>

                    <?php if (!empty($fields['team_advisors'])) : ?>
                        <button class="team-tabs__button <?php echo (empty($fields['team_board']) && empty($fields['team_fellows'])) ? 'active' : ''; ?>"
                                role="tab"
                                aria-selected="<?php echo (empty($fields['team_board']) && empty($fields['team_fellows'])) ? 'true' : 'false'; ?>"
                                aria-controls="tab-advisors"
                                id="btn-advisors"
                                data-tab="advisors">
                            <?php esc_html_e('RAI Advisors', 'responsible-ai'); ?>
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Tab Content -->
                <div class="team-tabs__content">
                    <?php if (!empty($fields['team_board'])) : ?>
                        <div class="team-tabs__panel active"
                             role="tabpanel"
                             aria-labelledby="btn-board"
                             id="tab-board">
                            <div class="team-grid">
                                <?php foreach ($fields['team_board'] as $member) : ?>
                                    <?php responsibleai_team_card($member); ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($fields['team_fellows'])) : ?>
                        <div class="team-tabs__panel <?php echo empty($fields['team_board']) ? 'active' : ''; ?>"
                             role="tabpanel"
                             aria-labelledby="btn-fellows"
                             id="tab-fellows">
                            <div class="team-grid">
                                <?php foreach ($fields['team_fellows'] as $member) : ?>
                                    <?php responsibleai_team_card($member); ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($fields['team_advisors'])) : ?>
                        <div class="team-tabs__panel <?php echo (empty($fields['team_board']) && empty($fields['team_fellows'])) ? 'active' : ''; ?>"
                             role="tabpanel"
                             aria-labelledby="btn-advisors"
                             id="tab-advisors">
                            <div class="team-grid">
                                <?php foreach ($fields['team_advisors'] as $member) : ?>
                                    <?php responsibleai_team_card($member); ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php
    // Partners Section with Category Tabs
    $partner_categories = get_field('partner_categories');
    if (!empty($partner_categories)) :
    ?>
    <section class="partners-section">
        <div class="container">
            <h2 class="section-title text-center">
                <?php esc_html_e('Our Partners', 'responsible-ai'); ?>
            </h2>

            <div class="partners-tabs">
                <!-- Partner Category Navigation -->
                <div class="partners-tabs__nav" role="tablist" aria-label="<?php esc_attr_e('Partner Categories', 'responsible-ai'); ?>">
                    <?php
                    $first_category = true;
                    foreach ($partner_categories as $category_index => $category) :
                        $tab_id = 'partner-' . sanitize_title($category['category_name']);
                    ?>
                        <button class="partners-tabs__button <?php echo $first_category ? 'active' : ''; ?>"
                                role="tab"
                                aria-selected="<?php echo $first_category ? 'true' : 'false'; ?>"
                                aria-controls="<?php echo esc_attr($tab_id); ?>"
                                id="btn-<?php echo esc_attr($tab_id); ?>"
                                data-tab="<?php echo esc_attr($tab_id); ?>">
                            <?php echo esc_html($category['category_name']); ?>
                        </button>
                    <?php
                        $first_category = false;
                    endforeach;
                    ?>
                </div>

                <!-- Partner Category Content -->
                <div class="partners-tabs__content">
                    <?php
                    $first_category = true;
                    foreach ($partner_categories as $category_index => $category) :
                        $tab_id = 'partner-' . sanitize_title($category['category_name']);
                        if (!empty($category['partners'])) :
                    ?>
                        <div class="partners-tabs__panel <?php echo $first_category ? 'active' : ''; ?>"
                             role="tabpanel"
                             aria-labelledby="btn-<?php echo esc_attr($tab_id); ?>"
                             id="<?php echo esc_attr($tab_id); ?>">
                            <div class="partners-grid">
                                <?php foreach ($category['partners'] as $partner) : ?>
                                    <div class="partner-logo">
                                        <?php if (!empty($partner['partner_url'])) : ?>
                                            <a href="<?php echo esc_url($partner['partner_url']); ?>"
                                               target="_blank"
                                               rel="noopener"
                                               aria-label="<?php echo esc_attr($partner['partner_name']); ?>">
                                                <?php if (!empty($partner['partner_logo'])) : ?>
                                                    <img src="<?php echo esc_url($partner['partner_logo']['sizes']['partner-logo'] ?? $partner['partner_logo']['url']); ?>"
                                                         alt="<?php echo esc_attr($partner['partner_name']); ?>"
                                                         loading="lazy">
                                                <?php else : ?>
                                                    <span class="partner-logo__text"><?php echo esc_html($partner['partner_name']); ?></span>
                                                <?php endif; ?>
                                            </a>
                                        <?php else : ?>
                                            <?php if (!empty($partner['partner_logo'])) : ?>
                                                <img src="<?php echo esc_url($partner['partner_logo']['sizes']['partner-logo'] ?? $partner['partner_logo']['url']); ?>"
                                                     alt="<?php echo esc_attr($partner['partner_name']); ?>"
                                                     loading="lazy">
                                            <?php else : ?>
                                                <span class="partner-logo__text"><?php echo esc_html($partner['partner_name']); ?></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php
                        endif;
                        $first_category = false;
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

</article>

<?php get_footer(); ?>
