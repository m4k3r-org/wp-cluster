<?php
/**
 * Property Default Template for Single Property View
 *
 *
 * @todo add get_attribute('property_type_label', array('allow_multiple_values' =>false )); back to title. (removed when title moved to flawless_page_title() );
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
 */

  //** Bail out if page is being loaded directly and flawless_theme does not exist */
  if(!function_exists('get_header')) {
    die();
  }

?>

<?php get_header( 'property' ); ?>

<div class="<?php flawless_wrapper_class( 'property_content' ); ?>">

  <?php flawless_widget_area('left_sidebar'); ?>

  <div class="<?php flawless_block_class( 'main cfct-block' ); ?>">

  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <div id="post-<?php the_ID(); ?>" class="<?php flawless_module_class( 'property_page_post' ); ?>">

      <?php do_action( 'flawless_ui::above_header' ); ?>

      <header class="entry-title-wrapper property_title_wrapper">
        <?php flawless_breadcrumbs(); ?>
        <?php flawless_page_title(); ?>
        <h3 class="entry-subtitle"><?php the_tagline(); ?></h3>
      </header>

      <?php get_template_part( 'entry-meta', 'header' ); ?>

      <div class="entry-content clearfix">
        <div class="the_content">
          <?php the_content('More Info'); ?>
  
          <?php foreach( (array) $wp_properties['property_meta'] as $meta_slug => $meta_title):
            if(empty($post->$meta_slug) || $meta_slug == 'tagline')
              continue; ?>
            <h2><?php echo $meta_title; ?></h2>
            <p><?php echo  do_shortcode(html_entity_decode($post->$meta_slug)); ?></p>
          <?php endforeach; ?>
            
        </div>

        <div class="formatted-row row-fluid">
          <div class="span6">
            <div class="cfct-module">

              <?php if ( empty($wp_properties['property_groups']) || $wp_properties['configuration']['property_overview']['sort_stats_by_groups'] != 'true' ) : ?>
              <ul id="property_stats" class="property_stats overview_stats">
                <?php if(!empty($post->display_address)): ?>
                <li><span class="wpp_stat_dt_location"><?php echo $wp_properties['property_stats'][$wp_properties['configuration']['address_attribute']]; ?></span>
                <span class="wpp_stat_dd_location alt"><?php echo $post->display_address; ?>&nbsp;</span></li>
                <?php endif; ?>
                <?php @draw_stats("make_link=true&exclude={$wp_properties['configuration']['address_attribute']}"); ?>
              </ul>
            <?php else: ?>
              <?php if(!empty($post->display_address)): ?>
              <ul id="property_stats" class="property_stats overview_stats">
                <li><span class="wpp_stat_dt_location"><?php echo $wp_properties['property_stats'][$wp_properties['configuration']['address_attribute']]; ?></span>
                <span class="wpp_stat_dd_location alt"><?php echo $post->display_address; ?>&nbsp;</span></li>
              </ul>
              <?php endif; ?>
              <?php @draw_stats("make_link=true&exclude={$wp_properties['configuration']['address_attribute']}"); ?>
            <?php endif; ?>

          </div>
        </div>

        <?php if(!empty($wp_properties['taxonomies'])) { ?>
        <div class="span6">
          <div class="cfct-module">
          <?php foreach($wp_properties['taxonomies'] as $tax_slug => $tax_data): ?>
            <?php if(get_features("type={$tax_slug}&format=count")):  ?>
            <div class="wpp_feature_list <?php echo $tax_slug; ?>_list">
            <h2><?php echo $tax_data['label']; ?></h2>
            <ul class="clearfix">
            <?php get_features("type={$tax_slug}&format=list&links=true"); ?>
            </ul>
            </div>
            <?php endif; ?>
          <?php endforeach; ?>
          </div>
        </div>
        <?php } ?>

      </div>

        <?php if($post->post_parent): ?>
          <a href="<?php echo $post->parent_link; ?>"><?php _e('Return to building page.','wpp') ?></a>
        <?php endif; ?>

      </div><!-- .entry-content -->

      <?php get_template_part('content','single-property-map'); ?>

      <?php get_template_part('content','single-property-inquiry'); ?>

    </div> <!-- flawless_module_class() -->

    <?php endwhile; endif; ?>

    <?php get_template_part('content','single-property-bottom'); ?>

  </div> <!-- .main.cfct-block -->

  <?php flawless_widget_area('right_sidebar'); ?>

</div> <!-- #content -->

<?php get_footer( 'property' );  ?>
