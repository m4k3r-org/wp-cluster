<?php
/**
 * Search form template.
 *
 * @version 0.5.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package WP-Disco
 */
global $flawless;

/******************************************************************************
 * Depreciating until EF is fixed - JBRW
 ******************************************************************************
<form class="search_format" role="search" method="get" action="/events" >
  <div class="search_inner_wrapper">
    <label class="screen-reader-text" for="df_q"><?php _e('Search for:'); ?></label>
    <input data-bind="elastic_settings: { url: 'https://cloud.usabilitydynamics.com', access_key: '<?php echo get_option('access-key'); ?>', account_id: '<?php echo get_option('account-id'); ?>', index: 'hdp_event', per_page: '10' }, fulltext-search: {minimumInputLength: 3, query:hddp.fulltext_search_query, formatSelection:hddp.selection_callback}" class="search_input_field <?php echo $flawless[ 'header' ][ 'grow_input_when_clicked' ] ? 'flawless_input_autogrow' : ''; ?>" type="text" value="<?php echo trim( get_search_query() ); ?>" name="df_q" id="df_q" placeholder="<?php echo $flawless[ 'header' ][ 'search_input_placeholder' ] ? $flawless[ 'header' ][ 'search_input_placeholder' ] : sprintf(__('Search %1s', 'flawless'), get_bloginfo('name')); ?>" />
  </div>
</form>
 ******************************************************************************/ ?>
<form class="search_format" role="search" method="get" action="/events" >
  <div class="search_inner_wrapper">
    <label class="screen-reader-text" for="df_q"><?php _e('Search for:'); ?></label>
    <input class="search_input_field <?php echo $flawless[ 'header' ][ 'grow_input_when_clicked' ] ? 'flawless_input_autogrow' : ''; ?>" type="text" value="<?php echo trim( get_search_query() ); ?>" name="df_q" id="df_q" placeholder="<?php echo $flawless[ 'header' ][ 'search_input_placeholder' ] ? $flawless[ 'header' ][ 'search_input_placeholder' ] : sprintf(__('Search %1s', 'flawless'), get_bloginfo('name')); ?>" />
  </div>
</form>
