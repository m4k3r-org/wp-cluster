<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #page div elements.
 *
 * @todo Get rid of the closing </div> for #doc - footer should be be part of wrapper. -potanin@UD
 *
 * @author Usability Dynamics
 * @module wp-festival
 * @since wp-festival 2.0.0
 */

global $post;
/* Determine if 'footer' section has been disabled for the current page */
$disabled_sections = get_post_meta( $post->ID, 'disabledSections' ); ?>

      <div class="clearfix"></div>

      <?php if( empty( $disabled_sections ) || !in_array( 'footer', $disabled_sections ) ) : ?>
        <footer>
          <?php wp_festival2()->section( 'footer' ); ?>
        </footer>
      <?php endif; ?>

    </div> <!-- /#doc -->

    <?php get_template_part( 'templates/overlays/tickets', get_post_type() ); ?>
    <?php get_template_part( 'templates/overlays/nav', get_post_type() ); ?>
    <?php get_template_part( 'templates/overlays/account', get_post_type() ); ?>
    <?php get_template_part( 'templates/overlays/share', get_post_type() ); ?>
    <?php get_template_part( 'templates/overlays/imagelightbox', get_post_type() ); ?>
    
    <?php wp_footer(); ?>

    <?php if( is_front_page() ) : ?>
      <script src="<?php echo get_stylesheet_directory_uri(); ?>/static/scripts/src/wp-social-stream/wp-social-stream-ordering-home.js"></script>
    <?php endif; ?>

  </body>
</html>