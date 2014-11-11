# Carrington Build for Developers

---


## Enabling Build on Other Post Types

By default the Carrington Build admin is only enabled on pages. To enable the Carrington Build admin on posts or on custom post types.

	function my_build_admin_filter($types) {
		$types = array_merge($types, array('post', 'my-post-type'));
		return $types;
	}
	add_filter('cfct-build-enabled-post-types', 'my_build_admin_filter');

---


## Disabling Carrington Build

Carrington Build can be disabled by defining a constant `CFCT_BUILD_DISABLE` and setting that constant to true. The constant needs to be defined BEFORE WordPress `init`.

---


## WordPress VIP

WordPress VIP has issues (we'll call them that to make ourselves feel better) with enqueuing admin scripts to a CDN for caching and whatnot. This doesn't play well with our dynamic gathering of JS & CSS resources. The enqueing of scripts to the admin can be overridden by defining a function that manually puts the admin script tags in to the head of the document. For example:

	if (!function_exists('cfct_build_admin_scripts')) {
		function cfct_build_admin_scripts() {
			echo '
				<script type="text/javascript" src="'.admin_url('?cfct_action=cfct_admin_js&amp;ver='.CFCT_BUILD_VERSION).'"></script>
				<link rel="stylesheet" href="'.admin_url('?cfct_action=cfct_admin_css&amp;ver='.CFCT_BUILD_VERSION).'" type="text/css" media="screen" />
			';
		}
	}

The function name `cfct_build_admin_scripts` is the important part, you can do whatever you want inside of it. The function must be defined before `init`.

---

## Registering Rows and Modules to use specific options

To opt into specific options for a custom row or module, you need to specify the classname of the option in the row or module constructor as an array of "extras" like so.

### Modules

	class cfct_module_image extends cfct_build_module {
		...
		public function __construct() {
			$opts = array(
				'description' => __('Add an image from the media library.', 'carrington-build'),
				'icon' => 'image/icon.png',
				'extras' => array("class_of_option", "class_of_another_option"),
			);
			parent::__construct('cfct-module-image', __('Image', 'carrington-build'), $opts);
		}
		...
	}

### Rows

	class cfct_row_ab extends cfct_build_row {
		...
		public function __construct() {
			$config = array(
				'name' => __('2 Columns', 'carrington-build'),
				'description' => __('A 2 column row.', 'carrington-build'),
				'icon' => '2col/icon.png',
				'extras' => array("class_of_option", "class_of_another_option"),
			);

			...
			parent::__construct($config);
		}
		...
	}

You can also override the row and module function `get_extras` if you wish to take that approach

	class custom_row extends cfct_build_row {
		...
		public function get_extras() {
			// remove option
			return array_diff(parent::get_extras(), array('cfct_removed_option'));
		}
	}


There are also 2 new filters that allow you to globally add or remove row or module options.

    cfct-build-module-options
    cfct-build-row-options

These filter the list of enabled options for each row, and are additionally passed the current row class.  This is so you can conditionally add/remove row/module optinos on a per class basis.
---
