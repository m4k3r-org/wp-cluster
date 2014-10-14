<?php
/**
 * Widget News Block
 */
if( !class_exists( 'UsabilityDynamics_Festival2_Widget_News_Block' ) ){
  class UsabilityDynamics_Festival2_Widget_News_Block extends \WP_Widget
  {

    /**
     * Construct
     */
    public function __construct(){

      parent::__construct( 'widget-news-block', __( 'News Block' ), array(
        'classname' => 'widget-news-block',
        'description' => __( 'News block.' )
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

      $featured = $instance[ 'featured' ];

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

      $instance[ 'featured' ] = $new_instance[ 'featured' ];

      return $instance;

    }

    /**
     * Generates the administration form for the widget.
     *
     * @param  array  instance  The array of keys and values for the widget.
     */
    public function form( $instance ){

      $data[ 'featured' ] = isset ( $instance[ 'featured' ] ) ? $instance[ 'featured' ] : '0';

      // Display the admin form
      include( plugin_dir_path( __FILE__ ) . '/views/admin.php' );

    }

  }
}