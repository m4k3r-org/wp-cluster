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
  <title><?php wp_title( '|', true, 'right' ); ?></title>
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?> style="background-image: url(<?php echo get_background_image(); ?>)" data-post-type="<?php get_post_type(); ?>">

  <header id="header" class="header">
    <div class="container"><?php wp_festival()->aside( 'header' ); ?></div>
  </header>

  <header id="banner" class="banner-poster" role="banner" data-requires="banner.poster">
    <div class="container"><?php wp_festival()->aside( 'banner' ); ?></div>
  </header>

  <?php get_template_part( 'templates/nav/top', get_post_type() ); ?>

  <div id="wrapper" class="container-wrap" style="background-color:<?php echo get_option( 'content_bg_color', '#f2f2f2' ); ?>" role="wrapper">
