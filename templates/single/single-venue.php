<?php get_header(); ?>

<?php get_template_part( 'attention', 'venue' ); ?>

<?php $venue = new \DiscoDonniePresents\Venue( get_the_ID(), false ); the_post(); ?>

<div class="<?php flawless_wrapper_class( 'tabbed-content' ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"<?php microdata_type( $venue, '', true ); ?>>
  
  <?php microdata_meta( $venue, array( 'url' ), true ); ?>
  <?php if ( $venue->meta('geo_located') ) : ?>
  <span class="meta"<?php microdata_manual( 'geo', 'GeoCoordinates', true ); ?>>
    <span<?php microdata_manual( 'latitude', '', true ); ?>><?php echo $venue->meta('latitude'); ?></span>
    <span<?php microdata_manual( 'longitude', '', true ); ?>><?php echo $venue->meta('longitude'); ?></span>
  </span>
  <?php endif; ?>

  <div class="cfct-block sidebar-left span4 first visible-desktop">
    <div class="cfct-module" style="padding: 0; margin: 0;">

    <?php if ( $venue->meta( 'imageLogo' ) ) : ?>
    <div class="visible-desktop dd_featured_image_wrap <?php echo $venue->meta( 'imageLogo' ) ? 'have_image' : 'no_image'; ?>">
      <?php echo $venue->image( 'imageLogo', 'sidebar_poster', true ); ?>
    </div>
    <?php endif; ?>

    <ul class="dd_side_panel_nav">

      <li class="visible-desktop link first ui-tabs-selected"><a href="#section_event_details"><i class="icon-info-blue icon-dd"></i> Info</a></li>

      <li class="visible-desktop link">
        <a href="#section_event">
          <i class="icon-hdp_event icon-dd"></i> <?php _e('Events'); ?>
          <span class="comment_count" data-bind="html:events.total"></span>
        </a>
      </li>

      <li class="visible-desktop link">
        <a href="#section_hdp_photo_gallery">
          <i class="icon-hdp_photo_gallery icon-dd"></i> <?php _e('Photos'); ?>
          <span class="comment_count" data-bind="html:photos.total"></span>
        </a>
      </li>

      <li class="visible-desktop link">
        <a href="#section_hdp_video">
          <i class="icon-hdp_video icon-dd"></i> <?php _e('Videos'); ?>
          <span class="comment_count" data-bind="html:videos.total"></span>
        </a>
      </li>

      <?php if( $venue->meta('geo_located') ) { ?>
       <li class="visible-desktop link"><a href="#section_map"><i class="hdp_venue icon-dd"></i> Location Map</a></li>
      <?php } ?>

    </ul>

    <div class="visible-desktop" style="height: 50px;"></div>

    </div>
  </div>

  <div class="<?php flawless_block_class( 'main cfct-block span8' ); ?>">

    <div class="<?php flawless_module_class( 'taxonomy-archive' ); ?>">

      <div id="section_event_details">

        <header class="entry-title-wrapper term-title-wrapper">
          <?php flawless_breadcrumbs(); ?>
          <h1 class="entry-title"><?php echo $venue->post('post_title', array() ); ?></h1>
        </header>

        <div class="entry-content clearfix">

          <?php if ( $venue->meta( 'imageLogo' ) ) : ?>
          <div class="poster-iphone hidden-desktop">
            <?php echo $venue->image( 'imageLogo', 'sidebar_poster', true ); ?>
          </div>
          <hr class="hidden-desktop"/>
          <?php endif; ?>

          <div class="category_description taxonomy">
          <?php the_content(); ?>
          </div>
          <hr class="dotted visible-desktop" style="margin-top:5px;"/>

          <?php if ( $venue->meta('geo_located') ) :?>
          <div class="tax_address">
            <span>Address:</span>
            <?php echo $venue->meta('locationAddress', null, array( 'super_type' => 'PostalAddress', 'super_prop' => 'address' ) ); ?>
          </div>
          <?php endif; ?>

          <?php if ( $venue->meta( 'officialLink' ) ) : ?>
          <div class="official_link">
            <span>Official Website:</span>
            <a href="<?php echo $venue->meta( 'officialLink' ); ?>" target="_blank"<?php microdata_manual( 'sameAs', '', true ); ?>><?php echo $venue->meta( 'officialLink' ); ?></a>
          </div>
          <?php endif; ?>

          <?php if ( $venue->meta( 'socialLinks' ) ) { ?>
          <ul class="tax_meta">
            <?php foreach( $venue->meta( 'socialLinks' ) as $link ) : ?>
            <li><a href="<?php echo $link; ?>" target="_blank"><?php echo $link; ?></a></li>
            <?php endforeach; ?>
          </ul>
          <?php } ?>

        </div>

      </div>

      <div id="section_event">
        <h1><?php echo $venue->post('post_title'); ?> <?php _e('Events'); ?></h1>

        <form data-scope="events" data-bind="elasticFilter:{
          middle_timepoint: { gte: 'now-1d', lte: 'now-1d' },
          per_page: 15, period: false, period_field: 'start_date', sort_by: 'start_date', type: 'event', location_field: 'venue.address.geo',
          custom_query: { filter: { bool: { must: [{ term: { 'venue.name': '<?php echo addslashes($venue->post('post_title')); ?>'}}]}}},
          return_fields: ['start_date','description','summary','venue.address.city','venue.address.state','url','image.poster','venue.name','artists.name','tickets']}" class="elastic_form">
        </form>

        <?php get_template_part('templates/elastic/loop', 'event'); ?>

      </div>

      <div id="section_hdp_photo_gallery">
        <h1><?php echo $venue->post('post_title'); ?> <?php _e('Photos'); ?></h1>

        <form data-scope="photos" data-bind="elasticFilter:{
          middle_timepoint: { gte: 'now-1d', lte: 'now-1d' },
          per_page: 6, period: false, period_field: 'event_date', sort_by: 'event_date', type: 'imagegallery', location_field: 'venue.address.geo',
          custom_query: { filter: { bool: { must: [{ term: { 'venue.name': '<?php echo addslashes($venue->post('post_title')); ?>'}}]}}},
          return_fields: [ 'summary', 'url', 'image.small', 'image.poster', 'event_date', 'venue.address.state', 'venue.address.city']}" class="elastic_form">
        </form>

        <?php get_template_part('templates/elastic/loop', 'imagegallery'); ?>

      </div>

      <div id="section_hdp_video">
        <h1><?php echo $venue->post('post_title'); ?> <?php _e('Videos'); ?></h1>

        <form data-scope="videos" data-bind="elasticFilter:{
          middle_timepoint: { gte: 'now-1d', lte: 'now-1d' },
          per_page: 6, period: false, period_field: 'event_date', sort_by: 'event_date', type: 'videoobject', location_field: 'venue.address.geo',
          custom_query: { filter: { bool: { must: [{ term: { 'venue.name': '<?php echo addslashes($venue->post('post_title')); ?>'}}]}}},
          return_fields: [ 'summary', 'url', 'image.small', 'image.poster', 'event_date', 'venue.address.state', 'venue.address.city']}" class="elastic_form">
        </form>

        <?php get_template_part('templates/elastic/loop', 'videoobject'); ?>

      </div>

      <?php if( $venue->meta('geo_located') ) { ?>
        <div id="section_map" class="inner not-for-iphone not-for-ipad">
          <div id="event_location" style="height: 400px; width: 100%;"></div>
        </div>
      <?php } ?>

    </div>

  </div>

</div>

<?php echo '<script type="text/javascript">var hdp_current_venue = jQuery.parseJSON( ' . json_encode( json_encode( $venue ) ) . ' ); </script>'; ?>

<?php get_footer(); ?>
