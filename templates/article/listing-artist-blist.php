<?php 
/**
 * Module: Artist-List
 * Template: B-List
 * Description: Alphabetical List
 */

$data = wp_festival()->get_post_data( get_the_ID() );

// Try to get Image
$src = false;
$img_opts = array( 
  'width' => '738', 
  'height' => '880' 
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
<article class="artist-preview artist-alpha" data-type="<?php get_post_type(); ?>">
  <a href="<?php the_permalink(); ?>">
    <div class="image">
      <img class="img-responsive person" src="<?php echo $src; ?>" alt="<?php the_title(); ?>" />
      <div class="caption"><?php the_title(); ?></div>
    </div>
  </a>
</article>