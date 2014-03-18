<?php

if( !class_exists( 'SocialStreamModule' ) ) {

  /**
   * Social Stream Module
   */
  class SocialStreamModule extends \UsabilityDynamics\Theme\Module {

    /**
     * Construct
     */
    public function __construct(){

      add_action( 'wp_ajax_nopriv_social_stream_twitter', array( $this, 'social_stream_twitter' ) );
      add_action( 'wp_ajax_social_stream_twitter', array( $this, 'social_stream_twitter' ) );

      $opts = array(
        'description' => __( 'The plugin creates a social stream, which is a single stream of items and updates created from all of your individual social network profiles, data feeds and APIs.' ),
        'icon' => plugins_url( '/icon.png', __DIR__ )
      );
      parent::__construct( 'cfct-module-social-stream', __( 'Social Stream' ), $opts );

    }

    /**
     * Ajax twitter responder
     */
    function social_stream_twitter() {
      $post_data = maybe_unserialize(get_post_meta($_GET['post_id'], CFCT_BUILD_POSTMETA, true));

      $options = $post_data['data']['modules'][$_GET['module_id']];

      define( 'SS_TWITTER_CONSUMER_KEY', $options[$this->get_field_name('twitter_consumer_key')] );
      define( 'SS_TWITTER_CONSUMER_SECRET', $options[$this->get_field_name('twitter_consumer_secret')] );
      define( 'SS_TWITTER_ACCESS_TOKEN', $options[$this->get_field_name('twitter_access_token')] );
      define( 'SS_TWITTER_ACCESS_TOKEN_SECRET', $options[$this->get_field_name('twitter_access_token_secret')] );

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
      return $this->load_view( $data );
    }

    /**
     *
     * @param type $data
     * @return type
     */
    public function admin_form( $data ){
      ob_start();
      require_once( __DIR__ . '/admin/form.php' );
      return ob_get_clean();
    }

    /**
     *
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

    public function text() {return null;}
  }
}
