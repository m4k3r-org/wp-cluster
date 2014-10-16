<p>
  <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title (optional)</label>
  <input class="widefat" type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" id="<?php echo $this->get_field_id( 'title' ); ?>" value="<?php echo $data['title']; ?>" />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'description' ); ?>">Description (optional)</label>
  <textarea class="widefat" name="<?php echo $this->get_field_name( 'description' ); ?>" id="<?php echo $this->get_field_id( 'description' ); ?>" ><?php echo $data['description'] ?></textarea>
</p>

<p>
  <input type="checkbox" name="<?php echo $this->get_field_name( 'featured' ); ?>" id="<?php echo $this->get_field_id( 'featured' ); ?>" value="1" <?php if( isset( $data[ 'featured' ] ) && $data[ 'featured' ] == '1' ) echo 'checked="checked"' ?> />
  <label for="<?php echo $this->get_field_id( 'featured' ); ?>">Show Only Featured</label>
</p>
