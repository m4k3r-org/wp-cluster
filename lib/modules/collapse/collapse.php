<?php
/**
 *
 */
if( !class_exists( 'CollapseModule' ) ) {

  /**
   *
   */
  class CollapseModule extends \UsabilityDynamics\Theme\Module {

    /**
     * Construct
     */
    public function __construct(){
      wp_enqueue_script('jquery-ui-accordion');
      $opts = array(
        'description' => __( '', wp_festival( 'domain' ) ),
        'icon' => plugins_url( '/icon.png', __DIR__ )
      );
      parent::__construct( 'cfct-module-collapse', __( 'Collapse', wp_festival( 'domain' ) ), $opts );
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
     * @param type $data
     * @return null
     */
    public function text( $data ){
      return null;
    }

    /**
     *
     * @param type $data
     * @return type
     */
    public function admin_text( $data ){
      return strip_tags( $data[ 'title' ] );
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
  }
}
