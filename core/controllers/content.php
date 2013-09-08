<?php
/**
 * Flawless
 *
 * @author potanin@UD
 * @version 0.0.1
 * @namespace Flawless
 */
namespace Flawless {

  /**
   * Content Type Management
   *
   * @author potanin@UD
   * @version 0.1.0
   * @class Content
   */
  class Content {

    // Class Version.
    public static $version = '0.1.1';

    /**
     * Constructor for the Content class.
     *
     * @author potanin@UD
     * @version 0.0.1
     * @method __construct
     *
     * @constructor
     * @for Content
     *
     * @param array 
     */
    public function __construct( $options = array() ) {
      add_filter( 'flawless::init_upper', array( $this, 'setup_content_types' ) );
      add_filter( 'flawless::template_redirect', array( $this, 'template_redirect' ) );
    }

    /**
     * Return the allowed pages that $pagenow global is allowed to be.
     *
     */
    public function changeable_post_type( $post_type = false ) {
      global $post, $flawless;

      $post_type = $post_type ? $post_type : $post->post_type;

      if ( !$post_type ) {
        return false;
      }

      $changeable_post_types = (array) apply_filters( 'flawless::changeable_post_types', array_keys( (array) $flawless[ 'post_types' ] ) );

      if ( in_array( $post_type, $changeable_post_types ) ) {
        return true;
      }

      return false;
    }

    /**
     * Primary function for handling front-end actions
     *
     * @method template_redirect
     * @for Content
     */
    static function template_redirect( &$flawless ) {
      global $post;

      if ( get_post_meta( $post->ID, 'must_be_logged_in', true ) == 'true' && !is_user_logged_in() ) {
        die( wp_redirect( home_url() ) );
      }

    }

    /**
     * Setup theme features using the WordPress API as much as possible.
     *
     * This function must run after all the post types are created and initialized to have effect.
     *
     * This function may be called more than once at different action levels ( ALs ) since taxonomy and post types may be added by plugins,
     * yet we want the admin to have full control over all the post types and taxonomies in one UI.
     *
     * @todo Need to update all labels for taxonomoies. - potanin@UD
     *
     * @method setup_content_types
     * @for Content
     *
     * @action init (0)
     * @since 0.0.2
     */
    static function setup_content_types( &$flawless ) {
      global $wp_post_types, $wp_taxonomies;

      Log::add( 'Executed: Flawless::setup_content_types();' );

      do_action( 'flawless::content_types', $flawless );

      //** May only be necessary temporarily since Attachments were included in version 0.0.6 by accident */
      unset( $flawless[ 'post_types' ][ 'attachment' ] );

      //** Create any new post types that are in our settings array, but not in the global $wp_post_types variable*/
      foreach ( (array) $flawless[ 'post_types' ] as $type => $data ) {

        if ( $data[ 'flawless_post_type' ] != 'true' ) {
          continue;
        }

        Log::add( sprintf( __( 'Adding custom post type: %1s', 'flawless' ), $type ) );

        $post_type_settings = array(
          'label' => $data[ 'name' ],
          'menu_position' => ( $data[ 'hierarchical' ] == "true" ? 21 : 6 ),
          'public' => true,
          'exclude_from_search' => $data[ 'exclude_from_search' ],
          'hierarchical' => $data[ 'hierarchical' ],
          'has_archive' => is_numeric( $data[ 'root_page' ] ) && $data[ 'root_page' ] > 0 ? false : true,
          'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'revisions', 'post-formats', 'author' ),
        );

        if ( !empty( $data[ 'rewrite_slug' ] ) ) {
          $post_type_settings[ 'rewrite' ] = array(
            'slug' => $data[ 'rewrite_slug' ],
            'with_front' => true
          );
        }

        //** 'has_archive' allows post_type entries list korotkov@ud */
        register_post_type( $type, $post_type_settings );

        do_action( 'flawless_content_type_added', array( 'type' => $type, 'data' => $data ) );

      }

      //** Create any Flawless taxonomoies an create them, or update existing ones with custom settings  */
      foreach ( (array) $flawless[ 'taxonomies' ] as $type => $data ) {
        if ( $data[ 'flawless_taxonomy' ] == 'true' ) {

          Log::add( sprintf( __( 'Adding custom flawless_taxonomy: %1s', 'flawless' ), $type ) );

          $flawless[ 'taxonomies' ][ $type ][ 'rewrite_tag' ] = '%' . $type . '%';

          if ( !taxonomy_exists( $type ) ) {

            $taxonomy_settings = array(
              'label' => $data[ 'label' ],
              'exclude_from_search' => $data[ 'exclude_from_search' ],
              'hierarchical' => $data[ 'hierarchical' ]
            );

            $taxonomy_settings[ 'rewrite' ] = array(
              'slug' => $data[ 'rewrite_slug' ] ? $data[ 'rewrite_slug' ] : $type,
              'with_front' => true
            );

            register_taxonomy( $type, '', $taxonomy_settings );
          }

          add_rewrite_tag( $flawless[ 'taxonomies' ][ 'rewrite_tag' ], '([^/]+)' );

          do_action( 'flawless_content_type_added', array( 'type' => $type, 'data' => $data ) );

        }

        //** Check to see if a taxonomy has disappeared ( i.e. plugin deactivated that was adding it ) */
        if ( !in_array( $type, array_keys( $wp_taxonomies ) ) ) {
          unset( $flawless[ 'taxonomies' ][ $type ] );
        }

        $data = apply_filters( 'flawless::generate_taxonomies', $data, $type );

        //** Save our custom settings to global taxononmy object */
        $wp_taxonomies[ $type ]->hierarchical = $data[ 'hierarchical' ] == 'true' ? true : false;
        $wp_taxonomies[ $type ]->exclude_from_search = $data[ 'exclude_from_search' ] == 'true' ? true : false;
        //$wp_taxonomies[ $type ]->show_tagcloud = $data[ 'show_tagcloud' ] == 'true' ? true : false;

        $wp_taxonomies[ $type ]->label = $data[ 'label' ] ? $data[ 'label' ] : $wp_taxonomies[ $type ]->label;

        //** Automatically try to get singular form if not set ( experimental ) */
        $wp_taxonomies[ $type ]->labels->singular_name = $data[ 'singular_label' ] ? $data[ 'singular_label' ] : self::depluralize( $data[ 'label' ] );
        $wp_taxonomies[ $type ]->labels->name = $data[ 'label' ] ? $data[ 'label' ] : $wp_taxonomies[ $type ]->label;

        //** Set singular labels */
        $wp_taxonomies[ $type ]->labels->add_new_item = sprintf( __( 'New %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->singular_name );
        $wp_taxonomies[ $type ]->labels->new_item = sprintf( __( 'New %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->singular_name );
        $wp_taxonomies[ $type ]->labels->edit_item = sprintf( __( 'Edit %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->singular_name );
        $wp_taxonomies[ $type ]->labels->update_item = sprintf( __( 'Update %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->singular_name );
        $wp_taxonomies[ $type ]->labels->view_item = sprintf( __( 'No %1s found.', 'flawless' ), $wp_taxonomies[ $type ]->labels->singular_name );
        $wp_taxonomies[ $type ]->labels->new_item_name = sprintf( __( 'New %1s Name.', 'flawless' ), $wp_taxonomies[ $type ]->labels->singular_name );
        $wp_taxonomies[ $type ]->labels->not_found = sprintf( __( 'Add New %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->singular_name );
        $wp_taxonomies[ $type ]->labels->not_found_in_trash = sprintf( __( 'Add New %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->singular_name );

        //** Plural Labels */
        $wp_taxonomies[ $type ]->labels->search_items = sprintf( __( 'Search %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->name );
        $wp_taxonomies[ $type ]->labels->not_found_in_trash = sprintf( __( 'No %1s found in trash.', 'flawless' ), $wp_taxonomies[ $type ]->labels->name );
        $wp_taxonomies[ $type ]->labels->popular_items = sprintf( __( 'Popular %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->name );
        $wp_taxonomies[ $type ]->labels->add_or_remove_items = sprintf( __( 'Add ore remove %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->name );
        $wp_taxonomies[ $type ]->labels->choose_from_most_used = sprintf( __( 'Choose from most used %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->name );
        $wp_taxonomies[ $type ]->labels->all_items = sprintf( __( 'All %1s', 'flawless' ), $wp_taxonomies[ $type ]->labels->name );
        $wp_taxonomies[ $type ]->labels->menu_name = $wp_taxonomies[ $type ]->labels->name;

      }

      //** Cycle through all existing taxonomies, and load their settings into FS settings */
      foreach ( (array) $wp_taxonomies as $type => $data ) {

        //** We do not do anything with non displayed taxononomies */
        if ( !$data->show_ui ) {
          continue;
        }

        if ( $flawless[ 'taxonomies' ][ $type ][ 'flawless_taxonomy' ] == 'true' ) {
          $flawless[ 'taxonomies' ][ $type ][ 'rewrite_slug' ] = $data->rewrite[ 'slug' ];
        }

        $flawless[ 'taxonomies' ][ $type ][ 'label' ] = $wp_taxonomies[ $type ]->labels->name;
        $flawless[ 'taxonomies' ][ $type ][ 'label' ] = $wp_taxonomies[ $type ]->labels->name;
        $flawless[ 'taxonomies' ][ $type ][ 'hierarchical' ] = $wp_taxonomies[ $type ]->hierarchical ? 'true' : 'false';
        $flawless[ 'taxonomies' ][ $type ][ 'exclude_from_search' ] = $wp_taxonomies[ $type ]->exclude_from_search ? 'true' : 'false';
        //$flawless[ 'taxonomies' ][ $type ][ 'show_tagcloud' ] = $wp_taxonomies[ $type ]->show_tagcloud ? 'true' : 'false';

        do_action( 'flawless::setup_taxonomy::' . $type, $data );
        do_action( 'flawless::setup_taxonomy', $type, $data );

      }

      //** Loop through post types and update the $flawless array */
      foreach ( (array) $wp_post_types as $type => $data ) {

        //** We don't do anything with any post types that are not displayed */
        if ( !$data->public || !$data->show_ui ) {
          continue;
        }

        $defaults = get_object_taxonomies( $type );

        //** Configure special settings if they are set, or use default settings */
        $flawless[ 'post_types' ][ $type ][ 'name' ] = ( isset( $flawless[ 'post_types' ][ $type ][ 'name' ] ) ? $flawless[ 'post_types' ][ $type ][ 'name' ] : $data->labels->name );
        $flawless[ 'post_types' ][ $type ][ 'hierarchical' ] = ( isset( $flawless[ 'post_types' ][ $type ][ 'hierarchical' ] ) ? $flawless[ 'post_types' ][ $type ][ 'hierarchical' ] : ( $data->hierarchical ? 'true' : false ) );

        //** Cycle through all available taxonomies and add them back to post type. */
        foreach ( (array) $flawless[ 'taxonomies' ] as $tax => $tax_data ) {

          $flawless[ 'post_types' ][ $type ][ 'taxonomies' ][ $tax ] = ( isset( $flawless[ 'post_types' ][ $type ][ 'taxonomies' ][ $tax ] ) ? $flawless[ 'post_types' ][ $type ][ 'taxonomies' ][ $tax ] : ( in_array( $tax, $defaults ) ? 'enabled' : '' ) );

          if ( $flawless[ 'post_types' ][ $type ][ 'taxonomies' ][ $tax ] == 'enabled' ) {
            register_taxonomy_for_object_type( $tax, $type );
          }

          //** Remove blank values added as placeholders when FL taxonomies are initially registered */
          $wp_taxonomies[ $tax ]->object_type = array_filter( $wp_taxonomies[ $tax ]->object_type );
        }

        $flawless[ 'post_types' ][ $type ][ 'rewrite_slug' ] = $data->rewrite[ 'slug' ];

        @ksort( $flawless[ 'post_types' ][ $type ][ 'taxonomies' ] );

        if ( $flawless[ 'post_types' ][ $type ][ 'hierarchical' ] == 'true' ) {
          $wp_post_types[ $type ]->hierarchical = true;
          add_post_type_support( $type, 'page-attributes' );
        }

        if ( $flawless[ 'post_types' ][ $type ][ 'disable_comments' ] == 'true' ) {
          remove_post_type_support( $type, 'comments' );
        }

        if ( $flawless[ 'post_types' ][ $type ][ 'custom_fields' ] != 'true' ) {
          remove_post_type_support( $type, 'custom-fields' );
        }

        if ( $flawless[ 'post_types' ][ $type ][ 'disable_author' ] == 'true' ) {
          remove_post_type_support( $type, 'author' );
        }

        if ( $flawless[ 'post_types' ][ $type ][ 'exclude_from_search' ] == 'true' ) {
          $wp_post_types[ $type ]->exclude_from_search = true;
        }

        //** Rename post types. Do special stuff for post and page since they are built in, and Menu is hardcoded for some reason. */
        if ( $flawless[ 'post_types' ][ $type ][ 'name' ] != $data->labels->name || $flawless[ 'post_types' ][ $type ][ 'flawless_post_type' ] == 'true' ) {

          if ( $flawless[ 'post_types' ][ $type ][ 'name' ] != $data->labels->name ) {
            Log::add( sprintf( __( 'Changing labels for post type: %1s, from %2s to %3s', 'flawless' ), $type, $data->labels->name, $flawless[ 'post_types' ][ $type ][ 'name' ] ) );
          }

          $original_labels = ( !empty( $wp_post_types[ $type ]->labels ) ? (array) $wp_post_types[ $type ]->labels : array() );

          //** Update Post Type Labels */
          if ( empty( $flawless[ 'post_types' ][ $type ][ 'singular_name' ] ) ) {
            $flawless[ 'post_types' ][ $type ][ 'singular_name' ] = self::depluralize( $flawless[ 'post_types' ][ $type ][ 'name' ] );
          }

          $wp_post_types[ $type ]->labels = ( object ) array_merge( $original_labels, array(
            'name' => $flawless[ 'post_types' ][ $type ][ 'name' ], /* plural */
            'singular_name' => ucfirst( $flawless[ 'post_types' ][ $type ][ 'singular_name' ] ),
            'add_new_item' => sprintf( __( 'Add New %1s', 'flawless' ), $flawless[ 'post_types' ][ $type ][ 'singular_name' ] ),
            'new_item' => sprintf( __( 'New %1s', 'flawless' ), $flawless[ 'post_types' ][ $type ][ 'singular_name' ] ),
            'edit_item' => sprintf( __( 'Edit %1s', 'flawless' ), ucfirst( $flawless[ 'post_types' ][ $type ][ 'singular_name' ] ) ),
            'search_items' => sprintf( __( 'Search %1s', 'flawless' ), $flawless[ 'post_types' ][ $type ][ 'name' ] ),
            'view_item' => sprintf( __( 'View %1s', 'flawless' ), $flawless[ 'post_types' ][ $type ][ 'singular_name' ] ),
            'search_items' => sprintf( __( 'Search %1s', 'flawless' ), $flawless[ 'post_types' ][ $type ][ 'name' ] ),
            'not_found' => sprintf( __( 'No %1s found.', 'flawless' ), strtolower( $flawless[ 'post_types' ][ $type ][ 'singular_name' ] ) ),
            'not_found_in_trash' => sprintf( __( 'No %1s found in trash.', 'flawless' ), strtolower( $flawless[ 'post_types' ][ $type ][ 'name' ] ) )
          ) );

          switch ( $type ) {
            case 'post':
              add_action( 'admin_menu', create_function( '', ' global $menu, $submenu, $flawless; $menu[5][0] = $flawless["post_types"]["post"]["name"]; $submenu["edit.php"][5][0] = "All " . $flawless["post_types"]["post"]["name"];  ' ) );
              break;
            case 'page':
              add_action( 'admin_menu', create_function( '', ' global $menu, $submenu, $flawless; $menu[20][0] = $flawless["post_types"]["page"]["name"]; $submenu["edit.php?post_type=page"][5][0] = "All " . $flawless["post_types"]["page"]["name"];  ' ) );
              break;
          }

        }

        //** If this post type can have an archive, we determine the URL */
        //** @todo This nees work, we are guessing that the permalink will be top level, need to check other factors */
        if ( $wp_post_types[ $type ]->has_archive ) {

          add_filter( 'nav_menu_items_' . $type, array( __SELF__, 'add_archive_checkbox' ), null, 3 );

          $flawless[ 'post_types' ][ $type ][ 'archive_url' ] = get_bloginfo( 'url' ) . '/' . $type . '/';

        }

        //** Disable post type, and do work-around for built-in types since they are hardcoded into menu.*/
        if ( $flawless[ 'post_types' ][ $type ][ 'disabled' ] == 'true' ) {
          switch ( $type ) {
            case 'post':
              add_action( 'admin_menu', create_function( '', 'global $menu; unset( $menu[5] );' ) );
              break;
            case 'page':
              add_action( 'admin_menu', create_function( '', 'global $menu; unset( $menu[20] );' ) );
              break;
          }
          unset( $wp_post_types[ $type ] );
        }

      }

    }

    /**
     * {need description}
     *
     *
     * @since 0.0.2
     *
     */
    static function add_archive_checkbox( $posts, $args, $post_type ) {
      global $_nav_menu_placeholder, $wp_rewrite, $flawless;

      $_nav_menu_placeholder = ( 0 > $_nav_menu_placeholder ) ? intval( $_nav_menu_placeholder ) - 1 : -1;

      $archive_slug = $post_type[ 'args' ]->has_archive === true ? $post_type[ 'args' ]->rewrite[ 'slug' ] : $post_type[ 'args' ]->has_archive;

      if ( $post_type[ 'args' ]->rewrite[ 'with_front' ] ) {
        $archive_slug = substr( $wp_rewrite->front, 1 ) . $archive_slug;
      } else {
        $archive_slug = $wp_rewrite->root . $archive_slug;
      }

      array_unshift( $posts, ( object ) array(
        'ID' => 0,
        '_add_to_top' => true,
        'object_id' => $_nav_menu_placeholder,
        'post_content' => '',
        'post_excerpt' => '',
        'custom_thing' => 'hola',
        'post_title' => sprintf( __( '%1s Archive Root', 'flawless' ), $post_type[ 'args' ]->labels->all_items ),
        'post_type' => 'nav_menu_item',
        'type' => 'custom',
        'url' => site_url( $archive_slug ),
      ) );

      return $posts;

    }

  }

}