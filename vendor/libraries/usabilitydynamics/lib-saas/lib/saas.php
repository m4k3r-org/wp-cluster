<?php
/**
 * Usability Dynamics SaaS Library
 *
 * @version 0.1.2
 * @module UsabilityDynamics
 */
namespace UsabilityDynamics {

  if( !class_exists( 'UsabilityDynamics\SaaS' ) ) {

    /**
     * SaaS Functions
     *
     * - UD_API_Key / ud::api_key
     * - UD_Site_UID / ud::site_uid
     * - UD_Public_Key / ud::public_key
     * - UD_Customer_Key / ud::customer_key
     *
     * @author team@UD
     * @version 0.1.2
     *
     * @class SaaS
     * @module SaaS
     * @extends Utility
     */
    class SaaS extends Utility {

      /**
       * SaaS Class version.
       *
       * @public
       * @static
       * @property $version
       * @type {Object}
       */
      public static $version = '0.1.2';

      /**
       * Returns one of several keys. Different keys are used for different things.
       *
       * api - key for API requests to site
       *
       * site_uid - site ( blog ) unique ID. Only set your site UID if you know what you're doing. The key should only be used on one website at a time to avoid conflicts.
       * For instance, if you have a staging and a production environment with synchronized databases, you can use the same Site UID on both sites.
       *
       * public_key - Public key is used on the front-end of the site to access data that requires a subscription. The public key is restricted to specified IP addresses
       * and and is therefore safe for front-end use. The public key is provided by the UD API service after the site requesting it has been verified. This key is required to access
       * most restricted and premium functionality.
       *
       * customer_key - private keys issued to individuals directly, may be used on multiple sites and in most cases can take place of a missing site_uid. Never shown publicly
       * in clear text. It can be used in md5 format when a public_key does not exist, or has been rejected.
       *
       * @updated 2.0
       * @author potanin@UD
       */
      static public function get_key( $type = 'api', $args = false ) {

        $args = wp_parse_args( $args, array(
          'force_check' => false
        ) );

        $key = false;

        switch( $type ) {

          /**
           * API Keys must be manually entered into the Settings UI or it can be set as define in wp-config.php
           *
           */
          case 'api_key':
            if( !defined( 'UD_API_Key' ) ) define( 'UD_API_Key', get_option( 'ud::api_key' ) );
            $key = UD_API_Key;
            break;

          /**
           * Site UID must be manually generated into the Settings UI or it can be set as define in wp-config.php
           */
          case 'site_uid':
            if( !defined( 'UD_Site_UID' ) ) define( 'UD_Site_UID', get_option( 'ud::site_uid' ) );
            $key = UD_Site_UID;
            break;

          /**
           * Requires site verification.
           */
          case 'public_key':
            if( !defined( 'UD_Public_Key' ) ) define( 'UD_Public_Key', get_option( 'ud::public_key' ) );
            $key = UD_Public_Key;
            break;

          /**
           * Customer Keys must be manually entered into the Settings UI. Customer Keys are given out during purchases, to beta testers,
           * and in other non-automated situations.
           */
          case 'customer_key':
            if( is_multisite() ) {
              if( !defined( 'UD_Customer_Key' ) ) {
                /**
                 * Customer key must be the same for all blogs
                 * It's stored in wp_options of blog #1.
                 */
                switch_to_blog( 1 );
                define( 'UD_Customer_Key', get_option( 'ud::customer_key' ) );
                restore_current_blog();
              }
            } else {
              if( !defined( 'UD_Customer_Key' ) ) define( 'UD_Customer_Key', get_option( 'ud::customer_key' ) );
            }
            $key = UD_Customer_Key;
            break;

        }

        return $key;

      }

      /**
       * Determines if SaaS settings are available
       *
       * @author peshkov@UD
       * @return boolean
       */
      static public function is_saas_cap_available() {
        $result = !is_multisite() || is_super_admin() ? true : false;
        $result = apply_filters( 'ud::saas_cap_available', $result );

        return $result;
      }

      /**
       * Handler for general API calls to UD
       *
       * On Errors, the data response includes request URL, request body, and response headers / body.
       *
       * @method get_service
       * @for SaaS
       *
       * @updated 1.0.3
       * @since 1.0.0
       * @author potanin@UD
       */
      static public function get_service( $service = false, $resource = '', $args = array(), $settings = array() ) {

        if( $_query = parse_url( $service, PHP_URL_QUERY ) ) {
          $service = str_replace( '?' . $_query, '', $service );
        }

        if( !$service ) {
          return new WP_Error( 'error', sprintf( __( 'API service not specified.', UD_Transdomain ) ) );
        }

        $request = array_filter( wp_parse_args( $settings, array(
          'headers'   => array(
            'Authorization' => 'Basic ' . base64_encode( 'api_key:' . get_option( 'ud::customer_key' ) ),
            'Accept'        => 'application/json'
          ),
          'timeout'   => 120,
          'stream'    => false,
          'sslverify' => false,
          'source'    => ( is_ssl() ? 'https' : 'http' ) . '://saas.usabilitydynamics.com',
        ) ) );

        foreach( (array) $settings as $set ) {

          switch( $set ) {

            case 'json':
              $request[ 'headers' ][ 'Accept' ] = 'application/json';
              break;

            case 'encrypted':
              $request[ 'headers' ][ 'Encryption' ] = 'Enabled';
              break;

            case 'xml':
              $request[ 'headers' ][ 'Accept' ] = 'application/xml';
              break;

          }

        }

        if( !empty( $request[ 'filename' ] ) && file_exists( $request[ 'filename' ] ) ) {
          $request[ 'stream' ] = true;
        }

        $request_url = trailingslashit( $request[ 'source' ] );
        unset( $request[ 'source' ] );

        if( $settings[ 'method' ] == 'POST' ) {
          $response = wp_remote_post( $request_url = $request_url . $service . '/' . $resource, array_merge( $request, array( 'body' => $args ) ) );
        } else {
          $response = wp_remote_get( $request_url = $request_url . $service . '/' . $resource . ( is_array( $args ) ? '?' . _http_build_query( $args, null, '&' ) : $args ), $request );
        }

        if( !is_wp_error( $response ) ) {

          /** If content is streamed, must rely on message codes */
          if( $request[ 'stream' ] ) {

            switch( $response[ 'response' ][ 'code' ] ) {

              case 200:
                return true;
                break;

              default:
                unlink( $request[ 'filename' ] );

                return false;
                break;
            }

          }

          switch( true ) {

            /* |Disabled until issue with RETS API is not resolved| case ( intval( $response[ 'headers' ][ 'content-length' ] ) === 0 ):
              return new WP_Error( 'self::ger_service' , __( 'API did not send back a valid response.' ), array(
                'request_url' => $request_url,
                'request_body' => $request,
                'headers' => $response[ 'headers' ],
                'body' => $response[ 'body' ]
              ));
            break;*/

            case ( $response[ 'response' ][ 'code' ] == 404 ):
              return new WP_Error( 'ud_api', __( 'API Not Responding. Please contact support.' ), array(
                'request_url'  => $request_url,
                'request_body' => $request,
                'headers'      => $response[ 'headers' ]
              ) );
              break;

            case ( strpos( $response[ 'headers' ][ 'content-type' ], 'text/html' ) !== false ):
              return $response[ 'body' ];
              break;

            case ( strpos( $response[ 'headers' ][ 'content-type' ], 'application/json' ) !== false ):
              $json = @json_decode( $response[ 'body' ] );
              if( !is_object( $json ) ) return new WP_Error( 'UD_Functions::get_service', __( 'An unknown error occurred while trying to make an API request to Usability Dynamics. Please contact support', 'wpp' ), array( 'response' => $response[ 'body' ] ) );

              return $json->success === false ? new WP_Error( 'UD_Functions::get_service', $json->message, $json->data ) : $json;
              break;

            case ( strpos( $response[ 'headers' ][ 'content-type' ], 'application/xml' ) !== false ):
              return $response[ 'body' ];
              break;

            default:
              return new WP_Error( 'ud_api', __( 'An unknown error occurred while trying to make an API request to Usability Dynamics. Please contact support.', 'wpp' ) );
              break;

          }

        } else {
          if( !empty( $request[ 'filename' ] ) && is_file( $request[ 'filename' ] ) ) {
            unlink( $request[ 'filename' ] );
          }

          return $response;
        }

      }

      /**
       * Returns location information from Google Maps API call.
       *
       * From version 1.2.0, the geohash is generated automatically.
       *
       * @version 1.2.0
       * @since 1.0.0
       *
       * @param bool   $address
       * @param string $localization
       * @param bool   $return_obj_on_fail
       * @param bool   $latlng
       *
       * @return object
       */
      static public function geo_locate_address( $address = false, $localization = "en", $return_obj_on_fail = false, $latlng = false ) {

        if( !$address && !$latlng ) {
          return false;
        }

        if( is_array( $address ) ) {
          return false;
        }

        $address = urlencode( $address );

        $url = str_replace( ' ', '+', "http://maps.google.com/maps/api/geocode/json?" . ( ( is_array( $latlng ) ) ? "latlng={$latlng['lat']},{$latlng['lng']}" : "address={$address}" ) . "&sensor=true&language={$localization}" );

        //** check if we have waited enough time
        $last_error = get_option( 'ud::geo_locate_address_last_OVER_QUERY_LIMIT' );

        if( self::available_address_validation() ) {
          $obj = ( json_decode( wp_remote_fopen( $url ) ) );
        } else {
          $obj          = new stdClass();
          $obj->status  = 'OVER_QUERY_LIMIT';
          $obj->induced = true;
        }

        if( $obj->status != "OK" ) {

          if( empty( $obj->induced ) && $obj->status == 'OVER_QUERY_LIMIT' ) {
            self::available_address_validation( true );
          }

          // Return Google result if needed instead of just false
          if( $return_obj_on_fail ) {
            return $obj;
          }

          return false;

        }

        $results        = $obj->results;
        $results_object = $results[ 0 ];
        $geometry       = $results_object->geometry;

        $return = new stdClass();

        $return->formatted_address = $results_object->formatted_address;
        $return->latitude          = $geometry->location->lat;
        $return->longitude         = $geometry->location->lng;
        $return->location_type     = $geometry->location_type;

        // Cycle through address component objects picking out the needed elements, if they exist
        foreach( (array) $results_object->address_components as $ac ) {

          // types is returned as an array, look through all of them
          foreach( (array) $ac->types as $type ) {
            switch( $type ) {

              case 'street_number':
                $return->street_number = $ac->long_name;
                break;

              case 'route':
                $return->route = $ac->long_name;
                break;

              case 'locality':
                $return->city = $ac->long_name;
                break;

              case 'administrative_area_level_3':
                if( empty( $return->city ) )
                  $return->city = $ac->long_name;
                break;

              case 'administrative_area_level_2':
                $return->county = $ac->long_name;
                break;

              case 'administrative_area_level_1':
                $return->state      = $ac->long_name;
                $return->state_code = $ac->short_name;
                break;

              case 'country':
                $return->country      = $ac->long_name;
                $return->country_code = $ac->short_name;
                break;

              case 'postal_code':
                $return->postal_code = $ac->long_name;
                break;

              case 'sublocality':
                $return->district = $ac->long_name;
                break;

            }
          }
        }

        $_table = "0123456789bcdefghjkmnpqrstuvwxyz";

        $lap               = strlen( $return->latitude ) - strpos( $return->latitude, "." );
        $lop               = strlen( $return->longitude ) - strpos( $return->longitude, "." );
        $return->precision = pow( 10, -max( $lap - 1, $lop - 1, 0 ) ) / 2;
        $return->geo_hash  = "";

        $minlat = -90;
        $maxlat = 90;
        $minlng = -180;
        $maxlng = 180;
        $latE   = 90;
        $lngE   = 180;
        $i      = 0;
        $error  = 180;

        while( $error >= $return->precision ) {
          $chr = 0;
          for( $b = 4; $b >= 0; --$b ) {
            if( ( 1 & $b ) == ( 1 & $i ) ) { // even char, even bit OR odd char, odd bit...a lng
              $next = ( $minlng + $maxlng ) / 2;
              if( $lng > $next ) {
                $chr |= pow( 2, $b );
                $minlng = $next;
              } else {
                $maxlng = $next;
              }
              $lngE /= 2;
            } else { // odd char, even bit OR even char, odd bit...a lat
              $next = ( $minlat + $maxlat ) / 2;
              if( $lat > $next ) {
                $chr |= pow( 2, $b );
                $minlat = $next;
              } else {
                $maxlat = $next;
              }
              $latE /= 2;
            }
          }
          $return->geo_hash .= $_table[ $chr ];
          $i++;
          $error = min( $latE, $lngE );
        }

        //** API Callback */
        $return = apply_filters( 'ud::geo_locate_address', $return, $results_object, $address, $localization );

        //** API Callback (Legacy) - If no actions have been registered for the new hook, we support the old one. */
        if( !has_action( 'ud::geo_locate_address' ) ) {
          $return = apply_filters( 'geo_locate_address', $return, $results_object, $address, $localization );
        }

        return $return;

      }

      /**
       * Address Validation
       *
       * @param bool $update
       *
       * @return bool
       */
      static public function available_address_validation( $update = false ) {

        if( empty( $update ) ) {

          $last_error = (int) get_option( 'ud::geo_locate_address_last_OVER_QUERY_LIMIT' );
          if( !empty( $last_error ) && ( time() - (int) $last_error ) < 2 ) {
            sleep( 1 );
          }

        } else {
          update_option( 'ud::geo_locate_address_last_OVER_QUERY_LIMIT', time() );

          return false;
        }

        return true;
      }

      /**
       * Return useful information about the current server.
       *
       *
       * Migrated from UsabilityDynamiocs\Utility.
       *
       * @method client_instance
       * @for SaaS
       *
       * @since 0.1.1
       * @author potanin@UD
       */
      static public function get_server_capabilities() {

        $return = array(
          'success'            => true,
          'server_name'        => $_SERVER[ 'SERVER_ADDR' ],
          'server_address'     => $_SERVER[ 'REMOTE_ADDR' ],
          'supported_encoding' => explode( ',', $_SERVER[ 'HTTP_ACCEPT_ENCODING' ] ),
          'server_name'        => $_SERVER[ 'SERVER_ADDR' ],
          'memory_usage'       => memory_get_usage(),
          'wordpress'          => array(
            'language'     => defined( 'WPLANG' ) ? WPLANG : null,
            'memory_limit' => defined( 'WP_MEMORY_LIMIT' ) ? WP_MEMORY_LIMIT : null,
            'charset'      => get_bloginfo( 'charset' ),
            'language'     => get_bloginfo( 'language' ),
            'charset'      => get_bloginfo( 'charset' ),
            'site'         => site_url(),
            'home'         => home_url()
          )
        );

        if( function_exists( 'ini_get_all' ) ) {
          $return[ 'config' ] = ini_get_all( null, false );
        }

        if( function_exists( 'get_loaded_extensions' ) ) {
          $return[ 'curl' ] = in_array( 'curl', get_loaded_extensions() ) ? true : false;
        }

        if( version_compare( PHP_VERSION, '5.3.0' ) >= 0 ) {
          $return[ 'xpath_php_support' ] = true;
        }

        return self::array_filter_deep( $return );
      }

      /**
       * Standard Instance
       *
       * Migrated from UsabilityDynamiocs\Utility.
       *
       * @method client_instance
       * @for SaaS
       *
       * @since 0.1.1
       * @author potanin@UD
       */
      static public function client_instance() {

        return array_filter( array(
          'api_key'  => get_option( 'ud::api_key' ),
          'key'      => get_option( 'ud::public_key' ) ? get_option( 'ud::public_key' ) : md5( get_option( 'ud::customer_key' ) ),
          'site_uid' => get_option( 'ud::site_uid' ),
          'home'     => home_url(),
          'ajax'     => admin_url( 'wp-ajax.php' ),
          'ip'       => $_SERVER[ 'SERVER_ADDR' ]
        ) );

      }

    }
  }

}