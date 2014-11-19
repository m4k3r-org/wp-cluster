<!DOCTYPE html>
<html class="no-js">
<head>
  <meta charset="utf-8">
  <title><?php echo wp_title(); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="apple-touch-icon-precomposed" href="<?php echo get_stylesheet_directory_uri(); ?>/static/images/favicon/favicon-152.png">
  <meta name="msapplication-TileColor" content="#090533">
  <meta name="msapplication-TileImage" content="<?php echo get_stylesheet_directory_uri(); ?>/static/images/favicons/favicon-144.png">

  <link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>">

  <script type="text/javascript">
    var pageMeta = {
      baseUrl: '<?php echo get_stylesheet_directory_uri() ."/"; ?>'
    };
  </script>


  <script type="text/javascript" data-main="<?php echo get_stylesheet_directory_uri(); ?>/static/scripts/src/app" src="http://cdn.udx.io/udx.requires.js"></script>

  <!--
  <script type="text/javascript" src="vendor/components/require.config.js"></script>
  <script type="text/javascript" data-main="app" src="vendor/components/require.js"></script>
  -->

  <!-- Production: does require built assets
  <script type="text/javascript" data-main="/static/scripts/app" src="http://cdn.udx.io/udx.requires.js"></script> -->

  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <div id="doc" <?php if ( !is_front_page()) echo 'class="inner-doc"'; ?>>

    <div class="main-nav-menu">
      <?php if ( has_nav_menu( 'main-navigation' ) ): ?>
        <a href="#" class="main-menu"><span class="icon-spectacle-menu"></span><span class="text">Menu</span></a>
      <?php endif;?>

      <a href="#" class="share-popup share-menu"><span class="icon-spectacle-share"></span><span class="text">Share</span></a>
    </div>