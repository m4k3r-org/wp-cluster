<div class="hdp_filter <?php echo $id; ?>" id="hdp_filter_events">
  <div class="hdp_filter_event_past hidden-phone" id="hdp_filter_event">
    <span class="hdp_filter_text">Display:</span>
    <div _filter="upcoming" class="df_element df_sortable_button df_sortable_upcoming df_sortable_active">Upcoming</div>
    <div _filter="past" class="df_element df_sortable_button df_sortable_past">Past</div>
    <div _filter="all" class="df_element df_sortable_button df_sortable_all">All</div>
  </div>
</div>

<div class="hdp_sort clearfix" id="hdp_sort_event">
  <div class="hdp_results_sorter_distance df_element df_sorter <?php echo $id; ?>" id="hdp_results_sorter">
    <span class="hdp_sort_text">Sort By:</span>
    <div class="df_element df_sortable_button df_sortable_active" attribute_key="hdp_event_date" sort_direction="ASC">Date</div>
    <div class="df_element df_sortable_button" attribute_key="distance" sort_direction="ASC">Distance</div>
  </div>
</div>

<ul class="hdp_results_header clearfix" id="hdp_results_header_event">
    <li class="hdp_event_time">Date</li>
    <li class="hdp_event_name">Name</li>
    <li class="hdp_event_city">City</li>
    <li class="hdp_event_state">State</li>
</ul>

<div id="dynamic_filter" class="<?php echo $id; ?> dynamic_filter df_element df_top_wrapper df_element df_top_wrapper clearfix" dynamic_filter="hdp_event">
  <div class="df_element hdp_results clearfix">
    <ul id="elasticsearch-results-<?php echo $id; ?>" class="df_element hdp_results_items">
    </ul>
    <div class="df_element df_load_more">
      <div class="df_load_status">
        Displaying <span class="df_current_count">0</span> of <span class="df_total_count"></span> Events
      </div>
      <a class="btn">
        <span>Show <em class="df_more_count"></em> More</span>
      </a>
    </div>
  </div>
</div>