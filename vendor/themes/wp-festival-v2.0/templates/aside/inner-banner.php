<?php
/**
 * Banner aside on inner pages
 *
 * @author Usability Dynamics
 * @module wp-escalade
 * @since wp-escalade 0.1.0
 */
?>
<header class="banner-poster inner-banner">
  <div class="row">

    <div class="col-md-6 col-sm-6">
      <?php if( get_header_image() ): ?>
        <div class="logo">
          <img class="img-responsive header-logo" src="<?php echo get_header_image(); ?>" alt="" />
        </div>
      <?php endif; ?>
    </div>

    <div class="col-md-6 col-sm-6 details">
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <h3><?php _e('March', wp_festival2( 'domain' )); ?></h3>
        </div>
      </div>
      <div class="row days">
        <div class="col-md-4 col-sm-4">
          <div class="day wide"><span class="number">13</span><br/><?php _e('Thursday', wp_festival2( 'domain' )); ?></div>
        </div>
        <div class="col-md-4 col-sm-4">
          <div class="day wide"><span class="number">14</span><br/><?php _e('Friday', wp_festival2( 'domain' )); ?></div>
        </div>
        <div class="col-md-4 col-sm-4">
          <div class="day wide"><span class="number">15</span><br/><?php _e('Saturday', wp_festival2( 'domain' )); ?></div>
        </div>
      </div>
      <div class="row event-place">
        <div class="col-md-4 col-sm-4">
          <div class="wide">
            <span class="label"><?php _e( 'Venue', wp_festival2( 'domain' ) ); ?></span>
            <span class="value"><?php _e('Schlitterbahn Beach Waterpark', wp_festival2( 'domain' )); ?></span>
          </div>
        </div>
        <div class="col-md-4 col-sm-4">
          <div class="wide">
            <span class="label"><?php _e( 'City', wp_festival2( 'domain' ) ); ?></span>
            <span class="value"><?php _e('South Padre Island', wp_festival2( 'domain' )); ?></span>
          </div>
        </div>
        <div class="col-md-4 col-sm-4">
          <div class="wide">
            <span class="label"><?php _e( 'State', wp_festival2( 'domain' ) ); ?></span>
            <span class="value"><?php _e('Texas', wp_festival2( 'domain' )); ?></span>
          </div>
        </div>
      </div>
    </div>

  </div>
</header>
