<?php
$options[] = array( "name" => "General",
						"sicon" => "advancedsettings.png",
                        "type" => "heading");


      $options[] = array( "name" => "Your Company Logo",
                        "desc" => "You can use your own company logo. Click to 'Upload Image' button and upload your logo.",
                        "id" => $shortname."_clogo",
                        "std" => "$blogpath/library/images/logo.png",
                        "type" => "upload");
						
	$options[] = array( "name" => "Text as Logo",
                        "desc" => "If you don't upload your own company logo, this text will show up in it's place.",
                        "id" => $shortname."_clogo_text",
                        "std" => "HandsUp",
                        "type" => "text");
	$options[] = array( "name" => "Theme Color Scheme",
                        "id" => $shortname."_colorscheme",
                        "std" => "dark-purple",
                        "type" => "select",
                        "class" => "tiny", //mini, tiny, small
                         "options" => $colorschemes);

    $options[] = array( "name" => "Theme Skin",
                        "desc" => "Display your desired skin.",
                        "id" => $shortname."_skin",
                        "std" => "Dark Skin",
                        "type" => "select",
                        "class" => "tiny", //mini, tiny, small
                        "options" => $lightskin);

	$options[] = array( "name" => "Custom Favicon",
                        "desc" => "You can use your own custom favicon. Click to 'Upload Image' button and upload your favicon.",
                        "id" => $shortname."_custom_shortcut_favicon",
                        "std" => "",
                        "type" => "upload");
?>