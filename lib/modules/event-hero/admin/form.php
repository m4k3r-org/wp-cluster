<!-- Do our inline CSS here -->
<style type="text/css">
  .colorpicker_wrapper label {
    display: block;
    float: left;
    margin-right: 10px;
    line-height: 22px;
    width: 125px;
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
    <legend><?php _e( 'Styles', 'wp-festival' ); ?></legend>
    <ul>
      <li class="colorpicker_wrapper">
        <label for="<?php echo $this->get_field_name('background_color'); ?>"><?php _e( 'Background Color', 'wp-festival' ); ?></label>
        <input type="text" class="colorpicker" name="background_color" id="<?php echo $this->get_field_name('background_color'); ?>" value="<?php echo esc_attr( isset( $data[ 'background_color' ] ) ? $data[ 'background_color' ] : '' ); ?>" />
      </li>
      <li class="colorpicker_wrapper">
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
    </ul>
  </fieldset>
  
  <script type="text/javascript">
    if( typeof jQuery.fn.wpColorPicker == 'function' ) { jQuery( '.colorpicker' ).wpColorPicker(); }
  </script>

</div>