<?php
/**
 * Template Name: Features
 *
 * The template for displaying the Features page.
 *
 * @author Usability Dynamics
 * @module wp-festival
 * @since wp-festival 2.0.0
 */
?>

<?php get_template_part( 'templates/header' ); ?>

<?php get_template_part( 'templates/aside/header-image' ); ?>

<main id="main" class="main" role="main">

<div class="page-features">
	<section class="features-content">


  <?php wp_festival2()->section( 'above-content' ); ?>

  <div class="container-fluid">
    <div class="row">

        <?php wp_festival2()->section( 'left-sidebar' ); ?>
        <?php wp_festival2()->section( 'right-sidebar' ); ?>

        <?php if( have_posts() ) : ?>
          <div <?php if ( $post->post_type != 'artist' ) post_class(); ?>>
            <?php get_template_part( 'templates/aside/title' ); ?>
            <section id="content" class="container-inner">
              <?php while( have_posts() ) : the_post(); ?>
                <?php  get_template_part( 'templates/article/content-page');?>
              <?php endwhile; ?>
              <?php wp_festival2()->page_navigation(); ?>
            </section>
          </div>
        <?php endif; ?>

      </div>
  </div>

  <?php wp_festival2()->section( 'below-content' ); ?>
  
  </section>
</div>


</main>

<?php get_template_part( 'templates/footer' ); ?>