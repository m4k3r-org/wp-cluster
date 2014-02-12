<?php
/**
 *
 * The right sidebar has a "col-md-push-4" class.
 * "main" elemnet
 */
?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="container">
        <div class="row">
          <?php wp_festival()->section( 'left-sidebar' ); ?>
          <?php wp_festival()->section( 'right-sidebar' ); ?>
          <?php get_template_part( 'templates/article/content' ); ?>
      </div>
    </div>
  </div>
</div>

<?php //get_template_part( 'templates/article/listing' ); ?>
<?php //get_template_part( 'templates/section/articles' ); ?>
<?php //get_template_part( 'templates/section/grid-artist' ); ?>
