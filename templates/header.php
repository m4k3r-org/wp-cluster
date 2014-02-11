<?php
/**
 * Theme Header
 *
 */

if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
  return;
}

?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
  <title><?php wp_title(); ?></title>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>


<header class="frame" data-container-type="header">
  <?php get_template_part( 'templates/nav/toolbar', get_post_type() ); ?>
  <?php get_template_part( 'templates/aside/header', get_post_type() ); ?>
  <?php get_template_part( 'templates/aside/banner', get_post_type() ); ?>
</header>
