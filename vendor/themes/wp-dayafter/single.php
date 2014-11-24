<?php
	$tfg_page = array(
		'description' => tfg_excerpt()
	);
?>
<?php get_header(); ?>
<h2 class="page-title">FESTIVAL BLOG</h2>
<div id="content">
	
	<?php if(have_posts()) : while (have_posts()) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
			<h3 class="post-title"><?php the_title(); ?></h3>
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
      <? the_content(); ?>
		</article>
	<?php endwhile; ?>
	<?php endif; ?>
	<div class="fb-comments" data-href="<?php the_permalink(); ?>" data-colorscheme="dark" data-width="530"></div>
</div><!-- end #content -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>