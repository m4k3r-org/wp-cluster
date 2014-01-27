<?php
/**
 * Header on Home Page
 *
 * @author Usability Dynamics
 * @module wp-escalade  
 * @since wp-escalade 0.1.0
 */

global $festival;

?>
<!-- Header Starts -->
<header id="poster">
  <div class="container">
    <div class="row">
      <div class="col-md-5 col-sm-5">
        <div class="logo">
          <img class="img-responsive" src="<?php echo get_template_directory_uri(); ?>/images/temp/header-logo.png" alt="" />
        </div>
        <div class="event-info">
          <span class="hr"></span>
          <div class="event-time">
            <h6>Saturday</h6>
            <span>04.12.14</span>
          </div>
          <span class="hr"></span>
          <div class="event-place">
            <div class="row">
              <div class="col-md-5 col-sm-5">
                <span class="label"><?php _e( 'Venue', $festival->text_domain ); ?></span>
                <span class="value">MMF Place Events</span>
              </div>
              <div class="col-md-4 col-sm-4">
                <span class="label"><?php _e( 'City', $festival->text_domain ); ?></span>
                <span class="value">New York</span>
              </div>
              <div class="col-md-3 col-sm-3">
                <span class="label"><?php _e( 'State', $festival->text_domain ); ?></span>
                <span class="value">London</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-5 col-sm-5 col-md-offset-2 col-sm-offset-2 right-header">
        <div class="countdown">
          <h6><span class="icon icon-time"></span> <?php _e( 'Count Down', $festival->text_domain ); ?></h6>
          <?php get_template_part( 'templates/aside/countdown', get_post_type() ); ?>
        </div>
        <span class="hr"></span>
        <div class="action-slider">
          <h6><span class="icon icon-ticket"></span> <?php _e( 'Tickets', $festival->text_domain ); ?></h6>
          <a role="button" class="btn btn-primary btn-lg" href="#"><?php _e( 'Buy Tickets', $festival->text_domain ); ?></a>
        </div>
      </div>
    </div>
  </div>
</header>
<!-- /Header -->