<section data-template="elastic/loop-event">

  <ul class="hdp_results_header clearfix" id="hdp_results_header_event">
    <li class="hdp_event_time">Date</li>
    <li class="hdp_event_name">Name</li>
    <li class="hdp_event_city">City</li>
    <li class="hdp_event_state">State</li>
  </ul>

  <div id="dynamic_filter" class="dynamic_filter df_element df_top_wrapper df_element df_top_wrapper clearfix" data-dynamic-filter="hdp_event">
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
        <li data-bind="attr: {id: _id}" class="hdp_results_item">
          <ul class="df_result_data">
            <li class="df_list_item">
              <ul>
                <li>
                  <ul class="hdp_event_collapsed clearfix">
                    <li data-bind="html:moment(fields.start_date[0]).format('MM DD, YYYY')" class="hdp_event_date"></li>
                    <li data-bind="html:fields['summary']" class="hdp_event_title"></li>
                    <li data-bind="html:fields['venue.address.city']" class="hdp_event_city"></li>
                    <li data-bind="html:fields['venue.address.state']" class="hdp_event_state"></li>
                  </ul>
                  <ul class="hdp_event_expanded clearfix">
                    <li class="hdp_event_flyer">
                      <a data-bind="attr: {href:fields.url}">
                        <img data-bind="attr: {src:fields['image.poster']}" class="fixed_size attachment-events_flyer_thumb" src="<?php echo includes_url( '/theme/img/placeholder.png' ); ?>" />
                      </a>
                    </li>
                    <li class="hdp_event_title">
                      <a data-bind="html:fields['summary'],attr: {href:fields.url}"></a>
                    </li>
                    <li class="hdp_event_date" data-bind="html:'<span>Date:</span> '+moment(fields.start_date[0]).format('LLLL')"></li>
                    <li class="hdp_event_venue">
                      <span>Location:</span>
                      <a data-bind="html:fields['venue.name'],attr: {href: fields['venue.url']}"></a>,
                      <span data-bind="html:fields['venue.address.city']"></span>,
                      <span data-bind="html:fields['venue.address.state']"></span>
                    </li>
                    <li class="hdp_event_artists" data-bind="visible:typeof fields['artists.name'] !== 'undefined'">
                      <span>Artists: </span>
                      <!-- ko if: fields['artists.url'] === undefined -->
                        <!-- ko foreach:fields['artists.name'] -->
                          <span data-bind="text:$data"></span><!-- ko if:$parent.fields['artists.name'].length>$index()+1 -->, <!-- /ko -->
                        <!-- /ko -->
                      <!-- /ko -->
                      <!-- ko ifnot: fields['artists.url'] === undefined -->
                        <!-- ko foreach:fields['artists.name'] -->
                          <a data-bind="text:$data,attr: {href: $parent.fields['artists.url'][$index()]}"></a><!-- ko if:$parent.fields['artists.name'].length>$index()+1 -->, <!-- /ko -->
                        <!-- /ko -->
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
      <div class="df_element df_load_more">
        <div class="df_load_status">
          Displaying <span class="df_current_count" data-bind="html:events.count">0</span> of <span data-bind="html:events.total"></span> events
        </div>
        <a class="btn" data-scope="events" data-bind="visible:events.has_more_documents,filterShowMoreControl:{count:15}">
          <span>Show More</span>
        </a>
      </div>
    </div>
  </div>

</section>