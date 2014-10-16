<?php
/**
 * Widget Video
 */
if( !class_exists( 'UsabilityDynamics_Festival2_Widget_Video' ) ){
  class UsabilityDynamics_Festival2_Widget_Video extends \WP_Widget
  {

    /**
     * Construct
     */
    public function __construct(){

      parent::__construct( 'widget-video', __( 'Video' ), array(
        'classname' => 'widget-video',
        'description' => __( 'Block for video.' )
      ) );

    }

    /**
     * Outputs the content of the widget.
     *
     * @param  array  args    The array of form elements
     * @param  array  instance  The current instance of the widget
     */
    public function widget( $args, $instance ){

      extract( $args, EXTR_SKIP );

      $code = $instance[ 'code' ];
      $background_mp4_url = $instance[ 'background_mp4_url' ];
      $background_webm_url = $instance[ 'background_webm_url' ];
      $background_ogg_url = $instance[ 'background_ogg_url' ];
      $background_mov_url = $instance[ 'background_mov_url' ];

      $image_source = false;

      if( array_key_exists( 'image_image_id', $instance ) ){
        $image_src = wp_get_attachment_image_src( $instance[ 'image_image_id' ], 'full' );
        $image_source = $image_src[ 0 ];
      }

      // Display the widget
      include( plugin_dir_path( __FILE__ ) . '/views/display.php' );

    }

    /**
     * Processes the widget's options to be saved.
     *
     * @param  array  new_instance  The previous instance of values before the update.
     * @param  array  old_instance  The new instance of values to be generated via the update.
     */
    public function update( $new_instance, $old_instance ){

      $instance = $old_instance;

      $instance[ 'code' ] = $new_instance[ 'code' ];
      $instance[ 'background_mp4_url' ] = $new_instance[ 'background_mp4_url' ];
      $instance[ 'background_webm_url' ] = $new_instance[ 'background_webm_url' ];
      $instance[ 'background_ogg_url' ] = $new_instance[ 'background_ogg_url' ];
      $instance[ 'background_mov_url' ] = $new_instance[ 'background_mov_url' ];

      if( array_key_exists( 'image', $new_instance ) ){
        $instance[ 'image' ] = $new_instance[ 'image' ];

        if( !empty( $new_instance[ 'image' ] ) ){

          $images = $this->_get_images( $new_instance[ 'image' ] );
          $images = $images[ 'meta' ];

          $instance[ 'image_image_id' ] = $images[ 'sel_image_id' ];
        } else{
          $instance[ 'image_image_id' ] = null;
        }
      }

      return $instance;

    }

    /**
     * Generates the administration form for the widget.
     *
     * @param  array  instance  The array of keys and values for the widget.
     */
    public function form( $instance ){

      $data[ 'code' ] = isset ( $instance[ 'code' ] ) ? $instance[ 'code' ] : '';
      $data[ 'background_mp4_url' ] = isset ( $instance[ 'background_mp4_url' ] ) ? $instance[ 'background_mp4_url' ] : '';
      $data[ 'background_webm_url' ] = isset ( $instance[ 'background_webm_url' ] ) ? $instance[ 'background_webm_url' ] : '';
      $data[ 'background_ogg_url' ] = isset ( $instance[ 'background_ogg_url' ] ) ? $instance[ 'background_ogg_url' ] : '';
      $data[ 'background_mov_url' ] = isset ( $instance[ 'background_mov_url' ] ) ? $instance[ 'background_mov_url' ] : '';
      $data[ 'selected_image' ] = null;

      if( array_key_exists( 'image', $instance ) ){
        $data[ 'selected_image' ] = $instance[ 'image' ];
      }

      if( array_key_exists( 'image_id', $instance ) ){
        $data[ 'image_id_value' ] = $instance[ 'image_id' ];
      }

      $data[ 'images' ] = $this->_get_images( $data[ 'selected_image' ] );

      // Display the admin form
      include( plugin_dir_path( __FILE__ ) . '/views/admin.php' );

    }

    /**
     * Get the images from the media library.
     *
     * @param null $sel_image Selected image src
     *
     * @return array|bool
     */
    private function _get_images( $sel_image = null ){
      $images = new \WP_Query( array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'post_mime_type' => 'image',
        'posts_per_page' => -1
      ) );

      $ret_val = array(
        'meta' => array(),
        'data' => array()
      );

      if( $images->have_posts() ){
        while( $images->have_posts() ){
          $images->the_post();

          $src = wp_get_attachment_image_src( get_the_ID() );

          $selected = false;
          if( $src[ 0 ] === $sel_image ){
            $selected = true;
            $ret_val[ 'meta' ][ 'sel_image' ] = $sel_image;
            $ret_val[ 'meta' ][ 'sel_image_id' ] = get_the_ID();
          }

          array_push( $ret_val[ 'data' ], array(
            'id' => get_the_ID(),
            'src' => $src[ 0 ],
            'name' => get_the_title(),
            'selected' => $selected
          ) );
        }

        // Populate default value, first image from the media library
        if( empty( $ret_val[ 'meta' ] ) ){
          $ret_val[ 'meta' ][ 'sel_image' ] = $ret_val[ 'data' ][ 0 ][ 'src' ];
          $ret_val[ 'meta' ][ 'sel_image_id' ] = $ret_val[ 'data' ][ 0 ][ 'id' ];
        }

        return $ret_val;
      } else{
        return false;
      }
    }

  }

}