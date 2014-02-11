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
    </div><!-- /container-wrap -->
    <footer>

      <section class="container">
        <?php wp_festival()->aside( 'footer' ); ?>
      </section>

    </footer>
    <?php wp_footer(); ?>
  </body>
</html>