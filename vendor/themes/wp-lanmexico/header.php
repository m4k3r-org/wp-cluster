<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <?php wp_head(); ?>
  </head>

<body>
  <div id="wrapper" <?php body_class(); ?>>
  
  	<?php do_action('icl_language_selector'); ?>
  
  	<a class="home" href="<?php echo home_url(); ?>" /></a>

		<a href="http://www.ticketmaster.com.mx/event/14004B990EA33B6B" class="tickets"></a>
