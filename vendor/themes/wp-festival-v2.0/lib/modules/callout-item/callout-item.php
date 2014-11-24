<?php
/**
 * Widget Feature Item
 */
if( !class_exists( 'UsabilityDynamics_Festival2_Widget_Callout_Item' ) ){
  class UsabilityDynamics_Festival2_Widget_Callout_Item extends \WP_Widget {

    /**
     * Construct
     */
    public function __construct(){

      parent::__construct( 'widget-callout-item', __( 'Callout Item' ), array(
        'classname' => 'widget-callout-item',
        'description' => __( 'Block for callout.' )
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

      $text = $instance[ 'text' ];
      $action = $instance[ 'action' ];
      $url = $instance[ 'url' ];
      $background = $instance[ 'background' ];


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

      $instance[ 'text' ] = $new_instance[ 'text' ];
      $instance[ 'action' ] = $new_instance[ 'action' ];
      $instance[ 'url' ] = $new_instance[ 'url' ];
      $instance[ 'background' ] = $new_instance[ 'background' ];

      return $instance;

    }

    /**
     * Generates the administration form for the widget.
     *
     * @param  array  instance  The array of keys and values for the widget.
     */
    public function form( $instance ){

      $data['text'] = isset ( $instance['text'] ) ? $instance['text'] : '';
      $data['action'] = isset ( $instance['action'] ) ? $instance['action'] : '';
      $data['url'] = isset ( $instance['url'] ) ? $instance['url'] : '';
      $data['background'] = isset ( $instance['background'] ) ? $instance['background'] : '0';

      // Display the admin form
      include( plugin_dir_path( __FILE__ ) . '/views/admin.php' );

    }

  }
}