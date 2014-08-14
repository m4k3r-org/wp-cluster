<?php

namespace WP_Spectacle;

require_once('artist-lineup/artist-lineup.php');

class Widget_Bootstrap
{
  public function init_widget_areas()
  {
    add_action( 'widgets_init', function (){
      register_sidebar( array(
        'name' => 'Header Widget Area',
        'id' => 'header_widget_area'
      ));
    });

    return $this;
  }

  public function init_artist_lineup()
  {
    // Init widget
    add_action( 'widgets_init', function() {
      register_widget( 'WP_Spectacle\Widgets\ArtistLineup' );
    });

    // Init widget scripts and styles
    add_action( 'wp_enqueue_scripts', function (){
      wp_enqueue_script( 'artist-lineup-widget-admin', __DIR__ .'/artist-lineup/static/scripts/artist-lineup-widget-admin.js', array(), '', true );
    });

    // Add shortcode for widget
    add_shortcode( 'widget_artist_lineup', function( $atts ) {
      // Configure defaults and extract the attributes into variables
      extract( shortcode_atts( array(
        'date' => '',
        'location' => '',
        'image_image_id' => ''
      ), $atts ) );

      ob_start();
      the_widget( 'WP_Spectacle\Widgets\ArtistLineup', $atts, $args );
      $output = ob_get_clean();

      return $output;

    });

    return $this;
  }

}
