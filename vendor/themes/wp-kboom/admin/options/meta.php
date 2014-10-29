<?php
    $options[] = array( "name" => "Meta",
    					"sicon" => "metatag.png",
						"type" => "heading");

    $options[] = array( "name" => "Active Meta Keywords, Description Revisit",
                        "id" => $shortname."_enablemeta",
                        "std" => "1",
                        "type" => "checkbox");

    $options[] = array( "name" => "Meta Description",
						"id" => $shortname."_metadescription",
						"std" => "full functionable, premium wordpress theme solution for your website.",
						"type" => "textarea");

    $options[] = array( "name" => "Meta Keywords",
						"std" => "proffesional wordpress theme, flexible wordpress theme, wordpress all in one theme, premium wordpress theme ",
						"id" => $shortname."_metakeywords",
                        "type" => "textarea");

    $options[] = array( "name" => "Revisit After",
                        "id" => $shortname."_revisitafter",
                        "std" => "2",
                        "type" => "select",
                        "class" => "tiny", //mini, tiny, small
						"class" => "sectionlast",
                        "options" => $numberofs_array);

    $options[] = array( "name" => "Active Robots Indexing Option",
                        "id" => $shortname."_enablerobot",
                        "std" => "1",
                        "type" => "checkbox");

    $options[] = array( "name" => "Choose General Bot Indexing Type",
						"id" => $shortname."_metabots",
                        "std" => "",
						"type" => "select",
						"class" => "tiny", //mini, tiny, small
						"options" => $robots_array);

    $options[] = array( "name" => "Choose Google Bot Indexing Type",
						"id" => $shortname."_metagooglebot",
                        "std" => "",
						"type" => "select",
						"class" => "tiny", //mini, tiny, small
						"options" => $robots_array);



?>