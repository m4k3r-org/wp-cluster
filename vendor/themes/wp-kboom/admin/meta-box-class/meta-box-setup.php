<?php

require_once( "meta-box-class.php" );

if (is_admin()){

	wp_enqueue_script('custom-meta-boxes', get_template_directory_uri() . '/admin/js/custom.metaboxes.js', array('jquery'),'', true);

	//All meta boxes prefix
	$prefix = 'sn_';


	//Aside meta box config
	$config1 = array(
	'id' => 'aside_post',          				// meta box id, unique per meta box
	'title' => 'Aside Settings',          	// meta box title
	'pages' => array('post'),      			// post types, accept custom post types as well, default is array('post'); optional
	'context' => 'normal',            		// where the meta box appear: normal (default), advanced, side; optional
	'priority' => 'high',            		// order of meta box: high (default), low; optional
	'fields' => array(),            		// list of meta fields (can be added by field arrays)
	'local_images' => true,          		// Use local or hosted images (meta box images for add/remove)
	'use_with_theme' => true          		//change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	);


	//Initiate aside meta box
	$my_meta1 =  new AT_Meta_Box($config1);


	//Aside fields
	$my_meta1->addTextarea($prefix.'aside_post',array('name'=> 'The Aside ', 'desc'=>'Enter your aside.'));

    //Finish aside meta mox decleration
	$my_meta1->Finish();


	//Quote meta box config
	$config2 = array(
	'id' => 'quote_post',          				// meta box id, unique per meta box
	'title' => 'Quote Settings',          	// meta box title
	'pages' => array('post'),      			// post types, accept custom post types as well, default is array('post'); optional
	'context' => 'normal',            		// where the meta box appear: normal (default), advanced, side; optional
	'priority' => 'high',            		// order of meta box: high (default), low; optional
	'fields' => array(),            		// list of meta fields (can be added by field arrays)
	'local_images' => true,          		// Use local or hosted images (meta box images for add/remove)
	'use_with_theme' => true          		//change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	);


	//Initiate quote meta box
	$my_meta2 =  new AT_Meta_Box($config2);


	//Quote fields
	$my_meta2->addTextarea($prefix.'quote_post',array('name'=> 'The Quote ', 'desc'=>'Enter your quote.'));
	$my_meta2->addText($prefix.'quote_author',array('name'=> 'Quote Author ', 'desc'=>'Enter the quote author name.'));

    //Finish quote meta box decleration
	$my_meta2->Finish();


	//Link meta box config
	$config3 = array(
	'id' => 'link_post',          			// meta box id, unique per meta box
	'title' => 'Link Settings',          	// meta box title
	'pages' => array('post'),      			// post types, accept custom post types as well, default is array('post'); optional
	'context' => 'normal',            		// where the meta box appear: normal (default), advanced, side; optional
	'priority' => 'high',            		// order of meta box: high (default), low; optional
	'fields' => array(),            		// list of meta fields (can be added by field arrays)
	'local_images' => true,          		// Use local or hosted images (meta box images for add/remove)
	'use_with_theme' => true          		//change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	);


	//Initiate link meta box
	$my_meta3 =  new AT_Meta_Box($config3);


	//Link fields
	$my_meta3->addText($prefix.'link_post_url',array('name'=> 'Link URL ', 'desc'=>'Enter the URL to be used for this Link post. for example: http://www.site5.com'));
	$my_meta3->addTextarea($prefix.'link_post_description',array('name'=> 'Link Description ', 'desc'=>'Enter the description to be used for this link. for example: Site5 WordPress Hosting'));

    //Finish link meta box decleration
	$my_meta3->Finish();


	//Image meta box config
	$config4 = array(
	'id' => 'image_post',          				// meta box id, unique per meta box
	'title' => 'Image Settings',          	// meta box title
	'pages' => array('post'),      			// post types, accept custom post types as well, default is array('post'); optional
	'context' => 'normal',            		// where the meta box appear: normal (default), advanced, side; optional
	'priority' => 'high',            		// order of meta box: high (default), low; optional
	'fields' => array(),            		// list of meta fields (can be added by field arrays)
	'local_images' => true,          		// Use local or hosted images (meta box images for add/remove)
	'use_with_theme' => true          		//change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	);


	//Initiate link meta box
	$my_meta4 =  new AT_Meta_Box($config4);


	//Link fields
	//$my_meta4->addImage($prefix.'image_post_preview',array('name'=> 'Upload Image ', 'desc'=>'Enter the URL to be used for this Link post. for example: http://www.site5.com'));
	$my_meta4->addImage($prefix.'image_post_preview',array('name'=> 'Upload Image '));

    //Finish link meta box decleration
	$my_meta4->Finish();



	//Gallery meta box config
	$config5 = array(
	  'id' => 'gallery_post',          		// meta box id, unique per meta box
	  'title' => 'Gallery Settings',        // meta box title
	  'pages' => array('post'),      		// post types, accept custom post types as well, default is array('post'); optional
	  'context' => 'normal',				// where the meta box appear: normal (default), advanced, side; optional
	  'priority' => 'high',            		// order of meta box: high (default), low; optional
	  'fields' => array(),            		// list of meta fields (can be added by field arrays)
	  'local_images' => true,          		// Use local or hosted images (meta box images for add/remove)
	  'use_with_theme' => true          	//change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	);


	//Initiate gallery meta box
	$my_meta5 =  new AT_Meta_Box($config5);


	//Gallery fields
	$repeater_fields[] = $my_meta5->addText($prefix.'gallery_post_image_title',array('name'=> 'Gallery Image Title '),true);
	$repeater_fields[] = $my_meta5->addImage($prefix.'gallery_post_image',array('name'=> 'Gallery Image '),true);

	//Gallery repeater block
	$my_meta5->addRepeaterBlock($prefix.'gl_',array('inline' => true, 'name' => 'Gallery Images','desc'=>'Click to upload images to this gallery post. Hold and move to sort images.', 'fields' => $repeater_fields, 'sortable'=> true));


	//Finish gallery meta mox decleration
	$my_meta5->Finish();


	//Video meta box config
	$config6 = array(
	'id' => 'video_post',          			// meta box id, unique per meta box
	'title' => 'Video Settings',          	// meta box title
	'pages' => array('post'),      			// post types, accept custom post types as well, default is array('post'); optional
	'context' => 'normal',            		// where the meta box appear: normal (default), advanced, side; optional
	'priority' => 'high',            		// order of meta box: high (default), low; optional
	'fields' => array(),            		// list of meta fields (can be added by field arrays)
	'local_images' => true,          		// Use local or hosted images (meta box images for add/remove)
	'use_with_theme' => true          		//change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	);


	//Initiate video meta box
	$my_meta6 =  new AT_Meta_Box($config6);


	//Video fields
	$my_meta6->addText($prefix.'video_post_m4v',array('name'=> 'M4V File URL ', 'desc'=>'Enter the URL to the .m4v video file url.'));
	$my_meta6->addText($prefix.'video_post_ogv',array('name'=> 'OGV File URL ', 'desc'=>'Enter the URL to the .ogv, video file url.'));
	$my_meta6->addImage($prefix.'video_post_poster',array('name'=> 'Upload Video Poster '));
	$my_meta6->addTextarea($prefix.'video_post_embed',array('name'=> 'Embedded Video Code ', 'desc'=>'If you are using something other than self hosted video such as Youtube or Vimeo, paste the embed code here.'));

    //Finish video meta box decleration
	$my_meta6->Finish();


	//Audio meta box config
	$config7 = array(
	'id' => 'audio_post',          			// meta box id, unique per meta box
	'title' => 'Audio Settings',          	// meta box title
	'pages' => array('post'),      			// post types, accept custom post types as well, default is array('post'); optional
	'context' => 'normal',            		// where the meta box appear: normal (default), advanced, side; optional
	'priority' => 'high',            		// order of meta box: high (default), low; optional
	'fields' => array(),            		// list of meta fields (can be added by field arrays)
	'local_images' => true,          		// Use local or hosted images (meta box images for add/remove)
	'use_with_theme' => true          		//change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	);


	//Initiate audio meta box
	$my_meta7 =  new AT_Meta_Box($config7);


	//Audio fields
	$my_meta7->addText($prefix.'audio_post_mp3',array('name'=> 'MP3 File URL ', 'desc'=>'Enter the URL to the .mp3 audio file url.'));
	$my_meta7->addText($prefix.'audio_post_ogg',array('name'=> 'OGG File URL ', 'desc'=>'Enter the URL to the .oga, .ogg audio file url.'));
	$my_meta7->addImage($prefix.'audio_post_poster',array('name'=> 'Upload Audio Poster '));

    //Finish audio meta box decleration
	$my_meta7->Finish();


}