<?php get_header(); ?>

<!--BEGIN LEFT-->
<div id="left">
	<div id="leftin">

    <?php
    // static front page paged parameter handling. 
    if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
    elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
    else { $paged = 1; }
    ?>
    
    <?php $args = array( 'post_type' => 'post', 'posts_per_page' => 50, 'paged' => $paged ); ?>
    <?php $wp_query = new WP_Query($args); ?>
    <?php while ( have_posts() ) : the_post(); ?>
       
      <?php $c++;
      if( !$paged && $c == 1 || $c == 1) :?>
      
        <article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
          <?php if ( has_post_thumbnail() ) { ?><p><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('full'); ?></a></p><?php } ?>
          <h3 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        	<p class="post-date"><?php the_time(get_option('date_format')); ?></p>
          <div class="id_share_buttons">
            <div class="id_share_tw"><a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php the_permalink(); ?>" data-text="<?php the_title(); ?>" data-related="SoundWaveAZ" data-hashtags="SoundWave">Tweet</a></div>
            <div class="id_share_fb"><div class="fb-like" data-href="<?php the_permalink(); ?>" data-send="false" data-layout="button_count" data-width="400" data-show-faces="false"></div></div>
            <div class="clearfix"> </div>
          </div>
          <?php the_content(); ?>
        </article>
        <hr />
        
      <?php else :?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
        	<h3 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
          <p class="post-date"><?php the_time(get_option('date_format')); ?></p>
        	<?php the_excerpt(); ?>
        </article>
        
      <?php endif;?>
    	
    <?php endwhile; ?>


	</div>
</div>
<!--END LEFT-->

<?php get_sidebar(); ?>
</div>
<!--END CONTENT-->
</div>
<!--END MAIN AREA-->
<?php get_footer(); ?>
