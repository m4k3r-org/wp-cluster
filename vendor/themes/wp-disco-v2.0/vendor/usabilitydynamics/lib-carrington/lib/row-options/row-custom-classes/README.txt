# Custom Row Classes

This row customization allows for the addition of additional CSS classes to the row DOM element. This row option will add a single text input which accepts space separated class names.

The row option uses the filter `cfct-build-row-class` to add its classes to the row's existing classes.


## Predefined Classes

This row option also has the ability to include pre-defined class names as options in a fly out menu. By using the filter `cfct-row-predefined-class-options`. Classes added via this method will be available by a dropdown menu in the row extra config area.
	
Example:

	function my_predefined_classes($classes) {
		if (empty($classes)) {
			$classes = array();
		}
		$classes = array_merge($classes, array(
			'cfct-custom-one' => 'Custom class one',
			'cfct-custom-two' => 'Custom class two'
		));
		return $classes;
	}
	add_filter('cfct-row-predefined-class-options','my_predefined_classes');
