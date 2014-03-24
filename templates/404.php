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

        <img class="splash-img img-responsive" src="<?php echo get_stylesheet_directory_uri().'/images/404.png' ?>" alt="404" />

        <h2>Sorry, but the requested page was not found.</h2>

        <div class="text-center">
        <?php echo do_shortcode('[styled_button class="button-404" url="/" anchor="BACK TO HOMEPAGE"]'); ?>
        </div>
      </div>
    </div>
  </div>
  <?php wp_festival()->section( 'below-content' ); ?>
</main>

<?php get_template_part( 'templates/footer' ); ?>