<p>
  <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title' ) ?></label>
  <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance[ 'title' ]; ?>" type="text" />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'categories' ); ?>"><?php _e( 'Filter by Category' ) ?></label>
  <?php wp_dropdown_categories( array(
    'name' => $this->get_field_name( 'categories' ),
    'selected' => $instance[ 'categories' ],
    'orderby' => 'Name',
    'hierarchical' => 1,
    'show_option_all' => 'All Categories',
    'hide_empty' => '0'
  ) ); ?>
  <?php
  $post_types = get_post_types();
  unset( $post_types[ 'page' ], $post_types[ 'attachment' ], $post_types[ 'revision' ], $post_types[ 'nav_menu_item' ] );
  ?>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'tags' ); ?>"><?php _e( 'Filter by Tag' ) ?></label>
  <?php wp_dropdown_categories( array(
    'name' => $this->get_field_name( 'tags' ),
    'selected' => $instance[ 'tags' ],
    'orderby' => 'Name',
    'hierarchical' => 1,
    'show_option_all' => 'All Tags',
    'hide_empty' => '0',
    'taxonomy' => 'post_tag'
  ) ); ?>
  <?php
  $post_types = get_post_types();
  unset( $post_types[ 'page' ], $post_types[ 'attachment' ], $post_types[ 'revision' ], $post_types[ 'nav_menu_item' ] );
  ?>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'categories' ); ?>"><?php _e( 'Filter by Post Type' ) ?></label>
  <select id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>" style="width:100%;">
    <?php
    foreach( $post_types as $post_type ){
      ?>
      <option value="<?php echo $post_type ?>" <?php selected( $post_type, $instance[ 'post_type' ], true ); ?>>
        <?php echo $post_type ?>
      </option>
    <?php } ?>
  </select>
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'slider_duration' ); ?>"><?php _e( 'Slider Duration - Length of time to change slides <em>(In milliseconds)</em>' ) ?></label>
  <input style="width: 40px;" id="<?php echo $this->get_field_id( 'slider_duration' ); ?>" name="<?php echo $this->get_field_name( 'slider_duration' ); ?>" value="<?php echo $instance[ 'slider_duration' ]; ?>" type="text" />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'slider_pause' ); ?>"><?php _e( 'Slider Pause - Length of time to pause on a slide <em>(In milliseconds)</em>' ) ?></label>
  <input style="width: 40px;" id="<?php echo $this->get_field_id( 'slider_pause' ); ?>" name="<?php echo $this->get_field_name( 'slider_pause' ); ?>" value="<?php echo $instance[ 'slider_pause' ]; ?>" type="text" />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'slider_count' ); ?>"><?php _e( 'Number of slides to display' ) ?></label>
  <input style="width: 40px;" id="<?php echo $this->get_field_id( 'slider_count' ); ?>" name="<?php echo $this->get_field_name( 'slider_count' ); ?>" value="<?php echo $instance[ 'slider_count' ]; ?>" type="text" />
</p>

<p style="display:none;">
  <label for="<?php echo $this->get_field_id( 'slider_height' ); ?>"><?php _e( 'Slider Height <em>(In pixels)</em>' ) ?></label>
  <input style="width: 40px;" id="<?php echo $this->get_field_id( 'slider_height' ); ?>" name="<?php echo $this->get_field_name( 'slider_height' ); ?>" value="<?php echo $instance[ 'slider_height' ]; ?>" type="text" />
</p>

<p>
  <label for="<?php echo $this->get_field_id( 'slider_animate' ); ?>"><?php _e( 'Slider Animation Style' ) ?></label>
  <select id="<?php echo $this->get_field_id( 'slider_animate' ); ?>" name="<?php echo $this->get_field_name( 'slider_animate' ); ?>" style="width:100%;">
    <option value="slide" <?php selected( 'slide', $instance[ 'slider_animate' ], true ); ?>>slide</option>
    <option value="fade" <?php selected( 'fade', $instance[ 'slider_animate' ], true ); ?>>fade</option>
  </select>
</p>