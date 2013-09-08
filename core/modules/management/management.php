<?php
/**
 * Name: Flawless Management
 * Description: The Management for the Flawless theme.
 * Author: Usability Dynamics, Inc.
 * Version: 1.0
 * Copyright 2010 - 2013 Usability Dynamics, Inc.
 *
 * @module Management
 * @namespace Flawless
 */
namespace Flawless {

  /**
   * Settings Management.
   *
   * @todo Disabled Features toggler checkboxes are not being rendered when a feature is disabled. - potanin@UD 5/30/12
   *
   * @class Management
   * @extends Module
   * @static
   */
  class Management extends Module {

    function __construct() {
      add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ), 10 );

      //** Load back-end JS and Contextual Help */
      add_action( 'admin_print_footer_scripts', array( __CLASS__, 'admin_print_footer_scripts' ), 10 );

      add_action( 'flawless::admin_menu', array( __CLASS__, 'admin_menu' ), 10 );

      add_action( 'flawless::theme_setup::after', array( __CLASS__, 'theme_setup' ), 10 );

    }

    /**
     *
     * @method theme_setup
     */
    static function theme_setup() {

      add_theme_support( 'custom-background', array(
        'wp-head-callback' => array( __CLASS__, 'custom_background' ),
        'admin-preview-callback' => array( __CLASS__, 'admin_image_div_callback' )
      ) );

      add_theme_support( 'custom-header', array(
        'default-image' => '',
        'random-default' => false,
        'width' => 0,
        'height' => 0,
        'flex-height' => false,
        'flex-width' => false,
        'default-text-color' => '',
        'header-text' => true,
        'uploads' => true,
        'wp-head-callback' => array( __CLASS__, 'flawless_admin_header_style' ),
        'admin-head-callback' => array( __CLASS__, 'flawless_admin_header_image' ),
        'admin-preview-callback' => ''
      ) );

    }

    /**
     * Adds an option to post editor
     *
     * Must be called early, before admin_init
     *
     * @since 0.0.2
     */
    static function add_post_type_option( $args = array() ) {
      global $flawless;

      $args = wp_parse_args( $args, array(
        'post_type' => 'page',
        'label' => '',
        'input_class' => 'regular-text',
        'placeholder' => '',
        'meta_key' => '',
        'type' => 'checkbox'
      ) );

      if ( !is_array( $args[ 'post_type' ] ) ) {
        $args[ 'post_type' ] = array( $args[ 'post_type' ] );
      }

      foreach ( (array) $args[ 'post_type' ] as $post_type ) {
        $flawless[ 'ui_options' ][ $post_type ][ $args[ 'meta_key' ] ] = $args;
      }

      //** Create filter to render input */
      add_action( 'save_post', array( __CLASS__, 'save_post' ), 10, 2 );

      //** Create filter to save / update */
      add_action( 'post_submitbox_misc_actions', array( __CLASS__, 'post_submitbox_misc_actions' ) );

    }

    /**
     * Saves extra post information
     *
     * @since 0.0.2
     */
    static function save_post( $post_id, $post ) {
      global $pagenow;

      //** Verify if this is an auto save routine.  */
      if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
      }

      if ( wp_is_post_revision( $post ) ) {
        return;
      }

      foreach ( (array) $_REQUEST[ 'flawless_option' ] as $meta_key => $value ) {
        if ( $value == 'false' || empty( $value ) ) {
          delete_post_meta( $post_id, $meta_key );
        } else {
          update_post_meta( $post_id, $meta_key, $value );
        }
      }

      if ( Flawless::changeable_post_type( $post->post_type ) ) {

        //** Return if option box is not selected. */
        if ( !isset( $_POST[ 'cpt-nonce-select' ] ) ) {
          return;
        }

        //** Return if selected nonce was used within time limit.  */
        if ( !wp_verify_nonce( $_POST[ 'cpt-nonce-select' ], 'post-type-selector' ) ) {
          return;
        }

        //** Return if user cannot edit post. */
        if ( !current_user_can( 'edit_post', $post_id ) ) {
          return;
        }

        //** Return if new post type matches current post type. */
        if ( $_POST[ 'flawless_cpt_post_type' ] == $post->post_type ) {
          return;
        }

        //** Return if post type slug returned null. */
        if ( !$new_post_type_object = get_post_type_object( $_POST[ 'flawless_cpt_post_type' ] ) ) {
          return;
        }

        //** Return if current user cannot publish posts. */
        if ( !current_user_can( $new_post_type_object->cap->publish_posts ) ) {
          return;
        }

        //** Updates the post type for the new post ID.  */
        set_post_type( $post_id, $new_post_type_object->name );

      }

    }

    /**
     * Render any options for this post type on editor page
     *
     * @since 0.0.2
     */
    static function post_submitbox_misc_actions() {
      global $post, $flawless, $pagenow;

      $cur_post_type_object = get_post_type_object( $post->post_type );

      if ( !$cur_post_type_object->public || !$cur_post_type_object->show_ui ) {
        return;
      }

      /** Create form for switching the post type */
      if ( current_user_can( $cur_post_type_object->cap->publish_posts ) && Flawless::changeable_post_type( $post->post_type ) ) {
        ?>

        <div class="misc-pub-section misc-pub-section-last change-post-type">
        <label for="flawless_cpt_post_type"><?php _e( 'Post Type:', 'flawless' ); ?></label>
        <span id="post-type-display"
          class="flawless_cpt_display"><?php echo $cur_post_type_object->labels->singular_name; ?></span>

        <a href="#" id="edit-post-type-change" class="hide-if-no-js"><?php _e( 'Edit' ); ?></a>
          <?php wp_nonce_field( 'post-type-selector', 'cpt-nonce-select' ); ?>
          <div id="post-type-select" class="flawless_cpt_select">
          <select name="flawless_cpt_post_type" id="flawless_cpt_post_type">
            <?php foreach ( (array) get_post_types( (array) apply_filters( 'flawless_cpt_metabox', array( 'public' => true, 'show_ui' => true ) ), 'objects' ) as $pt ) {
              if ( !current_user_can( $pt->cap->publish_posts ) || !Flawless::changeable_post_type( $pt->name ) ) {
                continue;
              }
              echo '<option value="' . esc_attr( $pt->name ) . '"' . selected( $post->post_type, $pt->name, false ) . '>' . $pt->labels->singular_name . "</option>\n";
            } ?>
          </select>
          <a href="#" id="save-post-type-change" class="hide-if-no-js button"><?php _e( 'OK' ); ?></a>
          <a href="#" id="cancel-post-type-change" class="hide-if-no-js"><?php _e( 'Cancel' ); ?></a>
        </div>
      </div>
      <?php
      }

      if ( !is_array( $flawless[ 'ui_options' ][ $post->post_type ] ) ) {
        return;
      }

      usort( $flawless[ 'ui_options' ][ $post->post_type ], create_function( '$a,$b', ' return $a["position"] - $b["position"]; ' ) );

      foreach ( (array) $flawless[ 'ui_options' ][ $post->post_type ] as $option ) {

        switch ( $option[ 'type' ] ) {

          case 'checkbox':

            $html[ ] = '<li class="post_option_' . $option[ 'meta_key' ] . '">' . sprintf( '<input type="hidden" name="%1s" value="false" /><label><input type="checkbox" name="%2s" value="true" %3s /> %4s</label>',
                'flawless_option[' . $option[ 'meta_key' ] . ']',
                'flawless_option[' . $option[ 'meta_key' ] . ']',
                checked( 'true', get_post_meta( $post->ID, $option[ 'meta_key' ], true ), false ),
                $option[ 'label' ]
              ) . '</li>';

            break;

          case 'datetime':

            wp_enqueue_script( 'jquery-ui-datepicker' );

            $meta_value = trim( esc_attr( implode( ', ', (array) get_post_meta( $post->ID, $option[ 'meta_key' ] ) ) ) );

            if ( is_numeric( $meta_value ) && (int) $meta_value == $meta_value && strlen( $value ) == 10 ) {
              $meta_value = date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $meta_value );
            }

            $html[ ] = '<li class="post_option_' . $option[ 'meta_key' ] . '">' . sprintf( '<label><span class="regular-text-label">%1s:</span> <input class="flawless_datepicker %2s" type="text" placeholder="%3s" name="%4s" value="' . $meta_value . '"  /></label>',
                $option[ 'label' ],
                $option[ 'input_class' ],
                $option[ 'placeholder' ] ? $option[ 'placeholder' ] : '',
                'flawless_option[' . $option[ 'meta_key' ] . ']', $meta_value ) . '</li>';

            break;

          case 'input':
          default:

            $meta_value = trim( esc_attr( implode( ', ', (array) get_post_meta( $post->ID, $option[ 'meta_key' ] ) ) ) );

            $html[ ] = '<li class="post_option_' . $option[ 'meta_key' ] . '">' . '<label><span class="regular-text-label">' . $option[ 'label' ] . ':</span>
          <input class="' . $option[ 'input_class' ] . '" type="text" placeholder="' . esc_attr( $option[ 'placeholder' ] ) . '" name="flawless_option[' . esc_attr( $option[ 'meta_key' ] ) . ']" value="' . esc_attr( $meta_value ) . '"  /></label></li>';

            break;

        }

      }

      if ( is_array( $html ) ) {
        echo '<ul class="flawless_post_type_options wp-tab-panel">' . implode( "\n", $html ) . '</ul>';
      }

    }

    /**
     * Draw the custom site background
     *
     * Run on Flawless options update to validate blog owner's address for map on front-end.
     *
     * @todo Add function to check if background image actually exists and is reachable. - potanin@UD
     * @since 0.0.2
     */
    static function custom_background() {

      $background = get_background_image();
      $color = get_background_color();
      $position = get_theme_mod( 'background_position_x', 'left' );
      $attachment = get_theme_mod( 'background_attachment', 'scroll' );
      $repeat = get_theme_mod( 'background_repeat', 'no-repeat' );

      if ( !$background && !$color ) {
        return;
      }

      $style = array();

      if ( $color ) {
        $style[ ] = "background-color: #$color;";
      }

      if ( !empty( $background ) ) {
        $style[ ] = " background-image: url( '$background' );";

        if ( !in_array( $repeat, array( 'no-repeat', 'repeat-x', 'repeat-y', 'repeat' ) ) ) {
          $repeat = ' no-repeat ';
        }

        $style[ ] = " background-repeat: $repeat;";

        if ( !in_array( $position, array( 'center', 'right', 'left' ) ) ) {
          $position = ' center ';
        }

        $style[ ] = " background-position: top $position;";

        if ( !in_array( $attachment, array( 'fixed', 'scroll' ) ) ) {
          $attachment = ' scroll ';
        }

        $style[ ] = " background-attachment: $attachment;";

      }

      echo '<style type="text/css">body { ' . trim( implode( '', (array) $style ) ) . ' }</style>';

    }

    /**
     * Display area for background image in back-end
     *
     *
     * @since 0.0.2
     */
    static function admin_image_div_callback() {
      ?>

      <h3><?php _e( 'Background Image' ); ?></h3>
      <table class="form-table">
      <tbody>
      <tr valign="top">
      <th scope="row"><?php _e( 'Preview' ); ?></th>
      <td>
    <?php
      $background_styles = '';
      if ( $bgcolor = get_background_color() )
        $background_styles .= 'background-color: #' . $bgcolor . ';';

      if ( get_background_image() ) {
        // background-image URL must be single quote, see below
        $background_styles .= ' background-image: url(\'' . get_background_image() . '\' );'
          . ' background-repeat: ' . get_theme_mod( 'background_repeat', 'no-repeat' ) . ';'
          . ' background-position: top ' . get_theme_mod( 'background_position_x', 'left' );
      }
      ?>













      <div id="custom-background-image"
        style=" min-height: 200px;<?php echo $background_styles; ?>"><?php // must be double quote, see above ?>

    </div>
    <?php

    }

    /**
     * Styles the header image displayed on the Appearance > Header admin panel.
     *
     * Referenced via add_custom_image_header() in flawless_setup().
     *
     */
    static function flawless_admin_header_style() {
      ?>
      <style type="text/css">

      <?php if( get_header_textcolor() != HEADER_TEXTCOLOR ) : ?>
      #site-title a,
      #site-description {
        color: # <?php echo get_header_textcolor(); ?>;
      }

      <?php endif; ?>

    </style>
    <?php
    }

    /**
     * Custom header image markup displayed on the Appearance > Header admin panel.
     *
     * Referenced via add_custom_image_header() in flawless_setup().
     *
     */
    static function flawless_admin_header_image() {
      ?>
      <div id="headimg">
      <?php
      if ( 'blank' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) || '' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) )
        $style = ' style="display:none;"';
      else
        $style = ' style="color:#' . get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) . ';"';
      ?>
        <h1><a id="name"<?php echo $style; ?> onclick="return false;"
            href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>

      <div id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
        <?php $header_image = get_header_image();
        if ( !empty( $header_image ) ) : ?>
          <img src="<?php echo esc_url( $header_image ); ?>" alt=""/>
        <?php endif; ?>
    </div>
    <?php
    }

    /**
     * Handles back-end theme configurations
     *
     * @since 0.0.2
     *
     */
    static function admin_menu() {
      global $flawless;

      $flawless[ 'options_ui' ][ 'tabs' ] = apply_filters( 'flawless_option_tabs', array(
        'options_ui_general' => array(
          'label' => __( 'General', 'flawless' ),
          'id' => 'options_ui_general',
          'position' => 10,
          'callback' => array( 'Flawless_ui', 'options_ui_general' )
        ),
        'options_ui_post_types' => array(
          'label' => __( 'Content', 'flawless' ),
          'id' => 'options_ui_post_types',
          'position' => 20,
          'callback' => array( 'Flawless_ui', 'options_ui_post_types' )
        ),
        'options_ui_design' => array(
          'label' => __( 'Design', 'flawless' ),
          'id' => 'options_ui_design',
          'position' => 25,
          'callback' => array( 'Flawless_ui', 'options_ui_design' )
        ),
        'options_ui_advanced' => array(
          'label' => __( 'Advanced', 'flawless' ),
          'id' => 'options_ui_advanced',
          'position' => 200,
          'callback' => array( 'Flawless_ui', 'options_ui_advanced' )
        )
      ) );

      //** Put the tabs into position */
      usort( $flawless[ 'options_ui' ][ 'tabs' ], create_function( '$a,$b', ' return $a["position"] - $b["position"]; ' ) );

      //** QC Tabs Before Rendering */
      foreach ( (array) $flawless[ 'options_ui' ][ 'tabs' ] as $tab_id => $tab ) {
        if ( !is_callable( $tab[ 'callback' ] ) ) {
          unset( $flawless[ 'options_ui' ][ 'tabs' ][ $tab_id ] );
          continue;
        }
      }

      $flawless[ 'navbar_options' ] = array(
        'wordpress' => array(
          'label' => __( 'WordPress "Toolbar" ', 'flawless' )
        ) );

      foreach ( (array) wp_get_nav_menus() as $menu ) {
        $flawless[ 'navbar_options' ][ $menu->slug ] = array(
          'type' => 'wp_menu',
          'label' => $menu->name,
          'menu_slug' => $menu->slug
        );
      }

      $flawless[ 'navbar_options' ] = apply_filters( 'flawless::navbar_options', (array) $flawless[ 'navbar_options' ] );

      if ( is_array( $flawless[ 'options_ui' ][ 'tabs' ] ) ) {
        $settings_page = add_theme_page( __( 'Settings', 'flawless' ), __( 'Settings', 'flawless' ), 'edit_theme_options', basename( __FILE__ ), array( 'Flawless', 'options_page' ) );
      }

    }

    /**
     * Adds "Theme Options" page on back-end
     *
     * Used for configurations that cannot be logically placed into a built-in Settings page
     *
     * @todo Update 'auto_complete_done' message to include a link to the front-end for quick view of setup results.
     * @since 0.0.2
     */
    static function options_page() {
      global $flawless, $_wp_theme_features, $flawless;

      if ( !empty( $_GET[ 'admin_splash_screen' ] ) ) {
        Flawless_ui::show_update_screen( $_GET[ 'admin_splash_screen' ] );
      }

      if ( $_REQUEST[ 'message' ] == 'auto_complete_done' ) {
        $updated = __( 'Your site has been setup.  You may configure more advanced options here.', 'flawless' );
      }

      if ( $_REQUEST[ 'message' ] ) {

        switch ( $_REQUEST[ 'message' ] ) {

          case 'settings_updated':
            $updated = __( 'Theme settings updated.', 'flawless' );
            break;

          case 'backup_restored':
            $updated = __( 'Theme backup has been restored from uploaded file.', 'flawless' );
            break;

          case 'backup_failed':
            $updated = __( 'Could not restore configuration from backup, file data was not in valid JSON format.', 'flawless' );
            break;

        }
      }

      echo '<style type="text/css">' . implode( '', (array) $theme_feature_styles ) . '</style>';

      ?>

      <div id="flawless_settings_page"
        class="wrap flawless_settings_page" <?php echo !empty( $_GET[ 'admin_splash_screen' ] ) ? 'hidden' : ''; ?>>

      <h2 class="placeholder_title"></h2>

        <?php if ( $updated ) { ?>
          <div class="updated fade"><p><?php echo $updated; ?></p></div>
        <?php } ?>

        <form action="<?php echo add_query_arg( 'flawless_action', 'update_settings', Flawless_Admin_URL ); ?>"
          method="post" enctype="multipart/form-data">

        <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'flawless_settings' ); ?>"/>

        <div class="flawless_settings_tabs">

          <div class="icon32" id="icon-themes"><br></div>

          <ul class="tabs">
            <?php foreach ( (array) $flawless[ 'options_ui' ][ 'tabs' ] as $tab ) { ?>
              <li><a class="nav-tab" href="#flawless_tab_<?php echo $tab[ 'id' ]; ?>"><?php echo $tab[ 'label' ]; ?></a>
              </li>
            <?php } ?>
          </ul>

          <?php foreach ( (array) $flawless[ 'options_ui' ][ 'tabs' ] as $tab ) { ?>
            <div id="flawless_tab_<?php echo $tab[ 'id' ]; ?>"
              class="flawless_tab <?php echo $tab[ 'panel_class' ]; ?>">
              <?php call_user_func( $tab[ 'callback' ], $flawless ); ?>
            </div>
          <?php } ?>

        </div>

        <div class="flawless_below_tabs">
          <div class="submit_wrapper"><input type="submit" value="Save Changes" class="button-primary" name="Submit"/>
          </div>
        </div>

      </form>
    </div>
    <?php
    }

    /**
     * Enqueue or print scripts in admin footer
     *
     * Renders json array of configuration.
     *
     * @since 0.0.2
     */
    static function admin_print_footer_scripts( $hook ) {
      global $flawless;
      echo '<script type="text/javascript">var flawless = jQuery.extend( true, jQuery.parseJSON( ' . json_encode( json_encode( $flawless ) ) . ' ), typeof flawless === "object" ? flawless : {});</script>';
    }

    /**
     * Used for loading contextual help and back-end scripts. Only active on Theme Options page.
     *
     * @todo Should switch to WP 3.3 contextual help with UD live-help updater.
     * @uses $current_screen global variable
     * @since 0.0.2
     */
    static function admin_enqueue_scripts( $hook ) {
      global $current_screen, $flawless;

      //* Load Flawless Global Scripts */
      wp_enqueue_script( 'jquery-ud-smart_buttons' );
      wp_enqueue_script( 'flawless-admin-global' );
      wp_enqueue_style( 'flawless-admin-styles', Flawless::load( 'flawless-admin.css', 'css' ), array( 'farbtastic' ), Flawless_Version, 'screen' );

      if ( $current_screen->id != 'appearance_page_functions' ) {
        return;
      }

      if ( function_exists( 'get_current_screen' ) ) {
        $screen = get_current_screen();
      }

      if ( !is_object( $screen ) ) {
        return;
      }

      $contextual_help[ 'General Usage' ][ ] = '<h3>' . __( 'Flawless Theme Help' ) . '</h3>';
      $contextual_help[ 'General Usage' ][ ] = '<p>' . __( 'Since version 3.0.0 much flexibility was added to page layouts by adding a number of conditional Tabbed Widget areas which are available on all the pages.', 'flawless' ) . '</p>';

      $contextual_help[ 'Theme Development' ][ ] = '<h3>' . __( 'Skins and Child Theme' ) . '</h3>';
      $contextual_help[ 'Theme Development' ][ ] = '<p>' . sprintf( __( 'You may harcode the skin selection into your child theme. <code>%1s</code>.', 'flawless' ), 'flawless_set_color_scheme( \'skin-default.css\' );' ) . '</p>';

      $contextual_help[ 'Theme Development' ][ ] = '<h3>' . __( 'Disabling Theme Features' ) . '</h3>';
      $contextual_help[ 'Theme Development' ][ ] = '<p>' . sprintf( __( 'You may also disable most theme features by adding PHP chlid theme, or header tag to your CSS file. For example, to remove Custom Skin selection UI, add the following code to your functions.php file: <code>%1s</code> or to remove the custom background: <code>%2s</code>.', 'flawless' ), 'remove_theme_support( \'custom-skins\' );', 'remove_theme_support( \'custom-background\' );' ) . '</p>';
      $contextual_help[ 'Theme Development' ][ ] = '<p>' . sprintf( __( 'To disable features from within the CSS file use the <b>Disabled Features</b> tag. For example, to disable the Header Logo and Header Search, and the following: <code>%1s</code>', 'flawless' ), 'Disabled Features: header-logo, header-search' ) . '</p>';

      $contextual_help[ 'Theme Development' ][ ] = '<h3>' . __( 'Loading Google Fonts' ) . '</h3>';
      $contextual_help[ 'Theme Development' ][ ] = '<p>' . sprintf( __( 'Loading Google Fonts is quite simple and may be done directly in a custom skin or chlid theme\'s style.css file. Add a <b>Google Fonts:</b> tag to the theme header, followed by a comma separated list of Google font names. For example, to load Droid Serif and Oswald, you would add the following: <code>%1s</code>', 'flawless' ), 'Google Fonts: Droid Serif, Oswald' ) . '</p>';

      $contextual_help[ 'JavaScript Helpers' ][ ] = '<h3>' . __( 'Progress Bar' ) . '</h3>';
      $contextual_help[ 'JavaScript Helpers' ][ ] = '<p>' . sprintf( __( 'The <b>%1s</b> function will return HTML for the loading bar, and create a timer, and attach a dynamic loading effect.  To add the progress bar to an existing HTML element, use the following code: <code>%2s</code> ', 'flawless' ), 'flawless.progress_bar()', 'jQuery( \'.css_selector\' ).append( flawless.progress_bar());' ) . '</p>';

      $contextual_help = apply_filters( 'flawless::contextual_help', $contextual_help );

      foreach ( (array) $contextual_help as $help_slug => $help_items ) {

        $screen->add_help_tab( array(
          'id' => $help_slug,
          'title' => self::de_slug( $help_slug ),
          'content' => implode( "\n", (array) $help_items )
        ) );

      }

      //** Enque Scripts on Theme Options Page */
      wp_enqueue_script( 'jquery-ui-sortable' );
      wp_enqueue_script( 'jquery-ui-tabs' );
      wp_enqueue_script( 'jquery-cookie' );
      wp_enqueue_script( 'flawless-admin' );

    }

    /**
     * Renders extra fields on term editing pages.
     *
     * @todo fix issue w/ content submitted by the_editor being overwritten by description field by filter.
     * @author potanin@UD
     */
  static function taxonomy_edit_form_fields( $tag, $taxonomy ) {
    global $post_ID;

    $_post_ID = $post_ID;

    $post = get_post_for_extended_term( $tag, $tag->taxonomy );

    if ( !$post ) {
      return;
    }

    $post_ID = $post->ID;

    do_action( 'flawless::extended_term_form_fields', $tag, $post );

    if ( !$post->ID ) {
      return;
    }

    ?>

    <tr class="form-field hidden">
        <th scope="row" valign="top"></th>
        <td>
          <input type="hidden" name="extended_post_id" value="<?php echo esc_attr( $post->ID ) ?>"/>
          <input type="hidden" name="post_data[ID]" value="<?php echo esc_attr( $post->ID ) ?>"/>
          <a class="button" target="_blank"
            href="<?php echo get_edit_post_link( $post->ID ); ?>"><?php _e( 'Open Advanced Editor', 'flawless' ); ?></a>
        </td>
      </tr>

  <?php if ( current_user_can( 'upload_files' ) ) { ?>
    <tr class="form-field">
          <th scope="row" valign="top"><?php _e( 'Images', 'flawless' ); ?></th>
          <td>
            <iframe style="width: 100%;height: 400px" src="<?php echo get_upload_iframe_src(); ?>"></iframe>
          </td>
        </tr>
  <?php } ?>

    <?php

    $post_ID = $_post_ID;

  }

    /**
     * When Term Meta is enabled, this is Management displayed on Taxonomy Edit pages before the Add New form.
     *
     * @todo Implement a dynamic table for addition of meta keys and selection of input types. - potanin@UD
     * @author potanin@UD
     */
  static function taxonomy_pre_add_form( $taxonomy ) {
    global $flawless;

    if ( !current_user_can( 'manage_options' ) ) {
      return;
    }

    $tax = get_taxonomy( $taxonomy );

    return;

    ?>

    <div class="form-wrap">
      <h3><?php _e( 'Taxonomy Meta' ); ?></h3>

      <form id="addtag" method="post" action="#" class="validate">

        <table class="widefat wpp_something_advanced_wrapper ud_ui_dynamic_table" sortable_table="true"
          allow_random_slug="false">
          <tbody>
          <?php foreach ( (array) $flawless[ 'business_card' ][ 'data' ] as $slug => $data ) { ?>
            <tr
              class="flawless_dynamic_table_row <?php echo( $data[ 'locked' ] == 'true' ? 'flawless_locked_row' : '' ); ?>"
              slug="<?php echo $slug; ?>" new_row="false"
              lock_row="<?php echo( $data[ 'locked' ] == 'true' ? 'true' : 'false' ); ?>">
              <th>
                <div class="delete_icon flawless_delete_row" verify_action="true"></div>
                <input type="text" id="flawless_card_<?php echo $slug; ?>" class="slug_setter"
                  name="flawless_settings[business_card][data][<?php echo $slug; ?>][label]"
                  value="<?php echo $data[ 'label' ]; ?>"/>
              </th>
              <td class="draggable_col">
                <input type="text" id="flawless_card_<?php echo $slug; ?>"
                  name="flawless_settings[business_card][data][<?php echo $slug; ?>][label]"
                  value="<?php echo $data[ 'label' ]; ?>"/>
              </td>
            </tr>
          <?php } ?>
          </tbody>
          <tfoot>
          <tr>
            <td colspan='2'><input type="button" class="flawless_add_row button-secondary"
                value="<?php _e( 'Add Row', 'flawless' ) ?>"/></td>
          </tr>
          </tfoot>
        </table>

        <?php submit_button( 'Save', 'button' ); ?>
      </form>
    </div>

  <?php

  }

    /**
     * Display a splash screen on update or new install
     *
     * @todo Once Utility::parse_readme() is updated to return data via associative array, this can be improved - potanin@UD
     * @author potanin@UD
     */
    function show_update_screen( $splash_type ) {
      $change_log = Utility::parse_readme(); ?>

      <div class="wrap flawless-update about-wrap flawless_settings_page">
      <h1>Welcome to Flawless <?php echo Flawless_Version; ?></h1>

      <div
        class="about-text"><?php printf( __( 'Thank you for updating to the latest version! Please take a look at some of the updates, <a href="%1s">go to dashboard</a> or <a href="%2s">view theme settings</a>.', 'flawless' ), admin_url(), '#flawless_action#toggle=.flawless_settings_page' ); ?></div>
      <div class="changelog point-releases"><?php echo $change_log; ?></div>
      <div class="return-dashboard"><a
          href="<?php echo esc_url( admin_url() ); ?>"><?php _e( 'Go to Dashboard &rarr; Home' ); ?></a></div>
    </div>

    <?php

    }

    /**
     * Primary Options Tab
     *
     * Footer and Header elements combined into header_elemeners in 0.5.0
     *
     * @author potanin@UD
     * @since Flawless 0.1.0
     */
    function options_ui_general( $flawless ) {

      if ( current_theme_supports( 'header-navbar' ) ) {
        $flawless[ 'options_ui' ][ 'header_elements' ][ 'navbar' ] = array(
          'label' => __( 'Navbar', 'flawless' ),
          'id' => 'navbar',
          'name' => 'flawless_settings[disabled_theme_features][header-navbar]',
          'position' => 10,
          'setting' => $flawless[ 'disabled_theme_features' ][ 'header-navbar' ],
          'callback' => array( 'Management', 'options_header_navbar' ),
          'toggle_label' => __( 'Do not show the Navbar.', 'flawless' )
        );
      }

      if ( current_theme_supports( 'mobile-navbar' ) ) {
        $flawless[ 'options_ui' ][ 'header_elements' ][ 'mobile-navbar' ] = array(
          'label' => __( 'Mobile Navbar', 'flawless' ),
          'id' => 'mobile-navbar',
          'name' => 'flawless_settings[disabled_theme_features][mobile-navbar]',
          'position' => 15,
          'setting' => $flawless[ 'disabled_theme_features' ][ 'mobile-navbar' ],
          'callback' => array( 'Management', 'options_mobile_navbar' ),
          'toggle_label' => __( 'Do not use a Mobile Navbar.', 'flawless' )
        );
      }

      $flawless[ 'options_ui' ][ 'header_elements' ][ 'search' ] = array(
        'label' => __( 'Header Search', 'flawless' ),
        'id' => 'header-search',
        'name' => 'flawless_settings[disabled_theme_features][header-search]',
        'position' => 20,
        'setting' => $flawless[ 'disabled_theme_features' ][ 'header-search' ],
        'callback' => array( 'Management', 'options_header_search' ),
        'toggle_label' => __( 'Do not show search box in header.', 'flawless' )
      );

      $flawless[ 'options_ui' ][ 'header_elements' ][ 'logo' ] = array(
        'label' => __( 'Logo', 'flawless' ),
        'id' => 'options_header_logo',
        'name' => 'flawless_settings[disabled_theme_features][header-logo]',
        'position' => 40,
        'setting' => $flawless[ 'disabled_theme_features' ][ 'header-logo' ],
        'toggle_label' => __( 'Hide logo from header.', 'flawless' ),
        'callback' => array( 'Management', 'options_header_logo' )
      );

      if ( current_theme_supports( 'header-dropdowns' ) ) {
        $flawless[ 'options_ui' ][ 'header_elements' ][ 'dropdowns' ] = array(
          'label' => __( 'Header Dropdowns', 'flawless' ),
          'id' => 'header-dropdowns',
          'name' => 'flawless_settings[disabled_theme_features][header-dropdowns]',
          'position' => 50,
          'setting' => $flawless[ 'disabled_theme_features' ][ 'header-dropdowns' ],
          'toggle_label' => __( 'Disable the header dropdown sections.', 'flawless' )
        );
      }

      $flawless[ 'options_ui' ][ 'header_elements' ][ 'header_text' ] = array(
        'label' => __( 'Header Text', 'flawless' ),
        'id' => 'header-text',
        'name' => 'flawless_settings[disabled_theme_features][header_text]',
        'position' => 20,
        'setting' => $flawless[ 'disabled_theme_features' ][ 'header_text' ],
        'toggle_label' => __( 'Do not show copyright in footer.', 'flawless' ),
        'callback' => array( 'Management', 'options_header_text' )
      );

      $flawless[ 'options_ui' ][ 'header_elements' ][ 'footer_text' ] = array(
        'label' => __( 'Footer Text', 'flawless' ),
        'id' => 'footer-copyright',
        'name' => 'flawless_settings[disabled_theme_features][footer-copyright]',
        'position' => 20,
        'setting' => $flawless[ 'disabled_theme_features' ][ 'footer-copyright' ],
        'toggle_label' => __( 'Do not show copyright in footer.', 'flawless' ),
        'callback' => array( 'Management', 'options_footer_copyright' )
      );

      $flawless[ 'options_ui' ][ 'header_elements' ] = apply_filters( 'flawless_option_header_elements', $flawless[ 'options_ui' ][ 'header_elements' ] );

      //** Put the tabs into position */
      usort( $flawless[ 'options_ui' ][ 'header_elements' ], create_function( '$a,$b', ' return $a["position"] - $b["position"]; ' ) );

      //** Check if sections have advanced configuration menus */
      foreach ( $flawless[ 'options_ui' ][ 'header_elements' ] as $tab_id => $tab ) {
        if ( is_callable( $tab[ 'callback' ] ) ) {
          $element_panels[ $tab_id ] = $tab;
        }
      }

      $page_selection_404 = Flawless::wp_dropdown_objects( array(
        'name' => "flawless_settings[404_page]",
        'show_option_none' => __( '&mdash; Select &mdash;' ),
        'option_none_value' => '0',
        'echo' => false,
        'post_type' => get_post_types( array( 'hierarchical' => true ) ),
        'selected' => $flawless[ '404_page' ]
      ) );

      $page_selection_not_found = Flawless::wp_dropdown_objects( array(
        'name' => "flawless_settings[no_search_result_page]",
        'show_option_none' => __( '&mdash; Select &mdash;' ),
        'option_none_value' => '0',
        'echo' => false,
        'post_type' => get_post_types( array( 'hierarchical' => true ) ),
        'selected' => $flawless[ 'no_search_result_page' ]
      ) );

      ?>

      <div
        class="tab_description"><?php _e( 'Configure general settings, and customize theme features and special landing pages.', 'flawless' ); ?></div>

      <table class="form-table">
      <tbody>

      <tr valign="top">
        <th><?php _e( 'General Options', 'flawless' ); ?></th>
        <td>
          <ul>
            <li><label><input type="checkbox" <?php checked( 'true', $flawless[ 'hide_breadcrumbs' ] ); ?>
                  name='flawless_settings[hide_breadcrumbs]'
                  value="true"/> <?php _e( 'Globally disable breadcrumbs.', 'flawless' ); ?></label></li>
            <?php do_action( 'flawless::options_ui_general::common_settings', $flawless ); ?>
          </ul>
        </td>
      </tr>

      <tr valign="top" class="flawless_option_group" flawless_option_group="top_navigation">
        <th><?php _e( 'Menus & Navigation', 'flawless' ); ?></th>
        <td>
          <ul>
            <li class="flawless_option" flawless_option="menu_descriptions">
              <label for="flawless_top_navigation_show_descriptions">
                <input
                  type="checkbox" <?php checked( 'true', $flawless[ 'menus' ][ 'header-menu' ][ 'show_descriptions' ] ); ?>
                  id="flawless_top_navigation_show_descriptions"
                  name="flawless_settings[menus][header-menu][show_descriptions]" value="true"/>
                <?php _e( 'Show menu item descriptions below the titles in the Top Navigation.', 'flawless' ); ?>
              </label>
            </li>
            <li>
              <label for="flawless_footer_navigation_show_descriptions">
                <input
                  type="checkbox" <?php checked( 'true', $flawless[ 'menus' ][ 'footer-menu' ][ 'show_descriptions' ] ); ?>
                  id="flawless_footer_navigation_show_descriptions"
                  name="flawless_settings[menus][footer-menu][show_descriptions]" value="true"/>
                <?php _e( 'Show menu item descriptions below the titles in the Footer Navigation.', 'flawless' ); ?>
              </label>
            </li>
          </ul>
        </td>
      </tr>

      <tr valign="top" class="flawless_special_pages">
        <th><?php _e( 'Special Pages', 'flawless' ); ?></th>
        <td>
          <ul>
            <?php if ( $page_selection_404 ) { ?>
              <li>
                <label for="404_page"><?php _e( '404 Page: ', 'flawless' ); ?><?php echo $page_selection_404; ?></label>
                <span
                  class="description"><?php _e( 'Use to display a custom page for all 404 pages.', 'flawless' ); ?></span>
              </li>
            <?php } ?>

            <?php if ( $page_selection_not_found ) { ?>
              <li>
                <label
                  for="404_page"><?php _e( 'No Result Page: ', 'flawless' ); ?><?php echo $page_selection_not_found; ?>
                  <span
                    class="description"><?php _e( 'Page to display when a search has no results.', 'flawless' ); ?></span>
                </label>
              </li>
            <?php } ?>

          </ul>
        </td>
      </tr>

      <tr valign="top" class="flawless_header_features">
        <th><?php _e( 'Management Elements', 'flawless' ); ?></th>
        <td class="flawless_tabs flawless_section_specific_tabs">

          <ul class="tabs">
            <?php foreach ( (array) $element_panels as $tab ) { ?>
              <li class="conditional_dependency" required_condition="<?php echo $tab[ 'callback' ][ 1 ]; ?>"><a
                  href="#flawless_header_tab_<?php echo $tab[ 'id' ]; ?>"><?php echo $tab[ 'label' ]; ?></a></li>
            <?php } ?>
          </ul>

          <?php foreach ( (array) $element_panels as $tab ) { ?>
            <div id="flawless_header_tab_<?php echo $tab[ 'id' ]; ?>"
              class="flawless_tab <?php echo $tab[ 'panel_class' ]; ?> conditional_dependency"
              required_condition="<?php echo $tab[ 'callback' ][ 1 ]; ?>"><?php call_user_func( $tab[ 'callback' ], $flawless ); ?></div>
          <?php } ?>

        </td>
      </tr>

      <tr valign="top" class="flawless_supported_theme_features">
        <th>
          <?php _e( 'Disabled Management Features', 'flawless' ); ?>
          <div
            class="description"><?php _e( 'These features are enabled by default, select them to disable.', 'flawless' ); ?></div>
        </th>
        <td>
          <ul class="wp-tab-panel">
            <?php if ( current_theme_supports( 'custom-background' ) ) { ?>
              <li><label><input
                    type="checkbox" <?php checked( 'false', $flawless[ 'disabled_theme_features' ][ 'custom-background' ] ); ?>
                    name="flawless_settings[disabled_theme_features][custom-background]"
                    value="false"/> <?php _e( 'Custom Backgrounds.', 'flawless' ); ?></label></li>
            <?php } ?>
            <?php if ( current_theme_supports( 'custom-header' ) ) { ?>
              <li><label><input
                    type="checkbox" <?php checked( 'false', $flawless[ 'disabled_theme_features' ][ 'custom-header' ] ); ?>
                    name="flawless_settings[disabled_theme_features][custom-header]"
                    value="false"/> <?php _e( 'Custom Header Image.', 'flawless' ); ?></label></li>
            <?php } ?>
            <?php foreach ( (array) $flawless[ 'options_ui' ][ 'header_elements' ] as $tab ) { ?>
              <li><label><input type="checkbox" <?php checked( 'false', $tab[ 'setting' ] ); ?>
                    name="<?php echo $tab[ 'name' ]; ?>" affects="<?php echo $tab[ 'callback' ][ 1 ]; ?>"
                    value="false"/> <?php printf( __( '%1s.', 'flawless' ), $tab[ 'label' ] ); ?></label>
              </li>
            <?php } ?>
            <?php /* foreach( (array) $flawless[ 'disabled_theme_features' ] as $feature_slug => $disabled ) {  ?>
            <li><label><input type="checkbox" checked="true" name="flawless_settings[disabled_theme_features][<?php echo $feature_slug; ?>]" value="true" /> <?php printf( __( '%1s.', 'flawless' ), ucwords( str_replace( array( '-', '_' ) , ' ', $feature_slug ) ) ); ?></label></li>
          <?php } */
            ?>

          </ul>
        </td>
      </tr>

      </tbody>
    </table>

    <?php
    }

    /**
     * { short description missing }
     *
     * @author potanin@UD
     */
    function options_footer_copyright( $flawless ) {
      ?>

      <textarea id="footer_copyright" class="large-text footer_copyright"
        name="flawless_settings[footer][copyright]"><?php echo $flawless[ 'footer' ][ 'copyright' ]; ?></textarea>
      <div
        class="description"><?php _e( 'Footer text, often used for copyright information, is displayed at the bottom of all pages. Shortcodes can be used here. Useful shortcodes: [current_year], [site_description].', 'flawless' ); ?></div>

    <?php

    }

    /**
     * { short description missing }
     *
     * @author potanin@UD
     */
    function options_header_text( $flawless ) {
      ?>

      <textarea id="footer_copyright" class="large-text"
        name="flawless_settings[header][header_text]"><?php echo $flawless[ 'header' ][ 'header_text' ]; ?></textarea>
      <div class="description"><?php _e( 'Header text, shortcodes are supported.', 'flawless' ); ?></div>

    <?php

    }

    /**
     * { short description missing }
     *
     * @author potanin@UD
     */
    function options_header_navbar( $flawless ) {
      ?>

      <div
        class="flawless_tab_description"><?php _e( 'A Navbar is displayed at the very top of your site.  If a custom Mobile Navbar is setup, this Navbar will not be displayed on mobile devices.', 'flawless' ); ?></div>

      <ul>
      <li>
        <label><?php _e( 'Navbar Type:', 'flawless' ); ?>
          <select name="flawless_settings[navbar][type]">
            <option value="-1"> -</option>
            <?php foreach ( (array) $flawless[ 'navbar_options' ] as $navbar_type => $navbar_data ) { ?>
              <option <?php selected( $navbar_type, $flawless[ 'navbar' ][ 'type' ] ); ?>
                value="<?php echo $navbar_type; ?>"><?php echo $navbar_data[ 'label' ]; ?></option>
            <?php } ?>
          </select>
        </label>
      </li>

      <li>
        <input type="hidden" name="flawless_settings[navbar][show_brand]" value="false"/>
        <label>
          <input type="checkbox" name="flawless_settings[navbar][show_brand]"
            value="true" <?php checked( $flawless[ 'navbar' ][ 'show_brand' ], 'true' ); ?> />
          <?php _e( 'Show your website\'s brand on far left side of the Navbar.', 'flawless' ); ?>
        </label>
      </li>

      <li>
        <input type="hidden" name="flawless_settings[navbar][show_login]" value="false"/>
        <label>
          <input type="checkbox" name="flawless_settings[navbar][show_login]"
            value="true" <?php checked( $flawless[ 'navbar' ][ 'show_login' ], 'true' ); ?> />
          <?php _e( 'Show user login on the far right side of the Navbar.', 'flawless' ); ?>
        </label>
      </li>

      <li>
        <input type="hidden" name="flawless_settings[navbar][show_editlayout]" value="false"/>
        <label>
          <input type="checkbox" name="flawless_settings[navbar][show_editlayout]"
            value="true" <?php checked( $flawless[ 'navbar' ][ 'show_editlayout' ], 'true' ); ?> />
          <?php _e( 'Show "Edit Layout" link at far right side of the Navbar.', 'flawless' ); ?>
        </label>
      </li>

      <li>
        <input type="hidden" name="flawless_settings[navbar][collapse]" value="false"/>
        <label>
          <input type="checkbox" name="flawless_settings[navbar][collapse]"
            value="true" <?php checked( $flawless[ 'navbar' ][ 'collapse' ], 'true' ); ?> />
          <?php _e( 'Show a menu expansion button when it is collapsed on small screens.', 'flawless' ); ?>
        </label>
      </li>

    </ul>

    <?php
    }

    /**
     * { short description missing }
     *
     * @author potanin@UD
     */
    function options_mobile_navbar( $flawless ) {
      ?>

      <div
        class="flawless_tab_description"><?php _e( 'A Navbar displayed only for mobile devices  When enabled, the standard Navbar will be hidden on mobile devices.', 'flawless' ); ?></div>

      <ul>

      <li>
        <label><?php _e( 'Navbar Type:', 'flawless' ); ?>
          <select name="flawless_settings[mobile_navbar][type]">
            <option value="-1"> -</option>
            <?php foreach ( (array) $flawless[ 'navbar_options' ] as $navbar_type => $navbar_data ) { ?>
              <option <?php selected( $navbar_type, $flawless[ 'mobile_navbar' ][ 'type' ] ); ?>
                value="<?php echo $navbar_type; ?>"><?php echo $navbar_data[ 'label' ]; ?></option>
            <?php } ?>
          </select>
        </label>
      </li>

      <li>
        <input type="hidden" name="flawless_settings[mobile_navbar][show_brand]" value="false"/>
        <label>
          <input type="checkbox" name="flawless_settings[mobile_navbar][show_brand]"
            value="true" <?php checked( $flawless[ 'mobile_navbar' ][ 'show_brand' ], 'true' ); ?> />
          <?php _e( 'Show your website\'s brand on far left side of the Navbar.', 'flawless' ); ?>
        </label>
      </li>

      <li>
        <input type="hidden" name="flawless_settings[mobile_navbar][show_login]" value="false"/>
        <label>
          <input type="checkbox" name="flawless_settings[mobile_navbar][show_login]"
            value="true" <?php checked( $flawless[ 'mobile_navbar' ][ 'show_login' ], 'true' ); ?> />
          <?php _e( 'Show user login on the far right side of the Navbar.', 'flawless' ); ?>
        </label>
      </li>

    </ul>

    <?php
    }

    /**
     * { short description missing }
     *
     * @author potanin@UD
     */
    function options_header_search( $flawless ) {
      ?>

      <ul>
      <li>
        <input type="hidden" name="flawless_settings[header][must_enter_search_term]" value="false"/>
        <label><input type="checkbox" name="flawless_settings[header][must_enter_search_term]"
            value="true" <?php checked( $flawless[ 'header' ][ 'must_enter_search_term' ], 'true' ); ?> />
          <?php _e( 'Users must enter a search term for search form to work.', 'flawless' ); ?></label>
      </li>
      <li>
        <input type="hidden" name="flawless_settings[header][must_enter_search_term]" value="false"/>
        <label><input type="checkbox" name="flawless_settings[header][grow_input_when_clicked]"
            value="true" <?php checked( $flawless[ 'header' ][ 'grow_input_when_clicked' ], 'true' ); ?> />
          <?php _e( 'Expand the search input box when being used.', 'flawless' ); ?></label>
      </li>
      <li>
        <label><?php _e( 'Search input placeholder:', 'flawless' ); ?>
          <input type="text" class="regular-text"
            placeholder="<?php printf( __( 'Search %1s', 'flawless' ), get_bloginfo( 'name' ) ); ?>"
            name="flawless_settings[header][search_input_placeholder]"
            value="<?php echo $flawless[ 'header' ][ 'search_input_placeholder' ]; ?>"/></label>
      </li>

    </ul>

    <?php
    }

    /**
     * { short description missing }
     *
     * @author potanin@UD
     */
    function options_header_logo( $flawless ) {
      ?>

      <ul class="flawless_logo_upload">

      <?php if ( !empty( $flawless[ 'flawless_logo' ][ 'url' ] ) ) { ?>
        <li class="current_flawless_logo">
          <input type="hidden" name="flawless_settings[flawless_logo][post_id]"
            value="<?php echo $flawless[ 'flawless_logo' ][ 'post_id' ]; ?>"/>
          <input type="hidden" name="flawless_settings[flawless_logo][url]"
            value="<?php echo $flawless[ 'flawless_logo' ][ 'url' ]; ?>"/>
          <input type="hidden" name="flawless_settings[flawless_logo][width]"
            value="<?php echo $flawless[ 'flawless_logo' ][ 'width' ]; ?>"/>
          <input type="hidden" name="flawless_settings[flawless_logo][height]"
            value="<?php echo $flawless[ 'flawless_logo' ][ 'height' ]; ?>"/>

          <?php if ( Flawless::can_get_image( $flawless[ 'flawless_logo' ][ 'url' ] ) ) {
            echo '<img src="' . $flawless[ 'flawless_logo' ][ 'url' ] . '" class="flawless_logo" />';
          } else {
            ?>
            <div
              class="flawless_asset_missing flawless_logo"><?php printf( __( 'Warning: Logo ( %1s ) Not Found', 'flawless' ), $flawless[ 'flawless_logo' ][ 'url' ] ); ?></div>
          <?php } ?>

          <div class="">
            <span class="flawless_delete_logo button"><?php _e( 'Delete Logo', 'flawless' ); ?></span>
            <a target="_blank"
              href="<?php echo admin_url( 'media.php?attachment_id=' . $flawless[ 'flawless_logo' ][ 'post_id' ] . '&action=edit' ); ?>"
              class="button"><?php _e( 'Edit Image', 'flawless' ); ?></a>
          </div>

        </li>
      <?php } ?>

        <li class="upload_new_logo">
        <label
          for="flawless_text_logo"><?php _e( 'To upload new logo, choose an image from your computer:', 'flawless' ); ?></label>
        <input id="flawless_text_logo" type="file" name="flawless_logo"/>
      </li>

    </ul>

    <?php
    }

    /**
     * Post Type and Taxonomy Management
     *
     * @author potanin@UD
     * @since Flawless 0.5.0
     */
    function options_ui_post_types( $flawless ) {
      global $wp_post_types, $_wp_post_type_features; ?>

      <div
        class="tab_description"><?php _e( 'Manage post types and taxonomies, associate them with widget areas, and configure display settings.', 'flawless' ); ?></div>

      <div class="flawless_content_ui">

    <div class="widget_area_sidebar">
      <div class="flawless_available_widget_areas"><?php _e( 'Available Widget Areas', 'flawless' ); ?></div>
      <ul class="flawless_widget_area_list" type="widget_area_selector">

        <?php foreach ( ( array ) $flawless[ 'widget_areas' ][ 'all' ] as $sidebar_id => $sidebar_data ) {
          Management::flawless_widget_item( array(
            'sidebar_id' => $sidebar_id,
            'widget_area_selector' => true,
            'sidebar_data' => $sidebar_data
          ) );
        } ?>

        <li class="flawless_add_new_widget_area">
          <?php _e( 'Add New Widget Area', 'flawless' ); ?>
        </li>

      </ul>
    </div>

    <div class="flawless_content_body">

    <div class="ud_ui_dynamic_table flawless_content_inner">

      <?php foreach ( ( array ) $flawless[ 'post_types' ] as $type => $data ) { ?>

        <div class="flawless_content_type_module flawless_dynamic_table_row" content_type="post_types"
          slug="<?php echo $type; ?>" new_row="false"
          lock_row="<?php echo( $data[ 'flawless_post_type' ] == "true" ? "false" : "true" ); ?>">

          <div class="ct_header">
            <input class="slug_setter" type="text" name="flawless_settings[post_types][<?php echo $type; ?>][name]"
              value="<?php echo $data[ 'name' ]; ?>"/>

            <ul class="flawless_dropdown_options" show_on_clone="true">
              <li class="flawless_delete_row" verify_action="true">Delete</li>
            </ul>

            <span
              class="content_type_label"><?php echo $data[ 'flawless_post_type' ] == 'true' ? __( 'Custom Post Type', 'flawless' ) : __( 'Post Type' ); ?></span>

          </div>

          <div class="flawless_content_type_options flawless_half_width">

            <ul class="flawless_options_wrapper flawless_advanced_content_type_options">

              <li class="flawless_option">
                <label>
                  <input type="checkbox" <?php checked( 'true', $data[ 'show_post_meta' ] ); ?>
                    name="flawless_settings[post_types][<?php echo $type; ?>][show_post_meta]" value="true"/>
                  <?php _e( 'Enable post meta.', 'flawless' ) ?>
                </label>
              </li>

              <li class="flawless_option">
                <label>
                  <input type="checkbox" <?php checked( 'true', $data[ 'disable_author' ] ); ?>
                    name="flawless_settings[post_types][<?php echo $type; ?>][disable_author]" value="true"/>
                  <?php _e( 'Disable authors.', 'flawless' ) ?>
                </label>
              </li>

              <li class="flawless_option">
                <label>
                  <input type="checkbox" <?php checked( 'true', $data[ 'disable_comments' ] ); ?>
                    name="flawless_settings[post_types][<?php echo $type; ?>][disable_comments]" value="true"/>
                  <?php _e( 'Disable comments.', 'flawless' ) ?>
                </label>
              </li>

              <?php if ( $data[ 'flawless_post_type' ] == 'true' ) { ?>
                <li class="flawless_option">
                  <label><?php _e( 'Root Page:', 'flawless' ) ?>
                    <?php Flawless::wp_dropdown_objects( array(
                      'name' => "flawless_settings[post_types][{$type}][root_page]",
                      'show_option_none' => __( '&mdash; Select &mdash;' ),
                      'option_none_value' => '0',
                      'post_type' => get_post_types( array( 'hierarchical' => true ) ),
                      'selected' => $data[ 'root_page' ]
                    ) ); ?>
                  </label>
                </li>
              <?php } ?>

              <li settings_wrapper="flawless_options_wrapper" class="flawless_show_advanced"
                text_if_shown="<?php _e( 'Hide Advanced', 'flawless' ) ?>"
                text_if_hidden="<?php _e( 'Show Advanced', 'flawless' ) ?>">
                <?php _e( 'Show Advanced', 'flawless' ) ?>
              </li>

              <?php do_action( 'flawless_post_types_advanced_options', array(
                  'type' => $type,
                  'data' => $data,
                  'fs' => $flawless )
              ); ?>

              <li class="flawless_advanced_option">
                <label>
                  <input type="checkbox" <?php checked( 'true', $data[ 'exclude_from_search' ] ); ?>
                    name="flawless_settings[post_types][<?php echo $type; ?>][exclude_from_search]" value="true"/>
                  <?php _e( 'Exclude from search.', 'flawless' ) ?>
                </label>
              </li>

              <li class="flawless_advanced_option">
                <label>
                  <input type="checkbox" <?php checked( 'true', $data[ 'custom_fields' ] ); ?>
                    name="flawless_settings[post_types][<?php echo $type; ?>][custom_fields]" value="true"/>
                  <?php _e( 'Enable Custom Fields metabox.', 'flawless' ) ?>
                </label>
              </li>

              <li class="flawless_advanced_option">
                <label>
                  <input type="checkbox" <?php checked( 'true', $data[ 'disabled' ] ); ?>
                    name="flawless_settings[post_types][<?php echo $type; ?>][disabled]" value="true"/>
                  <?php _e( 'Disable this content type, and hide all related content.', 'flawless' ) ?>
                </label>
              </li>

              <?php if ( $data[ 'flawless_post_type' ] == 'true' ) { ?>
                <li class="flawless_advanced_option">
                  <label>
                    <input type="checkbox" <?php checked( 'true', $data[ 'hierarchical' ] ); ?>
                      name="flawless_settings[post_types][<?php echo $type; ?>][hierarchical]" value="true"/>
                    <?php _e( 'Use this content in a hierarchical manner.', 'flawless' ) ?>
                  </label>
                </li>
              <?php } ?>

              <?php if ( $data[ 'flawless_post_type' ] == 'true' ) { ?>
                <li class="flawless_advanced_option">
                  <label>
                    <?php _e( 'Rewrite slug:', 'flawless' ) ?>
                    <input type="text" class="regular-text"
                      name="flawless_settings[post_types][<?php echo $type; ?>][rewrite_slug]"
                      value="<?php echo $data[ 'rewrite_slug' ]; ?>"/>
                  </label>
                </li>
              <?php } ?>

              <li class="flawless_advanced_option">
                <?php _e( 'Associated Taxonomies:', 'flawless' ); ?>
                <ul class="wp-tab-panel">
                  <?php foreach ( $data[ 'taxonomies' ] as $taxonomy => $enabled ) { ?>
                    <li>
                      <input
                        id="<?php echo $taxonomy; ?>_to_<?php echo $type; ?>" <?php checked( 'enabled', $enabled ); ?>
                        type="checkbox"
                        name="flawless_settings[post_types][<?php echo $type; ?>][taxonomies][<?php echo $taxonomy; ?>]"
                        value="enabled"/>
                      <label
                        for="<?php echo $taxonomy; ?>_to_<?php echo $type; ?>"><?php echo $flawless[ 'taxonomies' ][ $taxonomy ][ 'label' ] ? $flawless[ 'taxonomies' ][ $taxonomy ][ 'label' ] : $taxonomy; ?></label>
                    </li>
                  <?php } ?>
                </ul>
              </li>
            </ul> <?php /* .flawless_options_wrapper */ ?>

          </div> <?php /* .flawless_content_type_options.flawless_half_width */ ?>

          <div class="flawless_associated_widget_areas flawless_half_width">
            <?php foreach ( ( array ) $flawless[ 'widget_area_sections' ] as $was_slug => $was_data ) {
              $these_sidebars = $flawless[ 'views' ][ 'post_types' ][ $type ][ 'widget_areas' ][ $was_slug ]; ?>
              <div class="flawless_was_pane" was_slug="<?php echo $was_slug; ?>">
                <h3 class="flawless_was_pane_title"><?php echo $was_data[ 'label' ]; ?></h3>
                <ul class="flawless_widget_area_list" type="widget_area_holder">

                  <?php foreach ( ( array ) $these_sidebars as $sidebar_id ) {

                    Management::flawless_widget_item( array(
                      'sidebar_id' => $sidebar_id,
                      'was_slug' => $was_slug,
                      'post_type' => $type,
                      'sidebar_data' => $sidebar_data
                    ) );

                  }
                  ?>

                </ul>
              </div>
            <?php } ?>
          </div>

          <input type="hidden" class="flawless_added_post_type"
            name="flawless_settings[post_types][<?php echo $type; ?>][flawless_post_type]"
            value="<?php echo $data[ 'flawless_post_type' ] ? $data[ 'flawless_post_type' ] : 'false'; ?>"/>

        </div> <?php /*  .flawless_content_type_module*/ ?>

      <?php } /* end post_type loop */ ?>

      <div class="flawless_actions">
        <input type="button" element_wrapper=".flawless_actions" class="flawless_add_row button-secondary"
          callback_function="flawless_added_custom_post_type"
          value="<?php _e( 'Add Content Type', 'flawless' ) ?>"/>
      </div>

    </div> <?php /*  .ud_ui_dynamic_table */ ?>

    <div class="tab_description"><?php _e( 'Manage taxonomies.', 'flawless' ); ?></div>

    <div class="ud_ui_dynamic_table flawless_content_inner">

      <?php foreach ( ( array ) $flawless[ 'taxonomies' ] as $taxonomy_type => $taxonomy_data ) { ?>

        <div class="flawless_content_type_module flawless_dynamic_table_row" content_type="taxonomies"
          slug="<?php echo $taxonomy_type; ?>" new_row="false"
          lock_row="<?php echo( $taxonomy_data[ 'flawless_taxonomy' ] == 'true' ? 'false' : 'true' ); ?>">

          <div class="ct_header">
            <input class="slug_setter" type="text"
              name="flawless_settings[taxonomies][<?php echo $taxonomy_type; ?>][label]"
              value="<?php echo $taxonomy_data[ 'label' ]; ?>"/>

            <ul class="flawless_dropdown_options" show_on_clone="true">
              <li class="flawless_delete_row" verify_action="true">Delete</li>
            </ul>

            <span class="content_type_label"><?php _e( 'Taxonomy', 'flawless' ); ?></span>

          </div>

          <div class="flawless_taxonomy_options flawless_half_width">

            <ul class="flawless_options_wrapper flawless_advanced_content_type_options">

              <li class="flawless_option">
                <label>
                  <input type="checkbox" <?php checked( 'true', $taxonomy_data[ 'hierarchical' ] ); ?>
                    name="flawless_settings[taxonomies][<?php echo $taxonomy_type; ?>][hierarchical]"
                    value="true"/>
                  <?php _e( 'Hierarchical.', 'flawless' ) ?>
                </label>
              </li>

              <li class="flawless_option">
                <label>
                  <input type="checkbox" <?php checked( 'true', $taxonomy_data[ 'exclude_from_search' ] ); ?>
                    name="flawless_settings[taxonomies][<?php echo $taxonomy_type; ?>][exclude_from_search]"
                    value="true"/>
                  <?php _e( 'Exclude from search.', 'flawless' ) ?>
                </label>
              </li>

              <?php if ( current_theme_supports( 'extended-taxonomies' ) ) { ?>
                <li class="flawless_option">
                  <label>
                    <input type="checkbox" <?php checked( 'true', $taxonomy_data[ 'allow_term_thumbnail' ] ); ?>
                      name="flawless_settings[taxonomies][<?php echo $taxonomy_type; ?>][allow_term_thumbnail]"
                      value="true"/>
                    <?php _e( 'Enable featured images.', 'flawless' ) ?>
                  </label>
                </li>
              <?php } ?>

              <?php /*
              <li class="flawless_option">
                <label>
                  <input type="checkbox" <?php checked( 'true', $taxonomy_data[ 'show_tagcloud' ] ); ?>  name="flawless_settings[taxonomies][<?php echo $taxonomy_type; ?>][show_tagcloud]" value="true" />
                  <?php _e( 'Show tagcloud.' , 'flawless' ) ?>
                </label>
              </li>*/
              ?>

              <?php if ( $taxonomy_data[ 'flawless_taxonomy' ] == 'true' ) { ?>
                <li class="flawless_option">
                  <label>
                    <?php _e( 'Rewrite slug:', 'flawless' ) ?>
                    <input type="text" class="regular-text"
                      name="flawless_settings[taxonomies][<?php echo $taxonomy_type; ?>][rewrite_slug]"
                      value="<?php echo $taxonomy_data[ 'rewrite_slug' ]; ?>"/>
                  </label>
                </li>
              <?php } ?>

              <?php do_action( 'flawless_taxonomies_advanced_options', array(
                  'type' => $taxonomy_type,
                  'data' => $taxonomy_data,
                  'fs' => $flawless )
              ); ?>

            </ul> <?php /* .flawless_options_wrapper */ ?>

          </div> <?php /* .flawless_taxonomy_options.flawless_half_width */ ?>

          <div class="flawless_associated_widget_areas flawless_half_width">
            <?php  foreach ( ( array ) $flawless[ 'widget_area_sections' ] as $was_slug => $was_data ) {

              $these_sidebars = $taxonomy_data[ 'widget_areas' ][ $was_slug ];

              ?>
              <div class="flawless_was_pane" was_slug="<?php echo $was_slug; ?>">
                <h3 class="flawless_was_pane_title"><?php echo $was_data[ 'label' ]; ?></h3>
                <ul class="flawless_widget_area_list" type="widget_area_holder">

                  <?php foreach ( ( array ) $these_sidebars as $sidebar_id ) {

                    Management::flawless_widget_item( array(
                      'sidebar_id' => $sidebar_id,
                      'was_slug' => $was_slug,
                      'taxonomy_type' => $taxonomy_type,
                      'sidebar_data' => $sidebar_data
                    ) );

                  }
                  ?>

                </ul>
              </div>
            <?php } ?>
          </div>

          <input type="hidden" class="flawless_added_taxonomy"
            name="flawless_settings[taxonomies][<?php echo $taxonomy_type; ?>][flawless_taxonomy]"
            value="<?php echo $taxonomy_data[ 'flawless_taxonomy' ] ? $taxonomy_data[ 'flawless_taxonomy' ] : 'false'; ?>"/>

        </div> <?php /*  .flawless_content_type_module*/ ?>

      <?php } /* end taxonomies loop */ ?>

      <div class="flawless_actions">
        <input type="button" element_wrapper=".flawless_actions" class="flawless_add_row button-secondary"
          callback_function="flawless_added_custom_taxonomy" value="<?php _e( 'Add Taxonomy', 'flawless' ) ?>"/>
      </div>

    </div> <?php /*  .ud_ui_dynamic_table */ ?>

    </div> <?php /* .flawless_content_body */ ?>

    <div class="clear"></div>
    </div> <?php /* .flawless_content_ui */ ?>

    <?php

    }

    /**
     * Design Related Options
     *
     * @author potanin@UD
     * @since Flawless 0.5.0
     */
    function options_ui_design( $flawless ) {

      $current_theme = get_theme( get_current_theme() );

      //** Determine if current theme is a child theme theme */
      if ( $current_theme[ 'Template Dir' ] != $current_theme[ 'Stylesheet Dir' ] ) {
        $child_theme_screen = trailingslashit( get_stylesheet_directory_uri() ) . $current_theme[ 'Screenshot' ];
      } else {
        $child_theme_screen = false;
      }

      ?>

      <div class="tab_description">
      <?php if ( $child_theme_screen ) {
        _e( 'Color scheme selection and other design & layout related settings. You have an active Child Management, which may override some of the settings you can configure here.', 'flawless' );
      } else {
        _e( 'Color scheme selection and other design & layout related settings.', 'flawless' );
      } ?>
    </div>

      <table class="form-table">
      <tbody>

      <tr valign="top" class="flawless_design_common_settings">
        <th><?php _e( 'Common Settings', 'flawless' ); ?></th>
        <td>
          <ul>
            <li>
              <label>
                <?php _e( 'Maximum Layout Width: ', 'flawless' ); ?>
                <div class="input-append">
                  <input type="text" name="flawless_settings[layout_width]" class="small-text" placeholder="1090"
                    value="<?php echo $flawless[ 'layout_width' ]; ?>"/>
                  <span class="add-on">px</span>
                </div>
                <div
                  class="description"><?php _e( 'Override the default layout width of 1090px. This is the maximum width, which means the layout will be resized on smaller devices. ', 'flawless' ); ?></div>
              </label>
            </li>
            <?php if ( current_theme_supports( 'custom-header' ) ) { ?>
              <li>
                <label>
                  <?php _e( 'Header image dimensions: ', 'flawless' ); ?>
                  <div class="input-append">
                    <input type="text" name="flawless_settings[header_image_width]" class="small-text"
                      value="<?php echo HEADER_IMAGE_WIDTH; ?>"/>
                    <span class="add-on">px</span>
                  </div>
                </label>
                <label>
                  <?php _e( ' by ', 'flawless' ); ?>
                  <div class="input-append">
                    <input type="text" name="flawless_settings[header_image_height]" class="small-text"
                      value="<?php echo HEADER_IMAGE_HEIGHT; ?>"/>
                    <span class="add-on">px</span>
                  </div>
                </label>. <a href="<?php admin_url( 'themes.php?page=custom-header' ); ?>"
                  class="button"><?php _e( 'Edit header image. ', 'flawless' ); ?></a>
              </li>
            <?php } ?>

            <?php do_action( 'flawless::options_ui_design::common_settings', $flawless ); ?>
          </ul>
        </td>
      </tr>

      <tr valign="top" class="flawless_design_common_settings">
        <th><?php _e( 'Style Options', 'flawless' ); ?></th>
        <td>
          <table>

            <?php foreach ( $flawless[ 'css_options' ] as $option ) { ?>
              <?php echo Management::render_theme_option_input( $option, array( 'output' => 'table_row' ) ); ?>
            <?php } ?>

            <?php do_action( 'flawless::options_ui_design::style_options', $flawless ); ?>
          </table>
        </td>
      </tr>

      <?php if ( current_theme_supports( 'custom-skins' ) ) { ?>
        <tr valign="top" class="color_schemes">
          <th><?php _e( 'Skin Selection', 'flawless' ); ?></th>
          <td>
            <?php /* <div class="description"><?php _e( 'A Skin is like a child theme, and usually includes custom colors, spacing, and fonts. ', 'flawless' ); ?></div> */ ?>
            <?php echo Management::skin_selection(); ?></td>
        </tr>
      <?php } ?>

      </tbody>
    </table>

    <?php

    }

    /**
     * Advanced Options Page
     *
     * @todo 'Reset Flexible Layout' should be inserted via Editor API
     * @since Flawless 0.2.3
     */
    function options_ui_advanced( $flawless ) {
      ?>

      <div
        class="tab_description"><?php _e( 'Consult documentation before making changes on the Advanced tab.', 'flawless' ); ?></div>

      <table class="form-table">
      <tbody>
      <tr>

      <tr>
        <th><?php _e( 'Common Actions', 'flawless' ); ?></th>
        <td>
          <ul class="flawless" flawless="action_list">
            <li flawless_action="clean_up_revisions" class="flawless_action"
              processing_label="<?php _e( 'Processing...', 'flawless' ); ?>">
              <span class="button execute_action"><?php _e( 'Clean Up Post Revisions', 'flawless' ); ?></span>
              <span
                class="description"><?php printf( __( 'Check all post types that support revisions, and remove all but the %1s most recent.', 'flawless' ), intval( defined( 'WP_POST_REVISIONS' ) ? WP_POST_REVISIONS : 3 ) ); ?></span>
            </li>
          </ul>
        </td>
      </tr>

      <th><?php _e( 'Advanced Settings', 'flawless' ); ?></th>
      <td>
        <ul class="wp-tab-panel">
          <li>
            <label>
              <input type="checkbox"
                <?php checked( $flawless[ 'maintanance_mode' ], 'true' ); ?>name="flawless_settings[maintanance_mode]"
                value="true"/>
              <?php _e( 'Put site into maintanance mode.', 'flawless' ); ?>
            </label>

            <div
              class="description"><?php _e( 'Maintanance mode will display a splash image on front-end for non-administrators while you make changes.', 'flawless' ); ?></div>
          </li>

          <li>
            <label>
              <input type="checkbox" <?php checked( $flawless[ 'developer_mode' ], 'true' ); ?>
                name="flawless_settings[developer_mode]" value="true"/>
              <?php _e( 'Enable developer and debug mode.', 'flawless' ); ?>
            </label>
          </li>

          <li>
            <label>
              <input type="checkbox" <?php checked( $flawless[ 'console_log_options' ][ 'show_log' ], 'true' ); ?>
                name="flawless_settings[console_log_options][show_log]" value="true"/>
              <?php _e( 'Display basic log entries in console log.', 'flawless' ); ?>
              <span
                class="description"><?php _e( 'Error and Info log entries are always displayed (when debug mode is enabled).', 'flawless' ); ?></span>
            </label>
          </li>

          <li>
            <label>
              <input type="checkbox" <?php checked( $flawless[ 'do_not_compile_plugin_css' ], 'true' ); ?>
                name="flawless_settings[do_not_compile_plugin_css]" value="true"/>
              <?php _e( 'Exclude CSS added by plugins from compilation.', 'flawless' ); ?>
            </label>
          </li>

          <li>
            <label>
              <input type="checkbox" <?php checked( $flawless[ 'visual_debug' ], 'true' ); ?>
                name="flawless_settings[visual_debug]" value="true"/>
              <?php _e( 'Enable visual debug mode.', 'flawless' ); ?>
              <span
                class="description"><?php _e( 'Adds additional markup to front-end for layout design.', 'flawless' ); ?></span>
            </label>
          </li>

          <li>
            <label>
              <input type="checkbox" <?php checked( $flawless[ 'disable_updates' ][ 'plugins' ], 'true' ); ?>
                name="flawless_settings[disable_updates][plugins]" value="true"/>
              <?php _e( 'Disable WordPress plugin update notifications.', 'flawless' ); ?>
            </label>
          </li>

          <?php do_action( 'flawless::options_ui_advanced::advanced_settings', $flawless ); ?>

        </ul>
      </td>
      </tr>

      <tr valign="top" class="flawless_javaScript_enhancements">
        <th><?php _e( 'JavaScript Enhancements', 'flawless' ); ?></th>
        <td>
          <ul class="wp-tab-panel">
            <li><label><input type="checkbox" <?php checked( 'true', $flawless[ 'disable_masonry' ] ); ?>
                  name="flawless_settings[disable_masonry]"
                  value="true"/> <?php _e( 'Disable Masonry.', 'flawless' ); ?> <span
                  class="description"><?php _e( 'Removes masonry library, which is applied to Galleries, or on demand using .enable-masonry class.', 'flawless' ); ?></span></label>
            </li>
            <li><label><input type="checkbox" <?php checked( 'true', $flawless[ 'disable_equalheights' ] ); ?>
                  name="flawless_settings[disable_equalheights]"
                  value="true"/> <?php _e( 'Disable EqualHeights.', 'flawless' ); ?></label></li>
            <li><label><input type="checkbox" <?php checked( 'true', $flawless[ 'enable_lazyload' ] ); ?>
                  name="flawless_settings[enable_lazyload]"
                  value="true"/> <?php _e( 'Enable LazyLoad.', 'flawless' ); ?> <span
                  class="description"><?php _e( 'Add .lazy class to images to use.', 'flawless' ); ?></span></label>
            </li>
            <li><label><input type="checkbox" <?php checked( 'true', $flawless[ 'enable_google_pretify' ] ); ?>
                  name="flawless_settings[enable_google_pretify]"
                  value="true"/> <?php _e( 'Enable Google Pretify.', 'flawless' ); ?></label></li>
            <li><label><input type="checkbox" <?php checked( 'true', $flawless[ 'enable_dynamic_filter' ] ); ?>
                  name="flawless_settings[enable_dynamic_filter]"
                  value="true"/> <?php _e( 'Enable Dynamic Filter.', 'flawless' ); ?></label></li>
            <li><label><input type="checkbox" <?php checked( 'true', $flawless[ 'disable_form_helper' ] ); ?>
                  name="flawless_settings[disable_form_helper]"
                  value="true"/> <?php _e( 'Disable Form Helper.', 'flawless' ); ?> </label></li>
            <li><label><input type="checkbox" <?php checked( 'true', $flawless[ 'disable_fancybox' ] ); ?>
                  name="flawless_settings[disable_fancybox]"
                  value="true"/> <?php _e( 'Disable FancyBox.', 'flawless' ); ?> <span
                  class="description"><?php _e( 'Enabled by default and applied to all images.', 'flawless' ); ?></span></label>
            </li>
          </ul>
        </td>
      </tr>

      <tr valign="top" class="flawless_backup_and_restoration">
        <th><?php _e( 'Backup and Restoration', 'flawless' ); ?></th>
        <td>
          <ul class="flawless" flawless="action_list">
            <li>
              <a class="button"
                href="<?php echo wp_nonce_url( "themes.php?page=functions.php&flawless_action=download-backup", 'download-flawless-backup' ); ?>">
                <?php _e( 'Download Configuration Backup', 'flawless' ); ?>
              </a>
              <span
                class="description"><?php _e( 'Export the entire configuration into a .json file, which may be restored to this site, or another.', 'flawless' ); ?></span>
            </li>
            <li>
              <?php _e( 'Restore from file', 'flawless' ); ?>: <input name="flawless_settings[settings_from_backup]"
                type="file"/>
              <span class="description"><?php _e( 'Backup will overwrite all current settings.', 'flawless' ); ?></span>
            </li>
          </ul>
        </td>
      </tr>

      <tr>
        <th><?php _e( 'Debugging', 'flawless' ); ?></th>
        <td>
          <ul class="flawless" flawless="action_list">

            <li flawless_action="show_permalink_structure" class="flawless_action"
              processing_label="<?php _e( 'Processing...', 'flawless' ); ?>">
              <span class="button execute_action"><?php _e( 'Show Permalink Structure', 'flawless' ); ?></span>
            </li>

            <li>
            <li flawless_action="show_flawless_configuration" class="flawless_action"
              processing_label="<?php _e( 'Processing...', 'flawless' ); ?>">
              <span class="button execute_action"><?php _e( 'Show Flawless Configuration', 'flawless' ); ?></span>
            </li>

          </ul>

        </td>
      </tr>

      <tr>
        <th><?php _e( 'Advanced Actions', 'flawless' ); ?></th>
        <td>
          <ul class="flawless" flawless="action_list">

            <?php if ( current_theme_supports( 'frontend-editor' ) ) { ?>
              <li flawless_action="delete_flex_settings" class="flawless_action"
                processing_label="<?php _e( 'Processing...', 'flawless' ); ?>"
                verify_action="<?php _e( 'Are you sure?', 'flawless' ); ?>">
                <span class="button execute_action"><?php _e( 'Reset Flexible Layout', 'flawless' ); ?></span>
                <span
                  class="description"><?php _e( 'Delete all flexible layout ( header and footer ) settings and reset to default.', 'flawless' ); ?></span>
              </li>
            <?php } ?>

            <li flawless_action="delete_all_settings" class="flawless_action"
              processing_label="<?php _e( 'Processing...', 'flawless' ); ?>"
              verify_action="<?php _e( 'You are about to delete all theme settings, are you sure?', 'flawless' ); ?>">
              <span class="button execute_action"><?php _e( 'Delete all Management Settings', 'flawless' ); ?></span>
              <span
                class="description"><?php _e( 'Completely remove all Flawless Management settings and reset to default.', 'flawless' ); ?></span>
            </li>

          </ul>

        </td>
      </tr>

      </tbody>
    </table>

    <?php
    }

    /**
     * Renders Skin Selection
     *
     * @todo 'Reset Flexible Layout' should be inserted via Editor API
     * @since Flawless 0.5.0
     */
    function skin_selection( $args = false ) {
      global $flawless;

      $color_schemes = Flawless::get_color_schemes();

      ob_start(); ?>

      <ul class="flawless_color_schemes block_options">
      <?php foreach ( ( array ) $color_schemes as $scheme => $scheme_data ) { ?>
        <li class="flawless_setup_option_block">
          <?php if ( $scheme_data[ 'thumb_url' ] ) { ?>
            <div class="skin_thumb_placeholder">
              <img class="skin_thumb" src="<?php echo $scheme_data[ 'thumb_url' ]; ?>"
                title="<?php echo esc_attr( $scheme_data[ 'Name' ] ); ?>"/>
            </div>
          <?php } else { ?>
            <div class="skin_thumb_placeholder flawless_no_image">
              <img class="skin_thumb" src="<?php echo Flawless::load( 'no-skin-thumbanil.gif', 'image' ); ?>"
                title="<?php _e( 'No Thumbnail Found', 'flawless' ); ?>"/>
            </div>
          <?php } ?>
          <input class="checkbox"
            group="flawless_color_scheme" <?php checked( $scheme, $flawless[ 'color_scheme' ] ); ?> type="checkbox"
            name="flawless_settings[color_scheme]" id="color_scheme_<?php echo $scheme; ?>"
            value="<?php echo $scheme; ?>"/>

          <div class="option_note">
            <strong><?php echo $scheme_data[ 'Name' ]; ?></strong><br/><?php echo $scheme_data[ 'Description' ]; ?>
          </div>
        </li>
      <?php } ?>
        <li class="flawless_setup_option_block">
        <div class="skin_thumb_placeholder flawless_no_image">
          <img class="skin_thumb" src="<?php echo Flawless::load( 'no-skin-thumbanil.gif', 'image' ); ?>"
            title="<?php _e( 'No Thumbnail Found', 'flawless' ); ?>"/>
        </div>
        <input class="checkbox" group="flawless_color_scheme" <?php checked( false, $flawless[ 'color_scheme' ] ); ?>
          type="checkbox" name="flawless_settings[color_scheme]" id="color_scheme_<?php echo $scheme; ?>"
          value=""/>

        <div class="option_note">
          <strong><?php _e( 'No Skin', 'flawless' ); ?></strong><br/><?php _e( 'Twitter Bootstrap, Base and plugin-specific (when available) CSS only.', 'flawless' ); ?>
        </div>
      </li>
    </ul>

      <?php

      $html = ob_get_contents();
      ob_end_clean();

      return $html;

    }

    /**
     * Render widget area item
     *
     * @todo 'Reset Flexible Layout' should be inserted via Editor API
     * @since Flawless 0.2.3
     */
    function flawless_widget_item( $args = false ) {
      global $flawless;

      $sidebar_id = $args[ 'sidebar_id' ];

      $description = $flawless[ 'widget_areas' ][ 'all' ][ $sidebar_id ][ 'description' ] ? $flawless[ 'widget_areas' ][ 'all' ][ $sidebar_id ][ 'description' ] : '';
      $sidebar_data = $flawless[ 'widget_areas' ][ 'all' ][ $sidebar_id ];

      $sidebar_data[ 'name' ] = $sidebar_data[ 'name' ] ? $sidebar_data[ 'name' ] : __( 'Missing Title: ', 'flawless' ) . $sidebar_id;

      $classes = array( 'flawless_widget_item' );

      if ( $args[ 'sidebar_data' ][ 'flawless_widget_area' ] ) {
        $classes[ ] = 'flawless_widget_area';
      }

      if ( !empty( $sidebar_data[ 'description' ] ) ) {
        $classes[ ] = 'have_description';
      }

      ?>

      <li class="<?php echo implode( ' ', ( array ) $classes ); ?>" sidebar_name="<?php echo $sidebar_id; ?>"
        do_not_clone="true"
        flawless_widget_area="<?php echo $args[ 'sidebar_data' ][ 'flawless_widget_area' ] ? 'true' : 'false'; ?>">
      <div class="handle"></div>

        <?php if ( $args[ 'sidebar_data' ][ 'flawless_widget_area' ] == 'true' ) { ?>

          <input type="text" name="flawless_settings[flawless_widget_areas][<?php echo $sidebar_id; ?>][label]"
            class="flawless_wa" attribute="name" value="<?php echo $sidebar_data[ 'name' ] ?>"/>
          <input type="hidden" name="flawless_settings[flawless_widget_areas][<?php echo $sidebar_id; ?>][class]"
            class="flawless_wa" attribute="class" value="<?php echo $sidebar_data[ 'class' ]; ?>"/>

        <?php } else { ?>

          <input type="text" class="flawless_wa" attribute="name" value="<?php echo $sidebar_data[ 'name' ] ?>"
            readonly="true"/>
          <div class="flawless_wa" attribute="description"><?php echo $sidebar_data[ 'description' ]; ?></div>

        <?php } ?>

        <?php if ( isset( $args[ 'post_type' ] ) ) { ?>
          <input do_not_clone="true" type="hidden"
            name="flawless_settings[post_types][<?php echo $args[ 'post_type' ]; ?>][widget_areas][<?php echo $args[ 'was_slug' ]; ?>][]"
            value="<?php echo $sidebar_id; ?>"/>
        <?php } ?>

        <?php if ( isset( $args[ 'taxonomy_type' ] ) ) { ?>
          <input do_not_clone="true" type="hidden"
            name="flawless_settings[taxonomies][<?php echo $args[ 'taxonomy_type' ]; ?>][widget_areas][<?php echo $args[ 'was_slug' ]; ?>][]"
            value="<?php echo $sidebar_id; ?>"/>
        <?php } ?>

        <div
          class="delete" <?php echo $args[ 'widget_area_selector' ] ? 'verify_action="Are you sure? You cannot undo this."' : ''; ?>></div>
    </li>

    <?php

    }

    /**
     * Render option for LESS/CSS options
     *
     * @since Flawless 0.6.1
     */
    function render_theme_option_input( $option = false, $args = '' ) {
      global $flawless;

      $args = wp_parse_args( $args, array(
        'output' => 'table_row'
      ) );

      switch ( $option[ 'type' ] ) {

        case 'color':
          $html[ ] = '<input type="text" name="flawless_settings[css_options][' . $option[ 'name' ] . '][value]" class="regular-text flawless_color_picker" placeholder="" value="' . esc_attr( $flawless[ 'css_options' ][ $option[ 'name' ] ][ 'value' ] ) . '" />';
          break;

        case 'font':
          $html[ ] = '<input type="text" name="flawless_settings[css_options][' . $option[ 'name' ] . '][value]" class="regular-text" placeholder="" value="' . esc_attr( $flawless[ 'css_options' ][ $option[ 'name' ] ][ 'value' ] ) . '" />';

          break;

        case 'pixels':
          $html[ ] = '<div class="input-append">';
          $html[ ] = '<input type="text" name="flawless_settings[css_options][' . $option[ 'name' ] . '][value]" class="small-text" placeholder="" value="' . esc_attr( $flawless[ 'css_options' ][ $option[ 'name' ] ][ 'value' ] ) . '" />';
          $html[ ] = '<span class="add-on">px</span>';
          $html[ ] = '</div>';
          break;

        case 'percentage':
          $html[ ] = '<div class="input-append">';
          $html[ ] = '<input type="text" name="flawless_settings[css_options][' . $option[ 'name' ] . '][value]" class="small-text" placeholder="" value="' . esc_attr( $flawless[ 'css_options' ][ $option[ 'name' ] ][ 'value' ] ) . '" />';
          $html[ ] = '<span class="add-on">%</span>';
          $html[ ] = '</div>';
          break;

        case 'hidden':
        default:

          break;

      }

      if ( empty( $html ) ) {
        return;
      }

      if ( $option[ 'description' ] ) {
        $html[ ] = '<div class="description">' . $option[ 'description' ] . '</div>';
      }

      switch ( $args[ 'output' ] ) {
        case 'table_row':
          return '<tr><th>' . $option[ 'label' ] . '</th><td>' . implode( '', (array) $html ) . '</td></tr>';
          break;

      }

    }

  }

}