<?php

namespace WP_Spectacle;

// PSR-0 Autoload
if( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
  require_once( __DIR__ . '/vendor/autoload.php' );
}

// Load widgets
//require_once('lib/widgets/presenter-logos/presenter-logos.php');

require_once('lib/core.php');


$wp_spectacle = new \WP_Spectacle\Core();
$wp_spectacle
  ->load_styles()
  ->load_scripts();

