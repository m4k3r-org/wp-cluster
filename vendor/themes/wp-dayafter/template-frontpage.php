<?php /* Template Name: Frontpage */ ?>


<?php get_header(); ?>
<div id="content">
  <h2 class="page-title"><span>VIDEO TRAILER</span></h2>
  <iframe width="531" height="398" src="//www.youtube.com/embed/oWh0iPj6MiI?rel=0" frameborder="0" allowfullscreen></iframe>
  
  <h2 class="page-title home-news"><span>FESTIVAL BLOG</span></h2>
  
  <?php
  // static front page paged parameter handling. 
  if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
  elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
  else { $paged = 1; }
  ?>
  
  <?php $args = array( 'post_type' => 'post', 'posts_per_page' => 10, 'paged' => $paged ); ?>
  <?php $wp_query = new WP_Query($args); ?>
  <?php while ( have_posts() ) : the_post(); ?>
     
	  <?php $c++;
    if( !$paged && $c == 1 || $c == 1) :?>
    
      <article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
      	<h3 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
      	<p class="post-date"><?php the_time(get_option('date_format')); ?></p>
      	<?php
          if ( has_post_thumbnail() ) {
      		  echo '<p>';
            the_post_thumbnail();
            echo '</p>';
          }
        ?>
        <div class="id_share_buttons">
          <div class="id_share_tw"><a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php the_permalink(); ?>" data-text="<?php the_title(); ?>" data-related="TDAPanama" data-hashtags="TDA14">Tweet</a></div>
          <div class="id_share_fb"><div class="fb-like" data-href="<?php the_permalink(); ?>" data-width="400" data-layout="button_count" data-show-faces="false" data-send="false"></div></div>
          <div class="clearfix"> </div>
        </div>
        <?php the_content(); ?>
      </article>
      
    <?php else :?>
      
      <article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
      	<h3 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <p class="post-date"><?php the_time(get_option('date_format')); ?></p>
      	<?php the_excerpt(); ?>
      </article>
      
    <?php endif;?>
		
	<?php endwhile; ?>
	
	<?php tfg_pagination(); ?>

</div><!-- end #content -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>