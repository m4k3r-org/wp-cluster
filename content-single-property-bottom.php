<?php
/**
 * Content - Single Property Map. 
 *
 * Displays the attenion grabbing element on the homage page.
 *
 * This can be overridden in child themes using get_template_part()
 *
 * @package Flawless
 * @since Flawless 3.0
 *
 */ 
 
  $this_widget_area = 'wpp_foooter_' . $property['property_type'];
 
  if($tabs = flawless_theme::widget_area_tabs($this_widget_area)) { ?>

    <div class="flawless_widget_area_tabs wpp_property_bottom_area <?php echo (count($tabs) < 2 ? 'no_tabs' : 'have_tabs'); ?>">
    
    <?php if(count($tabs) > 1) { ?>
      <ul class="attention_grabber_tabs flawless_widget_tabs">
      <?php foreach($tabs as $widget) { ?>
          <li class="flawless_tab"><a href="#<?php echo $widget['id'];?>" class="flawless_tab_link"><?php echo $widget['title']; ?></a></li>
      <?php } ?>
      </ul>
    <?php } ?>
      
    <?php dynamic_sidebar($this_widget_area); ?>

    </div>
    
  <?php } ?>
    
   
  <?php if ( is_active_sidebar( "flawless_property_footer") ) : ?>
    <div class="content_horizontal_widget widget_area">
    <?php dynamic_sidebar( "flawless_property_footer"); ?>
    </div>
  <?php endif; ?>