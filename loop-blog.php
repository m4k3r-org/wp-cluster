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
<?php if (function_exists('wp_pagenavi') ) {
  wp_pagenavi();
} ?>
