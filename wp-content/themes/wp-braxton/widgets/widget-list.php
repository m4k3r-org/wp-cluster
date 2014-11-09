<?php
/**
 * Plugin Name: List Widget
 */

add_action( 'widgets_init', 'mvp_list_load_widgets' );

function mvp_list_load_widgets() {
	register_widget( 'mvp_list_widget' );
}

class mvp_list_widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function mvp_list_widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'mvp_list_widget', 'description' => __('A widget that displays posts in a list from a category of your choice.', 'mvp_list_widget') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'mvp_list_widget' );

		/* Create the widget. */
		$this->WP_Widget( 'mvp_list_widget', __('Braxton: List Widget', 'mvp_list_widget'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$categories = $instance['categories'];
		$number = $instance['number'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		?>


					<ul class="home-list">
						<?php $recent = new WP_Query(array( 'cat' => $categories, 'posts_per_page' => $number )); while($recent->have_posts()) : $recent->the_post(); ?>
						<li>
							<a href="<?php the_permalink(); ?>" rel="bookmark">
							<div class="home-list-img">
								<?php if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail())  ) { ?>
								<?php the_post_thumbnail('medium-thumb'); ?>
								<?php } ?>
							</div><!--home-list-img-->
							<div class="home-list-content">
								<span class="widget-info"><span class="widget-author"><?php the_author(); ?></span> | <?php the_time(get_option('date_format')); ?></span>
								<h2><?php the_title(); ?></h2>
								<p><?php echo excerpt(19); ?></p>
							</div><!--home-list-content-->
							</a>
						</li>
						<?php endwhile; ?>
					</ul>


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
		$instance['categories'] = $new_instance['categories'];
		$instance['number'] = strip_tags( $new_instance['number'] );


		return $instance;
	}


	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => 'Title', 'links' => 'on', 'number' => 4);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:90%;" />
		</p>

		<!-- Category -->
		<p>
			<label for="<?php echo $this->get_field_id('categories'); ?>">Select category:</label>
			<select id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>" style="width:100%;">
				<option value='all' <?php if ('all' == $instance['categories']) echo 'selected="selected"'; ?>>All Categories</option>
				<?php $categories = get_categories('hide_empty=0&depth=1&type=post'); ?>
				<?php foreach($categories as $category) {  ?>
				<option value='<?php echo $category->term_id; ?>' <?php if ($category->term_id == $instance['categories']) echo 'selected="selected"'; ?>><?php echo $category->cat_name; ?></option>
				<?php } ?>
			</select>
		</p>

		<!-- Number of posts -->
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>">Number of posts to display:</label>
			<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo $instance['number']; ?>" size="3" />
		</p>


	<?php
	}
}

?>