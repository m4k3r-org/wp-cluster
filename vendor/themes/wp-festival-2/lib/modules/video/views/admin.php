<p>
  <label for="<?php echo $this->get_field_id( 'code' ); ?>">Youtube Video Code</label>
  <br><br>
  <input class="widefat" type="text" name="<?php echo $this->get_field_name( 'code' ); ?>" id="<?php echo $this->get_field_id( 'code' ); ?>" value="<?php echo $data[ 'code' ]; ?>" />
</p>

<p>
  <br><br>
  <label for="<?php echo $this->get_field_id( 'background_mp4_url' ); ?>">Background .MP4 Video URL</label>
  <br><br>
  <input class="widefat" type="text" name="<?php echo $this->get_field_name( 'background_mp4_url' ); ?>" id="<?php echo $this->get_field_id( 'background_mp4_url' ); ?>" value="<?php echo $data[ 'background_mp4_url' ]; ?>" />
</p>

<p>
  <br><br>
  <label for="<?php echo $this->get_field_id( 'background_webm_url' ); ?>">Background .WebM Video URL</label>
  <br><br>
  <input class="widefat" type="text" name="<?php echo $this->get_field_name( 'background_webm_url' ); ?>" id="<?php echo $this->get_field_id( 'background_webm_url' ); ?>" value="<?php echo $data[ 'background_webm_url' ]; ?>" />
</p>

<p>
  <br><br>
  <label for="<?php echo $this->get_field_id( 'background_ogg_url' ); ?>">Background .Ogg Video URL</label>
  <br><br>
  <input class="widefat" type="text" name="<?php echo $this->get_field_name( 'background_ogg_url' ); ?>" id="<?php echo $this->get_field_id( 'background_ogg_url' ); ?>" value="<?php echo $data[ 'background_ogg_url' ]; ?>" />
</p>

<p>
  <br><br>
  <label for="<?php echo $this->get_field_id( 'background_mov_url' ); ?>">Background .MOV Video URL</label>
  <br><br>
  <input class="widefat" type="text" name="<?php echo $this->get_field_name( 'background_mov_url' ); ?>" id="<?php echo $this->get_field_id( 'background_mov_url' ); ?>" value="<?php echo $data[ 'background_mov_url' ]; ?>" />
</p>

<?php if ( ! empty( $data['images']['meta']['sel_image'] )  ): ?>
  <p>
    <img id="video-widget-selected-image" src="<?php echo $data['images']['meta']['sel_image']; ?>" alt="Selected Image">
  </p>
<?php endif; ?>
  <p>
    <label for="<?php echo $this->get_field_id( 'image' ); ?>"><strong>Select an image</strong></label>
    <br><br>

    <select class="widefat video-widget-image" name="<?php echo $this->get_field_name( 'image' ); ?>" id="<?php echo $this->get_field_id( 'image' ); ?>">
      <option value="">No Image (Display Youtube Thumbnail)</option>
      <?php foreach ( $data['images']['data'] as $key => $image ):
        ?>
        <option value="<?php echo $image['src']; ?>" <?php if ( $image['selected'] ) echo 'selected="selected"';  ?> ><?php echo $image['name'];  ?></option>
      <?php endforeach; ?>
    </select>
  </p>

  <input type="hidden" value="<?php echo $data['images']['meta']['sel_image_id']; ?>" name="<?php echo $this->get_field_name( 'image_image_id' ); ?>" id="<?php echo $this->get_field_id( 'image_image_id' ); ?>">

