<?php

if($flawless['flawless_logo']['url']) {
  $custom_logo = true;
  $splash_logo = $flawless['flawless_logo']['url'];
} else {
  $custom_logo = false;
  $splash_logo = get_bloginfo('template_url') . '/img/coffee_machine-256.png';
}

?>

<?php get_header('splash'); ?>

<div class="center_me">

  <?php if($custom_logo) { ?>
    <div class="splash_container">
      <img src="<?php echo $splash_logo; ?>" class="splash" title="<?php bloginfo(); ?>" />
      <div class="alert-message block-message info">
      <p><?php printf(__('%1s is currently receiving some updates.', 'flawless'), get_bloginfo()); ?></p>
      <p><?php printf(__('We will return in no time, thank you.', 'flawless'), get_bloginfo()); ?></p>
      </div>
    </div>
  <?php } else { ?>
    <div class="alert-message block-message info">
      <p><?php printf(__('%1s is currently receiving some updates.', 'flawless'), get_bloginfo()); ?></p>
      <p><?php printf(__('We will return in no time, thank you.', 'flawless'), get_bloginfo()); ?></p>
      </p>
    </div>
  <?php } ?>

  <?php /* if(current_user_can('manage_options')) { ?>
    <div class="alert-message block-message info" data-alert="alert">
      <a href="#" class="close">&times;</a>
      <p><?php _e('Administrator, this page is displayed because the site is in maintanance mode.', 'flawless'); ?></p>
    </div>
  <?php } */ ?>
  
</div>

<?php get_footer('splash'); ?>

