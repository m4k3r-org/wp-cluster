<?php get_header(); ?>

<?php get_template_part( 'attention', 'event' ); ?>

<?php $event = new \DiscoDonniePresents\Event( get_the_ID() ); the_post(); ?>

<div class="<?php flawless_wrapper_class( 'tabbed-content' ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"<?php microdata_type( $event, '', true ); ?>>
  
  <?php microdata_meta( $event, array( 'url', 'startDate', 'endDate' ), true ); ?>
  
  <div class="cfct-block sidebar-left span4 first">
    <div class="cfct-module" style="padding: 0; margin: 0;">
    
    <?php if ( $event->meta( 'posterImage' ) ) : ?>
    <div class="visible-desktop dd_featured_image_wrap <?php echo $event->meta('posterImage') ? 'have_image' : 'no_image'; ?>">
      <?php
      $img_src = wp_get_attachment_image_src( $event->meta( 'posterImage' ), 'full' );
      echo '<a href="' . $img_src[0] . '">';
      echo $event->image( 'posterImage', 'sidebar_poster', true );
      echo '</a>';
      ?>
    </div>
    <?php endif; ?>

    <ul class="dd_side_panel_nav">
      <li class="visible-desktop link first ui-tabs-active"><a href="#section_event_details"><i class="icon-events icon-dd"></i> <?php _e('Event Details'); ?></a></li>

      <?php if( post_type_supports( $event->type(), 'comments' ) && $event->post('comment_status') == 'open' ) { ?>
        <li class="visible-desktop link"><a href="#section_comments"><i class="icon-comments-blue icon-dd"></i> Comments</a></li>
      <?php } ?>

      <?php if( $event->venue()->meta('locationGoogleMap') ) { ?>
        <li class="visible-desktop link"><a href="#section_map"><i class="hdp_venue icon-dd"></i> Location Map</a></li>
      <?php } ?>

    </ul>

    <ul class="dd_side_panel_actions">
      <?php $time = strtotime( date( 'Y-m-d', current_time( 'timestamp' ) ) . ' 00:00:01 +3 hour' );
      if( $event->meta('urlTicket') && strtotime( $event->meta('dateStart') ) > $time ) { ?>
        <li class=""><a class="btn btn-blue" href="<?php echo $event->meta('urlTicket'); ?>" <?php if ($event->meta( 'disable_cross_domain_tracking' ) !== 'true') { ?>onclick="_gaq.push(['_link', '<?php echo $event->meta('urlTicket'); ?>']); return false;"<?php } ?>>Buy Tickets</a></li>
      <?php } ?>

      <?php if( $event->meta('urlRsvp') ) { ?>
        <li class=""><a class="btn btn-purple" href="<?php echo $event->meta('urlRsvp'); ?>">RSVP On Facebook</a></li>
      <?php } ?>
    </ul>

    <div class="visible-desktop" style="height: 50px;"></div>

    </div>
  </div>

  <div class="<?php flawless_block_class( 'main cfct-block span8' ); ?>">

    <div id="post-<?php the_ID(); ?>" class="<?php flawless_module_class( '' ); ?>">

      <header class="entry-title-wrapper">
        <?php flawless_breadcrumbs(); ?>
        <h1 class="entry-title"><?php echo $event->post('post_title', array() ); ?></h1>
        <p class="event_tagline"><?php echo $event->post('post_excerpt'); ?></p>
      </header>

      <hr class="dotted"/>
      
      <?php if ( $event->meta( 'posterImage' ) ) : ?>
      <div class="poster-iphone hidden-desktop">
        <?php echo $event->image( 'posterImage', 'sidebar_poster', true ); ?>
      </div>
      <hr class="dotted hidden-desktop"/>
      <?php endif; ?>

      <div id="section_event_details" class="inner">

        <div class="event_meta_wrapper row-fluid">

          <div class="span6">

            <span class="event_meta_label"><i class="hdp_event_date icon-dd"></i> <?php _e('Date'); ?></span>
            <span class="event_meta_value"><?php echo $event->meta('eventDateHuman'); ?></span>

            <span class="event_meta_label"><i class="hdp_event_time icon-dd"></i> <?php _e('Time'); ?></span>
            <span class="event_meta_value"><?php echo $event->meta('eventTimeHuman'); ?></span>
            
            <?php if( $event->taxonomies( 'age-limit' ) != '' ) : ?>
            <span class="event_meta_label"><i class="hdp_age_limit icon-dd"></i> <?php _e('Age Limit'); ?></span>
            <span class="event_meta_value"><?php echo $event->taxonomies( 'age-limit' ); ?></span>
            <?php endif; ?>

            <span class="event_meta_label"><i class="hdp_venue icon-dd"></i> <?php _e('Venue'); ?></span>
            <span class="event_meta_value">
              <?php microdata_link( $event->venue(), 'span', 'location', ', ', '', '<br />' . $event->venue()->taxonomies( 'city', 'link', ', ', array( 'super_prop' => 'contained_in' ) ) . ', ' . $event->venue()->taxonomies( 'state', 'link', ', ', array( 'super_prop' => 'contained_in' ) ), false, true ); ?>
            </span>

          </div>

          <div class="span6">

            <?php if ( $event->promoters() != '' ): ?>
            <span class="event_meta_label"><i class="hdp_artist icon-dd"></i> <?php _e('Promoter'); ?></span>
            <span class="event_meta_value"><?php echo $event->promoters(); ?></span>
            <?php endif; ?>

            <?php if ( $event->taxonomies( 'event-type' ) != '' ): ?>
            <span class="event_meta_label"><i class="hdp_type icon-dd"></i> <?php _e('Type'); ?></span>
            <span class="event_meta_value"><?php echo $event->taxonomies( 'event-type', 'link', ', ', array() ); ?></span>
            <?php endif; ?>

            <?php if ( $event->genre() != '' ): ?>
            <span class="event_meta_label"><i class="hdp_genre icon-dd"></i> <?php _e('Genre'); ?></span>
            <span class="event_meta_value"><span<?php microdata_manual( 'workPerformed', 'CreativeWork', true ); ?>><?php echo $event->genre( 'link', ', ', array() ); ?></span></span>
            <?php endif; ?>

            <?php if( $event->tour() ): ?>
            <span class="event_meta_label"><i class="hdp_tour icon-dd"></i> <?php _e('Tour'); ?></span>
            <span class="event_meta_value">
              <a href="<?php echo get_permalink( $event->tour()->post( 'ID' ) ); ?>">
                <?php echo $event->tour()->post( 'post_title' ); ?>
              </a>
            </span>
            <?php endif; ?>

            <?php if ( $event->artists() != '' ): ?>
            <span class="event_meta_label"><i class="hdp_promoter icon-dd"></i> <?php _e('Artist'); ?></span>
            <span class="event_meta_value"><?php microdata_link( $event->artists( 'raw' ), 'span', 'performer', ', ', '', '', false, true ); ?></span>
            <?php endif; ?>

          </div>

        </div>

        <hr class="dotted"/>

        <div class="entry-content clearfix">

          <?php the_content( 'More Info' ); ?>

        </div>

      </div>

      <?php if( post_type_supports( $event->type(), 'comments' ) && $event->post('comment_status') == 'open' ) { ?>
      <div id="section_comments" class="inner">
        <?php comments_template(); ?>
      </div>
      <?php } ?>

      <?php if( $event->venue()->meta('locationGoogleMap') ) { ?>
      <div id="section_map" class="inner not-for-iphone not-for-ipad">
        <div id="event_location" style="height: 400px; width: 100%;"></div>
      </div>
      <?php } ?>

    </div>

  </div>


  <?php flawless_widget_area( 'right_sidebar' ); ?>

</div>

<?php echo '<script type="text/javascript">var hdp_current_event = jQuery.parseJSON( ' . json_encode( json_encode( $event ) ) . ' ); </script>'; ?>

<?php get_footer(); ?>