<?php
/**
 * Infinite Loop
 * Contains:
 * - Social Stream
 * - Blog Updates
 * - Festival News
 *
 * MUST BE ADDED TO BOTTOM OF PAGE
 *
 * @author Usability Dynamics
 * @module festival
 * @since festival 0.1.0
 */

?>
<div class="container bottom-content block">

  <div class="head">

    <div id="bottom-headings" class="row">

      <div class="col-md-3 col-sm-3 social-stream">
        <div class="heading">
          <h3><?php _e( 'Social Stream', wp_festival( 'domain' ) ); ?></h3>
          <span class="hr"></span>
        </div>
      </div>

      <div class="col-md-3 col-sm-3 blog-updates">
        <div class="heading">
          <h3><?php _e( 'Blog Updates', wp_festival( 'domain' ) ); ?></h3>
          <span class="hr"></span>
        </div>
      </div>

      <div class="col-md-6 col-sm-6 festival-news">
        <div class="heading">
          <h3><?php _e( 'Festival News', wp_festival( 'domain' ) ); ?></h3>
          <span class="hr"></span>
        </div>
      </div>

    </div>

    <div class="bottom-line"></div>

  </div>

  <div class="container">

    <div class="row">

      <div class="col-md-3 col-sm-3 social-stream">
        <?php get_template_part( 'templates/aside/social-stream', get_post_type() ); ?>
      </div>

      <div class="col-md-3 col-sm-3 blog-updates">
        <?php get_template_part( 'templates/aside/blog-updates', get_post_type() ); ?>
      </div>

      <div class="col-md-6 col-sm-6 festival-news">
        <?php get_template_part( 'templates/aside/festival-news', get_post_type() ); ?>
      </div>

    </div>
  </div>
</div>