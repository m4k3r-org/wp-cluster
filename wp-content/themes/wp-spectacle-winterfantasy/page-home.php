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
    <img src="<?php echo get_stylesheet_directory_uri(); ?>/static/images/presenter-logos.png" alt="Disco Donnie Presents ultimo">
  </section>

  <h1 class="main-logo">
    <a href="/">
      <img src="<?php echo get_stylesheet_directory_uri(); ?>/static/images/logo.png" alt="Winter Fantasy">
    </a>
  </h1>

  <?php if (!empty( $artist_lineup ) && !empty( $artist_lineup[ 0 ][ 'data' ][ 'image_source' ] )): ?>
  <div class="container">
    <div class="row">
      <div class="col-xs-12">
        <h5 class="artist-lineup-header"><span>Artist Lineup</span></h5>
      </div>
    </div>
    <div class="artist-logos">
      <img src="<?php echo $artist_lineup[ 0 ][ 'data' ][ 'image_source' ]; ?>" alt="Artist Lineup" class="main-artists">
    </div>
  <?php endif; ?>
</header>

<?php if( !empty( $artist_lineup ) ): ?>

  <div class="diamond-box-container">

    <div class="container">

      <?php if( isset( $artist_lineup[ 0 ] ) ): ?>

        <div class="diamond-box diamond-box-left">
          <div class="inner">
            <span class="icon-calendar"></span>

            <?php echo $artist_lineup[ 0 ][ 'data' ][ 'text1' ]; ?>
            <strong><?php echo $artist_lineup[ 0 ][ 'data' ][ 'text2' ]; ?></strong>
          </div>
        </div>

      <?php endif; ?>


      <?php if( isset( $artist_lineup[ 1 ] ) ): ?>

        <div class="diamond-box diamond-box-right diamond-box-last">
          <div class="inner">
            <span class="icon-location"></span>

            <?php echo $artist_lineup[ 1 ][ 'data' ][ 'text1' ]; ?>
            <strong><?php echo $artist_lineup[ 1 ][ 'data' ][ 'text2' ]; ?></strong>
          </div>
        </div>

      <?php endif; ?>

      <div class="faux-line faux-line-left"></div>
      <div class="faux-line faux-line-right"></div>
    </div>
  </div>

<?php endif; ?>

<div class="clearfix"></div>
<div class="content-faux">
  <div class="content-container clearfix">
    <div class="triangle-top"></div>

    <div class="content-inner">
      <a href="https://www.eventbrite.com/e/freaks-beats-and-treats-2014-featuring-dj-bl3nd-tickets-13056683893" onclick="javascript:_gaq.push( [ '_link', 'https://www.eventbrite.com/e/freaks-beats-and-treats-2014-featuring-dj-bl3nd-tickets-13056683893?aff=spectacle' ] ); return false;" class="buy-tickets" target="_blank">
        <div class="inner">
          Tickets
          <strong>ON SALE</strong>
          <small>Fri, Sep 4 10 AM CT</small>
        </div>
      </a>

      <p class="main-content">
        For all the Ghouls and Boys! The Monster Block Party is a FREE, daytime Halloween festival for Salt Lake City's goblins and ghouls of all ages. There will be trick-or-treating booths, a costume contest with prizes (Kid, Teen and Adult divisions), free arts and crafts projects, a pumpkin drop, live performances and more!
      </p>

      <h2>Social Streame</h2>
      <hr>

      <div class="stream">
        <div class="container">
          <?php the_content(); ?>
        </div>
      </div>

    </div>
  </div>
</div>


<?php
get_template_part( 'page-home', 'contest' );
get_footer();
?>



