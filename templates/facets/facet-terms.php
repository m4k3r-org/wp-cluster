<div class="df_filter_inputs_list_wrapper">
  <span class="df_filter_label"><?php _elastic_label( $facet_key ); ?></span>
  <ul class="df_filter_inputs_list">
    <li class="df_filter_value_wrapper">
      <label class="df_input">
        <select name="terms[<?php echo $facet_key; ?>]" class="df_filter_trigger">
          <option value="0">Show All</option>
          <?php foreach( $facet_data[ 'terms' ] as $term ): ?>
            <option value="<?php echo $term[ 'term' ] ?>"><?php echo $term[ 'term' ] ?></option>
          <?php endforeach; ?>
        </select>
      </label>
    </li>
  </ul>
</div>