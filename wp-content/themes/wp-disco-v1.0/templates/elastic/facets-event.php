<form data-scope="events" data-bind="elasticFilter:{
  middle_timepoint: {
    gte: 'now-1d',
    lte: 'now-1d'
  },
  per_page: 100,
  period_field: 'start_date',
  sort_by: 'start_date',
  type: 'event',
  location_field: 'venue.address.geo',
  facets: {
    'artists.name': 'Artist',
    'venue.address.state': 'State',
    'venue.address.city': 'City',
    'venue.name': 'Venue',
    'promoters.name': 'Promoter',
    'tour.name': 'Tour',
    'event_type': 'Type',
    'artists.genre': 'Genre',
    'age_restriction': 'Age Limit'
  },
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
  ]}" class="elastic_form">

  <div data-bind="foreach: events.facets" class="facets-list inputs-container">
    <div class="df_filter_inputs_list_wrapper">
      <span class="df_filter_label" data-bind="html: $root.events.facetLabels()[key]"></span>
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
  <div data-attribute-key="hdp_date_range" class="df_filter_inputs_list_wrapper inputs-container">
    <span class="df_filter_label">Date Range</span>
    <ul class="df_filter_inputs_list">
      <li>
        <input type="text" name="date_range[gte]"/>
      </li>
      <li>
        <input type="text" name="date_range[lte]"/>
      </li>
    </ul>
  </div>
  <script type="text/javascript">
    jQuery( document ).ready( function() {
      jQuery( '[name^="date_range"]' ).datepicker( {
        dateFormat: 'yy-mm-dd'
      } );
    } );
  </script>
  <div class="clearfix"></div>
</form>