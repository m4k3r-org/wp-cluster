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
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
  <title><?php
    global $page, $paged;
    wp_title( '|', true, 'right' );
    bloginfo( 'name' );
    $site_description = get_bloginfo( 'description', 'display' );
    if ( $site_description && ( is_home() || is_front_page() ) )
      echo " | $site_description";

    if ( $paged >= 2 || $page >= 2 )
      echo ' | ' . sprintf( __( 'Page %s', wp_festival( 'domain' ) ), max( $paged, $page ) );
    ?>
  </title>
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

  <header id="header" class="header">
    <?php wp_festival()->section( 'header' ); ?>
    <?php wp_festival()->section( 'header-banner' ); ?>
    <?php get_template_part( 'templates/nav/top', get_post_type() ); ?>
  </header>


