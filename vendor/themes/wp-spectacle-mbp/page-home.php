<?php
/*
 * Template Name: Homepage
 */
get_header();

the_post();

ob_start();
dynamic_sidebar('header_widget_area');
$artist_lineup = ob_get_clean();

$artist_lineup = str_replace('}{', '},{', $artist_lineup);
$artist_lineup = '[' . $artist_lineup .']';

$artist_lineup = json_decode( $artist_lineup, true );
?>

<header>
  <section class="presenter-logos">
    <img src="<?php echo get_stylesheet_directory_uri(); ?>/static/images/presenter-logos.png" alt="Disco Donnie Presents ultimo">
  </section>

  <a href="https://www.youtube.com/watch?v=9qB21tkD7Ro" class="play-video icon-video hover-pop"></a>

  <h1 class="main-logo">
    <a href="/">
      <img src="<?php echo get_stylesheet_directory_uri(); ?>/static/images/logo.png" alt="Monster Block Party">
    </a>
  </h1>

</header>

<?php if (! empty( $artist_lineup) ): ?>


  <div class="container">
    <div class="row">
      <div class="col-xs-12">
        <h5 class="artist-lineup-header"><span>Artist Lineup</span></h5>
      </div>
    </div>

    <div class="row">
      <div class="col-xs-12">
        <div class="artist-lineup-photo">
          <img src="<?php echo $artist_lineup[0]['data']['image_source']; ?>" alt="Artist Lineup">
        </div>
      </div>
    </div>
  </div>

  <div class="diamond-box-container">

    <div class="container">

    <?php if ( isset( $artist_lineup[0] ) ): ?>

      <div class="diamond-box diamond-box-left">
        <div class="inner">
          <span class="icon-calendar"></span>

          <?php echo $artist_lineup[0]['data'][ 'text1' ]; ?>
          <strong><?php echo $artist_lineup[0]['data'][ 'text2' ]; ?></strong>
        </div>
      </div>

    <?php endif; ?>

    <?php if ( isset( $artist_lineup[1] ) ): ?>

      <div class="diamond-box diamond-box-right diamond-box-last">
        <div class="inner">
          <span class="icon-location"></span>

          <?php echo $artist_lineup[1]['data'][ 'text1' ]; ?>
          <strong><?php echo $artist_lineup[1]['data'][ 'text2' ]; ?></strong>
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
      <a href="http://www.eventbrite.com/e/monster-block-party-2014-tickets-12946873447" onclick="javascript:_gaq.push( [ '_link', 'http://www.eventbrite.com/e/monster-block-party-2014-tickets-12946873447?aff=spectacle' ] ); return false;" class="buy-tickets" target="_blank">
        <div class="inner">
          Buy
          <strong>Tickets</strong>
        </div>
      </a>
      <p class="main-content">
        The streets of Nashville are about to be turned out as Monster Block Party returns to haunt the heart of the the Music City on Saturday, October 25th. Disco Donnie Presents and Ultimo are teaming up once again to craft the spookiest party of them all. Deep into the witching hour, you will dance to the devious sounds of our monster lineup, but beware! For creatures of the night lurk among you in The Gulch.
      </p>

      <h2>#MonsterBlockParty</h2>
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
  get_template_part('page-home', 'contest');
  get_footer();
?>



