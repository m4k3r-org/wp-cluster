<?php
/*********************************************************************************************

This theme uses wp_nav_menu() in one location.

*********************************************************************************************/
register_nav_menus(                      		// wp3+ menus
		array(
			'main_nav' => 'The Main Menu',   // main nav in header
		)
	);
	
function site5_main_nav() {
	// display the wp3 menu if available
    wp_nav_menu(
    	array(
    		'menu' => 'main_nav', /* menu name */
    		'theme_location' => 'main_nav', /* where in the theme it's assigned */
			'container' =>false,
			'menu_class' => 'sf-menu', /* menu class */
    		'fallback_cb' => 'site5_main_nav_fallback' /* menu fallback */
    	)
    );
}

// this is the fallback for header menu
function site5_main_nav_fallback() {
	echo'<ul class="sf-menu">';
    wp_list_pages( 'title_li=&show_home=Home' );
	echo'</ul>';
}

?>