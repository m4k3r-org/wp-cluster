<?php
/**
 * Festival Core
 *
 * @version 0.1.0
 * @author Usability Dynamics
 * @namespace UsabilityDynamics
 */
namespace UsabilityDynamics\Festival {

  /**
   * Festival Theme
   *
   * @author Usability Dynamics
   */
  class Core extends \UsabilityDynamics\Theme\Scaffold {

    /**
     * Version of theme
     *
     * @public
     * @property version
     * @var string
     */
    public $version = null;

    /**
     * Textdomain String
     *
     * Parses namespace, should be something like "wpp-theme-festival"
     *
     * @public
     * @property text_domain
     * @var string
     */
    public $text_domain = null;

    /**
     * ID of instance, used for settings.
     *
     * Parses namespace, should be something like wpp:theme:festival
     *
     * @public
     * @property id
     * @var string
     */
    public $id = null;

    /**
     * Settings.
     *
     * @public
     * @property id
     * @var string
     */
    public $carringon;

    /**
     * Class Initializer
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function __construct() {

      // Get information about current theme
      $_theme_info = get_file_data( dirname( __DIR__ ) . '/style.css', array( 'version' => 'Version' ) );

      // Configure properties.
      $this->version = $_theme_info[ 'version' ];
      $this->id = Utility::create_slug( __NAMESPACE__ . ' festival', array( 'separator' => ':' ) );
      $this->text_domain = Utility::create_slug( __NAMESPACE__ . ' festival', array( 'separator' => '-' ) );

      // Configure Theme.
      $this->initialize( array(
        'minify'    => true,
        'obfuscate' => true
      ) );

      // Initialize Settings.
      $this->settings();
      
      //echo "<pre>"; print_r( $this->get() ); echo "</pre>"; die();

      // Declare Public Scripts.
      $this->scripts(array(
        'app' => get_stylesheet_directory() . '/scripts/app.js',
        'app.admin' => get_stylesheet_directory() . '/scripts/app.admin.js'
      ));

      // Declare Public Styles.
      $this->styles(array(
        'app' => get_stylesheet_directory() . '/styles/app.css',
        'app.admin' => get_stylesheet_directory() . '/styles/app.admin.css',
        'content' => get_stylesheet_directory() . '/styles/content.css'
      ));

      // Declare Public Models.
      $this->models(array(
        'theme'  => '{}',
        'locale' => '{}'
      ));

      // Configure Post Types and Meta.
      $this->structure( array(
        'artist' => array(
          'type' => 'post'
        ),
        'venue' => array(
          'type' => 'post'
        ),
        'location' => array(
          'type' => 'post'
        )
      ) );

      // Configure API Methods.
      $this->api( array(
        'search.Elastic'       => array(),
        'search.DynamicFilter' => array()
      ) );

      // Configure Image Sizes.
      $this->media(array(
        'post-thumbnail' => array(
          'description' => '',
          'width' => 120,
          'height' => 90,
          'crop' => true
        ),
        'gallery' => array(
          'description' => '',
          'width' => 200,
          'height' => 999,
          'crop' => false
        ),
        'sidebar_thumb' => array(
          'description' => '',
          'width' => 120,
          'height' => 100,
          'crop' => true
        )
      ));

      // Declare Supported Theme Features.
      $this->supports( array(
        'custom-header'        => array(),
        'custom-skins'         => array(),
        'custom-background'    => array(),
        'header-dropdowns'     => array(),
        'header-business-card' => array(),
        'frontend-editor'      => array()
      ) );

      // Enables Customizer for Options.
      $this->customizer( array(
        'background-color' => array(),
        'header-banner'    => array()
      ) );

      // Core Actions
      add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
      add_action( 'widgets_init', array( $this, 'widgets_init' ), 100 );
      add_action( 'template_redirect', array( $this, 'template_redirect' ), 100 );
      add_action( 'admin_init', array( $this, 'admin_init' ) );
      add_action( 'admin_menu', array( $this, 'admin_menu' ) );
      add_action( 'init', array( $this, 'init' ), 100 );
      add_action( 'wp_footer', array( $this, 'wp_footer' ) );
      add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
      add_action( 'wp_head', array( $this, 'wp_head' ) );

      // Disable WP Gallery styles
      add_filter( 'use_default_gallery_style', function () {
        return false;
      } );

    }

    /**
     * Initial Theme Setup
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function after_setup_theme() {

      // Make theme available for translation
      if( is_dir( get_template_directory() . '/static/languages' ) ) {
        load_theme_textdomain( $this->text_domain, get_template_directory() . '/static/languages' );
      }

      add_theme_support( 'html5' );

      add_theme_support( 'comment-list' );

      // Enable relative URLs
      add_theme_support( 'root-relative-urls' );

      // Enable URL rewrites
      add_theme_support( 'rewrites' );

      // Standard Bootstrap grid
      add_theme_support( 'bootstrap-grid' );

      // Enable Bootstrap's top navbar
      add_theme_support( 'bootstrap-top-navbar' );

      // Enable Bootstrap's thumbnails component on [gallery]
      add_theme_support( 'bootstrap-gallery' );

      // Enable /?s= to /search/ redirect
      add_theme_support( 'nice-search' );

      // Enable to load jQuery from the Google CDN
      add_theme_support( 'jquery-cdn' );

      // Add default posts and comments RSS feed links to <head>.
      add_theme_support( 'automatic-feed-links' );

      // This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
      add_theme_support( 'post-thumbnails' );

      // Register Navigation Menus
      register_nav_menu( 'primary', __( 'Primary Menu', $this->text_domain ) );

      //register_nav_menu( 'mobile', __( 'Mobile Menu', $this->text_domain ) );
      register_nav_menu( 'footer', __( 'Footer Menu', $this->text_domain ) );

      // Enable Carrington Build / Layout Engine
      //$this->layout_engine();

    }

    /**
     * Enable Carrington Build / Layout Engine
     *
     * author peshkov@UD
     */
    private function layout_engine() {

      $this->carringon = new Carrington();

    }

    /**
     * Register Sidebars
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function widgets_init() {

      register_sidebar( array(
        'name'          => __( 'Right Sidebar' ),
        'description'   => __( 'Default Sideber. Shown on pages with specific template and blog pages.' ),
        'id'            => 'right-sidebar',
        'before_widget' => '<section class="widget %1$s %2$s"><div class="widget-inner">',
        'after_widget'  => '</div></section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
      ) );

      register_sidebar( array(
        'name'          => __( 'Left Sidebar' ),
        'description'   => __( 'Shown on Pages with specific template.' ),
        'id'            => 'left-sidebar',
        'before_widget' => '<section class="widget %1$s %2$s"><div class="widget-inner">',
        'after_widget'  => '</div></section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
      ) );

      register_sidebar( array(
        'name'          => __( 'Single Page Sidebar' ),
        'description'   => __( 'Shown on all Single Pages.' ),
        'id'            => 'single-sidebar',
        'before_widget' => '<section class="widget %1$s %2$s"><div class="widget-inner">',
        'after_widget'  => '</div></section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
      ) );

    }

    /**
     * Primary Frontend Hook
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function template_redirect() {

    }

    /**
     * Primary Admin Hook
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function admin_init() {
    }

    /**
     * Primary Admin Hook
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function admin_menu() {

    }

    /**
     * Primary Hook
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function init() {

      // Register Custom Psot Types and set their taxonomies
      $this->register_post_types_and_taxonomies();

      // Sync 'Social Streams' data with social networks
      $this->sync_streams();

      // Register scripts
      wp_register_script( $this->text_domain . '-require', get_template_directory_uri() . '/scripts/require.js', array(), $this->version, true );

      // Register styles
      wp_register_style( $this->text_domain . '-app', defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? get_template_directory_uri() . '/styles/app.dev.css' : get_template_directory_uri() . '/styles/app.css', array(), $this->version, 'all' );

      // Register Color schema
      wp_register_style( $this->text_domain . '-color', get_template_directory_uri() . '/styles/' . $this->get( 'configuration.color_schema' ) . '.css', array( $this->text_domain . '-app' ), $this->version, 'all' );

      // Add custom editor styles
      add_editor_style( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'styles/editor-style.dev.css' : 'styles/editor-style.css' );

      // Custom Hooks
      add_filter( 'wp_get_attachment_image_attributes', array( $this, 'wp_get_attachment_image_attributes' ), 10, 2 );

    }

    /**
     * Registers post types and taxonomies
     *
     */
    private function register_post_types_and_taxonomies() {
      $post_types = array();
      foreach( (array) $this->get( 'post_types' ) as $post_type ) {
        $class = '\UsabilityDynamics\Festival\Post_Type_' . ucfirst( $post_type );
        if( class_exists( $class ) ) {
          $object = new $class;
          $post_types[ $object->object_type ] = $object;
        }
      }
      $this->set( 'post_types', $post_types );
    }

    /**
     * Sync 'Social Streams' data with social networks
     *
     */
    private function sync_streams() {

      // Enable Twitter
      if( class_exists( '\UsabilityDynamics\Festival\Sync_Twitter' ) ) {
        $tw = new Sync_Twitter(
          array(
            'id' => 'twitter',
            'interval' => false,
            'post_type' => 'social',
            'oauth' => array(
              'oauth_access_token' => '101485804-shGXjN0D43uU7CtCBHaML5K8uycHqgvEMd5gHtrY',
              'oauth_access_token_secret' => 'YcCOXWu1bidAv1APgRAd8ATNBl2UmTDXFkoGzicJny5aw',
              'consumer_key' => 'yZUAnH7GkJGtCVDpjD5w',
              'consumer_secret' => 'j8o75Fd5MUCtPYWCH9xV4X0AT8qPECcwdIpNl9sHCU',
            ),
            'request' => array(
              'screen_name' => 'UMESouthPadre',
            )
          )
        );
      }

    }

    /**
     * Frontend Footer
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function wp_footer() {

    }

    /**
     * Enqueue Frontend Scripts
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function wp_enqueue_scripts() {

      // Add filter to fix the require.js script tag
      add_filter( 'clean_url', array( __CLASS__, 'fix_requirejs_script' ), 11, 1 );

      // Require will load app.js and other Require.js modules
      wp_enqueue_script( $this->text_domain . '-require' );

      // Compiled styles which include Bootstrap and custom styles.
      wp_enqueue_style( $this->text_domain . '-app' );
      wp_enqueue_style( $this->text_domain . '-color' );

    }

    /**
     * Adds Require.js data attribute to script tag
     *
     * @param $url
     *
     * @return string
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function fix_requirejs_script( $url ) {
      if( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
        return strpos( $url, 'require.js' ) !== false ? "$url' data-main='" . get_template_directory_uri() . "/scripts/app.dev.js" : $url;
      }
      return strpos( $url, 'require.js' ) !== false ? "$url' data-main='" . get_template_directory_uri() . "/scripts/app.js" : $url;
    }

    /**
     * Frontend Header
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function wp_head() {

    }

    /**
     * Returns path to page's template
     *
     * @param bool $basename
     *
     * @return string
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function get_query_template( $basename = true ) {
      $object = get_queried_object();

      if     ( is_404()                           && $template = get_404_template()            ) :
      elseif ( is_search()                        && $template = get_search_template()         ) :
      elseif ( is_tax()                           && $template = get_taxonomy_template()       ) :
      elseif ( is_front_page()                    && $template = get_front_page_template()     ) :
      elseif ( is_home()                          && $template = get_home_template()           ) :
      elseif ( is_attachment()                    && $template = get_attachment_template()     ) :
      elseif ( is_single()                        && $template = get_single_template()         ) :
      elseif ( is_page()                          && $template = get_page_template()           ) :
      elseif ( is_category()                      && $template = get_category_template()       ) :
      elseif ( is_tag()                           && $template = get_tag_template()            ) :
      elseif ( is_author()                        && $template = get_author_template()         ) :
      elseif ( is_date()                          && $template = get_date_template()           ) :
      elseif ( is_archive()                       && $template = get_archive_template()        ) :
      elseif ( is_comments_popup()                && $template = get_comments_popup_template() ) :
      elseif ( is_paged()                         && $template = get_paged_template()          ) :
      else : $template = get_index_template();
      endif;

      $template = apply_filters( 'template_include', $template );

      if( $basename ) {
        $template = str_replace( '.php', '', basename( $template ) );
      }

      return $template;
    }

    /**
     * Adds 'img-responsive' Bootstrap class to all images
     *
     * @param array $attr
     * @param type $attachment
     * @return array
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function wp_get_attachment_image_attributes( $attr, $attachment ) {
      $attr[ 'class' ] = trim( $attr[ 'class' ] . ' img-responsive' );
      return $attr;
    }

    /**
     * Returns post image's url with required size.
     *
     * Examples:
     * 1) $festival->get_image_link_by_post_id( get_the_ID() ); // Returns Full image
     * 2) $festival->get_image_link_by_post_id( get_the_ID(), array( 'size' => 'medium' ) ); // Returns image with predefined size
     * 3) $festival->get_image_link_by_post_id( get_the_ID(), array( 'width' => '430', 'height' => '125' ) ); // Returns image with custom size
     *
     * @param int          $post_id
     * @param array|string $args
     *
     * @global array       $wpp_query
     * @return string Returns false if image can not be returned
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function get_image_link_by_post_id( $post_id, $args = array() ) {
      global $wpp_query;

      $args = (object) wp_parse_args( $args, $default = array(
        'size' => 'full', // Get image by predefined image_size. If width and height are set - it's ignored.
        'width' => '', // Custom size
        'height' => '', // Custom size
        // Optionals:
        'post_type' => false, // Different post types can have different default images
        'default' => true, // Use default image if images doesn't exist or not.
        'default_filetype' => 'jpg', // Filetype of default image. May be used in theme childs implementations
      ));

      if ( has_post_thumbnail( $post_id ) ) {
        $attachment_id = get_post_thumbnail_id( $post_id );
      } else {

        // Use default image if image for post doesn't exist
        if ( $default ) {

          $wp_upload_dir = wp_upload_dir();
          $dir = $wp_upload_dir[ 'basedir' ] . '/no_image/' . md5( $this->text_domain ) . '';
          $url = $wp_upload_dir[ 'baseurl' ] . '/no_image/' . md5( $this->text_domain ) . '';
          $path = $dir . '/' . $this->get( 'configuration.color_schema' ) . ( !empty( $post_type ) ? "-{$post_type}" : "" ) . '.' . $args->default_filetype;
          $default_path = get_template_directory() . '/images/no_image/' . basename( $path );
          $guid = $url . '/' . basename( $path );

          if( !is_dir( $dir ) ) {
            wp_mkdir_p( $dir );
          }

          // If attachment for default image doesn't exist
          if( !$attachment_id = \UsabilityDynamics\Utility::get_image_id_by_guid( $guid ) ) {
            // Determine if image exists. Check image by post_type at first if post_type is passed.\
            if( !file_exists( $default_path ) ) {
              return false;
            }
            if( !file_exists( $path ) ) {
              copy( $default_path, $path );
            }

            $wp_filetype = wp_check_filetype( basename( $path ), null );

            $attachment = array(
              'guid' => $guid,
              'post_mime_type' => $wp_filetype['type'],
              'post_title' => __( 'No Image', $this->text_domain ),
              'post_content' => '',
              'post_status' => 'inherit'
            );

            if( !$attachment_id = wp_insert_attachment( $attachment, $path ) ) {
              return false;
            }

            // image.php file must be included at first
            // for the function wp_generate_attachment_metadata() to work
            require_once( ABSPATH . 'wp-admin/includes/image.php' );

            $attachment_data = wp_generate_attachment_metadata( $attachment_id, $path );

            wp_update_attachment_metadata( $attachment_id, $attachment_data );

          }

        } else {
          return false;
        }
      }

      if( !empty( $width ) && !empty( $height ) ) {
        $_attachment = \UsabilityDynamics\Utility::get_image_link_with_custom_size( $attachment_id, $width, $height );
      } else {
        if( $args->size == 'full' ) {
          $_attachment = wp_get_attachment_image_src( $attachment_id, $args->size );
          $_attachment[ 'url' ] = $_attachment[0];
        } else {
          $_attachment = \UsabilityDynamics\Utility::get_image_link( $attachment_id, $args->size );
        }
      }

      return is_wp_error( $_attachment ) ? false : $_attachment[ 'url' ];
    }

    /**
     * Set/reset excerpt filters: excerpt_length, excerpt_more
     *
     * Example:
     * $wp_escalade->set_excerpt_filter( '30', 'length' ); // Set excerpt length = 30
     * the_excerpt(); // Excerpt's length will be 30.
     * $wp_escalade->set_excerpt_filter( false, 'length' ); // Reset applied above filter.
     *
     * @staticvar array $_function
     * @param mixed $val
     * @param string $filter Available values: length, more
     * @return boolean
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function set_excerpt_filter( $val = false, $filter = 'length' ) {
      static $_function = array( 'excerpt_length' => '', 'excerpt_more' => '' );

      if( !in_array( $filter, array( 'length', 'more' ) ) ) {
        return false;
      }

      $_filter = 'excerpt_' . $filter;

      if( has_action( $_filter, $_function[ $_filter ] ) ) {
        remove_filter( $_filter, $_function[ $_filter ] );
      }

      if( !$val ) {
        $_function[ $_filter ] = '';
        return true;
      }

      $_function[ $_filter ] = create_function( '$val', 'return "'. $val . '";' );

      add_filter( $_filter, $_function[ $_filter ] );

      return true;
    }

    /**
     * Return name of Navigation Menu
     *
     * @param string $slug
     * @return string If menus not found, boolean false will be returned
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function get_menus_name( $slug ) {
      $cippo_menu_locations = (array) get_nav_menu_locations();
      $menu = get_term_by( 'id', (int) $cippo_menu_locations[ $slug ], 'nav_menu', ARRAY_A );
      return !empty( $menu[ 'name' ] ) ? $menu[ 'name' ] : false;
    }

    /**
     * Prints styled bloginfo name
     *
     * @return type
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function the_bloginfo_name() {
      $name = get_bloginfo( 'name', 'display' );
      echo $name;
    }

  }

}
