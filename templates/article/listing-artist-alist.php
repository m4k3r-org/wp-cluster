<?php 
/**
 * Module: Artist-List
 * Template: A-List
 * Description: Primary List
 *
 */

$data = wp_festival()->get_post_data( get_the_ID() );

//echo "<pre>"; print_r( $data ); echo "</pre>";
 
?>
<article class="artist-preview" data-type="<?php get_post_type(); ?>">
  <div class="date">
    <span class="week-day">Monday,</span> <span class="month">Nov</span> <span class="day">18</span>
    <span class="hr"></span>
    <div class="clearfix"></div>
  </div>
  <div class="image">
    <img class="img-responsive" src="<?php echo wp_festival()->get_image_link_by_post_id( get_the_ID(), array( 'width' => '738', 'height' => '880' ) ); ?>" />
    <div class="caption"><?php the_title(); ?></div>
  </div>
</article>