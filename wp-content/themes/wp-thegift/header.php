<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <?php wp_head(); ?>
  </head>

<body>
  <div id="wrapper" <?php body_class(); ?>>
  
  <a class="home" href="<?php echo home_url(); ?>" /></a>

  <a onclick="_gaq.push(['_link', 'https://deadmau5tampa.eventbrite.com/']); return false;" href="https://deadmau5tampa.eventbrite.com/" class="tickets"></a>
