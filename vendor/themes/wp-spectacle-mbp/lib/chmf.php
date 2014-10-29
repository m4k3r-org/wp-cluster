<?php

namespace WP_Spectacle\CHMF;


class Core extends \WP_Spectacle\Core
{
  public function load_styles()
  {
    add_action('wp_enqueue_scripts', function() {});

    return $this;
  }

  public function load_scripts()
  {
    parent::load_scripts();

    return $this;
  }

  public function load_widgets()
  {
    add_action( 'widgets_init', function() {
      register_widget('WP_Spectacle\Widgets\PresenterLogos');
    });

    return $this;
  }
}
