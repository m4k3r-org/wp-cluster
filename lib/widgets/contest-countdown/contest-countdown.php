<?php

namespace WP_Spectacle\Widgets;

/**
 * Class ContestCountdown
 * @package WP_Spectacle\Widgets
 * Contest Countdown Widget
 */
class ContestCountdown extends \WP_Widget
{

  private $_mustache_engine = null;

  public function __construct(){
    parent::__construct( 'wp_spectacle_contest_countdown_widget', __( 'Contest Countdown', 'wp_spectacle_widget_domain' ), array(
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
   * Display the widget.
   *
   * @param array $args
   * @param array $instance
   */
  public function widget( $args, $instance ){
    $title = null;
    $description = null;
    $dates = array();
    $valid_widget = true;
    $errors = array();

    if( array_key_exists( 'title', $instance ) ){
      $title = $instance[ 'title' ];
    } else{
      $valid_widget = false;
      $errors[ ] = 'missing title';
    }

    if( array_key_exists( 'description', $instance ) ){
      $description = $instance[ 'description' ];
    } else{
      $valid_widget = false;
      $errors[ ] = 'missing description';
    }

    if( array_key_exists( 'dates', $instance ) && !empty( $instance[ 'dates' ] ) ){
      $dates = $instance[ 'dates' ];
    } else{
      $valid_widget = false;
      $errors[ ] = 'missing description';
    }

    if( $valid_widget ){

      echo $this->_mustache_engine->render( 'json', array(
        'json' => json_encode( array(
          'data' => $instance,
          'type' => 'widget_contest_countdown'
        ) )
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

    $timezones = array(
      array( 'value' => '-12' ),
      array( 'value' => '-11' ),
      array( 'value' => '-9' ),
      array( 'value' => '-8' ),
      array( 'value' => '-7' ),
      array( 'value' => '-6' ),
      array( 'value' => '-5' ),
      array( 'value' => '-4' ),
      array( 'value' => '-3' ),
      array( 'value' => '-2' ),
      array( 'value' => '-1' ),
      array( 'value' => '0' ),
      array( 'value' => '+1' ),
      array( 'value' => '+2' ),
      array( 'value' => '+3' ),
      array( 'value' => '+4' ),
      array( 'value' => '+5' ),
      array( 'value' => '+6' ),
      array( 'value' => '+7' ),
      array( 'value' => '+8' ),
      array( 'value' => '+9' ),
      array( 'value' => '+10' ),
      array( 'value' => '+11' ),
      array( 'value' => '+12' )
    );

    $data[ 'title' ] = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '';
    $data[ 'description' ] = isset( $instance[ 'description' ] ) ? $instance[ 'description' ] : '';
    $data[ 'dates' ] = array();

    if( isset( $instance[ 'dates' ] ) && is_array( $instance[ 'dates' ] ) && !empty( $instance[ 'dates' ] ) ){
      for( $i = 0, $mi = count( $instance[ 'dates' ] ); $i < $mi; $i++ ){
        $data[ 'dates' ][ ] = array( 'value' => $instance[ 'dates' ][ $i ], 'cnt' => $i );
      }
    } else{
      $data[ 'dates' ][ ] = array( array( 'value' => '', 'cnt' => 0 ) );
    }

    if( array_key_exists( 'timezone', $instance ) ){

      for( $i = 0, $mi = count( $timezones ); $i < $mi; $i++ ){
        if( $timezones[ $i ][ 'value' ] == $instance[ 'timezone' ] ){
          $timezones[ $i ][ 'selected' ] = true;
        }
      }
    }

    // Populate the template data
    $data = array(
      'title_id' => $this->get_field_id( 'title' ),
      'title_name' => $this->get_field_name( 'title' ),
      'description_id' => $this->get_field_id( 'description' ),
      'description_name' => $this->get_field_name( 'description' ),
      'timezone_id' => $this->get_field_id( 'timezone' ),
      'timezone_name' => $this->get_field_name( 'timezone' ),
      'dates_id' => $this->get_field_id( 'dates' ),
      'dates_name' => $this->get_field_name( 'dates' ),
      'title' => $data[ 'title' ],
      'description' => $data[ 'description' ],
      'dates' => $data[ 'dates' ],
      'timezones' => $timezones
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
    $instance[ 'title' ] = ( !empty( $new_instance[ 'title' ] ) ) ? strip_tags( $new_instance[ 'title' ] ) : '';
    $instance[ 'description' ] = ( !empty( $new_instance[ 'description' ] ) ) ? strip_tags( $new_instance[ 'description' ] ) : '';
    $instance[ 'dates' ] = ( !empty( $new_instance[ 'dates' ] ) ) ? $new_instance[ 'dates' ] : array();
    $instance[ 'timezone' ] = ( isset( $new_instance[ 'timezone' ] ) ) ? $new_instance[ 'timezone' ] : '0';

    if( isset( $instance[ 'dates' ] ) && is_array( $instance[ 'dates' ] ) && !empty( $instance[ 'dates' ] ) ){
      for( $i = 0, $mi = count( $instance[ 'dates' ] ); $i < $mi; $i++ ){

        if( empty( $instance[ 'dates' ][ $i ] ) ){
          unset( $instance[ 'dates' ][ $i ] );
        }
      }
    }

    $instance[ 'dates' ] = array_values( $instance[ 'dates' ] );

    return $instance;
  }
}