<?php
    $options[] = array( "name" => "Thumbnails",
    					"sicon" => "thumbnail.png",
						"type" => "heading");
						
	$options[] = array( "name" => "Regenerate Thumbnails",
					"desc" => "Click on <a href='".admin_url('admin.php?page=ajax-thumbnail-rebuild')."'>this link</a> to regenerate your existing thumbnails to fit the theme.",
					"id" => $shortname."_thumbnailsinfo",
					"std" => "",
					"type" => "info");
						
		

?>