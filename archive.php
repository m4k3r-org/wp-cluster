<?php
/**
 * Template for archives and categories.
 *
 *
 *
 * @version 3.0.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
 */
?>

<?php get_template_part( 'templates/header', 'archive' ); ?>

<?php get_template_part( 'templates/aside/attention', 'archive' ); ?>

  <div class="<?php flawless_wrapper_class(); ?>">

  <?php flawless_widget_area( 'left_sidebar' ); ?>

    <div class="<?php flawless_block_class( 'main cfct-block' ); ?>">
    <div class="<?php flawless_module_class( 'archive-hentry' ); ?>">

      <?php do_action( 'flawless_ui::above_header' ); ?>

      <header class="entry-title-wrapper">
        <?php flawless_breadcrumbs(); ?>
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

    </div> <?php /* .archive-hentry */ ?>

  </div> <?php /* .main.cfct-block */ ?>

    <?php flawless_widget_area( 'right_sidebar' ); ?>

</div> <!-- #content -->

<?php get_template_part( 'templates/footer', 'archive' ); ?>