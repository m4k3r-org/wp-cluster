<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #page div elements.
 *
 * @author Usability Dynamics
 * @module festival  
 * @since festival 0.1.0
 */
?>
    </div> <!-- /container-wrap -->
    <footer>
      <div class="container">
        <div class="row">
          <div class="col-md-8 col-sm-8">
            <section class="logo">
              <img class="img-responsive" src="<?php echo get_template_directory_uri(); ?>/images/temp/logo-footer.png" alt="" />
            </section>
          </div>
          <div class="col-md-4 col-sm-4">
            <?php get_template_part( 'templates/aside/social', get_post_type() ); ?>
          </div>
        </div>
        <div class="row">
          <div class="col-md-8 col-sm-8">
            <?php get_template_part( 'templates/nav/footer', get_post_type() ); ?>
          </div>
          <div class="col-md-4 col-sm-4">
            <p>&copy; 2013 By MMF. All Rights Reserved.</p>
          </div>
        </div>
      </div>
    </footer>
    <?php wp_footer(); ?>
  </body>
</html>