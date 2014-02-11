<?php
/**
 * Template for standard single posts.
 *
 *
 *
 * @version 3.0.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package WP-Disco
 */
die('single');
?>

<?php get_template_part( 'templates/header', get_post_type() ); ?>

<section id="body-content" class="frame">
<?php get_template_part( 'templates/aside/attention', get_post_type() ); ?>

  <div class="<?php wp_disco()->wrapper_class(); ?>">
    <?php wp_disco()->widget_area( 'left_sidebar' ); ?>
    <?php get_template_part( 'templates/article/single', get_post_type() ); ?>
    <?php wp_disco()->widget_area( 'right_sidebar' ); ?>
  </div>

</section>

<?php get_template_part( 'templates/footer', get_post_type() ); ?>