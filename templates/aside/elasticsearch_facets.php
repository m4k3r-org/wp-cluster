<form class="elastic_form" id="elasticsearch-facets-<?php echo $id; ?>" data-id="<?php echo $id; ?>" data-action="<?php echo $action; ?>" data-type="<?php echo $type; ?>" data-size="<?php echo $size; ?>">

  <div class="inputs-container df_filter_inputs_list_wrapper">
    <ul class="df_filter_inputs_list">
      <li class="df_filter_value_wrapper">
        <label class="df_input">
          <input name="q" type="text" placeholder="Enter Artist, City, State, or Venue" class="df_filter_trigger">
        </label>
      </li>
    </ul>
  </div>

  <div class="facets-list inputs-container"></div>

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