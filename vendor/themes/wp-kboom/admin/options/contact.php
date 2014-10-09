<?php
    $options[] = array( "name" => "Contact",
    					"sicon" => "mail.png",
                        "type" => "heading");

    $options[] = array( "name" => "Display Content  on Contact Page",
                        "desc" => "do you want to display content on contact page ?",
                        "id" => $shortname."_contactcontent",
                        "std" => "1",
                        "type" => "checkbox");

    $options[] = array( "name" => "Display Contact Address",
                        "id" => $shortname."_displaycaddress",
                        "std" => "1",
                        "type" => "checkbox");

    $options[] = array( "name" => "Display Contact Phone",
                        "id" => $shortname."_displaycphone",
                        "std" => "1",
                        "type" => "checkbox");

    $options[] = array( "name" => "Display Contact Fax",
                        "id" => $shortname."_displaycfax",
                        "std" => "1",
                        "type" => "checkbox");

    $options[] = array( "name" => "Display Contact E-Mail ",
                        "id" => $shortname."_displaycemail",
                        "std" => "1",
                        "type" => "checkbox");

	$options[] = array( "name" => "Contact Address",
                        "id" => $shortname."_contact_address",
                        "std" => "2736 Luke Lane South Bend, IN 46601",
                        "type" => "text");

    $options[] = array( "name" => "Contact Phone",
                        "id" => $shortname."_contact_phone",
                        "std" => "+1 223-445-6678",
                        "type" => "text");
						
	$options[] = array( "name" => "Contact Fax",
                        "id" => $shortname."_contact_fax",
                        "std" => "+1 223-445-0000",
                        "type" => "text");
						
	$options[] = array( "name" => "Contact E-Mail",
                        "id" => $shortname."_contact_email",
                        "std" => "dummy@yoursite.com",
                        "type" => "text");

    $options[] = array( "name" => "Contact Map",
                        "id" => $shortname."_contact_map",
                        "std" => "",
                        "type" => "textarea");

?>