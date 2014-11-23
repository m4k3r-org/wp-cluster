<?php
/**
 * Plugin Name: 125x125 Ads Widget
 */

add_action( 'widgets_init', 'mvp_ad125_load_widgets' );

function mvp_ad125_load_widgets() {
	register_widget( 'mvp_ad125_widget' );
}

class mvp_ad125_widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function mvp_ad125_widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'mvp_ad125_widget', 'description' => __('A widget that displays four 125x125 ads.', 'mvp_ad125_widget') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'mvp_ad125_widget' );

		/* Create the widget. */
		$this->WP_Widget( 'mvp_ad125_widget', __('Braxton: 125x125 Ads Widget', 'mvp_ad125_widget'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$code1 = $instance['code1'];
		$code2 = $instance['code2'];
		$code3 = $instance['code3'];
		$code4 = $instance['code4'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		?>
			<h4 class="ad-header"><?php _e( 'Advertisement', 'mvp-text' ); ?></h4>
			<div class="ad125-wrapper">
				<ul class="ad125">
					<li class="ad125-1"><?php echo $code1; ?></li>
					<li class="ad125-2"><?php echo $code2; ?></li>
					<li class="ad125-3"><?php echo $code3; ?></li>
					<li class="ad125-4"><?php echo $code4; ?></li>
				</ul>
			</div><!--ad125-wrapper-->

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
		$instance['code1'] = $new_instance['code1'];
		$instance['code2'] = $new_instance['code2'];
		$instance['code3'] = $new_instance['code3'];
		$instance['code4'] = $new_instance['code4'];


		return $instance;
	}


	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'code1' => 'Enter ad code here');
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Ad code 1 -->
		<p>
			<label for="<?php echo $this->get_field_id( 'code1' ); ?>">Ad #1 code:</label>
			<textarea id="<?php echo $this->get_field_id( 'code1' ); ?>" name="<?php echo $this->get_field_name( 'code1' ); ?>" style="width:96%;" rows="6"><?php echo $instance['code1']; ?></textarea>
		</p>

		<!-- Ad code 2 -->
		<p>
			<label for="<?php echo $this->get_field_id( 'code2' ); ?>">Ad #2 code:</label>
			<textarea id="<?php echo $this->get_field_id( 'code2' ); ?>" name="<?php echo $this->get_field_name( 'code2' ); ?>" style="width:96%;" rows="6"><?php echo $instance['code2']; ?></textarea>
		</p>

		<!-- Ad code 3 -->
		<p>
			<label for="<?php echo $this->get_field_id( 'code3' ); ?>">Ad #3 code:</label>
			<textarea id="<?php echo $this->get_field_id( 'code3' ); ?>" name="<?php echo $this->get_field_name( 'code3' ); ?>" style="width:96%;" rows="6"><?php echo $instance['code3']; ?></textarea>
		</p>

		<!-- Ad code 4 -->
		<p>
			<label for="<?php echo $this->get_field_id( 'code4' ); ?>">Ad #4 code:</label>
			<textarea id="<?php echo $this->get_field_id( 'code4' ); ?>" name="<?php echo $this->get_field_name( 'code4' ); ?>" style="width:96%;" rows="6"><?php echo $instance['code4']; ?></textarea>
		</p>


	<?php
	}
}

?>