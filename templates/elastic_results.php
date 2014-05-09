<div class="elastic_filter" data-bind="elastic-filter: { <?php echo $elastic; ?> }, if: elastic_ready">

  <!-- Events -->
  <!-- ko if: $data.settings.index() == 'hdp_event' -->
    <!-- ko if: $data.documents().length -->
      <div>

        <div class="ef_sort_event hdp_sort clearfix" data-bind="visible: documents().length">
          <div class="ef_results_sorter hdp_results_sorter_distance df_element df_sorter">
          <span class="hdp_sort_text">Sort By:</span>
          <div attribute_key="hdp_event_date" data-field="time" data-bind="click: sort_by, attr:{'class':'df_element df_sortable_button df_sortable_'+is_active_sort('time')}">Date</div>
          <div attribute_key="distance" data-field="_geo_distance" data-bind="click: sort_by, attr:{'class':'df_element df_sortable_button df_sortable_'+is_active_sort('_geo_distance')}">Distance</div>
        </div>
        </div>

        <!-- Header Row -->
        <ul id="hdp_results_header_event" class="hdp_results_header clearfix" data-bind="visible: documents().length">
          <li class="hdp_event_time">Date</li>
          <li class="hdp_event_name">Name</li>
          <li class="hdp_event_city">City</li>
          <li class="hdp_event_state">State</li>
        </ul>

        <div data-bind="attr:{'class':'elastic_results hdp_results hdp_results_'+state()}, template: { name: 'template-event', afterRender: document_after_render, foreach: documents }"></div>

      </div>

      <div class="hdp_results_message">
        <span class="hdp_results_total">
          <span class="hdp_results_showing">Showing <strong data-bind="text: documents().length"></strong></span> of <strong data-bind="text: total"></strong> events.
        </span>
        <!-- ko if: have_more() -->
          <a data-bind="click: load_more" class="btn"><span>Show More</span></a>
        <!-- /ko -->
        <div class="clearfix"></div>
      </div>
    <!-- /ko -->
    <!-- ko if: !$data.documents().length -->
    <div>Nothing found...</div>
    <!-- /ko -->
  <!-- /ko -->

  <!-- Photos -->
  <!-- ko if: $data.settings.index() == 'hdp_photo_gallery' -->
    <div class="dynamic_filter df_element df_top_wrapper df_element df_top_wrapper clearfix">

      <div class="df_element hdp_results clearfix">

        <!-- ko if: $data.documents().length -->
          <ul data-bind="attr:{'class':'elastic_results df_element hdp_results_items hdp_results hdp_results_'+state()}, template: { name: 'template-photo', afterRender: document_after_render, foreach: documents }"></ul>

          <div class="clearfix"></div>
          <div class="hdp_results_message">
            <span class="hdp_results_total">
              <span class="hdp_results_showing">Showing <strong data-bind="text: documents().length"></strong></span> of <strong data-bind="text: total"></strong> galleries.
            </span>
            <!-- ko if: have_more() -->
              <a data-bind="click: load_more" class="btn"><span>Show More</span></a>
            <!-- /ko -->
            <div class="clearfix"></div>
          </div>
        <!-- /ko -->

        <!-- ko if: !$data.documents().length -->
          <div>Nothing found...</div>
        <!-- /ko -->

      </div>

    </div>
  <!-- /ko -->

  <!-- Videos -->
  <!-- ko if: $data.settings.index() == 'hdp_video' -->
    <div class="dynamic_filter df_element df_top_wrapper df_element df_top_wrapper clearfix">

      <div class="df_element hdp_results clearfix">

        <!-- ko if: $data.documents().length -->
          <ul data-bind="attr:{'class':'elastic_results df_element hdp_results_items hdp_results hdp_results_'+state()}, template: { name: 'template-photo', afterRender: document_after_render, foreach: documents }"></ul>

          <div class="clearfix"></div>
          <div class="hdp_results_message">
            <span class="hdp_results_total">
              <span class="hdp_results_showing">Showing <strong data-bind="text: documents().length"></strong></span> of <strong data-bind="text: total"></strong> videos.
            </span>
            <!-- ko if: have_more() -->
              <a data-bind="click: load_more" class="btn"><span>Show More</span></a>
            <!-- /ko -->
            <div class="clearfix"></div>
          </div>
        <!-- /ko -->

        <!-- ko if: !$data.documents().length -->
          <div>Nothing found...</div>
        <!-- /ko -->

      </div>

    </div>
  <!-- /ko -->

</div>

<!-- Event Result Item -->
<script type="text/html" id="template-event">

  <div class="ef_row" data-bind="if: $data.body.venue, click: hddp.toggle_row, attr: { 'data-id': $data.id } ">

  	<ul class="hdp_event_collapsed clearfix">
  		<li class="hdp_event_date" data-field="time" data-bind="text: hddp.time( $data.body.time )"></li>
  		<li class="hdp_event_title" data-field="title" data-bind="text: $data.body.title"></li>
  		<li class="hdp_event_city" data-field="venue.location.city" data-bind="text: $data.body.venue.location.city"></li>
  		<li class="hdp_event_state" data-field="venue.location.state_code" data-bind="attr: { title: $data.body.venue.location.state }, text: $data.body.venue.location.state_code"></li>
  	</ul>

    <ul class="hdp_event_expanded clearfix">

    	<li class="hdp_event_flyer">
    	  <a href="#" data-bind="attr: { href: $data.body.url } " class="events_flyer_wrapper">
    	    <img class="fixed_size attachment-events_flyer_thumb" src="<?php echo get_template_directory_uri() ?>/img/1x1-pixel.png" data-field="thumbnail" data-bind="attr: { 'src': $data.body.thumbnail }"/>
    	  </a>
    	</li>

      <li class="hdp_event_title"><a href="#" data-field="url" data-bind="attr: { href: $data.body.url }, text: $data.body.title"></a></li>

    	<li class="hdp_event_date">
        <span style="float: left;">Date:&nbsp;</span>
        <span class="hdp_normal_text" data-bind="text: hddp.time( $data.body.time, 'dddd, MMMM D, YYYY' )"></span>
      </li>

    	<li class="hdp_event_venue" data-bind="with: $data.body.venue">
        <span>Venue:</span>
        <a href="#" data-field="venue.name" data-bind="attr: { href: $data.url }, text: $data.name"></a>,
        <a href="#" data-field="venue.location.city" data-bind="attr: { href: $data.url }, text: $data.location.city"><a>,
        <a href="#" data-field="venue.location.state_code" data-bind="attr: { href: $data.url }, text: $data.location.state"></a>
      </li>

      <li class="hdp_event_artists" data-bind="visible: $data.body.artists">
        <span>Artists:</span>
        <!-- ko foreach: $data.body.artists -->
        <a href="#" data-bind="attr: { href: $data.url }, text: $data.name"></a><!-- ko if: !($index() === ($parent.body.artists.length - 1)) -->,<!-- /ko -->
        <!-- /ko -->
      </li>

    	<li class="hdp_event_description">
    	  <p data-field="summary" data-bind="text: $data.body.summary"></p>
   	  </li>

    	<li class="hdp_event_information">
    	  <a class="btn" href="#" data-field="purchase" data-bind="visible: $data.body.purchase, attr: { href: $data.body.purchase }"><span>Buy Tickets</span></a>
    	  <a class="btn" href="#" data-field="url" data-bind="visible: $data.body.url, attr: { href: $data.body.url }"><span>More Info</span></a>
      </li>
    </ul>

  </div>

</script>

<!-- Photo Result Item -->
<script type="text/html" id="template-photo">
  <li class="hdp_results_photo hdp_results_item">
    <ul class="df_result_data">
      <li>
        <ul>
          <li>
            <ul class="hdp_photo clearfix">
              <li class="hdp_photo_thumbnail">
                <a data-bind="attr: {href: $data.url, title: 'Photos from '+$data.title}">
                  <img data-bind="attr: {alt: $data.title, src: $data.thumbnail}"/>
                </a>
              </li>
              <li class="hdp_photo_title">
                <a data-bind="attr: {href: $data.url, title: 'Photos from '+$data.title}, text: $data.title"></a>
              </li>
              <li class="hdp_photo_date" data-bind="text: hddp.time( $data.time, 'dddd, MMMM D, YYYY' )"></li>
              <li class="hdp_photo_location" data-bind="text: $data.venue.location.city+', '+$data.venue.location.state"></li>
            </ul>
          </li>
        </ul>
      </li>
    </ul>
  </li>
</script>
