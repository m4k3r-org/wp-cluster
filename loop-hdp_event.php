<?php
/*
 * Loop Archive/Taxonomy Template for Event "hdp_event"
 *
 * @todo Add back in lazy loader for images. The lazyload script has to be re-initialized when DF is done loading. - potanin@UD 5/22/12
 */

if( is_array( $post ) ){
  $event = $post;
}else{
  $event = get_event( $post->ID );
}
$header_date = date( 'F j, Y', strtotime( $event[ 'meta' ][ 'hdp_event_date' ] ) );
$permalink = get_permalink( $event[ 'ID' ] ); ?>

<li <?php post_class(); ?>>

	<ul class="hdp_event_collapsed clearfix">
		<li class="hdp_event_date"><?php echo $header_date; ?></li>
		<li class="hdp_event_title"><?php echo $event[ 'post_title' ]; ?></li>
		<li class="hdp_event_city"><?php echo $event[ 'attributes' ][ 'hdp_city' ]; ?></li>
		<li class="hdp_event_state"><?php echo $event[ 'meta' ][ 'state_code' ]; ?></li>
	</ul>

	<ul class="hdp_event_expanded clearfix">
		<li class="hdp_event_flyer"><a href="<?php echo $permalink; ?>"><img class="fixed_size attachment-events_flyer_thumb" src="<?php echo flawless_image_link( $event[ 'event_poster_id' ] , 'events_flyer_thumb' ); ?>" /></a></li>
		<li class="hdp_event_title"><a href="<?php echo $permalink; ?>"><?php echo $event[ 'post_title' ]; ?></a></li>
		<li class="hdp_event_date"><span>Date:</span> <?php echo $event[ 'summary_qa' ][ 'hdp_event_date' ]; ?></li>
		<li class="hdp_event_venue"><span>Venue:</span> <?php echo $event[ 'summary_qa' ][ 'hdp_venue' ]; ?></li>
		<li class="hdp_event_artists"><span>Artists:</span> <?php echo $event[ 'summary_qa' ][ 'hdp_artist' ]; ?></li>
		<li class="hdp_event_description"><p><?php echo $event[ 'post_excerpt' ]; ?></p></li>
		<li class="hdp_event_information">
		  <?php $time = strtotime( date( 'Y-m-d', current_time('timestamp') ).' 00:00:01 +3 hour' ); if( isset( $event[ 'meta' ][ 'hdp_purchase_url' ] ) && strtotime( $event['meta']['hdp_event_date'].' '.$event['meta']['hdp_event_time'] ) > $time ) { ?>
        <a class="btn" href="<?php echo $event[ 'meta' ][ 'hdp_purchase_url' ]; ?>" <?php if ( $event[ 'meta' ][ 'disable_cross_domain_tracking' ] !== 'true' ) { ?>onclick="_gaq.push(['_link', '<?php echo $event['meta']['hdp_purchase_url']; ?>']); return false;"<?php } ?>><span>Buy Tickets</span></a> <?php
      } ?>
      <a class="btn" href="<?php echo $permalink; ?>" title="<?php the_title_attribute(); ?>"><span>More Info</span></a>
    </li>
	</ul>

</li>
