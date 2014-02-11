<?php
/**
 * Template for home page which may be static or include latest posts.
 *
 *
 *
 * @version 0.60.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package WP-Disco
 */
?>
<?php get_template_part( 'templates/header', 'index' ); ?>

<section id="body-content" class="frame">
  <?php get_template_part( 'templates/aside/attention', 'home' ); ?>

  <div class="<?php wp_disco()->wrapper_class( ); ?>">
    <?php wp_disco()->widget_area( 'left_sidebar' ); ?>
    <?php get_template_part( 'templates/article/loop', 'home' ); ?>
    <?php wp_disco()->widget_area( 'right_sidebar' ); ?>
  </div>

</section>

<?php get_template_part( 'templates/header', 'index' ); ?>
