<?php 
/**
 * Module: Artist-List
 * Template: A-List
 * Description: Primary List
 *
 */

global $wp_query;

extract( $wp_query->data );
 
$data = wp_festival()->get_post_data( get_the_ID() );
$date = false;

if( !empty( $data[ 'perfomances' ] ) ) {
  foreach( $data[ 'perfomances' ] as $perfomance ) {
    if( !empty( $perfomance[ 'startDateTime' ] ) ) {
      $date = strtotime( $perfomance[ 'startDateTime' ] );
      break;
    }
  }
}

// Try to get Image
$src = false;
$img_opts = array( 
  'width' => $map[ 2 ], 
  'height' => $map[ 3 ],
);
if( !empty( $data[ 'portraitImage' ] ) ) {
  $src = wp_festival()->get_image_link_by_attachment_id( $data[ 'portraitImage' ], $img_opts );
}
if( !$src && !empty( $data[ 'headshotImage' ] ) ) {
  $src = wp_festival()->get_image_link_by_attachment_id( $data[ 'headshotImage' ], $img_opts );
}
if( !$src ) {
  $src = wp_festival()->get_no_image_link( $img_opts );
}
 
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