<?php
/**
 * Module: Artist-List
 * Template: B-List
 * Description: Alphabetical List
 */

global $wp_query;

extract( $wp_query->data );

$enable_links = ( isset( $enable_links ) && $enable_links == 'true' ) ? true : false;
$fcolor = !empty( $font_color ) ? "color: {$font_color} !important;" : "";

?>
<article class="" data-type="<?php get_post_type(); ?>">
  <?php if( $enable_links ) : ?>
    <a href="<?php the_permalink(); ?>"><span><?php the_title(); ?></span></a>
  <?php else : ?>
    <span style="<?php echo $fcolor; ?>"><?php the_title(); ?></span>
  <?php endif; ?>
</article>