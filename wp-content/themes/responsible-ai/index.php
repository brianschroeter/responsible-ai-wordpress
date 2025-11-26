<?php
/**
 * Index Template (Fallback)
 *
 * @package ResponsibleAI
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="section">
    <div class="container">
        <?php if (have_posts()) : ?>
            <div class="posts-grid grid grid-cols-3">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('card hover-lift'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('blog-card', array('class' => 'card__image')); ?>
                            </a>
                        <?php endif; ?>
                        <div class="card__content">
                            <h2 class="card__title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            <div class="card__excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            <div class="card__meta text-muted">
                                <span><?php echo get_the_date(); ?></span>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <?php
            the_posts_pagination(array(
                'prev_text' => '<i class="fas fa-arrow-left"></i> ' . __('Previous', 'responsible-ai'),
                'next_text' => __('Next', 'responsible-ai') . ' <i class="fas fa-arrow-right"></i>',
            ));
            ?>

        <?php else : ?>
            <div class="no-content text-center">
                <h2><?php esc_html_e('Nothing Found', 'responsible-ai'); ?></h2>
                <p><?php esc_html_e('It seems we can\'t find what you\'re looking for.', 'responsible-ai'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
