

<h3 class="flexslider-title"><?php if( !empty( $title ) ){
    echo $title;
  }; ?></h3>

<?php
$flex_args = array(
  'cat' => $categories,
  'post_status' => 'publish',
  'post_type' => $post_type_array,
  'showposts' => $slider_count,
  'ignore_sticky_posts' => true,
);

$flex_query = new WP_Query( $flex_args );

// Get Image for Slider
function get_recent_post_flexslider_image( $image_size ){
  $image = '';
  $post_id = get_the_ID();
  $the_title = get_the_title();
  $files = get_children( 'post_parent=' . $post_id . '&post_type=attachment&post_mime_type=image&order=desc' );

  if( has_post_thumbnail() ): // Return Featured Image

    $image = get_the_post_thumbnail( $post_id, $image_size, array(
      'class' => $image_size,
      'title' => $the_title,
      'alt' => $the_title
    ) );

  elseif( $files && !has_post_thumbnail() ): // If no Featured Image search for images inside the post

    $keys = array_reverse( array_keys( $files ) );
    $num = $keys[ 0 ];
    $image_args = wp_get_attachment_image_src( $num, $image_size );
    $image = '<img src="' . $image_args[ 0 ] . '" width="' . $image_args[ 1 ] . '" height="' . $image_args[ 2 ] . '" alt="' . $the_title . '" title="' . $the_title . '" class="' . $image_size . ' wp-post-image"/>';

  endif;

  return $image;
}


// Set Limit of Words in Excerpt
function recent_post_flexslider_excerpt( $string, $word_limit, $more = '&nbsp;&hellip;' ){
  $words = explode( ' ', $string, ( $word_limit + 1 ) );
  if( count( $words ) > $word_limit ){
    array_pop( $words );
    $return = implode( ' ', $words ) . $more;
  } else{
    $return = implode( ' ', $words );
  }

  return $return;
}

/** Generate a random ID */
$id = rand(); ?>

<div id="slider-wrap">
  <div data-requires="flexslider" class="flexslider" <?php
  if( $slider_count == 1 ){
    echo 'style="margin: 0;"';
  } ?>>
    <ul class="slides">
      <?php
      if( $flex_query->have_posts() ) : while( $flex_query->have_posts() ): $flex_query->the_post();
        $output = '<li style="text-align:center; max-height: ' . ( is_numeric( $slider_height ) ? $slider_height . 'px' : 'none' ) . '">';
        $output .= '<a href="' . get_permalink() . '" title="' . get_the_title() . '">';
        $output .= '<div style="height: ' . ( is_numeric( $slider_height ) ? $slider_height . 'px' : 'auto' ) . '">';
        $output .= get_recent_post_flexslider_image( "full" );
        $output .= '</div>';

        if( $post_title == 'true' || $post_excerpt == 'true' ):
          $output .= '<div class="flexslider-caption"><div class="flexslider-caption-inner">';
          if( $post_title == 'true' ):
            $output .= '<h3>' . get_the_title() . '</h3>';
          endif;
          if( $post_excerpt == 'true' ):
            $output .= '<p>' . recent_post_flexslider_excerpt( get_the_excerpt(), $excerpt_length ) . '</p>';
          endif;
          $output .= '</div></div>';
        endif;

        $output .= '</a>';
        $output .= '</li>';

        echo $output;
      endwhile; endif;
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