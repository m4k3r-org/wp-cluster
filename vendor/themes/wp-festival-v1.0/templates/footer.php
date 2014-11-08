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

global $post;
/* Determine if 'footer' section has been disabled for the current page */
$disabled_sections = get_post_meta( $post->ID, 'disabledSections' );

?>
    <?php if( empty( $disabled_sections ) || !in_array( 'footer', $disabled_sections ) ) : ?>
    <footer class="footer">
      <?php wp_festival()->section( 'footer' ); ?>
    </footer>
    <?php endif; ?>
    <?php wp_footer(); ?>
  </body>
</html>