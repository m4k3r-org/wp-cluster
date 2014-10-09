<?php
/**
 * Search form template.
 *
 * @version 0.5.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
 */

global $flawless;
?>

<form class="search_format" role="search" method="get" action="<?php echo  home_url( '/' ); ?>" >
  <div class="search_inner_wrapper">
    <label class="screen-reader-text" for="s"><?php _e('Search for:'); ?></label>
    <input class="search_input_field <?php echo $flawless[ 'header' ][ 'grow_input_when_clicked' ] ? 'flawless_input_autogrow' : ''; ?>" type="text" value="<?php echo trim( get_search_query() ); ?>" name="s" id="s" placeholder="<?php echo $flawless[ 'header' ][ 'search_input_placeholder' ] ? $flawless[ 'header' ][ 'search_input_placeholder' ] : sprintf(__('Search %1s', 'flawless'), get_bloginfo('name')); ?>" />
    <input class="action_button search_button icon-search"  type="submit" id="searchsubmit" value="<?php echo esc_attr__('Search'); ?>" />
  </div>
</form>
