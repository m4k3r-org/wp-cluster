<li <?php post_class(); ?>>

	<ul class="hdp_event_collapsed clearfix">
		<li class="hdp_event_date"><?php echo date( 'F j, Y', strtotime( $event->meta('dateStart') ) ); ?></li>
		<li class="hdp_event_title"><?php echo $event->post('post_title'); ?></li>
		<li class="hdp_event_city"><?php echo $event->venue()->taxonomies('city'); ?></li>
		<li class="hdp_event_state"><?php echo $event->venue()->taxonomies('state'); ?></li>
	</ul>

	<ul class="hdp_event_expanded clearfix">
		<li class="hdp_event_flyer"><a href="<?php echo get_permalink( $event->post('ID') ); ?>"><img class="fixed_size attachment-events_flyer_thumb" src="<?php echo flawless_image_link( $event->meta('posterImage'), 'events_flyer_thumb' ); ?>"/></a></li>
		<li class="hdp_event_title"><a href="<?php echo get_permalink( $event->post('ID') ); ?>"><?php echo $event->post('post_title'); ?></a></li>
		<li class="hdp_event_date"><span>Date:</span> <?php echo $event->meta('eventDateHuman'); ?></li>
		<li class="hdp_event_venue"><span>Venue:</span>
      <a href="<?php echo get_permalink( $event->venue()->post( 'ID' ) ); ?>">
        <?php echo $event->venue()->post( 'post_title' ); ?>
      </a><br />
      <?php echo $event->venue()->taxonomies( 'city' ); ?>, <?php echo $event->venue()->taxonomies( 'state' ); ?>
    </li>
    <?php if ( $event->artists() ): ?>
      <li class="hdp_event_artists"><span>Artists:</span> <?php echo $event->artists(); ?></li>
    <?php endif; ?>
		<li class="hdp_event_description"><p><?php echo $event->post('post_excerpt'); ?></p></li>
		<li class="hdp_event_information">
		  <?php $time = strtotime( date( 'Y-m-d', current_time( 'timestamp' ) ) . ' 00:00:01 +3 hour' );
      $ticketurl = $event->meta('urlTicket');
      if( isset( $ticketurl ) && strtotime( $event->meta('dateStart') ) > $time ) { ?>
        <a class="btn" href="<?php echo $ticketurl; ?>" <?php if ( $event->meta('disable_cross_domain_tracking') !== 'true' ) { ?>onclick="_gaq.push(['_link', '<?php echo $ticketurl; ?>']); return false;"<?php } ?>><span>Buy Tickets</span></a> <?php
      } ?>
      <a class="btn" href="<?php echo get_permalink( $event->post('ID') ); ?>" title="<?php the_title_attribute(); ?>"><span>More Info</span></a>
    </li>
	</ul>

</li>