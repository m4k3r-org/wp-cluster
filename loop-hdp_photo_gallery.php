<?php
/*
 * Photo Taxonomy loop template.
 *
 */

if( is_array( $post ) ) {
  $photo = $post;
} else {
  $photo = get_event( $post->ID );
}
?>

<li <?php post_class(); ?>>

  <ul class="hdp_photo clearfix">
    <li class="hdp_photo_thumbnail"><a href="<?php echo get_permalink( $photo[ 'ID' ] ); ?>" title="Photos from <?php echo $photo[ 'post_title' ]; ?>"><div class="overlay"></div><img src="<?php echo array_shift( wp_get_attachment_image_src( get_post_thumbnail_id( $photo[ 'ID' ] ), 'hd_small' ) ); ?>" alt="<?php echo $photo[ 'post_title' ]; ?>"/></a></li>
    <li class="hdp_photo_title"><a href="<?php echo get_permalink( $photo[ 'ID' ] ); ?>" title="Photos from <?php echo $photo[ 'post_title' ]; ?>"><?php echo $photo[ 'post_title' ]; ?></a></li>
    <li class="hdp_photo_date"><?php echo $photo[ 'summary_qa' ][ 'hdp_event_date' ]; ?></li>
    <li class="hdp_photo_location"><?php echo $photo[ 'terms' ][ 'hdp_city' ][ 0 ][ 'name' ] . ', ' . $photo[ 'terms' ][ 'hdp_state' ][ 0 ][ 'name' ]; ?></li>
  </ul>

</li>
