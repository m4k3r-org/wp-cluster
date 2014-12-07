<?php
/**
 * CLI
 *
 * @author potanin@UD
 * @class UsabilityDynamics\Cluster\CLI
 */
namespace UsabilityDynamics\Cluster {

  if (!class_exists('UsabilityDynamics\Cluster\CLI') && class_exists('WP_CLI_Command')) {

    /**
     * Manager WordPress cluster.
     *
     * @module Cluster
     */
    class CLI extends \WP_CLI_Command
    {

      /**
       * Test stuff.
       *
       * ## OPTIONS
       *
       * <stage>
       * : Which migration stage we want to do, defaults to all
       *
       * ## EXAMPLES
       *
       *     wp cluster test
       *     wp cluster test all
       *
       * @synopsis [<stage>]
       * @param $args
       */
      function test($args)
      {
        $this->_init();
        $type = false;

        \WP_CLI::line('DB_NAME: ' . DB_NAME);
        \WP_CLI::line('DB_USER: ' . DB_USER);
        \WP_CLI::line('DB_HOST: ' . DB_HOST);

      }

      /**
       * Join Cluster.
       *
       * ## OPTIONS
       *
       * <branch>
       * : Which branch this is.
       *
       * ## EXAMPLES
       *
       *     wp cluster join
       *     wp cluster join production
       *     wp cluster join develop
       *
       * @synopsis [<stage>]
       * @param $args
       */
      function join($args)
      {
        $this->_init();
        $type = false;

        \WP_CLI::line('DB_NAME: ' . DB_NAME);
        \WP_CLI::line('DB_USER: ' . DB_USER);
        \WP_CLI::line('DB_HOST: ' . DB_HOST);

        die('<pre>' . print_r($args, true) . '</pre>');

      }

      /**
       * Display active themes accross the network including relative path.
       *
       * ## OPTIONS
       *
       * <stage>
       * : Which migration stage we want to do, defaults to all
       *
       * ## EXAMPLES
       *
       *     wp cluster themes
       *
       * @synopsis [<stage>]
       */
      function themes($args)
      {
        global $wpdb, $current_blog;

        //WP_CLI::line( 'DB_NAME: ' . DB_NAME );
        //WP_CLI::line( 'DB_USER: ' . DB_USER );
        //WP_CLI::line( 'DB_HOST: ' . DB_HOST );

        // WP_CLI::line( 'Generating list of sites with themes.' );

        $_results = array();

        foreach ((array)wp_get_sites(array('public' => true, 'network_id' => null)) as $site) {

          switch_to_blog($site['blog_id']);

          $_template = wp_get_theme(get_option('template'));
          $_stylesheet = wp_get_theme(get_option('stylesheet'));
          $_status = array();

          $_templateActual = get_option('stylesheet') !== get_option('template') ? get_option('template') : null;

          if ($_templateActual && !is_dir($_template->get_stylesheet_directory())) {
            $_status[] = 'Template missing.';
          }

          if (!is_dir($_stylesheet->get_stylesheet_directory())) {
            $_status[] = 'Theme missing.';
          }

          $_network = (array)wp_get_network($site['site_id']);

          $_results[$site['domain']] = array(
            'id' => $site['blog_id'],
            'network' => $_network['domain'],
            'site' => $site['domain'],
            'theme' => get_option('stylesheet'), // . ' ' . $_stylesheet->get( 'Version' ),
            'template' => $_templateActual,
            'path' => str_replace(getcwd(), '.', $_stylesheet->get_stylesheet_directory()),
            'status' => join(' ', $_status)
          );

        }

        \WP_CLI\Utils\format_items('table', $_results, array('id', 'network', 'site', 'theme', 'path', 'template', 'status'));

      }

      /**
       * Display active themes accross the network including relative path.
       *
       * ## OPTIONS
       *
       * <stage>
       * : Which migration stage we want to do, defaults to all
       *
       * ## EXAMPLES
       *
       *     wp cluster sites
       *
       * @synopsis [<stage>]
       */
      function sites($args)
      {
        global $wpdb, $current_blog;

        // WP_CLI::line( 'Generating list of sites with themes.' );

        $_results = array();

        foreach ((array)wp_get_sites(array('network_id' => null)) as $site) {

          switch_to_blog($site['blog_id']);

          $_template = wp_get_theme(get_option('template'));
          $_stylesheet = wp_get_theme(get_option('stylesheet'));
          $_status = '';
          $_a_records = array();

          $_templateActual = get_option('stylesheet') !== get_option('template') ? get_option('template') : null;

          if ($_templateActual && !is_dir($_template->get_stylesheet_directory())) {
            $_status[] = 'Template missing.';
          }

          if (!is_dir($_stylesheet->get_stylesheet_directory())) {
            $_status[] = 'Theme missing.';
          }

          $_network = (array)wp_get_network($site['site_id']);

          foreach ((array)dns_get_record($site['domain'], DNS_A) as $_record) {
            $_a_records[] = $_record['ip'];
          }

          if (ms_is_switched()) {
          }

          $_status = $site['public'] ? 'Public' : null;

          $_results[$site['domain']] = array(
            'id' => $site['blog_id'],
            'domain' => $site['domain'],
            'network' => $_network['domain'],
            'url' => get_blog_option($site['blog_id'], 'home'),
            'ip' => gethostbyname($site['domain']),
            'dns' => join(", ", $_a_records),
            'status' => $_status,
            'globalTerms' => global_terms_enabled(),
            'mainSite' => is_main_site(),
            'mainNetwork' => is_main_network(),
          );

        }

        \WP_CLI\Utils\format_items('table', $_results, array('id', 'domain', 'network', 'url', 'ip', 'dns', 'status'));

      }

      /**
       * Display active themes accross the network including relative path.
       *
       * ## OPTIONS
       *
       * <stage>
       * : Which migration stage we want to do, defaults to all
       *
       * ## EXAMPLES
       *
       *     wp cluster sites
       *
       * @synopsis [<stage>]
       */
      function media($args)
      {
        global $wpdb, $current_blog;

        // WP_CLI::line( 'Generating list of sites with themes.' );

        $_results = array();

        foreach ((array)wp_get_sites(array('network_id' => null)) as $site) {

          switch_to_blog($site['blog_id']);

          $_template = wp_get_theme(get_option('template'));
          $_stylesheet = wp_get_theme(get_option('stylesheet'));
          $_status = '';

          $_templateActual = get_option('stylesheet') !== get_option('template') ? get_option('template') : null;

          if ($_templateActual && !is_dir($_template->get_stylesheet_directory())) {
            $_status[] = 'Template missing.';
          }

          if (!is_dir($_stylesheet->get_stylesheet_directory())) {
            $_status[] = 'Theme missing.';
          }

          if (defined('WP_CLI')) {
            //die( '<pre>' . print_r( get_current_site(), true ) . '</pre>');
            //die( '<pre>' . print_r( wp_upload_dir(), true ) . '</pre>');
          }

          if (class_exists('UsabilityDynamics\Veneer\Bootstrap')) {
            //die(WP_VENEER_STORAGE);
            //global $wp_veneer;
            //die( '<pre>' . print_r( $wp_veneer->get(), true ) . '</pre>');
            //die( 'have:' . current_action() );

          }
          //$_wp_upload_dir = wp_upload_dir();
          //die( '<pre>' . print_r( $_wp_upload_dir , true ) . '</pre>');

          $_network = (array)wp_get_network($site['site_id']);
          $_path = wp_normalize_path(trailingslashit(WP_CONTENT_DIR) . get_option('upload_path'));

          $_results[$site['domain']] = array(
            'id' => $site['blog_id'],
            'network' => $_network['domain'],
            'domain' => $site['domain'],
            'path' => $_path,
            'url' => get_option('upload_url_path'),
            'size' => is_dir($_path) ? format_size(foldersize($_path)) : '-'
          );

        }

        \WP_CLI\Utils\format_items('table', $_results, array('id', 'network', 'domain', 'path', 'url', 'size'));

        //die( '<pre>' . print_r( $_results, true ) . '</pre>');

      }

      /**
       * Test stuff.
       *
       * ## OPTIONS
       *
       * <stage>
       * : Which migration stage we want to do, defaults to all
       *
       * ## EXAMPLES
       *
       *     wp cluster status
       *
       * @synopsis [<stage>]
       */
      function status()
      {
        global $wp_version, $wp_db_version, $wpdb, $wp_veneer, $wp_cluster;

        $_results = array(
          "wp.version" => $wp_version,
          "db.version" => $wp_db_version,
          "db.name" => DB_NAME,
          "db.user" => DB_USER,
          "db.host" => DB_HOST,
          "wp.cluster" => method_exists($wp_veneer, 'get') ? true : false,
          "wp.veneer" => class_exists('UsabilityDynamics\Cluster\WPDB') ? true : false,
          "wpdb.ready" => $wpdb->ready,
          "wpdb.blogid" => $wpdb->blogid,
          "wpdb.siteid" => $wpdb->siteid,
          "db.password" => str_repeat('*', strlen(DB_PASSWORD)),
          "os.hostname" => php_uname('n'),
          //"os.type" =>  php_uname('s') ,
          //"os.version" =>  php_uname('v') ,
          //"os.release" =>  php_uname('r') ,
          // "os.machine" =>  php_uname('m')
        );

        \WP_CLI::log('Cloud Status:');

        die('<pre>' . print_r($_results, true) . '</pre>');

      }

      /**
       * Attempts the migration
       *
       * ## OPTIONS
       *
       * <stage>
       * : Which migration stage we want to do, defaults to all
       *
       * ## EXAMPLES
       *
       *     wp cluster migrate
       *     wp cluster migrate artist
       *
       * @synopsis [<stage>]
       * @param $args
       */
      function migrate($args)
      {
        $this->_init();
        $type = false;

        if (isset($args) && is_array($args) && count($args)) {
          $type = array_shift($args);
        }

      }

      /**
       * Backup entire WordPress install, including core, plugins and database.
       *
       * @subcommand backup
       *
       * @alias mv
       *
       * @param array $args
       * @param array $assoc_args
       * @synopsis [backup_filename] [--no-db] [--db-name=<filename>]
       */
      function backup( $args, $assoc_args ) {
        $filename         = $dbname = null;
        $backup_directory = '../';

        // If a filename isn't specified, default to "Site's Title.tar.gz".
        if ( empty( $args ) ) {
          $filename = $backup_directory . get_bloginfo() . '.tar.gz';
        } else {
          $filename = $args[ 0 ];
        }

        // If --no-db is specified, don't include the database in backup
        if ( ! isset( $assoc_args[ 'no-db' ] ) ) {
          $dbname = isset( $assoc_args[ 'db-name' ] ) ? $assoc_args[ 'db-name' ] : 'database_backup.sql';

          WP_CLI::run_command( array( 'db', 'export', $backup_directory . $dbname ), array() );

        }

        // Using esc_cmd to automatically escape parameters.
        // We can't use --exclude-vcs, because it's not available on OSX.
        WP_CLI::line( "Backing up to $filename ..." );
        $result = \WP_CLI::launch( \WP_CLIUtilsesc_cmd( "
				tar
					--exclude '.git'
					--exclude '.svn'
					--exclude '.hg'
					--exclude '.bzr'
					-czf %s . -C %s %s
			", $filename, $backup_directory, $dbname ), false );

        // If we created a database backup, remove the temp file.
        if ( $dbname && ! unlink( $backup_directory . $dbname ) ) {
          WP_CLI::warning( "Couldn't remove temporary database backup, '$dbname'." );
        }

        if ( 0 == $result ) {
          WP_CLI::success( "Backup Complete." );
        } else {
          WP_CLI::error( "Backup Failed." );
        }
      }

      /**
       * Setup our limits
       *
       */
      private function _init()
      {
        set_time_limit(0);
        ini_set('memory_limit', '2G');
      }

    }

    function foldersize($path) {
      $total_size = 0;

      if( !$path ) {
        return null;
      }

      $files = scandir($path);
      $cleanPath = rtrim($path, '/'). '/';

      foreach( (array) $files as $t) {
        if ($t<>"." && $t<>"..") {
          $currentFile = $cleanPath . $t;
          if (is_dir($currentFile)) {
            $size = foldersize($currentFile);
            $total_size += $size;
          }
          else {
            $size = filesize($currentFile);
            $total_size += $size;
          }
        }
      }

      return $total_size;
    }

    function format_size($size) {
      global $units;

      $mod = 1024;

      for ($i = 0; $size > $mod; $i++) {
        $size /= $mod;
      }

      $endIndex = strpos($size, ".")+3;

      return substr( $size, 0, $endIndex).' '.$units[$i];
    }

  }

}