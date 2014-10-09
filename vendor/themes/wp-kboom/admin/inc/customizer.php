<?php

function site5framework_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

}
add_action( 'customize_register', 'site5framework_customize_register' );

/**
 * Bind JS handlers to make Theme Customizer preview reload changes asynchronously.
 * Used with blogname and blogdescription.
 */
function site5framework_customize_preview_js() {
	wp_enqueue_script( 'site5framework-customizer', get_template_directory_uri() . '/admin/js/theme-customizer.js', array( 'customize-preview' ), '20120523', true );
}
add_action( 'customize_preview_init', 'site5framework_customize_preview_js' );


?>