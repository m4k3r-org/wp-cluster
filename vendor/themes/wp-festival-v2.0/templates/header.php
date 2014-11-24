<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @author Usability Dynamics
 * @module wp-festival
 * @since festival 2.0.0
 */
?>
<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
  <head>
    <title><?php wp_title( '|', true, 'right' ); ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="utf-8">

    <?php wp_head(); ?>
    
    <?php if( get_theme_mod( 'typography_extra_fonts' ) ): ?>
      <link href='http://fonts.googleapis.com/css?family=<?php echo get_theme_mod( 'typography_extra_fonts' ); ?>' rel='stylesheet' type='text/css'>
    <?php endif; ?>

  </head>
  <body <?php body_class(); ?>>
    <div id="doc" class="page-home">

      <div class="top-nav">
        <h1 class="logo">
          <a href="/" class="logo hover-pop">
            <img src="<?php echo get_theme_mod( 'general_default_header_logo' ); ?>" />
          </a>
        </h1>

        <div class="count-down" data-todate="<?php echo wp_festival2()->settings->get( 'configuration.meta.start_date' ); ?>">
          <span class="days"><strong>0</strong>D</span>
          <span class="hours"><strong>0</strong>H</span>
          <span class="minutes"><strong>0</strong>M</span>
          <span class="seconds"><strong>0</strong>S</span>
        </div>

				<?php get_template_part('templates/aside/language-switcher'); ?>

				<?php
					// Need to use a custom function here instead of wp_festival2->nav()
					$header_nav_items = wp_get_nav_menu_items('header');
					if ( $header_nav_items !== false ):
				?>

						<nav class="main-navigation clearfix <?php if (function_exists( 'icl_get_languages' )) { echo ' has-multilanguage '; } ?>">
							<?php for ( $i = 0, $mi = count($header_nav_items); $i < $mi; $i++ ): ?>
								<a href="<?php echo $header_nav_items[$i]->url; ?>" class="<?php echo implode(' ', $header_nav_items[ $i ]->classes); ?>">
									<?php if ( in_array('menu', $header_nav_items[ $i ]->classes ) ): ?>
										<span class="icon-menu"></span>
									<?php elseif ( in_array('buy-tickets', $header_nav_items[ $i ]->classes ) ): ?>
											<span class="icon-tickets"></span>
									<?php elseif ( in_array('share-popup', $header_nav_items[ $i ]->classes ) ): ?>
										<span class="icon-share"></span>
									<?php endif; ?>
									<span class="text"><?php echo $header_nav_items[ $i ]->title; ?></span>
								</a>
							<?php endfor; ?>
						</nav>
				<?php endif; ?>
      </div>

      <div class="clearfix"></div>

      <?php /** This 'header' should be in the page content, not here
      <header>
        <img src="application/static/images/header-logo-big.png" alt="SCMF" class="scmf-logo-big">

        <div class="container">
          <div class="row">
            <div class="col-xs-12">
              <h4>August 30 &amp; 31, 2014<span><br></span>Ascarate Park, El Paso, TX</h4>
            </div>
          </div>

          <div class="row tickets">
            <div class="col-xs-12 col-sm-6">
              <a href="#" class="buy-tickets ticket ticket-r hover-buzz"><span>Buy Tickets</span></a>
            </div>
            <div class="col-xs-12 col-sm-6">
              <a href="#" class="ticket ticket-l hover-buzz"><span>Festival Pass</span></a>
            </div>
          </div>
        </div>
        <a href="#news-updates" class="nav-arrows clearfix">
          <span class="icon-down-arrow arrow-1"></span>
          <span class="icon-down-arrow arrow-2"></span>
          <span class="icon-down-arrow arrow-3"></span>
        </a>
      </header>

      <?php /** Old, what do we do with this?
      <header id="header" class="header">
        <?php wp_festival2()->section( 'header' ); ?>
        <?php wp_festival2()->section( 'header-banner' ); ?>
        <?php get_template_part( 'templates/nav/top', get_post_type() ); ?>
      </header>  */ ?>