<?php

namespace WP_Spectacle;

class Core
{
  public function load_styles(){
    return $this;
  }

  public function load_scripts(){
    return $this;
  }

  public function register_navigation()
  {
    add_action( 'init', function() {
      register_nav_menu( 'main-navigation', __( 'Main Navigation' ) );
      register_nav_menu( 'footer-navigation', __( 'Footer Navigation' ) );
      register_nav_menu( 'footer-profiles-navigation', __( 'Footer Profiles' ) );
    });

    return $this;
  }

  public function add_featured_image()
  {
    // Add post thumbnails
    add_theme_support( 'post-thumbnails' );

    // Set the featured image sizes
    set_post_thumbnail_size(700);
    set_post_thumbnail_size(2000);
  }
}
