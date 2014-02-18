<!-- Do our inline CSS here -->
<style type="text/css">
  .colorpicker_wrapper label,
  .parallax label  {
    display: block;
    float: left;
    margin-right: 10px;
    line-height: 22px;
    width: 125px;
  }
  
  .parallax input[type="text"] {
    width: 60px;
  }
  
</style>

<!-- basic info -->
<fieldset id="artists-list-basic-info" class="cfct-form-section">
  <legend><?php _e( 'Content', wp_festival( 'domain' ) ); ?></legend>
  <ul>
    <li class="parallax">
      <label for="<?php echo $this->get_field_name( 'content_position' ); ?>"><?php _e( 'Content Position', wp_festival( 'domain' ) ); ?></label>
      <select name="content_position" id="<?php echo $this->get_field_name( 'content_position' ); ?>">
        <?php foreach( $content_position as $k => $v ):
          $selected = isset( $data[ 'content_position' ] ) && $data[ 'content_position' ] == $k ? 'selected="selected"' : ''; ?>
          <option value="<?php echo $k; ?>" <?php echo $selected; ?>><?php echo $v; ?></option>
        <?php endforeach; ?>
      </select>
    </li>
    <li>
      <textarea name="<?php echo $this->get_field_name( 'content' ); ?>" id="<?php echo $this->get_field_id( 'content' ); ?>">
        <?php if( isset( $data[ $this->get_field_name( 'content' ) ] ) ) : ?>
          <?php echo htmlspecialchars( $data[ $this->get_field_name( 'content' ) ] ); ?>
        <?php endif; ?>
      </textarea>
      <?php echo $this->inline_js(); ?>
    </li>
  </ul>
</fieldset>
<!-- /basic info -->

<fieldset class="cfct-form-section">
  <legend><?php _e( 'Styles', wp_festival( 'domain' ) ); ?></legend>
  <ul>
    <li class="colorpicker_wrapper">
      <label for="background_color"><?php _e( 'Background Color', wp_festival( 'domain' ) ); ?></label>
      <input type="text" class="colorpicker" name="background_color" id="background_color" value="<?php echo esc_attr( isset( $data[ 'background_color' ] ) ? $data[ 'background_color' ] : '' ); ?>" />
    </li>
    <li>
      <label><?php _e( 'Background Image:', 'wp-festival' ); ?></label>
      <?php
      // tabs
      $image_selector_tabs = array(
        $this->id_base.'-post-image-wrap' => __( 'Post Images', wp_festival( 'domain' ) ),
        $this->id_base.'-global-image-wrap' => __( 'All Images', wp_festival( 'domain' ) )
      );

      // set active tab
      $active_tab = $this->id_base.'-post-image-wrap';
      if (!empty($data[$this->get_field_name('global_image')])) {
        $active_tab = $this->id_base.'-global-image-wrap';
      }
      ?>
      <!-- image selector tabs -->
      <div class="<?php echo $this->id_base; ?>-image-selectors">
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

<fieldset class="cfct-form-section">
  <legend><?php _e( 'Parallax', wp_festival( 'domain' ) ); ?></legend>
  <ul>
    <li class="parallax">
      <label for="<?php echo $this->get_field_name( 'parallax_rotation' ); ?>"><?php _e( 'Image Rotation (px)', wp_festival( 'domain' ) ); ?></label>
      <input type="text" class="" name="parallax_rotation" id="<?php echo $this->get_field_name( 'parallax_rotation' ); ?>" value="<?php echo esc_attr( !empty( $data[ 'parallax_rotation' ] ) ? $data[ 'parallax_rotation' ] : '400' ); ?>" />
    </li>
    <li class="parallax">
      <label for="<?php echo $this->get_field_name( 'parallax_position' ); ?>"><?php _e( 'Image Position', wp_festival( 'domain' ) ); ?></label>
      <select name="parallax_position" id="<?php echo $this->get_field_name( 'parallax_position' ); ?>">
        <?php foreach( $parallax_position as $k => $v ):
          $selected = isset( $data[ 'parallax_position' ] ) && $data[ 'parallax_position' ] == $k ? 'selected="selected"' : ''; ?>
          <option value="<?php echo $k; ?>" <?php echo $selected; ?>><?php echo $v; ?></option>
        <?php endforeach; ?>
      </select>
    </li>
    <li>
      <label><?php _e( 'Parallax Image:', 'wp-festival' ); ?></label>
      <?php
      // tabs
      $image_selector_tabs = array(
        $this->id_base.'-post-parallax-image-wrap' => __( 'Post Images', wp_festival( 'domain' ) ),
        $this->id_base.'-global-parallax-image-wrap' => __( 'All Images', wp_festival( 'domain' ) )
      );

      // set active tab
      $active_tab = $this->id_base.'-post-parallax-image-wrap';
      if (!empty($data[$this->get_field_name('parallax_global_image')])) {
        $active_tab = $this->id_base.'-global-parallax-image-wrap';
      }
      ?>
      <!-- image selector tabs -->
      <div class="<?php echo $this->id_base; ?>-image-selectors">
        <!-- tabs -->
        <?php echo $this->cfct_module_tabs($this->id_base.'-image-selector-tabs', $image_selector_tabs, $active_tab); ?>
        <!-- /tabs -->
        <div class="cfct-module-tab-contents">
          <!-- select an image from this post -->
          <div id="<?php echo $this->id_base; ?>-post-parallax-image-wrap" <?php echo ( empty( $active_tab ) || $active_tab == $this->id_base.'-post-parallax-image-wrap' ? ' class="active"' : '' ); ?>>
            <?php echo $this->post_image_selector( $data, 'parallax' ); ?>
          </div>
          <!-- / select an image from this post -->
          <!-- select an image from media gallery -->
          <div id="<?php echo $this->id_base; ?>-global-parallax-image-wrap" <?php echo ( $active_tab == $this->id_base.'-global-parallax-image-wrap' ? ' class="active"' : '' ); ?>>
            <?php echo $this->global_image_selector( $data, 'parallax' ); ?>
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