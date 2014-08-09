<!-- Social Stream Module -->
<div 
  id="wp-social-stream-<?php echo rand(); ?>" 
  class="wp-social-stream"
  <?php // General options ?>
  data-requires="<?php echo $data[ 'requires' ]; ?>"
  data-path="<?php echo $data[ 'path' ] ?>"
  data-callback="<?php echo $data[ 'callback' ] ?>"
  data-wall="<?php echo $data[ 'wall' ] ?>"
  data-rotate_delay="<?php echo $data[ 'rotate_delay' ] ?>"
  data-rotate_direction="<?php echo $data[ 'rotate_direction' ] ?>"
  data-height="<?php echo $data[ 'height' ] ?>"
  data-limit="<?php echo $data[ 'limit' ] ?>"
  data-moderate="<?php echo $data[ 'moderate' ] ?>"
  data-remove="<?php echo $data[ 'remove' ] ?>"
  <?php // Twitter options ?>
  data-twitter_search_for="<?php echo $data[ 'twitter_search_for' ] ?>"
  data-twitter_show_text="<?php echo $data[ 'twitter_show_text' ] ?>"
  <?php // Instagram options ?>
  data-instagram_search_for="<?php echo $data[ 'instagram_search_for' ] ?>"
  data-instagram_client_id="<?php echo $data[ 'instagram_client_id' ] ?>"
  data-instagram_access_token="<?php echo $data[ 'instagram_access_token' ] ?>"
  data-instagram_redirect_url="<?php echo $data[ 'instagram_redirect_url' ] ?>"
  <?php // Youtube options ?>
  data-youtube_search_for="<?php echo $data[ 'youtube_search_for' ] ?>"
  <?php // Facebook options ?>
  data-facebook_search_for="<?php echo $data[ 'facebook_search_for' ] ?>"
</div>
<!-- #Social Stream Module -->