<?php

namespace WP_Spectacle\Widgets;

/**
 * Class ArtistLineup
 * @package WP_Spectacle\Widgets
 * Artist Lineup Widget
 */
class ArtistLineup extends \WP_Widget
{

  private $_mustache_engine = null;

  public function __construct(){
    parent::__construct( 'wp_spectacle_artist_lineup_widget', __( 'Artist Lineup', 'wp_spectacle_widget_domain' ), array(
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

    $text1 = $instance[ 'text1' ];
    $text2 = $instance[ 'text2' ];
    $text3 = $instance[ 'text3' ];

    if( array_key_exists( 'image_image_id', $instance ) && $instance[ 'image_image_id' ] != '' ){
      $image_src = wp_get_attachment_image_src( $instance[ 'image_image_id' ], 'full' );
      $image_source = $image_src[ 0 ];
    } else{
      $image_source = false;
    }

    if( array_key_exists( 'output', $instance ) && $instance[ 'output' ] == 'html' ){

      echo $this->_mustache_engine->render( 'widget', array(
        'image_source' => $image_source,
        'text1' => $text1,
        'text2' => $text2,
        'text3' => $text3
      ) );

    } else{

      echo $this->_mustache_engine->render( 'json', array(
        'image_source' => $image_source,
        'text1' => $text1,
        'text2' => $text2,
        'text3' => $text3
      ) );

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
    $data[ 'text1' ] = isset( $instance[ 'text1' ] ) ? $instance[ 'text1' ] : '';
    $data[ 'text2' ] = isset( $instance[ 'text2' ] ) ? $instance[ 'text2' ] : '';
    $data[ 'text3' ] = isset( $instance[ 'text3' ] ) ? $instance[ 'text3' ] : '';
    $data[ 'output' ] = ( isset( $instance[ 'output' ] ) && $instance[ 'output' ] == 'html' ) ? true : false;
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
      'text1' => $this->get_field_id( 'text1' ),
      'text1_name' => $this->get_field_name( 'text1' ),
      'text2_id' => $this->get_field_id( 'text2' ),
      'text2_name' => $this->get_field_name( 'text2' ),
      'text3_id' => $this->get_field_id( 'text3' ),
      'text3_name' => $this->get_field_name( 'text3' ),
      'image_id' => $this->get_field_id( 'image' ),
      'image_name' => $this->get_field_name( 'image' ),
      'image_image_id' => $this->get_field_id( 'image_image_id' ),
      'image_image_name' => $this->get_field_name( 'image_image_id' ),
      'output_id' => $this->get_field_id( 'output' ),
      'output_name' => $this->get_field_name( 'output' ),
      'images' => $this->_get_images( $data[ 'selected_image' ] ),
      'text1' => $data[ 'text1' ],
      'text2' => $data[ 'text2' ],
      'text3' => $data[ 'text3' ],
      'output' => $data[ 'output' ]
    );

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

    $instance[ 'text1' ] = ( !empty( $new_instance[ 'text1' ] ) ) ? strip_tags( $new_instance[ 'text1' ] ) : '';
    $instance[ 'text2' ] = ( !empty( $new_instance[ 'text2' ] ) ) ? strip_tags( $new_instance[ 'text2' ] ) : '';
    $instance[ 'text3' ] = ( !empty( $new_instance[ 'text3' ] ) ) ? strip_tags( $new_instance[ 'text3' ] ) : '';
    $instance[ 'output' ] = ( !empty( $new_instance[ 'output' ] ) && $new_instance[ 'output' ] == 'html' ) ? 'html' : 'json';

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