<?php

namespace WP_Spectacle\Widgets;

/**
 * Class BuyTicket
 * @package WP_Spectacle\Widgets
 * Buy Ticket Widget
 */
class BuyTicket extends \WP_Widget
{

  private $_mustache_engine = null;

  public function __construct(){
    parent::__construct( 'wp_spectacle_buy_ticket_widget', __( 'Buy Ticket', 'wp_spectacle_widget_domain' ), array(
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
    $url = null;

    $valid_widget = true;

    $errors = array();

    if( array_key_exists( 'title', $instance ) ){
      $title = $instance[ 'title' ];
    } else{
      $valid_widget = false;
      $errors[ ] = 'missing title';
    }

    if( array_key_exists( 'url', $instance ) ){
      $url = $instance[ 'url' ];
    } else{
      $valid_widget = false;
      $errors[ ] = 'missing url';
    }

    if( $valid_widget ){

        echo $this->_mustache_engine->render( 'json', array(
          'title' => $title,
          'url' => $url
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
    $data[ 'title' ] = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '';
    $data[ 'url' ] = isset( $instance[ 'url' ] ) ? $instance[ 'url' ] : '';

    // Populate the template data
    $data = array(
      'title_id' => $this->get_field_id( 'title' ),
      'title_name' => $this->get_field_name( 'title' ),
      'url_id' => $this->get_field_id( 'url' ),
      'url_name' => $this->get_field_name( 'url' ),
      'title' => $data[ 'title' ],
      'url' => $data[ 'url' ]
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
    $instance[ 'url' ] = ( !empty( $new_instance[ 'url' ] ) ) ? strip_tags( $new_instance[ 'url' ] ) : '';

    return $instance;
  }
}