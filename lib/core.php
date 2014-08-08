<?php

namespace WP_Spectacle;


class Core
{
  public function load_styles()
  {
    add_action('wp_enqueue_scripts', function() {});

    return $this;
  }

  public function load_scripts()
  {
    return $this;
  }

  public function load_widgets()
  {
    add_action( 'widgets_init', function() {
      register_widget('WP_Spectacle\Widgets\PresenterLogos');
    });

    return $this;
  }

  /*
  public function enable_featured_image()
  {
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 1400, 1488 );

    add_image_size('header-bg-1400', 1400);
    add_image_size('header-bg-992', 992);
    add_image_size('header-bg-768', 768);
    add_image_size('header-bg-480', 480);

    return $this;
  }
  */
}
