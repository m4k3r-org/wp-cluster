<p>
  <label for="<?php echo $this->get_field_id( 'artist_id' ); ?>"><strong>Select the artist</strong></label>
  <br><br>

  <select class="widefat" name="<?php echo $this->get_field_name( 'artist_id' ); ?>" id="<?php echo $this->get_field_id( 'artist_id' ); ?>">
    <option value="">Select Artist</option>
    <?php foreach( $data[ 'artists' ] as $key => $value ):
      ?>
      <option value="<?php echo $value[ 'id' ]; ?>" <?php if( $value[ 'selected' ] ) echo 'selected="selected"'; ?> ><?php echo $value[ 'name' ]; ?></option>
    <?php endforeach; ?>
  </select>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'text' ); ?>">Text</label>
  <br><br>
  <input class="widefat" type="text" name="<?php echo $this->get_field_name( 'text' ); ?>" id="<?php echo $this->get_field_id( 'text' ); ?>" value="<?php echo $data[ 'text' ]; ?>" />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'button1_text' ); ?>">Button 1 Text</label>
  <br><br>
  <input class="widefat" type="text" name="<?php echo $this->get_field_name( 'button1_text' ); ?>" id="<?php echo $this->get_field_id( 'button1_text' ); ?>" value="<?php echo $data[ 'button1_text' ]; ?>" />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'button1_link' ); ?>">Button 1 Link</label>
  <br><br>
  <input class="widefat" type="text" name="<?php echo $this->get_field_name( 'button1_link' ); ?>" id="<?php echo $this->get_field_id( 'button1_link' ); ?>" value="<?php echo $data[ 'button1_link' ]; ?>" />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'button2_text' ); ?>">Button 2 Text</label>
  <br><br>
  <input class="widefat" type="text" name="<?php echo $this->get_field_name( 'button2_text' ); ?>" id="<?php echo $this->get_field_id( 'button2_text' ); ?>" value="<?php echo $data[ 'button2_text' ]; ?>" />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'button2_link' ); ?>">Button 2 Link</label>
  <br><br>
  <input class="widefat" type="text" name="<?php echo $this->get_field_name( 'button2_link' ); ?>" id="<?php echo $this->get_field_id( 'button2_link' ); ?>" value="<?php echo $data[ 'button2_link' ]; ?>" />
</p>