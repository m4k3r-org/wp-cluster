<?php 
/**
 * Module: Artist-List
 * Template: A-List
 * Description: Primary List
 *
 */

global $wp_query;

extract( $wp_query->data );

$date = wp_festival()->get_artist_perfomance_date( get_the_ID() );

// Try to get Image
$src = wp_festival()->get_artist_image_link( get_the_ID(), array(
  'type' => $artist_image,
  'width' => $map[ 2 ], 
  'height' => $map[ 3 ],
) );
 
?>
<article class="artist-preview" data-type="<?php get_post_type(); ?>">
  <div class="date">
    <?php if( $date ) : ?>
      <span class="week-day"><?php echo date( 'l', $date ); ?>,</span> <span class="month"><?php echo date( 'M', $date ); ?></span> <span class="day"><?php echo date( 'j', $date ); ?></span>
      <span class="hr"></span>
      <div class="clearfix"></div>
    <?php endif; ?>
  </div>
  <a href="<?php the_permalink(); ?>">
    <div class="image">
      <img class="img-responsive" src="<?php echo $src; ?>" alt="<?php the_title(); ?>" />
      <div class="caption"><?php the_title(); ?></div>
    </div>
  </a>
</article>