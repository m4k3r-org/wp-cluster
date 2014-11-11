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
  <h1 class="logo"><a href="/">Coming Home Music Festival 2014</a></h1>

  <h2 class="lead-in">
    <span class="icon-logo2"></span>
    <span class="icon-logo1"></span>
  </h2>

  <a href="https://www.youtube.com/watch?v=zLL_PFjbAkI" class="play-video icon-video hover-pop"></a>

  <?php if (!empty( $artist_lineup )): ?>

  <img src="<?php echo $artist_lineup[ 0 ][ 'data' ][ 'image_source' ]; ?>" alt="Artist Lineup" class="main-artists">
</header>

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

<img src="<?php echo get_stylesheet_directory_uri(); ?>/static/images/content-image-left.jpg" alt="Content image left" class="content-image-left">
<img src="<?php echo get_stylesheet_directory_uri(); ?>/static/images/content-image-right.jpg" alt="Content image right" class="content-image-right">


<div class="content-faux">
  <div class="content-container clearfix">
    <div class="triangle-top"></div>

    <div class="content-inner">
      <a href="http://www.eventbrite.com/e/coming-home-music-festival-presents-life-in-color-unleash-tickets-12124188775?aff=spectacle" onclick="javascript:_gaq.push( [ '_link', 'http://www.eventbrite.com/e/coming-home-music-festival-presents-life-in-color-unleash-tickets-12124188775?aff=spectacle' ] ); return false;" class="buy-tickets" target="_blank">
        <div class="inner">
          Buy
          <strong>Tickets</strong>
        </div>
      </a>

      <h2>#licwindsor</h2>
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



