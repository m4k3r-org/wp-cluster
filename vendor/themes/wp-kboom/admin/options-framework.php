<?php
/* Basic plugin definitions */

define('OPTIONS_FRAMEWORK_VERSION', '0.6');

/* Make sure we don't expose any info if called directly */

if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a little plugin, don't mind me.";
	exit;
}

/* If the user can't edit theme options, no use running this plugin */

add_action('init', 'optionsframework_rolescheck' );

function optionsframework_rolescheck () {
	if ( current_user_can('edit_theme_options') ) {
		// If the user can edit theme options, let the fun begin!
		add_action('admin_menu', 'optionsframework_add_page');
		add_action('admin_init', 'optionsframework_init' );
		add_action( 'admin_init', 'optionsframework_mlu_init' );
	}
}

/*
 * Creates the settings in the database by looping through the array
 * we supplied in options.php.  This is a neat way to do it since
 * we won't have to save settings for headers, descriptions, or arguments-
 * and it makes it a little easier to change and set up in my opinion.
 *
 * Read more about the Settings API in the WordPress codex:
 * http://codex.wordpress.org/Settings_API
 *
 */

function optionsframework_init() {

	// Include the required files
	require_once dirname( __FILE__ ) . '/options-sanitize.php';
	require_once dirname( __FILE__ ) . '/options-interface.php';
	require_once dirname( __FILE__ ) . '/options-medialibrary-uploader.php';

	// Loads the options array from the theme
	/*if ( $optionsfile = locate_template( array('options.php') ) ) {
		$optionsfile = locate_template( $location );
	}
	else if (file_exists( dirname( __FILE__ ) . '/options.php' ) ) {
		require_once dirname( __FILE__ require_once dirname( __FILE__ ) . '/options-sanitize.php';) . '/options.php';
	}*/

	$optionsframework_settings = get_option('optionsframework');

	// Updates the unique option id in the database if it has changed
	optionsframework_option_name();

	// Gets the unique id, returning a default if it isn't defined
	$option_name = $optionsframework_settings['id'];

	// Set the option defaults in case they have changed
	optionsframework_setdefaults();

	// Registers the settings fields and callback
	register_setting('optionsframework', $option_name, 'optionsframework_validate' );
}

/*
 * Adds default options to the database if they aren't already present.
 * May update this later to load only on plugin activation, or theme
 * activation since most people won't be editing the options.php
 * on a regular basis.
 *
 * http://codex.wordpress.org/Function_Reference/add_option
 *
 */

function optionsframework_setdefaults() {

	$optionsframework_settings = get_option('optionsframework');

	// Gets the unique option id
	$option_name = $optionsframework_settings['id'];

	/*
	 * Each theme will hopefully have a unique id, and all of its options saved
	 * as a separate option set.  We need to track all of these option sets so
	 * it can be easily deleted if someone wishes to remove the plugin and
	 * its associated data.  No need to clutter the database.
	 *
	 */

	if ( isset($optionsframework_settings['knownoptions']) ) {
		$knownoptions =  $optionsframework_settings['knownoptions'];
		if ( !in_array($option_name, $knownoptions) ) {
			array_push( $knownoptions, $option_name );
			$optionsframework_settings['knownoptions'] = $knownoptions;
			update_option('optionsframework', $optionsframework_settings);
		}
	} else {
		$newoptionname = array($option_name);
		$optionsframework_settings['knownoptions'] = $newoptionname;
		update_option('optionsframework', $optionsframework_settings);
	}

	// Gets the default options data from the array in options.php
	$options = optionsframework_options();

	// If the options haven't been added to the database yet, they are added now
	foreach ($options as $option) {

		if ( ($option['type'] != 'heading') && ($option['type'] != 'info') ) {
			$option_id = preg_replace('/\W/', '', strtolower($option['id']) );

			// wp_filter_post_kses for strings
			if (isset($option['std' ]) ) {
				if ( !is_array($option['std' ]) ) {
					$values[$option_id] = wp_filter_post_kses($option['std']);
				} else {
					foreach ($option['std' ] as $key => $value) {
						$optionarray[$key] = wp_filter_post_kses($value);
					}
					$values[$option_id] = $optionarray;
					unset($optionarray);
				}
			} else {
				$value = '';
			}
		}
	}

	if ( isset($values) ) {
		add_option($option_name, $values);
	}
}

/* Add a subpage called "Theme Options" to the appearance menu. */

if ( !function_exists( 'optionsframework_add_page' ) ) {
function optionsframework_add_page() {

	$of_page = add_menu_page('Theme Options', 'Theme Options', 'edit_theme_options', 'options-framework','optionsframework_page');

	// Adds actions to hook in the required css and javascript
	add_action("admin_print_styles-$of_page",'optionsframework_load_styles');
	add_action("admin_print_scripts-$of_page", 'optionsframework_load_scripts');

}
}

/* Loads the CSS */

function optionsframework_load_styles() {
	wp_enqueue_style('admin-style', OPTIONS_FRAMEWORK_DIRECTORY .'css/admin-style.css');
	wp_enqueue_style('color-picker', OPTIONS_FRAMEWORK_DIRECTORY .'css/colorpicker.css');
}

/* Loads the javascript */

function optionsframework_load_scripts() {

	// Inline scripts from options-interface.php
	add_action('admin_head', 'of_admin_head');

	// Enqueued scripts
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('color-picker', OPTIONS_FRAMEWORK_DIRECTORY . 'js/colorpicker.js', array('jquery'));
	wp_enqueue_script('options-custom', OPTIONS_FRAMEWORK_DIRECTORY . 'js/options-custom.js', array('jquery'));
}

function of_admin_head() {

	// Hook to add custom scripts
	do_action( 'optionsframework_custom_scripts' );
}

/*
 * Builds out the options panel.
 *
 * If we were using the Settings API as it was likely intended we would use
 * do_settings_sections here.  But as we don't want the settings wrapped in a table,
 * we'll call our own custom optionsframework_fields.  See options-interface.php
 * for specifics on how each individual field is generated.
 *
 * Nonces are provided using the settings_fields()
 *
 */

if ( !function_exists( 'optionsframework_page' ) ) {
function optionsframework_page() {

	// Get the theme name so we can display it up top
	$themename = get_theme_data(get_stylesheet_directory() . '/style.css');
	$themename = $themename['Name'];
	settings_errors();
	?>

	<div class="wrap">
    <?php screen_icon( 'themes' ); ?>

    <div id="of_container">
       <form action="options.php" method="post">
	  <?php settings_fields('optionsframework'); ?>

        <div id="header">
          <div class="logo">
            <h2><?php esc_html_e( $themename ); ?> <?php esc_html_e( 'Options' ); ?></h2>
          </div>

          <div class="clear"></div>
        </div>
        <div id="main">
        <?php $return = optionsframework_fields(); ?>
          <div id="of-nav">
            <ul>
              <?php echo $return[1]; ?>
            </ul>
          </div>
          <div id="content">
            <?php echo $return[0]; /* Settings */ ?>
          </div>
          <div class="clear"></div>
        </div>
        <div class="of_admin_bar">
			<input type="submit" class="button-primary" name="update" value="<?php esc_attr_e( 'Save Options','site5framework' ); ?>" />
            <input type="submit" class="reset-button button-secondary" name="reset" value="<?php esc_attr_e( 'Restore Defaults','site5framework' ); ?>" onclick="return confirm( '<?php print esc_js( __( 'Click OK to reset. Any theme settings will be lost!','site5framework' ) ); ?>' );" />
		</div>
<div class="clear"></div>
	</form>
</div> <!-- / #container -->
</div> <!-- / .wrap -->

<?php
}
}

/*
 * Data sanitization!
 *
 * This runs after the submit/reset button has been clicked and
 * validates the inputs.
 *
 */

function optionsframework_validate($input) {

	$optionsframework_settings = get_option('optionsframework');

	// Gets the unique option id
	$option_name = $optionsframework_settings['id'];

	// If the reset button was clicked
	if (!empty($_POST['reset'])) {
		// If options are deleted sucessfully update the error message
		if (delete_option($option_name) ) {
			add_settings_error( 'options-framework', 'restore_defaults', __( 'Default options restored.', 'site5framework' ), 'updated fade' );
		}
	}

	else

	{

	if (!empty($_POST['update'])) {

		$clean = array();

		// Get the options array we have defined in options.php
		$options = optionsframework_options();

		foreach ($options as $option) {

			// Verify that the option has an id
			if ( isset ($option['id']) ) {

				// Keep all ids lowercase with no spaces
				$id = preg_replace( '/\W/', '', strtolower( $option['id'] ) );

				// Set checkbox to false if it wasn't sent in the $_POST
				if ( 'checkbox' == $option['type'] && ! isset( $input[$id] ) ) {
					$input[$id] = "0";
				}

				// Set each item in the multicheck to false if it wasn't sent in the $_POST
				if ( 'multicheck' == $option['type'] && ! isset( $input[$id] ) ) {
					foreach ( $option['options'] as $key => $value ) {
						$input[$id][$key] = "0";
					}
				}

				// For a value to be submitted to database it must pass through a sanitization filter
				if ( isset ( $input[$id] ) && has_filter('of_sanitize_' . $option['type']) ) {
					$clean[$id] = apply_filters( 'of_sanitize_' . $option['type'], $input[$id], $option );
				}

			} // end isset $input

		} // end isset $id

	} // end foreach

	if ( isset($clean) ) {
		add_settings_error( 'options-framework', 'save_options', __( 'Options saved.', 'site5framework' ), 'updated fade' );
		return $clean; // Return validated input
	}

	} // end $_POST['update']

}


/*
 * Helper function to return the theme option value. If no value has been saved, it returns $default.
 * Needed because options are saved as serialized strings.
 *
 */

if ( !function_exists( 'of_get_option' ) ) {
function of_get_option($name, $default = false) {

	$optionsframework_settings = get_option('optionsframework');

	// Gets the unique option id
	$option_name = $optionsframework_settings['id'];

	if ( get_option($option_name) ) {
		$options = get_option($option_name);
	}

	if ( !empty($options[$name]) ) {
		return $options[$name];
	} else {
		return $default;
	}
}
}

/**
 * Add Theme Options menu item to Admin Bar.
 */

add_action( 'wp_before_admin_bar_render', 'optionsframework_adminbar' );

function optionsframework_adminbar() {

	global $wp_admin_bar;

	$wp_admin_bar->add_menu( array(
		'parent' => 'appearance',
		'id' => 'of_theme_options',
		'title' => __( 'Theme Options','site5framework' ),
		'href' => admin_url( 'themes.php?page=options-framework' )
  ));
}