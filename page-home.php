<?php
/*
 * Template Name: Homepage
 */
get_header();

the_post();

ob_start();
dynamic_sidebar( 'header_widget_area' );
$artist_lineup = ob_get_clean();

$artist_lineup = str_replace( '}{', '},{', $artist_lineup );
$artist_lineup = '[' . $artist_lineup . ']';
$artist_lineup = json_decode( $artist_lineup, true );

?>

<header>
  <section class="presenter-logos">
    <img src="<?php echo get_stylesheet_directory_uri(); ?>/static/images/presenter-logos.png" alt="Disco Donnie Presents GlobalGroove London">
  </section>

  <h1 class="main-logo">
    <a href="/">
      <img src="<?php echo get_stylesheet_directory_uri(); ?>/static/images/main-logo-big.png" alt="Isla Del Sol">
    </a>
  </h1>
</header>

<?php if( isset( $artist_lineup[ 0 ] ) ): ?>

  <div class="diamond-box diamond-box-left">
    <div class="inner">
      <strong>
        <?php echo $artist_lineup[ 0 ][ 'data' ][ 'text1' ]; ?>
        <br><?php echo $artist_lineup[ 0 ][ 'data' ][ 'text2' ]; ?>
      </strong>

      <?php echo $artist_lineup[ 0 ][ 'data' ][ 'text3' ]; ?>

      <hr>
      <img src="<?php echo $artist_lineup[ 0 ][ 'data' ][ 'image_source' ] ?>" alt="Day 1">
    </div>
  </div>

<?php endif; ?>

<?php if( isset( $artist_lineup[ 1 ] ) ): ?>

  <div class="diamond-box diamond-box-right">
    <div class="inner">
      <strong>
        <?php echo $artist_lineup[ 1 ][ 'data' ][ 'text1' ]; ?>
        <br><?php echo $artist_lineup[ 1 ][ 'data' ][ 'text2' ]; ?>
      </strong>
      <?php echo $artist_lineup[ 1 ][ 'data' ][ 'text3' ]; ?>

      <hr>
      <span>Exlusive VIP Pool Party</span>

      <img src="<?php echo $artist_lineup[ 1 ][ 'data' ][ 'image_source' ] ?>" alt="Day 2">

      <small>Plus Over 20 Regional Artists</small>
    </div>
  </div>

<?php endif; ?>

<div class="clearfix"></div>
<div class="content-container clearfix">
  <div class="triangle-top"></div>
  <div class="content-inner">

    <a href="http://www.eventbrite.com/e/isla-del-sol-2014-tickets-12353536761?aff=spectacle" onclick="javascript:_gaq.push( [ '_link', 'http://www.eventbrite.com/e/isla-del-sol-2014-tickets-12353536761?aff=spectacle' ] ); return false;" class="buy-tickets" target="_blank">
      <div class="inner">
        Buy
        <strong>Tickets</strong>
      </div>
    </a>

    <h2>Join Us</h2>

    <p class="main-content">The summer season may be coming to a close, but we'll be going out with a bang. This Labor Day weekend, Isla Del Sol Fest is returning to South Padre Island and for the third edition, we’re taking over two new locations – Schlitterbahn Beach Waterpark on Saturday, August 30th and Peninsula Island Resort & Spa on Sunday, August 31st.</p>

    <h1>#isla2014</h1>
    <hr>

    <div class="stream">
      <div class="container">
        <?php the_content(); ?>
      </div>
    </div>

  </div>
</div>

<?php get_template_part( 'page-home', 'contest' ); ?>


<?php get_footer(); ?>


