<p>
  <input type="checkbox" name="<?php echo $this->get_field_name( 'featured' ); ?>" id="<?php echo $this->get_field_id( 'featured' ); ?>" value="1" <?php if( isset( $data[ 'featured' ] ) && $data[ 'featured' ] == '1' ) echo 'checked="checked"' ?> />
  <label for="<?php echo $this->get_field_id( 'featured' ); ?>">Show Only Featured</label>
</p>
