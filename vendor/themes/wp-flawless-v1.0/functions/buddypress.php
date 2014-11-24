<?php
/**
 * Name: BuddyPress Extensions
 * Version: 1.0
 * Description: Extra functionality for the BuddyPress plugin
 * Author: Usability Dynamics, Inc.
 *
 */


//** All BuddyPress actions are initialized after bb_include action is ran. */
add_action( 'flawless_theme_setup', array( 'Flawless_BuddyPress', 'flawless_theme_setup' ));

class Flawless_BuddyPress {

  /**
   * Force Flawless template_redirect to be loaded in BuddyPress
   *
   * @author potanin@UD
   */
  static function flawless_theme_setup($located_template) {
    global $bp, $flawless;

    if( !is_object($bp) ) {
      return;
    }

    add_theme_support( 'bbpress' );

    add_filter('bp_get_the_topic_post_content', array( 'flawless_shortcodes','do_code_shortcode'),0);
    add_filter('bp_get_the_topic_post_content', array( 'flawless_shortcodes','do_code_shortcode'),100);

    //** TEMPORARY SETTINGS */
    $flawless[ 'buddypress' ][ 'hide_group_search' ] = 'true';
    $flawless[ 'buddypress' ][ 'hide_top_pagination' ] = 'true';
    $flawless[ 'buddypress' ][ 'hide_group_admins' ] = 'true';
    $flawless[ 'buddypress' ][ 'navbar' ][ 'hide_dashboard' ] = 'true';
    $flawless[ 'buddypress' ][ 'navbar' ][ 'hide_random' ] = 'true';

    //** Add shortcodes */
    add_shortcode( 'buddy_press_groups', array( 'Flawless_BuddyPress', 'buddy_press_groups' ));
    add_shortcode( 'group_description', array( 'Flawless_BuddyPress', 'group_description' ));
    add_shortcode( 'group_meta', array( 'Flawless_BuddyPress', 'group_meta' ));
    add_shortcode( 'bp_registration_form', array( 'Flawless_BuddyPress', 'bp_registration_form' ));

    add_action( 'init', array( 'Flawless_BuddyPress', 'init' ));

    add_action( 'flawless::navbar_options', array( 'Flawless_BuddyPress', 'navbar_options' ) );
    add_action( 'flawless::use_navbar', array( 'Flawless_BuddyPress', 'use_navbar' ) );

    add_action( 'admin_init', array( 'Flawless_BuddyPress', 'admin_init'), 0);

    //** Stop the Admin Bar from being rendered by BuddyPress */
    remove_action( 'bp_init',    'bp_core_load_buddybar_css' );
    remove_action( 'wp_footer',    'bp_core_admin_bar', 8 );
    remove_action( 'admin_footer', 'bp_core_admin_bar'    );


    /** Determine if BP Template Pack is used */
    self::check_for_bp_template_pack();

    $params = array(
      'my_favs'           => __( 'My Favorites', 'buddypress' ),
      'accepted'          => __( 'Accepted', 'buddypress' ),
      'rejected'          => __( 'Rejected', 'buddypress' ),
      'show_all_comments' => __( 'Show all comments for this thread', 'buddypress' ),
      'show_all'          => __( 'Show all', 'buddypress' ),
      'comments'          => __( 'comments', 'buddypress' ),
      'close'             => __( 'Close', 'buddypress' ),
      'view'              => __( 'View', 'buddypress' )
    );

    wp_localize_script( 'flawless-asset-buddypress', 'BP_DTheme', $params );

    add_filter( 'body_class', array( 'Flawless_BuddyPress', 'body_class' ));

    add_action( 'bp_is_current_component', array( 'Flawless_BuddyPress', 'bp_is_current_component'), 2, 10);
    add_filter( 'flawless_request_type', array( 'Flawless_BuddyPress', 'flawless_request_type' ));
    add_filter( 'set_current_view', array( 'Flawless_BuddyPress', 'set_current_view' ));
    add_filter( 'bp_load_template', array( 'Flawless_BuddyPress', 'bp_load_template' ));

    add_filter( 'widgets_init', array( 'Flawless_BuddyPress', 'widgets_init' ));

    add_filter( 'bp_get_groups_pagination_count', array( 'Flawless_BuddyPress', 'render_pagination_count' ), 10 );
    add_filter( 'bp_get_groups_pagination_links', array( 'Flawless_BuddyPress', 'render_pagination_links' ), 10 );

    //** XProfile filters */
    add_filter( 'xprofile_filter_profile_group_tabs', array( 'Flawless_BuddyPress', 'profile_group_tabs'), 10, 3 );

    add_filter( 'bp_forum_topic_tag_list', array( 'Flawless_BuddyPress', 'bp_forum_topic_tag_list' ), 10 , 2 );

    add_filter( 'flawless::primary_notice_container', array( 'Flawless_BuddyPress', 'primary_notice_container' ), 10 );

    add_filter( 'flawless::my_account_url', array( 'Flawless_BuddyPress', 'my_account_url' ), 10 );

  }


  /**
   * Primary handler.
   *
   * @author potanin@UD
   */
  function init() {
    global $bp, $flawless;

    add_action( 'wp_ajax_bp_get_topics' , create_function( '', ' die(Flawless_BuddyPress::get_json_topics()); ' ) );
    add_action( 'wp_ajax_nopriv_bp_get_topics' , create_function( '', ' die(Flawless_BuddyPress::get_json_topics()); ' ) );

    /*  Add BuddyPress forum topics to  Google XML Sitemaps */
    add_action( 'sm_buildmap', array( 'Flawless_BuddyPress', 'sm_buildmap' ) );

    /* Automate redirection from /group/home to /group/ */
    add_action( 'groups_screen_group_home', array( 'Flawless_BuddyPress', 'groups_screen_group_home' ) );

    add_action( 'groups_custom_group_fields_editable', array( 'Flawless_BuddyPress', 'groups_custom_group_fields_editable' ) );
    add_action( 'groups_group_details_edited', array( 'Flawless_BuddyPress', 'groups_group_details_edited' ) );

    /** Theme Settings */
    add_action( 'flawless_options_ui_main', array( 'Flawless_BuddyPress', 'flawless_options_ui_main' ), 100 );

  }


  /**
   * Add option to edited page to use original content instead of BP override
   *
   * Only applied to "Root" BuddyPress pages such as Activity Streams, Discussion Forums, etc.
   * Child pages, such as /groups/some-group automatically override BP-generated pages when they exist.
   *
   * @author potanin@UD
   */
  function admin_init( $t ) {
    global $bp, $post, $wpdb;

    if( $_GET[ 'post' ] ) {
      $post_id = $_GET[ 'post' ];
    } else {
      return;
    }

    if( !in_array( $post_id, (array) bp_core_get_directory_page_ids() ) ) {
      return;
    }

    flawless_theme::add_post_type_option(array(
      'post_type' => $wpdb->get_var( "SELECT post_type FROM {$wpdb->posts} WHERE ID = $post_id" ),
      'position' => 1000,
      'meta_key' => 'override_buddypress_template',
      'label' => sprintf( __( 'Do not load BuddyPress content.' , 'flawless') )
    ));

  }


  /**
   * Add Theme Settings
   *
   * @author potanin@UD
   */
  function flawless_options_ui_main( $flawless ) { ?>

    <tr valign="top">
      <th><?php _e( 'BuddyPress', 'flawless' ); ?></th>
      <td>
        <ul>
          <li>
            <label>
              <?php _e( 'Forum sidebar:', 'flawless' ); ?>
              <select name="flawless_settings[buddypress][forum_sidebar]" class="flawless_live_data_target" flawless_live_data_target="widget_areas"/>
                <option value=""></option>
                <?php foreach( (array) $flawless[ 'widget_areas' ][ 'all' ] as $sidebar_id => $sidebar_data ) { ?>
                  <option value="<?php echo $sidebar_id; ?>" <?php echo selected( $sidebar_id, $flawless[ 'buddypress' ][ 'forum_sidebar'] ); ?>  ><?php echo $sidebar_data[ 'name' ]; ?></option>
                <?php } ?>
              </select>
              </label>
            </li>
        </ul>
      </td>
    </tr>

  <?php
  }


  /**
   * Automatically redirects /group/home to /group/
   *
   * @todo Bugs out. - potanin@UD
   * @action groups_screen_group_home (1)
   * @author UsabilityDynamics.com
   */
  function groups_screen_group_home() {
    global $bp;

    if( isset( $_GET['n'] ) || empty( $bp->groups->current_group ) ) {
      return;
    }

    /** Split up the current request */
    $segments = spliti('/', trim($_SERVER['REQUEST_URI'], '/'));
    $last_segment = array_pop($segments);

    $redirect = false;

    /** If we're on the home page, redirect */
    if($last_segment == "home") $redirect = true;

    /** If we're on the members page, and we are hiding the member list */
    $hide_member_list = groups_get_groupmeta( $bp->groups->current_group->id, 'hide_member_list' );
    if($last_segment == "members" && $hide_member_list == 'true') $redirect = true;

    /** Redirect if we need */
    if($redirect) die(wp_redirect( bp_get_group_permalink( $bp->groups->current_group ) ));

  }


  /**
   * Save extra group attributes, fired off only on group update only.
   *
   * @action groups_group_details_edited
   * @author UsabilityDynamics.com
   */
  function groups_group_details_edited( $group_id = false ) {

    if( !$group_id ) {
      return;
    }


    foreach( ( array ) $_REQUEST['bp_group_data'] as $meta_key => $meta_value ){
      groups_update_groupmeta( $group_id, $meta_key , $meta_value );
    }

  }

  /**
   * Add extra configuration options to Group Editing page.
   *
   * @author potanin@UD
   */
  function groups_custom_group_fields_editable( $t ) {
      global $bp, $groups_template, $wp_registered_sidebars;

      /* Don't display this on new group creation, only on saved groups */
      if( !$groups_template ) {
        return;
      }

      ?>

      <input type="hidden" name="bp_group_data[hide_header]" value="">
      <input type="hidden" name="bp_group_data[hide_navigation]" value="">
      <input type="hidden" name="bp_group_data[hide_member_list]" value="">


      <div class="control-group">
        <label class="control-label"><?php _e( 'Forum & Topics', 'flawless' ) ?></label>

        <div class="controls">
          <textarea name="ud_data[new_topic_entry_intro]" class="span7"><?php echo stripslashes( groups_get_groupmeta( $bp->groups->current_group->id, 'new_topic_entry_intro' ) ); ?></textarea>
          <p class="help-block"><?php _e( 'New Topic Entry intro text, displayed above the text area.', 'flawless' ) ?></p>
        </div>

        <div class="controls">
          <textarea name="ud_data[new_topic_entry_help]" class="span7"><?php echo stripslashes( groups_get_groupmeta( $bp->groups->current_group->id, 'new_topic_entry_help' ) ); ?></textarea>
          <p class="help-block"><?php _e( 'New Topic Entry help text, displayed below the textarea.', 'flawless' ) ?></p>
        </div>

      </div>

      <div class="control-group">
        <label class="control-label"><?php _e( 'Display Settings', 'flawless' ) ?></label>
        <div class="controls">
          <label class="checkbox">
            <input type="checkbox" name="bp_group_data[hide_header]" value="true" <?php checked( groups_get_groupmeta( $bp->groups->current_group->id, 'hide_header' ), 'true' ); ?>> <?php _e( 'Hide header.', 'flawless'); ?>
          </label>
          <label class="checkbox">
            <input type="checkbox" name="bp_group_data[hide_navigation]" value="true" <?php checked( groups_get_groupmeta( $bp->groups->current_group->id, 'hide_navigation' ), 'true' ); ?>> <?php _e( 'Hide navigation.', 'flawless'); ?>
          </label>
          <label class="checkbox">
            <input type="checkbox" name="bp_group_data[hide_member_list]" value="true" <?php checked( groups_get_groupmeta( $bp->groups->current_group->id, 'hide_member_list' ), 'true' ); ?>> <?php _e( 'Hide member list.', 'flawless'); ?>
          </label>
        </div>
      </div>

      <div class="control-group">
        <label class="control-label"><?php _e( 'Right Sidebar' , 'flawless' ) ?></label>
        <div class="controls">
          <select name="bp_group_data[right_sidebar]">
            <option value="">
            <?php foreach( (array) $wp_registered_sidebars as $sidebar_id => $sidebar_data ) { ?>
            <option <?php selected( groups_get_groupmeta( $bp->groups->current_group->id, 'right_sidebar' ), $sidebar_id ); ?> value="<?php echo $sidebar_id; ?>"><?php echo $sidebar_data['name']; ?></option>
            <?php } ?>
          </select>
        </div>
      </div>

      <?php
  }


  /**
   * Make BuddyPress be recognized as a seprate view
   *
   * @author potanin@UD
   */
  function flawless_request_type( $t ) {
    global $bp, $post;

    if( empty($bp->current_component) || ( get_post_meta( $post->ID, 'override_buddypress_template', true ) == 'true' && $bp->current_action == 'home' ) || $bp->use_default_page) {
      return $t;
    }

    $t[ 'group' ] = 'buddy_press';
    $t[ 'type' ] = $bp->current_component;
    $t[ 'view' ] = $bp->current_action;

    flawless_theme::console_log( 'P: Current View: Unknown - rendering same as Page.');

    return $t;

  }


  /**
   * Make BuddyPress be recognized as a seprate view
   *
   *
   * @todo This could be improved - if a Right sidebar is enabled for a group, then the function stops without checking for other sidebars - potanin@UD
   * @todo get_post_meta( $post->ID, 'override_buddypress_template', true ) kicks in for non-primary pages as well, while it should be ignored on secondary pages (such as /forum/ ) - potanin@UD
   * @author potanin@UD
   */
  function set_current_view( $view ) {
    global $bp, $post, $wp_query, $flawless;

    //** If this is a "home" action and override enabled, we do nothing */
    if( empty($bp->current_component) || ( get_post_meta( $post->ID, 'override_buddypress_template', true ) == 'true' && $bp->current_action == 'home' ) || $bp->use_default_page) {
      return $view;
    }

    if( $right_sidebar = groups_get_groupmeta( $bp->groups->current_group->id, 'right_sidebar' ) ) {

      if ($key = array_search( 'no-sidebars' , $view[ 'body_classes' ])) {
        unset($view[ 'body_classes' ][$key]);
      }

      if ($key = array_search( 'no-sidebar-right' , $view[ 'body_classes' ])) {
        unset($view[ 'body_classes' ][$key]);
      }

      $view[ 'body_classes' ][] = 'have-sidebar';
      $view[ 'body_classes' ][] = 'sidebar-right';

      $view['widget_areas']['right_sidebar'][] = $right_sidebar;

      return $view;

    }

    //** Remove sidebars if they are there */
    unset( $view[ 'widget_areas' ] );

    /**
     * I guess we don't need to fully override body_classes, because it can contain some necessary classes.
     * So just remove sidebar classes and set them again.
     * peshkov@UD
     */
    $view[ 'body_classes' ] = (array) $view[ 'body_classes' ];

    foreach( $view[ 'body_classes' ] as $key => $class ) {
      if( strpos($class, 'sidebar') !== false ) {
        unset($view[ 'body_classes' ][ $key ]);
      }
    }

    //** Unset to go full width */
    $view[ 'body_classes' ][] = 'no-sidebar-left';
    if( !is_active_sidebar( 'buddypress_sidebar' ) ) {
      $view[ 'body_classes' ][] = 'no-sidebars';

    } else {
      $view[ 'body_classes' ][] = 'have-sidebar';
      $view[ 'body_classes' ][] = 'sidebar-right';
    }

    return $view;

  }


  /**
   * Change the "My URL" URLs to the BuddyPress Profile
   *
   * @author potanin@UD
   */
  function my_account_url() {
    global $bp;

    return $bp->loggedin_user->domain;

  }


  /**
   * Add HTML to tag lists
   *
   * @author potanin@UD
   */
  function bp_forum_topic_tag_list( $tags, $format ) {

    $html = array();

    foreach( (array) $tags as $tag ) {
      $html[] = '<span class="btn">' . $tag . '</span>';
    }

    return implode( '', (array) $html );

  }


  /**
   * Display BuddyPress notices in standard notice location
   *
   * @author potanin@UD
   */
  function primary_notice_container( $notices ) {

    ob_start();
    do_action( 'template_notices' );
    $content = ob_get_contents();
    ob_end_clean();

    if( empty( $content )) {
      return $notices;
    }

    $notices[] = '<div class="alert fade in"><a class="close" data-dismiss="alert" href="#">&times;</a>' . $content . '</div>';

    return $notices;

  }


  /**
   * Render pagination links and wrapper if they exist
   *
   *
   * @todo Test with multiple pages of groups, developed to solve the one-page view for all groups.
   * @author potanin@UD
   */
  function render_pagination_links( $text ) {

    if( empty( $text ) ) {
      return false;
    }

    $html[] = '<div class="pagination-links">';
    $html[] = $text;
    $html[] = '</div>';

    return implode( '', (array) $html );

  }


  /**
   * Fix the pagination display
   *
   *
   * @todo Test with multiple pages of groups, developed to solve the one-page view for all groups.
   * @author potanin@UD
   */
  function render_pagination_count( $text ) {
    global $groups_template;

    if( $groups_template ) {
      $component = 'group';
    }

    if( ! $component ) {
      return $text;
    }

    switch ($component) {

      case 'group':

        $start_num = intval( ( $groups_template->pag_page - 1 ) * $groups_template->pag_num ) + 1;
        $from_num = bp_core_number_format( $start_num );
        $to_num = bp_core_number_format( ( $start_num + ( $groups_template->pag_num - 1 ) > $groups_template->total_group_count ) ? $groups_template->total_group_count : $start_num + ( $groups_template->pag_num - 1 ) );
        $total = bp_core_number_format( $groups_template->total_group_count );

        $total_viewed = $to_num - $from_num;

        /* We are viewing all available groups, no point showing this */
        if( $total_viewed <= $total ) {
          return;
        }

      break;

    }

    $html[] = '<div class="page-count">';
    $html[] = $text;
    $html[] = '</div>';

    return implode( '', (array) $html );

  }


  /**
   * Adds BuddyPress to the Navbar selection.
   *
   * @author potanin@UD
   */
  function navbar_options( $navbar_options ) {
    global $wp_query;

    $navbar_options[ 'buddypress' ] = array(
      'type' => 'buddypress',
      'label' => __( 'BuddyPress "Admin Bar"', 'flawless' )
    );

    return $navbar_options;

  }


  /**
   * Notifies Flawless if we will have a Navbar.
   *
   * @filter init ( 500 )
   * @author potanin@UD
   */
  function use_navbar( $default ) {
    global $flawless, $bp, $topic_template;

    if ( defined( 'BP_DISABLE_ADMIN_BAR' ) && BP_DISABLE_ADMIN_BAR || $flawless[ 'buddypress' ][ 'disable_admin_bar' ] == 'true' || $flawless['navbar']['type'] != 'buddypress' ) {
      return false;
    }

    if ( (int) bp_get_option( 'hide-loggedout-adminbar' ) && !is_user_logged_in() ){
      return false;
    }

    //** Add custom "Manage" toolbar to BP Navbar
    add_action( 'bp_adminbar_menus' , array( 'Flawless_BuddyPress', 'navbar_admin_actions'), 200 );

    //** Add Topic Navbar Item */
    if( $topic_template->topic_id ) {
      add_action( 'bp_adminbar_menus' , array( 'Flawless_BuddyPress', 'navbar_topic_actions'), 250 );
    }

    //** Remove the "Visit" Toolbar Menu */
    remove_action( 'bp_adminbar_menus' , 'bp_adminbar_random_menu', 100 );

    //** Handle BuddyPress-specific navbar settings
    if( $flawless[ 'buddypress' ][ 'navbar' ][ 'hide_dashboard' ] == 'true' ) {
      remove_action( 'bp_adminbar_menus', 'bp_adminbar_thisblog_menu', 6);
    }

    add_action( 'flawless::navbar_html', array( 'Flawless_BuddyPress', 'navbar_html' ) );

    return $default;

  }


  /**
   * Converts, rather hackishly, the default BP "Admin Bar"
   *
   * @todo Modify this to utilize the header-navbar.php file and the header-navbar action
   * @author potanin@UD
   */
  function navbar_html( $html ) {
    global $bp, $flawless;

    if(flawless_theme::load('simple_html_dom')) {
      //** Get BP Admin bar menu */
      ob_start();
      $bp->doing_admin_bar = true;
      do_action( 'bp_adminbar_menus' );
      $bp->doing_admin_bar = false;
      $contents = ob_get_contents();
      ob_end_clean();

      preg_match("/^\<ul/", $contents, $matches);

      if(empty($matches)) {
        $contents = "<ul>{$contents}</ul>";
      }

      $contents = str_replace( array( '<li id="' ), '<li class="dropdown" id="', $contents);
      $contents = str_replace( array( '<ul>', '<ul class="random-list">'), '<ul class="dropdown-menu">', $contents);

      $dom = str_get_html($contents);
      $menu = $dom->find('ul', 0)->children();

      if(empty($menu)) {
        return $html;
      }

      //** Set of not allowed menu items */
      $not_allowed = array(
        "log_in" => __("Log In"),
      );

      //** Go through all main menu items and parse them */
      foreach($menu as $li) {
        $class = "";
        $link = $li->find('a', 0);
        //** Don't add not allowed menu items */
        if(in_array($link->innertext, $not_allowed)) continue;
        else $link = $link->outertext;

        $children = $li->find('ul', 0)->outertext;
        if(!empty($children)) {
          $class = "dropdown";
          if(strpos($link, 'class=') === false) {
            $link = str_replace("<a ", "<a class=\"dropdown-toggle\" ", $link);
          } else {
            $link = str_replace("class=\"", "class=\"dropdown-toggle ", $link);
          }
          $link = str_replace("<a ", "<a data-toggle=\"dropdown\" ", $link);
          if(strpos($link, 'caret') === false) {
            $link = str_replace("</a>", "<b class=\"dropdown-toggle caret\"></b></a>", $link);
          }
        }
        $items[] = "<li class=\"{$class}\">{$link}{$children}</li>";
      }
    }

    $html[ 'left' ] = implode( '', (array) $items );

    return $html;
  }



  /**
   * Display list of groups via shortcode call.
   *
   * @author potanin@UD
   */
  function group_avatar( $atts  = false ) {
    return bp_get_group_avatar();
  }


  /**
   * Display current group description via shortcode call.
   *
   * @author potanin@UD
   */
  function group_description( $atts  = false ) {
    return bp_get_group_description();
  }


  /**
   * Render the registration form by extracting the code from register.php
   *
   * @author potanin@UD
   */
  function bp_registration_form( $atts  = false, $content = false ) {
    global $shortcode_content;

    //** If the shortcode is being requested on admin-side it's most likely because Carrington Build is previewing it, in which case we return a short description */
    if( is_admin() && current_theme_supports( 'carrington_build' ) ) {
      return __( 'BuddyPress Registration', 'flawless' );
    }

    //** Add filter to skip the footer when including the template */
    add_filter( 'skip_footer', array( 'flawless_theme', 'return_true' ), 20);

    //** Use OB to only capture the code between before and after page
    add_action( 'bp_before_register_page', create_function( '', ' ob_start();  ' ) );
    add_action( 'bp_after_register_page', create_function( '', ' global $shortcode_content; $shortcode_content = ob_get_contents(); ob_end_clean();  ' ) );

    //** Add any custom login here, such as modifying the global $bp variable to make the code in register.php work */

    locate_template( array( 'registration/register.php' ), true );

    //** Remove the footer skipping so the actual footer doesn't get skipped
    remove_filter( 'skip_footer', array( 'flawless_theme', 'return_true' ), 20);

    return $shortcode_content;

  }


  /**
   * Display current group description via shortcode call.
   *
   * @author potanin@UD
   */
  function group_meta( $atts  = false, $content = false ) {
    global $groups_template;

    $atts = wp_parse_args( $atts, array(
      'meta' => false,
      'do_not_format' => false
    ) );

    if( empty( $atts[ 'meta' ] ) ) {
      return;
    }

    $value = groups_get_groupmeta( $groups_template->groups[ $groups_template->current_group ]->id, $atts[ 'meta' ] );

    if( !$args[ 'do_not_format' ] ) {
      $value = stripslashes( $value );
    }

    return $value;

  }


  /**
   * Display list of groups via shortcode call.
   *
   * @author potanin@UD
   */
  function buddy_press_groups( $atts  = false ) {
    global $groups_template;

   	$atts = shortcode_atts( array(
      'user_id' => 0,
      'max_groups' => 5,
      'item_class' => 'group_item',
      'descriptions' => 'true',
      'avatars' => 'true',
      'titles' => 'true',
      'group_type' => 'active'
    ), $atts );

     if ( !bp_has_groups( 'user_id=' . $atts[ 'user_id' ] . '&type=' . $atts[ 'group_type' ] . '&max=' . $atts[ 'max_groups' ] ) ) {
      return;
    }

    $html = array();

    $html[] = '<div class="groups dir-list shortcode">';
    $html[] = '<ul class="groups-list item-list">';

    while ( bp_groups() ) : bp_the_group();

    $html[] = '<li class="list-item  ' . $atts[ 'item_class' ] . ' clearfix">';
    $html[] = '<div class="clearfix">';

    if( $atts[ 'avatars' ] == 'true' ) {
      $html[] = '<div class="item-avatar"><a href="' . bp_get_group_permalink() . '" title="' . bp_get_group_name() . '">' . bp_get_group_avatar() . '</a></div>';
    }

    if( $atts[ 'titles' ] == 'true' ) {
      $html[] = '<div class="item-title"><a href="' . bp_get_group_permalink() . '" title="' . bp_get_group_name() . '">' . bp_get_group_name() . '</a></div>';
    }

    if( $atts[ 'descriptions' ] == 'true' ) {
      $html[] = '<div class="item-desc">' . bp_get_group_description() . '</div>';
    }

    $html[] = '</div>';
    $html[] = '</li>';

    endwhile;

    $html[] = '</ul>';
    $html[] = '</div>';

    return implode( '', (array) $html );

  }


   /**
   * Displays inline navigation for current BP place.
   *
   *
   * @todo For Group Admin, the groups_admin_tabs action is not honored here.
   * @author potanin@UD
   */
  function render_navigation() {
    global $bp;

    if( groups_get_groupmeta( $bp->groups->current_group->id, 'hide_navigation' ) == 'true' ) {
      return;
    }

    $current_user = wp_get_current_user();

    $component_index = !empty( $bp->displayed_user ) ? $bp->current_component : bp_get_root_slug( $bp->current_component );

    //** If My Profile we override the compontent */
    if( bp_is_my_profile() ) {
      $component_index = 'member';
    }

    if( !$component_index ) {
      return;
    }

    $menu = array();
    $menu_class = array( 'nav', 'nav-tabs' );

    switch ( $component_index ) {

      case 'member' :

        $menu_class[] = 'bp_nav';

        foreach ( (array) $bp->bp_nav as $user_nav_item ) {

          if ( !$user_nav_item[ 'show_for_displayed_user' ] && !bp_is_my_profile() ) {
            continue;
          }

          $menu[ $user_nav_item[ 'slug' ] ] = $user_nav_item;

          $menu[ $user_nav_item[ 'slug' ] ][ 'classes' ] = array( 'bp-menu-item' );

          if ( $bp->current_component == $user_nav_item[ 'slug' ] ) {
            $menu[ $user_nav_item[ 'slug' ] ][ 'selected' ] = true;
            $menu[ $user_nav_item[ 'slug' ] ][ 'classes' ][] = 'active';
            $selected = ' class="current selected"';
          } else {
            $selected = '';
          }

          if ( $bp->loggedin_user->domain ) {
            $link = str_replace( $bp->loggedin_user->domain, $bp->displayed_user->domain, $user_nav_item[ 'link' ] );
          } else {
            $link = $bp->displayed_user->domain . $user_nav_item[ 'link' ];
          }

          $menu[ $user_nav_item[ 'slug' ] ][ 'link' ] = $link;
          $menu[ $user_nav_item[ 'slug' ] ][ 'id' ] = $user_nav_item[ 'css_id' ] . '-personal-li';
          $menu[ $user_nav_item[ 'slug' ] ][ 'css' ] = 'user-' . $user_nav_item[ 'css_id' ];
          $menu[ $user_nav_item[ 'slug' ] ][ 'html' ] = apply_filters_ref_array( 'bp_get_displayed_user_nav_' . $user_nav_item[ 'css_id' ], array( '<li id="' . $user_nav_item[ 'css_id' ] . '-personal-li" ' . $selected . '><a id="user-' . $user_nav_item[ 'css_id' ] . '" href="' . $link . '">' . $user_nav_item[ 'name' ] . '</a></li>', &$user_nav_item ) );

          if( $bp->bp_options_nav[ $user_nav_item[ 'slug' ] ] ) {
            $menu[ $user_nav_item[ 'slug' ] ][ 'submenu' ] = $bp->bp_options_nav[ $user_nav_item[ 'slug' ] ];
          }

        }

      break;

      default:

        $menu_class[] = 'bp_nav';

        if ( !bp_is_single_item() ) {
          if ( !isset( $bp->bp_options_nav[ $component_index ] ) || count( $bp->bp_options_nav[ $component_index ] ) < 1 ) {
            return false;
          } else {
            $the_index = $component_index;
          }
        } else {
          if ( !isset( $bp->bp_options_nav[ $bp->current_item ] ) || count( $bp->bp_options_nav[ $bp->current_item ] ) < 1 ) {
            return false;
          } else {
            $the_index = $bp->current_item;
          }
        }

        //** Loop through each navigation item */
        foreach ( (array) $bp->bp_options_nav[ $the_index ] as $subnav_item ) {

          if ( !$subnav_item[ 'user_has_access' ] ) {
            continue;
          }

          $menu[ $subnav_item[ 'slug' ] ] = $subnav_item;
          $menu[ $subnav_item[ 'slug' ] ][ 'classes' ] = array( 'bp-menu-item' );

          //** If the current action or an action variable matches the nav item id, then add a highlight CSS class. */
          if ( $subnav_item[ 'slug' ] == $bp->current_action ) {
            $selected = ' class="current selected"';
            $menu[ $subnav_item[ 'slug' ] ][ 'selected' ] = true;
            $menu[ $subnav_item[ 'slug' ] ][ 'classes' ][] = 'active';
          } else {
            $selected = '';
          }

          //** List type depends on our current component */
          $list_type = bp_is_group() ? 'groups' : 'personal';

          $menu[ $subnav_item[ 'slug' ] ][ 'css' ] = $subnav_item;
          $menu[ $subnav_item[ 'slug' ] ][ 'id' ] =  $subnav_item[ 'css_id' ] . '-' . $list_type . '-li';
          $menu[ $subnav_item[ 'slug' ] ][ 'html' ] = apply_filters( 'bp_get_options_nav_' . $subnav_item[ 'css_id' ], '<li id="' . $subnav_item[ 'css_id' ] . '-' . $list_type . '-li" ' . $selected . '><a id="' . $subnav_item[ 'css_id' ] . '" href="' . $subnav_item[ 'link' ] . '">' . $subnav_item[ 'name' ] . '</a></li>', $subnav_item );

          //** If this is the Groups Admin menu, we add submenus */
          if( $component_index == 'groups' && $subnav_item[ 'slug' ]  == 'admin' ) {

            $group = ( $groups_template->group ) ? $groups_template->group : $bp->groups->current_group;

            $current_tab = bp_action_variable( 0 );

            //** Add Details Submenu */
            if ( $bp->is_item_admin || $bp->is_item_mod ) {
              $menu[ $subnav_item[ 'slug' ] ][ 'submenu' ][] = array(
                'name' => __( 'Details', 'buddypress' ),
                'link' => bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug . '/admin/edit-details'
              );
            }

            if ( !$bp->is_item_admin ) {
              continue;
            }

            $menu[ $subnav_item[ 'slug' ] ][ 'submenu' ][] = array(
              'name' => __( 'Settings', 'buddypress' ),
              'link' => bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug . '/admin/group-settings'
            );

            if ( !(int) bp_get_option( 'bp-disable-avatar-uploads' ) ) {
              $menu[ $subnav_item[ 'slug' ] ][ 'submenu' ][] = array(
                'name' => __( 'Avatar', 'buddypress' ),
                'link' => bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug . '/admin/group-avatar'
              );
            }

            $menu[ $subnav_item[ 'slug' ] ][ 'submenu' ][] = array(
              'name' => __( 'Members', 'buddypress' ),
              'link' => bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug . '/admin/manage-members'
            );

            if ( $groups_template->group->status == 'private' ) {
              $menu[ $subnav_item[ 'slug' ] ][ 'submenu' ][] = array(
                'name' => __( 'Requests', 'buddypress' ),
                'link' => bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug . '/admin/membership-requests'
              );
            }

            $menu[ $subnav_item[ 'slug' ] ][ 'submenu' ][] = array(
              'name' => __( 'Delete', 'buddypress' ),
              'link' => bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug . '/admin/delete-group'
            );

          }

        }

      break;

    }

    $html = array();

    $html[] = '<ul class="' . implode( ' ', (array)  $menu_class )  . '">';

    foreach( (array) $menu as $slug => $settings ) {

      if( $settings[ 'submenu' ] ) {
        $settings[ 'classes' ][] = 'dropdown';
      }

      $html[] = '<li class="' . implode( ' ', (array) $settings[ 'classes' ] ) . '">';

      if( $settings[ 'submenu' ] ) {
        $html[] = '<a href="' . $settings[ 'link' ] . '" class="dropdown-toggle" data-toggle="dropdown">';
      } else {
        $html[] = '<a href="' . $settings[ 'link' ] . '" class="">';
      }

      $html[] = $settings[ 'name' ];

      if( $settings[ 'submenu' ] ) {
        $html[] = '<b class="caret"></b>';
      }

      $html[] = '</a>';


      if( $settings[ 'submenu' ] ) {
        $html[] = '<ul class="dropdown-menu">';

        foreach( (array) $settings[ 'submenu' ] as $ss ) {
          $html[] = '<li><a href="' . $ss[ 'link' ] . '">' . $ss[ 'name' ] . '</a></li>';
        }

        $html[] = '</ul>';
      }

      $html[] = '</li>';

    }

    //** Support for default BuddyPress API hook */
    ob_start();
      do_action( 'bp_' . $component_index . '_options_nav' );
      $html[] = ob_get_contents();
    ob_end_clean();

    $html[] = '</ul>';

    echo implode( '', (array) $html );

  }


   /**
   * Force Flawless template_redirect to be loaded in BuddyPress
   *
   * @todo This code is very UD-specific, needs to be migrated out once proof of concept is done.
   * @todo Add sort by usage to $current_filters
   *
   * @author potanin@UD
   */
  function get_json_topics( $args = false ) {
    global $forum_template, $topic_template, $wpdb;

    $_forum_template = $forum_template;


    /* Declare our supported forum IDs */
    $supported_forums = array( 1, 5, 6, 8 ); /* WPP, WPI, Denali, CRM */

    /* Overwrite $args with $_REQUEST if it exists */
    if( !$args && $_REQUEST ) {
      $args = $_REQUEST;
    }

    $args = wp_parse_args( $args,  array(
      'json_result' => true,
      'use_cache' => true,
      'request_range' => array(
        'start' => 0,
        'end' => 10
      ),
      'minimum_filter_usage' => 0,
      'filterable_attributes' => array(),
      'query' =>  array(
        'per_page' => -1
      )
    ));

    if( !empty( $args[ 'filter_query' ][ 'search_terms' ] ) ) {
      $args[ 'query' ][ 'search_terms' ] = $args[ 'filter_query' ][ 'search_terms' ];
    }

    if( !empty( $args[ 'filter_query' ][ 'topic_type' ] ) ) {
      $topic_type = $args[ 'filter_query' ][ 'topic_type' ][0];
    }

    if( !empty( $args[ 'filter_query' ][ 'object_name' ] ) ) {
      $forum_id = $wpdb->get_var( "SELECT forum_id FROM {$wpdb->prefix}bb_forums WHERE forum_name = '{$args[ 'filter_query' ][ 'object_name' ][0]}' ");
      $args[ 'query' ][ 'forum_id' ] = $forum_id;
    }

    //** Always create transiet key because results are always cached */
    $args[ 'transient_key' ] = 'bp_t_' . md5( serialize( $args[ 'query' ] ) );
    $args[ 'cache_timeout' ] = is_numeric( $args[ 'use_cache' ] ) ? $args[ 'use_cache' ] : 600;

    /** For now, we can't use cache - everything is determined by SQL */
    $results = false;
    $args[ 'use_cache' ] = false;

    /* Disabled - this will break JSON response - potanin@UD */
    /* if(isset($args['debug']) && $args['debug']) $wpdb->show_errors(); */

    /* If we don't have results. */
    if( empty( $results[ 'all_results' ] ) ) {

      /* Start to build our query */
      $query = "SELECT t.*, f.forum_slug, f.forum_name, MIN( CASE WHEN m.meta_key =  'ud_support_created' THEN m.meta_value END ) AS premium FROM {$wpdb->prefix}bb_topics AS t INNER JOIN {$wpdb->prefix}bb_posts AS p ON t.topic_id = p.topic_id LEFT JOIN {$wpdb->prefix}bb_forums AS f ON t.forum_id = f.forum_id JOIN {$wpdb->prefix}bb_meta AS m ON t.topic_id = m.object_id WHERE m.object_type = 'bb_topic' AND t.topic_status = 0";

      /* Add on our where clauses */
      $filter =& $args['filter_query'];

      /* Supported forums */
      $query .= " AND ( f.forum_id IN (".implode(',', $supported_forums).") )";

      /* Search box */
      if(isset($filter['search_terms']) && !empty($filter['search_terms'])){
        /* Use Boolean mode search */
        $query .= " AND ( MATCH (t.topic_title, p.post_text) AGAINST ('".$wpdb->escape($filter['search_terms'])."' IN BOOLEAN MODE) )";
      }

      /* Voices / Posts Count */
      if(isset($filter['topic_posts']) && !empty($filter['topic_posts'])){
        /* Use Boolean mode search */
        $query .= " AND ( topic_posts BETWEEN {$filter[topic_posts][min]} AND {$filter[topic_posts][max]} )";
      }

      /* Forum */
      if(isset($filter['object_name']) && is_array($filter['object_name']) && !empty($filter['object_name'])){
        /* Setup our forum map from key to ID */
        $t = $wpdb->get_results("SELECT forum_id, forum_name FROM {$wpdb->prefix}bb_forums", ARRAY_A);
        $forums_map = array();

        foreach($t as $arr){ $forums_map[$arr['forum_name']] = $arr['forum_id']; }
        $ids = array();
        foreach($filter['object_name'] as $name){
          if(in_array($name, array_keys($forums_map))){
            $ids[] = $forums_map[$name];
          }
        }
        if(count($ids)){
          $query .= " AND ( p.forum_id IN ( ".implode(",", $ids)." ) OR t.forum_id IN ( ".implode(",", $ids)." ) )";
        }
      }

      /* Add our group by */
      $query .= " GROUP BY t.topic_id ";

      /* We add the having statement after the group by always */
      /* Premium vs Regular */
      if(isset($filter['topic_type']) && is_array($filter['topic_type']) && !empty($filter['topic_type'])){
        /* If we have a search term */
        if(count($filter['topic_type']) == 1){
          $null = "IS NOT NULL";
          if($filter['topic_type'][0] == "Regular") $null = "IS NULL";
          $query .= " HAVING ( MIN( CASE WHEN m.meta_key =  'ud_support_created' THEN m.meta_value END ) {$null} )";
        }
      }

      /* @todo Add sort by when Andy is ready - williams@UD */
      $query .= " ORDER BY p.post_time DESC";

      /** Create our temporary table */
      $wpdb->query("CREATE TEMPORARY TABLE {$args['transient_key']} {$query}");

      /** Reset our query */
      $query = "SELECT * FROM {$args['transient_key']}";

      /** Run the query once to get all the ids */
      $all_ids = $wpdb->get_col($query, 0);

      /** Add in our limiters, then get the specific IDs */
      $query .= " LIMIT {$args['request_range']['start']}, {$args['dom_limit']}";

      /** Get the specific ids */
      $data = $wpdb->get_results($query, ARRAY_A);

      /** Load buddypress */
      bp_forums_load_bbpress();

      /** Now that we have our data, lets go through and add our additional stuff */
      foreach($data as &$row){
        $row['object_name'] = $row['forum_name'];
        $row['topic_link'] = home_url("products/{$row['forum_slug']}/forum/topic/{$row['topic_slug']}/");
        $row['topic_type'] = (is_numeric($row['premium']) ? 'Premium' : 'Regular' );
        $row['topic_tags'] = ($row['tag_count'] ? bb_get_topic_tags( $row['topic_id'], array( 'fields' => 'names', 'topic_id' =>$row['topic_id'] ) ) : array());
        $row['freshness'] = bp_core_time_since( strtotime( $row['topic_time'] ) );
        $row['group_link'] = site_url("products/{$row['forum_slug']}/");
        $row['group_avatar'] = bp_core_fetch_avatar( array( 'item_id' => $row['forum_id'], 'object' => 'group', 'type' => 'thumb',  'width' => false, 'height' => false, 'html' => false ) );
        $row['last_poster_avatar'] = bp_core_fetch_avatar( array( 'type' => 'thumb',  'width' => false, 'height' => false,  'item_id' => $row['topic_last_poster'], 'html' => false) );
      }

      /** Now, for each filterable item, lets figure out the counts */
      $current_filters = array();

      foreach( (array) $args['filterable_attributes'] as $filter_key => $filter_data ) {
        switch ( $filter_data[ 'filter_type' ] ) {

          case 'checkbox':

            switch ( $filter_key ){
              case 'object_name':
                $filter_query = "SELECT forum_name AS `label`, count(*) AS `usage_count` FROM {$args['transient_key']} GROUP BY forum_name HAVING count(*) > 0 ORDER BY usage_count DESC LIMIT 10";
                break;
              case 'topic_type':
                $filter_query = "SELECT 'Regular' AS `label`, count(*) as `usage_count` FROM {$args['transient_key']} WHERE premium IS NULL";
                break;
              default:
                $filter_query = false;
                break;
            }

            /* Run the filter query */
            if($filter_query){
              $current_filters[ $filter_key ] = $wpdb->get_results($filter_query, ARRAY_A);
            }

            /* Do our special consideration for topic_type */
            if($filter_key == 'topic_type'){
              $t =& $current_filters[ $filter_key ];
              $t[] = array(
                'label' => 'Premium',
                'usage_count' => count($all_ids) - $t[0]['usage_count'],
              );
              if($t[0]['usage_count'] < $t[1]['usage_count']){
                $t = array(
                  array(
                    'label' => $t[1]['label'],
                    'usage_count' => $t[1]['usage_count'],
                  ),
                  array(
                    'label' => $t[0]['label'],
                    'usage_count' => $t[0]['usage_count'],
                  )
                );
              }
            }
            break;


          case 'date_range':
          case 'range':

            $current_filters[$filter_key][ 'values' ][ 'max' ] = $wpdb->get_var( "SELECT MAX(topic_posts) FROM {$args['transient_key']}" );
            $current_filters[$filter_key][ 'values' ][ 'min' ] = $wpdb->get_var( "SELECT MIN(topic_posts) FROM {$args['transient_key']}" );

          break;

          case 'input':

            $current_filters[ $filter_key ] = array(array(
              'values' => (isset($args['filter_query'][$filter_key]) && !empty($args['filter_query'][$filter_key]) ? $args['filter_query'][$filter_key] : ''),
            ));

            break;

        }
      }


      /* These results are cached in transient */
      $results[ 'all_results' ] = apply_filters( 'all_results',  $data );
      $results[ 'total_results' ] = count($all_ids);
      $results[ 'current_filters' ] = $current_filters;

      /* Cache topic results, if set. */
      set_transient( $args[ 'transient_key' ] , $results, $args[ 'cache_timeout' ] );

      $results[ 'cached_used' ] = false;

    } else {
      $results[ 'cached_used' ] = true;

    }

    /* Sliced result are not cached */
    //$results[ 'all_results' ] = array_slice( (array) $results[ 'all_results' ], $args[ 'request_range' ][ 'start' ], $args[ 'dom_limit'] );

    /* If in debug mode - these are not cached */
    if( $args[ 'debug' ] ) {
      $results[ 'args' ] = $args;
      $results[ 'sql_queries' ] = $wpdb->num_queries;
      $results[ 'transient_key' ] = $args[ 'transient_key' ];
    }

    $results[ 'server_driven' ] = true;
    $results[ 'timer' ] = timer_stop();
    $results[ 'cache_timeout' ] = $args[ 'cache_timeout' ];

    if( $args[ 'json_result' ] ) {

      /* Set the return type */
      header( 'Content-Type: application/json');

      /* Do the JSON encode */
      $results = json_encode( $results );

      /* If we're a human */
      if(isset($args['human'])) $results = flawless_human_json($results);

    }

    //** Restore any global variables */
    $forum_template = $_forum_template;

    /* If we're here, we return normal json */
    return $results;

  }


  /**
   * Intercept BuddyPress temploate loader and disable it if the current page has a meta setting.
   *
   * Unsetting $bp->is_single_item prevents BP from loading on Single Group Pages
   *
   * @filter bp_is_current_component
   * @author potanin@UD
   */
  function bp_is_current_component( $is_current_component, $component ) {
    global $wp_filter, $wp_query, $post, $bp;

    //** Get first result from query, because WP will attempt to find a match and will cycle through multiple $post objects */
    $post_id = $wp_query->posts[ 0 ]->ID;

    //** If $is_current_component is already false, or there is no post object loaded, we return default */
    if( !$is_current_component || !$post_id) {
      return $is_current_component;
    }

    $primary_pages = array();

    //** Build array of post IDs of "Primary" BuddyPress pages */
    foreach( $bp->pages as $slug => $settings ) {
      $primary_pages[] = $settings->id;
    }

    //** If current request is not a primary page, but a corresponding WP page exists, we load the WP page */
    if( $wp_query->post_count == 1 && !$wp_query->query_vars[ '404' ] && !in_array( $post_id, $primary_pages )) {
      $bp->is_single_item = false;
      $bp->use_default_page = true;
      return false;
    }

    //** If this is not explicitly set to by 'true', we return default */
    if($wp_query->posts[ 0 ]->post_name=='register' || get_post_meta( $post_id, 'override_buddypress_template', true ) != 'true') {
    /* we still want use buddypress workflow of signup but at the end we break theirs template output. So we load our register page */
      return $is_current_component;
    }

    //** At this point this page must be set to not use the BP template */
    $bp->use_default_page = true;
    return false;

  }


  /**
   * Add Layout Editor button to BuddyPress toolbar
   *
   * @author potanin@UD
   */
  function navbar_admin_actions() {
    global $post, $bp, $flawless;

    if ( !is_user_logged_in() ) {
      return;
    }

    $links = array();

    if ( current_user_can( 'manage_options' ) && !is_admin() ) {
      $links[] = '<li class="no-arrow"><a href="' . admin_url() . '">' .  __( 'Dashboard' , 'flawless' ) . '</a></li>';
    }

    if ( is_user_logged_in() && bp_user_can_create_groups() ) {
      $links[] = '<li id="bp-adminbar-groupoptions-menu"><a href="' . trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/create' ) . '">' . __( 'Create a Group', 'buddypress' ) . '</a></li>';
    }

    if ( $post && $url = get_edit_post_link( $post->ID ) ) {
      $post_type_obj = get_post_type_object( $post->post_type );
      $links[] = '<li><a class="post-edit-link" href="' . $url . '" title="' . esc_attr( $post_type_obj->labels->edit_item ) . '">' . $post_type_obj->labels->edit_item . '</a></li>';
    }

    if ( current_user_can( 'manage_options' ) && !is_admin() ) {
      $links[] = '<li id="flawless_buddypress_edit_layout" class="no-arrow"><a href="#"><span class="flawless_edit_layout">' .  __( 'Edit Layout' , 'flawless' ) . '</span></a></li>';
      $links[] = '<li class="no-arrow"><a href="' . admin_url( 'themes.php?page=functions.php') . '">' .  __( 'Theme Settings' , 'flawless' ) . '</a></li>';
      $links[] = '<li class="no-arrow"><a href="' . admin_url( 'widgets.php') . '">' .  __( 'Widgets' , 'flawless' ) . '</a></li>';
    }

    if ( current_user_can( 'list_users' ) && !is_admin() ) {
      $links[] = '<li class="no-arrow"><a href="' . admin_url( 'users.php' ) . '">' .  __( 'View Users' , 'flawless' ) . '</a></li>';
    }

    $links = apply_filters( 'flawless::bp_navbar_admin_links', $links );

    if( empty( $links ) ) {
      return;
    }

  ?>
  <li class="dropdown">
    <a href="<?php echo admin_url(); ?>" class="dropdown-toggle" data-toggle="dropdown"><?php _e( 'Manage', 'flawless' ); ?><b class="caret"></b></a>
    <ul class="dropdown-menu"><?php echo implode( '', (array) $links ); ?></ul>
  </li>
  <?php

  }


  /**
   * Add Layout Editor button to BuddyPress toolbar
   *
   * @author potanin@UD
   */
  function navbar_topic_actions() {
    global $post, $bp, $flawless, $forum_template;

    if ( !is_user_logged_in() ) {
      return;
    }

    $links = array();

    if ( bp_group_is_admin() || bp_group_is_mod() || bp_get_the_topic_is_mine() ) {

      $links[] = '<li class="no-arrow"><a href="' . wp_nonce_url( bp_get_the_topic_permalink() . 'edit', 'bp_forums_edit_topic' ) . '">' . __( 'Edit Topic', 'buddypress' ) . '</a></li>';

      if ( $bp->is_item_admin || $bp->is_item_mod || is_super_admin() ) {
        if ( 0 == (int)$forum_template->topic->topic_sticky ) {
          $links[] = '<li class="no-arrow"><a href="' . wp_nonce_url( bp_get_the_topic_permalink() . 'stick', 'bp_forums_stick_topic' ) . '">' . __( 'Sticky Topic', 'buddypress' ) . '</a></li>';
        } else {
          $links[] = '<li class="no-arrow"><a href="' . wp_nonce_url( bp_get_the_topic_permalink() . 'unstick', 'bp_forums_unstick_topic' ) . '">' . __( 'Un-stick Topic', 'buddypress' ) . '</a></li>';
        }

        if ( 0 == (int)$forum_template->topic->topic_open ) {
          $links[] = '<li class="no-arrow"><a href="' . wp_nonce_url( bp_get_the_topic_permalink() . 'open', 'bp_forums_open_topic' ) . '">' . __( 'Open Topic', 'buddypress' ) . '</a></li>';
        } else {
          $links[] = '<li class="no-arrow"><a href="' . wp_nonce_url( bp_get_the_topic_permalink() . 'close', 'bp_forums_close_topic' ) . '">' . __( 'Close Topic', 'buddypress' ) . '</a></li>';
        }

        $links[] = '<li class="no-arrow"><a class="confirm" id="topic-delete-link" href="' . wp_nonce_url( bp_get_the_topic_permalink() . 'delete', 'bp_forums_delete_topic' ) . '">' . __( 'Delete Topic', 'buddypress' ) . '</a></li>';

      }

    }

    $links = apply_filters( 'flawless::bp_navbar::topic_links', $links );

    if( empty( $links ) ) {
      return;
    }

  ?>
  <li class="dropdown">
    <a href="<?php echo admin_url(); ?>" class="dropdown-toggle" data-toggle="dropdown"><?php _e( 'Topic', 'flawless' ); ?><b class="caret"></b></a>
    <ul class="dropdown-menu"><?php echo implode( '', (array) $links ); ?></ul>
  </li>
  <?php

  }

  /**
   * Add BuddyPress forum topics to Google XML Sitemaps
   *
   * @author potanin@UD
   */
  function sm_buildmap() {

    $generatorObject = &GoogleSitemapGenerator::GetInstance();

    if( !$generatorObject ) {
      return;
    }

    //** Load topics into $forum_template */
    bp_has_forum_topics( array( 'per_page' => -1 ) );

    while ( bp_forum_topics() ) {
      bp_the_forum_topic();
      $generatorObject->AddUrl( bp_get_the_topic_permalink()  ,time(), 'daily', 0.5);
    }

  }


  /**
   * Adds BuddyPress class to body tag
   *
   * @author potanin@UD
   */
  function body_class( $classes ) {
    global $flawless;

    return $classes;

  }


  /**
   * Force Flawless template_redirect to be loaded in BuddyPress
   *
   * @author potanin@UD
   */
  function bp_load_template($located_template) {
    global $wp_query;

    flawless_theme::console_log(sprintf(__( 'Executing: %1s.', 'wp_crm'), 'Flawless_BuddyPress::bp_load_template()'));

    flawless_theme::template_redirect();

    return $located_template;
  }


  /**
   * Force Flawless template_redirect to be loaded in BuddyPress
   *
   * @author potanin@UD
   */
  function widgets_init($sidebars) {

    register_sidebar( array(
      'name'          => 'BuddyPress Sidebar',
      'id'            => 'buddypress_sidebar',
      'description'   => __( 'The sidebar widget area displayed on BuddyPress pages.', 'flawless' ),
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget'  => '</div>',
      'before_title'  => '<h3 class="widgettitle">',
      'after_title'   => '</h3>'
    ) );

  }


  /**
   * Determine if BP Template plugin is used
   * and disable all CSS and JS/AJAX functionality of it.
   *
   * Also adds admin notice about it.
   *
   * @author Maxim Peshkov
   */
  function check_for_bp_template_pack() {
    if(function_exists( 'bp_tpack_loader')) {
      /** Disable BP Template Pack JS / AJAX */
      if (!get_option( 'bp_tpack_disable_js')) {
        add_option( 'bp_tpack_disable_js', 1);
      }
      /** Disable BP Template Pack CSS */
      if (!get_option( 'bp_tpack_disable_css')) {
        add_option( 'bp_tpack_disable_css', 1);
      }
      add_action( 'admin_notices', array( 'Flawless_BuddyPress', 'bp_template_pack_admin_notices'));
    }
  }

  /**
   * Renders admin notice about incompatibility of BP Template plugin
   * with the Flawless theme
   *
   * @author Maxim Peshkov
   */
  function bp_template_pack_admin_notices() {
    ?>
    <div class="error">
      <p><?php _e( "You're using <b>BP Template Pack</b> which can cause CSS and JS conflicts with <b>Flawless</b> theme. Actually, Flawless theme supports BuddyPress functionality and has own set of CSS Styles and JS. You should deactivate or remove your <b>BP Template Pack</b> <a href=\"". admin_url( 'plugins.php') ."\">here</a>.", 'flawless' ); ?></p>
    </div>
    <?php
    return;
  }

  /**
   * Handles rendering of xprofile group tabs:
   * adds some bootstrap styles.
   *
   * @param array $tabs.
   * @param $groups.
   * @param string $group_name. Current active group.
   * @author peshkov@UD
   */
  function profile_group_tabs($tabs, $groups, $group_name) {
    foreach ($tabs as &$tab) {
      if(strpos($tab, $group_name) !== false ) {
        $tab = str_replace('current', 'active', $tab);
      }
    }
    return $tabs;
  }

}
