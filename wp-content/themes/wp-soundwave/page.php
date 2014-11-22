<?php get_header(); ?>

<!--BEGIN LEFT-->
<div id="left">
	<div id="leftin">
    <?php if(have_posts()) : while (have_posts()) : the_post(); ?>	
  	  <? if ( !is_home() || !is_front_page() ) { ?>
    	  <div class="id_share_buttons">
          <div class="id_share_tw"><a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php the_permalink(); ?>" data-text="<?php the_title(); ?>" data-related="OfficialSCMF" data-hashtags="SCMF2013">Tweet</a></div>
          <div class="id_share_fb"><div class="fb-like" data-href="<?php the_permalink(); ?>" data-send="false" data-layout="button_count" data-width="400" data-show-faces="false"></div></div>
          <div class="clearfix"> </div>
        </div>
  	  <?php } ?>
  		<?php the_content(); ?>
  	<?php endwhile; ?>
    <?php endif; ?>
	</div>
</div>
<!--END LEFT-->

<?php get_sidebar(); ?>
</div>
<!--END CONTENT-->
</div>
<!--END MAIN AREA-->
<?php get_footer(); ?>
