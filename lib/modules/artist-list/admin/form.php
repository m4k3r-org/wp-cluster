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

	<br/>
	<input type="checkbox" name="complete_lineup_button" id="complete_lineup_button" value="1" <?php if ( isset( $data[ 'complete_lineup_button' ] ) ): echo 'checked="checked"'; endif; ?> />
	<label for="complete_lineup_button"><?php _e( 'Show "Complete Lineup" button', 'wp-festival' ); ?></label>

	<div class="complete-lineup-url">
		<br/>
		<label for="complete_lineup_page_url">Complete Lineup Page URL</label>
		<span class="cfct-input-full">
			<input type="text" name="complete_lineup_page_url" id="complete_lineup_page_url" value="<?php echo esc_attr( isset( $data[ 'complete_lineup_page_url' ] ) ? $data[ 'complete_lineup_page_url' ] : '' ); ?>" />
		</span>
	</div>
	
	<br />
	<input type="checkbox" name="link_to_single_artist_page" id="link_to_single_artist_page" value="1" <?php if ( isset( $data[ 'link_to_single_artist_page' ] ) ): echo 'checked="checked"'; endif; ?> />
	<label for="link_to_single_artist_page"><?php _e( 'Link Items to the Single Artist Page', 'wp-festival' ); ?></label>
</fieldset>
<!-- /basic info -->

<!-- display options -->
<fieldset id="artists-list-display-options" class="cfct-form-section">
  <legend><?php _e( 'Display Options', 'wp-festival' ) ?></legend>
  <ul>
    <li>
      <label for="artist_image"><?php _e( 'Artist Image', wp_festival2( 'domain' ) ); ?></label>
      <select name="artist_image" id="artist_image">
        <?php foreach( $artist_images as $k => $v ):
          $selected = isset( $data[ 'artist_image' ] ) && $data[ 'artist_image' ] == $k ? 'selected="selected"' : ''; ?>
          <option value="<?php echo $k; ?>" <?php echo $selected; ?>><?php echo $v; ?></option>
        <?php endforeach; ?>
      </select>
    </li>
    <li>
      <label for="order_by"><?php _e( 'Order by', wp_festival2( 'domain' ) ); ?></label>
      <select name="order_by" id="order_by">
          <option value="none" <?php echo isset( $data[ 'order_by' ] ) && $data[ 'order_by' ] == 'none' ? 'selected="selected"' : ''; ?>><?php _e( 'None', wp_festival2( 'domain' ) ); ?></option>
          <option value="ID" <?php echo isset( $data[ 'order_by' ] ) && $data[ 'order_by' ] == 'ID' ? 'selected="selected"' : ''; ?>><?php _e( 'ID', wp_festival2( 'domain' ) ); ?></option>
          <option value="author" <?php echo isset( $data[ 'order_by' ] ) && $data[ 'order_by' ] == 'author' ? 'selected="selected"' : ''; ?>><?php _e( 'Author', wp_festival2( 'domain' ) ); ?></option>
          <option value="title" <?php echo isset( $data[ 'order_by' ] ) && $data[ 'order_by' ] == 'title' ? 'selected="selected"' : ''; ?>><?php _e( 'Title', wp_festival2( 'domain' ) ); ?></option>
          <option value="name" <?php echo isset( $data[ 'order_by' ] ) && $data[ 'order_by' ] == 'name' ? 'selected="selected"' : ''; ?>><?php _e( 'Name', wp_festival2( 'domain' ) ); ?></option>
          <option value="date" <?php echo isset( $data[ 'order_by' ] ) && $data[ 'order_by' ] == 'date' ? 'selected="selected"' : ''; ?>><?php _e( 'Date', wp_festival2( 'domain' ) ); ?></option>
          <option value="modified" <?php echo isset( $data[ 'order_by' ] ) && $data[ 'order_by' ] == 'modified' ? 'selected="selected"' : ''; ?>><?php _e( 'Modified', wp_festival2( 'domain' ) ); ?></option>
          <option value="parent" <?php echo isset( $data[ 'order_by' ] ) && $data[ 'order_by' ] == 'parent' ? 'selected="selected"' : ''; ?>><?php _e( 'Parent', wp_festival2( 'domain' ) ); ?></option>
          <option value="rand" <?php echo isset( $data[ 'order_by' ] ) && $data[ 'order_by' ] == 'rand' ? 'selected="selected"' : ''; ?>><?php _e( 'Rand', wp_festival2( 'domain' ) ); ?></option>
          <option value="comment_count" <?php echo isset( $data[ 'order_by' ] ) && $data[ 'order_by' ] == 'comment_count' ? 'selected="selected"' : ''; ?>><?php _e( 'Comment Count', wp_festival2( 'domain' ) ); ?></option>
          <option value="menu_order" <?php echo isset( $data[ 'order_by' ] ) && $data[ 'order_by' ] == 'menu_order' ? 'selected="selected"' : ''; ?>><?php _e( 'Menu Order', wp_festival2( 'domain' ) ); ?></option>
          <option value="post__in" <?php echo isset( $data[ 'order_by' ] ) && $data[ 'order_by' ] == 'post__in' ? 'selected="selected"' : ''; ?>><?php _e( 'post__in Order', wp_festival2( 'domain' ) ); ?></option>
      </select>
    </li>
    <li>
      <label for="artist_columns"><?php _e( 'Number of Images per Row', wp_festival2( 'domain' ) ); ?></label>
      <select name="artist_columns" id="artist_columns">
        <?php foreach( $artist_columns as $columns ):
          $selected = isset( $data[ 'artist_columns' ] ) && $data[ 'artist_columns' ] == $columns ? 'selected="selected"' : ''; ?>
          <option value="<?php echo $columns; ?>" <?php echo $selected; ?>><?php echo $columns; ?></option>
        <?php endforeach; ?>
      </select>
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
  </ul>
</fieldset>

<!-- artists -->
<fieldset id="artists-list-artists" class="cfct-form-section">
  <legend><?php _e( 'Selected Artists', 'wp-festival' ); ?></legend>
  <div class="div-wrapper clearfix">
    <ul>
      <li class="alt header">
        <span class="order"><?php _e( 'Order', wp_festival2( 'domain' ) ); ?></span>
        <span class="column"><?php _e( 'Column', wp_festival2( 'domain' ) ); ?></span>
        <span class="custom_date"><?php _e( 'Custom Date', wp_festival2( 'domain' ) ); ?></span>
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

<script type="text/javascript">
  if( typeof jQuery.fn.wpColorPicker == 'function' ) { jQuery( '.colorpicker' ).wpColorPicker(); }
  if( typeof jQuery.fn.datepicker == 'function' ) { jQuery( '.datepicker' ).datepicker( { dateFormat : 'dd-mm-yy' } ); }


	jQuery('.cfct-module-form').on('change', '#complete_lineup_button', function(){
		var t = jQuery(this);

		if ( t.prop( 'checked' ) === true ){
			jQuery('.complete-lineup-url').show();
		}
		else
		{
			jQuery('.complete-lineup-url').hide();
		}
	});

	jQuery('.complete-lineup-url').hide();

	if ( jQuery('#complete_lineup_button').prop('checked') === true )
	{
		jQuery('.complete-lineup-url').show();
	}

</script>