<?php
/**
 * Hero Module View
 * @see carrington builder module Hero
 * @author Usability Dynamics
 * @module wp-festival
 * @since wp-festival 2.0.0
 */

global $wp_query;

extract( $data = wp_festival2()->extend( array(
  'image_src' => '',
  'image_alignment' => '',
  'title' => '',
  'content' => '',
  'box_height' => '',
  'id_base' => '',
  'url' => '',
  'fb_like' => false,
  'fb_app_id' => '',
  'fb_url' => '',
  'tw_share' => false,
  'tw_account' => '',
  'tw_hashtag' => '',
  'gp_share' => false,
), (array) $wp_query->data[ 'hero' ] ) );
?>

<div class="hero-container" style="<?php if( !empty( $image_src ) ){ ?> background-image: url(<?php echo $image_src[0]; ?>); background-position: <?php echo $image_alignment; ?>; background-repeat: no-repeat;<?php } ?>">

  <div class="container">
    <div class="row">
      <div class="col-xs-12">
        <h2><?php echo $title; ?></h2>
        <h4><?php echo $content; ?></h4>
      </div>
    </div>
  </div>

  <a href="#" class="nav-arrows clearfix">
    <span class="icon-down-arrow arrow-1"></span>
    <span class="icon-down-arrow arrow-2"></span>
    <span class="icon-down-arrow arrow-3"></span>
  </a>

</div>