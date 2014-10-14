<div class="video-container video-module-container" id="video-module-container">

  <div class="background-content" data-background-video-url="<?php echo $background_video_url; ?>">
    <div class="video-container">
      <video autoplay loop muted id="bgvid" class="bgvideo">
        <?php if( !empty( $background_mp4_url ) ) : ?>
          <source src="<?php echo $background_mp4_url; ?>" type="video/mp4">
        <?php endif; ?>

        <?php if( !empty( $background_webm_url ) ) : ?>
          <source src="<?php echo $background_webm_url; ?>" type="video/webm">
        <?php endif; ?>

        <?php if( !empty( $background_ogg_url ) ) : ?>
          <source src="<?php echo $background_ogg_url; ?>" type="video/ogg">
        <?php endif; ?>

        <?php if( !empty( $background_mov_url ) ) : ?>
          <source src="<?php echo $background_mov_url; ?>" >
        <?php endif; ?>

      </video>
    </div>

    <div class="image-container">
      <?php
      if( empty( $image_source ) ){
        $image_source = 'http://img.youtube.com/vi/' . $code . '/0.jpg';
      }
      ?>
      <img class="bgimage" src="<?php echo $image_source; ?>" alt="thumbnail" />
    </div>

    <div class="background-overlay">
      <a href="http://www.youtube.com/watch?v=<?php echo $code; ?>" class="watch-video">Watch
        <span><i class="icon-video"></i></span> Video</a>
    </div>
  </div>

  <div class="video-content" data-ytcode="<?php echo $code; ?>" style="display: none;">
    <div id="video-module-frame"></div>
    <a href="#" class="close-video">
      <i class="icon-close"></i>
    </a>
  </div>
</div>