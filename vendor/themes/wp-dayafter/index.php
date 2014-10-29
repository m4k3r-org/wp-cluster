<?php get_header(); ?>
<div id="content">
<h2 class="page-title"><span>RECENT NEWS</span></h2>
	<?php if(have_posts()) : while (have_posts()) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
			<h3 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			<?php the_content(); ?>
		</article>
	<?php endwhile; else: ?>
		<p>There are currently no news posts.</p>
	<?php endif; ?>
	<?php tfg_pagination(); ?>
</div><!-- end #content -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>