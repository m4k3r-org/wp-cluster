<?php
/*
 * Copyright 2012, Theia Post Slider, Liviu Cristian Mirea Ghiban.
 */

class TpsAdmin_Advanced {
	public $showPreview = false;
	
	public function echoPage() {
		?>
		<form method="post" action="options.php">
			<?php settings_fields('tps_options_advanced'); ?>
			<?php $options = get_option('tps_advanced'); ?>

			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label for="default_activation_behavior"><?php _e("Default activation behavior:", 'theia-post-slider'); ?></label>
					</th>
					<td>
						<select id="default_activation_behavior" name="tps_advanced[default_activation_behavior]">
							<option value="1" <?=$options['default_activation_behavior'] == 1 ? 'selected' : ''?>>Enable by default on all posts</option>
							<option value="0" <?=$options['default_activation_behavior'] == 0 ? 'selected' : ''?>>Disable by default on all posts</option>
						</select>
						<p class="description">
							You can also enable or disable the slider on a post-by-post basis.
						</p>
						<p></p>
					</td>
				</tr>
			</table>

			<h3><?php _e("Slide Loading Mechanism", 'theia-post-slider'); ?></h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label><?php _e("Slide loading mechanism:", 'theia-post-slider'); ?></label>
					</th>
					<td>
						<label>
							<input type="radio" name="tps_advanced[slide_loading_mechanism]" value="ajax" <?=$options['slide_loading_mechanism'] == 'ajax' ? 'checked' : ''?>>
							Load slides efficiently using AJAX.
							<p class="description">Recommended. Most efficient option and offers best user experience. Uses preloading and caching methods. </p>
						</label>
						<p></p>

						<label>
							<input type="radio" name="tps_advanced[slide_loading_mechanism]" value="refresh" <?=$options['slide_loading_mechanism'] == 'refresh' ? 'checked' : ''?>>
							Refresh page on each slide.
							<p class="description">The entire page will refresh when navigating to another slide. You can use this if ad refreshing doesn't work for you. Transition effects cannot be used with this option.</p>
						</label>
						<p></p>

						<label>
							<input type="radio" name="tps_advanced[slide_loading_mechanism]" value="all" <?=$options['slide_loading_mechanism'] == 'all' ? 'checked' : ''?>>
							Load all slides at once.
							<p class="description">Legacy mode. Use this option if you have compatibility issues.</p>
						</label>
						<p></p>
					</td>
				</tr>
			</table>
			<br>

			<h3><?php _e("Ad behavior", 'theia-post-slider'); ?></h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label><?php _e("Ad refreshing:", 'theia-post-slider'); ?></label>
					</th>
					<td>
						<label>
							<input type="checkbox" id="tps_refresh_ads" name="tps_advanced[refresh_ads]" onchange="updateAdRefreshing()" value="true" <?=$options['refresh_ads'] ? 'checked' : ''?>>
							Refresh ads when navigating to another slide.
							<p class="description">
								Works with Google DoubleClick and partners.
								Requires that you use <strong><a href="https://support.google.com/dfp_premium/answer/177207">GPT (Google Publishing Tags)</a></strong> and <strong><a href=https://support.google.com/dfp_premium/answer/183282">asynchronous rendering</a></strong>.
								DART tags are not supported. Google AdSense is not supported because their Terms of Service forbid this kind of behavior.
							</p>
							<p></p>
						</label>
						<p></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="tps_refresh_ads_every_n_slides"><?php _e("Refresh ads every N slides:", 'theia-post-slider'); ?></label>
					</th>
					<td>
						<input type="text" id="tps_refresh_ads_every_n_slides" name="tps_advanced[refresh_ads_every_n_slides]" value="<?=$options['refresh_ads_every_n_slides']?>">
						<p class="description">
							Use "1" to refresh ads on every slide.
						</p>
						<p></p>
					</td>
				</tr>
			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save All Changes', 'theia-post-slider') ?>" />
			</p>
		</form>
		<script type="text/javascript">
			function updateAdRefreshing() {
				var $ = jQuery,
					enabled = $('#tps_refresh_ads').attr('checked') == 'checked';
				$('#tps_refresh_ads_every_n_slides').attr('readonly', !enabled);
			}
			updateAdRefreshing();
		</script>
		<?php
	}
}