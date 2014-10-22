<?php get_header(); ?>

  <header>
    <h1 class="logo"><a href="/">Coming Home Music Festival 2014</a></h1>

    <h2 class="lead-in">
      <span class="icon-logo2"></span>
      <span class="icon-logo1"></span>
    </h2>

  </header>

  <div class="page-content">
    <div class="container">

      <h3>Blogs</h3>

      <?php
      $categories = get_categories(array(
        'hierarchical' => 0
      ));

      // Select the category
      $selected_category_id = get_query_var( 'cat' );
      ?>

      <nav class="blog_navigation_header">
        <a class="spectacle_navigation_tab <?php if ($selected_category_id == ''): echo ' spectacle_navigation_tab_active '; endif; ?>" href="<?php echo get_permalink( get_option( 'page_for_posts' ) ); ?>">
          All
        </a>

        <?php for ($i = 0, $mi = count( $categories ); $i < $mi; $i++ ): ?>

          <a class="spectacle_navigation_tab <?php if ( (int)$categories[$i]->cat_ID === $selected_category_id): echo ' spectacle_navigation_tab_active '; endif; ?>" href="<?php echo get_category_link( $categories[ $i ]->cat_ID ); ?>">
            <?php echo $categories[ $i ]->name; ?>
          </a>

        <?php endfor; ?>
      </nav>

      <?php

      // Custom post query to disable pagination
      $the_query = new WP_Query(array(
        'nopaging' => true,
        'cat' => $selected_category_id
      ));

      if ( $the_query->have_posts() ): ?>

        <div class="row blog-posts">

          <?php while ( $the_query->have_posts() ): $the_query->the_post(); ?>
            <div class="col-xs-12 col-sm-6 col-md-4">
              <a href="<?php the_permalink(); ?>" class="card">

                <?php

                if ( has_post_thumbnail() )
                {
                  $image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail-size', true);
                  $image_url = $image_url[0];
                }

                ?>

                <span class="photo" style="background-image:url('<?php echo $image_url; ?>');"></span>

                <span class="spectacle-category icon-spectacle-cat-1"></span>

                <h4 class="title"><?php the_title(); ?></h4>
                <span class="excerpt"><?php echo wp_strip_all_tags(get_the_excerpt()); ?></span>
                <hr class="divider">

          <span class="meta clearfix">
            <span class="date">
              <i class="icon-spectacle-time"></i>
              <span><?php echo get_the_time('D, M d, Y'); ?></span>
            </span>

            <span class="comments">
              <i class="icon-spectacle-comment"></i>
              <span><?php comments_number( 'no comments', '1 comment', '% comments' ); ?></span>
            </span>
          </span>
              </a>
            </div>
          <?php endwhile; ?>

        </div>

      <?php endif; ?>

    </div>
  </div>


<?php get_footer(); ?>