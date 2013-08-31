<?php
/**
 * Property Default Template for Single Property View
 *
 *
 * @todo add get_attribute( 'property_type_label', array( 'allow_multiple_values' =>false ) ); back to title. ( removed when title moved to flawless_page_title() );
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
 */

  if( !function_exists( 'get_header' ) ) {
    die();
  }

?>

<?php get_template_part( 'templates/header',  'property' ); ?>

<div class="<?php flawless_wrapper_class( 'property_content' ); ?>">

  <?php flawless_widget_area( 'left_sidebar' ); ?>

  <div class="<?php flawless_block_class( 'main cfct-block' ); ?>">

  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <div id="post-<?php the_ID(); ?>" class="<?php flawless_module_class( 'property_page_post' ); ?>">

      <?php do_action( 'flawless_ui::above_header' ); ?>

      <header class="entry-title-wrapper property_title_wrapper">
        <?php flawless_breadcrumbs(); ?>
        <?php flawless_page_title(); ?>
        <?php the_tagline( '<h3 class="entry-subtitle">', '</h3>' ); ?>
      </header>

      <?php get_template_part( 'templates/entry-meta', 'header' ); ?>

      <div class="entry-content clearfix">
        <div class="the_content">
          <?php the_content( 'More Info' ); ?>

          <?php foreach( (array) $wp_properties[ 'property_meta' ] as $meta_slug => $meta_title ):
            if( empty( $post->$meta_slug ) || $meta_slug == 'tagline' )
              continue; ?>
            <h2><?php echo $meta_title; ?></h2>
            <p><?php echo  do_shortcode( html_entity_decode( $post->$meta_slug ) ); ?></p>
          <?php endforeach; ?>

        </div>

        <div class="formatted-row row-fluid">
          <?php if( get_post_meta( $post->ID, 'hide_property_attributes', true ) != 'true' ) { ?>
          <div class="span6">
              <div class="cfct-module"><?php echo do_shortcode( '[property_attributes list_title="Attributes" property_id=' . $post->ID . ']' ); ?></div>
          </div>
        <?php } ?>

        <?php if( get_post_meta( $post->ID, 'hide_property_taxonomies', true ) != 'true' && !empty( $wp_properties[ 'taxonomies' ] ) ) { ?>
        <div class="span6">
          <div class="cfct-module"><?php echo do_shortcode( '[property_taxonomy_terms property_id=' . $post->ID . ']' ); ?></div>
        </div>
        <?php } ?>

      </div>

        <?php if( $post->post_parent ): ?>
          <a href="<?php echo $post->parent_link; ?>"><?php _e( 'Return to building page.','wpp' ) ?></a>
        <?php endif; ?>

      </div><!-- .entry-content -->

      <?php if( get_post_meta( $post->ID, 'hide_default_google_map', true ) != 'true' ) { ?>
      <div class="formatted-row row-fluid">
        <div class="span12">
          <div class="cfct-module">
          <?php echo do_shortcode( '[property_map property_id=' . $post->ID . ']' ); ?>
          </div>
        </div>
      </div>
      <?php } ?>

      <?php get_template_part( 'templates/content','single-property-inquiry' ); ?>

    </div> <!-- flawless_module_class() -->

    <?php endwhile; endif; ?>

    <?php get_template_part( 'templates/content','single-property-bottom' ); ?>

  </div> <!-- .main.cfct-block -->

  <?php flawless_widget_area( 'right_sidebar' ); ?>

</div> <!-- #content -->

<?php get_template_part( 'templates/footer',  'property' );  ?>