<li class="event-post-item">
  
  <a class="event-post-item-ident event-item-title" href="#event-post-<?php echo intval( $postdata[ 'id' ] ); ?>"><?php echo esc_html( $postdata[ 'post_title' ] ); ?></a>
  
  <div class="event-edit-form">
    
    <input type="hidden" name="<?php echo $this->get_field_name( 'posts' ); ?>[<?php echo $postdata[ 'id' ]; ?>][id]" value="<?php echo intval( $postdata[ 'id' ] ); ?>" />
    
    <ul>
      <li>
        <label><?php _e( 'Title:', wp_festival( 'domain' ) ); ?></label>
        <input type="text" class="text" name="<?php echo $this->get_field_name( 'posts' ); ?>[<?php echo $postdata[ 'id' ]; ?>][post_title]" value="<?php echo esc_attr( $postdata[ 'post_title' ] ); ?>" />
      </li>
      <li>
        <label><?php _e( 'Description:', wp_festival( 'domain' ) ); ?></label>
        <textarea name="<?php echo $this->get_field_name( 'posts' ); ?>[<?php echo $postdata[ 'id' ]; ?>][post_excerpt]"><?php echo esc_textarea( $postdata[ 'post_excerpt' ] ); ?></textarea>
      </li>
      <li>
      <?php if( !empty( $postdata[ 'relatedArtists' ] ) ) : ?>
      <li>
        <label><?php _e( 'Artists:', wp_festival( 'domain' ) ); ?></label>
        <ul>
          <?php foreach( $postdata[ 'relatedArtists' ] as $artist_ID ) : $artist = wp_festival()->get_post_data( $artist_ID ); ?>
            <?php if( !$artist ) continue; ?>
            <li>
              <label>
                <input 
                  type="checkbox" 
                  name="<?php echo $this->get_field_name( 'posts' ); ?>[<?php echo $postdata[ 'id' ]; ?>][enabledArtists][]" 
                  value="<?php echo $artist_ID; ?>" <?php echo in_array( $artist_ID, $postdata[ 'enabledArtists' ] ) ? 'checked="checked"' : ''; ?> />
                <?php echo $artist[ 'post_title' ]; ?>
              </label>
            </li>
          <?php endforeach; ?>
        </ul>
      </li>
      <?php endif; ?>
      <li>
        <span class="event-edit-remove trash"><a href="#remove" class="lnk-remove"><?php _e( 'Remove Event', wp_festival( 'domain' ) ); ?></a></span>
      </li>
    </ul>
    
  </div>
  
</li>