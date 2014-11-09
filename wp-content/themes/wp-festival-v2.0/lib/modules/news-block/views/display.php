<?php if ( !empty( $title) ): ?>
  <h2 id="main" class="latest-blog-posts container-fluid"><?php echo $title; ?></h2>
<?php endif; ?>
<?php if ( !empty( $description) ): ?>
  <p class="latest-blog-posts container-fluid"><?php echo $description; ?></p>
<?php endif; ?>
<section id="latest-blog-posts">
  <div class="posts posts-list-container posts-list-container-widget">
    <?php
    global $post;
    $my_backup = $post;
    $my_args = array(
      'post_type' => 'post',
      'post_status' => 'publish'
    );

    if( isset( $featured ) && $featured == '1' ){
      $my_args[ 'tag' ] = 'featured';
    }

    $my_query = new WP_Query( $my_args );
    if( $my_query->have_posts() ){
      $my_posts = $my_query->get_posts();
      foreach( $my_posts as $post ){
        setup_postdata( $post );
        get_template_part( 'templates/article/listing-post-featured' );
      }
      wp_reset_postdata();
    }
    $post = $my_backup;
    ?>
  </div>
  <div class="clearfix"></div>
  <div class="indicator-container">
    <div class="indicator-parent">
      <div class="indicator">
        <span class="icon-indicator"></span>
      </div>
    </div>
  </div>
</section>