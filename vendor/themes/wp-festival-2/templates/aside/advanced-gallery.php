<?php

/**
 * Advanced Gallery Module template
 * @package Festival 2
 */

$fancybox_group = rand();

?>

<section class="photos-videos">

  <!-- Currently not sure about sliding -->
  <!--  <a href="#" class="arrow icon-left-arrow hover-pop"></a>
  <a href="#" class="arrow icon-right-arrow hover-pop"></a>-->

  <h2>
    <span><?php echo !empty( $data['title'] ) ? $data['title'] : 'Title here'; ?></span>
  </h2>

  <div class="photos-videos-strip-container">
    <div class="photos-videos-strip">      
      
      <?php if ( !empty( $data['images'] ) && is_array( $data['images'] ) ) : $i = 0; ?>
        <?php foreach( $data['images'] as $image ) : $i++; ?>
      
            <a href="<?php echo wp_festival2()->get_image_link_by_attachment_id( $image['id'], array( 'width' => 1920, 'height' => 1080 ) ); ?>" class="imagelightbox item item-<?php echo $i; ?>" data-imagelightbox="imagelightbox-<?php echo $fancybox_group; ?>">
              <span style="background-image: url( '<?php echo wp_festival2()->get_image_link_by_attachment_id( $image['id'], array( 'width' => 530, 'height' => 320 ) ); ?>' );" alt="<?php echo $image['id']; ?>"></span>
            </a>
      
        <?php endforeach; ?>
      <?php endif; ?>
      
    </div>

    <div class="indicator-container">
      <div class="indicator-parent">
        <div class="indicator">
          <span class="icon-indicator"></span>
        </div>
      </div>
    </div>

  </div>

</section>
<!-- #photos-videos -->