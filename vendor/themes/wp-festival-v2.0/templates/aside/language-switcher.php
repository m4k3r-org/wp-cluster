<?php if ( function_exists( 'icl_get_languages' ) ): ?>

	<div class="language-switcher">
		<?php
			$languages = icl_get_languages('skip_missing=0&orderby=name');

			// Get the active language and generate a language name (suffix '-small')
			$language_flag_mobile_url = null;
			$language_flag_desktop_url = null;
			$language_name = null;

			foreach ( $languages as $language_id => $language_value )
			{
				if ( (bool) $language_value['active'] )
				{
					// Get by attachment title for the small flag (mobile)
					$get_language_image = new WP_Query(array(
							'post_per_page' => 1,
							'post_type' => 'attachment',
							'name' => $language_id .'-small-mobile'
					));

					if ( isset( $get_language_image->posts[0] ) )
					{
						$language_flag_mobile_url = wp_get_attachment_url( $get_language_image->posts[0]->ID );
					}

					// Get by attachment title for the small flag (desktop)
					$get_language_image = new WP_Query(array(
							'post_per_page' => 1,
							'post_type' => 'attachment',
							'name' => $language_id .'-small-desktop'
					));

					if ( isset( $get_language_image->posts[0] ) )
					{
						$language_flag_desktop_url = wp_get_attachment_url( $get_language_image->posts[0]->ID );
					}

					// Get the language name
					$language_name = $language_value['translated_name'];

					break;
				}
			}

			// hardcoded need to remove
			//$language_flag_mobile_url = '/wp-content/themes/wp-festival-v2.0/static/images/lang/es-small-mobile.png';
			//$language_flag_desktop_url = '/wp-content/themes/wp-festival-v2.0/static/images/lang/es-small-desktop.png';

			if ( ($language_flag_mobile_url !== null) && ($language_flag_desktop_url !== null) ): ?>
				<a href="#" class="active-language">
					<img class="flag-mobile" src="<?php echo $language_flag_mobile_url; ?>" alt="<?php echo $language_name; ?>">
					<img class="flag-desktop" src="<?php echo $language_flag_desktop_url; ?>" alt="<?php echo $language_name; ?>">
				</a>
			<?php endif; ?>
	</div>

<?php endif; ?>