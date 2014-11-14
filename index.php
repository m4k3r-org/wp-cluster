<?php get_header(); ?>

  <header>
    <section class="presenter-logos">
      <img src="<?php echo get_stylesheet_directory_uri(); ?>/static/images/presenter-logos.png" alt="Disco Donnie Presents ultimo">
    </section>

    <h1 class="main-logo">
      <a href="/">
        <img src="<?php echo get_stylesheet_directory_uri(); ?>/static/images/logo.png" alt="Monster Block Party">
      </a>
    </h1>

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

            <?php

            if( has_post_thumbnail() ){
              $image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail-size', true );
              $image_url = $image_url[ 0 ];
            }

            $share_count = do_shortcode( '[social_share_count total="true" url="' . get_the_permalink() . '"]' );
            $share_count = json_decode( $share_count, true );

            ?>

          <div class="col-xs-12 col-sm-6 col-md-4">

            <div class="flip-container">
              <div class="flipper">
                <div class="front">

                  <a href="<?php the_permalink(); ?>" class="card">
                    <span class="photo" style="background-image:url('<?php echo $image_url; ?>');"></span>

                    <h4 class="title"><?php the_title(); ?></h4>
                    <hr class="divider">

                <span class="meta clearfix">
                  <span class="date">
                    <i class="icon-spectacle-time"></i>
                    <span><?php echo get_the_time( 'D, M d, Y' ); ?></span>
                  </span>

                  <span class="comments">
                    <i class="icon-spectacle-comment"></i>
                    <span><?php comments_number( 'no comments', '1 comment', '% comments' ); ?></span>
                  </span>
                </span>
                  </a>

                  <a href="#" class="share news-single-share"><i class="icon-spectacle-share"></i></a>
                </div>

                <div class="back">
                  <div>
                    <a href="<?php the_permalink(); ?>" class="card">
                      <span class="photo" style="background-image:url('<?php echo $image_url; ?>');"></span>

                      <h4 class="title"><?php the_title(); ?></h4>
                      <hr class="divider">

                  <span class="meta clearfix">
                    <span class="date">
                      <i class="icon-spectacle-time"></i>
                      <span><?php echo get_the_time( 'D, M d, Y' ); ?></span>
                    </span>

                    <span class="comments">
                      <i class="icon-spectacle-comment"></i>
                      <span><?php comments_number( 'no comments', '1 comment', '% comments' ); ?></span>
                    </span>
                  </span>
                    </a>
                  </div>

                  <div class="social-share-overlay">
                    <div class="social-share-overlay-content">

                      <div class="share-wrapper clearfix">
                        <a href="https://twitter.com/intent/tweet?original_referer=<?php echo get_permalink(); ?>&text=<?php echo get_the_title(); ?>&url=<?php echo get_permalink(); ?>" target="_blank" class="twitter">
                          <span class="icon-spectacle-twitter"></span>

                          <em><?php if( empty( $share_count[ 'twitter' ] ) ) $share_count[ 'twitter' ] = 0;
                            echo $share_count[ 'twitter' ] ?></em>
                        </a>

                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo get_permalink(); ?>" target="_blank" class="facebook">
                          <span class="icon-spectacle-facebook"></span>

                          <em><?php if( empty( $share_count[ 'facebook' ] ) ) $share_count[ 'facebook' ] = 0;
                            echo $share_count[ 'facebook' ] ?></em>
                        </a>

                        <a href="https://plus.google.com/share?url=<?php echo get_permalink(); ?>" target="_blank" class="google-plus">
                          <span class="icon-spectacle-google-plus"></span>

                          <em><?php if( empty( $share_count[ 'google_plus' ] ) ) $share_count[ 'google_plus' ] = 0;
                            echo $share_count[ 'google_plus' ] ?></em>
                        </a>

                        <a href="http://pinterest.com/pin/create/button/?url=<?php echo get_permalink(); ?>&media=<?php echo $image_url; ?>&description=<?php echo get_the_title(); ?>" target="_blank" class="pinterest">
                          <span class="icon-spectacle-pinterest"></span>

                          <em><?php if( empty( $share_count[ 'pinterest' ] ) ) $share_count[ 'pinterest' ] = 0;
                            echo $share_count[ 'pinterest' ] ?></em>
                        </a>
                      </div>

                    </div>
                    <a href="#" class="share-close"><i class="icon-spectacle-close"></i></a>
                  </div>

                </div>
              </div>

            </div>

           </div>

          <?php endwhile; ?>

        </div>

      <?php endif; ?>

    </div>
  </div>


<?php get_footer(); ?>