<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @author Usability Dynamics
 * @module festival  
 * @since festival 0.1.0
 */
 
global $festival;

$cc = array();
$cc[] = $festival->get_query_template();
$cc[] = ( is_home() || is_front_page() ) ? 'front-page' : 'inner-page';
 
?><!DOCTYPE html>
<!--[if IE 7]> <html class="ie ie7" <?php language_attributes(); ?> <![endif]-->
<!--[if IE 8]> <html class="ie ie8" <?php language_attributes(); ?> <![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]> <html <?php language_attributes(); ?> <!--<![endif]-->
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <?php wp_head(); ?>
  </head>
  
  <body class="">

    <?php if( is_home() || is_front_page() ) : ?>
      <?php get_template_part( 'templates/aside/home-header', get_post_type() ); ?>
    <?php endif; ?>
    
    <?php get_template_part( 'templates/nav/top', get_post_type() ); ?>
    
    <div class="container-wrap theme-showcase <?php echo implode( ' ', $cc ); ?>">
    