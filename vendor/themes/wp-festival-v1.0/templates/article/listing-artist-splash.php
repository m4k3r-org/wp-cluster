<?php 
/**
 * Module: Artist-List
 * Template: A-List
 * Description: Primary List
 *
 */

global $wp_query;

extract( $wp_query->data );

$enable_links = ( isset( $enable_links ) && $enable_links == 'true' ) ? true : false;

$fcolor = !empty( $font_color ) ? "color: {$font_color} !important;" : "";

// Try to get Image
$src = wp_festival()->get_artist_image_link( get_the_ID(), array(
  'type' => $artist_image,
  'width' => $map[ 2 ], 
  'height' => $map[ 3 ],
) );
 
?>
<article class="artist-preview" data-type="<?php get_post_type(); ?>">
  <?php if( $enable_links ) : ?>
    <a href="<?php the_permalink(); ?>"><div class="image"><img class="img-responsive" src="<?php echo $src; ?>" alt="<?php the_title(); ?>" /></div></a>
  <?php else : ?>
    <div class="image"><img class="img-responsive" src="<?php echo $src; ?>" alt="<?php the_title(); ?>" /></div>
  <?php endif; ?>
</article>