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
<html <?php language_attributes(); ?>>
  <head>
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <?php wp_head(); ?>
  </head>
  
  <body <?php body_class( 'wp-festival' ); ?> style="background-image: url(<?php echo get_background_image(); ?>)" data-post-type="<?php get_post_type(); ?>" data-requires="app">

    <?php get_template_part( 'templates/aside/banner', get_post_type() ); ?>

    <?php get_template_part( 'templates/nav/top', get_post_type() ); ?>
    
    <div class="container-wrap theme-showcase">
    