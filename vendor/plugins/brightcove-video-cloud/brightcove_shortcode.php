<?php

add_shortcode('brightcove','add_brightcove');

function add_brightcove($atts) {

	$html;
	$width;
	$height;

	if (isset($atts['width'])) { 
		$width = $atts['width'];
	} else  {
		$width = get_option('bc_default_width');
	} 
	if (isset($width)) {
		//$width = 480;
  	}

  	if (isset($atts['height'])) { 
	  	$height = $atts['height'];
  	} else  {
	  	$height = get_option('bc_default_height');
  	}
  	if (isset($height)) {
   		 //$height= 270;
	}


	$html = '
				<div style="display:none"></div>
				<object id="'.rand().'" class="BrightcoveExperience">
				<param name="bgcolor" value="#FFFFFF" />
				<param name="wmode" value="transparent" />
				<param name="width" value="' . $width . '" />
				<param name="height"  value="'. $height .'" />';
				
	if (isset($atts['playerid'])) {   
   		$html = $html . '<param name="playerID" value="'.$atts['playerid'].'" />';
	}

	if (isset($atts['playerkey'])) {   
    	$html = $html . '<param name="playerKey" value="'.$atts['playerkey'].'"/>';
	}
	$html = $html .' <param name="isVid" value="true" />
					<param name="isUI" value="true" />
					<param name="dynamicStreaming" value="true" />';

	if (isset($atts['videoid'])) { 
    	$html = $html . '<param name="@videoPlayer" value="'.$atts['videoid'].'" />';
	}
	
	if (isset($atts['playlistid'])) {   
    	$html = $html . '<param name="@playlistTabs" value="'.$atts['playlistid'].'" />';
		$html = $html . '<param name="@videoList" value="'.$atts['playlistid'].'" />';
		$html = $html . '<param name="@playlistCombo" value="'.$atts['playlistid'].'" />';
	} 
	
	$html = $html . '</object><script type="text/javascript">brightcove.createExperiences();</script>';

	return $html;
}


