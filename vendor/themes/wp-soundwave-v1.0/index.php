<?php get_header(); ?>
 
        <!--BEGIN LEFT-->
        <div id="left">
          <div id="leftin">
            <?php if (have_posts()) : while (have_posts()) : the_post();?>
          	  <h4><?php the_title(); ?></h4>
              <p><?php the_content('Continue Reading...'); ?></p>
              <div class="newsdiv"></div>
            <?php endwhile; endif; ?>
          </div>
        </div>
        <!--END LEFT-->
        <?php get_sidebar(); ?>
        </div>
    		<!--END CONTENT-->
  		</div>
  		<!--END MAIN AREA-->
<?php get_footer(); ?>