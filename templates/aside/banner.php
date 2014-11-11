<?php
/**
 * Banner on Home Page
 *
 * @author Usability Dynamics
 * @module wp-escalade
 * @since wp-escalade 0.1.0
 */
?>
<header class="banner-poster home-banner">
  <div class="container">
    <div class="row">

      <div class="col-md-5 col-sm-5">

        <?php if( get_header_image() ): ?>
        <div class="logo">
          <img class="img-responsive header-logo" src="<?php echo get_header_image(); ?>" alt="" />
        </div>
        <?php endif; ?>

        <div class="event-info">

          <span class="hr"></span>

          <div class="event-time">
            <h6><?php _e('Saturday', wp_festival2( 'domain' )); ?></h6>
            <span>04.12.14</span>
          </div>

          <span class="hr"></span>

          <div class="event-place">
            <div class="row">
              <div class="col-md-5 col-sm-5">
                <span class="label"><?php _e( 'Venue', wp_festival2( 'domain' ) ); ?></span>
                <span class="value"><?php _e('MMF Place Events', wp_festival2( 'domain' )); ?></span>
              </div>
              <div class="col-md-4 col-sm-4">
                <span class="label"><?php _e( 'City', wp_festival2( 'domain' ) ); ?></span>
                <span class="value"><?php _e('New York', wp_festival2( 'domain' )); ?></span>
              </div>
              <div class="col-md-3 col-sm-3">
                <span class="label"><?php _e( 'State', wp_festival2( 'domain' ) ); ?></span>
                <span class="value"><?php _e('London', wp_festival2( 'domain' )); ?></span>
              </div>
            </div>
          </div>

        </div>
      </div>

      <div class="col-md-5 col-sm-5 col-md-offset-2 col-sm-offset-2 right-header">

        <?php get_template_part( 'templates/aside/countdown', get_post_type() ); ?>

        <span class="hr"></span>

        <div class="action-slider">
          <h6><span class="icon icon-ticket"></span><?php _e( 'Tickets', wp_festival2( 'domain' ) ); ?></h6>
          <a data-role="button" class="btn btn-primary btn-lg" href="#"><?php _e( 'Buy Tickets', wp_festival2( 'domain' ) ); ?></a>
        </div>

      </div>

    </div>
  </div>
</header>
