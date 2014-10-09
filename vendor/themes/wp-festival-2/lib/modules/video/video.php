<?php
/**
 * Widget Video
 */
if( !class_exists( 'UsabilityDynamics_Festival2_Widget_Video' ) ){
  class UsabilityDynamics_Festival2_Widget_Video extends \WP_Widget {

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

      return $instance;

    }

    /**
     * Generates the administration form for the widget.
     *
     * @param  array  instance  The array of keys and values for the widget.
     */
    public function form( $instance ){

      $data['code'] = isset ( $instance['code'] ) ? $instance['code'] : '';

      // Display the admin form
      include( plugin_dir_path( __FILE__ ) . '/views/admin.php' );

    }

  }
}