<?php
/**
 * Template for custom taxonomies, categories will use archive.php
 *
 *@author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
  */

  //** Bail out if page is being loaded directly and flawless_theme does not exist */
  if(!function_exists('get_header')) {
    die();
  }

  $taxonomy = get_taxonomy( get_queried_object()->taxonomy );
  $term = get_queried_object();

  /** Get all our info that we need */
  $post = get_post_for_extended_term( $term->term_id );
  $meta = get_post_custom( $post->ID );
  $image = get_term_attachment_image( $term->ID, 'full' );

  /**
  pr($taxonomy,1);
  pr($term,1);
  pr($post,1);
  pr(get_term_attachment_image( $term->ID, 'full' ),1);
  prq( $meta);
  */

  $query_array = array( 'hdp_event' => "SELECT SQL_CALC_FOUND_ROWS * FROM {$wpdb->prefix}ud_qa_hdp_event WHERE 1=1 AND FIND_IN_SET( {$term->term_id}, `{$taxonomy->name}_ids` ) AND STR_TO_DATE( hdp_event_date, '%m/%d/%Y' ) >= CURDATE() ORDER BY STR_TO_DATE( hdp_event_date, '%m/%d/%Y' ) ASC" );

  //** Get all content types that use this taxonomy, and get their content coutns */
  foreach( $flawless['post_types'] as $post_type => $post_data ) {

    $this_query = array(
      'post_type' => $post_type,
      'numberposts' => -1,
      $taxonomy->name => $term->slug );

    if ( !empty( $query_array[$post_type] ) ) {
      $sql_query = $query_array[$post_type];

      $res = $wpdb->get_results( $sql_query );
      $total = $wpdb->get_col( "SELECT FOUND_ROWS();" );

      $found_content[ $post_type ] = array_merge( $this_query, array( 'numberposts' => $total[0] ) );
    } else {
      $query_result = new WP_Query( $this_query );

      if( $have_content = $query_result->found_posts ) {
        $found_content[ $post_type ] = array_merge( $this_query, array( 'numberposts' => $have_content ) );
      }
    }
  }

  $post_types = array_keys( (array) $found_content );
  $post_types_objects = array();

  /** Get our post type object */
  foreach( $post_types as $t ){
    $post_types_objects[ $t ] = get_post_type_object( $t );
  }
?>

<?php get_header( 'taxonomy' ) ?>

<?php get_template_part('attention', 'taxonomy'); ?>

<div class="<?php flawless_wrapper_class( 'tabbed-content' ); ?>">

  <div class="cfct-block sidebar-left span4 first visible-desktop">
    <div class="cfct-module" style="padding: 0; margin: 0;">

    <div class="visible-desktop dd_featured_image_wrap <?php echo $image ? 'have_image' : 'no_image'; ?>">
      <?php echo $image; ?>
    </div>

    <ul class="dd_side_panel_nav">

      <li class="visible-desktop link first ui-tabs-selected"><a href="#section_event_details"><i class="icon-info-blue icon-dd"></i> Info</a></li>

      <?php foreach( (array) $found_content as $post_type => $data ) { ?>
      <li class="visible-desktop link"><a href="#section_<?php echo $post_type; ?>">
        <i class="icon-<?php echo $post_type; ?> icon-dd"></i> <?php  echo get_post_type_object( $post_type )->labels->name;?> <span class="comment_count"><?php echo $data['numberposts']; ?></span></a>
      </li>
      <?php } ?>

      <?php if ($event[ 'geo_located' ] ) { ?>
      <li class="visible-desktop link"><a href="#section_map"><i class="hdp_venue icon-dd"></i> Location Map</a></li>
      <?php } ?>

    </ul>

    <div class="visible-desktop" style="height: 50px;"></div>

    </div>
  </div>

  <div class="<?php flawless_block_class( 'main cfct-block span8' ); ?>">

    <div class="<?php flawless_module_class( 'taxonomy-archive' ); ?>">

      <div id="section_event_details">

        <header class="entry-title-wrapper term-title-wrapper">
          <?php flawless_breadcrumbs(); ?>
          <h1 class="entry-title"><?php echo $term->name; ?></h1>
        </header>

        <div class="entry-content clearfix">

          <?php if( $image ) { ?>
            <div class="poster-iphone hidden-desktop">
              <?php echo $image; ?>
            </div>
            <hr class="hidden-desktop" />
          <?php } ?>

          <?php if( term_description() != '' ) { ?>
            <div class="category_description taxonomy">
            <?php echo do_shortcode( term_description() ); ?>
            </div>
            <hr class="dotted visible-desktop" style="margin-top:5px;" />
          <?php }

          /** Go through the meta, and print it out */
          if( isset( $meta[ 'formatted_address' ] ) && is_array( $meta[ 'formatted_address' ] ) && isset( $meta[ 'formatted_address' ][ 0 ] ) && !empty( $meta[ 'formatted_address' ][ 0 ] ) ){ ?>
            <div class="tax_address">
              <span>Address:</span>
              <?php echo $meta[ 'formatted_address' ][ 0 ]; ?>
            </div> <?php
          }

          /** Do the loop */
          $found = false;
          $map = array(
            'hdp_website_url' => 'Official Website',
            'hdp_facebook_url' => 'on Facebook',
            'hdp_twitter_url' => 'on Twitter',
            'hdp_google_plus_url' => 'on Google Plus',
            'hdp_youtube_url' => 'on YouTube',
          );
          foreach( $map as $slug => $text ){
            if( !is_array( $meta ) ) continue;
            if( !in_array( $slug, array_keys( $meta ) ) ) continue;
            if( !is_array( $meta[ $slug ] ) || !isset( $meta[ $slug ][ 0 ] ) || trim( $meta[ $slug ][ 0 ] ) == '' ) continue;

            if( !$found ){ ?>
              <ul class="tax_meta"> <?php
              $found = true;
            } ?>

            <li class="<?php echo $slug; ?>"><a href="<?php echo addcslashes( trim( $meta[ $slug ][ 0 ] ), '"' ); ?>" target="_blank"><?php echo $term->name; ?> <?php echo $text; ?></a></li> <?php
          }
          if( $found ) { ?>
            </ul> <?php
          } ?>

        </div>

      </div>

      <?php foreach( (array) $found_content as $post_type => $data ) { ?>
        <div id="section_<?php echo $post_type; ?>">
          <h1><?php echo $term->name; ?> <?php echo $post_types_objects[ $post_type ]->labels->name; ?></h1>

          <?php echo do_shortcode( "[hdp_custom_loop do_shortcode=false post_type={$post_type} per_page=0 {$taxonomy->name}={$term->term_id}] "); ?>
        </div>
      <?php } ?>


    </div>

  </div>

</div>

<?php get_footer(); ?>
