
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

<fieldset>
  <legend><h2>Twitter</h2></legend>
  <p>
    <label for="<?php echo $this->get_field_id( 'twitter_search_for' ); ?>"><?php _e( 'Search for' ) ?></label>
    <input type="text" value="<?php echo !empty( $data[ $this->get_field_name( 'twitter_search_for' ) ] )?$data[ $this->get_field_name( 'twitter_search_for' ) ]:''; ?>" id="<?php echo $this->get_field_id( 'twitter_search_for' ); ?>" name="<?php echo $this->get_field_name( 'twitter_search_for' ); ?>" />
    <small>e.g. google - Tweets from a specific user name. /9927875 - Tweets from a list. #london - Tweets from a search.</small>
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'twitter_consumer_key' ); ?>"><?php _e( 'Consumer Key' ) ?></label>
    <input type="text" value="<?php echo !empty( $data[ $this->get_field_name( 'twitter_consumer_key' ) ] )?$data[ $this->get_field_name( 'twitter_consumer_key' ) ]:''; ?>" id="<?php echo $this->get_field_id( 'twitter_consumer_key' ); ?>" name="<?php echo $this->get_field_name( 'twitter_consumer_key' ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'twitter_consumer_secret' ); ?>"><?php _e( 'Consumer Secret' ) ?></label>
    <input type="text" value="<?php echo !empty( $data[ $this->get_field_name( 'twitter_consumer_secret' ) ] )?$data[ $this->get_field_name( 'twitter_consumer_secret' ) ]:''; ?>" id="<?php echo $this->get_field_id( 'twitter_consumer_secret' ); ?>" name="<?php echo $this->get_field_name( 'twitter_consumer_secret' ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'twitter_access_token' ); ?>"><?php _e( 'Access Token' ) ?></label>
    <input type="text" value="<?php echo !empty( $data[ $this->get_field_name( 'twitter_access_token' ) ] )?$data[ $this->get_field_name( 'twitter_access_token' ) ]:''; ?>" id="<?php echo $this->get_field_id( 'twitter_access_token' ); ?>" name="<?php echo $this->get_field_name( 'twitter_access_token' ); ?>" />
  </p>
  <p>
    <label for="<?php echo $this->get_field_id( 'twitter_access_token_secret' ); ?>"><?php _e( 'Access Token Secret' ) ?></label>
    <input type="text" value="<?php echo !empty( $data[ $this->get_field_name( 'twitter_access_token_secret' ) ] )?$data[ $this->get_field_name( 'twitter_access_token_secret' ) ]:''; ?>" id="<?php echo $this->get_field_id( 'twitter_access_token_secret' ); ?>" name="<?php echo $this->get_field_name( 'twitter_access_token_secret' ); ?>" />
  </p>
</fieldset>

<fieldset>
  <legend><h2>Youtube</h2></legend>
  <p>
    <label for="<?php echo $this->get_field_id( 'youtube_search_for' ); ?>"><?php _e( 'Search for' ) ?></label>
    <input type="text" value="<?php echo !empty( $data[ $this->get_field_name( 'youtube_search_for' ) ] )?$data[ $this->get_field_name( 'youtube_search_for' ) ]:''; ?>" id="<?php echo $this->get_field_id( 'youtube_search_for' ); ?>" name="<?php echo $this->get_field_name( 'youtube_search_for' ); ?>" />
    <small>e.g. username - Videos from a specific user name. #designchemical - Videos from a search. 'Playlist Title/8BCDD04DE8F771B2' - Videos from a playlist.</small>
  </p>
</fieldset>

<fieldset>
  <legend><h2>Facebook</h2></legend>
  <p>
    <label for="<?php echo $this->get_field_id( 'facebook_search_for' ); ?>"><?php _e( 'Search for' ) ?></label>
    <input type="text" value="<?php echo !empty( $data[ $this->get_field_name( 'facebook_search_for' ) ] )?$data[ $this->get_field_name( 'facebook_search_for' ) ]:''; ?>" id="<?php echo $this->get_field_id( 'facebook_search_for' ); ?>" name="<?php echo $this->get_field_name( 'facebook_search_for' ); ?>" />
    <small>e.g. 112372345237465 - Facebook page wall posts. 'Facebook Timeline/376995711728' - Facebook page gallery images (Enter the text you would like to show for the facebook gallery name followed by "/" followed by the page gallery ID).</small>
  </p>
</fieldset>