<?php if ( function_exists( 'icl_get_languages' ) ): ?>

	<div class="language-switcher">
		<?php
			$languages = icl_get_languages('skip_missing=0&orderby=name');

			// Get the active language and generate a language name (suffix '-small')
			$language_flag_url = null;
			$language_name = null;
			foreach ( $languages as $language_id => $language_value )
			{
				if ( (bool) $language_value['active'] )
				{
					// Get by attachment title for the small flag
					$get_language_image = new WP_Query(array(
							'post_per_page' => 1,
							'post_type' => 'attachment',
							'name' => $language_id .'-small'
					));

					if ( isset( $get_language_image->posts[0] ) )
					{
						$language_flag_url = wp_get_attachment_url( $get_language_image->posts[0]->ID );
					}

					$language_name = $language_value['translated_name'];

					break;
				}
			}

			// hardcoded need to remove
			$language_flag_url = '/wp-content/themes/wp-festival-v2.0/static/images/lang/en-small.png';

			if ( $language_flag_url !== null ): ?>
				<a href="#" class="active-language"><img src="<?php echo $language_flag_url; ?>" alt="<?php echo $language_name; ?>"></a>
			<?php endif; ?>
	</div>

<?php endif; ?>