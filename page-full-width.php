<?php
/**
 * Template Name: Splash
 *
 * Page does not contain any sidebars and a minimal header and footer.
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
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <meta name="HandheldFriendly" content="True"/>
    <meta name="MobileOptimized" content="360"/>
    <link rel="apple-touch-icon" href="<?php echo content_url( '/assets/apple-touch-icon-72x72.png', 'relative' ); ?>"/>
    <link rel="apple-touch-icon" href="<?php echo content_url( '/assets/apple-touch-icon-72x72.png', 'relative' ); ?>" sizes="72x72"/>
    <link rel="apple-touch-icon" href="<?php echo content_url( '/assets/apple-touch-icon-114x114.png', 'relative' ); ?>" sizes="114x114"/>
    <link rel="apple-touch-startup-image" href="<?php echo content_url( '/assets/apple-touch-icon-72x72.png', 'relative' ); ?>"/>
    <?php wp_head(); ?>
  </head>

  <body <?php body_class(); ?> data-requires="site">

    <section class="container inner-wrapper entry-<?php echo get_post_type(); ?>">
      <div class="row">
        <section class="content-container">
        <?php while( have_posts() ) : the_post(); ?>
          <?php get_template_part( 'templates/article/content', get_post_type() ); ?>
        <?php endwhile; ?>
        </section>
      </div>
    </section>

    <footer></footer>
  <?php wp_footer(); ?>
  </body>
</html>