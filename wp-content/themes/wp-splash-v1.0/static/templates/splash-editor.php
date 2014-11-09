<div class="wrap" id="panels-home-page">
	<form action="<?php admin_url( 'edit.php?post_type=page&page=splash-home-editor' ); ?>" class="hide-if-no-js" method="post" id="panels-home-page-form">
		<h2><?php esc_html_e( 'Splash Page Editor', 'siteorigin-panels' ) ?></h2>

    <?php settings_errors( 'wp-splash' ); ?>

		<div id="post-body-wrapper">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content" style="position: relative">
					<a href="#" class="preview button" id="post-preview"><?php _e('Preview Changes', 'siteorigin-panels') ?></a>
          <?php wp_editor('', 'content') ?>
          <?php do_meta_boxes('appearance_page_so_panels_home_page', 'advanced', false) ?>
          <p><input type="submit" class="button button-primary" id="panels-save-home-page" value="<?php esc_attr_e('Save Home Page', 'siteorigin-panels') ?>" /></p>
				</div>
			</div>
		</div>

		<input type="hidden" id="panels-home-enabled" name="siteorigin_panels_home_enabled" value="<?php echo esc_attr( get_option('siteorigin_panels_home_page_enabled', $settings['home-page-default']) ? 'true' : 'false' ); ?>" />
    <?php wp_nonce_field('save', '_sopanels_home_nonce') ?>
	</form>
	<noscript><p><?php _e('This interface requires Javascript', 'siteorigin-panels') ?></p></noscript>
</div> 