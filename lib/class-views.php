<?php
/**
 * Flawless
 *
 * @author potanin@UD
 * @version 0.1.2
 * @namespace Flawless
 * @module Flawless
 */
namespace UsabilityDynamics\Flawless {

  /**
   * Views
   *
   * @author potanin@UD
   * @version 0.1.0
   * @class Views
   * @submodule Views
   */
  class Views {

    /**
     * Views class version.
     *
     * @static
     * @property $version
     * @type {Object}
     */
    public static $version = '0.1.2';

    /**
     * Constructor for the Views class.
     *
     * @author potanin@UD
     * @version 0.0.1
     * @method __construct
     *
     * @constructor
     * @for Views
     *
     * @param bool $options
     *
     * @internal param $array
     */
    public function __construct( $options = false ) {

      add_action( 'flawless::init_lower',           array( $this, 'init_lower' ), 10 );
      add_action( 'flawless::template_redirect',    array( $this, 'template_redirect' ), 10 );
      add_action( 'flawless::theme_setup::after',   array( $this, 'theme_setup' ), 10 );

      //** Disable default Gallery shortcode styles */
      add_filter( 'use_default_gallery_style', create_function( '', ' return false; ' ) );

    }

    /**
     * Class Logger
     *
     * @method log
     * @for Views
     *
     * @since 0.1.2
     * @author potanin@UD
     */
    static function log() {
      //$args = func_num_args();
    }

    /**
     * Figure out which Widget Area Sections ( WAS ) are available for use in the theme
     *
     * @method theme_setup
     * @for Views
     *
     * @param $flawless
     */
    static function theme_setup( &$flawless ) {
      self::define_widget_area_sections( $flawless );
    }

    /**
     * Upper Level Loader
     *
     * @method template_redirect
     */
    public function template_redirect( &$flawless ) {
      Views::set_current_view();

      add_action( 'flawless_ui::above_header', array( __CLASS__, 'above_header' ) );

      //** Load a custom color scheme if set last, so it supercedes all others */
      if ( !empty( $flawless[ 'color_scheme' ] ) && Theme::get_color_schemes( $flawless[ 'color_scheme' ] ) ) {
        $flawless[ 'loaded_color_scheme' ] = Theme::get_color_schemes( $flawless[ 'color_scheme' ] );
        $flawless[ 'current_view' ][ 'body_classes' ][ ] = 'flawless_have_skin';
        $flawless[ 'current_view' ][ 'body_classes' ][ ] = 'flawless_' . str_replace( array( '.', '-', ' ' ), '_', $flawless[ 'color_scheme' ] );
      } else {
        $flawless[ 'current_view' ][ 'body_classes' ][ ] = 'flawless_no_skin';
      }


    }

    /**
     * Generates all views, registers Flawless widget areas, and unregisters any unsued widget areas.
     *
     * Unregistered widget areas are loaded into [widget_areas] array so they can be displayed on the Flawless settings page
     * for WAS association.
     *
     * Generates dynamic settings on every page load.
     *
     * @method init_lower
     * @for Views
     *
     * @creates [widget_areas]
     * @creates [views]
     *
     * @action init ( 500 )
     * @action flawless::init_lower ( 10 )
     *
     * @since 0.0.2
     */
    static function init_lower( &$flawless ) {
      global $wp_registered_sidebars;

      $widget_areas = array();
      $views = array();

      //** Create a default Flawless sidebar */
      if ( !isset( $flawless[ 'flawless_widget_areas' ] ) ) {

        $flawless[ 'flawless_widget_areas' ][ 'global_sidebar' ] = array(
          'label' => __( 'Global Sidebar', 'flawless' ),
          'class' => 'my_global_sidebar',
          'description' => __( 'Our default sidebar.', 'flawless' ),
          'id' => 'global_sidebar'
        );

      }

      //** Create custom widget areas */
      foreach ( (array) $flawless[ 'flawless_widget_areas' ] as $sidebar_id => $wa_data ) {

        //** Register this widget area with some basic information */
        register_sidebar( array(
          'name' => $wa_data[ 'label' ],
          'description' => $wa_data[ 'description' ],
          'class' => $wa_data[ 'class' ],
          'id' => $sidebar_id,
          'before_widget' => '<div id="%1$s"  class="flawless_widget theme_widget widget  %2$s">',
          'after_widget' => '</div>',
          'before_title' => '<h5 class="widgettitle widget-title">',
          'after_title' => '</h5>'
        ) );

        $wp_registered_sidebars[ $sidebar_id ][ 'flawless_widget_area' ] = true;

      }

      //** Build views from all used widget areas, update widget area info based on location and usage */
      foreach ( (array) $flawless[ 'post_types' ] as $post_type => $post_type_data ) {

        //** Load post type configuration ( not essential, just in case ) */
        $views[ 'post_types' ][ $post_type ][ 'settings' ] = $post_type_data;
        $views[ 'post_types' ][ $post_type ][ 'widget_areas' ] = array();

        Flawless::add_post_type_option( array(
          'post_type' => $post_type,
          'position' => 50,
          'meta_key' => 'hide_page_title',
          'label' => sprintf( __( 'Hide Page Title.', 'flawless' ) )
        ) );

        /** If breadcrumbs are not globally hidden, show an option to hide them */
        if ( $flawless[ 'hide_breadcrumbs' ] != 'true' ) {
          Flawless::add_post_type_option( array(
            'post_type' => $post_type,
            'position' => 70,
            'meta_key' => 'hide_breadcrumbs',
            'label' => sprintf( __( 'Hide Breadcrumbs.', 'flawless' ) )
          ) );
        }

        if ( post_type_supports( $post_type, 'author' ) && $post_type_data[ 'disable_author' ] != 'true' ) {
          Flawless::add_post_type_option( array(
            'post_type' => $post_type,
            'position' => 100,
            'meta_key' => 'hide_post_author',
            'label' => sprintf( __( 'Hide Author.', 'flawless' ) )
          ) );
        }

        if ( post_type_supports( $post_type, 'capability_restrictions' ) ) {
          Flawless::add_post_type_option( array(
            'post_type' => $post_type,
            'position' => 1000,
            'meta_key' => 'must_be_logged_in',
            'label' => sprintf( __( 'Must Be Logged In To View.', 'flawless' ) )
          ) );
        }

        //** Load used widget areas into array */
        foreach ( (array) $post_type_data[ 'widget_areas' ] as $was_slug => $these_widget_areas ) {

          Flawless::add_post_type_option( array(
            'post_type' => $post_type,
            'position' => 200,
            'meta_key' => 'disable_' . $was_slug,
            'label' => sprintf( __( 'Disable %1s.', 'flawless' ), $flawless[ 'widget_area_sections' ][ $was_slug ][ 'label' ] )
          ) );

          $views[ 'post_types' ][ $post_type ][ 'widget_areas' ][ $was_slug ] = array_filter( (array) $these_widget_areas );

          $widget_areas[ 'used' ] = array_merge( (array) $widget_areas[ 'used' ], (array) $these_widget_areas );

        }

      }

      //** Build views from all used widget areas, update widget area info based on location and usage */
      foreach ( (array) $flawless[ 'taxonomies' ] as $taxonomy => $taxonomy_data ) {

        //** Load post type configuration ( not essential, just in case ) */
        $views[ 'taxonomies' ][ $taxonomy ][ 'settings' ] = $taxonomy_data;
        $views[ 'taxonomies' ][ $taxonomy ][ 'widget_areas' ] = array();

        //** Load used widget areas into array */
        foreach ( (array) $taxonomy_data[ 'widget_areas' ] as $was_slug => $these_widget_areas ) {

          $views[ 'taxonomies' ][ $taxonomy ][ 'widget_areas' ][ $was_slug ] = array_filter( (array) $these_widget_areas );

          $widget_areas[ 'used' ] = array_merge( (array) $widget_areas[ 'used' ], (array) $these_widget_areas );

        }

      }

      //** Create array of all sidebars */
      $widget_areas[ 'all' ] = $wp_registered_sidebars;

      ksort( $wp_registered_sidebars );

      ksort( $widget_areas[ 'all' ] );

      //** Unregister any WAs not placed into a WAS */
      foreach ( (array) $wp_registered_sidebars as $sidebar_id => $sidebar_data ) {

        //** If there are no active sidebars, we leave our default global sidebar active */
        if ( count( $widget_areas[ 'used' ] ) == 0 && $sidebar_id == 'global_sidebar' ) {
          continue;
        }

        if ( !in_array( $sidebar_id, (array) $widget_areas[ 'used' ] ) ) {

          $widget_areas[ 'unused' ][ $sidebar_id ] = $wp_registered_sidebars[ $sidebar_id ];

          if ( $flawless[ 'deregister_empty_widget_areas' ] ) {
            unset( $wp_registered_sidebars[ $sidebar_id ] );
          }

        }

      }

      //** Update descriptions of all used widget areas */
      foreach ( (array) $widget_areas[ 'used' ] as $sidebar_id ) {

        //$wp_registered_sidebars[$sidebar_id][ 'description' ] = 'Modified! ' . $wp_registered_sidebars[$sidebar_id][ 'description' ];

      }

      //** Load settings into global variable */
      $flawless[ 'widget_areas' ] = $widget_areas;
      $flawless[ 'views' ] = $views;

      do_action( 'flawless::create_views', $flawless );

    }

    /**
     * Display attention grabbing image. (Option to enable does not currently exist, for testing only )
     *
     * @since 0.6.0
     */
    static function above_header() {
      global $post;

      if ( has_post_thumbnail( $post->ID ) && get_post_meta( 'display_header_featured_image', true ) == 'true' && $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large-feature' ) ) {
        $html[ ] = '<div class="row-c4-1234 row row-fluid"><div class="span12 full-width c4-1234 cfct-block">';
        $html[ ] = '<div class="cfct-module cfct-module-hero"><img src="' . $image[ 0 ] . '" class="cfct-module-hero-image fixed_size attachment-large-feature"></div>';
        $html[ ] = '</div></div>';
        echo implode( '', $html );
      }

    }

    /**
     * Defined which "Widget Area Sections" available for use in the Theme.
     *
     * These sections can have different Widget Areas associated with them, based on content type, home page, or blog page.
     * Definitions here are only configurable via API.
     *
     * @todo Add "Attention Grabber" via Feature
     *
     * @action after_setup_theme( 10 )
     * @since 0.0.2
     */
    static function define_widget_area_sections( $flawless ) {

      $flawless[ 'widget_area_sections' ][ 'left_sidebar' ] = array(
        'placement' => __( 'left', 'flawless' ),
        'class' => 'c6-12 sidebar-left span4 first',
        'label' => __( 'Left Sidebar', 'flawless' )
      );

      $flawless[ 'widget_area_sections' ][ 'right_sidebar' ] = array(
        'placement' => __( 'right', 'flawless' ),
        'class' => 'c6-56 sidebar-right span4 last',
        'label' => __( 'Right Sidebar', 'flawless' )
      );

      $flawless[ 'widget_area_sections' ] = apply_filters( 'flawless::widget_area_sections', $flawless[ 'widget_area_sections' ] );

      do_action( 'flawless::define_widget_area_sections' );

      return $flawless;

    }

    /**
     * Remove all instanced of a widget from a sidebar
     *
     * Adds a widget to a sidebar, making sure that sidebar doesn't already have this widget.
     *
     * @since 0.0.2
     */
    static function remove_widget_from_sidebar( $sidebar_id, $widget_id ) {
      global $wp_registered_widget_updates;

      //** Load sidebars */
      $sidebars = wp_get_sidebars_widgets();

      //** Get widget ID */
      if ( is_array( $sidebars[ $sidebar_id ] ) ) {
        foreach ( (array) $sidebars[ $sidebar_id ] as $this_sidebar_id => $sidebar_widgets ) {

          //** Check if this sidebar already has this widget */

          if ( strpos( $sidebar_widgets, $widget_id ) === 0 || $widget_id == 'all' ) {

            //** Remove widget instance if it exists */
            unset( $sidebars[ $sidebar_id ][ $this_sidebar_id ] );

          }

        }
      }

      //** Save new siebars */
      wp_set_sidebars_widgets( $sidebars );
    }

    /**
     * Adds a widget to a sidebar.
     *
     * Adds a widget to a sidebar, making sure that sidebar doesn't already have this widget.
     *
     * Example usage:
     * Views::add_widget_to_sidebar( 'global_property_search', 'text', array( 'title' => 'Automatically Added Widget', 'text' => 'This widget was added automatically' ));
     *
     * @todo Some might exist that adds widgets twice.
     * @todo Consider moving functionality to UD Class
     *
     * @since 0.0.2
     */
    static function add_widget_to_sidebar( $sidebar_id = false, $widget_id = false, $settings = array(), $args = '' ) {
      global $wp_registered_widget_updates, $wp_registered_widgets;

      extract( wp_parse_args( $args, array(
        'do_not_duplicate' => 'true'
      ) ), EXTR_SKIP );

      require_once( ABSPATH . 'wp-admin/includes/widgets.php' );

      do_action( 'load-widgets.php' );
      do_action( 'widgets.php' );
      do_action( 'sidebar_admin_setup' );

      //** Need some validation here */
      if ( !$sidebar_id ) {
        return false;
      }

      if ( !$widget_id ) {
        return false;
      }

      if ( empty( $settings ) ) {
        return false;
      }

      //** Load sidebars */
      $sidebars = wp_get_sidebars_widgets();

      //** Get widget ID */
      $widget_number = next_widget_id_number( $widget_id );

      if ( is_array( $sidebars[ $sidebar_id ] ) ) {
        foreach ( (array) $sidebars[ $sidebar_id ] as $this_sidebar_id => $sidebar_widgets ) {

          //** Check if this sidebar already has this widget */
          if ( strpos( $sidebar_widgets, $widget_id ) === false ) {
            continue;
          }

          $widget_exists = true;

        }
      }

      if ( $do_not_duplicate == 'true' && $widget_exists ) {
        return true;
      }

      foreach ( (array) $wp_registered_widget_updates as $name => $control ) {

        if ( $name == $widget_id ) {
          if ( !is_callable( $control[ 'callback' ] ) ) {
            continue;
          }

          ob_start();
          call_user_func_array( $control[ 'callback' ], $control[ 'params' ] );
          ob_end_clean();
          break;
        }
      }

      //** May not be necessary */
      if ( $form = $wp_registered_widget_controls[ $widget_id ] ) {
        call_user_func_array( $form[ 'callback' ], $form[ 'params' ] );
      }

      //** Add new widget to sidebar array */
      $sidebars[ $sidebar_id ][ ] = $widget_id . '-' . $widget_number;

      //** Add widget to widget area */
      wp_set_sidebars_widgets( $sidebars );

      //** Get widget configuration */
      $widget_options = get_option( 'widget_' . $widget_id );

      //** Check if current widget has any settings ( it shouldn't ) */
      if ( $widget_options[ $widget_number ] ) {
      }

      //** Update widget with settings */
      $widget_options[ $widget_number ] = $settings;

      //** Commit new widget data to database */
      update_option( 'widget_' . $widget_id, $widget_options );

      return true;

    }

    /**
     * Determines the currently requested page type.
     *
     * Returns information about curent view:
     * - type: The general type of request, typically corresponding with the type of template WP would load
     * - view: The specific view type, such as 'post_type', 'taxonomy', 'home', etc. that are used by Flawless to display custom elements such as sidebars
     * - group: The "group" this view belongs to, such as post types, taxonomies, etc.
     *
     * @todo Ensure $wp_query->query_vars work with other permalink structures. - potanin@UD
     * @since 0.0.2
     * @author potanin@UD
     */
    static function this_request() {
      global $wp_query, $post;

      $t = array();

      switch ( true ) {

        /**
         * The home page, when a page is used.  In this instance, we treate it just like any other page.
         *
         */
        case is_page() && is_front_page():
          $t[ 'view' ] = 'single';
          $t[ 'group' ] = 'post_types';
          $t[ 'type' ] = 'page'; /* WP only allows pages to be set as home page */
          $t[ 'note' ] = 'Static Home Page';

          self::log( 'Current View: Home page with static page.' );

          break;

        /**
         * The home page, when no page is set, so displayed as archive
         *
         */
        case !is_page() && is_front_page():
          $t[ 'view' ] = 'archive';
          $t[ 'type' ] = 'home';
          $t[ 'note' ] = 'Non-Static ( Archive ) Home Page';

          self::log( 'Current View: Home page, default posts archive.' );

          break;

        /**
         * If this is the Blog Posts index page.
         *
         * By default posts page is never rendered NOT being attached to a page, therefore always 'single'
         */
        case $wp_query->is_posts_page:
          $t[ 'view' ] = 'single';
          $t[ 'group' ] = 'posts_page';
          $t[ 'type' ] = $wp_query->query_vars[ 'post_type' ] ? $wp_query->query_vars[ 'post_type' ] : 'page';
          $t[ 'note' ] = 'Posts Page ( Archive )';

          self::log( 'Current View: Blog Posts Index page.' );

          break;

        /**
         * If viewing a root of a post type, when the post type allows for a root archive
         * Note, default WP post types such as post and page do not have a post type archive
         *
         */
        case $wp_query->is_post_type_archive:
          $t[ 'view' ] = 'archive';
          $t[ 'group' ] = 'post_types';
          $t[ 'type' ] = $wp_query->query_vars[ 'post_type' ];

          self::log( sprintf( 'Current View: Post Type Archive ( %1s ).', $wp_query->query_vars[ 'post_type' ] ) );

          break;

        /**
         * If this is a single page, just as a post, page or custom post type single view
         *
         * Developer Notice: BuddyPress Pages are recognized as this ( page ).
         * Could create custom "BuddyPress" content type and modify wp_dropdown_pages filter to make them selectable.
         */
        case is_singular():
          $t[ 'view' ] = 'single';
          $t[ 'group' ] = 'post_types';
          $t[ 'type' ] = $post->post_type;

          self::log( sprintf( 'Current View: Single post-type page ( %1s ).', $post->post_type ) );

          break;

        /**
         * For search results.
         *
         */
        case is_search():
          $t[ 'view' ] = 'search';
          $t[ 'group' ] = 'post_types';
          $t[ 'type' ] = 'page';

          self::log( 'Current View: Search Results page.' );

          break;

        /**
         * For taxonomy archives ( not taxonomy roots )
         * Template Load: ( category.php | tag.php | taxonomy-{$taxonomy} ) -> ( archive.php )
         * Although category and tag are taxonomies, WP has special templates for them.
         */
        case is_tax() || is_category() || is_tag():
          $t[ 'view' ] = 'archive';
          $t[ 'group' ] = 'taxonomies';
          $t[ 'type' ] = $wp_query->tax_query->queries[ 0 ][ 'taxonomy' ];

          self::log( sprintf( 'Current View: Taxonomy archive ( %1s ) - ( non-root ). ', $wp_query->tax_query->queries[ 0 ][ 'taxonomy' ] ) );

          break;

        /**
         * Taxonomy Root, by default results in 404.  WordPress does not support root pages for taxonomies, i.e. .com/category/ or .com/genre/
         * We check that the queried name is for a valid taxonomy, yet no taxonomy nor page is detece
         * Theoretically such a request should show all the objects associated with the taxonomy, perhaps uncategorized or ideally a Tagcloud
         */
        case taxonomy_exists( $wp_query->query_vars[ 'name' ] ) && !is_archive() && !is_singular():
          $t[ 'view' ] = 'archive';
          $t[ 'group' ] = 'taxonomies';
          $t[ 'type' ] = $wp_query->query_vars[ 'name' ];

          self::log( 'Current View: Taxonomy root archive.' );

          break;

        default:
          $t[ 'view' ] = 'search';
          $t[ 'group' ] = 'post_types';
          $t[ 'type' ] = 'page';

          self::log( 'Current View: Unknown - rendering same as Page.' );

          break;

      }

      $t = apply_filters( 'flawless_request_type', $t );

      return $t;

    }

    /**
     * Return array of sidebars that the current page needs to display
     *
     * Used to load CSS classes early on into the <body> element, as well as others
     *
     * Determines:
     * - widget_areas - removes any widget areas that do not have any widgets
     * - body_classes
     * - block_classes - the primary content container, class varies depending on number of sidebars
     *
     * @todo Fix issue with Post Page not displaying sidebar. Should be treated as a page. - potanin@UD 5/30/12
     * @filter template_redirect ( 0 )
     * @since 0.0.2
     * @author potanin@UD
     */
    static function set_current_view() {
      global $post, $wp_query, $flawless;

      //** Typically $flawless[ 'current_view' ] would be blank, but in case it was set by another function via API we do not override */
      $flawless[ 'current_view' ] = array_merge( (array) $flawless[ 'current_view' ], self::this_request() );

      $flawless[ 'current_view' ][ 'body_classes' ] = (array) $flawless[ 'current_view' ][ 'body_classes' ];

      //** Load view data if it exists ( Widget areas, etc. )
      if ( $flawless[ 'views' ][ $flawless[ 'current_view' ][ 'group' ] ] ) {
        $flawless[ 'current_view' ] = array_merge( (array) $flawless[ 'current_view' ], (array) $flawless[ 'views' ][ $flawless[ 'current_view' ][ 'group' ] ][ $flawless[ 'current_view' ][ 'type' ] ] );
      }

      //** Get body classes from active widget sections */
      foreach ( (array) $flawless[ 'current_view' ][ 'widget_areas' ] as $was_slug => $wa_sidebars ) {

        //** If widget area sections and widget areas are loaded, make sure widget areas are active */
        foreach ( (array) $wa_sidebars as $this_key => $sidebar_id ) {
          if ( !Flawless::is_active_sidebar( $sidebar_id ) || apply_filters( 'flawless::exclude_sidebar', false, $sidebar_id ) ) {
            unset( $flawless[ 'current_view' ][ 'widget_areas' ][ $was_slug ][ $this_key ] );
          }
        }

        $flawless[ 'current_view' ][ 'widget_areas' ] = array_filter( (array) $flawless[ 'current_view' ][ 'widget_areas' ] );

        //** Check if we have any active sidebars left - if not, leave.  */
        if ( empty( $flawless[ 'current_view' ][ 'widget_areas' ][ $was_slug ] ) ) {
          continue;
        }

        if ( get_post_meta( $post->ID, 'disable_' . $was_slug, true ) ) {
          unset( $flawless[ 'current_view' ][ 'widget_areas' ][ $was_slug ] );
        }

      }

      if ( count( $flawless[ 'current_view' ][ 'widget_areas' ] ) === 0 ) {
        $flawless[ 'current_view' ][ 'block_classes' ] = array( 'c6-123456 span12' );
        $flawless[ 'current_view' ][ 'body_classes' ][ ] = 'no_wp_sidebar';
      }

      if ( count( $flawless[ 'current_view' ][ 'widget_areas' ] ) == 1 ) {
        $flawless[ 'current_view' ][ 'body_classes' ][ ] = 'have_sidebar';

        if ( array_key_exists( 'right_sidebar', $flawless[ 'current_view' ][ 'widget_areas' ] ) ) {
          $flawless[ 'current_view' ][ 'block_classes' ][ ] = 'c6-1234 span8 first';
          $flawless[ 'current_view' ][ 'body_classes' ][ ] = 'have_right_sidebar';

        }

        if ( array_key_exists( 'left_sidebar', $flawless[ 'current_view' ][ 'widget_areas' ] ) ) {
          $flawless[ 'current_view' ][ 'block_classes' ][ ] = 'c6-3456 span8 last';
          $flawless[ 'current_view' ][ 'body_classes' ][ ] = 'have_left_sidebar';
        }

      }

      if ( count( $flawless[ 'current_view' ][ 'widget_areas' ] ) == 2 ) {
        $flawless[ 'current_view' ][ 'block_classes' ][ ] = 'c6-45 span4';
      }

      //** If navbar is active */
      if ( is_array( $flawless[ 'navbar' ][ 'html' ] ) ) {
        $flawless[ 'current_view' ][ 'body_classes' ][ ] = 'have-navbar';
      }

      if ( hide_page_title() ) {
        $flawless[ 'current_view' ][ 'body_classes' ][ ] = 'no-title-wrapper';
      }

      if ( $flawless[ 'developer_mode' ] == 'true' ) {
        $flawless[ 'current_view' ][ 'body_classes' ][ ] = 'developer_mode';
      }

      if ( current_user_can( 'manage_options' ) ) {
        $flawless[ 'current_view' ][ 'body_classes' ][ ] = 'user_is_admin';
      }

      $flawless[ 'current_view' ][ 'body_classes' ] = array_unique( $flawless[ 'current_view' ][ 'body_classes' ] );
      $flawless[ 'current_view' ][ 'block_classes' ] = array_unique( $flawless[ 'current_view' ][ 'block_classes' ] );

      $flawless[ 'current_view' ] = apply_filters( 'set_current_view', $flawless[ 'current_view' ] );

      unset( $flawless[ 'current_view' ][ 'settings' ] );

      self::console_log( 'Executed: Views::set_current_view();' );
      self::console_log( $flawless[ 'current_view' ] );

    }

    /**
     * Return array of sidebars that the current page needs to display
     *
     * Used to load CSS classes early on into the <body> element, as well as others
     * Whether sidebars are active or not is already checked in set_current_view();
     *
     *
     * @since 0.0.2
     * @author potanin@UD
     */
    static function get_current_sidebars( $widget_area_type = false ) {
      global $post, $flawless;

      if ( !$widget_area_type ) {
        return array();
      }

      $response = array();

      foreach ( (array) $flawless[ 'current_view' ][ 'widget_areas' ][ $widget_area_type ] as $sidebar_id ) {

        $response[ ] = array(
          'sidebar_id' => $sidebar_id,
          'class' => $flawless[ 'widget_area_sections' ][ $widget_area_type ][ 'class' ]
        );

      }

      //self::log( 'Executed: Views::get_current_sidebars();' );
      //self::log( $response );

      return $response;

    }

    /**
     * Checks if sidebar is active. Same as default function, but allows hooks
     *
     * @since 0.2.0
     */
    static function is_active_sidebar( $sidebar ) {
      return is_active_sidebar( $sidebar );
    }

  }

}