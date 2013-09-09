<?php
/**
 * Template for home page which may be static or include latest posts.
 *
 * @module Flawless
 * @submodule Template
 *
 * @version 0.60.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 */
?>

<?php get_template_part( 'templates/header', 'index' ); ?>

<section id="blog">

  <div class="hgroup">
    <h1>Fugiat dapibus, tellus ac cursus commodo, mauesris condime ntum nibh, ut fermentum mas justo sitters amet risus.</h1>
    <h2><i class="icon-time"></i> March 1, 2013 <a href="#post_comments"><i class="icon-comments-alt"></i> 6 comments</a></h2>
    <ul class="breadcrumb pull-right">
         <li><a href="index.html">Home</a> <span class="divider">/</span></li>
         <li><a href="blog.html">Blog</a></li>
    </ul>
  </div>

  <div class="<?php flawless_wrapper_class(); ?>">

    <?php flawless_widget_area( 'left_sidebar' ); ?>

    <div class="<?php flawless_block_class( 'main cfct-block' ); ?>">
      <?php get_template_part( 'templates/loop', 'home' ); ?>
      <?php get_template_part( 'templates/content', 'home-bottom' ); ?>
    </div>

    <?php flawless_widget_area( 'right_sidebar' ); ?>

  </div>

</section>

<?php get_template_part( 'templates/footer', 'index' ); ?>
