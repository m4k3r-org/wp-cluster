<!-- Do our inline CSS here -->
<style type="text/css">


.carousel-sortable-placeholder {
					height: 18px;
					background-color: gray;
					border: 1px solid white;
					border-width: 1px 0
				}
				
				/* Carousel List */
				.carousel-list {
					background-color: #eee;
					border: 1px solid #aaa;
					-moz-border-radius: 5px; /* FF1+ */
					-webkit-border-radius: 5px; /* Saf3+, Chrome */
					border-radius: 5px; /* Standard. IE9 */
					padding: 0;
					margin: 0;
				}
				.carousel-list li {
					border-bottom: 1px solid #aaa;
					list-style-type: none;
					margin: 0;
					min-height: 45px;
					padding: 5px;
				}
				.carousel-list li:hover {
					background: #fff url(data:image/gif;base64,R0lGODlhFAAIAJEDAKGhoaKiov///////yH5BAEAAAMALAAAAAAUAAgAAAIbhIOAMe0vopyv2otXUBvhNoXCR5bKkRikSH0FADs=) 100% 50% no-repeat;
					cursor: move;
				}
				.carousel-list li.carousel-item-edit:hover {
					background: none;
				}
				.carousel-list li:first-child {
					-moz-border-radius-topleft: 4px; /* FF1+ */
					-webkit-border-top-left-radius: 4px; /* Saf3+, Chrome */
					border-top-left-radius: 4px; /* Standard. IE9 */
					-moz-border-radius-topright: 4px; /* FF1+ */
					-webkit-border-top-right-radius: 4px; /* Saf3+, Chrome */
					border-top-right-radius: 4px; /* Standard. IE9 */
				}
				.carousel-list li:last-child {
					border-bottom: 0;
					-moz-border-radius-bottomleft: 4px; /* FF1+ */
					-webkit-border-bottom-left-radius: 4px; /* Saf3+, Chrome */
					border-bottom-left-radius: 4px; /* Standard. IE9 */
					-moz-border-radius-bottomright: 4px; /* FF1+ */
					-webkit-border-bottom-right-radius: 4px; /* Saf3+, Chrome */
					border-bottom-right-radius: 4px; /* Standard. IE9 */
				}
				.carousel-list li.no-items {
					line-height: 45px;
				}
				
				/* clearfix */
				.carousel-list li:after { content: "."; display: block; height: 0; clear: both; visibility: hidden; }

				/* Floating */
				.carousel-item-img,
				.carousel-item-title,
				.carousel-edit-form {
					float: left;
				}

				/* Setting heights */
				.carousel-item-img,
				.carousel-item-title {
					height: 45px;
				}
				.carousel-item-img {
				  background-image: #D2CFCF url(data:image/png;base64,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) center center no-repeat;
					display: inline-block;
					margin-right: 10px;
					width: 150px;
				}
				.carousel-item-title {
					font-size: 15px;
					line-height: 42px;
				}

				/* Show/hide elements for editing */
				.carousel-item-edit .carousel-item-title {
					display: none;
				}
				.carousel-item-edit .carousel-item-img {
					height: 150px;
					background-position: 0px 0px !important;
				}
				.carousel-edit-form {
					display: none;
				}
				.carousel-item-edit .carousel-edit-form {
					display: block;
				}

				/* Edit mode */
				.carousel-edit-form {
					padding: 5px 0;
					width: 475px;
				}
				.carousel-edit-form label {
					display: none;
				}
				.carousel-edit-form input.text,
				.carousel-edit-form textarea {
					width: 90%;	
				}
				.carousel-edit-form input.text {
					font-size: 13px;
					margin-bottom: 8px;
				}
				.carousel-edit-form textarea {
					font-size: 11px;
					height: 80px;
				}
				.carousel-edit-done {
					margin-top: 7px;
				}
				.carousel-edit-remove {
					line-height: 1px;
					margin: 0 0 0 10px;
				}			
				
				/* Carousel Live Search */
				#car-items {
					min-height: 400px;
				}
				.cfct-popup-content #car-item-search {
					margin-bottom: 10px;
					position: relative;
				}
				.cfct-popup-content #car-item-search label {
					float: left;
					font-size: 13px;
					font-weight: bold;
					line-height: 23px;
					width: 165px;
				}
				.cfct-popup-content #car-item-search .elm-align-bottom {
					padding-left: 165px;
				}
				.cfct-module-form .cfct-popup-content #car-item-search #car-search-term {
					/**
					 * @workaround absolute positioning fix
					 * IE doesn\'t position absolute elements beneath inline-block els
					 * instead, it overlays them on top of elements.
					 * Basically, this caused the type-ahead search to sit on top
					 * of the input. A simple display: block fixes it.
					 * @affected ie7
					 */
					display: block;
					margin: 0;
					width: 500px; 
				}
				.cfct-popup-content #car-item-search .otypeahead-target {
					background: white;
					border: 1px solid #ccc;
					-moz-border-radius-bottomleft: 5px; /* FF1+ */
					-moz-border-radius-bottomright: 5px; /* FF1+ */
					-webkit-border-bottom-left-radius: 5px; /* Saf3+, Chrome */
					-webkit-border-bottom-right-radius: 5px; /* Saf3+, Chrome */
					border-bottom-left-radius: 5px; /* Standard. IE9 */
					border-bottom-right-radius: 5px; /* Standard. IE9 */
					border-width: 0 1px 1px 1px;
					display: none;
					left: 0;
					margin-top: 0;
					margin-left: 165px;
					padding: 0;
					position: absolute;
					width: 498px;
					z-index: 99;
				}
				.cfct-popup-content #car-item-search .otypeahead-target ul,
				.cfct-popup-content #car-item-search .otypeahead-target li,
				.cfct-popup-content #car-item-search .otypeahead-target li a {
					margin: 0;
					padding: 0;
				}
				.cfct-popup-content #car-item-search .otypeahead-target li a {
					color: #454545;
					text-decoration: none;
					display: block;
					/*width: 738px;*/
					padding: 5px;
				}
				.cfct-popup-content #car-item-search .otypeahead-target li a:hover,
				.cfct-popup-content #car-item-search .otypeahead-target li.otypeahead-current a {
					color: #333;
					background-color: #eee;
				}
				.cfct-popup-content #car-item-search .otypeahead-target li .carousel-item-title,
				.cfct-popup-content #car-item-search .otypeahead-target li.no-items-found {
					float: none;
					font-size: 12px;
					height: 15px;
					line-height: 15px;
				}
				.cfct-popup-content #car-item-search .otypeahead-target li:last-child a {
					-moz-border-radius-bottomleft: 5px; /* FF1+ */
					-moz-border-radius-bottomright: 5px; /* FF1+ */
					-webkit-border-bottom-left-radius: 5px; /* Saf3+, Chrome */
					-webkit-border-bottom-right-radius: 5px; /* Saf3+, Chrome */
					border-bottom-left-radius: 5px; /* Standard. IE9 */
					border-bottom-right-radius: 5px; /* Standard. IE9 */
				}
				.cfct-popup-content #car-item-search .otypeahead-target li.no-items-found,
				.cfct-popup-content #car-item-search .otypeahead-target li .otypeahead-loading {
					padding: 5px;
				}
				.cfct-popup-content #car-item-search .otypeahead-target .cfct-module-carousel-loading {
					padding: 5px;
					font-size: .9em;
					color: gray;
					-moz-border-radius-bottomleft: 5px; /* FF1+ */
					-moz-border-radius-bottomright: 5px; /* FF1+ */
					-webkit-border-bottom-left-radius: 5px; /* Saf3+, Chrome */
					-webkit-border-bottom-right-radius: 5px; /* Saf3+, Chrome */
					border-bottom-left-radius: 5px; /* Standard. IE9 */
					border-bottom-right-radius: 5px; /* Standard. IE9 */
				}
				.cfct-popup-content #car-item-search .otypeahead-target li .carousel-item-img {
					display: none;
				}


















  .colorpicker_wrapper label {
    display: block;
    float: left;
    margin-right: 10px;
    line-height: 22px;
    width: 125px;
  }
</style>

<fieldset class="cfct-form-section">
  <legend><?php _e( 'Event', 'wp-festival' ); ?></legend>
  <div id="car-items" class="active">
    <div id="car-item-search" class="car-item-search-container">
      <label for="car-search-term"><? _e( 'Search Event:', wp_festival( 'domain' ) ); ?></label>
      <input type="text" name="car-search-term" id="car-search-term" value="" />
      <span class="elm-help elm-align-bottom"><? _e( 'Only items with a featured image are available.', wp_festival( 'domain' ) ); ?></span>
    </div>
    <div class="car-items-wrapper">
      <ol class="carousel-list">
        <?php if( isset( $data[ $this->get_field_name( 'posts' ) ] ) && count( $data[ $this->get_field_name( 'posts' ) ] ) )  : ?>
          <?php foreach( $data[ $this->get_field_name( 'posts' ) ] as $item ) : ?>
            <?php echo $this->get_event_admin_item( $item ); ?>
          <?php endforeach; ?>
        <?php else : ?>
          <li class="no-items"><?php _e( 'Event is not chosen', wp_festival( 'domain' ) ); ?></li>
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