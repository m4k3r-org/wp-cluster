<!-- Do our inline CSS here -->
<style type="text/css">
  .option label {
    display: block;
    float: left;
    margin-right: 10px;
    line-height: 22px;
    width: 150px;
  }
  
  .option {
    display: block;
    min-height: 22px;
  }
  
  .option:after {
    content: " ";
    clear: both;
    line-indent: 9999px;
    line-height: 1px;
    font-size: 1px;
    margin-top: -1px;
  }
  
  .event-hero-form .car-search-elements .event-edit-form,
  .event-hero-form .hidden,
  .event-hero-form .events-list .event-post-item-ident  {
    display: none;
  }
  
  .event-hero-form #car-items {
    min-height: 0;
  }
  
  .event-hero-form ol {
    list-style-type: none;
    margin-left: 0;
  }
  
</style>

<div class="event-hero-form">

  <fieldset class="cfct-form-section">
    <legend><?php _e( 'Event', 'wp-festival' ); ?></legend>
    <div id="car-items" class="active">
      <div id="car-item-search" class="car-item-search-container <?php echo isset( $data[ $this->get_field_name( 'posts' ) ] ) && count( $data[ $this->get_field_name( 'posts' ) ] ) ? 'hidden' : ''; ?>">
        <label for="car-search-term"><?php _e( 'Search Event:', wp_festival( 'domain' ) ); ?></label>
        <input type="text" name="car-search-term" id="car-search-term" value="" />
        <span class="elm-help elm-align-bottom"><? _e( 'Only items with a featured image are available.', wp_festival( 'domain' ) ); ?></span>
      </div>
      <div class="car-items-wrapper">
        <ol class="events-list">
          <?php if( isset( $data[ $this->get_field_name( 'posts' ) ] ) && count( $data[ $this->get_field_name( 'posts' ) ] ) )  : ?>
            <?php foreach( $data[ $this->get_field_name( 'posts' ) ] as $item ) : ?>
              <?php echo $this->get_event_admin_item( $item ); ?>
            <?php endforeach; ?>
          <?php endif; ?>
        </ol>
      </div>
    </div>
  </fieldset>

  <fieldset class="cfct-form-section">
    <legend><?php _e( 'Style & Display Options', 'wp-festival' ); ?></legend>
    <ul>
      <li class="option">
        <label>
          <input type="checkbox" class="" name="enable_links" value="true" <?php echo ( isset( $data[ 'enable_links' ] ) && $data[ 'enable_links' ] == 'true' ) ? 'checked="checked"' : ''; ?> />
          <?php _e( 'Enable Artists Links', 'wp-festival' ); ?>
        </label>
      </li>
      <li class="option">
        <label for="<?php echo $this->get_field_name( 'artist_image_type' ); ?>"><?php _e( 'Artist Image', wp_festival( 'domain' ) ); ?></label>
        <select name="artist_image_type" id="<?php echo $this->get_field_name( 'artist_image_type' ); ?>">
          <?php foreach( $artist_images as $k => $v ):
            $selected = isset( $data[ 'artist_image_type' ] ) && $data[ 'artist_image_type' ] == $k ? 'selected="selected"' : ''; ?>
            <option value="<?php echo $k; ?>" <?php echo $selected; ?>><?php echo $v; ?></option>
          <?php endforeach; ?>
        </select>
      </li>
      <li class="option">
        <label for="<?php echo $this->get_field_name( 'artist_columns' ); ?>"><?php _e( 'Image Columns per Row', wp_festival( 'domain' ) ); ?></label>
        <select name="artist_columns" id="<?php echo $this->get_field_name( 'artist_columns' ); ?>">
          <?php foreach( $artist_columns as $columns ):
            $selected = isset( $data[ 'artist_columns' ] ) && $data[ 'artist_columns' ] == $columns ? 'selected="selected"' : ''; ?>
            <option value="<?php echo $columns; ?>" <?php echo $selected; ?>><?php echo $columns; ?></option>
          <?php endforeach; ?>
        </select>
      </li>
      <li class="option">
        <label for="<?php echo $this->get_field_name('background_color'); ?>"><?php _e( 'Background Color', 'wp-festival' ); ?></label>
        <input type="text" class="colorpicker" name="background_color" id="<?php echo $this->get_field_name('background_color'); ?>" value="<?php echo esc_attr( isset( $data[ 'background_color' ] ) ? $data[ 'background_color' ] : '' ); ?>" />
      </li>
      <li class="option">
        <label for="<?php echo $this->get_field_name('font_color'); ?>"><?php _e( 'Font Color', 'wp-festival' ); ?></label>
        <input type="text" class="colorpicker" name="font_color" id="<?php echo $this->get_field_name('font_color'); ?>" value="<?php echo esc_attr( isset( $data[ 'font_color' ] ) ? $data[ 'font_color' ] : '' ); ?>" />
      </li>
      <li>
        <label><?php _e( 'Background Image:', 'wp-festival' ); ?></label>
        <?php
        // tabs
        $image_selector_tabs = array(
          $this->id_base.'-post-image-wrap' => __('Post Images', wp_festival( 'domain' ) ),
          $this->id_base.'-global-image-wrap' => __('All Images', wp_festival( 'domain' ) )
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
      <li>
        <label><?php _e( 'Logo:', 'wp-festival' ); ?></label>
        <?php
        // tabs
        $image_selector_tabs = array(
          $this->id_base.'-post-logo-image-wrap' => __('Post Images', wp_festival( 'domain' ) ),
          $this->id_base.'-global-logo-image-wrap' => __('All Images', wp_festival( 'domain' ) )
        );

        // set active tab
        $active_tab = $this->id_base.'-post-logo-image-wrap';
        if (!empty($data[$this->get_field_name('logo_global_image')])) {
          $active_tab = $this->id_base.'-global-logo-image-wrap';
        }
        ?>
        <!-- image selector tabs -->
        <div id="<?php echo $this->id_base; ?>-image-selectors">
          <!-- tabs -->
          <?php echo $this->cfct_module_tabs($this->id_base.'-image-selector-tabs', $image_selector_tabs, $active_tab); ?>
          <!-- /tabs -->
          <div class="cfct-module-tab-contents">
            <!-- select an image from this post -->
            <div id="<?php echo $this->id_base; ?>-post-logo-image-wrap" <?php echo ( empty( $active_tab ) || $active_tab == $this->id_base.'-post-logo-image-wrap' ? ' class="active"' : '' ); ?>>
              <?php echo $this->post_image_selector( $data, 'logo' ); ?>
            </div>
            <!-- / select an image from this post -->
            <!-- select an image from media gallery -->
            <div id="<?php echo $this->id_base; ?>-global-logo-image-wrap" <?php echo ( $active_tab == $this->id_base.'-global-logo-image-wrap' ? ' class="active"' : '' ); ?>>
              <?php echo $this->global_image_selector( $data, 'logo' ); ?>
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
  </script>

</div>