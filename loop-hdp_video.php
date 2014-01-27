<?php
/*
 * Video Taxonomy loop template.
 *
*/

if( is_array( $post ) ){
  $video = $post;
}else{
  $video = get_event( $post->ID );
}
?>

<li <?php post_class(); ?>>

  <ul class="hdp_video clearfix">
    <li class="hdp_video_thumbnail"><a href="<?php echo get_permalink( $video[ 'ID' ] ); ?>" title="Video from <?php echo $video[ 'post_title' ]; ?>"><div class="overlay"></div><img src="<?php echo array_shift( wp_get_attachment_image_src( get_post_thumbnail_id( $video[ 'ID' ] ), 'hd_small' ) ); ?>" alt="<?php echo $video[ 'post_title' ]; ?>" /></a></li>
    <li class="hdp_video_title"><a href="<?php echo get_permalink( $video[ 'ID' ] ); ?>" title="Video from <?php echo $video[ 'post_title' ]; ?>"><?php echo $video[ 'post_title' ]; ?></a></li>
    <li class="hdp_video_date"><?php echo $video[ 'summary_qa' ][ 'hdp_event_date' ]; ?></li>
    <li class="hdp_video_location"><?php echo $video['terms']['hdp_city'][0]['name'].', '.$video['terms']['hdp_state'][0]['name']; ?></li>
  </ul>

</li>
