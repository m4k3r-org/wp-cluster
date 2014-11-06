<?php
/**
 * Widget Artist Callout
 */
if( !class_exists( 'UsabilityDynamics_Festival2_Widget_Artist_Callout' ) ){
  class UsabilityDynamics_Festival2_Widget_Artist_Callout extends \WP_Widget
  {

    /**
     * Construct
     */
    public function __construct(){

      parent::__construct( 'widget-artist-callout', __( 'Artist Callout' ), array(
        'classname' => 'widget-artist-callout',
        'description' => __( 'Artist callout module.' )
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
      $button1_text = $instance[ 'button1_text' ];
      $button2_text = $instance[ 'button2_text' ];
      $button1_link = $instance[ 'button1_link' ];
      $button2_link = $instance[ 'button2_link' ];
      $artist_id = $instance[ 'artist_id' ];

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

      $instance[ 'artist_id' ] = $new_instance[ 'artist_id' ];
      $instance[ 'text' ] = $new_instance[ 'text' ];
      $instance[ 'button1_text' ] = $new_instance[ 'button1_text' ];
      $instance[ 'button2_text' ] = $new_instance[ 'button2_text' ];
      $instance[ 'button1_link' ] = $new_instance[ 'button1_link' ];
      $instance[ 'button2_link' ] = $new_instance[ 'button2_link' ];

      return $instance;

    }

    /**
     * Generates the administration form for the widget.
     *
     * @param  array  instance  The array of keys and values for the widget.
     */
    public function form( $instance ){

      $data[ 'artist_id' ] = isset ( $instance[ 'artist_id' ] ) ? $instance[ 'artist_id' ] : '';
      $data[ 'text' ] = isset ( $instance[ 'text' ] ) ? $instance[ 'text' ] : '';
      $data[ 'button1_text' ] = isset ( $instance[ 'button1_text' ] ) ? $instance[ 'button1_text' ] : '';
      $data[ 'button2_text' ] = isset ( $instance[ 'button2_text' ] ) ? $instance[ 'button2_text' ] : '';
      $data[ 'button1_link' ] = isset ( $instance[ 'button1_link' ] ) ? $instance[ 'button1_link' ] : '';
      $data[ 'button2_link' ] = isset ( $instance[ 'button2_link' ] ) ? $instance[ 'button2_link' ] : '';

      $data[ 'artists' ] = $this->_get_artists( $data[ 'artist_id' ] );

      // Display the admin form
      include( plugin_dir_path( __FILE__ ) . '/views/admin.php' );

    }

    /**
     * Get the images from the media library.
     *
     * @param null $sel_artist Selected artist id
     *
     * @return array|bool
     */
    private function _get_artists( $sel_artist = null ){
      $artists = new \WP_Query( array(
        'post_type' => 'artist',
        'post_status' => 'publish',
        'posts_per_page' => -1
      ) );

      $ret_val = array();

      if( $artists->have_posts() ){
        while( $artists->have_posts() ){

          $artists->the_post();

          $selected = false;

          if( get_the_ID() == $sel_artist ){
            $selected = true;
          }

          array_push( $ret_val, array(
            'id' => get_the_ID(),
            'name' => get_the_title(),
            'selected' => $selected
          ) );
        }

        return $ret_val;

      } else{
        return false;
      }

    }

  }

}