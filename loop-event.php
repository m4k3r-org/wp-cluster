<?php
/*
 * Loop Archive/Taxonomy Template for Event "hdp_event"
 *
 * @todo Add back in lazy loader for images. The lazyload script has to be re-initialized when DF is done loading. - potanin@UD 5/22/12
 */


//echo '<pre>';
//print_r( $event );
//echo '</pre>';

//echo '<pre>';
//print_r( $venue );
//echo '</pre>';

$header_date = date( 'F j, Y', strtotime( $event->meta('dateStart') ) );
$permalink = get_permalink( $event->post('ID') ); ?>

<li <?php post_class(); ?>>

	<ul class="hdp_event_collapsed clearfix">
		<li class="hdp_event_date"><?php echo $header_date; ?></li>
		<li class="hdp_event_title"><?php echo $event->post('post_title'); ?></li>
		<li class="hdp_event_city"><?php echo $venue->taxonomies('city'); ?></li>
		<li class="hdp_event_state"><?php echo $venue->taxonomies('state'); ?></li>
	</ul>

	<ul class="hdp_event_expanded clearfix">
		<li class="hdp_event_flyer"><a href="<?php echo $permalink; ?>"><img class="fixed_size attachment-events_flyer_thumb" src="<?php echo flawless_image_link( $event->meta('posterImage'), 'events_flyer_thumb' ); ?>"/></a></li>
		<li class="hdp_event_title"><a href="<?php echo $permalink; ?>"><?php echo $event->post('post_title'); ?></a></li>
		<li class="hdp_event_date"><span>Date:</span> <?php echo $event->meta('eventDateHuman'); ?></li>
		<li class="hdp_event_venue"><span>Venue:</span>
      <a href="<?php echo get_permalink( $venue->post( 'ID' ) ); ?>">
        <?php echo $venue->post( 'post_title' ); ?>
      </a><br />
      <?php echo $venue->taxonomies( 'city' ); ?>, <?php echo $venue->taxonomies( 'state' ); ?>
    </li>
		<li class="hdp_event_artists"><span>Artists:</span> <?php echo $event->artists(); ?></li>
		<li class="hdp_event_description"><p><?php echo $event->post('post_excerpt'); ?></p></li>
		<li class="hdp_event_information">
		  <?php $time = strtotime( date( 'Y-m-d', current_time( 'timestamp' ) ) . ' 00:00:01 +3 hour' );
      $ticketurl = $event->meta('urlTicket');
      if( isset( $ticketurl ) && strtotime( $event->meta('dateStart') ) > $time ) { ?>
        <a class="btn" href="<?php echo $ticketurl; ?>" <?php if ( $event->meta('disable_cross_domain_tracking') !== 'true' ) { ?>onclick="_gaq.push(['_link', '<?php echo $ticketurl; ?>']); return false;"<?php } ?>><span>Buy Tickets</span></a> <?php
      } ?>
      <a class="btn" href="<?php echo $permalink; ?>" title="<?php the_title_attribute(); ?>"><span>More Info</span></a>
    </li>
	</ul>

</li>
