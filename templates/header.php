<!DOCTYPE html>
<!--[if lt IE 7]>
<html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html <?php language_attributes(); ?> class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"> <!--<![endif]-->
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>"/>
  <title><?php wp_title( '' ); ?></title>
  <link rel="profile" href="http://gmpg.org/xfn/11"/>
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>"/>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header>
  <div class="container">
  <div class="navbar">
    <div class="navbar-inner">
      <a class="brand" href="index.html"><img src="images/restart-logo.png" width="90" height="90" alt="optional logo"><span class="logo_title">{re}<strong>start</strong></span><span class="logo_subtitle">a clean &amp; multipurpose template</span></a>
      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"><span class="nb_left pull-left"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span></span> <span class="nb_right pull-right">menu</span> </a>
    <div class="nav-collapse collapse">
       <ul class="nav pull-right">
        <li><a href="index.html">Home</a></li>
        <li><a href="about_us.html">About Us</a></li>
        <li><a href="services.html">Services</a></li>
        <li><a href="portfolio.html">Portfolio</a></li>
        <li><a href="blog.html">Blog</a></li>
        <li><a href="contact.html">Contact</a></li>
        <li class="dropdown"> <a data-toggle="dropdown" class="dropdown-toggle" href="#">Pages<span class="caret"></span></a>
             <ul class="dropdown-menu">
              <li><a href="home_alternative.html">Home Alternative</a></li>
              <li><a href="page_alternative.html">Page Alternative</a></li>
              <li><a href="gallery.html">Portfolio Masonry</a></li>
              <li><a href="portfolio_item.html">Portfolio Item</a></li>
              <li><a href="portfolio_item_2.html">Portfolio Item II</a></li>
              <li><a href="single_post.html">Single Post</a></li>
              <li><a href="404.html">ERROR 404</a></li>
              <li><a href="register.html">Register or Sign in <span class="label label-important">new</span></a></li>
              <li><a href="elements.html">Bootstrap Elements</a></li>
             </ul>
        </li>
       </ul>
    </div>
    </div>
    </div>
    <div id="social_media_wrapper"> <a href="#facebook"><i class="icon icon-facebook"></i></a> <a href="#twitter"><i class="icon icon-twitter"></i></a> <a href="#googleplus"><i class="icon icon-google-plus"></i></a> </div>
    <div id="sign"><a href="register.html"><i class="icon icon-user"></i>Register/Sign in</a></div>
  </div>
</header>

<?php do_action( 'header-navbar' ); ?>

<div id="main">

  <div class="container">