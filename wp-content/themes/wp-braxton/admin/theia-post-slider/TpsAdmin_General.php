<?php
/*
 * Copyright 2012, Theia Post Slider, Liviu Cristian Mirea Ghiban.
 */

class TpsAdmin_General {
	public $showPreview = true;
	
	public function echoPage() {
		?>
		<form method="post" action="options.php">
			<?php settings_fields('tps_options_general'); ?>
			<?php $options = get_option('tps_general'); ?>

			<h3><?php _e("General Settings", 'theia-post-slider'); ?></h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label for="tps_theme"><?php _e("Theme:", 'theia-post-slider'); ?></label>
					</th>
					<td>
						<select id="tps_theme" name="tps_general[theme]" onchange="updateSlider()">
							<?php
							foreach (TpsOptions::getThemes() as $key => $value) {
								$output = '<option value="' . $key . '"' . ($key == $options['theme'] ? ' selected' : '') . '>' .$value . '</option>' . "\n";
								echo $output;
							}
							?>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="tps_transition_effect"><?php _e("Transition effect:", 'theia-post-slider'); ?></label>
					</th>
					<td>
						<select id="tps_transition_effect" name="tps_general[transition_effect]" onchange="updateSlider()">
							<?php
							foreach (TpsOptions::getTransitionEffects() as $key => $value) {
								$output = '<option value="' . $key . '"' . ($key == $options['transition_effect'] ? ' selected' : '') . '>' .$value . '</option>' . "\n";
								echo $output;
							}
							?>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="tps_transition_speed"><?php _e("Transition duration (ms):", 'theia-post-slider'); ?></label>
					</th>
					<td>
						<input type="text" id="tps_transition_speed" name="tps_general[transition_speed]" value="<?php echo $options['transition_speed']; ?>" class="regular-text" onchange="updateSlider()"/>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save All Changes', 'theia-post-slider') ?>" />
			</p>
		</form>
		<script type="text/javascript">
			function updateSlider() {
				var $ = jQuery;

				// Update transition
				slider.setTransition({
					'effect': $('#tps_transition_effect').val(),
					'speed': parseInt($('#tps_transition_speed').val())
				});

				// Update theme
				var css = $('#theiaPostSlider-css');
				var href = '<?php echo TPS_PLUGINS_URL . 'css/' ?>' + $('#tps_theme').val() + '?ver=<?php echo TPS_VERSION ?>';
				if (css.attr('href') != href) {
					css.attr('href', href);
				}
			}
		</script>
		<?php
	}
}