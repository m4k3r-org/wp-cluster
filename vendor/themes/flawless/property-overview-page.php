<?php
/**
 * The default page for property overview page.
 *
 * Used when no WordPress page is setup to display overview via shortcode.
 * Will be rendered as a 404 not-found, but still can display properties.
 *
 * @package WP-Property
 */
?>

<?php get_header(); ?>

<?php get_template_part('attention', 'property-overview'); ?>

<div class="<?php flawless_wrapper_class(); ?>">

  <?php flawless_widget_area('left_sidebar'); ?>

  <div class="<?php flawless_block_class( 'main cfct-block' ); ?>">

  <div id="post-0" class="<?php flawless_module_class( 'post error404 not-found' ); ?>">

      <header class="entry-title-wrapper">
        <?php flawless_breadcrumbs(); ?>
        <?php flawless_page_title(); ?>
      </header>
      
      <?php get_template_part( 'entry-meta', 'header' ); ?>

      <div class="entry-content clearfix">
        
        <?php if(is_404()): ?>
          <p><?php _e('Sorry, we could not find what you were looking for.  Since you are here, take a look at some of our properties.','wpp') ?></p>
        <?php endif; ?>
        
        <?php if($wp_properties['configuration']['do_not_override_search_result_page'] == 'true'): ?>            
          <?php echo $content = apply_filters('the_content', $post->post_content);  ?>
        <?php endif; ?>
        
        <?php echo WPP_Core::shortcode_property_overview(); ?>        
        
      </div>
      
      <?php get_template_part( 'entry-meta', 'footer' ); ?>
      
    </div> <!-- post_class() -->

    
  </div> <!-- .main cfct-block -->

  <?php flawless_widget_area('right_sidebar'); ?>

</div> <!-- #content --> 

<?php get_footer(); ?>
