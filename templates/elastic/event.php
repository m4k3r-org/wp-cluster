<div class="hdp_filter" id="hdp_filter_events">
  <div data-scope="events" data-bind="elasticTimeControl:{}" class="hdp_filter_event_past hidden-phone" id="hdp_filter_event">
    <span class="hdp_filter_text">Display:</span>
    <div data-type="upcoming" data-direction="asc" class="df_element df_sortable_button df_sortable_upcoming df_sortable_active">Upcoming</div>
    <div data-type="past" data-direction="desc" class="df_element df_sortable_button df_sortable_past">Past</div>
    <div data-type="all" data-direction="desc" class="df_element df_sortable_button df_sortable_all">All</div>
  </div>
</div>

<div class="hdp_sort clearfix" id="hdp_sort_event">
  <div data-scope="events" data-bind="elasticSortControl:{}" class="hdp_results_sorter_distance df_element df_sorter" id="hdp_results_sorter">
    <span class="hdp_sort_text">Sort By:</span>
    <div class="df_element df_sortable_button df_sortable_active" data-type="start_date" data-direction="asc">Date</div>
    <div class="df_element df_sortable_button" data-type="distance" data-direction="desc">Distance</div>
  </div>
</div>

<ul class="hdp_results_header clearfix" id="hdp_results_header_event">
  <li class="hdp_event_time">Date</li>
  <li class="hdp_event_name">Name</li>
  <li class="hdp_event_city">City</li>
  <li class="hdp_event_state">State</li>
</ul>

<div id="dynamic_filter" class="dynamic_filter df_element df_top_wrapper df_element df_top_wrapper clearfix" dynamic_filter="hdp_event">
  <div class="df_element hdp_results clearfix">

    <!-- ko if: !events.documents().length -->
    <ul class="df_element hdp_results_items">
      <li class="hdp_results_item">
        <ul class="hdp_event_collapsed clearfix">
          <li>Nothing found</li>
        </ul>
      </li>
    </ul>
    <!-- /ko -->

    <!-- ko if: events.documents().length -->
    <ul data-bind="foreach: events.documents" class="df_element hdp_results_items">
      <li data-bind="attr: {event_id: _id}" class="hdp_results_item">
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
                  <li class="hdp_event_artists" data-bind="visible:typeof fields['artists.name'] !== 'undefined'">
                    <span>Artists: </span>
                    <!-- ko foreach:fields['artists.name'] -->
                      <a data-bind="text:$data"></a><!-- ko if:$parent.fields['artists.name'].length>$index()+1 -->, <!-- /ko -->
                    <!-- /ko -->
                  </li>
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

    <div class="hdp_results_message clearfix" style="display: block;">
      <div class="df_load_status left">
        Displaying <span class="df_current_count" data-bind="html:events.count">0</span> of <span data-bind="html:events.total"></span> Events
      </div>
      <a class="btn" data-scope="events" data-bind="visible:events.has_more_documents,filterShowMoreControl:{count:100}">
        <span>Show More</span>
      </a>
    </div>

  </div>
</div>