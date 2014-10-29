<?php

  $footer_menu = wp_nav_menu(array(
    'theme_location'  => 'footer-menu',
    'menu_class'      => 'footer-nav flawless-menu clearfix',
    'after'           => ' <span class="divider">|</span> ',
    'echo'           => false,
    'fallback_cb'     => 'flawless_list_pages'
  ));

  $bottom_of_page_menu = wp_nav_menu(array(
    'theme_location'  => 'bottom_of_page_menu',
    'menu_class'      => 'footer-nav flawless-menu clearfix',
    'after'           => ' <span class="divider">|</span> ',
    'echo'           => false,
    'fallback_cb'     => 'flawless_list_pages'
  ));

?>

    <div class="bottom"><div class="inner_bottom"></div></div>
  </div>

  <div class="footer bottom-of-page clearfix">
    <div class="inner_footer container clearfix flawless_dynamic_area" container_type="footer">

      <?php if($footer_menu) { ?>
      <div <?php flawless_element('menu-footer-container clearfix'); ?>><?php echo $footer_menu; ?></div>
      <?php } ?>
      
      <?php if($bottom_of_page_menu) { ?>
      <div <?php flawless_element('bottom-of-page-container clearfix'); ?>><?php echo $bottom_of_page_menu; ?></div>
      <?php } ?>

      <div <?php flawless_element('flawless_footer_copyright'); ?>>
        <?php flawless_footer_copyright(); ?>
      </div>

    </div>
  </div>

</div><?php //** .wrapper */ ?>
<?php wp_footer(); ?>
</body>
</html>
