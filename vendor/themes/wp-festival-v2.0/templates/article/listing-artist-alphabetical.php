<?php
/**
 * Module: Artist-List
 * Template: A-List
 * Description: Primary List
 *
 */
global $_col, $wp_query;
extract( $wp_query->data );
$date = $custom_date[ $post->ID ];
if( $date ){
  $date = date('l, F d' , strtotime($date ));
}
/** See if we need to link */
$link_it = false;
if( isset( $link_to_single_artist_page ) && $link_to_single_artist_page ){
  $link_it = true;
}
$meta = get_post_meta( $post->ID );

/**
 * Replace the links:
 * <a class="tier2-artist" href="<?php the_permalink(); ?>">
 */
?>

<a class="tier2-artist <?php echo $link_it ? "" : "no-link"; ?>" href="<?php if( $link_it ): the_permalink(); else: ?>javascript:void(0);<?php endif; ?>">
  <div class="photo">
    <div class="resp-content">
      <img alt="<?php the_title(); ?>" src="<?php echo wp_festival2()->get_image_link_by_post_id( get_post_meta( $post->ID, $artist_image, true ), array( 'type' => 'attachment', 'width' => '320', 'height' => '480' ) ); ?>">
    </div>
  </div>
  <?php if( $date ): ?>
    <time><?php echo $date; ?></time>
  <?php endif; ?>
  <h3 class="<?php echo $date ? '' : 'no-date'; ?>"><?php the_title(); ?></h3>
</a>
