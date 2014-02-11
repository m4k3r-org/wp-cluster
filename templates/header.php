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

  <?php get_template_part( 'templates/nav/toolbar' ); ?>

  <div class="super_wrapper">

    <?php get_template_part( 'templates/aside/banner', get_post_type() ); ?>

    <div class="general_header_wrapper">
      <div class="header container clearfix" data-container-type="header">

        <?php get_template_part( 'templates/aside/logo', get_post_type() ); ?>

        <?php get_template_part( 'templates/nav/header', get_post_type() ); ?>

        <?php do_action( 'flawless::header_bottom' ); ?>

      </div>
    </div>

    <div class="content_container clearfix">

      <?php get_template_part( 'templates/aside/notice', get_post_type() ); ?>

      <?php get_template_part( 'templates/nav/sub-menu', get_post_type() ); ?>

      <?php do_action( 'flawless::content_container_top' ); ?>
