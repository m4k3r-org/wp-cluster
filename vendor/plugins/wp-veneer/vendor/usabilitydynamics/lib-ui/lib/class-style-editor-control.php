<?php
/**
 *
 */
namespace UsabilityDynamics\UI {

  /**
   * Class Style_Editor_Control
   *
   * @package UsabilityDynamics\UI
   */
  class Style_Editor_Control extends \WP_Customize_Control {

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
        '<div id="udx-style-editor-wrapper"></div>',
        '<textarea id="udx-style-editor" style="display: none;" ', $this->get_link(), '>',
        esc_textarea( $this->value() ),
        '</textarea>',

      ]);

    }

  }

}

