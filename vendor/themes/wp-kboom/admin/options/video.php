<?php
    $options[] = array( "name" => "Video",
    					"sicon" => "video.png",
						"type" => "heading");

    $options[] = array( "name" => "Display Filterable on Video page",
                        "desc" => "do you want to display filterable section on Video page ?",
                        "id" => $shortname."_video_filterableon",
                        "std" => "1",
                        "type" => "checkbox");

    $options[] = array( "name" => "Choose the filtering type on Video page",
                        "desc" => "",
                        "id" => $shortname."_portfoliofilters",
                        "type" => "select",
                        "std"  => "javascript",
                        "options" => array(
                        	/*'regular'=>'Regular filtering (with page reload)',*/
                        	'javascript'=>'Javascript Filtering (without page reload)'
                        	)
                        );
    $options[] = array( "name" => "Video Item per Page",
                        "desc" => "Set the number of items that appear on the Video page.",
                        "id" => $shortname."_videoitemsperpage",
                        "std" => "6",
                        "type" => "text");
?>