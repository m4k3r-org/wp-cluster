<?php
/**
 * Domain Mapping
 *
 *
 * @version 0.1.5
 * @module Veneer
 * @author potanin@UD
 */
namespace UsabilityDynamics\Veneer {

  /**
   * Class Locale
   *
   * @module Veneer
   */
  class Mapping {

    /**
     * Current site (blog)
     *
     * @public
     * @static
     * @property $site_id
     * @type {Object}
     */
    public $site_url = null;

    /**
     * Initialize Locale
     *
     * @for Locale
     */
    public function __construct() {

      // URLs
      $this->home_url          = get_home_url();
      $this->site_url          = get_site_url();
      $this->admin_url         = get_admin_url();
      $this->includes_url      = includes_url();
      $this->content_url       = content_url();
      $this->plugins_url       = plugins_url();
      $this->network_site_url  = network_site_url();
      $this->network_home_url  = network_home_url();
      $this->network_admin_url = network_admin_url();
      $this->self_admin_url    = self_admin_url();
      $this->user_admin_url    = user_admin_url();

      // overrite "home" option / home_url()
      add_filter( 'pre_option_home', function() {
        return ( is_ssl() ? 'https://' : 'http://' ) . Bootstrap::get_instance()->primary_domain;
      });

      // Overrite "site" option / site_url()
      add_filter( 'pre_option_siteurl', function() {
        return ( is_ssl() ? 'https://' : 'http://' ) . Bootstrap::get_instance()->primary_domain;
      });

    }

    public function legacy() {

      add_action( 'manage_blogs_custom_column', array( $this, 'ra_domain_mapping_field' ), 1, 3 );
      add_action( 'manage_sites_custom_column', array( $this, 'ra_domain_mapping_field' ), 1, 3 );

      add_action( 'admin_menu', array( $this, 'add_pages' ) );
      add_action( 'network_admin_menu', array( $this, 'network_pages' ) );
      add_action( 'dm_echo_updated_msg', array( $this, 'dm_echo_default_updated_msg' ) );

      if( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'domainmapping' ) {
        // add_action( 'admin_init', array( $this, 'dm_handle_actions' ) );
      }

      if( isset( $_GET[ 'dm' ] ) ) {
        // add_action( 'template_redirect', array( $this, 'remote_login_js' ) );

      }

      return;

      add_filter( 'wpmu_blogs_columns', array( $this, 'ra_domain_mapping_columns' ) );

      $this->siteurl = $this->domain_siteurl();

      if( defined( 'DOMAIN_MAPPING' ) ) {

        add_filter( 'pre_option_siteurl', array( $this, 'domain_siteurl' ) );
        add_filter( 'pre_option_home', array( $this, 'domain_siteurl' ) );

        add_action( 'wp_head', array( $this, 'remote_login_js_loader' ) );
        add_action( 'login_head', array( $this, 'redirect_login_to_orig' ) );
        add_action( 'wp_logout', array( $this, 'remote_logout_loader' ), 9999 );

        add_filter( 'the_content', array( $this, 'domain_mapping_post_content' ) );
        add_filter( 'theme_root_uri', array( $this, 'domain_mapping_themes_uri' ), 1 );
        add_filter( 'stylesheet_uri', array( $this, 'domain_mapping_post_content' ) );
        add_filter( 'stylesheet_directory', array( $this, 'domain_mapping_post_content' ) );
        add_filter( 'stylesheet_directory_uri', array( $this, 'domain_mapping_post_content' ) );
        add_filter( 'template_directory', array( $this, 'domain_mapping_post_content' ) );
        add_filter( 'template_directory_uri', array( $this, 'domain_mapping_post_content' ) );
        add_filter( 'plugins_url', array( $this, 'domain_mapping_post_content' ) ); // Affects self_admin_url - removes the domain name fully

        //add_filter( 'plugins_url', array( $this, 'domain_mapping_plugins_uri' ), 1 );

      } else {
        add_filter( 'admin_url', array( $this, 'domain_mapping_adminurl' ), 10, 3 );
      }

      add_action( 'template_redirect', array( $this, 'redirect_to_mapped_domain' ) );
      add_action( 'admin_init', array( $this, 'dm_redirect_admin' ) );
      add_action( 'delete_blog', array( $this, 'delete_blog_domain_mapping' ), 1, 2 );

    }

    public static function domain_mapping_warning() {
      echo "<div id='domain-mapping-warning' class='updated fade'><p><strong>" . __( 'Domain Mapping Disabled.', Bootstrap::$text_domain ) . "</strong> " . sprintf( __( 'You must <a href="%1$s">create a network</a> for it to work.', Bootstrap::$text_domain ), "http://codex.wordpress.org/Create_A_Network" ) . "</p></div>";
    }

    public static function add_pages() {
      global $current_site, $wpdb, $wp_db_version, $wp_version;

      if( !isset( $current_site ) && $wp_db_version >= 15260 ) { // WP 3.0 network hasn't been configured
        add_action( 'admin_notices', array( get_class(), 'domain_mapping_warning' ) );

        return false;
      }
      if( $current_site->path != "/" ) {
        wp_die( __( "The domain mapping plugin only works if the site is installed in /. This is a limitation of how virtual servers work and is very difficult to work around.", Bootstrap::$text_domain ) );
      }

      if( get_site_option( 'dm_user_settings' ) && $current_site->blog_id != $wpdb->blogid && !static::dm_sunrise_warning( false ) ) {
        add_management_page( __( 'Domain Mapping', Bootstrap::$text_domain ), __( 'Domain Mapping', Bootstrap::$text_domain ), 'manage_options', 'domainmapping', array( get_class(), 'dm_manage_page' ) );
      }

    }

    public static function network_pages() {
      add_submenu_page( 'settings.php', 'Domain Mapping', 'Domain Mapping', 'manage_options', 'dm_admin_page', array( get_class(), 'dm_admin_page' ) );
      add_submenu_page( 'settings.php', 'Domains', 'Domains', 'manage_options', 'dm_domains_admin', array( get_class(), 'dm_domains_admin' ) );
    }

    public static function dm_echo_default_updated_msg() {
      switch( $_GET[ 'updated' ] ) {
        case "add":
          $msg = __( 'New domain added.', Bootstrap::$text_domain );
          break;
        case "exists":
          $msg = __( 'New domain already exists.', Bootstrap::$text_domain );
          break;
        case "primary":
          $msg = __( 'New primary domain.', Bootstrap::$text_domain );
          break;
        case "del":
          $msg = __( 'Domain deleted.', Bootstrap::$text_domain );
          break;
      }
      echo "<div class='updated fade'><p>$msg</p></div>";
    }

    public static function maybe_create_db() {
      global $wpdb;

      static::get_dm_hash(); // initialise the remote login hash

      $wpdb->dmtable       = $wpdb->base_prefix . 'domain_mapping';
      $wpdb->dmtablelogins = $wpdb->base_prefix . 'domain_mapping_logins';
      if( static::dm_site_admin() ) {
        $created = 0;
        if( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->dmtable}'" ) != $wpdb->dmtable ) {
          $wpdb->query( "CREATE TABLE IF NOT EXISTS {$wpdb->dmtable} (
				id bigint(20) NOT NULL auto_increment,
				blog_id bigint(20) NOT NULL,
				domain varchar(255) NOT NULL,
				active tinyint(4) default '1',
				PRIMARY KEY  (id),
				KEY blog_id (blog_id,domain,active)
			);" );
          $created = 1;
        }
        if( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->dmtablelogins}'" ) != $wpdb->dmtablelogins ) {
          $wpdb->query( "CREATE TABLE IF NOT EXISTS {$wpdb->dmtablelogins} (
				id varchar(32) NOT NULL,
				user_id bigint(20) NOT NULL,
				blog_id bigint(20) NOT NULL,
				t timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
				PRIMARY KEY  (id)
			);" );
          $created = 1;
        }
        if( $created ) {
          echo __( 'Domain mapping database table created.', Bootstrap::$text_domain );
        }
      }

    }

    public static function dm_domains_admin() {
      global $wpdb, $current_site;
      if( false == static::dm_site_admin() ) { // paranoid? moi?
        return false;
      }

      static::dm_sunrise_warning();

      if( $current_site->path != "/" ) {
        wp_die( sprintf( __( "<strong>Warning!</strong> This plugin will only work if WordPress is installed in the root directory of your webserver. It is currently installed in &#8217;%s&#8217;.", "wordpress-mu-domain-mapping" ), $current_site->path ) );
      }

      echo '<h2>' . __( 'Domain Mapping: Domains', Bootstrap::$text_domain ) . '</h2>';
      if( !empty( $_POST[ 'action' ] ) ) {
        check_admin_referer( 'domain_mapping' );
        $domain = strtolower( $_POST[ 'domain' ] );
        switch( $_POST[ 'action' ] ) {
          case "edit":
            $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->dmtable} WHERE domain = %s", $domain ) );
            if( $row ) {
              static::edit_domain( $row );
            } else {
              echo "<h3>" . __( 'Domain not found', Bootstrap::$text_domain ) . "</h3>";
            }
            break;
          case "save":
            if( $_POST[ 'blog_id' ] != 0 AND
              $_POST[ 'blog_id' ] != 1 AND
              null == $wpdb->get_var( $wpdb->prepare( "SELECT domain FROM {$wpdb->dmtable} WHERE blog_id != %d AND domain = %s", $_POST[ 'blog_id' ], $domain ) )
            ) {
              if( $_POST[ 'orig_domain' ] == '' ) {
                $wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->dmtable} ( blog_id, domain, active ) VALUES ( %d, %s, %d )", $_POST[ 'blog_id' ], $domain, $_POST[ 'active' ] ) );
                echo "<p><strong>" . __( 'Domain Add', Bootstrap::$text_domain ) . "</strong></p>";
              } else {
                $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->dmtable} SET blog_id = %d, domain = %s, active = %d WHERE domain = %s", $_POST[ 'blog_id' ], $domain, $_POST[ 'active' ], $_POST[ 'orig_domain' ] ) );
                echo "<p><strong>" . __( 'Domain Updated', Bootstrap::$text_domain ) . "</strong></p>";
              }
            }
            break;
          case "del":
            $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->dmtable} WHERE domain = %s", $domain ) );
            echo "<p><strong>" . __( 'Domain Deleted', Bootstrap::$text_domain ) . "</strong></p>";
            break;
          case "search":
            $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->dmtable} WHERE domain LIKE %s", $domain ) );
            static::domain_listing( $rows, sprintf( __( "Searching for %s", Bootstrap::$text_domain ), esc_html( $domain ) ) );
            break;
        }
        if( $_POST[ 'action' ] == 'update' ) {
          if( preg_match( '|^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$|', $_POST[ 'ipaddress' ] ) )
            update_site_option( 'dm_ipaddress', $_POST[ 'ipaddress' ] );

          if( !preg_match( '/(--|\.\.)/', $_POST[ 'cname' ] ) && preg_match( '|^([a-zA-Z0-9-\.])+$|', $_POST[ 'cname' ] ) )
            update_site_option( 'dm_cname', stripslashes( $_POST[ 'cname' ] ) );
          else
            update_site_option( 'dm_cname', '' );

          update_site_option( 'dm_301_redirect', intval( $_POST[ 'permanent_redirect' ] ) );
        }
      }

      echo "<h3>" . __( 'Search Domains', Bootstrap::$text_domain ) . "</h3>";
      echo '<form method="POST">';
      wp_nonce_field( 'domain_mapping' );
      echo '<input type="hidden" name="action" value="search" />';
      echo '<p>';
      echo _e( "Domain:", Bootstrap::$text_domain );
      echo " <input type='text' name='domain' value='' /></p>";
      echo "<p><input type='submit' class='button-secondary' value='" . __( 'Search', Bootstrap::$text_domain ) . "' /></p>";
      echo "</form><br />";
      static::edit_domain();
      $rows = $wpdb->get_results( "SELECT * FROM {$wpdb->dmtable} ORDER BY id DESC LIMIT 0,20" );
      static::domain_listing( $rows );
      echo '<p>' . sprintf( __( '<strong>Note:</strong> %s', Bootstrap::$text_domain ), static::dm_idn_warning() ) . "</p>";
    }

    public static function edit_domain( $row = false ) {
      if( is_object( $row ) ) {
        echo "<h3>" . __( 'Edit Domain', Bootstrap::$text_domain ) . "</h3>";
      } else {
        echo "<h3>" . __( 'New Domain', Bootstrap::$text_domain ) . "</h3>";
        $row               = (object) Array();
        $row->blog_id      = '';
        $row->domain       = '';
        $_POST[ 'domain' ] = '';
        $row->active       = 1;
      }

      echo "<form method='POST'><input type='hidden' name='action' value='save' /><input type='hidden' name='orig_domain' value='" . esc_attr( $_POST[ 'domain' ] ) . "' />";
      wp_nonce_field( 'domain_mapping' );
      echo "<table class='form-table'>\n";
      echo "<tr><th>" . __( 'Site ID', Bootstrap::$text_domain ) . "</th><td><input type='text' name='blog_id' value='{$row->blog_id}' /></td></tr>\n";
      echo "<tr><th>" . __( 'Domain', Bootstrap::$text_domain ) . "</th><td><input type='text' name='domain' value='{$row->domain}' /></td></tr>\n";
      echo "<tr><th>" . __( 'Primary', Bootstrap::$text_domain ) . "</th><td><input type='checkbox' name='active' value='1' ";
      echo $row->active == 1 ? 'checked=1 ' : ' ';
      echo "/></td></tr>\n";
      if( get_site_option( 'dm_no_primary_domain' ) == 1 ) {
        echo "<tr><td colspan='2'>" . __( '<strong>Warning!</strong> Primary domains are currently disabled.', Bootstrap::$text_domain ) . "</td></tr>";
      }
      echo "</table>";
      echo "<p><input type='submit' class='button-primary' value='" . __( 'Save', Bootstrap::$text_domain ) . "' /></p></form><br /><br />";
    }

    public static function domain_listing( $rows, $heading = '' ) {
      if( $rows ) {
        if( file_exists( ABSPATH . 'wp-admin/network/site-info.php' ) ) {
          $edit_url = network_admin_url( 'site-info.php' );
        } elseif( file_exists( ABSPATH . 'wp-admin/ms-sites.php' ) ) {
          $edit_url = admin_url( 'ms-sites.php' );
        } else {
          $edit_url = admin_url( 'wpmu-blogs.php' );
        }
        if( $heading != '' )
          echo "<h3>$heading</h3>";
        echo '<table class="widefat" cellspacing="0"><thead><tr><th>' . __( 'Site ID', Bootstrap::$text_domain ) . '</th><th>' . __( 'Domain', Bootstrap::$text_domain ) . '</th><th>' . __( 'Primary', Bootstrap::$text_domain ) . '</th><th>' . __( 'Edit', Bootstrap::$text_domain ) . '</th><th>' . __( 'Delete', Bootstrap::$text_domain ) . '</th></tr></thead><tbody>';
        foreach( $rows as $row ) {
          echo "<tr><td><a href='" . add_query_arg( array( 'action' => 'editblog', 'id' => $row->blog_id ), $edit_url ) . "'>{$row->blog_id}</a></td><td><a href='http://{$row->domain}/'>{$row->domain}</a></td><td>";
          echo $row->active == 1 ? __( 'Yes', Bootstrap::$text_domain ) : __( 'No', Bootstrap::$text_domain );
          echo "</td><td><form method='POST'><input type='hidden' name='action' value='edit' /><input type='hidden' name='domain' value='{$row->domain}' />";
          wp_nonce_field( 'domain_mapping' );
          echo "<input type='submit' class='button-secondary' value='" . __( 'Edit', Bootstrap::$text_domain ) . "' /></form></td><td><form method='POST'><input type='hidden' name='action' value='del' /><input type='hidden' name='domain' value='{$row->domain}' />";
          wp_nonce_field( 'domain_mapping' );
          echo "<input type='submit' class='button-secondary' value='" . __( 'Del', Bootstrap::$text_domain ) . "' /></form>";
          echo "</td></tr>";
        }
        echo '</table>';
        if( get_site_option( 'dm_no_primary_domain' ) == 1 ) {
          echo "<p>" . __( '<strong>Warning!</strong> Primary domains are currently disabled.', Bootstrap::$text_domain ) . "</p>";
        }
      }
    }

    public static function dm_admin_page() {
      global $wpdb, $current_site;
      if( false == static::dm_site_admin() ) { // paranoid? moi?
        return false;
      }

      static::dm_sunrise_warning();
      static::maybe_create_db();

      if( $current_site->path != "/" ) {
        wp_die( sprintf( __( "<strong>Warning!</strong> This plugin will only work if WordPress is installed in the root directory of your webserver. It is currently installed in &#8217;%s&#8217;.", "wordpress-mu-domain-mapping" ), $current_site->path ) );
      }

      // set up some defaults
      if( get_site_option( 'dm_remote_login', 'NA' ) == 'NA' )
        add_site_option( 'dm_remote_login', 1 );
      if( get_site_option( 'dm_redirect_admin', 'NA' ) == 'NA' )
        add_site_option( 'dm_redirect_admin', 1 );
      if( get_site_option( 'dm_user_settings', 'NA' ) == 'NA' )
        add_site_option( 'dm_user_settings', 1 );

      if( !empty( $_POST[ 'action' ] ) ) {
        check_admin_referer( 'domain_mapping' );
        if( $_POST[ 'action' ] == 'update' ) {
          $ipok        = true;
          $ipaddresses = explode( ',', $_POST[ 'ipaddress' ] );
          foreach( $ipaddresses as $address ) {
            if( ( $ip = trim( $address ) ) && !preg_match( '|^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$|', $ip ) ) {
              $ipok = false;
              break;
            }
          }
          if( $ipok )
            update_site_option( 'dm_ipaddress', $_POST[ 'ipaddress' ] );
          if( intval( $_POST[ 'always_redirect_admin' ] ) == 0 )
            $_POST[ 'dm_remote_login' ] = 0; // disable remote login if redirecting to mapped domain
          update_site_option( 'dm_remote_login', intval( $_POST[ 'dm_remote_login' ] ) );
          if( !preg_match( '/(--|\.\.)/', $_POST[ 'cname' ] ) && preg_match( '|^([a-zA-Z0-9-\.])+$|', $_POST[ 'cname' ] ) )
            update_site_option( 'dm_cname', stripslashes( $_POST[ 'cname' ] ) );
          else
            update_site_option( 'dm_cname', '' );
          update_site_option( 'dm_301_redirect', isset( $_POST[ 'permanent_redirect' ] ) ? intval( $_POST[ 'permanent_redirect' ] ) : 0 );
          update_site_option( 'dm_redirect_admin', isset( $_POST[ 'always_redirect_admin' ] ) ? intval( $_POST[ 'always_redirect_admin' ] ) : 0 );
          update_site_option( 'dm_user_settings', isset( $_POST[ 'dm_user_settings' ] ) ? intval( $_POST[ 'dm_user_settings' ] ) : 0 );
          update_site_option( 'dm_no_primary_domain', isset( $_POST[ 'dm_no_primary_domain' ] ) ? intval( $_POST[ 'dm_no_primary_domain' ] ) : 0 );
        }
      }

      echo '<h3>' . __( 'Domain Mapping Configuration', Bootstrap::$text_domain ) . '</h3>';
      echo '<form method="POST">';
      echo '<input type="hidden" name="action" value="update" />';
      echo "<p>" . __( "As a super admin on this network you can set the IP address users need to point their DNS A records at <em>or</em> the domain to point CNAME record at. If you don't know what the IP address is, ping this blog to get it.", Bootstrap::$text_domain ) . "</p>";
      echo "<p>" . __( "If you use round robin DNS or another load balancing technique with more than one IP, enter each address, separating them by commas.", Bootstrap::$text_domain ) . "</p>";
      _e( "Server IP Address: ", Bootstrap::$text_domain );
      echo "<input type='text' name='ipaddress' value='" . get_site_option( 'dm_ipaddress' ) . "' /><br />";

      // Using a CNAME is a safer method than using IP adresses for some people (IMHO)
      echo "<p>" . __( "If you prefer the use of a CNAME record, you can set the domain here. This domain must be configured with an A record or ANAME pointing at an IP address. Visitors may experience problems if it is a CNAME of another domain.", Bootstrap::$text_domain ) . "</p>";
      echo "<p>" . __( "NOTE, this voids the use of any IP address set above", Bootstrap::$text_domain ) . "</p>";
      _e( "Server CNAME domain: ", Bootstrap::$text_domain );
      echo "<input type='text' name='cname' value='" . get_site_option( 'dm_cname' ) . "' /> (" . static::dm_idn_warning() . ")<br />";
      echo '<p>' . __( 'The information you enter here will be shown to your users so they can configure their DNS correctly. It is for informational purposes only', Bootstrap::$text_domain ) . '</p>';

      echo "<h3>" . __( 'Domain Options', Bootstrap::$text_domain ) . "</h3>";
      echo "<ol><li><input type='checkbox' name='dm_remote_login' value='1' ";
      echo get_site_option( 'dm_remote_login' ) == 1 ? "checked='checked'" : "";
      echo " /> " . __( 'Remote Login', Bootstrap::$text_domain ) . "</li>";
      echo "<li><input type='checkbox' name='permanent_redirect' value='1' ";
      echo get_site_option( 'dm_301_redirect' ) == 1 ? "checked='checked'" : "";
      echo " /> " . __( "Permanent redirect (better for your blogger's pagerank)", Bootstrap::$text_domain ) . "</li>";
      echo "<li><input type='checkbox' name='dm_user_settings' value='1' ";
      echo get_site_option( 'dm_user_settings' ) == 1 ? "checked='checked'" : "";
      echo " /> " . __( 'User domain mapping page', Bootstrap::$text_domain ) . "</li> ";
      echo "<li><input type='checkbox' name='always_redirect_admin' value='1' ";
      echo get_site_option( 'dm_redirect_admin' ) == 1 ? "checked='checked'" : "";
      echo " /> " . __( "Redirect administration pages to site's original domain (remote login disabled if this redirect is disabled)", Bootstrap::$text_domain ) . "</li>";
      echo "<li><input type='checkbox' name='dm_no_primary_domain' value='1' ";
      echo get_site_option( 'dm_no_primary_domain' ) == 1 ? "checked='checked'" : "";
      echo " /> " . __( "Disable primary domain check. Sites will not redirect to one domain name. May cause duplicate content issues.", Bootstrap::$text_domain ) . "</li></ol>";
      wp_nonce_field( 'domain_mapping' );
      echo "<p><input class='button-primary' type='submit' value='" . __( "Save", Bootstrap::$text_domain ) . "' /></p>";
      echo "</form><br />";
    }

    public static function dm_handle_actions() {
      global $wpdb, $parent_file;
      $url = add_query_arg( array( 'page' => 'domainmapping' ), admin_url( $parent_file ) );
      if( !empty( $_POST[ 'action' ] ) ) {
        $domain = $wpdb->escape( $_POST[ 'domain' ] );
        if( $domain == '' ) {
          wp_die( "You must enter a domain" );
        }
        check_admin_referer( 'domain_mapping' );
        do_action( 'dm_handle_actions_init', $domain );
        switch( $_POST[ 'action' ] ) {
          case "add":
            do_action( 'dm_handle_actions_add', $domain );
            if( null == $wpdb->get_row( "SELECT blog_id FROM {$wpdb->blogs} WHERE domain = '$domain'" ) && null == $wpdb->get_row( "SELECT blog_id FROM {$wpdb->dmtable} WHERE domain = '$domain'" ) ) {
              if( $_POST[ 'primary' ] ) {
                $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->dmtable} SET active = 0 WHERE blog_id = %d", $wpdb->blogid ) );
              }
              $wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->dmtable} ( id , blog_id , domain , active ) VALUES ( NULL, %d, %s, %d )", $wpdb->blogid, $domain, $_POST[ 'primary' ] ) );
              wp_redirect( add_query_arg( array( 'updated' => 'add' ), $url ) );
              exit;
            } else {
              wp_redirect( add_query_arg( array( 'updated' => 'exists' ), $url ) );
              exit;
            }
            break;
          case "primary":
            do_action( 'dm_handle_actions_primary', $domain );
            $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->dmtable} SET active = 0 WHERE blog_id = %d", $wpdb->blogid ) );
            $orig_url = parse_url( static::get_original_url( 'siteurl' ) );
            if( $domain != $orig_url[ 'host' ] ) {
              $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->dmtable} SET active = 1 WHERE domain = %s", $domain ) );
            }
            wp_redirect( add_query_arg( array( 'updated' => 'primary' ), $url ) );
            exit;
            break;
        }
      } elseif( $_GET[ 'action' ] == 'delete' ) {
        $domain = $wpdb->escape( $_GET[ 'domain' ] );
        if( $domain == '' ) {
          wp_die( __( "You must enter a domain", Bootstrap::$text_domain ) );
        }
        check_admin_referer( "delete" . $_GET[ 'domain' ] );
        do_action( 'dm_handle_actions_del', $domain );
        $wpdb->query( "DELETE FROM {$wpdb->dmtable} WHERE domain = '$domain'" );
        wp_redirect( add_query_arg( array( 'updated' => 'del' ), $url ) );
        exit;
      }

    }

    public static function dm_sunrise_warning( $die = true ) {
      if( !file_exists( WP_CONTENT_DIR . '/sunrise.php' ) ) {
        if( !$die )
          return true;

        if( static::dm_site_admin() ) {
          wp_die( sprintf( __( "Please copy sunrise.php to %s/sunrise.php and ensure the SUNRISE definition is in %swp-config.php", Bootstrap::$text_domain ), WP_CONTENT_DIR, ABSPATH ) );
        } else {
          wp_die( __( "This plugin has not been configured correctly yet.", Bootstrap::$text_domain ) );
        }
      } elseif( !defined( 'SUNRISE' ) ) {
        if( !$die )
          return true;

        if( static::dm_site_admin() ) {
          wp_die( sprintf( __( "Please uncomment the line <em>define( 'SUNRISE', 'on' );</em> or add it to your %swp-config.php", Bootstrap::$text_domain ), ABSPATH ) );
        } else {
          wp_die( __( "This plugin has not been configured correctly yet.", Bootstrap::$text_domain ) );
        }
      } elseif( !defined( 'SUNRISE_LOADED' ) ) {
        if( !$die )
          return true;

        if( static::dm_site_admin() ) {
          wp_die( sprintf( __( "Please edit your %swp-config.php and move the line <em>define( 'SUNRISE', 'on' );</em> above the last require_once() in that file or make sure you updated sunrise.php.", Bootstrap::$text_domain ), ABSPATH ) );
        } else {
          wp_die( __( "This plugin has not been configured correctly yet.", Bootstrap::$text_domain ) );
        }
      }

      return false;
    }

    public static function dm_manage_page() {

      global $wpdb, $parent_file;

      if( isset( $_GET[ 'updated' ] ) ) {
        do_action( 'dm_echo_updated_msg' );
      }

      static::dm_sunrise_warning();

      echo "<div class='wrap'><h2>" . __( 'Domain Mapping', Bootstrap::$text_domain ) . "</h2>";

      if( false == get_site_option( 'dm_ipaddress' ) && false == get_site_option( 'dm_cname' ) ) {
        static::dm_site_admin;
      }

      $protocol = is_ssl() ? 'https://' : 'http://';
      $domains  = $wpdb->get_results( "SELECT * FROM {$wpdb->dmtable} WHERE blog_id = '{$wpdb->blogid}'", ARRAY_A );
      if( is_array( $domains ) && !empty( $domains ) ) {
        $orig_url   = parse_url( static::get_original_url( 'siteurl' ) );
        $domains[ ] = array( 'domain' => $orig_url[ 'host' ], 'path' => $orig_url[ 'path' ], 'active' => 0 );
        echo "<h3>" . __( 'Active domains on this blog', Bootstrap::$text_domain ) . "</h3>";
        echo '<form method="POST">';
        echo "<table><tr><th>" . __( 'Primary', Bootstrap::$text_domain ) . "</th><th>" . __( 'Domain', Bootstrap::$text_domain ) . "</th><th>" . __( 'Delete', Bootstrap::$text_domain ) . "</th></tr>\n";
        $primary_found = 0;
        $del_url       = add_query_arg( array( 'page' => 'domainmapping', 'action' => 'delete' ), admin_url( $parent_file ) );
        foreach( $domains as $details ) {
          if( 0 == $primary_found && $details[ 'domain' ] == $orig_url[ 'host' ] ) {
            $details[ 'active' ] = 1;
          }
          echo "<tr><td>";
          echo "<input type='radio' name='domain' value='{$details['domain']}' ";
          if( $details[ 'active' ] == 1 )
            echo "checked='1' ";
          echo "/>";
          $url = "{$protocol}{$details['domain']}{$details['path']}";
          echo "</td><td><a href='$url'>$url</a></td><td style='text-align: center'>";
          if( $details[ 'domain' ] != $orig_url[ 'host' ] && $details[ 'active' ] != 1 ) {
            echo "<a href='" . wp_nonce_url( add_query_arg( array( 'domain' => $details[ 'domain' ] ), $del_url ), "delete" . $details[ 'domain' ] ) . "'>Del</a>";
          }
          echo "</td></tr>";
          if( 0 == $primary_found )
            $primary_found = $details[ 'active' ];
        }

        echo "</table>";

        echo '<input type="hidden" name="action" value="primary" />';
        echo "<p><input type='submit' class='button-primary' value='" . __( 'Set Primary Domain', Bootstrap::$text_domain ) . "' /></p>";
        wp_nonce_field( 'domain_mapping' );
        echo "</form>";
        echo "<p>" . __( "* The primary domain cannot be deleted.", Bootstrap::$text_domain ) . "</p>";
        if( get_site_option( 'dm_no_primary_domain' ) == 1 ) {
          echo __( '<strong>Warning!</strong> Primary domains are currently disabled.', Bootstrap::$text_domain );
        }
      }
      echo "<h3>" . __( 'Add new domain', Bootstrap::$text_domain ) . "</h3>";
      echo '<form method="POST">';
      echo '<input type="hidden" name="action" value="add" />';
      echo "<p>http://<input type='text' name='domain' value='' />/<br />";
      wp_nonce_field( 'domain_mapping' );
      echo "<input type='checkbox' name='primary' value='1' /> " . __( 'Primary domain for this blog', Bootstrap::$text_domain ) . "</p>";
      echo "<p><input type='submit' class='button-secondary' value='" . __( "Add", Bootstrap::$text_domain ) . "' /></p>";
      echo "</form><br />";

      if( get_site_option( 'dm_cname' ) ) {
        $dm_cname = get_site_option( 'dm_cname' );
        echo "<p>" . sprintf( __( 'If you want to redirect a domain you will need to add a DNS "CNAME" record pointing to the following domain name for this server: <strong>%s</strong>', Bootstrap::$text_domain ), $dm_cname ) . "</p>";
        echo "<p>" . __( 'Google have published <a href="http://www.google.com/support/blogger/bin/answer.py?hl=en&answer=58317" target="_blank">instructions</a> for creating CNAME records on various hosting platforms such as GoDaddy and others.', Bootstrap::$text_domain ) . "</p>";
      } else {
        echo "<p>" . __( 'If your domain name includes a hostname like "www", "blog" or some other prefix before the actual domain name you will need to add a CNAME for that hostname in your DNS pointing at this blog URL.', Bootstrap::$text_domain ) . "</p>";
        $dm_ipaddress = get_site_option( 'dm_ipaddress', 'IP not set by admin yet.' );
        if( strpos( $dm_ipaddress, ',' ) ) {
          echo "<p>" . sprintf( __( 'If you want to redirect a domain you will need to add DNS "A" records pointing at the IP addresses of this server: <strong>%s</strong>', Bootstrap::$text_domain ), $dm_ipaddress ) . "</p>";
        } else {
          echo "<p>" . sprintf( __( 'If you want to redirect a domain you will need to add a DNS "A" record pointing at the IP address of this server: <strong>%s</strong>', Bootstrap::$text_domain ), $dm_ipaddress ) . "</p>";
        }
      }
      echo '<p>' . sprintf( __( '<strong>Note:</strong> %s', Bootstrap::$text_domain ), static::dm_idn_warning() ) . "</p>";
      echo "</div>";
    }

    public static function domain_siteurl( $setting = null ) {
      global $wpdb, $current_blog;

      // To reduce the number of database queries, save the results the first time we encounter each blog ID.
      static $return_url = array();

      $wpdb->dmtable = $wpdb->base_prefix . 'domain_mapping';

      if( !isset( $return_url[ $wpdb->blogid ] ) ) {
        $s = $wpdb->suppress_errors();

        if( get_site_option( 'dm_no_primary_domain' ) == 1 ) {
          $domain = $wpdb->get_var( "SELECT domain FROM {$wpdb->dmtable} WHERE blog_id = '{$wpdb->blogid}' AND domain = '" . $wpdb->escape( $_SERVER[ 'HTTP_HOST' ] ) . "' LIMIT 1" );
          if( null == $domain ) {
            $return_url[ $wpdb->blogid ] = untrailingslashit( static::get_original_url( "siteurl" ) );

            return $return_url[ $wpdb->blogid ];
          }
        } else {
          // get primary domain, if we don't have one then return original url.
          $domain = $wpdb->get_var( "SELECT domain FROM {$wpdb->dmtable} WHERE blog_id = '{$wpdb->blogid}' AND active = 1 LIMIT 1" );
          if( null == $domain ) {
            $return_url[ $wpdb->blogid ] = untrailingslashit( static::get_original_url( "siteurl" ) );

            return $return_url[ $wpdb->blogid ];
          }
        }

        $wpdb->suppress_errors( $s );
        $protocol = is_ssl() ? 'https://' : 'http://';
        if( $domain ) {
          $return_url[ $wpdb->blogid ] = untrailingslashit( $protocol . $domain );
          $setting                     = $return_url[ $wpdb->blogid ];
        } else {
          $return_url[ $wpdb->blogid ] = false;
        }
      } elseif( $return_url[ $wpdb->blogid ] !== FALSE ) {
        $setting = $return_url[ $wpdb->blogid ];
      }

      return $setting;
    }

    public static function get_original_url( $url, $blog_id = 0 ) {
      global $wpdb;

      if( $blog_id != 0 ) {
        $id = $blog_id;
      } else {
        $id = $wpdb->blogid;
      }

      static $orig_urls = array();
      if( !isset( $orig_urls[ $id ] ) ) {
        if( defined( 'DOMAIN_MAPPING' ) )
          remove_filter( 'pre_option_' . $url, 'domain_mapping_' . $url );
        if( $blog_id == 0 ) {
          $orig_url = get_option( $url );
        } else {
          $orig_url = get_blog_option( $blog_id, $url );
        }
        if( is_ssl() ) {
          $orig_url = str_replace( "http://", "https://", $orig_url );
        } else {
          $orig_url = str_replace( "https://", "http://", $orig_url );
        }
        if( $blog_id == 0 ) {
          $orig_urls[ $wpdb->blogid ] = $orig_url;
        } else {
          $orig_urls[ $blog_id ] = $orig_url;
        }
        if( defined( 'DOMAIN_MAPPING' ) )
          add_filter( 'pre_option_' . $url, 'domain_mapping_' . $url );
      }

      return $orig_urls[ $id ];
    }

    public static function domain_mapping_adminurl( $url, $path, $blog_id = 0 ) {
      $index = strpos( $url, '/wp-admin' );
      if( $index !== false ) {
        $url = static::get_original_url( 'siteurl', $blog_id ) . substr( $url, $index );

        // make sure admin_url is ssl if current page is ssl, or admin ssl is forced
        if( ( is_ssl() || force_ssl_admin() ) && 0 === strpos( $url, 'http://' ) ) {
          $url = 'https://' . substr( $url, 7 );
        }
      }

      return $url;
    }

    public static function domain_mapping_post_content( $post_content ) {
      global $wpdb;

      $orig_url = static::get_original_url( 'siteurl' );

      if( static::domain_siteurl( 'NA' ) == 'NA' ) {
        return $post_content;
      }

      return str_replace( $orig_url, static::domain_siteurl( 'NA' ), $post_content );
    }

    public static function dm_redirect_admin() {
      // don't redirect admin ajax calls
      if( strpos( $_SERVER[ 'REQUEST_URI' ], 'wp-admin/admin-ajax.php' ) !== false )
        return;

      if( get_site_option( 'dm_redirect_admin' ) ) {
        // redirect mapped domain admin page to original url
        if( false === strpos( static::get_original_url( 'siteurl' ), $_SERVER[ 'HTTP_HOST' ] ) ) {
          wp_redirect( untrailingslashit( static::get_original_url( 'siteurl' ) ) . $_SERVER[ 'REQUEST_URI' ] );
          exit;
        }
      } else {
        global $current_blog;
        // redirect original url to primary domain wp-admin/ - remote login is disabled!

        $request_uri = str_replace( $current_blog->path, '/', $_SERVER[ 'REQUEST_URI' ] );
        if( false === strpos( self::domain_siteurl( false ), $_SERVER[ 'HTTP_HOST' ] ) ) {
          wp_redirect( str_replace( '//wp-admin', '/wp-admin', trailingslashit( self::domain_siteurl( false ) ) . $request_uri ) );
          exit;
        }
      }
    }

    public static function redirect_login_to_orig() {
      if( !get_site_option( 'dm_remote_login' ) || $_GET[ 'action' ] == 'logout' || isset( $_GET[ 'loggedout' ] ) ) {
        return false;
      }
      $url = static::get_original_url( 'siteurl' );
      if( $url != site_url() ) {
        $url .= "/wp-login.php";
        echo "<script type='text/javascript'>\nwindow.location = '$url'</script>";
      }
    }

    public static function domain_mapping_plugins_uri( $full_url, $path = NULL, $plugin = NULL ) {
      return get_option( 'siteurl' ) . substr( $full_url, stripos( $full_url, PLUGINDIR ) - 1 );
    }

    public static function domain_mapping_themes_uri( $full_url ) {
      return str_replace( static::get_original_url( 'siteurl' ), get_option( 'siteurl' ), $full_url );
    }

    public static function remote_logout_loader() {
      global $current_site, $current_blog, $wpdb;
      $wpdb->dmtablelogins = $wpdb->base_prefix . 'domain_mapping_logins';
      $protocol            = is_ssl() ? 'https://' : 'http://';
      $hash                = static::get_dm_hash();
      $key                 = md5( time() );
      $wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->dmtablelogins} ( id, user_id, blog_id, t ) VALUES( %s, 0, %d, NOW() )", $key, $current_blog->blog_id ) );
      if( get_site_option( 'dm_redirect_admin' ) ) {
        wp_redirect( $protocol . $current_site->domain . $current_site->path . "?dm={$hash}&action=logout&blogid={$current_blog->blog_id}&k={$key}&t=" . mt_rand() );
        exit;
      }
    }

    public static function redirect_to_mapped_domain() {
      global $current_blog;

      // don't redirect the main site
      if( is_main_site() )
        return;
      // don't redirect post previews
      if( isset( $_GET[ 'preview' ] ) && $_GET[ 'preview' ] == 'true' )
        return;

      // don't redirect theme customizer (WP 3.4)
      if( isset( $_POST[ 'customize' ] ) && isset( $_POST[ 'theme' ] ) && $_POST[ 'customize' ] == 'on' )
        return;

      $url      = self::domain_siteurl( false );
      $protocol = is_ssl() ? 'https://' : 'http://';

      if( $url && $url != untrailingslashit( $protocol . $current_blog->domain . $current_blog->path ) ) {
        $redirect = get_site_option( 'dm_301_redirect' ) ? '301' : '302';
        if( ( defined( 'VHOST' ) && constant( "VHOST" ) != 'yes' ) || ( defined( 'SUBDOMAIN_INSTALL' ) && constant( 'SUBDOMAIN_INSTALL' ) == false ) ) {
          $_SERVER[ 'REQUEST_URI' ] = str_replace( $current_blog->path, '/', $_SERVER[ 'REQUEST_URI' ] );
        }
        header( "Location: {$url}{$_SERVER['REQUEST_URI']}", true, $redirect );
        exit;
      }
    }

    public static function get_dm_hash() {
      $remote_login_hash = get_site_option( 'dm_hash' );
      if( null == $remote_login_hash ) {
        $remote_login_hash = md5( time() );
        update_site_option( 'dm_hash', $remote_login_hash );
      }

      return $remote_login_hash;
    }

    public static function remote_login_js() {
      global $current_blog, $current_user, $wpdb;

      if( 0 == get_site_option( 'dm_remote_login' ) )
        return false;

      $wpdb->dmtablelogins = $wpdb->base_prefix . 'domain_mapping_logins';
      $hash                = static::get_dm_hash();
      $protocol            = is_ssl() ? 'https://' : 'http://';
      if( $_GET[ 'dm' ] == $hash ) {
        if( $_GET[ 'action' ] == 'load' ) {
          if( !is_user_logged_in() )
            exit;
          $key = md5( time() . mt_rand() );
          $wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->dmtablelogins} ( id, user_id, blog_id, t ) VALUES( %s, %d, %d, NOW() )", $key, $current_user->ID, $_GET[ 'blogid' ] ) );
          $url = add_query_arg( array( 'action' => 'login', 'dm' => $hash, 'k' => $key, 't' => mt_rand() ), $_GET[ 'back' ] );
          echo "window.location = '$url'";
          exit;
        } elseif( $_GET[ 'action' ] == 'login' ) {
          if( $details = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->dmtablelogins} WHERE id = %s AND blog_id = %d", $_GET[ 'k' ], $wpdb->blogid ) ) ) {
            if( $details->blog_id == $wpdb->blogid ) {
              $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->dmtablelogins} WHERE id = %s", $_GET[ 'k' ] ) );
              $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->dmtablelogins} WHERE t < %d", ( time() - 120 ) ) ); // remote logins survive for only 2 minutes if not used.
              wp_set_auth_cookie( $details->user_id );
              wp_redirect( remove_query_arg( array( 'dm', 'action', 'k', 't', $protocol . $current_blog->domain . $_SERVER[ 'REQUEST_URI' ] ) ) );
              exit;
            } else {
              wp_die( __( "Incorrect or out of date login key", Bootstrap::$text_domain ) );
            }
          } else {
            wp_die( __( "Unknown login key", Bootstrap::$text_domain ) );
          }
        } elseif( $_GET[ 'action' ] == 'logout' ) {
          if( $details = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->dmtablelogins} WHERE id = %d AND blog_id = %d", $_GET[ 'k' ], $_GET[ 'blogid' ] ) ) ) {
            $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->dmtablelogins} WHERE id = %s", $_GET[ 'k' ] ) );
            $blog = get_blog_details( $_GET[ 'blogid' ] );
            wp_clear_auth_cookie();
            wp_redirect( trailingslashit( $blog->siteurl ) . "wp-login.php?loggedout=true" );
            exit;
          } else {
            wp_die( __( "Unknown logout key", Bootstrap::$text_domain ) );
          }
        }
      }
    }

    public static function remote_login_js_loader() {
      global $current_site, $current_blog;

      if( 0 == get_site_option( 'dm_remote_login' ) || is_user_logged_in() )
        return false;

      $protocol = is_ssl() ? 'https://' : 'http://';
      $hash     = static::get_dm_hash();
      echo "<script src='{$protocol}{$current_site->domain}{$current_site->path}?dm={$hash}&amp;action=load&amp;blogid={$current_blog->blog_id}&amp;siteid={$current_blog->site_id}&amp;t=" . mt_rand() . "&amp;back=" . urlencode( $protocol . $current_blog->domain . $_SERVER[ 'REQUEST_URI' ] ) . "' type='text/javascript'></script>";
    }

    public static function delete_blog_domain_mapping( $blog_id, $drop ) {
      global $wpdb;
      $wpdb->dmtable = $wpdb->base_prefix . 'domain_mapping';
      if( $blog_id && $drop ) {
        // Get an array of domain names to pass onto any delete_blog_domain_mapping actions
        $domains = $wpdb->get_col( $wpdb->prepare( "SELECT domain FROM {$wpdb->dmtable} WHERE blog_id  = %d", $blog_id ) );
        do_action( 'dm_delete_blog_domain_mappings', $domains );

        $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->dmtable} WHERE blog_id  = %d", $blog_id ) );
      }
    }

    public static function ra_domain_mapping_columns( $columns ) {
      $columns[ 'map' ] = __( 'Mapping' );

      return $columns;
    }

    public static function ra_domain_mapping_field( $column, $blog_id ) {
      global $wpdb;
      static $maps = false;

      if( $column == 'map' ) {
        if( $maps === false ) {
          $wpdb->dmtable = $wpdb->base_prefix . 'domain_mapping';
          $work          = $wpdb->get_results( "SELECT blog_id, domain FROM {$wpdb->dmtable} ORDER BY blog_id" );
          $maps          = array();
          if( $work ) {
            foreach( $work as $blog ) {
              $maps[ $blog->blog_id ][ ] = $blog->domain;
            }
          }
        }
        if( !empty( $maps[ $blog_id ] ) && is_array( $maps[ $blog_id ] ) ) {
          foreach( $maps[ $blog_id ] as $blog ) {
            echo $blog . '<br />';
          }
        }
      }
    }

    public static function dm_site_admin() {
      if( function_exists( 'is_super_admin' ) ) {
        return is_super_admin();
      } elseif( function_exists( 'is_site_admin' ) ) {
        return is_site_admin();
      } else {
        return true;
      }
    }

    public static function dm_idn_warning() {
      return sprintf( __( 'International Domain Names should be in <a href="%s">punycode</a> format.', Bootstrap::$text_domain ), "http://api.webnic.cc/idnconversion.html" );
    }

  }

}

