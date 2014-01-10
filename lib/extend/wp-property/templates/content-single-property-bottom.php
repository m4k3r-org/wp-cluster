<?php
/**
 * Content - Single Property Map.
 *
 * Displays the attenion grabbing element on the homage page.
 *
 * This can be overridden in child themes using get_template_part()
 *
 * @module Flawless
 * @since Flawless 0.0.3
 *
 */

$this_widget_area = 'wpp_foooter_' . $property[ 'property_type' ]; ?>

<?php if ( is_active_sidebar( "flawless_property_footer" ) ) : ?>
  <div class="content_horizontal_widget widget_area">
    <?php dynamic_sidebar( "flawless_property_footer" ); ?>
    </div>
<?php endif; ?>