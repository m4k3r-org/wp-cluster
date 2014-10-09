<?php

namespace WP_Spectacle;

// PSR-0 Autoload
if( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
  require_once( __DIR__ . '/vendor/autoload.php' );
}

require_once('lib/core.php');
require_once('lib/navigation-builder.php');

require_once('lib/widgets/widget-bootstrap.php');
require_once('lib/shortcodes.php');

// Initialize Spectacle core
$spectacle = new Core();
$spectacle
  ->load_styles()
  ->load_scripts()
  ->register_navigation()
  ->add_featured_image();

// Initialize Spectacle custom widgets
$spectacle_widgets = new Widget_Bootstrap();
$spectacle_widgets
    ->init_widget_areas()
    ->init_artist_lineup()
    ->init_contest_countdown()
    ->init_winner()
    ->init_buy_ticket()
    ->init_insert_image()
    ->init_hotel();


// Init shortcodes
$ids_shortcodes = new Shortcodes();
$ids_shortcodes
  ->register_highlighted_notes()
  ->register_list()
  ->register_tabs()
  ->register_list_content()
  ->register_info_page_artist_lineup_wrapper()
  ->register_highlighted_background()
  ->register_horizontal_line()
  ->register_icon()
  ->register_spacer()
  ->register_box();

