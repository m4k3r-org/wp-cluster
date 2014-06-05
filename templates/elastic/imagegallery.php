<form id="filter_wrapper" data-bind="elasticFilter:{
  type: 'imagegallery',
  period: false,
  sort_by: 'event_date',
  per_page: 6,
  return_fields: [
    'summary',
    'url',
    'image.small',
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
      <div data-bind="foreach: filter.facets" class="facets-list inputs-container">
        <div class="df_filter_inputs_list_wrapper" data-bind="attr: {facet:$root.filter.facetLabels()[key]}">
          <span class="df_filter_label" data-bind="html: $root.filter.facetLabels()[key]"></span>
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

<div id="dynamic_filter" class="dynamic_filter df_element df_top_wrapper df_element df_top_wrapper clearfix" dynamic_filter="hdp_photo_gallery">
  <div class="df_element hdp_results clearfix">
    <!-- ko if: filter.documents().length -->
    <ul data-bind="foreach: filter.documents" class="df_element hdp_results_items">

      <li class="hdp_results_item" data-bind="attr: {df_id_: _id}">
        <ul class="df_result_data">
          <li attribute_key="raw_html">
            <ul>
              <li>
                <ul class="hdp_photo clearfix">
                  <li class="hdp_photo_thumbnail">
                    <a data-bind="href:fields['url'],attr:{title:'Photos from '+fields['summary']}">
                      <div class="overlay"></div>
                      <img data-bind="attr:{src:fields['image.small']}" />
                    </a>
                  </li>
                  <li class="hdp_photo_title"><a data-bind="html:fields['summary'],attr:{href:fields['url'],title:'Photos from '+fields['summary']}"></a></li>
                  <li class="hdp_photo_date" data-bind="html:moment(fields.event_date[0]).format('LLLL')"></li>
                  <li class="hdp_photo_location" data-bind="html:(fields['venue.address.city']+', '+fields['venue.address.state'])"></li>
                </ul>
              </li>
            </ul>
          </li>
        </ul>
      </li>

    </ul>
    <!-- /ko -->

    <!-- <div class="hdp_results_message" style="display: block;">
      <div class="df_load_status">
        <span>Displaying <?php echo $per_page; ?></span> of <?php echo $total[ 0 ]; ?> Events <a class="btn" href="/events"><span>Show All</span></a>
      </div>
    </div> -->

  </div>
</div>