<?php
/**
 * Listing Event Hero
 *
 * @see carrington builder module Event Hero
 * @author Usability Dynamics
 * @module festival  
 * @since festival 0.1.0
 */

global $wp_query; 

echo "<pre>"; print_r( $wp_query->data ); echo "</pre>"; //die();

extract( $wp_query->data );

$url = !empty( $featured_image ) ? wp_festival()->get_image_link_by_attachment_id( $featured_image, array( 'default' => false ) ) : false;

$bimage = $url ? "background-image: url( {$url} );" : "";
$bcolor = !empty( $background_color ) ? "background-color: {$background_color} !important;" : "";
$fcolor = !empty( $font_color ) ? "color: {$font_color} !important;" : "";
$bfcolor = !empty( $font_color ) ? "border-color: {$font_color} !important;" : "";

?>
<div class="event-hero" style="<?php echo $bimage; ?><?php echo $bcolor; ?>" >

  <div class="container">

    TEST
  
  </div>
  
</div>