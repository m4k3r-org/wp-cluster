<?php
/*
 * Copyright 2012, Theia Post Slider, Liviu Cristian Mirea Ghiban.
 */

class TpsAdmin_NavigationBar {
	public $showPreview = true;
	
	public function echoPage() {
		?>
		<form method="post" action="options.php">
			<?php settings_fields('tps_options_nav'); ?>
			<?php $options = get_option('tps_nav'); ?>

			<h3><?php _e("Navigation Bar Settings", 'theia-post-slider'); ?></h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label for="tps_navigation_text"><?php _e("Navigation text:", 'theia-post-slider'); ?></label>
					</th>
					<td>
						<input type="text" id="tps_navigation_text" name="tps_nav[navigation_text]" value="<?php echo $options['navigation_text']; ?>" class="regular-text" onchange="updateSlider()"/>
						<p class="description">Variables: <b>%{currentSlide}</b> and <b>%{totalSlides}</b></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="tps_helper_text"><?php _e("Helper text:", 'theia-post-slider'); ?></label>
					</th>
					<td>
						<input type="text" id="tps_helper_text" name="tps_nav[helper_text]" value="<?php echo $options['helper_text']; ?>" class="regular-text" onchange="updateSlider()"/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="tps_prev_button_text"><?php _e("Previous button text:", 'theia-post-slider'); ?></label>
					</th>
					<td>
						<input type="text" id="tps_prev_button_text" name="tps_nav[prev_text]" value="<?php echo $options['prev_text']; ?>" class="regular-text" onchange="updateSlider()"/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="tps_next_button_text"><?php _e("Next button text:", 'theia-post-slider'); ?></label>
					</th>
					<td>
						<input type="text" id="tps_next_button_text" name="tps_nav[next_text]" value="<?php echo $options['next_text']; ?>" class="regular-text" onchange="updateSlider()"/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="tps_button_width"><?php _e("Button width (px):", 'theia-post-slider'); ?></label>
					</th>
					<td>
						<input type="text" id="tps_button_width" name="tps_nav[button_width]" value="<?php echo $options['button_width']; ?>" class="regular-text" onchange="updateSlider()"/>
						<p class="description">Use this if you want both buttons to have the same width. Insert "0" for no fixed width.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="tps_nav_horizontal_position"><?php _e("Horizontal position:", 'theia-post-slider'); ?></label>
					</th>
					<td>
						<select id="tps_nav_horizontal_position" name="tps_nav[nav_horizontal_position]" onchange="updateSlider()">
							<?php
							foreach (TpsOptions::getButtonHorizontalPositions() as $key => $value) {
								$output = '<option value="' . $key . '"' . ($key == $options['nav_horizontal_position'] ? ' selected' : '') . '>' .$value . '</option>' . "\n";
								echo $output;
							}
							?>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="tps_nav_vertical_position"><?php _e("Vertical position:", 'theia-post-slider'); ?></label>
					</th>
					<td>
						<select id="tps_nav_vertical_position" name="tps_nav[nav_vertical_position]" onchange="updateSlider()">
							<?php
							foreach (TpsOptions::getButtonVerticalPositions() as $key => $value) {
								$output = '<option value="' . $key . '"' . ($key == $options['nav_vertical_position'] ? ' selected' : '') . '>' .$value . '</option>' . "\n";
								echo $output;
							}
							?>
						</select>
					</td>
				</tr>
			</table>
			<br>

			<h3><?php _e("Post Navigation", 'theia-post-slider'); ?></h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label><?php _e("Button behavior:", 'theia-post-slider'); ?></label>
					</th>
					<td>
						<label>
							<input type="checkbox" name="tps_nav[enable_on_pages]" value="true"<?php echo $options['enable_on_pages'] ? ' checked' : ''?>> Enable slider on pages
						</label>
						<p class="description">By default, the slider is enabled only on <b>posts</b>. This will enable it also on <b>pages</b>. Note that some themes may be incompatible with this option.</p>

						<label>
							<input id="tps_post_navigation" onchange="updatePostNavigation()" type="checkbox" name="tps_nav[post_navigation]" value="true"<?php echo $options['post_navigation'] ? ' checked' : ''?>> Enable additional post navigation
						</label>
						<p class="description">Clicking the "previous" button on the <b>first</b> slide will open the previous post, and clicking the "next" button on the <b>last</b> slide will open the next post.</p>

						<label>
							<input id="tps_post_navigation_same_category" type="checkbox" name="tps_nav[post_navigation_same_category]" value="true"<?php echo $options['post_navigation_same_category'] ? ' checked' : ''?>> Only for posts from the same category
						</label>
						<p class="description">The "previous" and "next" buttons will only navigate through posts of the same category.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="tps_prev_post_button_text"><?php _e("Previous button text:", 'theia-post-slider'); ?></label>
					</th>
					<td>
						<input type="text" id="tps_prev_post_button_text" name="tps_nav[prev_text_post]" value="<?php echo $options['prev_text_post']; ?>" class="regular-text" onchange="updateSlider()"/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="tps_next_post_button_text"><?php _e("Next button text:", 'theia-post-slider'); ?></label>
					</th>
					<td>
						<input type="text" id="tps_next_post_button_text" name="tps_nav[next_text_post]" value="<?php echo $options['next_text_post']; ?>" class="regular-text" onchange="updateSlider()"/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="tps_post_button_width"><?php _e("Button width (px):", 'theia-post-slider'); ?></label>
					</th>
					<td>
						<input type="text" id="tps_post_button_width" name="tps_nav[button_width_post]" value="<?php echo $options['button_width_post']; ?>" class="regular-text" onchange="updateSlider()"/>
						<p class="description">Use this if you want both buttons to have the same width. Insert "0" for no fixed width.</p>
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

				// Update navigation text
				slider.setNavText($('#tps_navigation_text').val());

				// Update title text
				slider.setTitleText($('#tps_helper_text').val());

				// Update button text
				$('.theiaPostSlider_nav ._prev ._2').html($('#tps_prev_button_text').val());
				$('.theiaPostSlider_nav ._next ._2').html($('#tps_next_button_text').val());

				// Update button width
				var width = parseInt($('#tps_button_width').val());
				$('.theiaPostSlider_nav ._2').css('width', width > 0 ? width : '');

				// Update horizontal position
				$('#tps_nav_upper, #tps_nav_lower')
					.removeClass('_left _center _right')
					.addClass('_' + $('#tps_nav_horizontal_position').val());

				// Update vertical position
				$('#tps_nav_upper').toggle(['top_and_bottom', 'top'].indexOf($('#tps_nav_vertical_position').val()) != -1);
				$('#tps_nav_lower').toggle(['top_and_bottom', 'bottom'].indexOf($('#tps_nav_vertical_position').val()) != -1);
			}

			function updatePostNavigation() {
				var $ = jQuery,
					enabled = $('#tps_post_navigation').attr('checked') == 'checked';
				$('#tps_prev_post_button_text, #tps_next_post_button_text, #tps_post_button_width').attr('readonly', !enabled);
				$('#tps_post_navigation_same_category').attr('disabled', !enabled);
			}
			updatePostNavigation();
		</script>
		<?php
	}
}