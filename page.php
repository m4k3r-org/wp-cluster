<?php
/**
 * Template for standard pages.
 *
 *
 * @version 0.60.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package WP-Disco
*/

  //** Bail out if page is being loaded directly and flawless_theme does not exist */
  if( !function_exists( 'get_header' ) ) {
    die();
  }

?>

<?php get_template_part( 'templates/header', 'page' ); ?>

<section id="body-content" class="frame">

  <?php get_template_part( 'templates/aside/attention', 'page' ); ?>

  <?php wp_disco()->widget_area( 'left_sidebar' ); ?>

  <section class="">

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

      <article id="post-<?php the_ID(); ?>" class="<?php wp_disco()->module_class(); ?>" data-object-id="<?php the_ID(); ?>">

        <header class="entry-title-wrapper">
          <?php wp_disco()->page_title(); ?>
        </header>

        <aside class="breadcrumbs">
          <?php wp_disco()->breadcrumbs(); ?>
        </aside>

        <?php get_template_part( 'templates/article/entry-meta', 'header' ); ?>

        <div class="entry-content clearfix">
          <?php the_content( 'More Info' ); ?>
        </div>

        <?php get_template_part( 'templates/article/comments', get_post_type() ); ?>
        <?php get_template_part( 'templates/article/entry-meta', 'footer' ); ?>

      </article>

    <?php endwhile; endif; ?>

  </section>

  <?php wp_disco()->widget_area( 'right_sidebar' ); ?>

</section>

<?php get_template_part( 'templates/footer', get_post_type() ); ?>