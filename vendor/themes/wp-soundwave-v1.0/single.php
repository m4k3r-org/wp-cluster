<?php get_header(); ?>
 
        <!--BEGIN LEFT-->
        <div id="left">
            <?php if (have_posts()) : while (have_posts()) : the_post();?>
            <div id="leftin">
              <?php
                if ( has_post_thumbnail() ) {
                  echo '<p>';
                  the_post_thumbnail('full');
                  echo '</p>';
                }
              ?>
              <article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
              <h3 class="post-title"><?php the_title(); ?></h3>
            	<p class="post-date"><?php the_time(get_option('date_format')); ?></p>
              <div class="id_share_buttons">
                <div class="id_share_tw"><a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php the_permalink(); ?>" data-text="<?php the_title(); ?>" data-related="SoundWaveAZ" data-hashtags="SoundWave">Tweet</a></div>
                <div class="id_share_fb"><div class="fb-like" data-href="<?php the_permalink(); ?>" data-send="false" data-layout="button_count" data-width="400" data-show-faces="false"></div></div>
                <div class="clearfix"> </div>
              </div>
              <?php the_content(); ?>
            </article>
            <?php endwhile; endif; ?>
            
            <hr />
            
            <h3 class="post-title">Comments</h3>
            <div id="box_facebook_comments"><div class="fb-comments" data-href="<?php the_permalink(); ?>" data-width="466"></div></div>
          </div>
        </div>
        <!--END LEFT-->
        
        <?php get_sidebar(); ?>
        
			</div>
      <!--END CONTENT-->
  </div>
  <!--END MAIN AREA-->
<?php get_footer(); ?>