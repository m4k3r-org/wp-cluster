<form style="margin: 0;" data-bind="elasticFilter:{
  middle_timepoint: {
    gte: 'now-1d',
    lte: 'now-1d'
  },
  per_page: <?php echo $args['per_page'] ?>,
  period_field: 'start_date',
  sort_by: 'start_date',
  type: 'event',
  return_fields: [
    'start_date',
    'description',
    'summary',
    'venue.address.city',
    'venue.address.state',
    'url',
    'image.poster',
    'venue.name',
    'artists.name',
    'tickets'
  ]}" class="elastic_form"></form>

  <ul class="hdp_results_header clearfix" id="hdp_results_header_event">
  <li class="hdp_event_time">Date</li>
  <li class="hdp_event_name">Name</li>
  <li class="hdp_event_city">City</li>
  <li class="hdp_event_state">State</li>
</ul>

<div style="margin-bottom: 30px;" id="dynamic_filter" class="dynamic_filter df_element df_top_wrapper df_element df_top_wrapper clearfix" dynamic_filter="hdp_event">
  <div class="df_element hdp_results clearfix">
    <!-- ko if: !filter.documents().length -->
    <ul class="df_element hdp_results_items">
      <li class="hdp_results_item">
        <ul class="hdp_event_collapsed clearfix">
          <li>Nothing found</li>
        </ul>
      </li>
    </ul>
    <!-- /ko -->
    <!-- ko if: filter.documents().length -->
    <ul data-bind="foreach: filter.documents" class="df_element hdp_results_items">
      <li data-bind="attr: {id: _id}" class="hdp_results_item">
        <ul class="df_result_data">
          <li class="df_list_item">
            <ul>
              <li>
                <ul class="hdp_event_collapsed clearfix">
                  <li data-bind="html:moment(fields.start_date[0]).format('LL')" class="hdp_event_date"></li>
                  <li data-bind="html:fields['summary']" class="hdp_event_title"></li>
                  <li data-bind="html:fields['venue.address.city']" class="hdp_event_city"></li>
                  <li data-bind="html:fields['venue.address.state']" class="hdp_event_state"></li>
                </ul>
                <ul class="hdp_event_expanded clearfix">
                  <li class="hdp_event_flyer">
                    <a data-bind="attr: {href:fields.url}">
                      <img data-bind="attr: {src:fields['image.poster']}" class="fixed_size attachment-events_flyer_thumb"/>
                    </a>
                  </li>
                  <li class="hdp_event_title">
                    <a data-bind="html:fields['summary'],attr: {href:fields.url}"></a>
                  </li>
                  <li class="hdp_event_date" data-bind="html:'<span>Date:</span> '+moment(fields.start_date[0]).format('LLLL')"></li>
                  <li class="hdp_event_venue" data-bind="html:'<span>Venue:</span> '+fields['venue.name']"></li>
                  <li class="hdp_event_artists" data-bind="html:'<span>Artists:</span> '+fields['artists.name']"></li>
                  <li class="hdp_event_description"><p data-bind="html:fields['description']"></p></li>
                  <li class="hdp_event_information">
                    <a class="btn" data-bind="attr: {href:fields.tickets}"><span>Buy Tickets</span></a>
                    <a class="btn" data-bind="attr: {href:fields.url}"><span>More Info</span></a>
                  </li>
                </ul>
              </li>
            </ul>
          </li>
        </ul>
      </li>
    </ul>
    <!-- /ko -->

  </div>
</div>