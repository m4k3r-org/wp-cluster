<?php
/**
 * Widget Company Item
 */
if( !class_exists( 'UsabilityDynamics_Festival2_Widget_Company_Item' ) ){
  class UsabilityDynamics_Festival2_Widget_Company_Item extends \WP_Widget
  {

    /**
     * Construct
     */
    public function __construct(){

      parent::__construct( 'widget-company-item', __( 'Company Item' ), array(
        'classname' => 'widget-company-item',
        'description' => __( 'Block for organizers or sponsors page.' )
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

      $title = $instance[ 'title' ];
      $description = $instance[ 'description' ];
      $background = $instance[ 'background' ];
      $url = $instance[ 'url' ];
      $button_text = $instance[ 'button_text' ];
      $is_sponsor_leadin = $instance[ 'is_sponsor_leadin' ];

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

      $instance[ 'title' ] = $new_instance[ 'title' ];
      $instance[ 'description' ] = $new_instance[ 'description' ];
      $instance[ 'background' ] = $new_instance[ 'background' ];
      $instance[ 'url' ] = $new_instance[ 'url' ];
      $instance[ 'button_text' ] = $new_instance[ 'button_text' ];
      $instance[ 'is_sponsor_leadin' ] = $new_instance[ 'is_sponsor_leadin' ];

      if( array_key_exists( 'image', $new_instance ) ){
        $instance[ 'image' ] = $new_instance[ 'image' ];

        if ( !empty( $new_instance[ 'image' ] ) ){
          $images = $this->_get_images( $new_instance[ 'image' ] );
          $images = $images[ 'meta' ];

          $instance[ 'image_image_id' ] = $images[ 'sel_image_id' ];
        }
        else{
          $instance[ 'image_image_id' ] = null;
        }
      }
      /*
            if( array_key_exists( 'image_image_id', $new_instance ) ){
              $instance[ 'image_image_id' ] = $new_instance[ 'image_image_id' ];
            }*/

      return $instance;

    }

    /**
     * Generates the administration form for the widget.
     *
     * @param  array  instance  The array of keys and values for the widget.
     */
    public function form( $instance ){

      $data[ 'title' ] = isset ( $instance[ 'title' ] ) ? $instance[ 'title' ] : '';
      $data[ 'description' ] = isset ( $instance[ 'description' ] ) ? $instance[ 'description' ] : '';
      $data[ 'background' ] = isset ( $instance[ 'background' ] ) ? $instance[ 'background' ] : '';
      $data[ 'url' ] = isset ( $instance[ 'url' ] ) ? $instance[ 'url' ] : '';
      $data[ 'button_text' ] = isset ( $instance[ 'button_text' ] ) ? $instance[ 'button_text' ] : '';
      $data[ 'is_sponsor_leadin' ] = isset ( $instance[ 'is_sponsor_leadin' ] ) ? $instance[ 'is_sponsor_leadin' ] : '';
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
      $images = new \WP_Query( [
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'post_mime_type' => 'image',
        'posts_per_page' => -1
      ] );

      $ret_val = [
        'meta' => [ ],
        'data' => [ ]
      ];

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

          array_push( $ret_val[ 'data' ], [
            'id' => get_the_ID(),
            'src' => $src[ 0 ],
            'name' => get_the_title(),
            'selected' => $selected
          ] );
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