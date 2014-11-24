<p>
  <label for="<?php echo $this->get_field_id( 'action' ); ?>">Action</label>
  <br><br>
  <input class="widefat" type="text" name="<?php echo $this->get_field_name( 'action' ); ?>" id="<?php echo $this->get_field_id( 'action' ); ?>" value="<?php echo $data[ 'action' ]; ?>" />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'text' ); ?>">Text</label>
  <br><br>
  <input class="widefat" type="text" name="<?php echo $this->get_field_name( 'text' ); ?>" id="<?php echo $this->get_field_id( 'text' ); ?>" value="<?php echo $data[ 'text' ]; ?>" />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'url' ); ?>">URL</label>
  <br><br>
  <input class="widefat" type="text" name="<?php echo $this->get_field_name( 'url' ); ?>" id="<?php echo $this->get_field_id( 'url' ); ?>" value="<?php echo $data[ 'url' ]; ?>" />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'background' ); ?>">Background</label>
  <br><br>
  <select name="<?php echo $this->get_field_name( 'background' ); ?>" id="<?php echo $this->get_field_id( 'background' ); ?>">
    <option value="0">Lighter</option>
    <option value="1" <?php if ($data['background']) echo 'selected="selected"';  ?> >Darker</option>
  </select>
</p>
