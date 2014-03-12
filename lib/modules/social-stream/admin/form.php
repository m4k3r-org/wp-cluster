
<fieldset>
  <legend><h2>General</h2></legend>
  <p>
    <label for="<?php echo $this->get_field_id( 'wall' ); ?>"><?php _e( 'Use Wall' ) ?></label>
    <select id="<?php echo $this->get_field_id( 'wall' ); ?>" name="<?php echo $this->get_field_name( 'wall' ); ?>" style="width:100%;">
      <option value="false" <?php selected( 'false', $data[ $this->get_field_name( 'wall' ) ], true ); ?>>No</option>
      <option value="true" <?php selected( 'true', $data[ $this->get_field_name( 'wall' ) ], true ); ?>>Yes</option>
    </select>
  </p>

  <p>
    <label for="<?php echo $this->get_field_id( 'rotate_delay' ); ?>"><?php _e( 'Rotate Delay (ms)' ) ?></label>
    <input type="text" value="<?php echo !empty( $data[ $this->get_field_name( 'rotate_delay' ) ] )?$data[ $this->get_field_name( 'rotate_delay' ) ]:''; ?>" id="<?php echo $this->get_field_id( 'rotate_delay' ); ?>" name="<?php echo $this->get_field_name( 'rotate_delay' ); ?>" />
  </p>
</fieldset>

<fieldset>
  <legend><h2>Instagram</h2></legend>

  <p>
    <label for="<?php echo $this->get_field_id( 'instagram_search_for' ); ?>"><?php _e( 'Search for' ) ?></label>
    <input type="text" value="<?php echo !empty( $data[ $this->get_field_name( 'instagram_search_for' ) ] )?$data[ $this->get_field_name( 'instagram_search_for' ) ]:''; ?>" id="<?php echo $this->get_field_id( 'instagram_search_for' ); ?>" name="<?php echo $this->get_field_name( 'instagram_search_for' ); ?>" />
    <small>e.g. !123456 - The user feed will show the latest posts from a specific user ID. @123456 - To display the latest posts for a specific location. #london - To display the latest posts for a specific tag. ?55.123/-1.345/1000 - To display the latest posts for a geographical location you need 3 parameters - latitude, longitude and distance(in meters up to 5000m). !123456,@123456,?55.123/-1.345/1000,#london,#newyork - Multiple search.</small>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'instagram_client_id' ); ?>"><?php _e( 'Client ID' ) ?></label>
    <input type="text" value="<?php echo !empty( $data[ $this->get_field_name( 'instagram_client_id' ) ] )?$data[ $this->get_field_name( 'instagram_client_id' ) ]:''; ?>" id="<?php echo $this->get_field_id( 'instagram_client_id' ); ?>" name="<?php echo $this->get_field_name( 'instagram_client_id' ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'instagram_access_token' ); ?>"><?php _e( 'Access Token' ) ?></label>
    <input type="text" value="<?php echo !empty( $data[ $this->get_field_name( 'instagram_access_token' ) ] )?$data[ $this->get_field_name( 'instagram_access_token' ) ]:''; ?>" id="<?php echo $this->get_field_id( 'instagram_access_token' ); ?>" name="<?php echo $this->get_field_name( 'instagram_access_token' ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'instagram_redirect_url' ); ?>"><?php _e( 'Redirect URL' ) ?></label>
    <input type="text" value="<?php echo !empty( $data[ $this->get_field_name( 'instagram_redirect_url' ) ] )?$data[ $this->get_field_name( 'instagram_redirect_url' ) ]:''; ?>" id="<?php echo $this->get_field_id( 'instagram_redirect_url' ); ?>" name="<?php echo $this->get_field_name( 'instagram_redirect_url' ); ?>" />
  </p>
</fieldset>