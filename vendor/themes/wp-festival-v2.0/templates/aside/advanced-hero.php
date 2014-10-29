<?php
/**
 * Advanced Hero
 * Shows parallax block
 *
 * @author Usability Dynamics
 * @module wp-escalade
 * @since wp-escalade 0.1.0
 */

global $wp_query;

extract( $data = wp_festival2()->extend( array(
  'content' => '',
  'column_class' => 'col-md-4',
  'background_color' => false,
  'background_image' => false,
  'parallax_rotation' => '400',
  'parallax_position' => 'right',
  'parallax_image' => false,
), (array)$wp_query->data[ 'advanced-hero' ] ) );

//echo "<pre>"; print_r( $data ); echo "</pre>";

$bgi_url = !empty( $background_image ) ? wp_festival2()->get_image_link_by_attachment_id( $background_image, array( 'default' => false ) ) : false;
$bimage = $bgi_url ? "background-image: url( {$bgi_url} );" : "";
$bcolor = !empty( $background_color ) ? "background-color: {$background_color} !important;" : "";

$prlx_url = !empty( $parallax_image ) ? wp_festival2()->get_image_link_by_attachment_id( $parallax_image, array( 'default' => false ) ) : false;
$pimage = $prlx_url ? "background-image: url( {$prlx_url} );" : "";
$ppos = '';
switch( $parallax_position ) {
  case 'left':
    $ppos = 'right:33%;left:0;';
    break;
  case 'right':
    $ppos = 'left:33%;right:0;';
    break;
  case 'full':
    $ppos = 'left:0;right:0;';
    break;
}

?>
<div class="advanced-hero" data-requires="udx.ui.scrollr" style="<?php echo $bimage; ?><?php echo $bcolor; ?>">
  <div class="parallax-img" style="<?php echo $pimage; ?><?php echo $ppos; ?>" data-bottom="background-position: left 0px;" data-top-bottom="background-position: left -<?php echo $parallax_rotation; ?>px;"></div>
  <div class="container">
    <div class="row">
      <div class="<?php echo $column_class; ?>">
        <div class="advanced-hero-content">
          <?php echo $content; ?>
        </div>
      </div>
    </div>
  </div>
</div>