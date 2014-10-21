<?php
/**
 * Template for archives and categories.
 *
 *
 *
 * @version 3.0.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package WP-Disco
 */
?>

<?php get_template_part( 'templates/header', 'archive' ); ?>

<?php get_template_part( 'templates/aside/attention', 'archive' ); ?>

  <div class="<?php wp_disco()->wrapper_class( ); ?>">

  <?php wp_disco()->widget_area( 'left_sidebar' ); ?>

    <div class="<?php wp_disco()->block_class( 'main cfct-block' ); ?>">
    <div class="<?php wp_disco()->module_class( 'archive-hentry' ); ?>">

      <?php do_action( 'flawless_ui::above_header' ); ?>

      <header class="entry-title-wrapper">
        <?php wp_disco()->breadcrumbs(); ?>
        <?php
        /**
         * Commented out regarding ticket.
         *
         * @author korotkov@ud
         * @ticket https://ud-dev.com/projects/projects/discodonniepresentscom-november-2012/tasks/12 comment #2
         */
        //flawless_page_title();
        ?>

        <?php if( term_description() != '' ) { ?>
          <div class="category_description">
            <?php echo get_term_attachment_image(); ?>
            <?php echo do_shortcode( term_description() ); ?>
          </div>
        <?php } ?>
      </header>

      <div class="loop loop-blog post-listing clearfix">
      <?php get_template_part( 'templates/article/loop', 'blog' ); ?>
      </div>

    </div>

  </div>

  <?php wp_disco()->widget_area( 'right_sidebar' ); ?>

</div>

<?php get_template_part( 'templates/footer', 'archive' ); ?>