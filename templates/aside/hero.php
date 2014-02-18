<?php
/**
 * Hero Module View
 *
 * @see carrington builder module Hero
 * @author Usability Dynamics
 * @module festival  
 * @since festival 0.1.0
 */
 
global $wp_query; 

extract( $data = wp_festival()->extend( array(
  'image_src' => '',
  'image_alignment' => '',
  'title' => '',
  'content' => '',
  'box_height' => '',
  'id_base' => '',
  'url' => '',
), (array)$wp_query->data[ 'hero' ] ) );

?>
<div class="<?php echo $id_base; ?>-image" style="min-height: <?php echo $box_height; ?>px;<?php if (!empty($image_src)) { ?> background-image: url(<?php echo $image_src[0]; ?>); background-position: <?php echo $image_alignment; ?>; background-repeat: no-repeat;<?php } ?>">
	<div class="<?php echo $id_base; ?>-gradient">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="<?php echo $id_base; ?>-wrap" >
            <div class="<?php echo $id_base; ?>-content">
              <?php
                if (!empty($title)) { 
                  echo '<h2 class="cfct-mod-title">'.$title.'</h2>';
                }
                if (!empty($content)) { 
                  echo '<div class="cfct-mod-content">'.$content.'</div>';
                }
                if (!empty($url)) {
                  echo '<p><a href="'.$url.'" class="more-link">'.__('Read More', 'carrington-build').'</a></p>';
                }
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
