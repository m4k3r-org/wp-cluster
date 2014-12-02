<?php
/**
 * Festival Core
 *
 * @version 0.1.0
 * @author Usability Dynamics
 * @namespace UsabilityDynamics
 */
namespace UsabilityDynamics {

	Use UsabilityDynamics\UI;
	Use UsabilityDynamics\Structure;
	Use UsabilityDynamics\Shortcode;
	Use UsabilityDynamics\Theme\Scaffold;

  /**
   * Festival Theme
   *
   * @author Usability Dynamics
   */
  class Festival2 extends Scaffold {

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
     * @property domain
     * @var string
     */
    public $domain = null;

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
    public $carrington;

    /**
     * Class Initializer
     *
     *    http://umesouthpadre.com/manage/admin-ajax.php?action=main
     *    http://umesouthpadre.com/manage/admin-ajax.php?action=festival.model
     *    http://umesouthpadre.com/manage/admin-ajax.php?action=main
     *
     *
     * @example
     *
     *    // JavaScript
     *    require( 'festival.model' )
     *    require( 'festival.settings' )
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function __construct() {

      // Setup our lib-meta-box path if it's not set
      if( !defined( 'RWMB_URL' ) ){
        define( 'RWMB_URL', get_template_directory_uri() . '/vendor/plugins/wp-meta-box/' );
      }

      // Add this filter now, because otherwise it won't catch
      add_filter( 'cfct-build-url', array( $this, 'plugins_url' ), 10, 2 );

      // Configure Properties.
      $this->id      = Utility::create_slug( __NAMESPACE__ . ' festival', array( 'separator' => ':' ));
      $this->domain  = Utility::create_slug( __NAMESPACE__ . ' festival', array( 'separator' => '-' ));
      $this->version = wp_get_theme()->get( 'Version' );

      // Initialize Settings.
      $this->initialize( array(
        'key'       => 'festival',
        'version'   => $this->version
      ));

      // Register Custom Post Types, meta and set their taxonomies
      $this->structure = Structure::define( $this->get_schema( '/static/schemas/schema.structure.json' ) );

      // Set Theme UI
      if( class_exists( '\UsabilityDynamics\UI\Settings' ) ) {
        $this->ui = new UI\Settings( $this->settings, $this->get_schema( '/static/schemas/schema.ui.json' ) );
      }

      // Configure API Methods.
      $this->api( array(
        'search.AutoSuggest'   => array(),
        'search.ElasticSearch' => array(),
        'search.Elastic'       => array(),
        'search.DynamicFilter' => array()
      ));

      // Configure Image Sizes.
      $this->media( array(
        'post-thumbnail' => array(
          'description' => __( 'Standard Thumbnail.' ),
          'width'       => 230,
          'height'      => 130,
          'crop'        => true
        ),
        'gallery'        => array(
          'description' => __( 'Gallery Image Thumbnail.' ),
          'post_types'  => array( 'page', 'artist' ),
          'width'       => 300,
          'height'      => 170,
          'crop'        => false
        ),
        'tablet'         => array(
          'description' => __( 'Tablet Maximum Resolution.' ),
          'post_types'  => array( '_aside' ),
          'width'       => 670,
          'height'      => 999,
          'crop'        => true
        )
      ));

      // Declare Supported Theme Features.
      $this->supports( array(
        'admin-bar'         => array(
          'callback' => '__return_false'
        ),
        'asides'            => array(
          'header',
          'banner',
          'footer'
        ),
        'html5'             => array(
          'main',
          'nav',
          'comment-list',
          'comment-form',
          'search-form'
        ),
        'attachment:audio'  => array(
          'enabled' => true
        ),
        'attachment:video'  => array(
          'enabled' => true
        ),
        'custom-background' => array(
          'default-color' => '',
          'default-image' => '',
          'wp-head-callback' => '__return_false'
        ),
        'post-thumbnails'   => array(
          'event',
          'artist',
          'page',
          'post'
        )
      ));

      // Head Tags.
      $this->head( array(
        array(
          'tag'     => 'meta',
          'name'    => 'apple-mobile-web-app-status-bar-style',
          'content' => 'black'
        ),
        array(
          'tag'     => 'meta',
          'name'    => 'apple-mobile-web-app-capable',
          'content' => 'yes'
        ),
        array(
          'tag'     => 'meta',
          'name'    => 'HandheldFriendly',
          'content' => 'True'
        ),
        array(
          'tag'     => 'meta',
          'name'    => 'MobileOptimized',
          'content' => '360'
        ),
        array(
          'tag'     => 'meta',
          'name'    => 'viewport',
          'content' => 'width=device-width, initial-scale=1.0'
        ),
/*        array(
          'tag'     => 'link',
          'rel'     => 'apple-touch-icon',
          'href'    => content_url( '/apple-touch-icon-72x72.png' )
        ),
        array(
          'tag'     => 'link',
          'rel'     => 'apple-touch-startup-image',
          'href'    => content_url( '/apple-touch-icon-72x72.png' )
        ),
        array(
          'tag'     => 'link',
          'rel'     => 'apple-touch-icon',
          'href'    => content_url( '/apple-touch-icon-72x72.png' ),
          'sizes'   => '72x72'
        ),
        array(
          'tag'     => 'link',
          'rel'     => 'apple-touch-icon',
          'href'    => content_url( '/apple-touch-icon-114x114.png' ),
          'sizes'   => '114x114'
        ),
        array(
          'tag'     => 'link',
          'href'    => content_url( '/apple-touch-icon-72x72.png' )
        ),
        array(
          'tag'     => 'link',
          'rel'     => 'shortcut icon',
          'href'    => content_url( '/favicon.png' )
        ),*/
        array(
          'tag'     => 'link',
          'rel'     => 'api',
          'href'    => admin_url( 'admin-ajax.php' )
        ),
        array(
          'tag'     => 'link',
          'rel'     => 'profile',
          'href'    => 'http://gmpg.org/xfn/11'
        ),
        array(
          'tag'     => 'link',
          'rel'     => 'pingback',
          'href'    => get_bloginfo( 'pingback_url' )
        )
      ));

      //** Enables Customizer for Options. */
      $this->customizer();

      // Enable Carrington Build.
      $this->carrington( array(
        'bootstrap'          => true,
        'templates'          => true,
        'styles'             => array(),
        'module_directories' => array(
          __DIR__ . '/modules'
        ),
        'post_types'         => array(
          'page',
          'post',
          'artist',
          '_aside'
        )
      ) );

      // Register Navigation Menus
      $this->menus( array(
        'primary' => array(
          'name' => __( 'Primary', $this->domain )
        )
      ));

      // Define Dynamic Aside Sections.
      $this->sections( array(
        'header' => array(
          'title'       => __( 'Header', $this->domain ),
          'description' => __( 'Header Section', $this->domain ),
        ),
        'front_header_inside' => array(
          'title'       => __( 'Front Page Header', $this->domain ),
          'description' => __( 'Front Page Header', $this->domain ),
        ),
        'overlay_tickets' => array(
          'title'       => __( 'Ticket Overlay', $this->domain ),
          'description' => __( 'Holds the tickets overlay content', $this->domain )
        ),
        'header-widgets' => array(
          'title' => __( 'Header', $this->domain ),
          'description' => __( 'Header Widget Area', $this->domain ),
          'sidebar' => true,
          'options' => array(
            'id' => 'header-widgets',
            'class' => '',
            'before' => '',
            'after' => ''
          )
        ),
        'left-sidebar' => array(
          'title'       => __( 'Left Sidebar', $this->domain ),
          'description' => __( 'Shown on Pages with specific template.' ),
          'sidebar' => true,
          'options' => array(
            'id'      => 'left-sidebar-section',
            'class'   => 'left-sidebar-section col-md-3 section',
            'before'  => '<div class="module widget %1$s %2$s"><div class="module-inner">',
            'after'   => '</div></div>',
          )
        ),
        'right-sidebar' => array(
          'title'       => __( 'Right Sidebar', $this->domain ),
          'description' => __( 'Right widget area.', $this->domain ),
          'sidebar' => true,
          'options'     => array(
            'id'      => 'right-sidebar-section',
            'class'   => 'right-sidebar-section col-md-3 section',
            'before'  => '<div class="module widget %1$s %2$s"><div class="module-inner">',
            'after'   => '</div></div>'
          )
        ),
        'footer' => array(
          'title' => __( 'Footer' ),
          'description' => __( 'Footer Section.' ),
          'options'     => array(
            'type' => 'swiper'
          )
        ),
        'below-single-content' => array(
          'title' => __( 'Below Single Content' ),
          'description' => __( 'Below Single Content Section.' ),
        ),
					'hotel-header-widget' => array(
							'title' => __( 'Hotel Header Aside' ),
							'description' => __( 'Header Aside for the hotel page.' ),
					)
      ));

      // Set Pluggable Module Directory.
      $this->modules( __DIR__ . '/modules' );

      // Core Actions
      add_action( 'init', array( $this, 'init' ), 100 );
      add_action( 'template_redirect', array( $this, 'redirect' ), 100 );
      add_action( 'admin_init', array( $this, 'admin' ));
      add_action( 'get_model', array( $this, 'admin_menu' ));
      add_action( 'widgets_init', array( $this, 'widgets' ), 100 );
      add_action( 'wp_print_scripts', array( $this, 'wp_print_scripts' ), 100 );
      add_action( 'wp_print_styles', array( $this, 'wp_print_styles' ), 100 );
      add_action( 'wp_footer', array( $this, 'wp_footer' ), 999999999 );

      // Actions for the dynamic less compilation
      add_action( 'wp_ajax_wp_festival_compile_less', array( $this, 'compile_less' ) );
      add_action( 'wp_ajax_nopriv_wp_festival_compile_less', array( $this, 'compile_less' ) );

      // Remove URL from comments form
      add_filter( 'comment_form_default_fields', array( $this, 'url_filtered' ) );

      // Alter gallery container to load masonry
      add_filter( 'gallery_style', array( $this, 'gallery_attributes' ));
      add_filter( 'post_gallery', array( $this, 'get_gallery_template' ), 10, 2 );

      // Auto-wrap videos with container to make them responsive
      add_filter( 'embed_oembed_html', array( $this, 'wrap_video' ), 99, 4);

      add_action( 'init', array( $this, 'infinite_scroll_init' ));

    }

    function infinite_scroll_init(){

      add_theme_support( 'infinite-scroll', array(
        'container' => 'blog',
        'footer' => 'blog-loop',
        'wrapper' => false,
        'posts_per_page' => 3,
        'render' => array( $this, 'infinite_template_part' )
      ) );

    }

    public function infinite_template_part(){

      if( have_posts() ){
        $i = 0;
        while( have_posts() ){
          the_post();
          $i++;
          get_template_part( 'templates/article/content', $this->get_query_template() );

          if( $i % 3 == 0 ) echo '<div class="clearfix hidden-sm hidden-xs hidden-md"></div>';
        }

      }

    }

    /**
     * Handles removing all extra scripts
     */
    public function wp_print_scripts(){
      global $wp_scripts;
      $disallowed = array(
        'admin' => array(),
        'frontend' => array(
          'jquery-ui-accordion',
          'media-editor',
          'media-audiovideo',
          'knockout',
          'devicepx',
          'wp-mediaelement'
        )
      );
      if( is_admin() ){
        $disallowed = $disallowed[ 'admin' ];
      }else{
        $disallowed = $disallowed[ 'frontend' ];
      }
      foreach( $disallowed as $disallow ){
        foreach( $wp_scripts->queue as $key => $value ){
          if( $disallow == $value ){
            unset( $wp_scripts->queue[ $key ] );
          }
        }
      }
    }

    /**
     * Handles removing all extra styles
     */
    public function wp_print_styles(){
      global $wp_styles;
      $disallowed = array(
        'admin' => array(),
        'frontend' => array(
          'media-views',
          'imgareaselect',
          'wp-mediaelement'
        )
      );
      if( is_admin() ){
        $disallowed = $disallowed[ 'admin' ];
      }else{
        $disallowed = $disallowed[ 'frontend' ];
      }
      foreach( $disallowed as $disallow ){
        foreach( $wp_styles->queue as $key => $value ){
          if( $disallow == $value ){
            unset( $wp_styles->queue[ $key ] );
          }
        }
      }
    }

    /**
     * Our function to print our requireJS stuff
     */
    public function wp_footer(){
      /** First thing we're going to do is print our require config inline */
      $file = get_stylesheet_directory() . '/static/scripts/src/components/require.config.js';
      if( file_exists( $file ) && is_readable( $file ) ){
        /** Replace new line and extra spaces */
        $js = preg_replace( '/[\t\n]/', '', file_get_contents( $file ) );
        while( preg_match_all( '/  /', $js ) > 0 ){
          $js = preg_replace( '/  /', ' ', $js );
        }
        /** Replace the base URL */
        $js = preg_replace( '/"baseUrl": "([^"]*)"/', '"baseUrl": "' . get_stylesheet_directory_uri() . '/$1"', $js );
        /** Ok, we can go ahead and print our script */ ?>
        <script type="text/javascript" language="javascript"><?php echo $js; ?></script> <?php
      }
      /** Now we determine what JS file to print - based on script debug */
      if( SCRIPT_DEBUG ){
        $file = '/static/scripts/src/app.js';
      }else{
        $file = '/static/scripts/app.js';
      } ?>
      <script type="text/javascript" data-main="<?php echo str_ireplace( home_url(), '', get_template_directory_uri() ) . $file; ?>" src="http://cdn.udx.io/udx.requires.js"></script> <?php
    }

    public function get_gallery_template( $attr ){

      global $post;

      static $instance = 0;
      $instance++;

      // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
      if( isset( $attr[ 'orderby' ] ) ){
        $attr[ 'orderby' ] = sanitize_sql_orderby( $attr[ 'orderby' ] );
        if( !$attr[ 'orderby' ] ) unset( $attr[ 'orderby' ] );
      }

      extract( shortcode_atts( array(
        'order' => 'ASC',
        'orderby' => 'menu_order ID',
        'id' => $post->ID,
//        'itemtag' => 'dl',
//        'icontag' => 'dt',
//        'captiontag' => 'dd',
        'columns' => 3,
        'size' => 'thumbnail',
        'include' => '',
        'exclude' => ''
      ), $attr ) );

      $id = intval( $id );

      if( 'RAND' == $order ) $orderby = 'none';

      if( !empty( $include ) ){
        $include = preg_replace( '/[^0-9,]+/', '', $include );
        $_attachments = get_posts( array(
          'include' => $include,
          'post_status' => 'inherit',
          'post_type' => 'attachment',
          'post_mime_type' => 'image',
          'order' => $order,
          'orderby' => $orderby
        ) );

        $attachments = array();
        foreach( $_attachments as $key => $val ){
          $attachments[ $val->ID ] = $_attachments[ $key ];
        }
      } elseif( !empty( $exclude ) ){
        $exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
        $attachments = get_children( array(
          'post_parent' => $id,
          'exclude' => $exclude,
          'post_status' => 'inherit',
          'post_type' => 'attachment',
          'post_mime_type' => 'image',
          'order' => $order,
          'orderby' => $orderby
        ) );
      } else{
        $attachments = get_children( array(
          'post_parent' => $id,
          'post_status' => 'inherit',
          'post_type' => 'attachment',
          'post_mime_type' => 'image',
          'order' => $order,
          'orderby' => $orderby
        ) );
      }

      if( empty( $attachments ) ) return '';

      global $wp_query;

      $wp_query->data = $attachments;

      ob_start();

      get_template_part( 'templates/aside/gallery' );

      return ob_get_clean();

    }

    /**
     * It's used by Jetpack Infinite Loop
     * @see: http://jetpack.me/support/infinite-scroll/
     */
    public function get_infinite_template_part() {
      the_post();
      get_template_part( 'templates/article/content', $this->get_query_template() );
    }

    /**
     * Default page Navigation
     */
    function page_navigation() {
      // Don't print empty markup if there's only one page.
      if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
        return;
      }

      $paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
      $pagenum_link = html_entity_decode( get_pagenum_link() );
      $query_args   = array();
      $url_parts    = explode( '?', $pagenum_link );

      if ( isset( $url_parts[1] ) ) {
        wp_parse_str( $url_parts[1], $query_args );
      }

      $pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
      $pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

      $format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
      $format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

      // Set up paginated links.
      $links = paginate_links( array(
        'base'     => $pagenum_link,
        'format'   => $format,
        'total'    => $GLOBALS['wp_query']->max_num_pages,
        'current'  => $paged,
        'mid_size' => 1,
        'add_args' => array_map( 'urlencode', $query_args ),
        'prev_text' => __( 'Previous', $this->domain ),
        'next_text' => __( 'Next', $this->domain ),
      ) );

      if ( $links ) {
        ?>
        <nav class="navigation paging-navigation" role="navigation">
          <div class="pagination loop-pagination">
            <?php echo $links; ?>
          </div><!-- .pagination -->
        </nav><!-- .navigation -->
        <?php
      }
    }

    /**
     * Auto-wrap videos with container to make them responsive
     * @param type $html
     * @param type $url
     * @param type $attr
     * @param type $post_id
     * @return type
     *
     * @author unknown
     */
    function wrap_video( $html, $url, $attr, $post_id ) {
      return '<div class="video-container">' . $html . '</div>';
    }

    /**
     * Make standard gallery "masonarable"
     * @param type $current
     * @return type
     */
    public function gallery_attributes( $current ) {
      return preg_replace('/(id=\'gallery.+?\')/', '$1 data-requires="udx.ui.gallery"', $current);
    }

    /**
     * Remove URL from comments form
     * @param type $fields
     * @return type
     */
    public function url_filtered( $fields ) {
      if( isset( $fields['url'] ) ) {
        unset($fields['url']);
      }
      return $fields;
    }

    /**
     * Adds settings to customizer
     */
    public function customizer( $options = array() ) {
      //** Add Global JS and CSS handlers ( wp-amd ) */
      if( defined( 'WP_VENDOR_DIR' ) && file_exists( WP_VENDOR_DIR . '/usabilitydynamics/wp-amd/wp-amd.php' ) ) {
        include_once( WP_VENDOR_DIR . '/usabilitydynamics/wp-amd/wp-amd.php' );
      }
      if( class_exists( '\UsabilityDynamics\Festival2\Customizer' ) ) {
        $this->customizer = Festival2\Customizer::define( array(
          'text_domain' => $this->domain,
        ) );
      }
    }

    /**
     * On settings init we also merge structure with global network settings
     *
     */
    public function settings( $args = array(), $data = array() ) {
      parent::settings( $args, $data );
    }

    /**
     * Dynamically try to compile the sent less asset
     *
     * @todo Move into lib-utility, or similar. -potanin@UD
     *
     * @method compile_less
     * @note Only works on *nix systems right now
     * @author williams@UD
     */
    public function compile_less( $file ){

      try {

        if( !isset( $_REQUEST[ 'less_file' ] ) ) {
          throw new Exception( 'LESS file was not set.' );
        }

        $less_file = $_REQUEST[ 'less_file' ];
        if( file_exists( $less_file ) ) {
          throw new Exception( 'LESS file exists, you shouldn\'t be accessing this.' );
        }

        if( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' ) {
          throw new Exception( 'You\'re on Windows, you\'re going to have to fix this yourself for Windows: vendor/themes/wp-festival-2/lib/class-festival.php:~516.' );
        }

        /** Try to figure out the path to the less file */
        $theme_path = substr( __DIR__, 0, -4 ) . '/';
        $less_path  = $theme_path . 'static/styles/';
        $less_file  = str_ireplace( '.css', '.less', str_ireplace( $less_path, '', $less_file ) );
        $less_path  = $less_path . 'src/';
        $lessc_path = $theme_path . 'node_modules/less/bin/lessc';

        /** Ok, if we don't have lessc bail */
        if( !is_file( $lessc_path ) ) {
          throw new Exception( 'Could not find the lessc executable, please run npm install in wp-festival-2.' );
        }

        if( !is_file( $less_path . $less_file ) ) {
          throw new Exception( 'The LESS file specified does not exist: ' . $less_path . $less_file );
        }

        /** Ok, we made it, run the system command */
        $cmd = 'cd ' . $less_path . ';node ' . $lessc_path . ' -x ' . $less_file;
        header( 'Content-Type: text/css' );
        system( $cmd );

        die();

      } catch( Exception $e ) {
        http_response_code( 404 );
        echo "<h1>LESS File Could Not Be Compiled</h1><p>{$e->getMessage()}</p>";
        die();
      }

    }

    /**
     * Register Sidebars
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function widgets() {
      unregister_widget( 'WP_Widget_Recent_Comments' );
      unregister_widget( 'WP_Widget_RSS' );
      unregister_widget( 'WP_Widget_Calendar' );
      unregister_widget( 'WP_Widget_Tag_Cloud' );
      unregister_widget( 'WP_Widget_Meta' );
      unregister_widget( 'WP_Widget_Archives' );
      unregister_widget( 'WP_Widget_Categories' );
    }

    /**
     * Primary Frontend Hook
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function redirect() {

      //** Hack. De-Register jquery.spin ( used in Jetpack ) to prevent javascript errors. */
      if ( wp_script_is( 'jquery.spin', 'registered' ) ) {
        wp_deregister_script( 'jquery.spin' );
      } elseif ( wp_script_is( 'jquery.spin', 'enqueued' ) ) {
        wp_dequeue_script( 'jquery.spin' );
      }

      // Disable WP Gallery styles
      add_filter( 'use_default_gallery_style', function () {
        return false;
      } );

    }

    /**
     * Primary Admin Hook
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function admin() {

      // Init widget scripts and styles
      add_action( 'admin_enqueue_scripts', function (){
        wp_enqueue_script( 'feature-item-widget-admin', get_template_directory_uri() . '/lib/modules/feature-item/static/scripts/feature-item-widget-admin.js' );
        wp_enqueue_script( 'company-item-widget-admin', get_template_directory_uri() . '/lib/modules/company-item/static/scripts/company-item-widget-admin.js' );
        wp_enqueue_script( 'video-widget-admin', get_template_directory_uri() . '/lib/modules/video/static/scripts/video-admin.js' );
      } );

    }

    /**
     * Primary Hook
     *
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function init() {

      // Register Carrington Modules.
      if( is_object( $this->carrington ) ) {

        /** Overwrite some specific modules, as we'll be replacing them below */
        $this->carrington->deregisterModule( 'HeroModule' );
        $this->carrington->deregisterModule( 'LoopModule' );
        $this->carrington->deregisterModule( 'ImageModule' );

        /** Now do our custom ones, in folder order */
        $this->carrington->registerModule( 'UsabilityDynamics_Festival2_AdvancedHeroModule' );
        $this->carrington->registerModule( 'UsabilityDynamics_Festival2_ArtistListModule' );
        $this->carrington->registerModule( 'UsabilityDynamics_Festival2_CollapseModule' );
        $this->carrington->registerModule( 'UsabilityDynamics_Festival2_FestivalHeroModule' );
        $this->carrington->registerModule( 'UsabilityDynamics_Festival2_ImageModule' );
        $this->carrington->registerModule( 'UsabilityDynamics_Festival2_FestivalLoopModule' );
        /** Doesn't work $this->carrington->registerModule( 'UsabilityDynamics_Festival2_Widget_Posts_Slider' ); */
        $this->carrington->registerModule( 'UsabilityDynamics_Festival2_SectionBreakModule' );
        $this->carrington->registerModule( 'UsabilityDynamics_Festival2_SocialStreamModule' );
        $this->carrington->registerModule( 'UsabilityDynamics_Festival2_AdvancedGalleryModule' );
        $this->carrington->registerModule( 'UsabilityDynamics_Festival2_NewArtistListModule' );
      }

      // Disable the parent function from printing scripts
      $this->set( 'scripts.print', false );

      // Declare Dynamic / Public Scripts.
      $this->scripts( array( ) );

      // Tell our customzier to reregister our scripts
      $this->customizer->register_asset( array(
        'deps' => array(
          'app'
        ),
        'enqueue' => false,
        'force' => true
      ) );
      // Declare Public Styles.
      $this->styles( array(
        'app' => get_stylesheet_directory_uri() . '/static/styles/app.css'

      ) );

      // Automatically Load Shortcodes.
      $this->load_shortcodes();

      // Register Editor Style. @todo Fix this
      // add_editor_style( home_url( '/assets/styles/app-editor.css' ) );

      // Custom Hooks
      add_filter( 'wp_get_attachment_image_attributes', array( $this, 'wp_get_attachment_image_attributes' ), 10, 2 );
      add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 600 );
      add_filter( 'plugins_url', array( $this, 'plugins_url' ), 10, 2 );

    }

    /**
     * Enqueue Frontend Scripts/Styles
     *
     * @author Usability Dynamics
     * @since 2.0.0
     */
    public function wp_enqueue_scripts() {

      /** First, bring in the CSS */
      wp_enqueue_style( 'app' );

    }

    /**
     * Adds 'img-responsive' Bootstrap class to all images
     *
     * @param array $attr
     * @param type  $attachment
     *
     * @return array
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function wp_get_attachment_image_attributes( $attr, $attachment ) {
      $attr[ 'class' ] = trim( $attr[ 'class' ] . ' img-responsive' );

      return $attr;
    }

    /**
     * Return name of Navigation Menu
     *
     * @param string $slug
     *
     * @return string If menus not found, boolean false will be returned
     * @author Usability Dynamics
     * @since 0.1.0
     */
    public function get_menus_name( $slug ) {
      $cippo_menu_locations = (array) get_nav_menu_locations();
      $menu                 = get_term_by( 'id', (int) $cippo_menu_locations[ $slug ], 'nav_menu', ARRAY_A );

      return !empty( $menu[ 'name' ] ) ? $menu[ 'name' ] : false;
    }

    /**
     * Make RPC Request.
     *
     * @example
     *
     *      // Create Import Request.
     *      $_response = self::raasRequest( 'build.compileLESS', array(
     *        'asdf' => 'sadfsadfasdfsadf'
     *      ));
     *
     * @param string $method
     * @param array  $data
     *
     * @method raasRequest
     * @since 5.0.0
     *
     * @return array
     * @author potanin@UD
     */
    public function raasRequest( $method = '', $data = array() ) {

      include_once( ABSPATH . WPINC . '/class-IXR.php' );
      include_once( ABSPATH . WPINC . '/class-wp-http-ixr-client.php' );

      $client = new \WP_HTTP_IXR_Client( 'raas.udx.io', '/rpc/v1', 80, 20000 );

      // Set User Agent.
      $client->useragent = 'WordPress/3.7.1 WP-Property/3.6.1 WP-Festival/' . $this->version;

      // Request Headers.
      $client->headers = array(
        'authorization'    => 'Basic ' . $this->get( 'raas.token' ) . ':' . $this->get( 'raas.session', defined( 'NONCE_KEY' ) ? NONCE_KEY : null ),
        'x-request-id'     => uniqid(),
        'x-client-name'    => get_bloginfo( 'name' ),
        'x-client-token'   => $this->get( 'raas.client', defined( 'AUTH_KEY' ) ? AUTH_KEY : null ),
        'x-callback-token' => $this->get( 'raas.callback.token', md5( wp_get_current_user()->data->user_pass ) ),
        'x-callback-url'   => site_url( 'xmlrpc.php' ),
        'content-type'     => 'text/xml; charset=utf-8'
      );

      // Execute Request.
      $client->query( $method, $data );

      if( $client->error ) {
        return new \WP_Error( $client->error->code, $client->error->message );
      }

      // Return Message.
      $_result = isset( $client->message ) && isset( $client->message->params ) && is_array( $client->message->params ) ? $client->message->params[ 0 ] : array();

      return json_decode( json_encode( $_result ));

    }

    /**
     * Automatically Load Shortcodes.
     *
     */
    private function load_shortcodes() {
      // Inits shortcodes
      Shortcode\Utility::maybe_load_shortcodes( get_stylesheet_directory() . '/lib/shortcodes' );
    }

    /**
     * Determine if called method is stored in Utility class.
     * Allows to call \UsabilityDynamics\Festival2\Utility methods directly.
     *
     * @author peshkov@UD
     */
    public function __call( $name , $arguments ){
      if( !is_callable( '\UsabilityDynamics\Festival2\Utility', $name ) ){
        die( "Method $name is not found." );
      }
      return call_user_func_array( array( '\UsabilityDynamics\Festival2\Utility', $name ), $arguments );
    }

  }

}
