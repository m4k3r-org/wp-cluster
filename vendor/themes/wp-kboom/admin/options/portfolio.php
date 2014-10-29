<?php
    $options[] = array( "name" => "Portfolio",
    					"sicon" => "portfolio-32x32.png",
						"type" => "heading");

    $options[] = array( "name" => "Display Filterable on Portfolio page",
                        "desc" => "do you want to display filterable section on portfolio page ?",
                        "id" => $shortname."_filterableon",
                        "std" => "1",
                        "type" => "checkbox");

    $options[] = array( "name" => "Choose the filtering type on Portfolio page",
                        "desc" => "",
                        "id" => $shortname."_portfoliofilters",
                        "type" => "select",
                        "std"  => "javascript",
                        "options" => array(
                        	/*'regular'=>'Regular filtering (with page reload)',*/
                        	'javascript'=>'Javascript Filtering (without page reload)'
                        	)
                        );
    $options[] = array( "name" => "Portfolio Item per Page",
                        "desc" => "Set the number of items that appear on the Portfolio page.",
                        "id" => $shortname."_portfolioitemsperpage",
                        "std" => "6",
                        "type" => "text");
?>