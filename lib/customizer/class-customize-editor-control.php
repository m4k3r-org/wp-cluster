<?php
/**
 * Class Customize_Editor_Control
 * Adds Editor functionality
 *
 * @author usabilitydynamics@UD
 * @see https://codex.wordpress.org/Theme_Customization_API
 * @version 0.1
 * @module UsabilityDynamics\AMD
 */
namespace UsabilityDynamics\AMD {
  
  if( !class_exists( '\UsabilityDynamics\AMD\Customize_Editor_Control' ) ) {
  
    /**
     * Class Customize_Editor_Control
     * Adds Editor functionality
     *
     * @package UsabilityDynamics\UI
     */
    class Customize_Editor_Control extends \WP_Customize_Control {

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
        ?>
        <div id="udx-style-editor-wrapper" class="udx-customization-editor" data-require="ui.wp.editor.style">
          <label>
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            <textarea class="udx-style-editor" rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
          </label>
        </div>
        <?php
      }

    }
    
  }

}


      