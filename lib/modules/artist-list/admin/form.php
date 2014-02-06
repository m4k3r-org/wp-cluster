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
  fieldset#artists-list-artists ul li input[type=text]{
    display: inline;
    width: 35px;
    float: right;
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
  <label for="artist_type"><?php _e( 'Artist Type' ); ?></label>
  <select name="artist_type" id="artist_type">
    <?php foreach( $artist_types as $key => $type ):
      $selected = isset( $data[ 'artist_type' ] ) && $data[ 'artist_type' ] == $key ? 'selected="selected"' : ''; ?>
      <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $type; ?></option>
    <?php endforeach; ?>
  </select>
  <label for="artist_columns"><?php _e( 'Artist Columns' ); ?></label>
  <select name="artist_columns" id="artist_columns">
    <?php foreach( $artist_columns as $columns ):
      $selected = isset( $data[ 'artist_columns' ] ) && $data[ 'artist_columns' ] == $columns ? 'selected="selected"' : ''; ?>
      <option value="<?php echo $columns; ?>" <?php echo $selected; ?>><?php echo $columns; ?></option>
    <?php endforeach; ?>
  </select>
</fieldset>

<!-- artists -->
<fieldset id="artists-list-artists" class="cfct-form-section">
  <legend><?php _e( 'Selected Artists', 'wp-festival' ); ?></legend>
  <div class="div-wrapper clearfix">
    <ul>
      <?php foreach( $artists as $artist ):
        $alt = !isset( $alt ) ? '' : ( $alt === '' ? 'alt' : '' );
        $checked = isset( $data[ 'artists' ] ) && is_array( $data[ 'artists' ] ) && in_array( $artist[ 'ID' ], $data[ 'artists' ] ) ? 'checked="checked"' : ''; ?>
        <li class="<?php echo $alt; ?>">
          <input type="checkbox" name="artists[]" id="artist-<?php echo $artist[ 'ID' ]; ?>" class="post-type-select" value="<?php echo $artist[ 'ID' ]; ?>" <?php echo $checked; ?> />
          <label for="artist-<?php echo $artist[ 'ID' ]; ?>"><?php echo $artist[ 'post_title' ]; ?></label>
          <input type="text" id="sorting-<?php echo $artist[ 'ID' ]; ?>" name="sorting[<?php echo $artist[ 'ID' ]; ?>]" value="<?php echo esc_attr( isset( $data[ 'sorting' ] ) && is_array( $data[ 'sorting' ] ) && isset( $data[ 'sorting' ][ $artist[ 'ID' ] ] ) ? $data[ 'sorting' ][ $artist[ 'ID' ] ] : '' ); ?>" />
          <div style="clear:both;"></div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</fieldset>