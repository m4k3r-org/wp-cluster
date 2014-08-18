<?php

namespace WP_Spectacle;

class Core
{
  public function load_styles(){
    add_action( 'admin_enqueue_scripts', function (){
      wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
    });

    return $this;
  }

  public function load_scripts(){
    return $this;
  }

}
