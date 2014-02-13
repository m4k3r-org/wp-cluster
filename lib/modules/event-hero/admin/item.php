<li class="event-post-item">
  
  <a class="event-post-item-ident event-item-title" href="#event-post-<?php echo intval( $postdata[ 'id' ] ); ?>"><?php echo esc_html( $postdata[ 'title' ] ); ?></a>
  
  <div class="event-edit-form">
    
    <input type="hidden" name="<?php echo $this->get_field_name( 'posts' ); ?>[<?php echo $postdata[ 'id' ]; ?>][id]" value="<?php echo intval( $postdata[ 'id' ] ); ?>" />
    
    <ul>
      <li>
        <label><?php _e( 'Title', wp_festival( 'domain' ) ); ?></label>
        <input type="text" class="text" name="<?php echo $this->get_field_name( 'posts' ); ?>[<?php echo $postdata[ 'id' ]; ?>][title]" value="<?php echo esc_attr( $postdata[ 'title' ] ); ?>" />
      </li>
      <li>
        <label><?php _e( 'Description', wp_festival( 'domain' ) ); ?></label>
        <textarea name="<?php echo $this->get_field_name( 'posts' ); ?>[<?php echo $postdata[ 'id' ]; ?>][content]"><?php echo esc_textarea( $postdata[ 'content' ] ); ?></textarea>
      </li>
      <li>
        <span class="event-edit-remove trash"><a href="#remove" class="lnk-remove"><?php _e( 'Remove current Event', wp_festival( 'domain' ) ); ?></a></span>
      </li>
    </ul>
    
  </div>
  
</li>