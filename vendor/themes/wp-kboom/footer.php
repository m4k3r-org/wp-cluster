			</div>
            <!-- end #page here -->
            <!-- begin #footer -->
			<footer id="footer">

                <div id="footer-arrow"></div>
			
				<div class="container clearfix">
					
					<?php if ( is_active_sidebar( 'footer-sidebar' ) ) : ?>

						<?php dynamic_sidebar( 'footer-sidebar' ); ?>

					<?php else : ?>

						<!-- This content shows up if there are no widgets defined in the backend. -->
						
						<div class="one-fourth">
						
							<!-- This content shows up if there are no widgets defined in the backend. -->
			
							<div class="help">
							
								<p>
									<?php _e("Please activate some Widgets.", "site5framework"); ?>

									<?php if(current_user_can('edit_theme_options')) : ?>
									<a href="<?php echo admin_url('widgets.php')?>" class="add-widget"><?php _e("Add Widget", "site5framework"); ?></a>
									<?php endif ?>
								</p>
							
							</div>
						
						</div>

					<?php endif; ?>
					
					
					
				</div> <!-- end #footerWidgets -->
				
				<!-- begin #copyright -->
				<div id="copyrights">
					<div class="container clearfix">

						<?php if(of_get_option('sc_footer_copyright') == '') { ?>
						<?php } else { ?>
						<?php echo of_get_option('sc_footer_copyright')  ?>
						<?php } ?>

				    </div> <!-- end #copyright -->
                </div> <!-- end #container -->
			</footer> <!-- end footer -->



		<!-- scripts are now optimized via Modernizr.load -->
		<script src="<?php echo get_template_directory_uri(); ?>/library/js/scripts.js" type="text/javascript"></script>
		
		<!--[if lt IE 7 ]>
  			<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
  			<script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
		<![endif]-->

<!--            <script src="--><?php //echo get_template_directory_uri(); ?><!--/library/js/jquery.jscrollpane.min.js" type="text/javascript"></script>-->
            <script src="<?php echo get_template_directory_uri(); ?>/library/js/soundmanager2-nodebug-jsmin.js" type="text/javascript"></script>
            <script src="<?php echo get_template_directory_uri(); ?>/library/js/jquery.apPlaylistManager.js" type="text/javascript"></script>
            <script src="<?php echo get_template_directory_uri(); ?>/library/js/jquery.apTextScroller.js" type="text/javascript"></script>
            <script src="<?php echo get_template_directory_uri(); ?>/library/js/jquery.html5audio.js" type="text/javascript"></script>
            <script src="<?php echo get_template_directory_uri(); ?>/library/js/jquery.html5audio.settings.js" type="text/javascript"></script>
            <script type="text/javascript">
                jQuery.extend(ap_settings, {
                    buttonsUrl: {
                        prev: '<?php echo get_template_directory_uri(); ?>/library/images/html5-audio-player/set1/prev.png',
                        prevOn: '<?php echo get_template_directory_uri(); ?>/library/images/html5-audio-player/set1/prev_on.png',
                        next: '<?php echo get_template_directory_uri(); ?>/library/images/html5-audio-player/set1/next.png',
                        nextOn: '<?php echo get_template_directory_uri(); ?>/library/images/html5-audio-player/set1/next_on.png',
                        pause: '<?php echo get_template_directory_uri(); ?>/library/images/html5-audio-player/set1/pause.png',
                        pauseOn: '<?php echo get_template_directory_uri(); ?>/library/images/html5-audio-player/set1/pause_on.png',
                        play: '<?php echo get_template_directory_uri(); ?>/library/images/html5-audio-player/set1/play.png',
                        playOn: '<?php echo get_template_directory_uri(); ?>/library/images/html5-audio-player/set1/play_on.png',
                        volume: '<?php echo get_template_directory_uri(); ?>/library/images/html5-audio-player/set1/volume.png',
                        volumeOn: '<?php echo get_template_directory_uri(); ?>/library/images/html5-audio-player/set1/volume_on.png',
                        mute: '<?php echo get_template_directory_uri(); ?>/library/images/html5-audio-player/set1/mute.png',
                        muteOn: '<?php echo get_template_directory_uri(); ?>/library/images/html5-audio-player/set1/mute_on.png',
                        loop: '<?php echo get_template_directory_uri(); ?>/library/images/html5-audio-player/set1/loop.png',
                        loopOn: '<?php echo get_template_directory_uri(); ?>/library/images/html5-audio-player/set1/loop_on.png',
                        shuffle: '<?php echo get_template_directory_uri(); ?>/library/images/html5-audio-player/set1/shuffle.png',
                        shuffleOn: '<?php echo get_template_directory_uri(); ?>/library/images/html5-audio-player/set1/shuffle_on.png'
                    }
                });

                soundManager.setup({
                    url: '<?php echo get_template_directory_uri(); ?>/library/swf/', // path to SoundManager2 SWF files
                    allowScriptAccess: 'always',
                    debugMode: false,
                    noSWFCache: true,
                    useConsole: false,
                    waitForWindowLoad: true,
                    flashVersion: 9,
                    useFlashBlock: true,
                    preferFlash: true,
                    useHTML5Audio: true
                });
            </script>
            <script src="<?php echo get_template_directory_uri(); ?>/library/js/jquery.html5audio.func.js" type="text/javascript"></script>

        <script src="<?php echo get_template_directory_uri(); ?>/library/js/jquery.mixitup.js" type="text/javascript"></script>
		<?php wp_footer(); // js scripts are inserted using this function ?>
	</body>
</html>