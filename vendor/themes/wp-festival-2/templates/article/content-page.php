<?php
/**
 * The default template for displaying content. Used for both single and index/archive/search.
 *
 * @author Usability Dynamics
 * @module wp-festival
 * @since wp-festival 2.0.0
 */
?>
<section class="article-content" data-type="content" class="container">
  <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', wp_festival2( 'domain' ) ) ); ?>
</section>

