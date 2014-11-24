<?php
	if ( function_exists( 'icl_get_languages' ) ):
		$languages = icl_get_languages('skip_missing=0&orderby=name');

		// Get the active language and generate a language name (suffix '-large')
		foreach ( $languages as $language_id => $language_value )
		{
			// Get by attachment title
			$get_language_image = new WP_Query(array(
					'post_per_page' => 1,
					'post_type' => 'attachment',
					'name' => trim($language_value['language_code']) .'-large'
			));

			if ( isset( $get_language_image->posts[0] ) )
			{
				$languages[ $language_id ]['country_flag_large_url'] = wp_get_attachment_url( $get_language_image->posts[0]->ID );
			}
		}
?>

<div class="language-overlay overlay">
	<a href="#" class="icon-close"></a>

	<div class="overlay-content">

		<div class="container">
			<div class="row">

				<?php foreach ( $languages as $language_id => $language_value ): ?>

						<div class="col-xs-12 col-md-6 language-item">
							<a href="<?php echo $language_value['url']; ?>">
								<img src="<?php echo $language_value['country_flag_large_url']; ?>" alt="<?php echo $language_value['translated_name']; ?>">
								<span><?php echo $language_value['translated_name']; ?></span>
							</a>
						</div>

				<?php endforeach; ?>

			</div>
		</div>
	</div>

	<div class="bg"></div>
</div>

<?php endif; ?>
