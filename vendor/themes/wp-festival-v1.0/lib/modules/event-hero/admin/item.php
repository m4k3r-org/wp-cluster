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
        <label><?php _e( 'Location:', wp_festival( 'domain' ) ); ?></label>
        <input type="text" class="text" name="<?php echo $this->get_field_name( 'posts' ); ?>[<?php echo $postdata[ 'id' ]; ?>][locationAddress]" value="<?php echo esc_attr( $postdata[ 'locationAddress' ] ); ?>" />
      </li>
      <li>
      <?php if( !empty( $postdata[ 'relatedArtists' ] ) && is_array( $postdata[ 'relatedArtists' ] ) ) : ?>
      <li>
        <label><?php _e( 'Artists:', wp_festival( 'domain' ) ); ?></label>
        
        <div class="artists-list div-wrapper clearfix">
          <ul>
            <li class="alt header">
              <span class="name"><?php _e( 'Name', wp_festival( 'domain' ) ); ?></span>
              <span class="order"><?php _e( 'Order', wp_festival( 'domain' ) ); ?></span>
              <div style="clear:both;"></div>
            </li>
            <?php foreach( $postdata[ 'relatedArtists' ] as $artist_ID ) : ?> 
              <?php $artist = wp_festival()->get_post_data( $artist_ID ); ?>
              <?php $alt = !isset( $alt ) ? '' : ( $alt === '' ? 'alt' : '' ); ?>
              <?php $checked = in_array( $artist_ID, (array)$postdata[ 'enabledArtists' ] ) ? 'checked="checked"' : ''; ?>
              <li class="<?php echo $alt; ?>">
                <input 
                  type="checkbox" 
                  name="<?php echo $this->get_field_name( 'posts' ); ?>[<?php echo $postdata[ 'id' ]; ?>][enabledArtists][]" 
                  id="artist-<?php echo $artist[ 'ID' ]; ?>" 
                  class="post-type-select" 
                  value="<?php echo $artist_ID; ?>" <?php echo $checked; ?> />
                <label for="artist-<?php echo $artist[ 'ID' ]; ?>"><?php echo $artist[ 'post_title' ]; ?></label>
                <input 
                  type="text" 
                  id="sorting-<?php echo $artist_ID; ?>" 
                  name="<?php echo $this->get_field_name( 'posts' ); ?>[<?php echo $postdata[ 'id' ]; ?>][sorting][<?php echo $artist_ID; ?>]" 
                  value="<?php echo esc_attr( isset( $postdata[ 'sorting' ][ $artist_ID ] )) ? $postdata[ 'sorting' ][ $artist_ID ] : ''; ?>" />
                <div style="clear:both;"></div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <!--
        <ul>
          <?php foreach( $postdata[ 'relatedArtists' ] as $artist_ID ) : $artist = wp_festival()->get_post_data( $artist_ID ); ?>
            <?php if( !$artist ) continue; ?>
            <li>
              <label>
                <input 
                  type="checkbox" 
                  name="<?php echo $this->get_field_name( 'posts' ); ?>[<?php echo $postdata[ 'id' ]; ?>][enabledArtists][]" 
                  value="<?php echo $artist_ID; ?>" <?php echo in_array( $artist_ID, (array)$postdata[ 'enabledArtists' ] ) ? 'checked="checked"' : ''; ?> />
                <?php echo $artist[ 'post_title' ]; ?>
              </label>
            </li>
          <?php endforeach; ?>
        </ul>
        -->
      </li>
      <?php endif; ?>
      <li>
        <span class="event-edit-remove trash"><a href="#remove" class="lnk-remove"><?php _e( 'Remove Event', wp_festival( 'domain' ) ); ?></a></span>
      </li>
    </ul>
    
  </div>
  
</li>