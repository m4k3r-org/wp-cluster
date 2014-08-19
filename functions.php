<?php

namespace WP_Spectacle;

// PSR-0 Autoload
if( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
  require_once( __DIR__ . '/vendor/autoload.php' );
}

require_once('lib/core.php');
require_once('lib/navigation-builder.php');

require_once('lib/widgets/widget-bootstrap.php');


// Initialize Spectacle core
$spectacle = new Core();
$spectacle
  ->load_styles()
  ->load_scripts()
  ->register_navigation();


// Initialize Spectacle custom widgets
$spectacle_widgets = new Widget_Bootstrap();
$spectacle_widgets
    ->init_widget_areas()
    ->init_artist_lineup()
    ->init_contest_countdown()
    ->init_winner();
