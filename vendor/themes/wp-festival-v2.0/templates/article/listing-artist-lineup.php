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
  $date = DateTime::createFromFormat('d-m-Y', $date );
  $date = $date->format( 'l, F d' );
}
$meta = get_post_meta( $post->ID );
/** See if we need to link */
$link_it = false;
if( isset( $link_to_single_artist_page ) && $link_to_single_artist_page ){
  $link_it = true;
}

/**
 * Replace the link 
 *  <a href="<?php the_permalink(); ?>" class="col-xs-12 col-sm-<?php echo $_col; ?> main-artist">
 */
?>

<a href="<?php if( $link_it ): the_permalink(); else: ?>javascript:void(0);<?php endif; ?>" class="col-xs-12 col-sm-<?php echo $_col; ?> main-artist <?php echo $link_it ? "" : "no-link"; ?>">
  <div class="photo">
    <div class="resp-content">
      <img src="<?php echo wp_festival2()->get_image_link_by_post_id( get_post_meta( $post->ID, $artist_image, true ), array( 'type' => 'attachment', 'width' => '600', 'height' => '600' ) ); ?>" alt="<?php the_title(); ?>">
    </div>
  </div>

  <?php if( $date ): ?>
    <time><?php echo $date; ?></time>
  <?php endif; ?>
  <h3 class="<?php echo $date ? '' : 'no-date'; ?>"><?php the_title(); ?></h3>
</a>
