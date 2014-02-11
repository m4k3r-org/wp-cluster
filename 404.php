<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @author Usability Dynamics
 * @module festival  
 * @since festival 0.1.0
 */
 
get_template_part( 'templates/header', get_post_type() );
?>
<section class="container">
  <div class="row">

    <div class="column col-md-12">

      <article class="article-404">

        <div class="alert alert-warning">
          <?php _e( 'Sorry, but the page you were trying to view does not exist.', wp_festival( 'domain' ) ); ?>
        </div>

        <p><?php _e( 'It looks like this was the result of either:', wp_festival( 'domain' ) ); ?></p>

        <ul>
          <li><?php _e( 'a mistyped address', wp_festival( 'domain' ) ); ?></li>
          <li><?php _e( 'an out-of-date link', wp_festival( 'domain' ) ); ?></li>
        </ul>

      </article>

    </div>

  </div>
</section>
<?php get_template_part( 'templates/footer', get_post_type() ); ?>