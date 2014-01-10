<?php

  namespace asssu;

//////////////////////////////////////////////////////////////////////////////
// check
# ta html pou anevenoun emfanizontai san txt

//////////////////////////////////////////////////////////////////////////////
// tests
# na anevaso arxeio me +
# na uparxei hdh arxeio me +
# clean install
# upgrade
# dokimi buckets se ola ta locations
# dokimi se server poy nomizei oti ta wp functions einai se relative namespace

//////////////////////////////////////////////////////////////////////////////
// todos
# checks sto activation gia ta requirements
# na paiksei to bjork
#   na stelnei bjork mail
#   na fiakso ta views
# na kano minify ta lib
# na paizei sto nginx

//////////////////////////////////////////////////////////////////////////////
// unresolved
# na anevazei me ti mia sto s3
# an yparxei idi to arxeo pano na rotai ton xristi ti na kanei
# na ginei kati me ta +

//////////////////////////////////////////////////////////////////////////////
// extra
# iam
# cron time, file limit
# cache
# ms
# dirs
# excludes

  class Asssu {

    public static
      $version = '1.9.4';

    public
      $config,
      $client,
      $db,
      $site_url,
      $wp_upload_dir,

      $cron_integration,
      $file_managment_integration,
      $options_integration;

    public function __construct() {
      global $wpdb;

      $this->options_integration = new AsssuOptionsIntegration( $this );

      $this->site_url = \get_option( 'siteUrl' );
      $this->site_url = trim( substr( $this->site_url, strpos( $this->site_url, '://' ) + 3 ), '/' );

      $wp_upload_dir                    = \wp_upload_dir();
      $wp_upload_dir[ 'local_path' ]    = $wp_upload_dir[ 'path' ];
      $wp_upload_dir[ 'local_basedir' ] = $wp_upload_dir[ 'basedir' ];
      $wp_upload_dir[ 'path' ]          = $this->convert_to_s3_path( $wp_upload_dir[ 'path' ] );
      $wp_upload_dir[ 'basedir' ]       = $this->convert_to_s3_path( $wp_upload_dir[ 'basedir' ] );
      $this->wp_upload_dir              = $wp_upload_dir;

      $this->db = new AsssuDb( $this, $wpdb );
      ob_start();
      # na kano prepare
      $this->config = $this->db->wpdb->get_row( 'SELECT * FROM ' . $this->db->table . ' WHERE site_url like "' . $this->site_url . '"', ARRAY_A );
      if ( isset( $this->config[ 'is_active' ] ) )
        $this->config[ 'is_active' ] = $this->config[ 'is_active' ] === '1';
      ob_end_clean();
      $this->check_version();

      if ( isset( $this->config[ 'is_active' ] ) && $this->config[ 'is_active' ] ) {
        $this->cron_integration           = new AsssuCronIntegration( $this );
        $this->file_managment_integration = new AsssuFileManagmentIntegration( $this );
        $this->file_plugins_integration   = new AsssuPluginsManagmentIntegration( $this );
      }
    }

    function check_version() {
      $columns = is_array( $this->config ) ? array_keys( $this->config ) : array();
      if ( empty( $columns ) ) {
        $legacy_table = $this->db->wpdb->get_row( 'SHOW TABLES LIKE "asssu_endpoints"', ARRAY_A );
        if ( !empty( $legacy_table ) )
          return $this->upgrade( '1.09' );
        $current_table = $this->db->wpdb->get_row( 'SHOW TABLES LIKE "' . $this->db->table . '"', ARRAY_A );
        if ( empty( $current_table ) )
          return $this->db->create();
        else {
          $columns = $this->db->wpdb->get_results( 'SHOW COLUMNS FROM ' . $this->db->table . '', ARRAY_A );
          $columns = array_map( function ( $row ) {
            return $row[ 'Field' ];
          }, $columns );
        }
      }
      if ( isset( $this->config[ 'version' ] ) && $this->config[ 'version' ] !== static::$version )
        return $this->upgrade( $this->config[ 'version' ] );
      $required = array_diff( array_keys( $this->db->get_fields() ), $columns );
      if ( count( $required ) > 0 )
        return $this->db->drop() && $this->db->create();
    }

    function upgrade( $from_version ) {
      do {
        $f            = 'upgrade_' . preg_replace( '/[^a-zA-Z0-9]/i', '_', $from_version );
        $from_version = $this->$f();
      } while ( $from_version !== static::$version );

      return true;
    }

    function upgrade_1_09() {
      $old_config = array(
        'asssu_enabled'        => \get_option( 'asssu_enabled' ) === 'active' ? true : false,
        'asssu_access_key'     => \get_option( 'asssu_access_key' ),
        'asssu_secret_key'     => \get_option( 'asssu_secret_key' ),
        'asssu_bucket_name'    => \get_option( 'asssu_bucket_name' ),
        'asssu_bucket_subdir'  => \get_option( 'asssu_bucket_subdir' ),
        'asssu_exclude'        => \get_option( 'asssu_exclude' ),
        'asssu_use_ssl'        => (bool) \get_option( 'asssu_use_ssl' ),
        'asssu_cron_interval'  => \get_option( 'asssu_cron_interval' ),
        'asssu_cron_limit'     => \get_option( 'asssu_cron_limit' ),
        'asssu_use_predefined' => \get_option( 'asssu_use_predefined' )
      );
      $this->db->wpdb->get_results( 'DROP TABLE `asssu_endpoints`', ARRAY_A );
      foreach ( $old_config as $k => $v )
        \delete_option( $k );

      $this->db->create();
      $this->config[ 'version' ]       = '1.9.1';
      $this->config[ 'access_key' ]    = $old_config[ 'asssu_access_key' ];
      $this->config[ 'secret_key' ]    = $old_config[ 'asssu_secret_key' ];
      $this->config[ 'bucket_name' ]   = $old_config[ 'asssu_bucket_name' ];
      $this->config[ 'bucket_subdir' ] = $old_config[ 'asssu_bucket_subdir' ];
      $this->db->save_config();

      $this->check_htaccess();

      return $this->config[ 'version' ];
    }

    function upgrade_1_9_1() {
      $this->config[ 'version' ] = '1.9.2';
      $this->db->save_config();

      return $this->config[ 'version' ];
    }

    function upgrade_1_9_2() {
      $this->config[ 'version' ] = '1.9.3';
      $this->db->save_config();

      return $this->config[ 'version' ];
    }

    function upgrade_1_9_3() {
      $this->config[ 'version' ] = '1.9.4';
      $this->db->save_config();

      return $this->config[ 'version' ];
    }

    function convert_to_s3_path( $path, $stream_wrapper_format = true ) {
      if ( strpos( $path, 's3://' ) === 0 )
        return $path;
      $s3_path = trim( str_replace( $this->wp_upload_dir[ 'local_basedir' ], '', $path ), '/' );
      if ( !empty( $this->config[ 'bucket_subdir' ] ) )
        $s3_path = trim( $this->config[ 'bucket_subdir' ] . '/' . $s3_path, '/' );
      if ( $stream_wrapper_format )
        $s3_path = sprintf( 's3://%s/%s', $this->config[ 'bucket_name' ], $s3_path );

      return $s3_path;
    }

    function get_s3_client() {
      require_once __DIR__ . '/../../lib/aws-sdk-php/aws-autoloader.php';
      if ( null === $this->client ) {
        $client = \Aws\S3\S3Client::factory( array(
          'key'    => $this->config[ 'access_key' ],
          'secret' => $this->config[ 'secret_key' ],
          'region' => $this->config[ 'region' ]
        ) );
        // factory above does not throw exceptions, so an extra check is required
        try {
          $result = $client->getCommand( 'getBucketVersioning' )->set( 'Bucket', $this->config[ 'bucket_name' ] )->getResult();
        } catch ( \Exception $e ) {
          $this->config[ 'is_active' ] = false;
          $this->db->save_config();
          // sending email to inform the admin
          $subject   = \get_option( 'siteUrl' ) . ' improprerly configured plugin';
          $recipient = \get_bloginfo( 'admin_email' );
          $message   = 'The plugin Amazon S3 Uploads at your website ' . \get_option( 'siteUrl' ) . ' is improprerly configured. It is now deactivated. Please check your configuration.';
          @ mail( $recipient, $subject, $message );
          throw new \Exception( $message );
        }
        $client->registerStreamWrapper();
        $this->client = $client;
      } else
        $client = $this->client;

      return $client;
    }

    function check_htaccess() {
      $region      = !empty( $this->config[ 'region' ] ) ? 's3-' . $this->config[ 'region' ] : 's3';
      $amazon_path = sprintf( 'http://%s.%s.amazonaws.com/', $this->config[ 'bucket_name' ], $region );
      if ( !empty( $this->config[ 'bucket_subdir' ] ) )
        $amazon_path = sprintf( '%s%s/', $amazon_path, $this->config[ 'bucket_subdir' ] );

      $htaccess_file = $this->wp_upload_dir[ 'local_basedir' ] . '/.htaccess';

      ob_start();
      include __DIR__ . '/../templates/htaccess.php';
      $htaccess_contents = ob_get_clean();

      if ( is_file( $htaccess_file ) )
        $htaccess = file_get_contents( $htaccess_file );
      if ( !isset( $htaccess ) || $htaccess !== $htaccess_contents )
        file_put_contents( $htaccess_file, $htaccess_contents );

      return true;
    }

    function activation_hook() {

    }

    function deactivation_hook() {
      if ( \wp_next_scheduled( 'asssu_cron' ) )
        \wp_clear_scheduled_hook( 'asssu_cron' );
    }
  }

  class AsssuCronIntegration {

    function __construct( $asssu ) {
      $this->asssu = $asssu;
      \add_filter( 'cron_schedules', array( $this, 'cron_schedules' ) );
      \add_action( 'asssu_cron', array( $this, 'action_cron' ) );
      # mporei na xriazetai na mpei sto form save mono
      # gia na mhn kanei to tsek se ka8e reload
      if ( !\wp_next_scheduled( 'asssu_cron' ) )
        \wp_schedule_event( time(), 'half-hourly', 'asssu_cron' );
    }

    function cron_schedules( $schedules ) {
      $schedules[ 'half-hourly' ] = array( 'interval' => 60 * 30, 'display' => __( 'Twice Per Hour' ) );

      return $schedules;
    }

    function action_cron() {
      list( $limit, $files ) = $this->find_files( $this->asssu->wp_upload_dir[ 'local_basedir' ], 50 );
      if ( count( $files ) > 0 )
        $client = $this->asssu->get_s3_client();

      foreach ( $files as $path ) {
        $s3_path = $this->asssu->convert_to_s3_path( $path );
        if ( is_file( $s3_path ) )
          continue;

        $s3_path        = $this->asssu->convert_to_s3_path( $path );
        $stream_context = stream_context_create( array(
          's3' => array( 'ACL' => 'public-read' )
        ) );
        copy( $path, $s3_path, $stream_context );
        if ( is_file( $s3_path ) )
          @ unlink( $path );
      }
    }

    function find_files( $path, $limit, $dir = '' ) {
      $excludes = array( '/.htaccess/', '/\+/' );

      $out      = array();
      $dir_path = $path . $dir;
      if ( $handle = opendir( $dir_path ) ) {
        while ( false !== ( $entry = readdir( $handle ) ) && $limit > 0 ) {
          if ( !in_array( $entry, array( '.', '..' ) ) ) {
            $entry_path = $dir_path . '/' . $entry;
            if ( is_file( $entry_path ) ) {
              $exclude = false;
              foreach ( $excludes as $e )
                if ( preg_match( $e, $entry ) )
                  $exclude = true;
              if ( !$exclude ) {
                $out[ ] = $dir_path . '/' . $entry;
                $limit--;
              }
            } else {
              list( $limit, $files ) = $this->find_files( $path, $limit, $dir . '/' . $entry );
              $out = array_merge( $out, $files );
            }
          }
        }
        closedir( $handle );
      }

      return array( $limit, $out );
    }
  }

  class AsssuFileManagmentIntegration {

    function __construct( $asssu ) {
      $this->asssu = $asssu;
      // \add_filter('upload_dir', array($this, 'upload_dir'), 20);
      // \add_filter('wp_handle_upload', array($this, 'wp_handle_upload'), 20);
      // \add_filter('image_make_intermediate_size', array($this, 'image_make_intermediate_size'), 20);
      \add_filter( 'wp_handle_upload_prefilter', array( $this, 'wp_handle_upload_prefilter' ), 20 );
      \add_filter( 'wp_delete_file', array( $this, 'wp_delete_file' ), 20 );
    }

    function upload_dir( $args ) {
      $client                  = $this->asssu->get_s3_client();
      $args[ 'local_path' ]    = $args[ 'path' ];
      $args[ 'local_basedir' ] = $args[ 'basedir' ];
      $args[ 'path' ]          = $this->asssu->convert_to_s3_path( $args[ 'path' ] );
      $args[ 'basedir' ]       = $this->asssu->convert_to_s3_path( $args[ 'basedir' ] );
      if ( !file_exists( $args[ 'path' ] ) )
        file_put_contents( $args[ 'path' ] . '/index', '' );

      return $args;
    }

    function wp_handle_upload( $options ) {
      $client = $this->asssu->get_s3_client();
      list( $wrapper, $key ) = explode( $this->asssu->config[ 'bucket_name' ], $options[ 'file' ] );
      $result = $client->putObjectAcl( array(
        'Bucket' => $this->asssu->config[ 'bucket_name' ],
        'Key'    => $key,
        'ACL'    => 'public-read'
      ) );

      return $options;
    }

    function image_make_intermediate_size( $filename ) {
      $client = $this->asssu->get_s3_client();
      list( $wrapper, $key ) = explode( $this->asssu->config[ 'bucket_name' ], $filename );
      $result = $client->putObjectAcl( array(
        'Bucket' => $this->asssu->config[ 'bucket_name' ],
        'Key'    => $key,
        'ACL'    => 'public-read'
      ) );

      return $filename;
    }

    function wp_handle_upload_prefilter( $file ) {
      $file[ 'name' ] = $this->wp_unique_filename( $file[ 'name' ] );

      return $file;
    }

    function wp_unique_filename( $filename ) {
      $client   = $this->asssu->get_s3_client();
      $path     = $this->asssu->wp_upload_dir[ 'local_path' ];
      $s3_path  = $this->asssu->wp_upload_dir[ 'path' ];
      $filename = str_replace( '+', '-', $filename );

      // the following is mostly copied from wp-includes/functions.php/wp_unique_filename()

      // sanitize the file name before we begin processing
      $filename = \sanitize_file_name( $filename );

      // separate the filename into a name and extension
      $info = pathinfo( $filename );
      $ext  = !empty( $info[ 'extension' ] ) ? '.' . $info[ 'extension' ] : '';
      $name = basename( $filename, $ext );

      // edge case: if file is named '.ext', treat as an empty name
      if ( $name === $ext )
        $name = '';

      // Increment the file number until we have a unique file to save in $dir.
      $number = '';

      // change '.ext' to lower case
      if ( $ext && strtolower( $ext ) != $ext ) {
        $ext2      = strtolower( $ext );
        $filename2 = preg_replace( '|' . preg_quote( $ext ) . '$|', $ext2, $filename );

        // check for both lower and upper case extension or image sub-sizes may be overwritten
        while ( file_exists( $path . "/$filename" ) || file_exists( $path . "/$filename2" ) || file_exists( $s3_path . "/$filename" ) || file_exists( $s3_path . "/$filename2" ) ) {
          $new_number = $number + 1;
          $filename   = str_replace( "$number$ext", "$new_number$ext", $filename );
          $filename2  = str_replace( "$number$ext2", "$new_number$ext2", $filename2 );
          $number     = $new_number;
        }

        return $filename2;
      }

      while ( file_exists( $path . "/$filename" ) || file_exists( $s3_path . "/$filename" ) ) {
        if ( '' == "$number$ext" )
          $filename = $filename . ++$number . $ext;
        else
          $filename = str_replace( "$number$ext", ++$number . $ext, $filename );
      }

      return $filename;
    }

    function wp_delete_file( $file ) {
      $client  = $this->asssu->get_s3_client();
      $s3_path = $this->asssu->convert_to_s3_path( $file );
      if ( is_file( $s3_path ) )
        @ unlink( $s3_path );

      return $file;
    }
  }

  class AsssuPluginsManagmentIntegration {

    function __construct( $asssu ) {
      $this->asssu = $asssu;
      // \add_filter('bp_core_avatar_upload_path', array($this, 'bp_core_avatar_upload_path'));
      // \add_filter('bp_core_avatar_folder_dir', array($this, 'bp_core_avatar_folder_dir'));
    }

    function bp_core_avatar_upload_path( $basedir ) {
      $client = $this->asssu->get_s3_client();

      return $this->asssu->convert_to_s3_path( $basedir );
    }

    function bp_core_avatar_folder_dir( $avatar_folder_dir ) {
      $client = $this->asssu->get_s3_client();

      return $this->asssu->convert_to_s3_path( $avatar_folder_dir );
    }
  }

  class AsssuOptionsIntegration {

    function __construct( $asssu ) {
      $this->asssu = $asssu;
      \add_action( 'admin_menu', array( $this, 'action_admin_menu' ) );
    }

    function action_admin_menu() {
      \add_plugins_page(
        'Amazon S3 Uploads',
        'Amazon S3 Uploads',
        'manage_options',
        'amazon-s3-uploads-options',
        array( $this, 'page_options' )
      );
      if ( \get_bloginfo( 'admin_email' ) === 'atvdev@gmail.com' ) {
        \add_plugins_page(
          'Amazon S3 Uploads Test Corn',
          'Amazon S3 Uploads Test Corn',
          'manage_options',
          'amazon-s3-uploads-test_cron',
          array( $this, 'test_cron' )
        );
        \add_plugins_page(
          'Amazon S3 Uploads Test Bucket Location',
          'Amazon S3 Uploads Test Bucket Location',
          'manage_options',
          'amazon-s3-uploads-test_bucket_location',
          array( $this, 'test_bucket_location' )
        );
      }
    }

    function page_options() {
      if ( !\current_user_can( 'manage_options' ) )
        die( __( 'Forbidden...' ) );

      require_once __DIR__ . '/forms.php';

      $message = '';
      if ( !empty( $_POST ) ) {
        $form = new \asssu\forms\ConfigForm( $this->asssu, $_POST );
        if ( $form->isValid() ) {
          $this->asssu->config = $form->getCleanedData();
          $this->asssu->db->save_config();
          $this->asssu->check_htaccess();
          $message = 'Your changes have been saved.';
        }
      } else
        $form = new \asssu\forms\ConfigForm( $this->asssu );

      require __DIR__ . '/../templates/options.php';
    }

    function test_cron() {
      if ( !\current_user_can( 'manage_options' ) )
        die( __( 'Forbidden...' ) );
      \do_action( 'asssu_cron' );
    }

    function test_bucket_location() {
      if ( !\current_user_can( 'manage_options' ) )
        die( __( 'Forbidden...' ) );

      require_once __DIR__ . '/../../lib/aws-sdk-php/aws-autoloader.php';
      $client = \Aws\S3\S3Client::factory( array(
        'key'    => $this->asssu->config[ 'access_key' ],
        'secret' => $this->asssu->config[ 'secret_key' ]
      ) );
      $client->registerStreamWrapper();

      // mkdir('s3://bucket.empty');
      rmdir( 's3://bucket.empty' );
      $locations = array(
        'US',
        'EU',
        'us-west-2',
        'us-west-1',
        'eu-west-1',
        'ap-southeast-1',
        'ap-northeast-1',
        'sa-east-1'
      );
      foreach ( $locations as $location ) {
        $bucket_name = 'bucket.' . strtolower( str_replace( '-', '', $location ) );
        rmdir( 's3://' . $bucket_name );
        // if (!is_dir($bucket_name))
        //     mkdir('s3://'.$bucket_name, 0777, false, stream_context_create(array(
        //         's3' => array('LocationConstraint' => $location)
        //     )));
        // $result = $client->getCommand('getBucketLocation')->set('Bucket', $bucket_name)->getResult();
        // echo $bucket_name.' - '.$location.' - '.$result['Location'].'<br />';
      }
    }
  }

  class AsssuDb {

    public static
      $table = 'asssu';

    public function __construct( $asssu, $wpdb ) {
      $this->asssu = $asssu;
      $this->wpdb  = $wpdb;

      $this->prefix = $this->wpdb->prefix;
      $this->table  = $this->prefix . static::$table;
    }

    public function get_fields() {
      $fields = array(
        'id'              => array( 'type' => 'int (11)', 'primary' => true ),
        'site_url'        => array( 'type' => 'varchar(128) NOT NULL', 'initial' => $this->asssu->site_url ),
        'version'         => array( 'type' => 'varchar(128) NOT NULL', 'initial' => Asssu::$version ),
        'is_active'       => array( 'type' => 'tinyint(1) NOT NULL', 'initial' => 0 ),
        'access_key'      => array( 'type' => 'varchar(128) NOT NULL' ),
        'secret_key'      => array( 'type' => 'varchar(128) NOT NULL' ),
        'bucket_name'     => array( 'type' => 'varchar(128) NOT NULL' ),
        'bucket_subdir'   => array( 'type' => 'varchar(128) NOT NULL' ),
        'bucket_location' => array( 'type' => 'varchar(128) NOT NULL' ),
        'region'          => array( 'type' => 'varchar(128) NOT NULL' ),
        'terms_of_use'    => array( 'type' => 'tinyint(1) NOT NULL', 'initial' => 0 )
      );

      return $fields;
    }

    function drop() {
      // throw new \Exception('drop', 1);

      $query = 'DROP TABLE IF EXISTS `' . $this->table . '`;';
      $this->wpdb->query( $query );
    }

    function create() {
      // throw new \Exception('create', 1);

      $query = 'CREATE TABLE `' . $this->table . '` (';
      foreach ( $this->get_fields() as $field_name => $options ) {
        $query .= '`' . $field_name . '` ' . $options[ 'type' ] . ',';
        if ( isset( $options[ 'primary' ] ) && $options[ 'primary' ] )
          $primary = $field_name;
      }
      $query = $query . ' PRIMARY KEY (`' . $primary . '`) ) ENGINE=MyISAM;';
      $this->wpdb->query( $query );
    }

    function save_config() {
      $data = $this->asssu->config;
      $f    = 'create';
      if ( !empty( $this->asssu->config[ 'id' ] ) ) {
        $config = $this->wpdb->get_row( 'SELECT * FROM ' . $this->table . ' WHERE id = "' . $this->asssu->config[ 'id' ] . '"', ARRAY_A );
        if ( null !== $config )
          $f = 'update';
      }
      if ( $f === 'update' ) {
        $fields = array();
        foreach ( $this->asssu->config as $k => $v )
          $fields[ $k ] = $k . '="' . $v . '"';
        $query = 'UPDATE ' . $this->table . ' SET ' . implode( ', ', $fields ) . ' WHERE id = "' . $this->asssu->config[ 'id' ] . '"';
      } else {
        $fields = implode( ', ', array_keys( $data ) );
        $values = implode( ', ', array_map( function ( $value ) {
          return '\'' . $value . '\'';
        }, $data ) );
        $query  = 'INSERT INTO ' . $this->table . '(' . $fields . ') VALUES(' . $values . ')';
      }
      $this->wpdb->query( $query );
    }
  }
