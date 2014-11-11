<?php

namespace WP_Spectacle\Widgets;

/**
 * Class InsertImage
 * @package WP_Spectacle\Widgets
 * Insert Image Widget
 */
class InsertImage extends \WP_Widget
{

  private $_mustache_engine = null;

  public function __construct(){
    parent::__construct( 'wp_spectacle_insert_image_widget', __( 'Insert Image', 'wp_spectacle_widget_domain' ), array(
      'description' => __( 'Part of WP Spectacle', 'wp_spectacle_widget_domain' )
    ) );

    // Set up the mustache engine
    $this->_mustache_engine = new \Mustache_Engine( array(
      'loader' => new \Mustache_Loader_FilesystemLoader( dirname( __FILE__ ) . '/templates' ),
      'escape' => function ( $value ){
          return esc_attr( $value );
        },
      'strict_callables' => true
    ) );
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

  /**
   * Display the widget.
   *
   * @param array $args
   * @param array $instance
   */
  public function widget( $args, $instance ){
    $image_source = null;
    $type = null;

    $valid_widget = true;

    $errors = array();

    if( array_key_exists( 'type', $instance ) ){
      $type = $instance[ 'type' ];
    } else{
      $valid_widget = false;
      $errors[ ] = 'missing type';
    }

    if( array_key_exists( 'image_image_id', $instance ) ){
      $image_src = wp_get_attachment_image_src( $instance[ 'image_image_id' ], 'full' );
      $image_source = $image_src[ 0 ];
    } else{
      $valid_widget = false;
      $errors[ ] = 'missing image id';
    }

    if( $valid_widget ){

      echo $this->_mustache_engine->render( 'json', array(
        'image_source' => $image_source,
        'type' => $type
      ) );

    } else{
      echo 'Broken widget: ' . implode( ', ', $errors );
    }

  }

  /**
   * Admin form for the widget
   *
   * @param array $instance
   *
   * @return string|void
   */
  public function form( $instance ){
    // Get the selected image if any
    $data[ 'type' ] = isset( $instance[ 'type' ] ) ? $instance[ 'type' ] : '';
    $data[ 'selected_image' ] = null;

    // Get saved data
    if( array_key_exists( 'image', $instance ) ){
      $data[ 'selected_image' ] = $instance[ 'image' ];
    }

    if( array_key_exists( 'image_id', $instance ) ){
      $data[ 'image_id_value' ] = $instance[ 'image_id' ];
    }

    // Populate the template data
    $data = array(
      'type_id' => $this->get_field_id( 'type' ),
      'type_name' => $this->get_field_name( 'type' ),
      'image_id' => $this->get_field_id( 'image' ),
      'image_name' => $this->get_field_name( 'image' ),
      'image_image_id' => $this->get_field_id( 'image_image_id' ),
      'image_image_name' => $this->get_field_name( 'image_image_id' ),
      'images' => $this->_get_images( $data[ 'selected_image' ] ),
      'type' => $data[ 'type' ]
    );

    // No images found in the media library
    if( $data[ 'images' ] === false ){
      $data[ 'error' ] = true;
    }

    echo $this->_mustache_engine->render( 'admin-form', $data );
  }

  /**
   * Save the admin form.
   *
   * @param array $new_instance
   * @param array $old_instance
   *
   * @return array
   */
  public function update( $new_instance, $old_instance ){
    $instance = array();

    $instance[ 'type' ] = ( !empty( $new_instance[ 'type' ] ) ) ? strip_tags( $new_instance[ 'type' ] ) : '';

    if( array_key_exists( 'image', $new_instance ) ){
      $instance[ 'image' ] = $new_instance[ 'image' ];

      $images = $this->_get_images( $new_instance[ 'image' ] );
      $images = $images[ 'meta' ];

      $instance[ 'image_image_id' ] = $images[ 'sel_image_id' ];
    }

    if( array_key_exists( 'image_image_id', $new_instance ) ){
      $instance[ 'image_image_id' ] = $new_instance[ 'image_image_id' ];
    }

    return $instance;
  }
}