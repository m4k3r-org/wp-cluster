<?php
    $options[] = array( "name" => "Audio",
    					"sicon" => "audio.png",
						"type" => "heading");

    $options[] = array( "name" => "Display Filterable on Audio page",
                        "desc" => "do you want to display filterable section on audio page ?",
                        "id" => $shortname."_audio_filterableon",
                        "std" => "1",
                        "type" => "checkbox");

    $options[] = array( "name" => "Choose the filtering type on Audio page",
                        "desc" => "",
                        "id" => $shortname."_portfoliofilters",
                        "type" => "select",
                        "std"  => "javascript",
                        "options" => array(
                        	/*'regular'=>'Regular filtering (with page reload)',*/
                        	'javascript'=>'Javascript Filtering (without page reload)'
                        	)
                        );
    $options[] = array( "name" => "Audio Item per Page",
                        "desc" => "Set the number of items that appear on the Audio page.",
                        "id" => $shortname."_audioitemsperpage",
                        "std" => "6",
                        "type" => "text");
?>