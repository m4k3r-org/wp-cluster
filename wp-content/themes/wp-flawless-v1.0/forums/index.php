<?php
/**
 * Template for standard pages.
 *
 *
 *
 * @version 3.0.0
 * @author Usability Dynamics, Inc. <info@usabilitydynamics.com>
 * @package Flawless
*/

  //** Bail out if page is being loaded directly and flawless_theme does not exist */
  if(!function_exists('get_header')) {
    die();
  }

?>

<?php get_header('buddypress'); ?>

<script type="text/javascript">
  jQuery( document ).ready(function() {

    if( typeof jQuery.fn.dynamic_filter == 'function' ) {

      jQuery( '#forums-dir-list' ).dynamic_filter({
        settings: {
          server_driven: true,
          debug: true,
          per_page: 50,
          dom_limit: 200,
          unique_tag: 'topic_id',
          messages: {
            server_error: false
          }
        },
        ajax: {
          url: '<?php echo admin_url('admin-ajax.php'); ?>?action=bp_get_topics'
        },
        classes: {
          inputs_list_wrapper: 'inputs_list_wrapper cfct-module'
        },
        ux: {
          filter: jQuery('.dynamic_filter .filter_container .filters'),
          results_wrapper: jQuery('.dynamic_filter .results_wrapper'),
          results: jQuery('<div class="results cfct-module"></div>'),
          status: jQuery('<div class="alert alert-success"></div>')
        },
        attributes: {
          search_terms: {
            label: 'Search Topics...',
            sort_order: 1,
            hide_filter_label: true,
            filter_type: 'input',
            filter: true
          },
          topic_title: {
            label: 'Topic Title',
            sort_order: 10,
            display: true,
            render_callback: function( default_value, args ) {
              return '<a href="' + args.result_row.topic_link + '">' + default_value + '</a>'
            }
          },
          topic_tags: {
            label: 'Tags',
            sort_order: 55,
            display: true,
            render_callback: function( default_value, args ) {
              return '<span class="product_name">' + args.result_row.object_name + '</span>' + ( default_value != '' ? ': ' + default_value : '' );
            }
          },
          freshness: {
            label: 'Freshness',
            sort_order: 90,
            display: true,
            render_callback: function( default_value, args ) {
              return 'Freshness ' + default_value + ', Posts: ' + args.result_row.topic_posts ;
            }
          },
          object_name: {
            label: 'Product',
            sort_order: 60,
            display: false,
            filter: true
          },
          topic_type: {
            label: 'Topic Types',
            display: false,
            filter: true,
            values: {
              regular_event: {
                label: 'Premium Request'
              },
              major_event: {
                label: 'Regular Topic'
              }
            },
          },
          topic_last_poster_name: {
            label: 'Last Poster',
            sort_order: 80,
            display: true,
            collapse: true,
            filter: false,
            render_callback: function( default_value, args ) {
              return '<img class="lazy forum-thumbnail" src="<?php echo  get_bloginfo('template_url'); ?>/img/gray.gif" data-original="' + args.result_row.last_poster_avatar + '" title="' + args.result_row.topic_last_poster_name + '" />';
            }
          },
          topic_time: {
            label: 'Time',
            display: false,
            filter: false
          },
          topic_posts: {
            label: 'Topic Posts',
            filter_type: 'range',
            display: false,
            filter: true
          }
        }
      });

    } else {
      //flawless.add_notice('Dynamic Filter disabled.');
    }

  });
</script>

<?php // locate_template( array( 'forums/forums-loop.php' ), true ); ?>

<div class="<?php flawless_wrapper_class(); ?>">

  <?php flawless_widget_area('left_sidebar'); ?>

  <div class="<?php flawless_block_class( 'main cfct-block' ); ?>">
    <div class="<?php flawless_module_class( '' ); ?>">

      <div id="forums-dir-list" class="forums dir-list dynamic_filter clearfix">
        <div class="results_wrapper cfct-block block-70"></div>
        <div class="filter_container cfct-block block-30 right">
          <?php if(is_user_logged_in()): ?>
            <button class="add_new_topic btn big blue"><i class="icon-plus-sign icon-white"></i> Add New Topic</button>
            <?php endif; ?>
            <div class="filters"></div>
            <div class="forum_sidebar"><?php dynamic_sidebar( $flawless[ 'buddypress' ][ 'forum_sidebar' ] ); ?></div>
      </div>
      </div>

      <?php if ( is_user_logged_in() ) : ?>
        <?php get_template_part('forums/add-new-topic'); ?>
      <?php endif; ?>

    </div>
  </div> <!-- .main cfct-block -->

  <?php flawless_widget_area('right_sidebar'); ?>

</div> <!-- #content -->



<?php get_footer(); ?>
