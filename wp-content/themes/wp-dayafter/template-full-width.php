<?php /* Template Name: Full Width */ ?>
<?php get_header(); ?>
<div id="content" class="full">
	<h2 class="page-title"><span><?php the_title(); ?></span></h2>
	<?php if(have_posts()) : while (have_posts()) : the_post(); ?>	
		<?php the_content(); ?>
	<?php endwhile; ?>
	<?php endif; ?>
</div><!-- end #content -->
<?php get_footer(); ?>