<?php
/**
 * The loop that displays posts.
 *
 */
?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div id="post-<?php the_ID(); ?>" class="<?php flawless_module_class( 'loop-listing cfct-module' ); ?>">
  <div class="post_listing_inner">

    <?php flawless_page_title( array( 'link' => true, 'before' => '<h2 class="entry-title">', 'after' => '</h2>' ) ); ?>

    <?php get_template_part( 'entry-meta-header', get_post_format() ); ?>

    <?php get_template_part( 'entry-content',  get_post_format() ); ?>

    <?php get_template_part( 'entry-meta-footer', get_post_format() ); ?>

  </div>
</div>
<?php endwhile; endif; ?>

<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php if (  $wp_query->max_num_pages > 1 ) : ?>
  <div class="row-fluid navigation">
    <div class="span6">
        <span class="cfct-module nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'flawless' ) ); ?></span>
    </div>
    <div class="span6">
      <span class="cfct-module nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'flawless' ) ); ?></span>
    </div>
  </div>
<?php endif; ?>

