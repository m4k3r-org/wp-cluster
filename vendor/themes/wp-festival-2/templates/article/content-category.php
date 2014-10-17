<?php
/**
 *  Category
 */

$share_count = do_shortcode('[social_share_count twitter="true" url="'. get_the_permalink() . '"]');
$share_count = json_decode( $share_count, true );

?>
<div class="post col-xs-12 col-md-6 col-lg-4">

  <div class="flip-container">
    <div class="flipper">

      <div class="front">

        <a href="<?php the_permalink(); ?>" class="content">

          <span class="img-wrap" style="background-image:url( '<?php echo wp_festival2()->get_image_link_by_post_id( get_the_ID(), array(
              'width' => '738',
              'height' => '415'
            ) ); ?>' );"></span>

          <span class="description">

            <span class="text">
              <h3 class="post_title"><?php echo wp_trim_words( get_the_title(), 8 ); ?></h3>
            </span>

            <hr class="divider">

            <span class="meta row">

              <span class="col-xs-12 col-sm-12 col-lg-7 date">
                <i class="icon-time"></i>
                <span><?php the_time( get_option( 'date_format' ) ); ?></span>
              </span>

              <span class="col-xs-12 col-sm-12 col-lg-5 comments-count">
                <i class="icon-comments"></i>
                <span>
                  <?php

                  // Need to do this, because anchor in anchors are not allowed.
                  // Comment link will be triggered with JS

                  $comment_nr = (int) get_comments_number( get_the_ID() );

                  if( $comment_nr === 1 ){
                    echo '1 comment';
                  } else if( $comment_nr > 1 ){
                    echo $comment_nr . ' comments';
                  } else{
                    echo 'No comments';
                  }

                  // comments_popup_link(__('No comments'), __('1 comment'), __('% comments'))
                  ?>
                </span>
              </span>
            </span>

            <?php $cats = get_the_category(); ?>
            <div class="category-icon">
              <?php if( function_exists( 'z_taxonomy_image_url' ) && !empty( z_taxonomy_image_url( $cats[ 0 ]->term_id ) ) )  : ?>
                <img src="<?php echo z_taxonomy_image_url( $cats[ 0 ]->term_id ); ?>">
              <?php endif; ?>
            </div>

          </span>
        </a>

        <a href="#" class="share news-single-share"><i class="icon-share"></i></a>

      </div>


      <div class="back">

        <a href="<?php the_permalink(); ?>" class="content">

          <span class="img-wrap" style="background-image:url( '<?php echo wp_festival2()->get_image_link_by_post_id( get_the_ID(), array(
              'width' => '738',
              'height' => '415'
            ) ); ?>' );"></span>

          <span class="description">

            <span class="text">
              <h3 class="post_title"><?php echo wp_trim_words( get_the_title(), 8 ); ?></h3>
            </span>

            <hr class="divider">

            <span class="meta row">

              <span class="col-xs-12 col-sm-12 col-lg-7 date">
                <i class="icon-time"></i>
                <span><?php the_time( get_option( 'date_format' ) ); ?></span>
              </span>

              <span class="col-xs-12 col-sm-12 col-lg-5">
                <i class="icon-comments"></i>
                <span>
                  <?php

                  // Need to do this, because anchor in anchors are not allowed.
                  // Comment link will be triggered with JS

                  $comment_nr = (int) get_comments_number( get_the_ID() );

                  if( $comment_nr === 1 ){
                    echo '1 comment';
                  } else if( $comment_nr > 1 ){
                    echo $comment_nr . ' comments';
                  } else{
                    echo 'No comments';
                  }

                  // comments_popup_link(__('No comments'), __('1 comment'), __('% comments'))
                  ?>
                </span>
              </span>
            </span>

            <?php $cats = get_the_category(); ?>
            <div class="category-icon">
              <?php if( function_exists( 'z_taxonomy_image_url' ) && !empty( z_taxonomy_image_url( $cats[ 0 ]->term_id ) ) )  : ?>
                <img src="<?php echo z_taxonomy_image_url( $cats[ 0 ]->term_id ); ?>">
              <?php endif; ?>
            </div>

          </span>
        </a>

        <a href="#" class="share news-single-share"><i class="icon-share"></i></a>

        <div class="social-share-overlay">

          <div class="social-share-overlay-content">

            <div class="share-wrapper clearfix">
              <a href="https://twitter.com/intent/tweet?original_referer=<?php echo get_permalink(); ?>&text=<?php echo get_the_title(); ?>&url=<?php echo get_permalink(); ?>" target="_blank" class="twitter">
                <span class="icon-twitter"></span>

                <em><?php if ( empty($share_count['twitter']) ) $share_count['twitter'] = 0; echo $share_count['twitter'] ?></em>
              </a>

              <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo get_permalink(); ?>" target="_blank" class="facebook">
                <span class="icon-facebook"></span>

                <em><?php if ( empty($share_count['facebook']) ) $share_count['facebook'] = 0; echo $share_count['facebook'] ?></em>
              </a>

              <a href="https://plus.google.com/share?url=<?php echo get_permalink(); ?>" target="_blank" class="google-plus">
                <span class="icon-google-plus"></span>

                <em><?php if ( empty($share_count['google_plus']) ) $share_count['google_plus'] = 0; echo $share_count['google_plus'] ?></em>
              </a>

              <a href="http://pinterest.com/pin/create/button/?url=<?php echo get_permalink(); ?>&media=<?php echo wp_festival2()->get_image_link_by_post_id( get_the_ID() ); ?>&description=<?php echo get_the_title(); ?>" target="_blank" class="pinterest">
                <span class="icon-pinterest"></span>

                <em><?php if ( empty($share_count['pinterest']) ) $share_count['pinterest'] = 0; echo $share_count['pinterest'] ?></em>
              </a>
            </div>

          </div>

          <a href="#" class="share-close"><i class="icon-close"></i></a>
        </div>

      </div>
    </div>

  </div>

</div>