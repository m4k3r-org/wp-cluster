<?php
/**
 * Plugin Name: Tag Cloud Widget
 */

add_action( 'widgets_init', 'mvp_social_load_widgets' );

function mvp_social_load_widgets() {
	register_widget( 'mvp_social_widget' );
}

class mvp_social_widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function mvp_social_widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'mvp_social_widget', 'description' => __('A widget that displays social buttons from several social media sites.', 'mvp_social_widget') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'mvp_social_widget' );

		/* Create the widget. */
		$this->WP_Widget( 'mvp_social_widget', __('Braxton: Social Widget', 'mvp_social_widget'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		?>


				<div id="content-social">
					<ul>
						<?php if(get_option('mvp_facebook')) { ?>
						<li class="fb-item">
							<a href="http://www.facebook.com/<?php echo get_option('mvp_facebook'); ?>" alt="Facebook" class="fb-but" target="_blank"></a>
						</li>
						<?php } ?>
						<?php if(get_option('mvp_twitter')) { ?>
						<li class="twitter-item">
							<a href="http://www.twitter.com/<?php echo get_option('mvp_twitter'); ?>" alt="Twitter" class="twitter-but" target="_blank"></a>
						</li>
						<?php } ?>
						<?php if(get_option('mvp_pinterest')) { ?>
						<li class="pinterest-item">
							<a href="http://www.pinterest.com/<?php echo get_option('mvp_pinterest'); ?>" alt="Pinterest" class="pinterest-but" target="_blank"></a>
						</li>
						<?php } ?>
						<?php if(get_option('mvp_google')) { ?>
						<li class="google-item">
							<a href="<?php echo get_option('mvp_google'); ?>" alt="Google Plus" class="google-but" target="_blank"></a>
						</li>
						<?php } ?>
						<?php if(get_option('mvp_instagram')) { ?>
						<li class="instagram-item">
							<a href="http://www.instagram.com/<?php echo get_option('mvp_instagram'); ?>" alt="Instagram" class="instagram-but" target="_blank"></a>
						</li>
						<?php } ?>
						<?php if(get_option('mvp_youtube')) { ?>
						<li class="youtube-item">
							<a href="http://www.youtube.com/user/<?php echo get_option('mvp_youtube'); ?>" alt="YouTube" class="youtube-but" target="_blank"></a>
						</li>
						<?php } ?>
						<?php if(get_option('mvp_linkedin')) { ?>
						<li class="linkedin-item">
							<a href="http://www.linkedin.com/company/<?php echo get_option('mvp_linkedin'); ?>" alt="Linkedin" class="linkedin-but" target="_blank"></a>
						</li>
						<?php } ?>
						<?php if(get_option('mvp_rss')) { ?>
						<li><a href="<?php echo get_option('mvp_rss'); ?>" alt="RSS Feed" class="rss-but"></a></li>
						<?php } else { ?>
						<li><a href="<?php bloginfo('rss_url'); ?>" alt="RSS Feed" class="rss-but"></a></li>
						<?php } ?>
					</ul>
				</div><!--content-social-->

		<?php

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = strip_tags( $new_instance['number'] );


		return $instance;
	}


	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => 'Title');
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:90%;" />
		</p>

		<!-- Social buttons -->
		<p>Social media buttons will automatically appear once you enter your information in the "Social Media Options" section of the <a href="../wp-admin/themes.php?page=siteoptions">Theme Options.</a></p>


	<?php
	}
}

?>