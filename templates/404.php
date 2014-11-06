<?php
/**
 *
 * The right sidebar has a "col-md-push-4" class.
 * "main" elemnet
 */
?>

<?php get_template_part( 'templates/header' ); ?>


<header class="header-404"></header>

<main id="main" class="main" role="main">
  <?php wp_festival2()->section( 'above-content' ); ?>
  <div class="container container-404">
    <div class="row">
      <div class="col-xs-12">

        <h2><?php _e('Sorry, but the requested page was not found.', wp_festival2( 'domain' )); ?></h2>

				<?php echo do_shortcode('[styled_button class="button-404" url="/" anchor="BACK TO HOMEPAGE"]'); ?>
      </div>
    </div>
  </div>
  <?php wp_festival2()->section( 'below-content' ); ?>
</main>


<?php get_template_part( 'templates/footer' ); ?>