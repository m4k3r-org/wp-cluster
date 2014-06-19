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