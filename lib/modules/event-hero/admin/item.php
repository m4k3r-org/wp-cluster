<li class="carousel-post-item">
  <a class="carousel-post-item-ident carousel-item-title" href="#carousel-post-<?php echo intval( $postdata[ 'id' ] ); ?>"><?php echo esc_html( $postdata[ 'title' ] ); ?></a>
  <div class="carousel-edit-form">
    <input type="hidden" name="<?php echo $this->get_field_name( 'posts' ); ?>[<?php echo $postdata[ 'id' ]; ?>][id]" value="<?php echo intval( $postdata[ 'id' ] ); ?>" />
    <label><?php _e( 'Title', wp_festival( 'domain' ) ); ?></label>
    <input type="text" class="text" name="<?php echo $this->get_field_name( 'posts' ); ?>[<?php echo $postdata[ 'id' ]; ?>][title]" value="<?php echo esc_attr( $postdata[ 'title' ] ); ?>" />
    <label><?php _e( 'Description', wp_festival( 'domain' ) ); ?></label>
    <textarea name="<?php echo $this->get_field_name( 'posts' ); ?>[<?php echo $postdata[ 'id' ]; ?>][content]"><?php echo esc_textarea( $postdata[ 'content' ] ); ?></textarea>
    <input type="button" name="done" class="button carousel-edit-done" value="Done" />
    <span class="carousel-edit-remove trash"><a href="#remove" class="lnk-remove"><?php _e( 'Remove', wp_festival( 'domain' ) ); ?></a></span>
  </div>
</li>