<?php
/**
 *
 */
namespace UsabilityDynamics\UI {

  /**
   * Class Script_Editor_Control
   *
   * @package UsabilityDynamics\UI
   */
  class Script_Editor_Control extends \WP_Customize_Control {

    /**
     * @var string
     *
     */
    public $type = 'textarea';

    /**
     * Render Textarea Input
     *
     */
    public function render_content() {

      echo join( '', [
        '<div id="udx-script-editor-wrapper"></div>',
        '<textarea id="udx-script-editor" style="display: none;" ', $this->get_link(), '>',
        esc_textarea( $this->value() ),
        '</textarea>',

      ]);

    }

  }

}

