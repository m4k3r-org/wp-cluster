<?php get_header(); ?>
<div id="content">
	<h2 class="page-title"><span>404 Error - Page Not Found</span></h2>
	<p>The page you were looking for (<em><?php echo current_url(); ?></em>) could not be found.</p>
	<p><strong><a href="<?php bloginfo('url'); ?>">&laquo; Return to the Homepage</a></strong></p>
</div><!-- end #content -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>