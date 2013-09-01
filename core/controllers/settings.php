<?php
/**
 *
 */

namespace Flawless;

/**
 * Class Settings
 *
 * Migrated from WPP 2.0.
 *
 * @package Flawless
 */
class Settings {

  /**
   * @action after_setup_theme (10)
   *
   */
  function __construct() {
    global $wpp_asset, $wp_properties;

    //** Update WPP taxonomies */
    add_filter( 'wpp_taxonomies', array( __CLASS__, '_update_taxonomies' ) );

    //** Merge attribute classifications with default data */
    add_filter( 'wpp_attribute_classifications', array( __CLASS__, '_update_attribute_classifications' ) );

    //** Set all available assets after loading premium features to have ability to use filters on assets */
    add_action( 'wpp_init', array( __CLASS__, 'set_assets' ) );

    //** Get current WP-Property configuration */
    self::get();

    //echo "<pre>"; print_r( $wp_properties ); echo "</pre>"; die();

    //** Set Default Attribute Classification */
    reset( $wp_properties[ '_attribute_classifications' ] );
    define( 'WPP_Default_Classification', key( $wp_properties[ '_attribute_classifications' ] ) );

    //** Set Default Attribute's Group */
    reset( $wp_properties[ '_predefined_groups' ] );
    $default_group = !empty( $wp_properties[ 'configuration' ][ 'main_stats_group' ] ) ? $wp_properties[ 'configuration' ][ 'main_stats_group' ] : key( $wp_properties[ '_predefined_groups' ] );
    define( 'WPP_Default_Group', $default_group );

    $build_mode = isset( $wp_properties[ 'configuration' ][ 'build_mode' ] ) ? $wp_properties[ 'configuration' ][ 'build_mode' ] : false;
    $build_mode = ( $build_mode == 'true' ) ? true : false;

    //** Set assets compiler */
    $wpp_asset = new UD_Asset( array(
      //** Recompile if input file was changed */
      'monitor' => $build_mode,
      //** Prefix which will be used in dynamic asset's permalink */
      'prefix' => 'wpp',
      //** Required libraries */
      'pathes' => array(
        'lessc' => WPP_Path . 'third-party/lessphp/lessc.inc.php',
        'jsmin' => WPP_Path . 'third-party/jsmin.php',
      )
    ) );

  }

  /**
   * Saves configuration data - can be used via AJAX call.
   * $params roo keys may include: wp_options and wpp_settings
   *
   * @author potanin@UD
   */
  static function save( $params = false, $args = array() ) {
    global $wp_properties;

    $params = wp_parse_args( $params );

    $args = wp_parse_args( $args, array( 'verify_nonce' => true ) );

    if ( $args[ 'verify_nonce' ] && !wp_verify_nonce( $params[ '_wpnonce' ], 'wpp_setting_save' ) ) {
      return array( 'success' => false, 'message' => 'Validation fail' );
    }

    /* Saves any wp_option[] named fields into options table */
    foreach ( (array) $params[ 'wp_options' ] as $option_name => $option_value ) {
      update_option( $option_name, $option_value );
    }

    /* Protected Top Level Setting Keys - will be preserved if completely empty */
    $_protected = array( 'configuration', 'property_types' );

    $new_settings = $params[ 'wpp_settings' ];

    $new_settings = apply_filters( 'wpp_settings_save', $new_settings, $wp_properties );

    foreach ( (array) $_protected as $key ) {
      if ( empty( $new_settings[ $key ] ) ) {
        $new_settings[ $key ] = $wp_properties[ $key ];
      }
    }

    // Prevent removal of featured settings configurations if they are not present
    foreach ( (array) $wp_properties[ 'configuration' ][ 'feature_settings' ] as $feature_type => $preserved_settings ) {
      if ( empty( $params[ 'wpp_settings' ][ 'configuration' ][ 'feature_settings' ][ $feature_type ] ) ) {
        $new_settings[ 'configuration' ][ 'feature_settings' ][ $feature_type ] = $preserved_settings;
      }
    }

    /* Generate weekly backup */
    if ( time() - get_option( 'wpp_settings::last_backup', 0 ) > ( 60 * 60 * 24 * 7 ) && get_option( 'wpp_settings', false ) ) {
      add_option( 'wpp_settings::backup::' . time(), array_merge( array( '_version' => WPP_Version ), get_option( 'wpp_settings', array() ) ), '', 'no' );
      update_option( 'wpp_settings::last_backup', time() );
    }

    /** The only computed values added to the regular settings */
    $new_settings[ '_version' ] = WPP_Version;
    $new_settings[ '_updated' ] = time();

    update_option( 'wpp_settings', $new_settings );

    $wp_properties = self::get( array( 'recompute' => true ) );

    return array( 'success' => true, 'configuration' => $wp_properties );

  }

  /**
   * Returns properties configuration
   *
   * @since 2.0
   * @author potanin@UD
   */
  static function get( $args = array() ) {

    global $wp_properties;

    $wp_properties = get_option( 'wpp_settings', array() );

    $args = wp_parse_args( $args, array(
      'strip_protected_keys' => false,
      'stripslashes' => false,
      'sort' => false,
      'recompute' => ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ? false : $wp_properties[ 'configuration' ][ 'build_mode' ],
    ) );

    //** System WPP Settings */
    $system_settings = array();
    $trns = !$args[ 'recompute' ] ? get_transient( 'wpp::system_settings' ) : false;
    if ( !empty( $trns ) && $trns != '[]' ) {
      $system_settings = json_decode( $trns, true );
    } else {
      $system_settings = self::_localize( json_decode( file_get_contents( WPP_Schemas . '/system.settings.json' ), true ) );
      set_transient( 'wpp::system_settings', json_encode( $system_settings ), ( 60 * 60 * 24 ) );
    }

    $wp_properties = WPP_F::extend( array(
      'property_stats' => array(),
      'attribute_classification' => array(),
      'property_stats_descriptions' => array(),
      'admin_attr_fields' => array(),
      'searchable_attr_fields' => array(),
      'sortable_attributes' => array(),
      'searchable_attributes' => array(),
      'predefined_values' => array(),
      'predefined_search_values' => array(),
      'property_types' => array(),
      'property_groups' => array(),
      'property_stats_groups' => array(),
      'searchable_property_types' => array(),
      'hidden_attributes' => array(),
      'property_meta' => array(), // Depreciated element. It has not been used since 2.0 version
      'property_inheritance' => array(),
      'image_sizes' => array(),
    ), $system_settings, (array) $wp_properties );

    // Filters are applied
    $wp_properties[ 'configuration' ] = apply_filters( 'wpp_configuration', (array) ( !empty( $wp_properties[ 'configuration' ] ) ? $wp_properties[ 'configuration' ] : array() ) );
    $wp_properties[ 'taxonomies' ] = apply_filters( 'wpp_taxonomies', ( !empty( $wp_properties[ 'taxonomies' ] ) ? (array) $wp_properties[ 'taxonomies' ] : array() ) );
    $wp_properties[ 'property_stats_descriptions' ] = apply_filters( 'wpp_label_descriptions', (array) ( !empty( $wp_properties[ 'property_stats_descriptions' ] ) ? $wp_properties[ 'property_stats_descriptions' ] : array() ) );
    $wp_properties[ 'location_matters' ] = apply_filters( 'wpp_location_matters', (array) ( !empty( $wp_properties[ 'location_matters' ] ) ? $wp_properties[ 'location_matters' ] : array() ) );
    $wp_properties[ 'hidden_attributes' ] = apply_filters( 'wpp_hidden_attributes', (array) ( !empty( $wp_properties[ 'hidden_attributes' ] ) ? $wp_properties[ 'hidden_attributes' ] : array() ) );
    $wp_properties[ 'image_sizes' ] = apply_filters( 'wpp_image_sizes', (array) ( !empty( $wp_properties[ 'image_sizes' ] ) ? $wp_properties[ 'image_sizes' ] : array() ) );
    $wp_properties[ 'searchable_attributes' ] = apply_filters( 'wpp_searchable_attributes', (array) ( !empty( $wp_properties[ 'searchable_attributes' ] ) ? $wp_properties[ 'searchable_attributes' ] : array() ) );
    $wp_properties[ 'searchable_property_types' ] = apply_filters( 'wpp_searchable_property_types', (array) ( !empty( $wp_properties[ 'searchable_property_types' ] ) ? $wp_properties[ 'searchable_property_types' ] : array() ) );
    $wp_properties[ 'property_stats' ] = apply_filters( 'wpp_property_stats', (array) ( !empty( $wp_properties[ 'property_stats' ] ) ? $wp_properties[ 'property_stats' ] : array() ) );
    $wp_properties[ 'property_types' ] = apply_filters( 'wpp_property_types', (array) ( !empty( $wp_properties[ 'property_types' ] ) ? $wp_properties[ 'property_types' ] : array() ) );
    $wp_properties[ 'search_conversions' ] = apply_filters( 'wpp_search_conversions', (array) ( !empty( $wp_properties[ 'search_conversions' ] ) ? $wp_properties[ 'search_conversions' ] : array() ) );
    $wp_properties[ 'property_inheritance' ] = apply_filters( 'wpp_property_inheritance', (array) ( !empty( $wp_properties[ 'property_inheritance' ] ) ? $wp_properties[ 'property_inheritance' ] : array() ) );
    $wp_properties[ '_attribute_classifications' ] = apply_filters( 'wpp_attribute_classifications', (array) ( !empty( $wp_properties[ '_attribute_classifications' ] ) ? $wp_properties[ '_attribute_classifications' ] : array() ) );

    // Extend computed settings into WPP
    $wp_properties = WPP_F::extend( self::get_computed( array( 'recompute' => $args[ 'recompute' ] ) ), $wp_properties );

    if ( $args[ 'stripslashes' ] ) {
      $wp_properties = stripslashes_deep( $wp_properties );
    }

    if ( $args[ 'sort' ] ) {
      ksort( $wp_properties );
    }

    if ( $args[ 'strip_protected_keys' ] ) {
      $wp_properties = WPP_F::strip_protected_keys( $wp_properties );
    }

    if ( defined( 'WPP_DEBUG' ) && WPP_DEBUG ) {
      $wp_properties[ 'configuration' ][ 'developer_mode' ] = 'true';
    }

    //** Get rid of disabled attributes */
    if ( is_array( $wp_properties[ 'disabled_attributes' ] ) ) {
      foreach ( $wp_properties[ 'disabled_attributes' ] as $attribute ) {
        if ( array_key_exists( $attribute, $wp_properties[ 'property_stats' ] ) ) {
          if ( isset( $wp_properties[ 'property_stats' ][ $attribute ] ) ) {
            unset( $wp_properties[ 'property_stats' ][ $attribute ] );
          }
          if ( isset( $wp_properties[ 'attribute_classification' ][ $attribute ] ) ) {
            unset( $wp_properties[ 'property_stats' ][ $attribute ] );
          }
          if ( isset( $wp_properties[ 'property_stats_groups' ][ $attribute ] ) ) {
            unset( $wp_properties[ 'property_stats_groups' ][ $attribute ] );
          }
          if ( isset( $wp_properties[ 'property_stats_descriptions' ][ $attribute ] ) ) {
            unset( $wp_properties[ 'property_stats_descriptions' ][ $attribute ] );
          }
          if ( isset( $wp_properties[ 'searchable_attr_fields' ][ $attribute ] ) ) {
            unset( $wp_properties[ 'searchable_attr_fields' ][ $attribute ] );
          }
          if ( isset( $wp_properties[ 'admin_attr_fields' ][ $attribute ] ) ) {
            unset( $wp_properties[ 'admin_attr_fields' ][ $attribute ] );
          }
          if ( isset( $wp_properties[ 'predefined_values' ][ $attribute ] ) ) {
            unset( $wp_properties[ 'predefined_values' ][ $attribute ] );
          }
          if ( isset( $wp_properties[ 'predefined_search_values' ][ $attribute ] ) ) {
            unset( $wp_properties[ 'predefined_search_values' ][ $attribute ] );
          }
        }
      }
    }

    //** Set the list of frontend attributes */
    $wp_properties[ 'frontend_property_stats' ] = $wp_properties[ 'property_stats' ];
    //* System ( admin only ) attributes should not be showed. So we remove them from settings */
    foreach ( $wp_properties[ 'frontend_property_stats' ] as $i => $stat ) {
      if ( isset( $wp_properties[ 'attribute_classification' ][ $i ] ) ) {
        $classification = $wp_properties[ '_attribute_classifications' ][ $wp_properties[ 'attribute_classification' ][ $i ] ];
        if ( isset( $classification[ 'settings' ][ 'admin_only' ] ) && $classification[ 'settings' ][ 'admin_only' ] ) {
          unset( $wp_properties[ 'frontend_property_stats' ][ $i ] );
        }
      }
    }

    return $wp_properties;

  }

  /**
   * Generates Computed Settings, saves to transient, and returns.
   * By default recomputing is disabled on AJAX requests.
   *
   * @author potanin@UD
   */
  static function get_computed( $args = array() ) {

    global $wp_properties;

    $args = wp_parse_args( $args, array(
      'recompute' => ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ? false : $wp_properties[ 'configuration' ][ 'build_mode' ],
    ) );

    $trns = !$args[ 'recompute' ] ? get_transient( 'wpp::computed' ) : false;
    if ( !empty( $trns ) && $trns != '[]' ) {
      return json_decode( $trns, true );
    }

    /* Setup structure for system generated / API provided data */
    $_computed = array(
      '_computed' => time(),
      '_version' => WPP_Version,
      '_data_structure' => self::get_data_structure(),
      '_primary_keys' => array(
        'post_title' => sprintf( __( '%1$s Title', 'wpp' ), ucfirst( WPP_F::property_label( 'singular' ) ) ),
        'post_type' => __( 'Post Type' ),
        "post_content" => sprintf( __( '%1$s Content', 'wpp' ), ucfirst( WPP_F::property_label( 'singular' ) ) ),
        'post_excerpt' => sprintf( __( '%1$s Excerpt', 'wpp' ), ucfirst( WPP_F::property_label( 'singular' ) ) ),
        'post_status' => sprintf( __( '%1$s Status', 'wpp' ), ucfirst( WPP_F::property_label( 'singular' ) ) ),
        'menu_order' => sprintf( __( '%1$s Order', 'wpp' ), ucfirst( WPP_F::property_label( 'singular' ) ) ),
        'post_date' => sprintf( __( '%1$s Date', 'wpp' ), ucfirst( WPP_F::property_label( "singular" ) ) ),
        'post_author' => sprintf( __( '%1$s Author', 'wpp' ), ucfirst( WPP_F::property_label( "singular" ) ) ),
        'post_date_gmt' => '',
        'post_parent' => '',
        'ping_status' => '',
        'comment_status' => '',
        'post_password' => ''
      ),
      // Core Features may be disabled via some configurations
      '_core_features' => array( 'locations' => true, 'featured' => true ),
      // Consolidate all localization settings
      '_locale' => array(
        'thousands_sep' => $wp_properties[ 'configuration' ][ 'thousands_sep' ],
        'google_maps_localization' => $wp_properties[ 'configuration' ][ 'google_maps_localization' ],
        'currency_symbol' => $wp_properties[ 'configuration' ][ 'currency_symbol' ] ),
      '_queryable_keys' => array(), // @todo
      '_searchable_geo_parts' => self::get_searchable_geo_parts(),
    );

    $_computed = (array) apply_filters( 'wpp::computed', $_computed );

    set_transient( 'wpp::computed', json_encode( $_computed ), ( 60 * 60 * 24 ) );

    return $_computed;

  }

  /**
   * Returns default wpp settings based on system ( schema/system.settings.json ) settings and on user choice ( default schema/default.settings.json )
   *
   * @param array settings. Schema
   *
   * @author peshkov@UD
   */
  static function get_default_wpp_settings( $settings = false ) {

    //** STEP 1. Create the data based on system settings */

    $system = self::_localize( json_decode( file_get_contents( WPP_Schemas . '/system.settings.json' ), true ) );

    $wpp_settings = array(
      'configuration' => $system[ 'configuration' ],
      'property_stats' => array(),
      'attribute_classification' => array(),
      'property_stats_descriptions' => array(),

      'admin_attr_fields' => array(),
      'searchable_attr_fields' => array(),
      'sortable_attributes' => array(),
      'searchable_attributes' => array(),
      'column_attributes' => array(),
      'predefined_values' => array(),
      'predefined_search_values' => array(),

      'property_types' => array(),
      'searchable_property_types' => array(),
      'location_matters' => array(),

      'property_groups' => array(),
      'property_stats_groups' => array(),
    );

    //** Set default groups */
    foreach ( (array) $system[ '_predefined_groups' ] as $k => $v ) {
      $wpp_settings[ 'property_groups' ][ $v[ 'slug' ] ] = array(
        'name' => $v[ 'label' ]
      );
    }

    //** Begin to set default property attributes here */
    $predefined_attributes = (array) $system[ '_predefined_attributes' ];

    //** Add WP taxonomy 'category' to the predefined list */
    $taxonomies = array_merge( (array) $system[ 'taxonomies' ], array( 'category' => WPP_F::object_to_array( get_taxonomy( 'category' ) ) ) );

    //** Add other taxonomies to the predefined list */
    foreach ( $taxonomies as $taxonomy => $taxonomy_data ) {
      $predefined_attributes[ $taxonomy ] = array(
        'label' => $taxonomy_data[ 'label' ],
        'slug' => $taxonomy,
        'classification' => 'taxonomy',
        'description' => __( 'The current attribute is just a link to the existing taxonomy.', 'wpp' ),
        'meta' => true,
      );
    }

    foreach ( $predefined_attributes as $k => $v ) {
      if ( isset( $v[ 'meta' ] ) && $v[ 'meta' ] ) {
        $wpp_settings[ 'property_stats' ][ $v[ 'slug' ] ] = $v[ 'label' ];
        $wpp_settings[ 'attribute_classification' ][ $v[ 'slug' ] ] = $v[ 'classification' ];
        $wpp_settings[ 'property_stats_descriptions' ][ $v[ 'slug' ] ] = $v[ 'description' ];

        if ( !empty( $wpp_settings[ 'property_groups' ] ) ) {
          $wpp_settings[ 'property_stats_groups' ][ $v[ 'slug' ] ] = array_shift( array_keys( $wpp_settings[ 'property_groups' ] ) );
        }

        $clsf = !empty( $system[ '_attribute_classifications' ][ $v[ 'classification' ] ] ) ? $system[ '_attribute_classifications' ][ $v[ 'classification' ] ] : $system[ '_attribute_classifications' ][ 'string' ];

        $wpp_settings[ 'attribute_classification' ][ $v[ 'slug' ] ] = $clsf[ 'slug' ];

        if ( isset( $clsf[ 'settings' ][ 'editable' ] ) && $clsf[ 'settings' ][ 'editable' ] && !empty( $clsf[ 'admin' ] ) ) {
          $wpp_settings[ 'admin_attr_fields' ][ $v[ 'slug' ] ] = array_shift( array_keys( $clsf[ 'admin' ] ) );
        }

        if ( isset( $clsf[ 'settings' ][ 'searchable' ] ) && $clsf[ 'settings' ][ 'searchable' ] && !empty( $clsf[ 'search' ] ) ) {
          $wpp_settings[ 'searchable_attr_fields' ][ $v[ 'slug' ] ] = array_shift( array_keys( $clsf[ 'search' ] ) );
        }
      }
    }

    //** STEP 2. Merge with 'custom default' settings which can be got from specific json schema */

    //** Set Configuration */
    if ( isset( $settings[ 'configuration' ] ) ) {
      $wpp_settings[ 'configuration' ] = WPP_F::extend( $wpp_settings[ 'configuration' ], (array) $settings[ 'configuration' ] );
    }

    //** Set Property types */
    if ( isset( $settings[ 'types' ] ) ) {

      $default_type = array(
        'slug' => '',
        'label' => '',
        'description' => '',
        'searchable' => false,
        'location_matters' => false,
      );

      foreach ( (array) $settings[ 'types' ] as $k => $v ) {
        $v = WPP_F::extend( $default_type, $v );

        $wpp_settings[ 'property_types' ][ $v[ 'slug' ] ] = $v[ 'label' ];

        if ( $v[ 'searchable' ] ) {
          $wpp_settings[ 'searchable_property_types' ][ ] = $v[ 'slug' ];
        }

        if ( $v[ 'location_matters' ] ) {
          $wpp_settings[ 'location_matters' ][ ] = $v[ 'slug' ];
        }
      }
    }

    //** Set Groups */
    if ( isset( $settings[ 'groups' ] ) ) {
      $default_group = array(
        'slug' => '',
        'label' => '',
      );

      foreach ( $settings[ 'groups' ] as $k => $v ) {
        $v = WPP_F::extend( $default_group, ( isset( $wpp_settings[ '_predefined_groups' ][ $v[ 'slug' ] ] ) ? $wpp_settings[ '_predefined_groups' ][ $v[ 'slug' ] ] : array() ), $v );
        $wpp_settings[ 'property_groups' ][ $v[ 'slug' ] ] = array(
          'name' => $v[ 'label' ],
        );
      }
    }

    //** Set attributes */
    if ( isset( $settings[ 'attributes' ] ) ) {

      $default_attribute = array(
        'slug' => '',
        'label' => '',
        'description' => '',
        'classification' => 'string',
        'searchable' => false,
        'sortable' => false,
        'in_overview' => false,
        'search_input_type' => 'input',
        'admin_input_type' => 'input',
        'group' => !empty( $wpp_settings[ 'property_groups' ] ) ? array_shift( array_keys( (array) $wpp_settings[ 'property_groups' ] ) ) : false,
      );

      foreach ( (array) $settings[ 'attributes' ] as $k => $v ) {
        $v = WPP_F::extend( $default_attribute, ( isset( $predefined_attributes[ $v[ 'slug' ] ] ) ? $predefined_attributes[ $v[ 'slug' ] ] : array() ), $v );

        if ( !empty( $v[ 'slug' ] ) && !empty( $v[ 'label' ] ) ) {

          $wpp_settings[ 'property_stats' ][ $v[ 'slug' ] ] = $v[ 'label' ];
          $wpp_settings[ 'attribute_classification' ][ $v[ 'slug' ] ] = $v[ 'classification' ];
          $wpp_settings[ 'property_stats_descriptions' ][ $v[ 'slug' ] ] = $v[ 'description' ];

          if ( !empty( $v[ 'group' ] ) ) {
            $v[ 'group' ] = key_exists( $v[ 'group' ], $wpp_settings[ 'property_groups' ] ) ? $v[ 'group' ] : array_shift( array_keys( $wpp_settings[ 'property_groups' ] ) );
            $wpp_settings[ 'property_stats_groups' ][ $v[ 'slug' ] ] = $v[ 'group' ];
          }

          if ( $v[ 'searchable' ] && !in_array( $v[ 'slug' ], $wpp_settings[ 'searchable_attributes' ] ) ) {
            $wpp_settings[ 'searchable_attributes' ][ ] = $v[ 'slug' ];
          }

          if ( $v[ 'sortable' ] && !in_array( $v[ 'slug' ], $wpp_settings[ 'sortable_attributes' ] ) ) {
            $wpp_settings[ 'sortable_attributes' ][ ] = $v[ 'slug' ];
          }

          if ( $v[ 'in_overview' ] && !in_array( $v[ 'slug' ], $wpp_settings[ 'column_attributes' ] ) ) {
            $wpp_settings[ 'column_attributes' ][ ] = $v[ 'slug' ];
          }

          $clsf = !empty( $system[ '_attribute_classifications' ][ $v[ 'classification' ] ] ) ? $system[ '_attribute_classifications' ][ $v[ 'classification' ] ] : $system[ '_attribute_classifications' ][ 'string' ];

          $wpp_settings[ 'attribute_classification' ][ $v[ 'slug' ] ] = $clsf[ 'slug' ];

          if ( isset( $clsf[ 'settings' ][ 'editable' ] ) && $clsf[ 'settings' ][ 'editable' ] && !empty( $clsf[ 'admin' ] ) ) {
            $v[ 'admin_input_type' ] = !empty( $v[ 'admin_input_type' ] ) && key_exists( $v[ 'admin_input_type' ], $clsf[ 'admin' ] ) ? $v[ 'admin_input_type' ] : array_shift( array_keys( $clsf[ 'admin' ] ) );
            $wpp_settings[ 'admin_attr_fields' ][ $v[ 'slug' ] ] = $v[ 'admin_input_type' ];
          }

          if ( isset( $clsf[ 'settings' ][ 'searchable' ] ) && $clsf[ 'settings' ][ 'searchable' ] && !empty( $clsf[ 'search' ] ) ) {
            $v[ 'search_input_type' ] = !empty( $v[ 'search_input_type' ] ) && key_exists( $v[ 'search_input_type' ], $clsf[ 'search' ] ) ? $v[ 'search_input_type' ] : array_shift( array_keys( $clsf[ 'search' ] ) );
            $wpp_settings[ 'searchable_attr_fields' ][ $v[ 'slug' ] ] = $v[ 'search_input_type' ];
          }

        }
      }
    }

    return $wpp_settings;

  }

  /**
   * Returns the list of attributes belonging to geo classification which are searchable
   *
   * @return array
   * @author peshkov@UD
   */
  static function get_searchable_geo_parts() {
    $result = array();
    $data_sctructure = self::get_data_structure();
    foreach ( (array) $data_sctructure[ 'attributes' ] as $k => $v ) {
      if ( $v[ 'classification' ] == 'geo' && isset( $v[ 'searchable' ] ) && $v[ 'searchable' ] ) {
        $result[ $k ] = $v[ 'label' ];
      }
    }
    return $result;
  }

  /**
   * Return array of WPP attributes, groups and types structure.
   *
   * @todo Taxonomy counts and some sort of uniqueness / quality score. - potanin@UD 8/14/12
   * @author potanin@UD
   * @author peshkov@UD
   */
  static function get_data_structure( $args = false ) {
    global $wpdb, $wp_properties;

    //** STEP 1. Init all neccessary variables before continue. */

    $args = wp_parse_args( $args, array(
      'analyze_usage' => false,
    ) );

    //** Default classification */
    $def_cl_slug = 'string';
    $def_cl = !empty( $wp_properties[ '_attribute_classifications' ][ $def_cl_slug ] ) ? $wp_properties[ '_attribute_classifications' ][ $def_cl_slug ] : false;
    //** Classification Taxonomy */
    $def_cl_tax_slug = 'taxonomy';
    $def_cl_tax = !empty( $wp_properties[ '_attribute_classifications' ][ $def_cl_tax_slug ] ) ? $wp_properties[ '_attribute_classifications' ][ $def_cl_tax_slug ] : false;
    //** Default group */
    $def_group_slug = 'wpp_main';
    $def_group = !empty( $wp_properties[ '_predefined_groups' ][ $def_group_slug ] ) ? $wp_properties[ '_predefined_groups' ][ $def_group_slug ] : false;

    $default_attribute = array(
      'label' => '',
      'slug' => '',
      'description' => '',
      // Classification
      'classification' => !empty( $def_cl ) ? $def_cl_slug : false,
      'classification_label' => !empty( $def_cl ) ? $def_cl[ 'label' ] : false,
      'classification_settings' => !empty( $def_cl ) ? $def_cl[ 'settings' ] : false,
      // Specific data
      'type' => 'meta', // Available values: 'post', 'meta', 'taxonomy'
      'values' => false,
      'group' => !empty( $def_group ) ? $def_group_slug : false,
      'path' => false, // {group}.{attribute}
      'reserved' => false, // It's predefined by WPP or not
      'system' => false, // true if attribute is system data ( e.g. wp_posts data, like post_title, etc )
      'admin_inputs' => array(),
      'search_inputs' => array(),
      // Settings
      'searchable' => false,
      'sortable' => false,
      'in_overview' => false,
      'disabled' => false,
      'search_input_type' => false,
      'admin_input_type' => false,
      'search_predefined' => false,
      'admin_predefined' => false,
      'path' => false,
    );

    $default_group = array(
      'label' => '',
      'slug' => '',
      'reserved' => false,
    );

    $return = array(
      'attributes' => array(),
      'groups' => array(),
      'types' => array(),
    );

    //** STEP 2. Prepare the list of 'post column' attributes. These attributes are system and cannot be created or edited by user */
    $system_attributes = array();
    //** Add to system attributes all specific WP data ( wp_posts columns ) */
    $columns = $wpdb->get_results( "SELECT DISTINCT( column_name ) FROM information_schema.columns WHERE table_name = '{$wpdb->posts}'", ARRAY_N );
    foreach ( $columns as $column ) {
      $system_attributes[ ] = $column[ 0 ];
    }

    //** STEP 3. Prepare the list of predefined attributes ( Taxonomies also are related to this list ). These attributes cannot be created by user. */

    $predefined_attributes = !empty( $wp_properties[ '_predefined_attributes' ] ) ? $wp_properties[ '_predefined_attributes' ] : array();

    foreach ( $predefined_attributes as $k => $v ) {
      $predefined_attributes[ $k ][ 'type' ] = in_array( $k, $system_attributes ) ? 'post' : ( isset( $predefined_attributes[ $k ][ 'meta' ] ) && !$predefined_attributes[ $k ][ 'meta' ] ? 'post' : 'meta' );
      if ( isset( $v[ 'classification' ] ) && !empty( $wp_properties[ '_attribute_classifications' ][ $v[ 'classification' ] ] ) ) {
        $predefined_attributes[ $k ][ 'classification_label' ] = $wp_properties[ '_attribute_classifications' ][ $v[ 'classification' ] ][ 'label' ];
        $predefined_attributes[ $k ][ 'classification_settings' ] = $wp_properties[ '_attribute_classifications' ][ $v[ 'classification' ] ][ 'settings' ];
      }
      $predefined_attributes[ $k ] = array_merge( $default_attribute, $predefined_attributes[ $k ] );
    }

    $taxonomies = !empty( $wp_properties[ 'taxonomies' ] ) ? (array) $wp_properties[ 'taxonomies' ] : array();
    //** Add WP taxonomy 'category' to the predefined list */
    $taxonomies[ 'category' ] = WPP_F::object_to_array( get_taxonomy( 'category' ) );

    //** Add other taxonomies to the predefined list */
    foreach ( $taxonomies as $taxonomy => $taxonomy_data ) {
      $predefined_attributes[ $taxonomy ] = array_merge( $default_attribute, array_filter( array(
        'label' => $taxonomy_data[ 'label' ],
        'slug' => $taxonomy,
        'type' => 'taxonomy',
        'decription' => __( 'The current attribute is just a link to the existing taxonomy.', 'wpp' ),
        'classification' => !empty( $def_cl_tax ) ? $def_cl_tax_slug : false,
        'classification_label' => !empty( $def_cl_tax ) ? $def_cl_tax[ 'label' ] : false,
        'classification_settings' => !empty( $def_cl_tax ) ? $def_cl_tax[ 'settings' ] : false,
      ) ) );
    }

    //** STEP 4. Get the main list of all property attributes and merge them with system and predefined attributes */

    $attributes = self::get_total_attribute_array();

    foreach ( $attributes as $meta_key => $label ) {
      $_data = self::get_attribute_data( $meta_key );

      $default = array_key_exists( $meta_key, $predefined_attributes ) ? $predefined_attributes[ $meta_key ] : $default_attribute;

      $return[ 'attributes' ][ $meta_key ] = WPP_F::extend( $default, array_filter( array(
        'label' => $_data[ 'label' ],
        'slug' => $_data[ 'slug' ],
        'description' => isset( $_data[ 'description' ] ) ? $_data[ 'description' ] : false,
        'values' => !empty( $_data[ '_values' ] ) ? $_data[ '_values' ] : false,
        'group' => isset( $_data[ 'group_key' ] ) ? $_data[ 'group_key' ] : false,
        'reserved' => array_key_exists( $meta_key, $predefined_attributes ) ? true : false,
        'searchable' => isset( $_data[ 'searchable' ] ) ? $_data[ 'searchable' ] : false,
        'sortable' => isset( $_data[ 'sortable' ] ) ? $_data[ 'sortable' ] : false,
        'in_overview' => isset( $_data[ 'in_overview' ] ) ? $_data[ 'in_overview' ] : false,
        'disabled' => isset( $_data[ 'disabled' ] ) ? $_data[ 'disabled' ] : false,
        'search_input_type' => isset( $_data[ 'input_type' ] ) ? $_data[ 'input_type' ] : false,
        'admin_input_type' => isset( $_data[ 'data_input_type' ] ) ? $_data[ 'data_input_type' ] : false,
        'search_predefined' => isset( $_data[ 'predefined_search_values' ] ) ? $_data[ 'predefined_search_values' ] : false,
        'admin_predefined' => isset( $_data[ 'predefined_values' ] ) ? $_data[ 'predefined_values' ] : false,
        'path' => ( $_data[ 'group_key' ] ? $_data[ 'group_key' ] . '.' . $_data[ 'slug' ] : false ),
        'classification' => array_key_exists( $meta_key, $predefined_attributes ) ? $predefined_attributes[ $meta_key ][ 'classification' ] : false,
        'classification_label' => array_key_exists( $meta_key, $predefined_attributes ) ? $predefined_attributes[ $meta_key ][ 'classification_label' ] : false,
        'classification_settings' => array_key_exists( $meta_key, $predefined_attributes ) ? $predefined_attributes[ $meta_key ][ 'classification_settings' ] : false,
      ) ) );

      //** Set specific data based on system and predefined attributes */
      $return[ 'attributes' ][ $meta_key ][ 'type' ] = in_array( $meta_key, $system_attributes ) ? 'post' : 'meta';
      if ( array_key_exists( $meta_key, $predefined_attributes ) ) {
        $return[ 'attributes' ][ $meta_key ][ 'type' ] = $predefined_attributes[ $meta_key ][ 'type' ];
      } else {
        /* Check if the slug exists in classifications, if so, override classification's settings */
        $classification = !empty( $wp_properties[ 'attribute_classification' ][ $meta_key ] ) ? $wp_properties[ 'attribute_classification' ][ $meta_key ] : false;
        if ( $classification && isset( $wp_properties[ '_attribute_classifications' ][ $classification ] ) ) {
          $return[ 'attributes' ][ $meta_key ][ 'classification' ] = $classification;
          $return[ 'attributes' ][ $meta_key ][ 'classification_label' ] = $wp_properties[ '_attribute_classifications' ][ $classification ][ 'label' ];
          $return[ 'attributes' ][ $meta_key ][ 'classification_settings' ] = $wp_properties[ '_attribute_classifications' ][ $classification ][ 'settings' ];
        }
      }
    }

    foreach ( $predefined_attributes as $k => $v ) {
      if ( !array_key_exists( $k, $return[ 'attributes' ] ) ) {
        $return[ 'attributes' ][ $k ] = $predefined_attributes[ $k ];
      }
    }

    //** Set specific data based on classification and type, etc */
    foreach ( $return[ 'attributes' ] as $k => $v ) {
      if ( $v[ 'type' ] === 'post' ) {
        $return[ 'attributes' ][ $k ][ 'system' ] = true;
        $return[ 'attributes' ][ $k ][ 'reserved' ] = true;
      }
      if ( array_key_exists( $k, $predefined_attributes ) ) {
        $return[ 'attributes' ][ $k ][ 'reserved' ] = true;
      }
      if ( !empty( $wp_properties[ '_attribute_classifications' ][ $v[ 'classification' ] ][ 'admin' ] ) ) {
        foreach ( (array) $wp_properties[ '_attribute_classifications' ][ $v[ 'classification' ] ][ 'admin' ] as $slug => $label ) {
          $return[ 'attributes' ][ $k ][ 'admin_inputs' ][ ] = array( 'slug' => $slug, 'label' => $label );
        }
      }
      if ( !empty( $wp_properties[ '_attribute_classifications' ][ $v[ 'classification' ] ][ 'search' ] ) ) {
        foreach ( (array) $wp_properties[ '_attribute_classifications' ][ $v[ 'classification' ] ][ 'search' ] as $slug => $label ) {
          $return[ 'attributes' ][ $k ][ 'search_inputs' ][ ] = array( 'slug' => $slug, 'label' => $label );
        }
      }
    }

    //** STEP 5. ANALYZE USAGE. If Analyze usage is needed we update attributes data based on type */

    if ( $args[ 'analyze_usage' ] ) {
      foreach ( $return[ 'attributes' ] as $k => $v ) {
        switch ( $v[ 'type' ] ) {
          case 'post':
            //** Not sure what could be here. peshkov@UD */
            break;
          case 'meta':
            $return[ 'attributes' ][ $k ][ 'total_usage' ] = ( int ) $wpdb->get_var( $wpdb->prepare( "
              SELECT COUNT( post_id )
              FROM {$wpdb->postmeta}
              WHERE meta_key = %s
                AND meta_value != '';
            ", $meta_key ) );
            $return[ 'attributes' ][ $k ][ 'uniqueness' ] = ( int ) $wpdb->get_var( $wpdb->prepare( "
              SELECT COUNT(DISTINCT(meta_value ))
              FROM {$wpdb->postmeta}
              WHERE meta_key = %s
                AND meta_value !='';
            ", $meta_key ) );
            break;
          case 'taxonomy':
            $return[ 'attributes' ][ $k ][ 'values' ] = $wpdb->get_col( $wpdb->prepare( "
              SELECT name
              FROM {$wpdb->terms} t
                LEFT JOIN {$wpdb->term_taxonomy} tr ON t.term_id = tr.term_id
              WHERE tr.taxonomy = %s;
            ", $k ) );
            break;
        }
      }
    }

    //** STEP 6. Set property types and groups and return data */

    foreach ( (array) $wp_properties[ 'property_types' ] as $slug => $label ) {
      $return[ 'types' ][ $slug ] = array(
        'label' => $label,
        'slug' => $slug,
        'meta' => $wp_properties[ 'property_type_meta' ][ $slug ],
        'settings' => array(
          'geolocatable' => in_array( $slug, (array) $wp_properties[ 'location_matters' ] ) ? true : false,
          'searchable' => in_array( $slug, (array) $wp_properties[ 'searchable_property_types' ] ) ? true : false,
          'hierarchical' => in_array( $slug, (array) $wp_properties[ 'hierarchical_property_types' ] ) ? true : false,
        ),
        'hidden_attributes' => (array) $wp_properties[ 'hidden_attributes' ][ $slug ],
        'property_inheritance' => (array) $wp_properties[ 'property_inheritance' ][ $slug ],
      );
    }

    $predefined_groups = !empty( $wp_properties[ '_predefined_groups' ] ) ? $wp_properties[ '_predefined_groups' ] : array();
    if ( !empty( $wp_properties[ 'property_groups' ] ) ) {
      foreach ( (array) $wp_properties[ 'property_groups' ] as $group_slug => $data ) {
        $default = array_key_exists( $group_slug, $predefined_groups ) ? array_merge( $default_group, $predefined_groups[ $group_slug ] ) : $default_group;
        $return[ 'groups' ][ $group_slug ] = WPP_F::extend( $default, array_filter( array(
          'label' => $data[ 'name' ],
          'slug' => $group_slug,
        ) ) );
      }
    }
    foreach ( array_reverse( $predefined_groups ) as $k => $v ) {
      if ( !array_key_exists( $k, $return[ 'groups' ] ) ) {
        $return[ 'groups' ] = array( $k => array_merge( $default_group, $predefined_groups[ $k ] ) ) + $return[ 'groups' ];
      }
    }

    //echo "<pre>"; print_r( $return ); echo "</pre>"; die();

    return WPP_F::array_filter_deep( (array) $return );

  }

  /**
   * Return an array of all available attributes and meta keys
   *
   * @updated 1.36.1
   */
  static function get_total_attribute_array( $args = '', $extra_values = array() ) {
    global $wp_properties, $wpdb;

    extract( wp_parse_args( $args, array( 'use_optgroups' => 'false' ) ), EXTR_SKIP );

    $property_stats = $wp_properties[ 'property_stats' ];
    $property_groups = $wp_properties[ 'property_groups' ];
    $property_stats_groups = $wp_properties[ 'property_stats_groups' ];

    if ( $use_optgroups == 'true' ) {

      foreach ( (array) $property_stats as $key => $attribute_label ) {

        if ( $property_stats_groups[ $key ] ) {
          $_group_slug = $property_stats_groups[ $key ];
          if ( isset( $property_groups[ $_group_slug ] ) ) {
            $_group_label = $property_groups[ $_group_slug ][ 'name' ];
          }
        }

        $_group_label = $_group_label ? $_group_label : 'Attributes';
        $attributes[ $_group_label ][ $key ] = $attribute_label;

      }

      $attributes[ 'Other' ] = $extra_values;

      $attributes = array_filter( (array) $attributes );

      foreach ( (array) $attributes as $_group_label => $_attribute_data ) {
        asort( $attributes[ $_group_label ] );
      }

    } else {
      $attributes = (array) $property_stats + (array) $extra_values;
    }

    $attributes = apply_filters( 'wpp_total_attribute_array', $attributes );

    if ( !is_array( $attributes ) ) {
      $attributes = array();
    }

    return $attributes;

  }

  /**
   * Returns attribute information.
   * Checks $wp_properties and returns a concise array of array-specific settings and attributes
   *
   * @TODO: need to compare the current version with the previous one ( WPP 1.36.X ). peshkov@UD
   *
   * @updated 2.0
   * @version 1.17.3
   */
  static function get_attribute_data( $attribute = false ) {
    global $wpdb, $wp_properties;

    $return = array();

    if ( !$attribute ) {
      return false;
    }

    if ( wp_cache_get( $attribute, 'wpp_attribute_data' ) ) {
      return wp_cache_get( $attribute, 'wpp_attribute_data' );
    }

    //** Set post table keys ( wp_posts columns ) */
    $post_table_keys = array();
    $columns = $wpdb->get_results( "SELECT DISTINCT( column_name ) FROM information_schema.columns WHERE table_name = '{$wpdb->prefix}posts'", ARRAY_N );
    foreach ( $columns as $column ) {
      $post_table_keys[ ] = $column[ 0 ];
    }

    $ui_class = array( $attribute );

    $return[ 'storage_type' ] = in_array( $attribute, (array) $post_table_keys ) ? 'post_table' : 'meta_key';
    $return[ 'slug' ] = $attribute;

    if ( isset( $wp_properties[ 'property_stats_descriptions' ][ $attribute ] ) ) {
      $return[ 'description' ] = $wp_properties[ 'property_stats_descriptions' ][ $attribute ];
    }

    if ( isset( $wp_properties[ 'property_stats_groups' ][ $attribute ] ) ) {
      $return[ 'group_key' ] = $wp_properties[ 'property_stats_groups' ][ $attribute ];
      $return[ 'group_label' ] = $wp_properties[ 'property_groups' ][ $wp_properties[ 'property_stats_groups' ][ $attribute ] ][ 'name' ];
    }

    $return[ 'label' ] = $wp_properties[ 'property_stats' ][ $attribute ];
    $return[ 'classification' ] = !empty( $wp_properties[ 'attribute_classification' ][ $attribute ] ) ? $wp_properties[ 'attribute_classification' ][ $attribute ] : 'string';

    $return[ 'is_stat' ] = ( !empty( $wp_properties[ '_attribute_classifications' ][ $attribute ] ) && $wp_properties[ '_attribute_classifications' ][ $attribute ] != 'detail' ) ? 'true' : 'false';

    if ( $return[ 'is_stat' ] == 'detail' ) {
      $return[ 'input_type' ] = 'textarea';
    }

    $ui_class[ ] = 'classification_' . $return[ 'classification' ];

    if ( isset( $wp_properties[ 'searchable_attr_fields' ][ $attribute ] ) ) {
      $return[ 'input_type' ] = $wp_properties[ 'searchable_attr_fields' ][ $attribute ];
      $ui_class[ ] = 'search_' . $return[ 'input_type' ];
    }

    if ( is_admin() && isset( $wp_properties[ 'admin_attr_fields' ][ $attribute ] ) ) {
      $return[ 'data_input_type' ] = $wp_properties[ 'admin_attr_fields' ][ $attribute ];
      $ui_class[ ] = 'admin_' . $return[ 'data_input_type' ];
    }

    if ( $wp_properties[ 'configuration' ][ 'address_attribute' ] == $attribute ) {
      $return[ 'is_address_attribute' ] = 'true';
      $ui_class[ ] = 'address_attribute';
    }

    foreach ( (array) $wp_properties[ 'property_inheritance' ] as $property_type => $type_data ) {
      if ( in_array( $attribute, (array) $type_data ) ) {
        $return[ 'inheritance' ][ ] = $property_type;
      }
    }

    $ui_class[ ] = $return[ 'data_input_type' ];

    if ( is_array( $wp_properties[ 'predefined_values' ] ) && ( $predefined_values = $wp_properties[ 'predefined_values' ][ $attribute ] ) ) {
      $return[ 'predefined_values' ] = $predefined_values;
      $return[ '_values' ] = (array) $return[ '_values' ] + explode( ',', $predefined_values );
    }

    if ( is_array( $wp_properties[ 'predefined_search_values' ] ) && ( $predefined_values = $wp_properties[ 'predefined_search_values' ][ $attribute ] ) ) {
      $return[ 'predefined_search_values' ] = $predefined_values;
      $return[ '_values' ] = (array) $return[ '_values' ] + explode( ',', $predefined_values );
    }

    if ( is_array( $wp_properties[ 'sortable_attributes' ] ) && in_array( $attribute, (array) $wp_properties[ 'sortable_attributes' ] ) ) {
      $return[ 'sortable' ] = true;
      $ui_class[ ] = 'sortable';
    }

    if ( is_array( $wp_properties[ 'searchable_attributes' ] ) && in_array( $attribute, (array) $wp_properties[ 'searchable_attributes' ] ) ) {
      $return[ 'searchable' ] = true;
      $ui_class[ ] = 'searchable';
    }

    if ( is_array( $wp_properties[ 'column_attributes' ] ) && in_array( $attribute, (array) $wp_properties[ 'column_attributes' ] ) ) {
      $return[ 'in_overview' ] = true;
      $ui_class[ ] = 'in_overview';
    }

    if ( is_array( $wp_properties[ 'disabled_attributes' ] ) && in_array( $attribute, (array) $wp_properties[ 'disabled_attributes' ] ) ) {
      $return[ 'disabled' ] = true;
      $ui_class[ ] = 'disabled';
    }

    //** Legacy. numeric, boolean and currency params should not be used anywhere more. peshkov@UD */
    if ( $return[ 'classification' ] == 'admin_note' ) {
      $return[ 'hidden_frontend_attribute' ] = true;
      $ui_class[ ] = 'fe_hidden';
    } else if ( $return[ 'classification' ] == 'currency' ) {
      $return[ 'currency' ] = true;
      $return[ 'numeric' ] = true;
    } else if ( $return[ 'classification' ] == 'area' ) {
      $return[ 'numeric' ] = true;
    } else if ( $return[ 'classification' ] == 'boolean' ) {
      $return[ 'boolean' ] = true;
    } else if ( $return[ 'classification' ] == 'numeric' ) {
      $return[ 'numeric' ] = true;
    }

    if ( in_array( $attribute, array_keys( (array) $wp_properties[ '_predefined_attributes' ] ) ) ) {
      $return[ 'standard' ] = true;
      $ui_class[ ] = 'standard_attribute';
    }

    if ( empty( $return[ 'title' ] ) ) {
      $return[ 'title' ] = WPP_F::de_slug( $return[ 'slug' ] );
    }

    $ui_class = array_filter( array_unique( $ui_class ) );
    $ui_class = array_map( create_function( '$class', 'return "wpp_{$class}";' ), $ui_class );
    $return[ 'ui_class' ] = implode( ' ', $ui_class );

    if ( is_array( $return[ '_values' ] ) ) {
      $return[ '_values' ] = array_unique( $return[ '_values' ] );
    }

    $return = apply_filters( 'wpp_attribute_data', array_filter( $return ) );

    wp_cache_add( $attribute, $return, 'wpp_attribute_data' );

    return $return;

  }

  /**
   * Set all specific dynamic assets.
   *
   * @since 2.0
   * @version 1.0
   * @author peshkov@UD
   */
  static function set_assets() {
    global $wpp_asset;

    $uploads = wp_upload_dir();

    $less_default_variables = array(
      'version' => "'" . WPP_Version . "'",
      'wpp_url' => "'" . str_replace( array( 'http:', 'https:' ), '', trailingslashit( WPP_URL ) ) . "images'",
      'cdn_url' => "'" . str_replace( array( 'http:', 'https:' ), '', ( trailingslashit( defined( 'UD_CDN_URL' ) ? UD_CDN_URL : "//ud-cdn.com" ) ) ) . "assets'",
    );

    $assets = array(
      'admin_css' => array(
        'file' => trailingslashit( WPP_Path ) . 'css/wpp.admin.min.css',
        'type' => 'css',
        'compile_options' => array(
          'input' => trailingslashit( WPP_Path ) . 'css/wpp.admin.less',
          'variables' => $less_default_variables,
        )
      ),
      'global_js' => array(
        'file' => trailingslashit( WPP_Path ) . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'js/dev/wpp.global.js' : 'js/wpp.global.js' ),
        'type' => 'js',
        'compile_options' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? false : array( 'input' => array( trailingslashit( WPP_Path ) . 'js/dev/wpp.global.js' ) )
      ),
      'admin_js' => array(
        'file' => trailingslashit( WPP_Path ) . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'js/dev/wpp.admin.js' : 'js/wpp.admin.js' ),
        'type' => 'js',
        'compile_options' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? false : array( 'input' => array( trailingslashit( WPP_Path ) . 'js/dev/wpp.admin.js' ) )
      ),
      'admin_overview_js' => array(
        'file' => trailingslashit( WPP_Path ) . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'js/dev/wpp.admin.overview.js' : 'js/wpp.admin.overview.js' ),
        'type' => 'js',
        'compile_options' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? false : array( 'input' => array( trailingslashit( WPP_Path ) . 'js/dev/wpp.admin.overview.js' ) )
      ),
      'admin_settings_js' => array(
        'file' => trailingslashit( WPP_Path ) . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'js/dev/wpp.admin.settings.js' : 'js/wpp.admin.settings.js' ),
        'type' => 'js',
        'compile_options' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? false : array( 'input' => array( trailingslashit( WPP_Path ) . 'js/dev/wpp.admin.settings.js' ) )
      ),
      'admin_property_js' => array(
        'file' => trailingslashit( WPP_Path ) . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'js/dev/wpp.admin.property.js' : 'js/wpp.admin.property.js' ),
        'type' => 'js',
        'compile_options' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? false : array( 'input' => array( trailingslashit( WPP_Path ) . 'js/dev/wpp.admin.property.js' ) )
      ),
      'admin_widgets_js' => array(
        'file' => trailingslashit( WPP_Path ) . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'js/dev/wpp.admin.widgets.js' : 'js/wpp.admin.widgets.js' ),
        'type' => 'js',
        'compile_options' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? false : array( 'input' => array( trailingslashit( WPP_Path ) . 'js/dev/wpp.admin.widgets.js' ) )
      ),
      'admin_upgrade_js' => array(
        'file' => trailingslashit( WPP_Path ) . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'js/dev/wpp.admin.upgrade.js' : 'js/wpp.admin.upgrade.js' ),
        'type' => 'js',
        'compile_options' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? false : array( 'input' => array( trailingslashit( WPP_Path ) . 'js/dev/wpp.admin.upgrade.js' ) )
      ),
      'app_js' => array(
        'file' => trailingslashit( $uploads[ 'basedir' ] ) . 'wpp_assets/wpp_app.js',
        'type' => 'js',
        'compile_options' => array(
          'input' => array( trailingslashit( WPP_Path ) . 'js/dev/wpp.global.js', trailingslashit( WPP_Path ) . 'js/dev/wpp.frontend.js', )
        )
      ),
      'theme_default_css' => array(
        'file' => trailingslashit( $uploads[ 'basedir' ] ) . 'wpp_assets/wpp_theme_default.css',
        'type' => 'css',
        'compile_options' => array(
          'input' => trailingslashit( WPP_Path ) . 'templates/less/app.less',
          'variables' => WPP_F::extend( $less_default_variables, array(
            'theme' => "'default'",
            'legacy' => 'true',
          ) ),
        )
      ),
    );

    //** Get all available themes specific by parsing templates/less/themes directory. */
    $themes_specific = array();
    $dir = trailingslashit( WPP_Path ) . 'templates/less/themes';
    if ( is_dir( $dir ) ) {
      $files = scandir( $dir );
      foreach ( $files as $file ) {
        if ( !in_array( $file, array( '.', '..' ) ) && is_file( $dir . '/' . $file ) && strpos( $file, '.less' ) !== false ) {
          $themes_specific[ ] = str_replace( '.less', '', $file );
        }
      }
    }

    //** Set assets for all themes specific */
    foreach ( $themes_specific as $theme ) {
      $assets[ "theme_{$theme}_css" ] = array(
        'file' => trailingslashit( $uploads[ 'basedir' ] ) . 'wpp_assets/wpp_theme_' . $theme . '.css',
        'type' => 'css',
        'compile_options' => array(
          'input' => trailingslashit( WPP_Path ) . 'templates/less/app.less',
          'variables' => WPP_F::extend( $less_default_variables, array(
            'theme' => "'" . $theme . "'",
            'legacy' => 'true',
          ) ),
        )
      );
    }

    $assets = apply_filters( 'wpp::assets', $assets );

    if ( is_array( $assets ) ) {
      return $wpp_asset->set_assets( $assets );
    } else {
      return false;
    }
  }

  /**
   * Merges classifications with default settings
   * Updates search/admin input data
   *
   * @uses self::_update_input_types()
   * @global array $wp_properties
   *
   * @param array $classifications
   *
   * @return array
   * @author peshkov@UD
   * @since 2.0
   */
  static function _update_attribute_classifications( $classifications ) {
    global $wp_properties;

    if ( !is_array( $classifications ) ) return $classifications;

    foreach ( $classifications as $k => $v ) {
      $classifications[ $k ][ 'settings' ] = WPP_F::extend( array(
        'searchable' => true,
        'editable' => true,
        'admin_only' => false,
        'system' => false,
        'can_be_disabled' => false,
        'admin_predefined_values' => true,
        'search_predefined_values' => true,
      ), $v[ 'settings' ] );

      //** Update input types data */
      if ( isset( $v[ 'search' ] ) ) {
        $classifications[ $k ][ 'search' ] = self::_update_input_types( $v[ 'search' ] );
      }
      if ( isset( $v[ 'admin' ] ) ) {
        $classifications[ $k ][ 'admin' ] = self::_update_input_types( $v[ 'admin' ] );
      }
    }

    return $classifications;
  }

  /**
   * Updates search/admin input types data.
   *
   * @global array $wp_properties
   *
   * @param array $types
   *
   * @return array
   * @author peshkov@UD
   * @since 2.0
   */
  static function _update_input_types( $types ) {
    global $wp_properties;

    if ( !empty( $wp_properties[ '_input_types' ] ) && is_array( $types ) ) {
      $arr = array();
      foreach ( $types as $i => $label ) {
        if ( is_numeric( $i ) && key_exists( $label, (array) $wp_properties[ '_input_types' ] ) ) {
          $arr[ $label ] = (array) $wp_properties[ '_input_types' ][ $label ];
        } else {
          $arr[ $i ] = $label;
        }
      }
      $types = $arr;
    }
    return $types;
  }

  /**
   * Adds taxonomies based on property attributes ( where classification is taxonomy )
   *
   * @since 2.0
   * @author peshkov@UD
   */
  static function _update_taxonomies( $taxonomies ) {
    global $wp_properties;

    if ( is_array( $taxonomies ) && isset( $wp_properties[ '_data_structure' ][ 'attributes' ] ) ) {
      foreach ( (array) $wp_properties[ '_data_structure' ][ 'attributes' ] as $k => $v ) {
        if ( $v[ 'classification' ] === 'taxonomy' && !isset( $taxonomies[ $k ] ) ) {
          $taxonomies[ $k ] = array(
            'label' => $v[ 'label' ]
          );
        }
      }
    }

    return $taxonomies;
  }

  /**
   * Localization functionality.
   * Replaces array's l10n data.
   * Helpful for localization of data which is stored in JSON files ( see /schemas )
   *
   * @param type $data
   *
   * @return type
   * @since 2.0
   * @author peshkov@UD
   */
  static function _localize( $data ) {

    if ( !is_array( $data ) ) return $data;

    //** The Localization's list. */
    $l10n = apply_filters( 'wpp::config::l10n', array(
      //** System (wp_posts) data */
      'post_title' => sprintf( __( '%1$s Title', 'wpp' ), ucfirst( WPP_F::property_label( 'singular' ) ) ),
      'post_type' => __( 'Post Type' ),
      'post_content' => sprintf( __( '%1$s Content', 'wpp' ), ucfirst( WPP_F::property_label( 'singular' ) ) ),
      'post_excerpt' => sprintf( __( '%1$s Excerpt', 'wpp' ), ucfirst( WPP_F::property_label( 'singular' ) ) ),
      'post_status' => sprintf( __( '%1$s Status', 'wpp' ), ucfirst( WPP_F::property_label( 'singular' ) ) ),
      'menu_order' => sprintf( __( '%1$s Order', 'wpp' ), ucfirst( WPP_F::property_label( 'singular' ) ) ),
      'post_date' => sprintf( __( '%1$s Date', 'wpp' ), ucfirst( WPP_F::property_label( "singular" ) ) ),
      'post_author' => sprintf( __( '%1$s Author', 'wpp' ), ucfirst( WPP_F::property_label( "singular" ) ) ),
      'post_date_gmt' => sprintf( __( '%1$s Date GMT', 'wpp' ), ucfirst( WPP_F::property_label( "singular" ) ) ),
      'post_parent' => sprintf( __( '%1$s Parent', 'wpp' ), ucfirst( WPP_F::property_label( "singular" ) ) ),
      'ping_status' => __( 'Ping Status', 'wpp' ),
      'comment_status' => __( 'Comment\'s Status', 'wpp' ),
      'post_password' => __( 'Password', 'wpp' ),

      //** Attributes Groups */
      'main' => __( 'General Information', 'wpp' ),

      //** Attributes and their descriptions */
      'price' => __( 'Price', 'wpp' ),
      'price_desc' => __( 'Numbers only', 'wpp' ),
      'bedrooms' => __( 'Bedrooms', 'wpp' ),
      'bedrooms_desc' => __( 'Numbers only', 'wpp' ),
      'bathrooms' => __( 'Bathrooms', 'wpp' ),
      'bathrooms_desc' => __( 'Numbers only', 'wpp' ),
      'phone_number' => __( 'Phone Number', 'wpp' ),
      'phone_number_desc' => __( '', 'wpp' ),
      'address' => __( 'Address', 'wpp' ),
      'address_desc' => __( 'Used by google validator', 'wpp' ),
      'area' => __( 'Area', 'wpp' ),
      'area_desc' => __( 'Numbers only', 'wpp' ),
      'deposit' => __( 'Deposit', 'wpp' ),
      'deposit_desc' => __( 'Numbers only', 'wpp' ),
      'geo_location' => __( 'Geo Location', 'wpp' ),
      'taxonomy' => __( 'Taxonomy', 'wpp' ),

      //** Property Types */
      'single_family_home' => __( 'Single Family Home', 'wpp' ),
      'building' => __( 'Building', 'wpp' ),
      'floorplan' => __( 'Floorplan', 'wpp' ),
      'farm' => __( 'Farm', 'wpp' ),

      //** Input types */
      'field_input' => __( 'Free Text', 'wpp' ),
      'field_dropdown' => __( 'Dropdown Selection', 'wpp' ),
      'field_textarea' => __( 'Textarea', 'wpp' ),
      'field_checkbox' => __( 'Checkbox', 'wpp' ),
      'field_multi_checkbox' => __( 'Multi-Checkbox', 'wpp' ),
      'field_range_input' => __( 'Text Input Range', 'wpp' ),
      'field_range_dropdown' => __( 'Range Dropdown', 'wpp' ),
      'field_date' => __( 'Date Picker', 'wpp' ),
      'range_date' => __( 'Range Date Picker', 'wpp' ),

      //** Attributes Classifications */
      'short_text' => __( 'Short Text', 'wpp' ),
      'used_for_short_desc' => __( 'Best used for short phrases and descriptions', 'wpp' ),
    ) );

    //** Replace l10n entries */
    foreach ( $data as $k => $v ) {
      if ( is_array( $v ) ) {
        $data[ $k ] = self::_localize( $v );
      } elseif ( is_string( $v ) ) {
        if ( strpos( $v, 'l10n' ) !== false ) {
          preg_match_all( '/l10n\.([^\s]*)/', $v, $matches );
          if ( !empty( $matches[ 1 ] ) ) {
            foreach ( $matches[ 1 ] as $i => $m ) {
              if ( key_exists( $m, $l10n ) ) {
                $data[ $k ] = str_replace( $matches[ 0 ][ $i ], $l10n[ $m ], $data[ $k ] );
              }
            }
          }
        }
      }
    }

    return $data;
  }

}
