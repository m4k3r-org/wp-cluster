<h3 class="flexslider-title"><?php if( !empty( $title ) ){ echo $title; }; ?></h3>
<div id="slider-wrap">
  <div data-requires="flexslider" class="flexslider" data-sliderHeight="<?php echo $slider_height; ?>" data-animation="<?php echo $slider_animate; ?>" data-slideshowSpeed="<?php echo $slider_pause; ?>" data-animationSpeed="<?php echo $slider_duration; ?>">
    <ul class="slides">
      <?php
        if ( $flex_query->have_posts() ) :
          while( $flex_query->have_posts() ) :
            $flex_query->the_post();
            get_template_part( 'templates/article/listing-post', 'slider' );
          endwhile;
        endif;
        wp_reset_query();
      ?>
    </ul>
  </div>
</div>