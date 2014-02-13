<?php /**<pre><?php print_r( $data ); ?></pre> */ ?>

<!-- Do our inline CSS here -->
<style type="text/css">
  fieldset#artists-list-artists ul{
    margin-top: 0;
    max-height: 300px;
    overflow-y: scroll;
    border: 1px solid #bbb;
    padding: 3px;
  }
  fieldset#artists-list-artists ul li input[type=checkbox],
  fieldset#artists-list-artists ul li label{
    position: relative;
    top: 6px;
    left: 6px;
  }
  fieldset#artists-list-artists ul li select,
  fieldset#artists-list-artists ul li input[type=text],
  fieldset#artists-list-artists ul li span.order,
  fieldset#artists-list-artists ul li span.column,
  fieldset#artists-list-artists ul li span.custom_date  {
    display: inline;
    float: right;
    width: 50px;
    text-align: right;
  }
  fieldset#artists-list-artists ul li input[type=text] {
    width: 35px;
    margin-left: 15px;
    margin-right: 4px;
  }
  fieldset#artists-list-artists ul li select{
    width: 38px;
    margin-left: 12px;
  }
  fieldset#artists-list-artists ul li.header {
    padding: 4px;
  }
  .colorpicker_wrapper label {
    display: block;
    float: left;
    margin-right: 10px;
    line-height: 22px;
    width: 125px;
  }
  
  fieldset#artists-list-artists ul li input.datepicker,
  fieldset#artists-list-artists ul li span.custom_date  {
    width: 100px;
    text-align: left;
  }
</style>

<!-- basic info -->
<fieldset id="artists-list-basic-info" class="cfct-form-section">
  <legend><?php _e( 'Basic Info', 'wp-festival' ) ?></legend>
  <label for="title"><?php _e( 'Title', 'wp-festival' ); ?></label>
  <span class="cfct-input-full">
    <input type="text" name="title" id="title" value="<?php echo esc_attr( isset( $data[ 'title' ] ) ? $data[ 'title' ] : '' ); ?>" />
  </span>
  <label for="tagline"><?php _e( 'Tagline', 'wp-festival' ); ?></label>
  <span class="cfct-input-full">
    <input type="text" name="tagline" id="tagline" value="<?php echo esc_attr( isset( $data[ 'tagline' ] ) ? $data[ 'tagline' ] : '' ); ?>" />
  </span>
  <label for="description"><?php _e( 'Description', 'wp-festival' ); ?></label>
  <span class="cfct-input-full">
    <textarea name="description" id="description"><?php echo htmlentities( isset( $data[ 'description' ] ) ? $data[ 'description' ] : '' ); ?></textarea>
  </span>
</fieldset>
<!-- /basic info -->

<!-- display options -->
<fieldset id="artists-list-display-options" class="cfct-form-section">
  <legend><?php _e( 'Display Options', 'wp-festival' ) ?></legend>
  <ul>
    <li>
      <label for="artist_type"><?php _e( 'Template', wp_festival( 'domain' ) ); ?></label>
      <select name="artist_type" id="artist_type">
        <?php foreach( $artist_types as $key => $type ):
          $selected = isset( $data[ 'artist_type' ] ) && $data[ 'artist_type' ] == $key ? 'selected="selected"' : ''; ?>
          <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $type; ?></option>
        <?php endforeach; ?>
      </select>
    </li>
    <li>
      <label for="artist_image"><?php _e( 'Artist Image', wp_festival( 'domain' ) ); ?></label>
      <select name="artist_image" id="artist_image">
        <?php foreach( $artist_images as $k => $v ):
          $selected = isset( $data[ 'artist_image' ] ) && $data[ 'artist_image' ] == $k ? 'selected="selected"' : ''; ?>
          <option value="<?php echo $k; ?>" <?php echo $selected; ?>><?php echo $v; ?></option>
        <?php endforeach; ?>
      </select>
    </li>
    <li>
      <label for="artist_columns"><?php _e( 'Number of Images per Row', wp_festival( 'domain' ) ); ?></label>
      <select name="artist_columns" id="artist_columns">
        <?php foreach( $artist_columns as $columns ):
          $selected = isset( $data[ 'artist_columns' ] ) && $data[ 'artist_columns' ] == $columns ? 'selected="selected"' : ''; ?>
          <option value="<?php echo $columns; ?>" <?php echo $selected; ?>><?php echo $columns; ?></option>
        <?php endforeach; ?>
      </select>
    </li>
    <li>
      <label for="enable_links">
        <input type="checkbox" id="enable_links" name="enable_links" value="true" <?php echo isset( $data[ 'enable_links' ] ) && $data[ 'enable_links' ] == 'true' ? 'checked="checked"' : '' ?> />
        <?php _e( 'Enable Links', wp_festival( 'domain' ) ); ?>
      </label>
    </li>
    <li>
      <label for="enable_dates">
        <input type="checkbox" id="enable_dates" name="enable_dates" value="true" <?php echo isset( $data[ 'enable_dates' ] ) && $data[ 'enable_dates' ] == 'true' ? 'checked="checked"' : '' ?> />
        <?php _e( 'Enable Perfomance Date', wp_festival( 'domain' ) ); ?>
      </label>
    </li>
  </ul>
</fieldset>

<!-- layout options -->
<fieldset id="artists-list-display-options" class="cfct-form-section">
  <legend><?php _e( 'Layout Options', 'wp-festival' ) ?></legend>
  <ul>
    <li>
      <label for="layout_type"><?php _e( 'Layout' ); ?></label>
      <select name="layout_type" id="layout_type">
        <?php foreach( $layout_types as $key => $type ):
          $selected = isset( $data[ 'layout_type' ] ) && $data[ 'layout_type' ] == $key ? 'selected="selected"' : ''; ?>
          <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $type; ?></option>
        <?php endforeach; ?>
      </select>
    </li>
    <li>
      <label for="column_title_1"><?php _e( 'First Column Title', 'wp-festival' ); ?></label>
      <span class="cfct-input">
        <input type="text" name="column_title_1" id="column_title_1" value="<?php echo esc_attr( isset( $data[ 'column_title_1' ] ) ? $data[ 'column_title_1' ] : '' ); ?>" />
      </span>
    </li>
    <li>
      <label for="column_title_2"><?php _e( 'Second Column Title', 'wp-festival' ); ?></label>
      <span class="cfct-input">
        <input type="text" name="column_title_2" id="column_title_2" value="<?php echo esc_attr( isset( $data[ 'column_title_2' ] ) ? $data[ 'column_title_2' ] : '' ); ?>" />
      </span>
    </li>
    <li>
      <label for="column_title_3"><?php _e( 'Third Column Title', 'wp-festival' ); ?></label>
      <span class="cfct-input">
        <input type="text" name="column_title_3" id="column_title_3" value="<?php echo esc_attr( isset( $data[ 'column_title_3' ] ) ? $data[ 'column_title_3' ] : '' ); ?>" />
      </span>
    </li>
  </ul>
</fieldset>

<!-- artists -->
<fieldset id="artists-list-artists" class="cfct-form-section">
  <legend><?php _e( 'Selected Artists', 'wp-festival' ); ?></legend>
  <div class="div-wrapper clearfix">
    <ul>
      <li class="alt header">
        <span class="order"><?php _e( 'Order', wp_festival( 'domain' ) ); ?></span>
        <span class="column"><?php _e( 'Column', wp_festival( 'domain' ) ); ?></span>
        <span class="custom_date"><?php _e( 'Custom Date', wp_festival( 'domain' ) ); ?></span>
        <div style="clear:both;"></div>
      </li>
      <?php foreach( $artists as $artist ):
        $alt = !isset( $alt ) ? '' : ( $alt === '' ? 'alt' : '' );
        $checked = isset( $data[ 'artists' ] ) && is_array( $data[ 'artists' ] ) && in_array( $artist[ 'ID' ], $data[ 'artists' ] ) ? 'checked="checked"' : ''; ?>
        <li class="<?php echo $alt; ?>">
          <input type="checkbox" name="artists[]" id="artist-<?php echo $artist[ 'ID' ]; ?>" class="post-type-select" value="<?php echo $artist[ 'ID' ]; ?>" <?php echo $checked; ?> />
          <label for="artist-<?php echo $artist[ 'ID' ]; ?>"><?php echo $artist[ 'post_title' ]; ?></label>
          <input type="text" id="sorting-<?php echo $artist[ 'ID' ]; ?>" name="sorting[<?php echo $artist[ 'ID' ]; ?>]" value="<?php echo esc_attr( isset( $data[ 'sorting' ] ) && is_array( $data[ 'sorting' ] ) && isset( $data[ 'sorting' ][ $artist[ 'ID' ] ] ) ? $data[ 'sorting' ][ $artist[ 'ID' ] ] : '' ); ?>" />
          <select name="col_position[<?php echo $artist[ 'ID' ]; ?>]">
            <?php foreach( $layout_columns as $k => $v ) : ?>
              <?php $selected = ( isset( $data[ 'col_position' ][ $artist[ 'ID' ] ] ) && $data[ 'col_position' ][ $artist[ 'ID' ] ] == $k ) ? 'selected="selected"' : ''; ?>
              <option value="<?php echo $k; ?>" <?php echo $selected; ?>><?php echo $v ?></option>
            <?php endforeach; ?>
          </select>
          <input type="text" name="custom_date[<?php echo $artist[ 'ID' ]; ?>]" class="datepicker" value="<?php echo esc_attr( isset( $data[ 'custom_date' ][ $artist[ 'ID' ] ] ) ? $data[ 'custom_date' ][ $artist[ 'ID' ] ] : '' ); ?>" />
          <div style="clear:both;"></div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</fieldset>

<fieldset class="cfct-form-section">
  <legend><?php _e( 'Styles', 'wp-festival' ); ?></legend>
  <ul>
    <li class="colorpicker_wrapper">
      <label for="background_color"><?php _e( 'Background Color', 'wp-festival' ); ?></label>
      <input type="text" class="colorpicker" name="background_color" id="background_color" value="<?php echo esc_attr( isset( $data[ 'background_color' ] ) ? $data[ 'background_color' ] : '' ); ?>" />
    </li>
    <li class="colorpicker_wrapper">
      <label for="font_color"><?php _e( 'Font Color', 'wp-festival' ); ?></label>
      <input type="text" class="colorpicker" name="font_color" id="font_color" value="<?php echo esc_attr( isset( $data[ 'font_color' ] ) ? $data[ 'font_color' ] : '' ); ?>" />
    </li>
    <li>
      <label><?php _e( 'Background Image:', 'wp-festival' ); ?></label>
      <?php
      // tabs
      $image_selector_tabs = array(
        $this->id_base.'-post-image-wrap' => __('Post Images', 'carrington-build'),
        $this->id_base.'-global-image-wrap' => __('All Images', 'carrington-build')
      );

      // set active tab
      $active_tab = $this->id_base.'-post-image-wrap';
      if (!empty($data[$this->get_field_name('global_image')])) {
        $active_tab = $this->id_base.'-global-image-wrap';
      }
      ?>
      <!-- image selector tabs -->
      <div id="<?php echo $this->id_base; ?>-image-selectors">
        <!-- tabs -->
        <?php echo $this->cfct_module_tabs($this->id_base.'-image-selector-tabs', $image_selector_tabs, $active_tab); ?>
        <!-- /tabs -->
        <div class="cfct-module-tab-contents">
          <!-- select an image from this post -->
          <div id="<?php echo $this->id_base; ?>-post-image-wrap" <?php echo ( empty( $active_tab ) || $active_tab == $this->id_base.'-post-image-wrap' ? ' class="active"' : '' ); ?>>
            <?php echo $this->post_image_selector($data); ?>
          </div>
          <!-- / select an image from this post -->
          <!-- select an image from media gallery -->
          <div id="<?php echo $this->id_base; ?>-global-image-wrap" <?php echo ( $active_tab == $this->id_base.'-global-image-wrap' ? ' class="active"' : '' ); ?>>
            <?php echo $this->global_image_selector($data); ?>
          </div>
          <!-- /select an image from media gallery -->
        </div>
      </div>
      <!-- / image selector tabs -->
    </li>
  </ul>
</fieldset>
<script type="text/javascript">
  if( typeof jQuery.fn.wpColorPicker == 'function' ) { jQuery( '.colorpicker' ).wpColorPicker(); }
  if( typeof jQuery.fn.datepicker == 'function' ) { jQuery( '.datepicker' ).datepicker( { dateFormat : 'dd-mm-yy' } ); }
</script>