<div id="<?php echo $this->id_base; ?>-admin-form-wrapper">
  
  <fieldset class="cfct-form-section">
    <!-- title -->
    <legend><?php _e( 'Title', wp_festival( 'domain' ) ); ?></legend>
    <span class="cfct-input-full">
      <input 
        type="text" 
        name="<?php echo $this->get_field_id( 'title' ); ?>" 
        id="<?php echo $this->get_field_id( 'title' ); ?>" 
        value="<?php echo esc_attr( $data[ $this->get_field_name( 'title' ) ] ); ?>" />
    </span>
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
      value="<?php echo json_encode( $tax_defs ); ?>" />
  </fieldset>
  
  
  <?php echo $this->admin_form_taxonomy_filter( $data ); ?>
  
  <?php echo $this->admin_form_display_options( $data ); ?>
</div>