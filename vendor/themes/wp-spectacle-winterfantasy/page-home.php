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
      <a href="http://www.eventbrite.com/e/winter-fantasy-2014-tickets-14331111741" onclick="javascript:_gaq.push( [ '_link', 'http://www.eventbrite.com/e/winter-fantasy-2014-tickets-14331111741' ] ); return false;" class="buy-tickets" target="_blank">
        <div class="inner">
          Buy
          <strong>Tickets</strong>
        </div>
      </a>

      <p class="main-content">
        Disco Donnie Presents and Global Groove Events are excited to announce that Winter Fantasy is back once again to celebrate its the fourth edition. Sure to put some heat in your season, come join us at the Pharr Events Center in Pharr, Texas on Friday, December 26th and get ready to deck the halls with bass!
      </p>

      <h2>#WinterFantasy</h2>
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



