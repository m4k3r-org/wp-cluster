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
    parent::__construct( 'wp_spectacle_contest_countdown_widget', __( 'Contest Countdown', 'wp_spectacle_widget_domain' ), [
      'description' => __( 'Part of WP Spectacle', 'wp_spectacle_widget_domain' )
    ] );

    // Set up the mustache engine
    $this->_mustache_engine = new \Mustache_Engine( [
      'loader' => new \Mustache_Loader_FilesystemLoader( dirname( __FILE__ ) . '/templates' ),
      'escape' => function ( $value ){
          return esc_attr( $value );
        },
      'strict_callables' => true
    ] );
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
    $date1 = null;
    $date2 = null;

    $valid_widget = true;

    $errors = [ ];

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


    if( $valid_widget ){

        echo $this->_mustache_engine->render( 'json', [
          'title' => $title,
          'description' => $description,
          'date1' => $date1,
          'date2' => $date2
        ] );

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
    $data[ 'title' ] = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '';
    $data[ 'description' ] = isset( $instance[ 'description' ] ) ? $instance[ 'description' ] : '';
    $data[ 'date1' ] = isset( $instance[ 'date1' ] ) ? $instance[ 'date1' ] : '';
    $data[ 'date2' ] = isset( $instance[ 'date2' ] ) ? $instance[ 'date2' ] : '';
    $data[ 'dates' ] = isset( $instance[ 'dates' ] ) ? $instance[ 'dates' ] : [['value' => '']];


    // Populate the template data
    $data = [
      'title_id' => $this->get_field_id( 'title' ),
      'title_name' => $this->get_field_name( 'title' ),
      'description_id' => $this->get_field_id( 'description' ),
      'description_name' => $this->get_field_name( 'description' ),

      'dates_id' => $this->get_field_id( 'dates' ),
      'dates_name' => $this->get_field_name( 'dates' ),

      'date1_id' => $this->get_field_id( 'date1' ),
      'date1_name' => $this->get_field_name( 'date1' ),
      'date2_id' => $this->get_field_id( 'date2' ),
      'date2_name' => $this->get_field_name( 'date2' ),

      'title' => $data[ 'title' ],
      'description' => $data[ 'description' ],
      'dates' => $data[ 'dates' ]
    ];

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
    $instance[ 'date1' ] = ( !empty( $new_instance[ 'date1' ] ) ) ? strip_tags( $new_instance[ 'date1' ] ) : '';
    $instance[ 'date2' ] = ( !empty( $new_instance[ 'date2' ] ) ) ? strip_tags( $new_instance[ 'date2' ] ) : '';

    return $instance;
  }
}