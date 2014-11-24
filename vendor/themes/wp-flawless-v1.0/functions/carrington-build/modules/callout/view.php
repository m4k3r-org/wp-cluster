<?php
	if (!empty($title)) {
		echo '<h2 class="cfct-mod-title';
		if (!empty($data[$this->get_field_id('style-title')])) {
			echo ' '.esc_attr($data[$this->get_field_name('style-title')]);
		}
		echo '">'.$title.'</h2>';
	}
	if (!empty($image)) {
		echo $image;
	}
	
	/* Modified by potanin@UD */
?>
<div class="cfct-mod-content<?php if (!empty($data[$this->get_field_id('style-content')])) { echo ' '.$data[$this->get_field_name('style-content')]; } ?>">
	<div class="callout-text-content"><?php echo $content; ?></div>
	
  <?php if($button_text && $url) {  ?>
  <a class="action_button callout_button" href="<?php echo $url;?>"><?php echo $button_text;?></a>
  <?php } ?>
</div>
