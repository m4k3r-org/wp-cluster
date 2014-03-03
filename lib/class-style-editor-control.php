<?php
/**
 * Style Editor Customizer
 *
 * @version 1.0.0
 */
namespace UsabilityDynamics\AMD {

  if( !class_exists( 'UsabilityDynamics\AMD\Style_Editor_Control' ) ) {

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

        echo join( '', array(
          '<div id="udx-style-editor-wrapper" class="udx-customization-editor" data-require="ui.wp.editor.style"></div>',
          '<textarea id="udx-style-editor" rows="15" style="width:100%;" ',
          $this->get_link(),
          '>',
          esc_textarea( $this->value() ),
          '</textarea>',

        ));

      }

    }
    
  }

}

