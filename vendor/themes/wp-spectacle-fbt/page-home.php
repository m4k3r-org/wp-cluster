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
      <img src="<?php echo get_stylesheet_directory_uri(); ?>/static/images/logo.png" alt="Monster Block Party">
    </a>
  </h1>

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
          Buy
          <strong>Tickets</strong>
        </div>
      </a>

      <p class="main-content">
        Join Disco Donnie Presents and Global Groove Events as we craft another crazy Halloween event filled with massive beats and eerie treats! Freaks, Beats, and Treats is making its return to Hidalgo, TX to haunt the State Farm Arena (Outdoors) this Hallows Eve. This year we have DJ Bl3ND headlining the stage and taking us deep into the night. With his big bass drops and piercing drum claps, DJ BL3ND is sure get us rocking at this year's event. Meet us on the dance floor this Friday, October 31st and come dressed in your freaky best!
      </p>

      <h2>#FreaksBeatsTreats</h2>
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



