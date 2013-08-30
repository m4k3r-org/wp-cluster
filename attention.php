<?php
/**
 * Attention General displays the attenion grabbing element on all standard pages.
 *
 *
 * This can be overridden in child themes with loop.php or
 * attention-template.php, where 'template' is the context
 * requested by a template. For example, attention-blog-home.php would
 * be used if it exists and we ask for the attention with:
 * <code>get_template_part( 'attention', 'blog-home' );</code>
 *
 * @package Flawless
 * @since Flawless 3.0
 *
 */ 

  if(get_post_meta($post->ID, 'hide_header', true) == 'true' || !current_theme_supports('inner_page_slideshow_area')) {
    return;
  }
 
  //** Check if Home Page Attention Grabber is active.
  
  $this_widget_area = 'inside_attention_grabber';
 
  if($tabs = flawless_theme::widget_area_tabs($this_widget_area)) { ?>
  
    <div class="sld-flexible flawless_attention_grabber_area">
    <div class='sld-top'></div>
    <div class="flawless_widget_area_tabs wpp_property_header_area <?php echo (count($tabs) < 2 ? 'no_tabs' : 'have_tabs'); ?>">
    
    <?php if(count($tabs) > 1) { ?>
      <ul class="attention_grabber_tabs flawless_widget_tabs">
      <?php foreach($tabs as $widget) { ?>
          <li class="flawless_tab"><a href="#<?php echo $widget['id'];?>"  class="flawless_tab_link"><?php echo $widget['title']; ?></a></li>
      <?php } ?>
      </ul>
    <?php } ?>
      
    <?php dynamic_sidebar($this_widget_area); ?>

    </div>
    <div class='sld-bottom'></div>
    </div>
    <?php
    
 
  } else {
  
    //** Show default / legacy headers */

    flawless_header_image();

  }
