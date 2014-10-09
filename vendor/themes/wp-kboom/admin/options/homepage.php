<?php
	$options[] = array( "name" => "Homepage",
	                    "sicon" => "user-home.png",
	                    "type" => "heading");
						
	$options[] = array( "name" => "Display Content Boxes on Homepage",
						"id" => $shortname."_homecontent",
						"std" => "1",
						"type" => "checkbox");
						
	$options[] = array( "name" => "Content Box 1 Title",
                        "id" => $shortname."_homecontent1title",
                        "std" => "THE WHITE NIGHT",
                        "type" => "text");
						
	$options[] = array( "name" => "Content Box 1 Text",
                        "id" => $shortname."_homecontent1",
                        "std" => "Fusce suscipit varius mi. Cum sociis natoque penatibus et magnis.",
                        "type" => "textarea");
						
	$options[] = array( "name" => "Content Box 1 Image",
                        "desc" => "Click to 'Upload Image' button and upload Content Box 1 image.",
                        "id" => $shortname."_homecontent1img",
                        "std" => "$blogpath/library/images/sampleimages/featured-icon-01.png",
                        "type" => "upload");
						
	$options[] = array( "name" => "Content Box 1 URL",
                        "id" => $shortname."_homecontent1url",
                        "std" => "#",
						"class" => "sectionlast",
                        "type" => "text");
					
	$options[] = array( "name" => "Content Box 2 Title",
                        "id" => $shortname."_homecontent2title",
                        "std" => "TONS OF FANS",
                        "type" => "text");

	$options[] = array( "name" => "Content Box 2 Text",
                        "id" => $shortname."_homecontent2",
                        "std" => "Fusce suscipit varius mi. Cum sociis natoque penatibus et magnis.",
                        "type" => "textarea");
						
	$options[] = array( "name" => "Content Box 2 Image",
                        "desc" => "Click to 'Upload Image' button and upload Content Box 2 image.",
                        "id" => $shortname."_homecontent2img",
                        "std" => "$blogpath/library/images/sampleimages/featured-icon-02.png",
                        "type" => "upload");
						
	$options[] = array( "name" => "Content Box 2 URL",
                        "id" => $shortname."_homecontent2url",
                        "std" => "#",
						"class" => "sectionlast",
                        "type" => "text");	

	$options[] = array( "name" => "Content Box 3 Title",
                        "id" => $shortname."_homecontent3title",
                        "std" => "BEST DJ'S EVER",
                        "type" => "text");
	
	$options[] = array( "name" => "Content Box 3",
                        "id" => $shortname."_homecontent3",
                        "std" => "Fusce suscipit varius mi. Cum sociis natoque penatibus et magnis.",
                        "type" => "textarea");
						
	$options[] = array( "name" => "Content Box 3 Image",
                        "desc" => "Click to 'Upload Image' button and upload Content Box 3 image.",
                        "id" => $shortname."_homecontent3img",
                        "std" => "$blogpath/library/images/sampleimages/featured-icon-03.png",
                        "type" => "upload");
						
	$options[] = array( "name" => "Content Box 3 URL",
                        "id" => $shortname."_homecontent3url",
                        "std" => "#",
						"class" => "sectionlast",
                        "type" => "text");

    $options[] = array( "name" => "Display Latest Albums & Music Player",
						"desc" => "do you want to display this section on homepage ?",
						"id" => $shortname."_portfoliohome",
						"std" => "1",
						"type" => "checkbox");
	
	$options[] = array( "name" => "Music Albums Section Title",
                        "id" => $shortname."_portfoliohometitle",
                        "std" => "LATEST ALBUMS",
                        "class" => "sectionlast",
                        "type" => "text");
    $options[] = array( "name" => "Player Section Title",
                        "id" => $shortname."_playerhometitle",
                        "std" => "AUDIO PLAYER",
                        "type" => "text");

    $options[] = array( "name" => "Audio Post Name",
                        "desc" => "Write the audio post name, (last word from permalink highlighted with yellow)",
                        "id" => $shortname."_audio_post_id",
                        "std" => "",
                        "class" => "sectionlast",
                        "type" => "text");

    $options[] = array( "name" => "Display Events",
                        "desc" => "do you want to display events section on homepage ?",
                        "id" => $shortname."_eventshome",
                        "std" => "1",
                        "type" => "checkbox");

    $options[] = array( "name" => "Events Section Title",
                        "id" => $shortname."_eventshometitle",
                        "std" => "UPCOMING EVENTS",
                        "type" => "text");

    $options[] = array( "name" => "Music Events Items On Homepage",
                        "desc" => " Set the number of music events items that appear on the homepage page.",
                        "id" => $shortname."_eventhomeitemsperpage",
                        "std" => "8",
                        "class" => "sectionlast",
                        "type" => "text");

    $options[] = array( "name" => "Display Blog Posts",
                        "desc" => "do you want to display blog posts section on homepage ?",
                        "id" => $shortname."_bloghome",
                        "std" => "1",
                        "type" => "checkbox");

    $options[] = array( "name" => "Blog Section Title",
                        "id" => $shortname."_bloghometitle",
                        "std" => "LATEST BLOG",
                        "type" => "text");

    $options[] = array( "name" => "Blogs Posts On Homepage",
                        "desc" => " Set the number of blogs posts that appear on the homepage page.",
                        "id" => $shortname."_blogpostsperpage",
                        "std" => "2",
                        "class" => "sectionlast",
                        "type" => "text");

    $options[] = array( "name" => "Display Video",
                        "desc" => "do you want to display videos section on homepage ?",
                        "id" => $shortname."_videohome",
                        "std" => "1",
                        "type" => "checkbox");

    $options[] = array( "name" => "Video Section Title",
                        "id" => $shortname."_videohometitle",
                        "std" => "LATEST VIDEO",
                        "type" => "text");

    $options[] = array( "name" => "Video Post Name",
                        "desc" => "Write the video post name, (last word from permalink highlighted with yellow)",
                        "id" => $shortname."_video_post_id",
                        "std" => "",
                        "type" => "text");

    $options[] = array( "name" => "Video Items On Homepage",
                        "desc" => " Set the number of video items that appear on the homepage page.",
                        "id" => $shortname."_videopostsperhomepage",
                        "std" => "1",
                        "class" => "sectionlast",
                        "type" => "text");

    $options[] = array( "name" => "Display SoundCloud Section",
                        "desc" => "do you want to display soundcloud section on homepage ?",
                        "id" => $shortname."_soundcloudhome",
                        "std" => "1",
                        "type" => "checkbox");

    $options[] = array( "name" => "SoundCloud Section Title",
                        "id" => $shortname."_soundcloudhometitle",
                        "std" => "SOUND CLOUD",
                        "type" => "text");

    $options[] = array( "name" => "SoundCloud Embeding Code",
                        "id" => $shortname."_soundcloud",
                        "std" => "",
                        "type" => "textarea");

?>