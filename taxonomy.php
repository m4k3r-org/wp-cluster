<?php
/**
 * Template for custom taxonomies, categories will use archive.php
 *
 * Taxonomies may be related to different post types.
 *
 * @version 3.0.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
*/

  //** Bail out if page is being loaded directly and flawless_theme does not exist */
  if(!function_exists('get_header')) {
    die();
  }

  //** Get ID of the term */
  $taxonomy = get_taxonomy( get_queried_object()->taxonomy );
  $term = get_queried_object();


  //** Get all content types that use this taxonomy, and get their content coutns */
  foreach($flawless['post_types'] as $post_type => $post_data) {

    $this_query = array( 'post_type' => $post_type, 'numberposts' => -1, $taxonomy->name => $term->slug );
    if($have_content = get_posts($this_query)) {
      $found_content[$post_type] = $this_query;
    }
  }

?>

<?php get_header( 'taxonomy' ) ?>

<?php get_template_part('attention', 'taxonomy'); ?>

<div class="<?php flawless_wrapper_class(); ?>">

  <?php flawless_widget_area('left_sidebar'); ?>

  <div class="<?php flawless_block_class( 'main cfct-block' ); ?>">
    <div class="<?php flawless_module_class( 'taxonomy-archive' ); ?>">

      <?php do_action( 'flawless_ui::above_header' ); ?>

      <header class="entry-title-wrapper">
        <?php flawless_breadcrumbs(); ?>
        <?php flawless_page_title(); ?>

        <?php if( term_description() != '' ) { ?>
          <div class="category_description taxonomy">
            <?php echo get_term_attachment_image(); ?>
            <?php echo do_shortcode( term_description() ); ?>
          </div>
        <?php } ?>
      </header>

      <div class="loop loop-blog post-listing clearfix"><?php foreach( (array) $found_content as $post_type => $this_query) { query_posts($this_query); ?>
      <?php get_template_part( 'loop', 'blog' ); ?>
      <?php } ?></div>

    </div> <?php /* .archive-hentry */ ?>

  </div> <?php /* .main.cfct-block */ ?>

  <?php flawless_widget_area('right_sidebar'); ?>

</div> <!-- #content -->

<?php get_footer(); ?>
