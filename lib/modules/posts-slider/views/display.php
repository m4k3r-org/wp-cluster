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

<script type="text/javascript">
//  (function( $ ){
//    "use strict";
//    $( function(){
//      jQuery( '#<?php echo $id; ?>.flexslider' ).flexslider( {
//        animation: "<?php echo $slider_animate; ?>",		// String: Set the slideshow animation (either slide or fade)
//        slideshowSpeed: <?php echo $slider_pause; ?>,		// Integer: Set the speed of the slideshow cycling, in milliseconds
//        animationSpeed: <?php echo $slider_duration; ?>,	// Integer: Set the speed of animations, in milliseconds
//      } ); <?php
      if( !is_numeric( $slider_height ) ){ ?>//
//        /** Go through the divs and set the height on all of them */
//        var maxHeight = 0, lis = jQuery( 'div#<?php echo $id; ?>.flexslider ul li' );
//        lis.each( function( i, e ){
//          var height = $( e ).height();
//          if( height > maxHeight ){
//            maxHeight = height;
//          }
//        } );
//        lis.each( function( i, e ){
//          jQuery( 'div:first-child', e ).height( maxHeight + 'px' );
//        } ); <?php
      } ?>//
//    } );
//  }( jQuery ));
</script>
