<?php

/**
 * Widget Posts Slider
 */

class Widget_Posts_Slider extends WP_Widget {

  /**
   * Construct
   */
  public function __construct(){

    parent::__construct( 'widget-posts-slider', __( 'Posts Slider' ), array(
      'classname' => 'widget-posts-slider',
      'description' => __( 'A responsive slider of posts.' )
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
    $categories = $instance[ 'categories' ];
    $tag = $instance[ 'tags' ];
    $post_type = $instance[ 'post_type' ];
    $slider_duration = $instance[ 'slider_duration' ];
    $slider_pause = $instance[ 'slider_pause' ];
    $slider_count = $instance[ 'slider_count' ];
    $slider_height = $instance[ 'slider_height' ];
    $slider_animate = $instance[ 'slider_animate' ];
    $excerpt_length = $instance[ 'excerpt_length' ];

    $post_category = isset( $instance[ 'post_category' ] ) ? 'true' : 'false';
    $post_title = isset( $instance[ 'post_title' ] ) ? 'true' : 'false';
    $post_date = isset( $instance[ 'post_date' ] ) ? 'true' : 'false';
    $post_excerpt = isset( $instance[ 'post_excerpt' ] ) ? 'true' : 'false';

    echo $before_widget;

    $post_types = get_post_types();
    unset( $post_types[ 'page' ], $post_types[ 'attachment' ], $post_types[ 'revision' ], $post_types[ 'nav_menu_item' ] );

    if( $post_type == 'all' ){
      $post_type_array = $post_types;
    } else{
      $post_type_array = $post_type;
    }

    $flex_args = array(
      'cat' => $categories,
      'tag_id' => $tag,
      'post_status' => 'publish',
      'post_type' => $post_type_array,
      'showposts' => $slider_count,
      'ignore_sticky_posts' => true,
    );

    $flex_query = new WP_Query( $flex_args );

    include( plugin_dir_path( __FILE__ ) . '/views/display.php' );

    echo $after_widget;

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
    $instance[ 'categories' ] = $new_instance[ 'categories' ];
    $instance[ 'tags' ] = $new_instance[ 'tags' ];
    $instance[ 'post_type' ] = $new_instance[ 'post_type' ];
    $instance[ 'slider_duration' ] = $new_instance[ 'slider_duration' ];
    $instance[ 'slider_pause' ] = $new_instance[ 'slider_pause' ];
    $instance[ 'slider_count' ] = $new_instance[ 'slider_count' ];
    $instance[ 'slider_height' ] = $new_instance[ 'slider_height' ];
    $instance[ 'slider_animate' ] = $new_instance[ 'slider_animate' ];
    $instance[ 'post_category' ] = $new_instance[ 'post_category' ];
    $instance[ 'post_title' ] = $new_instance[ 'post_title' ];
    $instance[ 'post_date' ] = $new_instance[ 'post_date' ];
    $instance[ 'post_excerpt' ] = $new_instance[ 'post_excerpt' ];
    $instance[ 'excerpt_length' ] = $new_instance[ 'excerpt_length' ];

    return $instance;

  }

  /**
   * Generates the administration form for the widget.
   *
   * @param  array  instance  The array of keys and values for the widget.
   */
  public function form( $instance ){

    $defaults = array(
      'title' => '',
      'categories' => 'all',
      'post_type' => 'post',
      'slider_duration' => '1000',
      'slider_pause' => '3000',
      'slider_count' => 3,
      'slider_height' => '', //300,
      'slider_animate' => 'slide',
      'post_category' => 'on',
      'post_title' => 'on',
      'post_date' => 'on',
      'post_excerpt' => 'on',
      'excerpt_length' => 20
    );
    $instance = wp_parse_args( (array) $instance, $defaults );

    // Display the admin form
    include( plugin_dir_path( __FILE__ ) . '/views/admin.php' );

  }

}