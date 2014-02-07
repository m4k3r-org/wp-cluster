<?php 
/**
 * Module: Artist-List
 * Template: B-List
 * Description: Alphabetical List
 */

global $wp_query;

extract( $wp_query->data ); 
 
// Try to get Image
$src = wp_festival()->get_artist_image_link( get_the_ID(), array(
  'type' => $artist_image,
  'width' => $map[ 2 ], 
  'height' => $map[ 3 ],
) );

?>
<article class="artist-preview artist-alpha" data-type="<?php get_post_type(); ?>">
  <a href="<?php the_permalink(); ?>">
    <div class="image">
      <img class="img-responsive person" src="<?php echo $src; ?>" alt="<?php the_title(); ?>" />
      <div class="caption"><?php the_title(); ?></div>
    </div>
  </a>
</article>