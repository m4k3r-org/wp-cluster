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

            <h1>404</h1>
            <p>The requested URL is this error was not found on this serve.</p>

          </div>
        </div>
      </div>
    </div>
  </div>
  <?php wp_festival()->section( 'below-content' ); ?>
</main>

<?php get_template_part( 'templates/footer' ); ?>