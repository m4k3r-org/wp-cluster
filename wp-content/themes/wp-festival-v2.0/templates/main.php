<?php
/**
 *
 * The right sidebar has a "col-md-push-4" class.
 * "main" elemnet
 */
?>

<?php get_template_part( 'templates/header' ); ?>

<?php get_template_part( 'templates/aside/header-image' ); ?>

<main id="main" class="main" role="main">

  <?php

  $pagename = get_query_var('pagename');
  wp_festival2()->section( 'above-content' ); ?>

  <div class="container-fluid">
    <div class="row">

        <?php wp_festival2()->section( 'left-sidebar' ); ?>
        <?php wp_festival2()->section( 'right-sidebar' ); ?>

        <?php if( have_posts() ) : ?>
          <div <?php if ( $post->post_type != 'artist' ) post_class(); ?>>
            <?php get_template_part( 'templates/aside/title' ); ?>
            <section id="content" class="container-inner">
              <?php while( have_posts() ) : the_post(); ?>
                <?php get_template_part( 'templates/article/content', wp_festival2()->get_query_template() ); ?>
              <?php endwhile; ?>
              <?php wp_festival2()->page_navigation(); ?>
            </section>
          </div>
        <?php endif; ?>

      </div>
  </div>

  <?php wp_festival2()->section( 'below-content' ); ?>

</main>

<?php get_template_part( 'templates/footer' ); ?>
