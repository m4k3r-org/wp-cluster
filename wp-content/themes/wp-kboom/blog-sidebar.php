<aside>	
		<?php if ( is_active_sidebar( 'Blog' ) ) : ?>

			<?php dynamic_sidebar( 'Blog' ); ?>

		<?php else : ?>

			<!-- This content shows up if there are no widgets defined in the backend. -->
			<?php if(current_user_can('edit_theme_options')) : ?>
			<div class="help">
			
				<p>
					<?php _e("Please activate some Widgets.", "site5framework"); ?>
					
					<a href="<?php echo admin_url('widgets.php')?>" class="add-widget"><?php _e("Add Widget", "site5framework"); ?></a>
					
				</p>
			
			</div>
			<?php endif ?>
			
		<?php endif; ?>
</aside>