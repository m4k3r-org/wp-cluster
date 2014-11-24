<?php
/**
 * Name: Login Module
 * Description: Can be inserted via widget, shortcode or PHP function.
 * Author: Usability Dynamics, Inc.
 * Version: 1.3.0
 *
 * API Filters:
 * flawless::dashboard_url
 * flawless::my_account_url
 * flawless::logged_in_links
 * flawless::login_name - Allow user lookups by meta other than email and login
 * flawless::login_redirect - URL to redirect user to after login. The global user object is set.
 * flawless::my_account_module::render_module_args
 *
 */

add_action( 'flawless_theme_setup', array( 'flawless_my_account_module', 'init' ) );

/**
 * Add custom shortcodes for various post types
 *
 * @since 1.0
 *
 */
class flawless_my_account_module {


  /**
   * {missing description}
   *
   * @author potanin@UD
   */
  static function init() {
    global $flawless;

    flawless_theme::console_log( 'P: Executed: flawless_my_account_module::init();' );

    add_action( 'wp_ajax_nopriv_flawless_ajax_reset_password', create_function( '', 'die( flawless_my_account_module::ajax_reset_password() );' ) );
    add_action( 'wp_ajax_nopriv_flawless_ajax_login', create_function( '', 'die( flawless_my_account_module::ajax_login() );' ) );
    add_action( 'wp_ajax_flawless_ajax_logout', create_function( '', 'die( flawless_my_account_module::ajax_logout() );' ) );

    /** Handle login and reset when JS fails */
    add_action( 'template_redirect', array( 'flawless_my_account_module', 'template_redirect' ) );

    /** Add navbar menu items */
    add_action( 'flawless::navbar_html', array( 'flawless_my_account_module', 'navbar_html' ), 100 );

    /** Theme Settings */
    add_action( 'flawless::options_ui_general::common_settings', array( 'flawless_my_account_module', 'options_ui_general_common_settings' ), 100 );

    if( isset($flawless[ 'login_module' ][ 'disbale_wp_login_access' ]) && $flawless[ 'login_module' ][ 'disbale_wp_login_access' ]  == 'true' ) {
      add_action( 'login_init', array( 'flawless_my_account_module', 'login_init' ) , 5 );
    }

    add_filter( 'flawless::primary_notice_container', array( 'flawless_my_account_module', 'primary_notice_container' ), 10 );

  }


  /**
   * {missing description}
   *
   * Not functional - $_REQUEST seems to be cleared out.
   *
   * @todo Add better check to see if AJAX request really did fail. - potanin@UD
   * @author potanin@UD
   */
  function template_redirect() {

    /** If AJAX Login fails for some reason, we catch it here */
    if( $_POST[ 'action' ] == 'flawless_ajax_login' && wp_verify_nonce( $_POST[ 'flawless_login_nonce' ], 'flawless_login_nonce' ) ) {

      $response = (array) json_decode( flawless_my_account_module::ajax_login() );
      if( $response[ 'success' ] ) {
        die( wp_redirect( $response[ 'redirect_to' ] ) );
      }
    }

    /** If AJAX Login fails for some reason, we catch it here */
    if( $_REQUEST[ 'action' ] == 'flawless_ajax_reset_password' && wp_verify_nonce( $_REQUEST[ 'flawless_ajax_reset_password' ], 'flawless_ajax_reset_password' ) ) {

      $response = (array) json_decode( flawless_my_account_module::ajax_reset_password() );

      if( $response[ 'success' ] ) {
        die( wp_redirect( $response[ 'redirect_to' ] ) );
      }
    }

  }


  /**
   * Add Theme Settings option to prevent viewing of default WP Login page
   *
   * @author potanin@UD
   */
  function login_init() {

    $action = isset( $_REQUEST[ 'action' ] ) ? $_REQUEST[ 'action' ] : 'login';

    switch ( $action ) {

      case 'logout':
        check_admin_referer( 'log-out' );
        wp_logout();
        wp_safe_redirect( add_query_arg( 'user_action', 'logout', home_url() ) );
        exit;

      break;

      case 'rp':
        $user = check_password_reset_key( $_GET[ 'key' ], $_GET[ 'login' ] );
        $userdata = get_user_by( 'login', $_GET[ 'login' ] );

        if ( is_wp_error( $user ) || !$userdata->ID ) {
          wp_redirect( add_query_arg( 'user_action', 'password_reset_fail', home_url() ) );
          exit;
        }

        //** Create temporary password */
        $temporary_password = wp_generate_password( 13, false );

        //** Update User Profile with temporary password */
        wp_set_password( $temporary_password, $userdata->ID );

        //** Automatically log the user in */
        wp_signon( array(
          'user_login' => $_GET[ 'login' ],
          'user_password' => $temporary_password
        ) );

        //** Store random password to display to user, will be cleared out once the message is rendered */
        update_user_option( $userdata->ID, 'temporary_password', $temporary_password );

        //** Redirect user to home, where a notification is displayed for them */
        wp_redirect( add_query_arg( 'user_action', 'password_reset', home_url() ) );

        exit;

      break;

      default:

        /** Prevent a strange cookie-initiated loop from happening - ensure user is logged out here */
        if( is_user_logged_in() ) {
          wp_logout();
        }

        wp_redirect( add_query_arg( 'user_action', 'login', home_url() ) );
        exit;

      break;

    }

    wp_redirect( add_query_arg( 'user_action', 'unknown', home_url() ) );
    exit;

  }


  /**
   * Handles any login-related notices.
   *
   * @author potanin@UD
   */
  function primary_notice_container( $notices = array() ) {
    $user_action = $_GET[ 'user_action' ];

    if( empty( $user_action ) ) {
      return $notices;
    }

    $user = wp_get_current_user();

    switch ( $user_action ) {

      case 'password_reset':

        $temporary_password = get_user_option( 'temporary_password' );
        delete_user_option( $user->ID, 'temporary_password' );

        if( empty( $temporary_password ) ) {
          return $notices;
        }

        $notices[] = '<div class="alert alert-success"><a class="close" data-dismiss="alert" href="#">&times;</a>' . sprintf( __( 'Welcome back, %1s! Your new password is: <b>%2s</b>, you can change it in the My Account section. ', 'flawless' ), $user->data->display_name, $temporary_password ) . '</div>';

      break;

      case 'password_reset_fail':
        $notices[] = '<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">&times;</a>' . sprintf( __( 'It looks like you attempted to reset your password, but something did not work, perhaps you followed an expired reset link?', 'flawless' ) ) . '</div>';

      break;

      case 'login':
        if( is_user_logged_in() && $user->data->display_name ) {
          $notices[] = '<div class="alert alert-info"><a class="close" data-dismiss="alert" href="#">&times;</a>' . sprintf( __( 'Welcome back, %1s! ', 'flawless' ), $user->data->display_name ) . '</div>';
        } elseif( is_user_logged_in() ) {
          $notices[] = '<div class="alert alert-info"><a class="close" data-dismiss="alert" href="#">&times;</a>' . __( 'You have been logged in, welcome back.', 'flawless' ) . '</div>';
        }
      break;

      case 'logout':
        if( !is_user_logged_in() ) {
          $notices[] = '<div class="alert alert-info"><a class="close" data-dismiss="alert" href="#">&times;</a>' . __( 'You have been logged out.', 'flawless' ) . '</div>';
        }
      break;

    }

    return $notices;

  }


  /**
   * Add Theme Settings option to prevent viewing of default WP Login page
   *
   * @author potanin@UD
   */
  function options_ui_general_common_settings( $flawless ) { ?>

    <li>
      <label>
        <input type="checkbox" <?php echo checked( 'true', $flawless[ 'login_module' ][ 'disbale_wp_login_access' ] ); ?>  name="flawless_settings[login_module][disbale_wp_login_access]" value="true" />
        <?php _e( 'Disable default WordPress Login page.', 'flawless' ); ?>
        <span class="description"><?php _e( 'If disabled, visitors trying to access /wp-admin/ will be redirected to the home page.', 'flawless' ); ?></span>
      </label>
    </li>

  <?php
  }


  /**
   * Adds Login Module elements to Navbar
   *
   *
   * @note This is run too early for $post or $wp_query to be used.  - potanin@UD
   * @return array $html
   * @author peshkov@UD
   */
  function navbar_html( $html ) {
    global $flawless;

    if( $flawless[ 'navbar' ][ 'show_login' ] != 'true' || !is_array( $html ) ) {
      return $html;
    }

    if( !is_user_logged_in() ) {

      self::enqueue_scripts();

      $render_reset_password = "<li class=\"hidden navbar_reset_password_form\">" . self::render_reset_password( array(
        'form_class' => 'flawless_navbar_form form-horizontal',
        'submit_class' => 'btn-inverse',
      ) ) . "</li>";

      $navbar_login_form = "<li class=\"navbar_login_form\">" . self::render_module( array(
        'position' => 'navbar',
        'user_ul_class' => 'nav',
        'form_class' => 'flawless_navbar_form form-horizontal',
        'submit_class' => 'btn-inverse',
        'redirect_to' => ( is_singular() ? get_permalink( $post->ID ) : home_url() ),
      ) ) . "</li>";

      if( is_array( $html[ 'left' ] ) ) {
        $html[ 'right' ][] = $render_reset_password;
        $html[ 'right' ][] = $navbar_login_form;

      } else {
        $html[ 'right' ] = $html[ 'right' ] . $render_reset_password . $navbar_login_form;
      }

    } else {

      $admin_links = array();

      if( current_user_can( 'edit_posts' ) ) {
        $admin_links[ 'dashboard' ] = '<li class="nav_dashboard_link"><a class="nav_dashboard_link" href="' . apply_filters( 'flawless::dashboard_url', admin_url( '' ) ) . '" >' . __( 'Dashboard' , 'flawless' ) . '</a></li>';
      }

      /*
      if( current_user_can( 'read' ) ) {
        $admin_links[ 'my_account' ] = '<li class="nav_my_account_link"><a class="nav_my_account_link" href="' . apply_filters( 'flawless::my_account_url', admin_url( 'profile.php' )  ) . '" >' . __( 'My Account' , 'flawless' ) . '</a></li>';
      }
      */

      $admin_links[ 'logout' ] = '<li class="nav_logout_link"><a class="nav_logout_link default" href="' . wp_logout_url() . ' ">' . __( 'Logout', 'flawless' ) . '</a></li>';

      $admin_links = apply_filters( 'flawless::logged_in_links', $admin_links );

      if( is_array( $html[ 'left' ] ) ) {
        $html[ 'right' ][] = implode( '', ( array ) $admin_links );

      } else {
        $html[ 'right' ] = $html[ 'right' ] . implode( '', ( array ) $admin_links );;
      }

    }

    return $html;
  }


  /**
   * Renders Reset password Form
   *
   * @param array $args.
   * @return string. HTML
   * @author peshkov@UD
   */
  function render_reset_password( $args = array() ) {
    $return = "";
    $args = wp_parse_args( $args,  array(
      'form_class' => 'form-inline',
      'username_text' => __( 'Username or Email', 'flawless' ),
      'submit_text' => __( 'Get New Password', 'flawless' ),
      'submit_class' => 'btn-primary',
    ) );

    if( is_user_logged_in() ) return $return;
    self::enqueue_scripts();
    ob_start();

    ?>
    <div class="flawless_password_reset_form_wrapper">
      <form name="flawless_password_reset_form" class="flawless_password_reset_form <?php echo $args[ 'form_class' ]; ?>" action="<?php add_query_arg( 'action', 'flawless_ajax_reset_password', site_url() ); ?>" method="POST" >
        <?php wp_nonce_field( 'flawless_password_reset_form', 'flawless_password_reset_form' ); ?>
        <input type="hidden" name="action" value="flawless_ajax_reset_password" />
        <input class="input user_login" type="text" tabindex="10" size="20" placeholder="<?php echo $args[ 'username_text' ]; ?>" value="" name="user_login">
        <input type="submit" class="btn <?php echo $args[ 'submit_class' ]; ?>" data-loading-text="<?php _e( 'Processing', 'flawless' ); ?>" value="<?php echo $args[ 'submit_text' ]; ?>" />
        <span class="flawless_ajax_response label hidden"></span>
      </form>
    </div>
    <?php

    $return = ob_get_contents();
    ob_end_clean();

    return $return;
  }


  /**
   *  Render login form / my account view
   *
   * @todo The JS function for this module needs to check for a JSON response - if no response at all, or response is not in expected JSON format, some sort of notice should be displayed to user. - potaninU@D
   * @filters: flawless::dashboard_url, flawless::my_account_url, flawless::logged_in_links
   * @author potanin@UD
   */
  function render_module( $args = array() ) {
    global $post;

    $args = wp_parse_args( $args,  array(
      'form_class' => 'form-inline',
      'submit_class' => 'btn-primary',
      'position' => false,
      'user_ul_class' => 'nav',
      'username_text' => __( 'Username or Email', 'flawless' ),
      'password_text' => __( 'Password', 'flawless' ),
      'redirect_to' => add_query_arg( 'action', 'logged_in', home_url() ),
      'login_message' => false,
      'logged_in_menu' => false,
      'login_text' => __( 'Login', 'flawless' )
    ) );

    $args = apply_filters( 'flawless::my_account_module::render_module_args', $args );

    self::enqueue_scripts();
    ob_start();

    ?>
    <div class="flawless_my_account clearfix" current_status="<?php echo is_user_logged_in() ? 'logged_in' : 'logged_out';  ?>">
    <?php //** USER IS LOGGED IN */ ?>
    <?php if( is_user_logged_in() ) : ?>
      <ul class="logged_in_info <?php echo $args[ 'user_ul_class' ]; ?> clearfix">
        <?php echo implode( '', ( array ) $admin_links ); ?>
      </ul>
    <?php //** USER IS NOT LOGGED IN */ ?>
    <?php else: ?>
      <?php if( $args[ 'login_message' ] ) { ?><p class="f_login_message"><?php echo $args[ 'login_message' ]; ?></p><?php } ?>
      <div class="flawless_login_form_wrapper">
        <form name="flawless_login_form" class="flawless_login_form <?php echo $args[ 'form_class' ]; ?>" action="<?php echo add_query_arg( 'action', 'flawless_ajax_login', home_url( 'index.php' ) ) ; ?>" method="POST">
          <?php wp_nonce_field( 'flawless_login_nonce', 'flawless_login_nonce' ); ?>
          <input type="text" name="log" placeholder="<?php echo $args[ 'username_text' ]; ?>" class="span2 user_login">
          <input type="password" name="pwd" placeholder="<?php echo $args[ 'password_text' ]; ?>" class="span2 user_password">
          <input type="hidden" name="rememberme" value="forever" tabindex="90" />
          <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $args[ 'redirect_to' ] ); ?>" />
          <input type="hidden" name="testcookie" value="1" />
          <input type="hidden" name="action" value="flawless_ajax_login" />
          <input type="submit" class="btn <?php echo $args[ 'submit_class' ]; ?>" data-loading-text="<?php _e( 'Processing', 'flawless' ); ?>" value="<?php echo $args[ 'login_text' ]; ?>" />
          <span class="flawless_ajax_response label hidden"></span>
        </form>
      </div>
    <?php endif; ?>
    </div>
    <?php

    $return = ob_get_contents();
    ob_end_clean();

    return $return;
  }


  /**
   * Retrives password and send the email to user.
   * Based on wp-login.php functionality.
   *
   * @return JSON
   * @author peshkov@UD
   */
  function ajax_reset_password() {

    if( !wp_verify_nonce( $_POST[ 'flawless_password_reset_form' ], 'flawless_password_reset_form' ) ) {
      return json_encode( array(
        'success' => false
      ));
    }

    $response = array(
      'success' => true,
    );

    $http_post = ( 'POST' == $_SERVER[ 'REQUEST_METHOD' ] );

    if ( !$http_post ) {
      $response[ 'success' ] = false;
      $response[ 'error' ] = __( 'Request method is wrong', 'flawless' );

    } else {

      $errors = self::retrieve_password();

      if ( is_wp_error( $errors ) ) {
        $response[ 'success' ] = false;
        $error = $errors->get_error_code();

        switch( $error ) {
          case 'invalid_email':
          case 'Validation Fail':
            $response[ 'code' ]  = 'Validation Fail';
            $response[ 'error' ] = __( 'We could not find an account with the information you provided.  You may enter your username or e-mail address.', 'flawless' );
            break;
          case 'mail_could_not_be_sent':
            $response[ 'code' ]  = 'sendmail_fail';
            $response[ 'error' ] = __( 'There was a problem sending password reset e-mail. Please contact support.', 'flawless' );
            break;
          default:
            $response[ 'error' ] = $error->get_error_code();
            break;
        }
      } elseif( $errors ) {
        $response[ 'code' ] = 'notification_sent';
      }
    }

    return json_encode( $response );

  }


  /**
   * Handles login.
   * Based on wp-login.php functionality.
   *
   * May be called directly when AJAX fails due to a JS error.
   *
   * @return JSON
   * @author peshkov@UD
   */
  function ajax_login() {

    if( !wp_verify_nonce( $_POST[ 'flawless_login_nonce' ], 'flawless_login_nonce' ) ) {
      return json_encode( array(
        'success' => false
      ));
    }

    $secure_cookie = '';
    $user = false;

    //** Try to get user by e-mail */
    if( is_email( $_POST[ 'log' ] ) ) {
      $user = get_user_by( 'email', $_POST[ 'log' ] );
    }

    //** If no match, try to get by login */
    if( !$user ) {
      $user = get_user_by( 'login', sanitize_user( $_POST[ 'log' ] ) );
    }

    $user = apply_filters( 'flawless::login_name', $user, $_POST[ 'log' ] );

    //* If the user wants ssl but the session is not ssl, force a secure cookie. */
    if ( !empty( $_POST[ 'log' ] ) && !force_ssl_admin() ) {
      if ( $user ) {
        if ( get_user_option( 'use_ssl', $user->ID ) ) {
          $secure_cookie = true;
          force_ssl_admin( true );
        }
      }
    }

    if ( isset( $_REQUEST[ 'redirect_to' ] ) ) {
      $redirect_to = $_REQUEST[ 'redirect_to' ];
      //** Redirect to https if user wants ssl */
      if ( $secure_cookie && false !== strpos( $redirect_to, 'wp-admin' ) )
      $redirect_to = preg_replace( '|^http://|', 'https://', $redirect_to );
    } else {
      $redirect_to = false;
    }

    /**
     * If the user was redirected to a secure login form from a non-secure admin page,
     * and secure login is required but secure admin is not, then don't use a secure
     * cookie and redirect back to the referring non-secure admin page.  This allows logins
     * to always be POSTed over SSL while allowing the user to choose visiting the admin via http or https.
     */
    if ( !$secure_cookie && is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos( $redirect_to, 'https' ) ) && ( 0 === strpos( $redirect_to, 'http' ) ) ) {
      $secure_cookie = false;
    }

    /* If a user login was found */
    if( $user->user_login ) {

      $user = wp_signon( array(
        'user_login' => $user->user_login,
        'user_password' => $_POST[ 'pwd' ],
        'remember' => ( !empty( $_POST[ 'rememberme' ] ) ? true : false ),
      ), $secure_cookie );

      if ( is_wp_error( $user ) ) {

        $response = array(
          'success' => false,
          'code' => $user->get_error_code()
        );

        switch( $response[ 'code' ] ) {
          case 'invalid_username':
            $response[ 'error' ] = __( 'Your login credentials could not be verified.', 'flawless' );
            $response[ 'code' ] = 'Validation Fail';
            break;
          case 'incorrect_password':
            $response[ 'error' ] = __( 'Your login credentials could not be verified.', 'flawless' );
            $response[ 'code' ] = 'Validation Fail';
            break;
          default:
            $response[ 'error' ] = $response[ 'code' ];
            break;
        }

      } else {

        $response = array(
          'success' => true,
          'redirect_to' => apply_filters( 'flawless::login_redirect', $redirect_to, $user->ID )
        );

      }

    } else {

      $response = array(
        'success' => false,
        'code' => 'Validation Fail',
        'error' => __( 'Your login credentials could not be verified.', 'flawless' )
      );

    }

    return json_encode( $response );

  }


  /**
   *  Handle front-end log out.
   *
   *
   */
  function ajax_logout() {
    wp_logout();
    $response = array( 'success' => true );
    return json_encode( $response );
  }


  /**
   * Enqueues ( once ) specific scripts.
   *
   * @author peshkov@UD
   */
  protected function enqueue_scripts() {
    static $loaded = false;

    if( $loaded ) {
      return;
    }

    wp_enqueue_script( 'flawless-login_module', get_bloginfo( 'template_url' ) . '/js/flawless.login_module.js', array( 'flawless-frontend' ), Flawless_Version, true );

    $l10n = array(
      'log_in' => __( 'Log in', 'flawless' ),
      'forget_password' => __( 'Would you like to reset your password?', 'flawless' ),
      'enter_fields_properly' => __( 'Please enter your Username or Email properly', 'flawless' ),
      'email_was_sent' => __( 'Sending password resetting instructions. Please check your e-mail. ', 'flawless' ),
      'something_wrong' => __( 'Something went wrong, please, try again later.', 'flawless' ),
      'enter_login' => __( 'Please enter your username, or e-mail address.', 'flawless' ),
      'enter_password' => __( 'Please enter a password.', 'flawless' )
    );

    wp_localize_script( 'flawless-login_module', 'lm_l10n', $l10n );
    $loaded = true;

  }


  /**
  * Handles sending password retrieval email to user.
  * The current method just duplicates WP function retrieve_password().
  *
  * @uses $wpdb WordPress Database object
  * @return bool|WP_Error True: when finish. WP_Error on error
  * @author peshkov@UD
  */
  protected function retrieve_password() {
    global $wpdb, $current_site;

    $errors = new WP_Error();

    if ( empty( $_POST[ 'user_login' ] ) ) {
      $errors->add( 'empty_username', __( '<strong>ERROR</strong>: Enter a username or e-mail address.' ) );

    } elseif ( strpos( $_POST[ 'user_login' ], '@' ) ) {
      $user_data = get_user_by( 'email', trim( $_POST[ 'user_login' ] ) );

      if ( empty( $user_data ) ) {
        $errors->add( 'invalid_email', __( '<strong>ERROR</strong>: There is no user registered with that email address.' ) );
      }

    } else {
      $login = trim( $_POST[ 'user_login' ] );
      $user_data = get_user_by( 'login', $login );
    }

    do_action( 'lostpassword_post' );

    if ( $errors->get_error_code() ) {
      return $errors;
    }

    if ( !$user_data ) {
      $errors->add( 'Validation Fail', __( '<strong>ERROR</strong>: Invalid username or e-mail.' ) );
      return $errors;
    }

    // redefining user_login ensures we return the right case in the email
    $user_login = $user_data->user_login;
    $user_email = $user_data->user_email;

    //** If WP-CRM is enabled, it hooks into the following action to check if WP password reset email is disabled */
    do_action( 'retrieve_password', $user_login );

    $allow = apply_filters( 'allow_password_reset', true, $user_data->ID );

    if ( ! $allow ) {
      return new WP_Error( 'no_password_reset', __( 'Password reset is not allowed for this user' ) );
    } elseif ( is_wp_error( $allow ) ) {
      return $allow;
    }

    $key = $wpdb->get_var( $wpdb->prepare( "SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login ) );

    if ( empty( $key ) ) {
      // Generate something random for a key...
      $key = wp_generate_password( 20, false );
      do_action( 'retrieve_password_key', $user_login, $key );
      // Now insert the new md5 key into the db
      $wpdb->update( $wpdb->users, array( 'user_activation_key' => $key ), array( 'user_login' => $user_login ) );
    }

    $message = __( 'Someone requested that the password be reset for the following account:' ) . "\r\n\r\n";
    $message .= network_site_url() . "\r\n\r\n";
    $message .= sprintf( __( 'Username: %s' ), $user_login ) . "\r\n\r\n";
    $message .= __( 'If this was a mistake, just ignore this email and nothing will happen.' ) . "\r\n\r\n";
    $message .= __( 'To reset your password, visit the following address:' ) . "\r\n\r\n";
    $message .= '<' . network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . ">\r\n";

    if ( is_multisite() ) {
      $blogname = $GLOBALS[ 'current_site' ]->site_name;
    } else {
      $blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
    }

    $title = sprintf( __( '[%s] Password Reset' ), $blogname );

    $title = apply_filters( 'retrieve_password_title', $title );

    //** WP-CRM Blanks thie following value out if the WP Password Rests are disabled in WP-CRM Settings, and stops the mail from going out here */
    $message = apply_filters( 'retrieve_password_message', $message, $key );

    if ( $message && !wp_mail( $user_email, $title, $message ) ) {
      $errors->add( 'mail_could_not_be_sent', __( '<strong>ERROR</strong>: The e-mail could not be sent. Possible reason: your host may have disabled the mail() function...' ) );
      return $errors;
    }
    return true;
  }

}

