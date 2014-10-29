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
          <h3>March</h3>
        </div>
      </div>
      <div class="row days">
        <div class="col-md-4 col-sm-4">
          <div class="day wide"><span class="number">13</span><br/>Thursday</div>
        </div>
        <div class="col-md-4 col-sm-4">
          <div class="day wide"><span class="number">14</span><br/>Friday</div>
        </div>
        <div class="col-md-4 col-sm-4">
          <div class="day wide"><span class="number">15</span><br/>Saturday</div>
        </div>
      </div>
      <div class="row event-place">
        <div class="col-md-4 col-sm-4">
          <div class="wide">
            <span class="label"><?php _e( 'Venue', wp_festival( 'domain' ) ); ?></span>
            <span class="value">Schlitterbahn Beach  Waterpark</span>
          </div>          
        </div>
        <div class="col-md-4 col-sm-4">
          <div class="wide">
            <span class="label"><?php _e( 'City', wp_festival( 'domain' ) ); ?></span>
            <span class="value">South Padre Island</span>
          </div>
        </div>
        <div class="col-md-4 col-sm-4">
          <div class="wide">
            <span class="label"><?php _e( 'State', wp_festival( 'domain' ) ); ?></span>
            <span class="value">Texas</span>
          </div>
        </div>
      </div>
    </div>

  </div>
</header>
