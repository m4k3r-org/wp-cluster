<?php
/**
 * Plugin Name: WP-Social-Stream
 * Text Domain: wp-social-stream
 * Description: Social Stream Plugin
 * Author: Usability Dynamics, Inc
 * Version: 1.0.0
 * Author URI: http://UsabilityDynamics.com
 */
if (! class_exists('WP_Social_Stream') ) {
  class WP_Social_Stream {

    /**
     * Construct
     */
    public function __construct(){
      /** Add our init action */
      add_action( 'init', array( $this, 'init' ) );
    }
    
    /** 
     * Our init function, where we should add all actions
     */
    function init(){
      /** Add our ajax actions for Twitter */
      add_action( 'wp_ajax_nopriv_social_stream_twitter', array( $this, 'social_stream_twitter' ) );
      add_action( 'wp_ajax_social_stream_twitter', array( $this, 'social_stream_twitter' ) );
      
      /** Add our moderation action */
      if ( current_user_can('manage_options') ) {
        add_action( 'wp_ajax_social_stream_moderate', array( $this, 'social_stream_moderate' ) );
      }

      /** Add our shortcode */
      add_shortcode( 'wp_social_stream', array( $this, 'social_stream_shortcode' ) );
    }

    /**
     * Ajax moderation
     */
    function social_stream_moderate() {
      if ( !current_user_can('manage_options') ) die('Permission Denied');

      $current = get_option( 'social_stream_hidden' );

      $current[] = $_POST['item'];

      die( update_option( 'social_stream_hidden', $current ) );
    }

    /**
     * Shortcode implementation for social stream
     * @param type $attrs
     * @return type
     */
    function social_stream_shortcode( $attrs ) {
    
      $defaults = array(
        'requires' => plugins_url( '/' . basename( __DIR__ ) . '/static/scripts/jquery.social.stream.1.5.5.custom.js', __DIR__ ),
        'path' => get_stylesheet_directory_uri(),
        'wall' => 'true',
        'rotate_delay' => '',
        'rotate_direction' => 'up',
        'height' => '',
        'limit' => '50',

        'twitter_search_for' => '',
        'twitter_show_text' => 'text',

        'twitter_consumer_key' => defined( 'WP_SOCIAL_STREAM_TWITTER_CONSUMER_KEY' ) ? WP_SOCIAL_STREAM_TWITTER_CONSUMER_KEY : false,
        'twitter_consumer_secret' => defined( 'WP_SOCIAL_STREAM_TWITTER_CONSUMER_SECRET' ) ? WP_SOCIAL_STREAM_TWITTER_CONSUMER_SECRET : false,
        'twitter_access_token' => defined( 'WP_SOCIAL_STREAM_TWITTER_ACCESS_TOKEN' ) ? WP_SOCIAL_STREAM_TWITTER_ACCESS_TOKEN : false,
        'twitter_access_token_secret' => defined( 'WP_SOCIAL_STREAM_TWITTER_ACCESS_TOKEN_SECRET' ) ? WP_SOCIAL_STREAM_TWITTER_ACCESS_TOKEN_SECRET : false,

        'instagram_search_for' => '',
        'instagram_client_id' => defined( 'WP_SOCIAL_STREAM_INSTAGRAM_CLIENT_ID' ) ? WP_SOCIAL_STREAM_INSTAGRAM_CLIENT_ID : false,
        'instagram_access_token' => defined( 'WP_SOCIAL_STREAM_INSTAGRAM_ACCESS_TOKEN' ) ? WP_SOCIAL_STREAM_INSTAGRAM_ACCESS_TOKEN : false,
        'instagram_redirect_url' => defined( 'WP_SOCIAL_STREAM_INSTAGRAM_REDIRECT_URL' ) ? WP_SOCIAL_STREAM_INSTAGRAM_REDIRECT_URL : false,

        'youtube_search_for' => '',

        'facebook_search_for' => ''
      );
      $data = shortcode_atts( $defaults, $attrs );

      $data['callback'] = admin_url('admin-ajax.php?action=social_stream_twitter&shortcode='.base64_encode($data['twitter_consumer_key'].':'.$data['twitter_consumer_secret'].':'.$data['twitter_access_token'].':'.$data['twitter_access_token_secret']));
      $data['moderate'] = current_user_can('manage_options')?'1':'0';

      $data['remove'] = $this->get_removed_items();

      ob_start();
      require_once( 'static/templates/view.php' );
      $ret = ob_get_clean();
      return $ret;

    }

    /**
     * Get items that are hidden by admin
     * @return string
     */
    function get_removed_items() {
      $hidden= '';

      $hidden_items = get_option( 'social_stream_hidden' );

      foreach( (array)$hidden_items as $net => $items ) {
        foreach( (array)$items as $item ) {
          $hidden .= $item.',';
        }
      }

      return $hidden;
    }

    /**
     * Ajax twitter responder
     */
    function social_stream_twitter() {
      if ( empty( $_GET['shortcode'] ) ) {
        $post_data = maybe_unserialize(get_post_meta($_GET['post_id'], CFCT_BUILD_POSTMETA, true));

        $options = $post_data['data']['modules'][$_GET['module_id']];
      } else {
        $options = explode(':', base64_decode( $_GET['shortcode'] ) );
      }

      define( 'SS_TWITTER_CONSUMER_KEY', $options[$this->get_field_name('twitter_consumer_key')]?$options[$this->get_field_name('twitter_consumer_key')]:$options[0] );
      define( 'SS_TWITTER_CONSUMER_SECRET', $options[$this->get_field_name('twitter_consumer_secret')]?$options[$this->get_field_name('twitter_consumer_secret')]:$options[1] );
      define( 'SS_TWITTER_ACCESS_TOKEN', $options[$this->get_field_name('twitter_access_token')]?$options[$this->get_field_name('twitter_access_token')]:$options[2] );
      define( 'SS_TWITTER_ACCESS_TOKEN_SECRET', $options[$this->get_field_name('twitter_access_token_secret')]?$options[$this->get_field_name('twitter_access_token_secret')]:$options[3] );

      require_once 'lib/twitter.php';
      die();
    }

    /**
     * Modify the data before it is saved, or not
     *
     * @param array $new_data
     * @param array $old_data
     * @return array
     */
    public function update( $new_data, $old_data ) {
      return $new_data;
    }

    /**
     * Display the module
     *
     * @param array $data - saved module data
     * @param array $args - previously set up arguments from a child class
     *
     * @return string HTML
     */
    public function display( $data ) {
      global $post;

      $_data = array();

      $_data['requires'] = 'socialstream';
      $_data['path']     = get_stylesheet_directory_uri();
      $_data['callback'] = admin_url('admin-ajax.php?action=social_stream_twitter&module_id='.$data['module_id'].'&post_id='.$post->ID);
      $_data['wall']     = $data[$this->get_field_name( 'wall' )];
      $_data['rotate_delay'] = $data[$this->get_field_name( 'rotate_delay' )];
      $_data['rotate_direction'] = $data[$this->get_field_name( 'rotate_direction' )];
      $_data['height']   = $data[$this->get_field_name( 'height' )];
      $_data['limit']    = $data[$this->get_field_name( 'limit' )];
      $_data['moderate'] = current_user_can('manage_options')?'1':'0';

      $_data['twitter_search_for'] = $data[$this->get_field_name('twitter_search_for')];
      $_data['twitter_show_text']  = $data[$this->get_field_name('twitter_show_text')];

      $_data['instagram_search_for'] = $data[$this->get_field_name('instagram_search_for')];
      $_data['instagram_client_id']  = $data[$this->get_field_name('instagram_client_id')];
      $_data['instagram_access_token'] = $data[$this->get_field_name('instagram_access_token')];
      $_data['instagram_redirect_url'] = $data[$this->get_field_name('instagram_redirect_url')];

      $_data['youtube_search_for'] = $data[$this->get_field_name('youtube_search_for')];

      $_data['facebook_search_for'] = $data[$this->get_field_name('facebook_search_for')];

      $_data['remove'] = $this->get_removed_items();

      return $this->load_view( $_data );
    }
    
    /**
     * Some necessary js
     * @return type
     */
    public function admin_js() {
      $js = '
        cfct_builder.addModuleLoadCallback("'.$this->id_base.'", function() {
          '.$this->cfct_module_tabs_js().'
        });

        cfct_builder.addModuleSaveCallback("'.$this->id_base.'", function() {
          // find the non-active image selector and clear his value
          $("#'.$this->id_base.'-image-selectors .cfct-module-tab-contents>div:not(.active)").find("input:hidden").val("");
          return true;
        });
      ';
      $js .= $this->global_image_selector_js('global_image', array('direction' => 'horizontal'));
      return $js;
    }

    /**
     * DO nothing for this
     * @return null
     */
    public function text() {return null;}
  }
}

$wp_social_stream = new WP_Social_Stream();
