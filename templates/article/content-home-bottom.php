<?php
/**
 * Content - Home Bottom
 *
 * Displays the bottom of page element on the home page.
 *
 * This can be overridden in child themes using get_template_part()
 *
 * @package WP-Disco
 * @since WP-Disco 1.0.0
 *
 */ 
 
 ?>
 
<div class="content_horizontal_widget widget_area">
  <?php dynamic_sidebar( 'home_bottom_sidebar' ); ?>
</div>
