<?php global $post; ?>
<!-- Social Stream Module -->
<div class="social-stream"
     data-requires="socialstream"
     data-path="<?php echo get_stylesheet_directory_uri() ?>"
     data-callback="<?php echo admin_url('admin-ajax.php?action=social_stream_twitter&module_id='.$data['module_id'].'&post_id='.$post->ID); ?>"
     data-wall="<?php echo $data[$this->get_field_name( 'wall' )]; ?>"
     data-rotate_delay="<?php echo $data[$this->get_field_name( 'rotate_delay' )]; ?>"
     data-rotate_direction="<?php echo $data[$this->get_field_name( 'rotate_direction' )]; ?>"
     data-height="<?php echo $data[$this->get_field_name( 'height' )]; ?>"
     data-limit="<?php echo $data[$this->get_field_name( 'limit' )]; ?>"

     data-twitter_search_for="<?php echo $data[$this->get_field_name('twitter_search_for')]; ?>"
     data-twitter_show_text="<?php echo $data[$this->get_field_name('twitter_show_text')]; ?>"

     data-instagram_search_for="<?php echo $data[$this->get_field_name('instagram_search_for')]; ?>"
     data-instagram_client_id="<?php echo $data[$this->get_field_name('instagram_client_id')]; ?>"
     data-instagram_access_token="<?php echo $data[$this->get_field_name('instagram_access_token')]; ?>"
     data-instagram_redirect_url="<?php echo $data[$this->get_field_name('instagram_redirect_url')]; ?>"

     data-youtube_search_for="<?php echo $data[$this->get_field_name('youtube_search_for')]; ?>"

     data-facebook_search_for="<?php echo $data[$this->get_field_name('facebook_search_for')]; ?>"></div>
<!-- #Social Stream Module -->