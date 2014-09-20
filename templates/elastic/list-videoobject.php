<form data-scope="videos" id="filter_wrapper" data-bind="elasticFilter:{
  type: 'videoobject',
  period: false,
  sort_by: 'event_date',
  sort_dir: 'desc',
  per_page: 18,
  return_fields: [
    'summary',
    'url',
    'image.small',
    'image.poster',
    'event_date',
    'venue.address.state',
    'venue.address.city'
  ],
  facets: {
    'artists.name': 'Artist',
    'venue.address.state': 'State',
    'venue.address.city': 'City',
    'venue.name': 'Venue',
    'event_type': 'Event Type',
    'promoters.name': 'Promoters'
  }
}">
  <a class="btn btn_show_filter clearfix" href="#"><span>Filter</span></a>
  <div id="df_sidebar_filters_wrap" style="visibility:hidden;">
    <div id="df_sidebar_filters">
      <div data-bind="foreach: videos.facets" class="facets-list inputs-container">
        <div class="df_filter_inputs_list_wrapper" data-bind="attr: {facet:$root.videos.facetLabels()[key]}">
          <span class="df_filter_label" data-bind="html: $root.videos.facetLabels()[key]"></span>
          <ul class="df_filter_inputs_list">
            <li class="df_filter_value_wrapper">
              <label class="df_input">
                <select data-bind="attr:{name:'terms['+key+']'}" class="df_filter_trigger">
                  <option value="0">Show All</option>
                  <!-- ko foreach: terms -->
                  <option data-bind="value: term, html: term+' ('+count+')'"></option>
                  <!-- /ko -->
                </select>
              </label>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</form>

<div data-template="/elastic/list-videoobject" id="dynamic_filter" class="dynamic_filter df_element df_top_wrapper df_element df_top_wrapper clearfix" data-dynamic-filter="hdp_video">
  <div class="df_element hdp_results clearfix">
    <!-- ko if: videos.documents().length -->
    <ul data-bind="foreach: videos.documents" class="df_element hdp_results_items clearfix">

      <li class="hdp_results_item" data-bind="attr: {df_id_: _id}">
        <ul class="df_result_data">
          <li data-attribute-key="raw_html">
            <ul>
              <li>
                <ul class="hdp_video clearfix">
                  <li class="hdp_video_thumbnail">
                    <div class="overlay"></div>
                    <a data-bind="attr:{'href':fields['image.poster'],title:'Videos from '+fields['summary']}" href="#" rel="videoobject">
                      <img data-bind="attr:{src:fields['image.small']}" src="<?php echo includes_url( '/theme/img/placeholder.png' ); ?>" />
                    </a>
                  </li>
                  <li class="hdp_video_title"><a data-bind="html:fields['summary'],attr:{href:fields['url'],title:'Videos from '+fields['summary']}"></a></li>
                  <li class="hdp_video_date" data-bind="visible:fields.event_date[0],html:moment(fields.event_date[0]).format('LLLL')"></li>
                  <li class="hdp_video_location" data-bind="visible:fields['venue.address.city'] && fields['venue.address.state'],html:(fields['venue.address.city'] +', '+fields['venue.address.state'])"></li>
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
        Displaying <span class="df_current_count" data-bind="html:videos.count">0</span> of <span data-bind="html:videos.total"></span> videos
      </div>
      <a class="btn" data-scope="videos" data-bind="visible:videos.has_more_documents,filterShowMoreControl:{count:6}">
        <span>Show <em data-bind="html:videos.moreCount" class="df_more_count"></em> More</span>
      </a>
    </div>

  </div>
</div>