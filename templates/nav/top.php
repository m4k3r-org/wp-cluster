<?php
/**
 * Top Navigation Menu.
 *
 * @author Usability Dynamics
 * @module wp-escalade
 * @since wp-escalade 0.1.0
 */
?>
<nav class="navbar navbar-inverse navbar-top" role="navigation">
  <div class="container">

    <div class="social-wrap">
      <a class="btn btn-default" role="button" href="https://www.eventbrite.com/e/ume-2014-tickets-9467005067" data-track><?php _e( 'Buy Tickets', wp_festival( 'domain' ) ); ?></a>
      <div class="no-sticky social-flex">
        <?php echo wp_festival()->nav( 'social', 2 ); ?>
      </div>
      <div class="sticky social-flex">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".social-collapse"><span class="icon icon-plus"></span></button>
        <div class="social-collapse collapse">
          <?php echo wp_festival()->nav( 'social', 2 ); ?>
        </div>
      </div>
    </div>

    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only"><?php _e( 'Toggle navigation', wp_festival( 'domain' ) ); ?></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <span class="navbar-brand">
        <a class="logo sticky" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><img class="img-responsive" src="<?php echo get_template_directory_uri(); ?>/images/temp/small-logo.png" /></a>
      </span>
    </div>

    <nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">
      <?php echo wp_festival()->nav( 'primary', 2 ); ?>
    </nav>

  </div>
</nav>
