<?php
/**
 *
 * The right sidebar has a "col-md-push-4" class.
 * "main" elemnet
 */
?>

<?php get_template_part( 'templates/header' ); ?>

<main id="main" class="main" role="main">
  <?php wp_festival()->section( 'above-content' ); ?>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="container">
          <div class="row">

              <?php wp_festival()->section( 'left-sidebar' ); ?>
              <?php wp_festival()->section( 'right-sidebar' ); ?>

              <?php if( have_posts() ) : ?>
                <div <?php echo post_class(); ?>>
                  <?php get_template_part( 'templates/aside/title' ); ?>
                  <section id="content" class="container-inner">
                    <?php while( have_posts() ) : the_post(); ?>
                      <?php get_template_part( 'templates/article/content', wp_festival()->get_query_template() ); ?>
                    <?php endwhile; ?>
                    <?php wp_festival()->page_navigation(); ?>
                  </section>
                </div>
              <?php endif; ?>

            </div>
        </div>
      </div>
    </div>
  </div>
  <?php wp_festival()->section( 'below-content' ); ?>
</main>

<?php get_template_part( 'templates/footer' ); ?>