<div id="<?php echo $this->id_base; ?>-admin-form-wrapper">
  <style>
    input.wauto {
      width: auto !important;
    }
  </style>
  <fieldset class="cfct-form-section">
    <!-- title -->
    <legend><?php _e( 'Title', wp_festival( 'domain' ) ); ?></legend>
    <div class="cfct-inline-els">
      <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', wp_festival( 'domain' ) ); ?></label>
      <input 
        type="text" 
        name="<?php echo $this->get_field_name( 'title' ); ?>" 
        id="<?php echo $this->get_field_id( 'title' ); ?>" 
        value="<?php echo esc_attr( $data[ $this->get_field_name( 'title' ) ] ); ?>" />
    </div>
    <div class="cfct-inline-els">
      <label for="<?php echo $this->get_field_id( 'content' ); ?>"><?php _e( 'Tagline:', wp_festival( 'domain' ) ); ?></label>
      <input 
        type="text"
        name="<?php echo $this->get_field_name( 'content' ); ?>" 
        id="<?php echo $this->get_field_id( 'content' ); ?>"
        value="<?php echo esc_attr( $data[ $this->get_field_name( 'content' ) ] ); ?>" />
    </div>
    <!-- /title -->
  </fieldset>
  
  <fieldset class="cfct-form-section" id="<?php echo $this->gfi( 'post_type_checks' ); ?>">
    <legend><?php _e( 'Post Type', wp_festival( 'domain' ) ); ?></legend>
    <?php if( count( $post_types ) > 1 ) : ?>
      <div class="cfct-columnized cfct-columnized-4x">
        <ul>
          <?php foreach( $post_types as $key => $post_type ) : ?>
            <?php $post_taxonomies = $this->get_post_type_taxonomies( $key ); ?>
            <li>
                <input 
                  type="checkbox" 
                  name="<?php echo $this->gfn( 'post_type' ); ?>[]" 
                  id="<?php echo $this->gfi( 'post-type-' . $key ) ?>" 
                  class="post-type-select" 
                  data-taxonomies="<?php echo implode( ',', $post_taxonomies ); ?>" 
                  value="<?php echo $key; ?>" <?php echo ( is_array( $selected ) && in_array( $key, $selected ) ) ? 'checked="checked"' : ''; ?> />
                <label for="<?php echo $this->gfi( 'post-type-' . $key ); ?>"><?php echo $post_type->labels->name; ?></label>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php elseif( count( $post_types ) == 1 ) : // if we only have one option then just set a hidden element ?>
      <?php $key = key( $post_types ); ?>
      <?php $post_type = current( $post_types ); ?>
      <?php $post_taxonomies = $this->get_post_type_taxonomies( $key ); ?>
      <input 
        type="hidden" 
        class="post-type-select" 
        name="<?php echo $this->get_field_name( 'post_type' ); ?>[]" 
        value="<?php echo $key; ?>" 
        data-taxonomies="<?php echo implode( ',', $post_taxonomies ); ?>" />
    <?php elseif( empty( $post_types ) ) : ?>
      <?php $type            = get_post_type( $this->default_post_type ); ?>
      <?php $post_taxonomies = $this->get_post_type_taxonomies( $type->name ); ?>
      <input 
        type="hidden" 
        class="post-type-select" 
        name="<?php echo $this->gfn( 'post_type' ); ?>[]" 
        value="<?php echo $post_type->name; ?>" 
        data-taxonomies="<?php echo implode( ',', $post_taxonomies ); ?>" />
    <?php endif; ?>
    <input 
      type="hidden" 
      name="<?php echo $this->gfn( 'tax_defs' ); ?>" 
      id="<?php echo $this->gfi( 'tax_defs' ); ?>" 
      disabled="disabled" 
      value='<?php echo json_encode( $tax_defs ); ?>' />
  </fieldset>
  
  <fieldset class="cfct-form-section">
    <script type="text/javascript">
      // you will not see this in the DOM, it gets parsed right away at ajax load
      var tax_defs = <?php echo json_encode( $tax_defs ); ?>;
    </script>
    <legend><?php _e( 'Taxonomies', 'carrington-build' ); ?></legend>
    <!-- taxonomy select -->
    <div class="<?php echo $this->id_base; ?>-input-wrapper <?php echo $this->id_base; ?>-post-category-select <?php echo $this->id_base; ?>-tax-wrapper">
      <div id="<?php echo $this->gfi( 'tax-select-inputs' ); ?>" class="cfct-inline-els">
        <?php echo $this->get_taxonomy_dropdown( $taxes, $data ); ?>
        <button id="<?php echo $this->id_base; ?>-add-tax-button" class="button" type="button"><?php _e( 'Add Filter', 'carrington-build' ); ?></button>
        <span class="<?php echo $this->id_base; ?>-loading cfct-spinner" style="display: none;">Loading&hellip;</span>
      </div>
      <div id="<?php echo $this->id_base; ?>-tax-filter-items" class="cfct-module-admin-repeater-block">
        <ol class="<?php echo ( empty( $data[ $this->gfn( 'tax_input' ) ] ) ? ' no-items' : '' ); ?>">
          <?php echo $this->get_taxonomy_filter_items( $data ); ?>
        </ol>
      </div>
    </div>
    <?php echo $this->get_filter_advanced_options( $data ); ?>
    <!-- /taxonomy select -->
  </fieldset>
  
  <fieldset class="cfct-form-section">
    <legend><?php _e( 'Author', 'carrington-build' ); ?></legend>
    <!-- author select -->
    <div class="cfct-inline-els">
      <?php echo $this->get_author_dropdown( $data ); ?>
    </div>
    <!-- /author select -->
  </fieldset>
  
  <script type="text/javascript">jQuery(document).ready(function() {});</script>
  <fieldset class="cfct-form-section">
    <legend><?php _e( 'Display', 'carrington-build' ); ?></legend>
    <div class="<?php echo $this->id_base; ?>-display-group-left">
      <div class="cfct-inline-els">
        <label for="<?php echo $this->get_field_id( 'template' ); ?>"><?php _e( 'Template:', wp_festival( 'domain' ) ); ?></label>
        <select name="<?php echo $this->get_field_name( 'template' ); ?>" id="<?php echo $this->get_field_id( 'template' ); ?>">
          <?php foreach( $templates as $k => $v ) : 
            $selected = isset( $data[ $this->get_field_name( 'template' ) ] ) && $data[ $this->get_field_name( 'template' ) ] == $k ? 'selected="selected"' : ''; ?>
            <option value="<?php echo $k ?>" <?php echo $selected; ?>><?php echo $v; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <!-- num posts input -->
      <div class="cfct-inline-els">
        <label for="<?php echo $this->get_field_id( 'item_count' ); ?>"><?php _e( 'Number of Items:', 'carrington-build' ); ?></label>
        <input 
          class="cfct-number-field wauto" 
          id="<?php echo $this->get_field_id( 'item_count' ); ?>" 
          name="<?php echo $this->get_field_name( 'item_count' ); ?>" 
          type="text" 
          value="<?php echo esc_attr( $this->get_data( 'item_count', $data, $this->default_item_count ) ); ?>" />
      </div>
      <!-- / num posts input -->

      <!-- num posts input -->
      <div class="cfct-inline-els">
        <label for="<?php echo $this->get_field_id( 'item_offset' ); ?>"><?php _e( 'Start at Item:', 'carrington-build' ); ?></label>
        <input 
          class="cfct-number-field wauto" 
          id="<?php echo $this->get_field_id( 'item_offset' ); ?>" 
          name="<?php echo $this->get_field_name( 'item_offset' ); ?>" 
          type="text" 
          value="<?php echo esc_attr( $this->get_data( 'item_offset', $data, $this->default_item_offset ) ); ?>" />
      </div>
      <!-- / num posts input -->
    </div>
  </fieldset>
  
</div>