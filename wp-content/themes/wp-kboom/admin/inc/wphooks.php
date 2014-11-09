<?php
/*********************************************************************************************

WP_Hooks - Enqueue Javascripts

 *********************************************************************************************/
function site5framework_header_init() {
    if (!is_admin()) {

        wp_enqueue_script( 'jquery' );


        wp_register_script( 'modernizr', get_template_directory_uri().'/library/js/modernizr-2.6.1.min.js');
        wp_enqueue_script( 'modernizr' );
        wp_enqueue_script( 'selectivizr', get_template_directory_uri().'/library/js/libs/selectivizr-min.js');

        wp_enqueue_script( 'superfishjs', get_template_directory_uri().'/library/js/superfish.js',  array( 'jquery' ));
        wp_enqueue_script( 'prettyphotojs', get_template_directory_uri().'/library/js/prettyphoto/jquery.prettyPhoto.js',  array( 'jquery' ));
        //wp_enqueue_script('tiptipjs', get_template_directory_uri().'/library/js/tiptip/jquery.tipTip.js', array( 'jquery' ));
        wp_enqueue_script('buttonsjs', get_template_directory_uri().'/lib/shortcodes/js/buttons.js');
        wp_enqueue_script('quovolverjs', get_template_directory_uri().'/lib/shortcodes/js/jquery.quovolver.js', array( 'jquery' ));
        wp_enqueue_script('cyclejs', get_template_directory_uri().'/lib/shortcodes/js/jquery.cycle.all.min.js', array( 'jquery' ));
        wp_enqueue_script( 'behaviours-js', get_template_directory_uri() .'/library/js/behaviours.js', array( 'jquery' ), false, true );

        wp_enqueue_style('superfish', get_template_directory_uri().'/library/css/superfish.css');
        wp_enqueue_style('tiptip', get_template_directory_uri().'/library/js/tiptip/tipTip.css');
        wp_enqueue_style('prettyphoto', get_template_directory_uri().'/library/js/prettyphoto/css/prettyPhoto.css');
        wp_enqueue_style('normalize', get_template_directory_uri().'/library/css/normalize.css');
        wp_enqueue_style('boxes', get_template_directory_uri().'/lib/shortcodes/css/boxes.css');
        wp_enqueue_style('lists', get_template_directory_uri().'/lib/shortcodes/css/lists.css');
        wp_enqueue_style('social', get_template_directory_uri().'/lib/shortcodes/css/social.css');
        wp_enqueue_style('slider', get_template_directory_uri().'/lib/shortcodes/css/slider.css');
        wp_enqueue_style('viewers', get_template_directory_uri().'/lib/shortcodes/css/viewers.css');
        wp_enqueue_style('tabs', get_template_directory_uri().'/lib/shortcodes/css/tabs.css');
        wp_enqueue_style('toggles', get_template_directory_uri().'/lib/shortcodes/css/toggles.css');
        wp_enqueue_style('button', get_template_directory_uri().'/lib/shortcodes/css/buttons.css');
        wp_enqueue_style('social-icons', get_template_directory_uri().'/lib/shortcodes/css/social-icons.css');
        wp_enqueue_style('columns', get_template_directory_uri().'/lib/shortcodes/css/columns.css');

    }
}
add_action('init', 'site5framework_header_init', 0);




/*********************************************************************************************

Admin Hooks / Portfolio and Slider Media Uploader

 *********************************************************************************************/
function site5framework_mediauploader_init() {
    if (is_admin()) {
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_enqueue_style('thickbox');
        wp_enqueue_script('site5mediauploader', get_template_directory_uri().'/admin/js/site5mediauploader.js', array('jquery'));
    }
}
add_action('init', 'site5framework_mediauploader_init');


/*********************************************************************************************

Favicon

 *********************************************************************************************/
function site5framework_custom_shortcut_favicon() {
    if (of_get_option('sc_custom_shortcut_favicon') != '') {
        echo '<link rel="shortcut icon" href="'. of_get_option('sc_custom_shortcut_favicon') .'" type="image/ico" />'."\n";
    }
    else { ?><link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri() ?>/images/ico/favicon.ico" type="image/ico" />
    <?php }
}
add_action('wp_head', 'site5framework_custom_shortcut_favicon');

/*********************************************************************************************

Contact Form JS

 *********************************************************************************************/
function site5framework_contactform_init() {
    if (is_page_template('template.contact.form.php') && !is_admin()) {
        wp_enqueue_script('contactform', get_template_directory_uri().'/library/js/contactform/contactform.js', array('jquery'), '1.0');
    }
}
add_action('template_redirect', 'site5framework_contactform_init');

/*********************************************************************************************

Portfolio Scripts and CSS Load

 *********************************************************************************************/
function site5framework_portfolio1_init() {
    if (is_page_template('template.portfolio.4cols.php') || is_page_template('template.portfolio.3cols.sidebar.R.php') || is_page_template('template.portfolio.3cols.sidebar.L.php') || is_page_template('template.portfolio.3cols.php') || is_page_template('template.portfolio.2cols.php')
        || is_page_template('template.audio.4cols.php') || is_page_template('template.audio.3cols.php') || is_page_template('template.audio.2cols.php') || is_page_template('template.video.4cols.php') || is_page_template('template.video.3cols.php') || is_page_template('template.video.2cols.php')   &&  !is_admin()) {
        wp_enqueue_script('quicksand-js', get_template_directory_uri().'/library/js/quicksand.js', array('jquery'), '1.0');
    }
}
add_action('template_redirect', 'site5framework_portfolio1_init');

/*********************************************************************************************

Stats

 *********************************************************************************************/
function site5framework_analytics(){
    $output = of_get_option('sc_stats');
    if ( $output <> "" )
        echo stripslashes($output) . "\n";
}
add_action('wp_footer','site5framework_analytics');
?>