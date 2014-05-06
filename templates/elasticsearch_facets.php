<!--<form data-bind="elasticFilter:{
  per_page: 100,
  period_field: 'event_date_time',
  sort_by: 'event_date_time',
  type: 'hdp_event',
  facets: {
    hdp_artist_name: 'Artist',
    hdp_state_name: 'State',
    hdp_city_name: 'City',
    hdp_venue_name: 'Venue',
    hdp_promoter_name: 'Promoter',
    hdp_tour_name: 'Tour',
    hdp_type_name: 'Type',
    hdp_genre_name: 'Genre',
    hdp_age_limit_name: 'Age Limit'
  },
  return_fields: [
    'event_date_human_format',
    'raw.post_title',
    'raw.attributes.hdp_city',
    'raw.meta.state_code',
    'permalink',
    'image_url',
    'raw.summary_qa.hdp_event_date',
    'raw.summary_qa.hdp_venue',
    'raw.summary_qa.hdp_artist',
    'raw.post_excerpt',
    'raw.meta.hdp_purchase_url'
  ]}" class="elastic_form">-->

<form data-bind="elasticFilter:{
  per_page: 100,
  period_field: 'startDate',
  sort_by: 'startDate',
  type: 'event',
  facets: {
    'artists.name': 'Artist',
    'venues.address.region': 'State',
    'venues.address.locality': 'City',
    'venues.name': 'Venue',
    //'hdp_promoter_name': 'Promoter', no information in object for this
    //'hdp_tour_name': 'Tour', no information in object for this
    'eventType.en-us': 'Type',
    'genre.en-us': 'Genre',
    'ageRestriction': 'Age Limit'
  },
  return_fields: [
    'startDate',
    'description.en-us',
    'venues.address.locality',
    'venues.address.region',
    'url',
    'image.poster.thumbnail',
    'venues.name',
    'artists.name',
    'tickets'
  ]}" class="elastic_form">

  <div data-bind="foreach: filter.facets" class="facets-list inputs-container">
    <div class="df_filter_inputs_list_wrapper">
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
  <div attribute_key="hdp_date_range" class="df_filter_inputs_list_wrapper inputs-container">
    <span class="df_filter_label">Date Range</span>
    <ul class="df_filter_inputs_list">
      <li>
        <input type="text" name="date_range[gte]" />
      </li>
      <li>
        <input type="text" name="date_range[lte]" />
      </li>
    </ul>
  </div>
  <script type="text/javascript">
    jQuery(document).ready(function(){
      jQuery('[name^="date_range"]').datepicker({
        dateFormat: 'yy-mm-dd'
      });
    });
  </script>
  <div class="clearfix"></div>
</form>