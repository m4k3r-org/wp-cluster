<?php
// WORDPRESS CUSTOM SHORTCODES Coded and Designed by Bulent Sahin www.wpthemess.com | wpthemess@gmail.com
// http://wpthemess.com
// Licence: Free / GPL

add_filter( 'widget_text', 'shortcode_unautop');
add_filter( 'widget_text', 'do_shortcode');

//1. Secure Email
function scd_secure_mail($atts) {
    extract(shortcode_atts(array(
        "mailto" => '',
        "txt" => ''
    ), $atts));
    $mailto = antispambot($mailto);
    $txt = antispambot($txt);
    return '<a href="mailto:' . $mailto . '">' . $txt . '</a>';
}

if ( function_exists('add_shortcode')
)
add_shortcode('ssmail', 'scd_secure_mail');
//USAGE [ssmail mailto="wpthemess@gmail.com" txt="Get In Touch With Us"] or  [ssmail mailto="wpthemess@gmail.com" txt="wpthemess@gmail.com"]


//2. PDF Viewer
function scd_pdf($atts, $content) {
 extract(shortcode_atts(array(
        "target" => '',
        ), $atts));
    return '<a class="viewpdf" href="http://docs.google.com/viewer?url=' . $atts['href'] . '" target="' . $atts['target'] . '" title="'.$content.'" alt="'.$content.'">'.$content.'</a>';
}
add_shortcode('sspdf', 'scd_pdf');
//USAGE: [sspdf href="http://yoursite.com/linktoyour/yourpdffile.pdf" target="_blank"]View PDF Via Google[/sspdf]

//3. DOC Viewer
function scd_doc($atts, $content) {
    return '<a class="viewdoc" href="http://docs.google.com/viewer?url=' . $atts['href'] . '" target="' . $atts['target'] . '" title="'.$content.'" alt="'.$content.'">'.$content.'</a>';
}
add_shortcode('ssdoc', 'scd_doc');
//USAGE: [ssdoc href="http://yoursite.com/linktoyour/yourdocfile.doc" target="_blank"]View DOC Via Google[/ssdoc]

//4. EXCEL Viewer
function scd_excel($atts, $content) {
    return '<a class="viewexcel" href="http://docs.google.com/viewer?url=' . $atts['href'] . '" target="' . $atts['target'] . '" title="'.$content.'" alt="'.$content.'">'.$content.'</a>';
}
add_shortcode('ssexcel', 'scd_excel');
//USAGE: [ssexcel href="http://yoursite.com/linktoyour/yourdocfile.doc" target="_blank"]View Excel Via Google[/ssexcel]

//5. PPT Viewer
function scd_ppt($atts, $content) {
    return '<a class="viewppt" href="http://docs.google.com/viewer?url=' . $atts['href'] . '" target="' . $atts['target'] . '" title="'.$content.'" alt="'.$content.'">'.$content.'</a>';
}
add_shortcode('ssppt', 'scd_ppt');
//USAGE: [ssppt href="http://yoursite.com/linktoyour/yourdocfile.doc" target="_blank"]View PPT Via Google[/ssppt]

//6. Get custom posts from WordPress Database
function scd_customquery($atts, $content = null) {
        extract(shortcode_atts(array(
        "num" => '',
        "cat" => ''
        ), $atts));
        global $post;
        $myposts = get_posts('numberposts='.$num.'&order=DESC&orderby=post_date&category='.$cat);
        $shortcode='<ul>';
        foreach($myposts as $post) :
                setup_postdata($post);
        $shortcode.='<li><a href="'.get_permalink().'">'.the_title("","",false).'</a></li>';
        endforeach;
        $shortcode.='</ul> ';
        return $shortcode;
}
add_shortcode("sscustomquery", "scd_customquery");
//USAGE: [sscustomquery num="3" cat="1"]


//7. Get the Last Image Attached to a Post Shortcode
function scd_postimage($atts, $content = null) {
    extract(shortcode_atts(array(
        "size" => 'thumbnail',
        "float" => 'none'
    ), $atts));
    $images =& get_children( 'post_type=attachment&post_mime_type=image&post_parent=' . get_the_id() );
    foreach( $images as $imageID => $imagePost )
    $fullimage = wp_get_attachment_image($imageID, $size, false);
    $imagedata = wp_get_attachment_image_src($imageID, $size, false);
    $width = ($imagedata[1]+2);
    $height = ($imagedata[2]+2);
    return '<div id="postimage">'.$fullimage.'</div><style type="text/css">#postimage{float: '.$float.';}</style>';
}
add_shortcode("postimage", "scd_postimage");
//USAGE: [postimage size="large" float="left"]


//8. Restrict Content via ShortCode
function scd_check_logged_in( $atts, $content = null ) {
 extract(shortcode_atts(array(
        "loginlink" => ''.get_bloginfo('home').'/wp-admin',
        "color" => '#cc0000',
        "size" => '14px'
    ), $atts));
if ( is_user_logged_in() &&
!is_null($content) &&
!is_feed()
) {

return $content;
} else {

return __( '<div style="color:'.$color.'; font-size:'.$size.'"> Sorry, this content is only available for <a href="'.$loginlink.'"><b>logged</b></a> users.</div>', SS_TEXTDOMAIN );
}
}
add_shortcode( 'ssmember', 'scd_check_logged_in' );
//USAGE: [ssmember loginlink="http://wpthemess.com/season/wp-admin" color="#cc0000"]Your restricted content will comes here and only members will see this content...[/ssmember]


//9. Embed an RSS Reader - This file is needed to be able to use the wp_rss() function.
//include_once(ABSPATH.WPINC.'/rss.php');

function scd_rss($atts) {
    extract(shortcode_atts(array(
        "feed" => 'http://',
        "num" => '1',
    ), $atts));

    return wp_rss($feed, $num);
}
add_shortcode('ssrss', 'scd_rss');
//USAGE :[ssrss feed="http://feeds.feedburner.com/wpthemess" num="5"][/ssrss]

//11. Send Twitter
function scd_twitt($atts, $content) {
  return '<a class="sendtwit" href="http://twitter.com/home?status=Currently reading '.get_permalink($post->ID).'" title="Click to send this page to Twitter!" target="_blank">'.$content.'</a>';
}
add_shortcode('sstwitter', 'scd_twitt');
//USAGE [sstwitter]Share on Twitter[/sstwitter]


//12. Google Adsense
function scd_adsense($atts, $content) {
        extract(shortcode_atts(array(
                "width" => '',
                "slot" => '1234567890',
                "height" => ''
        ), $atts));
    return '<div id="adsense"><script type="text/javascript"><!--
    google_ad_client = "pub-XXXXXXXXXXXXXX";
    google_ad_slot = "' . $atts['slot'] . '";
    google_ad_width = ' . $atts['width'] . ';
    google_ad_height = ' . $atts['height'] . ';
    //-->
</script>

<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script></div>';
}
add_shortcode('ssadsense', 'scd_adsense');
//USAGE [ssadsense width="468" height="60" slot="60"][/ssadsense]


//14. Fullpage Slider Shortcode
function scd_wide_slider_gallery($atts, $content) {
    extract(shortcode_atts(array(
        'id' => '1',
        'fx' => 'fade',
        'showpost' => '5',
		'width' => '300px',
		'height' => '200px',
        'timeout' => '3000',
    ), $atts));

    $shortcode.= '<script type="text/javascript" language="javascript">jQuery.noConflict(); jQuery(document).ready(function($){ $("#wide_post_gallery_'.$id.'").cycle({ fx:"'.$fx.'", timeout:"'.$timeout.'", pause:1, pager:  "#swpsnav",}); });</script>';
    $shortcode.= '<div class="site5_post_slider_wide"><div id="wide_post_gallery_'.$id.'">';
        $args = array(
            'post_type' => 'attachment',
            'numberposts' => $showpost,
            'post_status' => null,
            'orderby' => 'menu_order',
            'post_parent' => $id
        );
        $attachments = get_posts($args);
        if($attachments) {
            foreach ($attachments as $attachment) {
                $img_url = wp_get_attachment_url($attachment->ID);
                $shortcode.= '<a href="'.$img_url.'" class="prettyPhoto[mixed]" title="'.the_title("","",false).'"><img src="'.get_bloginfo('template_directory')."/timthumb.php?src=".$img_url.'&amp;w='.$width.'%&amp;h='.$height.'&amp;zc=1" alt="'.the_title("","",false).'" /></a>';
            }
        }
    $shortcode.= '</div><div id="swpsnav"></div></div><div class="clear"></div>';
    //Reset Query
	wp_reset_query();
    return $shortcode;
}
add_shortcode('scwideslider','scd_wide_slider_gallery');
//USAGE: [scwideslider id="3" showpost="5" width="300px" height="200px" fx="fade" timeout="3000"]


//16a. Featured Carousel Shortcode
function scd_featured_carousel($atts, $content) {

    extract(shortcode_atts(array(
        'id' => '1',
        'showpost' => '5',
    ), $atts));

   $shortcode.= '<!--featured carousel shortcode start--><script type="text/javascript" src="'.get_template_directory_uri().'/sliders/featuredcarousel/js/jquery.featured.carousel.sc.js"></script><link rel="stylesheet" href="'.get_template_directory_uri().'/sliders/featuredcarousel/css/featured.carousel.sc.css" type="text/css" media="all" />';

    $shortcode.= '<div id="featureCarousel">';
    $shortcode.= '<script type="text/javascript"> jQuery.noConflict(); jQuery(document).ready(function($){ $("#featureCarousel").featureCarousel({  });  }); </script>';
            $args = array(
            'post_type' => 'attachment',
            'numberposts' => $showpost,
            'post_status' => null,
            'orderby' => 'menu_order',
            'post_parent' => $id
        );
        $attachments = get_posts($args);
        if($attachments) {
           foreach ($attachments as $attachment) {
            $img_url = wp_get_attachment_url($attachment->ID);
            $shortcode.= '<div class="feature"><a href="'.$img_url.'"><img src="'.get_bloginfo('template_directory')."/timthumb.php?src=".$img_url.'&amp;w=650&amp;h=340&amp;zc=1" alt="'.the_title("","",false).'" /></a></div>';
            }
        }
    $shortcode.= '</div><div class="clear"></div><!--featured carousel shortcode end-->';
    //Reset Query
	wp_reset_query();
    return $shortcode;
}
add_shortcode('ssfeaturedcarousel','scd_featured_carousel');
//USAGE: [ssfeaturedcarousel id="3" showpost="5"]


//17. Column Shortcodes
function sc_one_third( $atts, $content = null ) {
   return '<div class="one_third">' . do_shortcode($content) . '</div>';
}
add_shortcode('one_third', 'sc_one_third');

//18. Column Shortcodes
function sc_one_third_last( $atts, $content = null ) {
   return '<div class="one_third last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('one_third_last', 'sc_one_third_last');

//19. Column Shortcodes
function sc_two_third( $atts, $content = null ) {
   return '<div class="two_third">' . do_shortcode($content) . '</div>';
}
add_shortcode('two_third', 'sc_two_third');

//20. Column Shortcodes
function sc_two_third_last( $atts, $content = null ) {
   return '<div class="two_third last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('two_third_last', 'sc_two_third_last');

//21. Column Shortcodes
function sc_one_half( $atts, $content = null ) {
   return '<div class="one_half">' . do_shortcode($content) . '</div>';
}
add_shortcode('one_half', 'sc_one_half');

//22. Column Shortcodes
function sc_one_half_last( $atts, $content = null ) {
   return '<div class="one_half last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('one_half_last', 'sc_one_half_last');

//23. Column Shortcodes
function sc_one_fourth( $atts, $content = null ) {
   return '<div class="one_fourth">' . do_shortcode($content) . '</div>';
}
add_shortcode('one_fourth', 'sc_one_fourth');

//24. Column Shortcodes
function sc_one_fourth_last( $atts, $content = null ) {
   return '<div class="one_fourth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('one_fourth_last', 'sc_one_fourth_last');

//25. Column Shortcodes
function sc_three_fourth( $atts, $content = null ) {
   return '<div class="three_fourth">' . do_shortcode($content) . '</div>';
}
add_shortcode('three_fourth', 'sc_three_fourth');

//26. Column Shortcodes
function sc_three_fourth_last( $atts, $content = null ) {
   return '<div class="three_fourth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('three_fourth_last', 'sc_three_fourth_last');

//27. Column Shortcodes
function sc_one_fifth( $atts, $content = null ) {
   return '<div class="one_fifth">' . do_shortcode($content) . '</div>';
}
add_shortcode('one_fifth', 'sc_one_fifth');

//28. Column Shortcodes
function sc_one_fifth_last( $atts, $content = null ) {
   return '<div class="one_fifth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('one_fifth_last', 'sc_one_fifth_last');

//29. Column Shortcodes
function sc_two_fifth( $atts, $content = null ) {
   return '<div class="two_fifth">' . do_shortcode($content) . '</div>';
}
add_shortcode('two_fifth', 'sc_two_fifth');

//30. Column Shortcodes
function sc_two_fifth_last( $atts, $content = null ) {
   return '<div class="two_fifth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('two_fifth_last', 'sc_two_fifth_last');

//31. Column Shortcodes
function sc_three_fifth( $atts, $content = null ) {
   return '<div class="three_fifth">' . do_shortcode($content) . '</div>';
}
add_shortcode('three_fifth', 'sc_three_fifth');

//32. Column Shortcodes
function sc_three_fifth_last( $atts, $content = null ) {
   return '<div class="three_fifth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('three_fifth_last', 'sc_three_fifth_last');

//33. Column Shortcodes
function sc_four_fifth( $atts, $content = null ) {
   return '<div class="four_fifth">' . do_shortcode($content) . '</div>';
}
add_shortcode('four_fifth', 'sc_four_fifth');

//34. Column Shortcodes
function sc_four_fifth_last( $atts, $content = null ) {
   return '<div class="four_fifth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('four_fifth_last', 'sc_four_fifth_last');

//35. Column Shortcodes
function sc_one_sixth( $atts, $content = null ) {
   return '<div class="one_sixth">' . do_shortcode($content) . '</div>';
}
add_shortcode('one_sixth', 'sc_one_sixth');

//36. Column Shortcodes
function sc_one_sixth_last( $atts, $content = null ) {
   return '<div class="one_sixth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('one_sixth_last', 'sc_one_sixth_last');

//37. Column Shortcodes
function sc_five_sixth( $atts, $content = null ) {
   return '<div class="five_sixth">' . do_shortcode($content) . '</div>';
}
add_shortcode('five_sixth', 'sc_five_sixth');

//38. Column Shortcodes
function sc_five_sixth_last( $atts, $content = null ) {
   return '<div class="five_sixth last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
}
add_shortcode('five_sixth_last', 'sc_five_sixth_last');


//24. Toogle Shortcode
function scd_toggles($atts, $content = null) {
        extract(shortcode_atts(array(
        ), $atts));
        $ssout .='<ul id="toggle-view">';
        $ssout .= do_shortcode($content);
        $ssout .='</ul>';
        return $ssout;
}
add_shortcode("sstoggles", "scd_toggles");

//25. Toogle Content Shortcode
function scd_togglecontent($atts, $content = null) {
        extract(shortcode_atts(array(
         "title" => '',
        ), $atts));
        $ssout .='<li><h5><span class="toggle-indicator"></span>'.$title.'</h5><p>';
        $ssout .= do_shortcode($content);
        $ssout .='</p></li>';
        return $ssout;
}
add_shortcode("sstoggle", "scd_togglecontent");
//USAGE: [sstoggles] [sstoggle title="Title1"] Content #1 comes here[/sstoggle] [sstoggle title="Title2"] Content #2 comes here[/sstoggle] [sstoggle title="Title3"] Content #3 comes here[/sstoggle] [/sstoggles]


//26. Toogle Top Content Shortcode
function scd_toggletopcontent($atts, $content = null) {
        extract(shortcode_atts(array(
                "title" => '',
        ), $atts));
        $ssout .='<li><h3>'.$title.'</h3><span>+</span><a href="#" class="toggletop">TOP</a><p>';
        $ssout .= do_shortcode($content);
        $ssout .='</p></li>';
        return $ssout;
}
add_shortcode("toggletop", "scd_toggletopcontent");

//USAGE:[toggles] [toggletop title="Title1"] Content #1 comes here[/toggle] [toggle title="Title2"] Content #2 comes here[/toggle] [toogle title="Title3"] Content #3 comes here[/toggletop] [/toggles]

//27. Fancy <blockquote>
function scd_fancy_quote($atts, $content = null) {
extract(shortcode_atts(array(
    'textcolor' => '#000000',
    'bgcolor' => '#fefef1',
    'bordercolor' => '#e5e5e5',
    'border' => '1px',
    'bordertype' => 'solid',
    'from' => 'John DOE, Site5.com',
    'link' => 'http://site5.com/',
    'linkcolor' => '#000000'
  ), $atts));
  return "<div class=\"ssquote\" style=\"background-color: $bgcolor; color: $textcolor; border: $border solid $bordercolor; font-size: 1em; font-style: italic; margin: 10px auto; padding: 10px;\">$content<br><br><p align=\"right\"><strong><em><a href=\"$link\" style=\"color: $linkcolor\">~ $from</a></em></strong></p></div>";
}
add_shortcode('ssquote','scd_fancy_quote');
//USAGE:[ssquote textcolor="#000000" bgcolor="#fefef1" bordercolor="#e5e5e5" border="1px" bordertype="solid" from="John DOE, Wpthemess.com"]There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. [/ssquote]



//28. Quotes As Testimonials
function scd_quote_testimonial($atts, $content = null) {
extract(shortcode_atts(array(
    'img' => '',
    'name' => '',
    'website' => '',
    'alt' => ''
  ), $atts));

if (isset($img)) {
    $img_style = '<p class="qtestimonialp"><img src="'.$img.'" height="150" width="150" border="0" alt="'.$alt.'" /></a></p>';
}
if ($name != "") {
    $byline = '<br><p align="right"><strong><em>~ '.$name.', <a href="'.$website.'">'.$website.'</a></em></strong></p>';
}
  return '<div class="qtestimonial">'.$img_style.' <i>'.$content.'</i> '.$byline.'</div>';
}
add_shortcode('qtestimonial','scd_quote_testimonial');
//USAGE:[qtestimonial img="http://yourtestimonialimagefullurl.jpg" name="John DOE" website="http://www.wpthemess.com/season" alt="John DOE"]Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old.[/qtestimonial]


//29. Tick lists
function scd_arrowlist($atts, $content = null) {
    return '<div class="arrowlist">'.$content.'</div>';
}
add_shortcode('ssarrowlist', 'scd_arrowlist');
//USAGE:[ssticklist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/ssticklist]

//30. Tick lists
function scd_ticklist($atts, $content = null) {
    return '<div class="ticklist">'.$content.'</div>';
}
add_shortcode('ssticklist', 'scd_ticklist');
//USAGE:[ssticklist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/ssticklist]

//31. Cross lists
function scd_crosslist($atts, $content = null) {
    return '<div class="crosslist">'.$content.'</div>';
}
add_shortcode('sscrosslist', 'scd_crosslist');
//USAGE:[sscrosslist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/sscrosslist]

//32. Star lists
function scd_starlist($atts, $content = null) {
    return '<div class="starlist">'.$content.'</div>';
}
add_shortcode('ssstarlist', 'scd_starlist');
//USAGE:[ssstarlist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/ssstarlist]

//33. Exclam lists
function scd_exclamlist($atts, $content = null) {
    return '<div class="exclamlist">'.$content.'</div>';
}
add_shortcode('ssexclamlist', 'scd_exclamlist');
//USAGE:[ssexclamlist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/ssexclamlist]

//34. Add lists
function scd_addlist($atts, $content = null) {
    return '<div class="addlist">'.$content.'</div>';
}
add_shortcode('ssaddlist', 'scd_addlist');
//USAGE:[ssaddlist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/ssaddlist]

//35. Black lists
function scd_blacklist($atts, $content = null) {
    return '<div class="blacklist">'.$content.'</div>';
}
add_shortcode('ssblacklist', 'scd_blacklist');
//USAGE:[ssblacklist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/ssblacklist]

//36. Blue lists
function scd_bluelist($atts, $content = null) {
    return '<div class="bluelist">'.$content.'</div>';
}
add_shortcode('ssbluelist', 'scd_bluelist');
//USAGE:[ssbluelist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/ssbluelist]

//37. Star Small lists
function scd_starlistsmall($atts, $content = null) {
    return '<div class="starlistsmall">'.$content.'</div>';
}
add_shortcode('ssstarlistsmall', 'scd_starlistsmall');
//USAGE:[ssstarlistsmall]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/ssstarlistsmall]

//38. Delete lists
function scd_deletelist($atts, $content = null) {
    return '<div class="deletelist">'.$content.'</div>';
}
add_shortcode('ssdeletelist', 'scd_deletelist');
//USAGE:[ssdeletelist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/ssdeletelist]

//39. Error lists
function scd_errorlist($atts, $content = null) {
    return '<div class="errorlist">'.$content.'</div>';
}
add_shortcode('sserrorlist', 'scd_errorlist');
//USAGE:[sserrorlist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/sserrorlist]

//40. Feed lists
function scd_feedlist($atts, $content = null) {
    return '<div class="feedlist">'.$content.'</div>';
}
add_shortcode('ssfeedlist', 'scd_feedlist');
//USAGE:[ssfeedlist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/ssfeedlist]

//41. Green lists
function scd_greenlist($atts, $content = null) {
    return '<div class="greenlist">'.$content.'</div>';
}
add_shortcode('ssgreenlist', 'scd_greenlist');
//USAGE:[ssgreenlist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/ssgreenlist]

//42. Idea lists
function scd_idealist($atts, $content = null) {
    return '<div class="idealist">'.$content.'</div>';
}
add_shortcode('ssidealist', 'scd_idealist');
//USAGE:[ssidealist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/ssidealist]

//43. Key lists
function scd_keylist($atts, $content = null) {
    return '<div class="keylist">'.$content.'</div>';
}
add_shortcode('sskeylist', 'scd_keylist');
//USAGE:[sskeylist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/sskeylist]

//44. New lists
function scd_newlist($atts, $content = null) {
    return '<div class="newlist">'.$content.'</div>';
}
add_shortcode('ssnewlist', 'scd_newlist');
//USAGE:[ssnewlist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/ssnewlist]

//45. Orange lists
function scd_orangelist($atts, $content = null) {
    return '<div class="orangelist">'.$content.'</div>';
}
add_shortcode('ssorangelist', 'scd_orangelist');
//USAGE:[ssorangelist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/ssorangelist]

//46. Pink lists
function scd_pinklist($atts, $content = null) {
    return '<div class="pinklist">'.$content.'</div>';
}
add_shortcode('sspinklist', 'scd_pinklist');
//USAGE:[sspinklist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/sspinklist]

//47. Plus lists
function scd_pluslist($atts, $content = null) {
    return '<div class="pluslist">'.$content.'</div>';
}
add_shortcode('sspluslist', 'scd_pluslist');
//USAGE:[sspluslist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/sspluslist]

//48. Purple lists
function scd_purplelist($atts, $content = null) {
    return '<div class="purplelist">'.$content.'</div>';
}
add_shortcode('sspurplelist', 'scd_purplelist');
//USAGE:[sspurplelist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/sspurplelist]

//49. Red lists
function scd_redlist($atts, $content = null) {
    return '<div class="redlist">'.$content.'</div>';
}
add_shortcode('ssredlist', 'scd_redlist');
//USAGE:[ssredlist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/ssredlist]

//50. Tag lists
function scd_taglist($atts, $content = null) {
    return '<div class="taglist">'.$content.'</div>';
}
add_shortcode('sstaglist', 'scd_taglist');
//USAGE:[sstaglist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/sstaglist]

//51. VCard lists
function scd_vcardlist($atts, $content = null) {
    return '<div class="vcardlist">'.$content.'</div>';
}
add_shortcode('ssvcardlist', 'scd_vcardlist');
//USAGE:[ssvcardlist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/ssvcardlist]

//52. Yellow lists
function scd_yellowlist($atts, $content = null) {
    return '<div class="yellowlist">'.$content.'</div>';
}
add_shortcode('ssyellowlist', 'scd_yellowlist');
//USAGE:[ssyellowlist]<ul> <li>This is item one</li> <li>This is item two</li> <li>This is item three</li> </ul>[/ssyellowlist]


//53. Highlight Text
function scd_highlight($atts, $content = null) {
extract(shortcode_atts(array(
    'color' => 'yellow',
    'fontcolor' => '#000000'
  ), $atts));
  return "<font style=\"background-color: $color; color: $fontcolor\">$content</font>";
}
add_shortcode('sshighlight','scd_highlight');
//USAGE:[sshighlight color="yellow" fontcolor="#000000"]look this this[/sshighlight]


//54. Highlighted Text Box
function scd_txtbox($atts, $content = null) {
extract(shortcode_atts(array(
    'textcolor' => '#555555',
    'bgcolor' => '#eeeeee',
    'bordercolor' => '#dddddd',
    'border' => '1px',
    'bordertype' => 'solid'
  ), $atts));
  return "<p style=\"padding: 2px 6px 4px 6px; color: $textcolor; background-color: $bgcolor; border: $bordercolor $border $bordertype\">$content</p>";
}
add_shortcode('sstxtbox','scd_txtbox');
//USAGE:[sstxtbox]This is Season Wordpress Theme Sample Highlighted Text[/sstxtbox]
//USAGE:[sstxtbox bgcolor="#ffffc4" bordercolor="#999999"]This is Season Wordpress Theme Sample Highlighted Text![/sstxtbox]

//55. SWF Video
function scd_embed_swf($atts, $content = null) {
extract(shortcode_atts(array(
'video'  => '',
'width'  => '560',
'height' => '340'
), $atts));

return '<div class="swf-video"><p><center><object type="application/x-shockwave-flash" style="width:'.$width.'px; height:'.$height.'px;" data="'.$video.'"><param name="movie" value="'.$video.'" /></object></center></p></div>';
}
add_shortcode('swf', 'scd_embed_swf');
//USAGE:[swf video="http://yourflashvideolinkhere.swf" width="560" height="340" /]


//56. Youtube Video
function scd_embed_youtube($atts, $content = null) {
extract(shortcode_atts(array(
'video'  => '',
'width'  => '560',
'height' => '340'
), $atts));

return '<div class="youtube-video"><iframe width="'.$width.'" height="'.$height.'" src="http://www.youtube.com/embed/'.$video.'?rel=0"  frameborder="0" allowfullscreen></iframe></div>';
}

add_shortcode('yt', 'scd_embed_youtube');
//USAGE:[yt video="lFZ0z5Fm-Ng" width="560" height="340" /]


//57. Vimeo Video
function scd_embed_vimeo($atts, $content = null) {
extract(shortcode_atts(array(
'video'  => '',
'width'  => '560',
'height' => '340'
), $atts));

return '<div class="vimeo-video"><iframe width="'.$width.'" height="'.$height.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen src="http://player.vimeo.com/video/'.$video.'"></iframe></div>';
}
add_shortcode('vi', 'scd_embed_vimeo');
//USAGE:[vi video="lFZ0z5Fm-Ng" width="560" height="340" /]


//58. Lightbox Gallery Group
function scd_lightbox_gallery($atts, $content = null) {
extract(shortcode_atts(array(
'width'  => '100%',
'padding'  => '2',
'margin'  => '2',
), $atts));

return '<div style="width:'.$width.'px;padding:'.$padding.'px;margin:'.$margin.'px;">' . do_shortcode($content) . '</div>';
}
add_shortcode('lighboxgallery', 'scd_lightbox_gallery');
//USAGE:[lighboxgallery width="100%" padding="5" margin="5" /] [/lighboxgallery]

//59. Lightbox Gallery Items
function scd_lightbox_gallery_item($atts, $content = null) {
extract(shortcode_atts(array(
'width'  => '100',
'height'  => '100',
'padding'  => '2',
'margin'  => '2',
'aclass'  => '',
'src'  => '',
'href'  => '#',
), $atts));

return '<a href="'.$href.'" style="width:'.$width.'px;padding:'.$padding.'px;margin:'.$margin.'px;" class="'.$aclass.'" rel="prettyPhoto[mixed]" title="' . do_shortcode($content) . '"><img src="'.$src.'"></a>';
}
add_shortcode('lbgitem', 'scd_lightbox_gallery_item');
//USAGE:[lbgitem width="100" height="100" padding="5" margin="5" href="http://" src="http://yourimagelinkhere"/] [/lbgitem]


//60. Dynamic Day Countdown
function scd_countdown_event($atts, $content = null)
{
  extract(shortcode_atts(array(
    'event' => '',
    'month' => '',
    'day' => '',
    'year' => ''
  ), $atts));
    // subtract desired date from current date and give an answer in terms of days
    $remain = ceil( ( mktime( 0,0,0,$month,$day,$year ) - time() ) / 86400 );
    // show the number of days left
    if( $remain > 0 )
    {
        $daysleft = "<strong>$remain</strong> more days until the $event";
    }
    // Event has arrived!
    else
    {
        $daysleft = "...woops, $event has passed...(or some other message in here)";
    }

return $daysleft;
}
add_shortcode('countdown', 'scd_countdown_event');
//USAGE:[countdown event="Custom Wordpress Theme Modification Service" month="3" day="30" year="2013" /]


//61. Buttons
function scbutton( $atts, $content = null ) {
    extract(shortcode_atts(array(
    'link'	=> '#',
    'target'	=> '',
    'variation'	=> '',
    'size'	=> '',
    'align'	=> '',
    ), $atts));

	$style = ($variation) ? ' '.$variation : '';
	$align = ($align) ? ' align'.$align : '';
	$size = ($size == 'large') ? ' large_button' : '';
	$target = ($target == 'blank') ? ' target="_blank"' : '';

	$out = '<a' .$target. ' class="button_link' .$style.$size.$align. '" href="' .$link. '">' .do_shortcode($content). '</a>';

    return $out;
}
add_shortcode('scbutton', 'scbutton');
//USAGE:[button link="http://wordpress.org" target="blank" variation="hot_pink" size="large" align="right"]Content Here[/button]

//62. Social Icons
function scsocial( $atts, $content = null ) {
    extract(shortcode_atts(array(
        'link'	=> '#',
        'target'	=> '',
        'variation'	=> '',
        'size'	=> '',
        'align'	=> '',
    ), $atts));

    $style = ($variation) ? ' '.$variation : '';
    $align = ($align) ? ' align'.$align : '';
    $target = ($target == 'blank') ? ' target="_blank"' : '';

    $out = '<a' .$target. ' class="social_link' .$style.$align. '" href="' .$link. '">' .do_shortcode($content). '</a>';

    return $out;
}
add_shortcode('scsocial', 'scsocial');
//USAGE:[scsocial link="http://wordpress.org" target="blank" variation="facebook" align="right"]Content Here[/scsocial]

//62. Dropcap Simple
function scd_dropcap_simple($atts, $content = null) {
	return '<div class="dropcapsimple">'.$content.'</div>';
}
add_shortcode('sdropcap', 'scd_dropcap_simple');
//USAGE:[sdropcap]S[/sdropcap]There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable...


//63. Dropcap Fancy
function scd_dropcap_fancy( $atts, $content = null )
{
    extract( shortcode_atts( array(
      'color' => '',
      'bg' => '',
      ), $atts ) );
	return '<div class="dropcapfancy" style="background-color:'.$bg.'; color:'.$color.'">'.$content.'</div>';
}
add_shortcode('fdropcap', 'scd_dropcap_fancy');
//USAGE: [fdropcap bg="#333333" color="#ffffff"]D[/fdropcap] There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable...

//64. Dropcap Square
function scd_dropcap_square( $atts, $content = null )
{
    extract( shortcode_atts( array(
      'color' => '',
      'bg' => '#ddd',
      ), $atts ) );
	return '<div class="dropcapsquare" style="background-color:'.$bg.'; color:'.$color.'">'.$content.'</div>';
}
add_shortcode('dropcapsquare', 'scd_dropcap_square');
//USAGE: [dropcapsquare bg="#333333" color="#ffffff"]D[/dropcapsquare] There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable...


//65. Display Posts From Category
function scd_category_posts( $atts )
{
extract(shortcode_atts(array(
	    'limit' => '',
        'exclude' => '',
        'category' => '',
	), $atts));
	//The Query
	query_posts('category=' . $id . '&exclude=' . $exclude . '&posts_per_page=' . $limit);
	//The Loop
	if ( have_posts() ) : while ( have_posts() ) : the_post();
		$shortcode.=	'<div style="float:left;display:block; margin-bottom:10px; padding:5px; border-bottom:1px solid #ddd;"><h4><a href="' . post_permalink() . '">'.the_title("","",false).'</a></h4>';

        if ( has_post_thumbnail() ) {
        $image_id = get_post_thumbnail_id();
		$image_url = wp_get_attachment_image_src($image_id,'large', true);
        $shortcode.=	'<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]" title="'.the_title("","",false).'"><img src="'.get_bloginfo('template_directory').'/timthumb.php?src=' . $image_url[0] . '&amp;h=80&amp;w=80&amp;zc=1" class="alignleft" alt="'.the_title("","",false).'" /></a>';
        } else {
       $shortcode.=  '<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]"><img src="'.get_bloginfo('template_directory').'/images/default/comingsoon.png" height="80" width="80" class="alignleft" alt="'.the_title("","",false).'" title="'.the_title("","",false).'" /></a> It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of...';
        }
	   $shortcode.=  excerpt(120);
       $shortcode.='</div>';
	endwhile; else:
	endif;

	//Reset Query
	wp_reset_query();
    return $shortcode;
}
add_shortcode('category', 'scd_category_posts');
//USAGE:[category id="-1" limit="5" exclude="seperated ids with comma"]



//66. Custom Post Type Portfolio Query
function scd_portfolio_items($atts){
	extract(shortcode_atts(array(
	        'limit' => '',
            'order' => ''
	    ), $atts));
	//The Query
	query_posts('post_type=portfolio&posts_per_page=' . $limit. '&post_status=publish&order=' . $order. '&paged=paged');
	//The Loop
	if ( have_posts() ) : while ( have_posts() ) : the_post();
		$shortcode.=	'<div style="width:900px;min-height:150px; border-bottom:1px solid #ddd;"><h4><a href="' . post_permalink() . '">'.the_title("","",false).'</a></h4>';
       if ( has_post_thumbnail() ) {
        $image_id = get_post_thumbnail_id();
		$image_url = wp_get_attachment_image_src($image_id,'large', true);
        $shortcode.=	'<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]" title="'.the_title("","",false).'"><img src="'.get_bloginfo('template_directory').'/timthumb.php?src=' . $image_url[0] . '&amp;h=80&amp;w=80&amp;zc=1" class="alignleft" alt="'.the_title("","",false).'" /></a>';
         } else {
       $shortcode.=  '<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]"><img src="'.get_bloginfo('template_directory').'/images/default/comingsoon.png" height="80" width="80" class="alignleft" alt="'.the_title("","",false).'" title="'.the_title("","",false).'" /></a> It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of...';
        }
		$shortcode.= the_excerpt();
       $shortcode.='</div>';
	endwhile; else:
	endif;

	//Reset Query
	wp_reset_query();
    return $shortcode;
}
add_shortcode('portfolio', 'scd_portfolio_items');
//USAGE:[portfolio limit="6" order="DESC"] [/portfolio]


//67 Custom Post Type 1 Column Portfolio Query
function scd_1column_portfolio_items($atts){
	extract(shortcode_atts(array(
	        'limit' => '',
            'order' => ''
	    ), $atts));
	//The Query
    $shortcode.=	'<div class="clear"></div>';
	query_posts('post_type=portfolio&posts_per_page=' . $limit. '&post_status=publish&order=' . $order. '&caller_get_posts="&paged=paged');
	//The Loop
	if ( have_posts() ) : while ( have_posts() ) : the_post();
		$shortcode.= '<div class="full"><h4><a href="' . post_permalink() . '">'.the_title("","",false).'</a></h4>';
       if ( has_post_thumbnail() ) {
        $image_id = get_post_thumbnail_id();
		$image_url = wp_get_attachment_image_src($image_id,'large', true);
        $shortcode.= 	'<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]" title="'.the_title("","",false).'"><img src="'.get_bloginfo('template_directory').'/timthumb.php?src=' . $image_url[0] . '&amp;h=200&amp;w=300&amp;zc=1" class="alignleft" alt="'.the_title("","",false).'" /></a>';
         } else {
       $shortcode.=  '<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]"><img src="'.get_bloginfo('template_directory').'/images/default/comingsoon.png" height="200" width="300" class="alignleft" alt="'.the_title("","",false).'" title="'.the_title("","",false).'" /></a> It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of...';
        }
		$shortcode.=  excerpt(120);
       $shortcode.= '</div>';
	endwhile; else:
	endif;
    $shortcode.=	'<div class="clear"></div>';

	//Reset Query
	wp_reset_query();
    return $shortcode;
}
add_shortcode('portfolio1c', 'scd_1column_portfolio_items');
//USAGE:[portfolio1c limit="1" order="DESC"] [/portfolio1c]

//68 Custom Post Type 2 Column Portfolio Query
function scd_2column_portfolio_items($atts){
	extract(shortcode_atts(array(
	        'limit' => '',
            'order' => ''
	    ), $atts));
	//The Query
    $shortcode.=	'<div class="clear"></div>';
    $c = 0;
	query_posts('post_type=portfolio&posts_per_page=' . $limit. '&order=' . $order. '&post_status=publish&caller_get_posts="&paged=paged');
	//The Loop
	if ( have_posts() ) : while ( have_posts() ) : the_post();
    	$c++;
        if( $c % 2 == 0 ) $extra_class = ' last';
		else $extra_class = '';
		$shortcode.=	'<div class="one_half'.$extra_class.'"><h4><a href="' . post_permalink() . '">'.the_title("","",false).'</a></h4>';
       if ( has_post_thumbnail() ) {
        $image_id = get_post_thumbnail_id();
		$image_url = wp_get_attachment_image_src($image_id,'large', true);
        $shortcode.=	'<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]" title="'.the_title("","",false).'"><img src="'.get_bloginfo('template_directory').'/timthumb.php?src=' . $image_url[0] . '&amp;h=150&amp;w=190&amp;zc=1" class="alignleft" alt="'.the_title("","",false).'" /></a>';
        } else {
       $shortcode.=  '<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]"><img src="'.get_bloginfo('template_directory').'/images/default/comingsoon.png" height="150" width="190" class="alignleft" alt="'.the_title("","",false).'" title="'.the_title("","",false).'" /></a> It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of...';
        }
		$shortcode.= excerpt(50);
       $shortcode.='</div>';
	endwhile; else:
	endif;
    $shortcode.=	'<div class="clear"></div>';

	//Reset Query
	wp_reset_query();
    return $shortcode;
}
add_shortcode('portfolio2c', 'scd_2column_portfolio_items');
//USAGE:[portfolio2c limit="2" order="DESC"] [/portfolio2c]

//69 Custom Post Type 3 Column Portfolio Query
function scd_3column_portfolio_items($atts){
	extract(shortcode_atts(array(
	        'limit' => '',
            'order' => '',
	    ), $atts));
	//The Query
    $shortcode.=	'<div class="clear"></div>';
    $c = 0;
	query_posts('post_type=portfolio&posts_per_page=' . $limit. '&order=' . $order. '&post_status=publish&caller_get_posts="&paged=paged');
	//The Loop
	if ( have_posts() ) : while ( have_posts() ) : the_post();
    	$c++;
        if( $c % 3 == 0 ) $extra_class = ' last';
		else $extra_class = '';
		$shortcode.=	'<div class="one_third'.$extra_class.'"><h4><a href="' . post_permalink() . '">'.the_title("","",false).'</a></h4>';
       if ( has_post_thumbnail() ) {
        $image_id = get_post_thumbnail_id();
		$image_url = wp_get_attachment_image_src($image_id,'large', true);
        $shortcode.=	'<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]" title="'.the_title("","",false).'"><img src="'.get_bloginfo('template_directory').'/timthumb.php?src=' . $image_url[0] . '&amp;h=90&amp;w=150&amp;zc=1" class="alignleft" alt="'.the_title("","",false).'" /></a>';
        } else {
       $shortcode.=  '<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]"><img src="'.get_bloginfo('template_directory').'/images/default/comingsoon.png" height="90" width="150" class="alignleft" alt="'.the_title("","",false).'" title="'.the_title("","",false).'" /></a> It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of...';
        }
		$shortcode.= excerpt(40);
       $shortcode.='</div>';
	endwhile; else:
	endif;
    $shortcode.=	'<div class="clear"></div>';

	//Reset Query
	wp_reset_query();
    return $shortcode;
}
add_shortcode('portfolio3c', 'scd_3column_portfolio_items');
//USAGE:[portfolio3c limit="3" order="DESC"] [/portfolio3c]

//70 Custom Post Type 4 Column Portfolio Query
function scd_4column_portfolio_items($atts){
	extract(shortcode_atts(array(
	        'limit' => '',
            'order' => '',
	    ), $atts));
	//The Query
    $shortcode.=	'<div class="clear"></div>';
    $c = 0;
	query_posts('post_type=portfolio&posts_per_page=' . $limit. '&order=' . $order. '&post_status=publish&caller_get_posts="&paged=paged');
	//The Loop
	if ( have_posts() ) : while ( have_posts() ) : the_post();
    	$c++;
        if( $c % 4 == 0 ) $extra_class = ' last';
		else $extra_class = '';
		$shortcode.=	'<div class="one_fourth'.$extra_class.'"><h5><a href="' . post_permalink() . '">'.the_title("","",false).'</a></h5>';
       if ( has_post_thumbnail() ) {
        $image_id = get_post_thumbnail_id();
		$image_url = wp_get_attachment_image_src($image_id,'large', true);
        $shortcode.=	'<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]" title="'.the_title("","",false).'"><img src="'.get_bloginfo('template_directory').'/timthumb.php?src=' . $image_url[0] . '&amp;h=70&amp;w=120&amp;zc=1" class="alignleft" alt="'.the_title("","",false).'" /></a>';
        } else {
       $shortcode.=  '<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]"><img src="'.get_bloginfo('template_directory').'/images/default/comingsoon.png" height="70" width="120" class="alignleft" alt="'.the_title("","",false).'" title="'.the_title("","",false).'" /></a> It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of...';
        }
		$shortcode.= excerpt(40);
       $shortcode.='</div>';
	endwhile; else:
	endif;
    $shortcode.=	'<div class="clear"></div>';

	//Reset Query
	wp_reset_query();
    return $shortcode;
}
add_shortcode('portfolio4c', 'scd_4column_portfolio_items');
//USAGE:[portfolio4c limit="4" order="DESC"] [/portfolio4c]

//71 Custom Post Type 5 Column Portfolio Query
function scd_5column_portfolio_items($atts){
	extract(shortcode_atts(array(
	        'limit' => '',
            'order' => ''
	    ), $atts));
	//The Query
    $shortcode.=	'<div class="clear"></div>';
    $c = 0;
	query_posts('post_type=portfolio&posts_per_page=' . $limit. '&order=' . $order. '&post_status=publish&caller_get_posts="&paged=paged');
	//The Loop
	if ( have_posts() ) : while ( have_posts() ) : the_post();
   		$c++;
        if( $c % 5 == 0 ) $extra_class = ' last';
		else $extra_class = '';
		$shortcode.=	'<div class="one_fifth'.$extra_class.'"><h6><a href="' . post_permalink() . '">'.the_title("","",false).'</a></h6>';
       if ( has_post_thumbnail() ) {
        $image_id = get_post_thumbnail_id();
		$image_url = wp_get_attachment_image_src($image_id,'large', true);
        $shortcode.=	'<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]" title="'.the_title("","",false).'"><img src="'.get_bloginfo('template_directory').'/timthumb.php?src=' . $image_url[0] . '&amp;h=50&amp;w=70&amp;zc=1" class="alignleft" alt="'.the_title("","",false).'" /></a>';
        } else {
       $shortcode.=  '<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]"><img src="'.get_bloginfo('template_directory').'/images/default/comingsoon.png" height="50" width="70" class="alignleft" alt="'.the_title("","",false).'" title="'.the_title("","",false).'" /></a> It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem...';
        }
		$shortcode.= excerpt(30);
       $shortcode.='</div>';
	endwhile; else:
	endif;
    $shortcode.=	'<div class="clear"></div>';

	//Reset Query
	wp_reset_query();
    return $shortcode;
}
add_shortcode('portfolio5c', 'scd_5column_portfolio_items');
//USAGE:[portfolio5c limit="5" order="DESC"] [/portfolio5c]


//72 Custom Post Type 6 Column Portfolio Query
function scd_6column_portfolio_items($atts){
	extract(shortcode_atts(array(
	        'limit' => '',
            'order' => '',
	    ), $atts));
	//The Query
    $shortcode.=	'<div class="clear"></div>';
    $c = 0;
	query_posts('post_type=portfolio&posts_per_page=' . $limit. '&order=' . $order. '&post_status=publish&caller_get_posts="&paged=paged');
	//The Loop
	if ( have_posts() ) : while ( have_posts() ) : the_post();
    	$c++;
        if( $c % 6 == 0 ) $extra_class = ' last';
		else $extra_class = '';
		$shortcode.=	'<div class="one_sixth'.$extra_class.'"><h6><a href="' . post_permalink() . '">'.the_title("","",false).'</a></h6>';
       if ( has_post_thumbnail() ) {
        $image_id = get_post_thumbnail_id();
		$image_url = wp_get_attachment_image_src($image_id,'large', true);
        $shortcode.=	'<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]" title="'.the_title("","",false).'"><img src="'.get_bloginfo('template_directory').'/timthumb.php?src=' . $image_url[0] . '&amp;h=40&amp;w=40&amp;zc=1" class="alignleft" alt="'.the_title("","",false).'" /></a>';
        } else {
       $shortcode.=  '<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]"><img src="'.get_bloginfo('template_directory').'/images/default/comingsoon.png" height="40" width="40" class="alignleft" alt="'.the_title("","",false).'" title="'.the_title("","",false).'" /></a> It is a long established fact that a reader will be distracted by the readable content of a page...';
        }
		$shortcode.= excerpt(10);
       $shortcode.='</div>';
	endwhile; else:
	endif;
    $shortcode.=	'<div class="clear"></div>';

	//Reset Query
	wp_reset_query();
    return $shortcode;
}
add_shortcode('portfolio6c', 'scd_6column_portfolio_items');
//USAGE:[portfolio6c limit="6" order="DESC"] [/portfolio6c]


//73 Show Related Posts
function scd_related_posts( $atts ) {
	extract(shortcode_atts(array(
	    'limit' => '5',
	), $atts));

	global $wpdb, $post, $table_prefix;

	if ($post->ID) {
		$retval = '<ul>';
 		// Get tags
		$tags = wp_get_post_tags($post->ID);
		$tagsarray = array();
		foreach ($tags as $tag) {
			$tagsarray[] = $tag->term_id;
		}
		$tagslist = implode(',', $tagsarray);

		// Do the query
		$q = "SELECT p.*, count(tr.object_id) as count
			FROM $wpdb->term_taxonomy AS tt, $wpdb->term_relationships AS tr, $wpdb->posts AS p WHERE tt.taxonomy ='post_tag' AND tt.term_taxonomy_id = tr.term_taxonomy_id AND tr.object_id  = p.ID AND tt.term_id IN ($tagslist) AND p.ID != $post->ID
			AND p.post_status = 'publish'
			AND p.post_date_gmt < NOW()
 			GROUP BY tr.object_id
			ORDER BY count DESC, p.post_date_gmt DESC
			LIMIT $limit;";

		$related = $wpdb->get_results($q);
 		if ( $related ) {
			foreach($related as $r) {
				$retval .= '
	<li><a title="'.wptexturize($r->post_title).'" href="'.get_permalink($r->ID).'">'.wptexturize($r->post_title).'</a></li>
';
			}
		} else {
			$retval .= '
	<li>No related posts found!</li>
';
		}
		$retval .= '</ul>
';
		return $retval;
	}
	return;
}
add_shortcode('related_posts', 'scd_related_posts');
//USAGE:[related_posts] //for no limit
//USAGE:[related_posts limit="enter your limit here for example 5"]


//74. Custom Font Style
function scd_fontstyle( $atts, $content = null ) {
    extract(shortcode_atts(array(
	"color" => 'blue',
    "size" => '14px',
    "padding" => '0px',
	), $atts));
   return '<span style="color: ' . $color . '; padding: ' . $padding . '; font-size: ' . $size . '">' . $content . '</span>';
}
add_shortcode('fontstyle', 'scd_fontstyle');
//USAGE:[fontstyle color="purple" size="18px" padding="5px" text-decoration"none"]Hello World![/fontstyle]


//75. Paypal Donation Link
function scd_donate_button( $atts ) {
    extract(shortcode_atts(array(
        'text' => 'Make a donation',
        'account' => 'info@yourwebsite.com',
        'for' => 'Wordpress Theme Modifications',
    ), $atts));

    global $post;

    if (!$for) $for = str_replace(" ","+",$post->post_title);

    return '<a class="gopaypal" href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business='.$account.'&item_name=Donation+for+'.$for.'">'.$text.'</a>';

}
add_shortcode('donate', 'scd_donate_button');
//USAGE:[donate text="Make a donation" account="info@yourwebsite.com" for="Site5 Wordpress Themes"] [/donate]


//76. Google chart
function scd_google_chart( $atts ) {
	extract(shortcode_atts(array(
	    'data' => '',
	    'colors' => '',
	    'size' => '400x200',
	    'bg' => 'ffffff',
	    'title' => '',
	    'labels' => '',
	    'advanced' => '',
	    'type' => 'pie'
	), $atts));

	switch ($type) {
		case 'line' :
			$charttype = 'lc'; break;
		case 'xyline' :
			$charttype = 'lxy'; break;
		case 'sparkline' :
			$charttype = 'ls'; break;
		case 'meter' :
			$charttype = 'gom'; break;
		case 'scatter' :
			$charttype = 's'; break;
		case 'venn' :
			$charttype = 'v'; break;
		case 'pie' :
			$charttype = 'p3'; break;
		case 'pie2d' :
			$charttype = 'p'; break;
        case 'r' :
			$charttype = 'r'; break;
        case 'map' :
			$charttype = 'map'; break;
		default :
			$charttype = $type;
		break;
	}

	if ($title) $string .= '&chtt='.$title.'';
	if ($labels) $string .= '&chl='.$labels.'';
	if ($colors) $string .= '&chco='.$colors.'';
	$string .= '&chs='.$size.'';
	$string .= '&chd=t:'.$data.'';
	$string .= '&chf='.$bg.'';

	return '<img title="'.$title.'" src="http://chart.apis.google.com/chart?cht='.$charttype.''.$string.$advanced.'" alt="'.$title.'" />';
}
add_shortcode('chart', 'scd_google_chart');
//USAGE:[chart data="41.52,37.79,20.67,0.03" bg="F7F9FA" labels="Reffering+sites|Search+Engines|Direct+traffic|Other" colors="058DC7,50B432,ED561B,EDEF00" size="488x200" title="Traffic Sources" type="pie"]

//USAGE:[chart data="0,12,24,26,32,64,54,24,22,20,8,2,0,0,3" bg="F7F9FA" size="200x100" type="sparkline"]

//77. Display member-info content
function scd_member_access_check( $attr, $content = null ) {
    extract( shortcode_atts( array( 'capability' => 'read' ), $attr ) );
    if ( current_user_can( $capability ) && !is_null( $content ) && !is_feed() )
        return $content;

    return 'Sorry, only registered members can access this content.';
}
add_shortcode( 'access', 'scd_member_access_check' );
//USAGE:[access capability="switch_themes"]


//78. Google Maps API
function scd_googlemap_api( $atts ) {
    extract(shortcode_atts(array(
        'width' => '500px',
        'height' => '300px',
        'apikey' => 'ABQIAAAAFRmho6B424DdFgF-5faonxQIKyIYUjf-_42PZGmgUIHs4418pBSS90SZjmqVURuVeGGR15H_iIywOg',
        'marker' => '',
        'center' => '',
        'zoom' => '13'
    ), $atts));

    if ($center) $setCenter = 'map.setCenter(new GLatLng('.$center.'), '.$zoom.');';
    if ($marker) $setMarker = 'map.addOverlay(new GMarker(new GLatLng('.$marker.')));';

    $rand = rand(1,100) * rand(1,100);

    return '
    	<script src="http://maps.google.com/maps?file=api&v=2.x&sensor=false&key='.$apikey.'" type="text/javascript"></script>

	    <script type="text/javascript">
		    function initialize() {
		      if (GBrowserIsCompatible()) {
		        var map = new GMap2(document.getElementById("map_canvas_'.$rand.'"));
		        '.$setCenter.'
		        '.$setMarker.'
		        map.setUIToDefault();
		      }
		    }
		    initialize();
	    </script>
    ';

}
add_shortcode('googlemapapi', 'scd_googlemap_api');
//USAGE:[googlemapapi zoom="13" center="52.66389056542801, 0.1641082763671875" marker="52.66389056542801, 0.1641082763671875" width="488px" height="488px"]

//79. Suceess Boxes
function scd_successbox($atts, $content=null, $code="") {
	$return = '<div class="successbox">';
	$return .= $content;
	$return .= '</div>';
	return $return;
}
add_shortcode('successbox' , 'scd_successbox' );
//USAGE:[successbox]Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. [/successbox]

//80. Idea Boxes
function scd_ideabox($atts, $content=null, $code="") {
	$return = '<div class="ideabox">';
	$return .= $content;
	$return .= '</div>';
	return $return;
}
add_shortcode('ideabox' , 'scd_ideabox' );
//USAGE:[ideabox]Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. [/ideabox]


//81. Ok Boxes
function scd_okbox($atts, $content=null, $code="") {
	$return = '<div class="okbox">';
	$return .= $content;
	$return .= '</div>';
	return $return;
}
add_shortcode('okbox' , 'scd_okbox' );
//USAGE:[okbox]Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. [/okbox]


//82. Question Boxes
function scd_questionbox($atts, $content=null, $code="") {
	$return = '<div class="questionbox">';
	$return .= $content;
	$return .= '</div>';
	return $return;
}
add_shortcode('questionbox' , 'scd_questionbox' );
//USAGE:[questionbox]Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. [/questionbox]


//83. Search Boxes
function scd_searchbox($atts, $content=null, $code="") {
	$return = '<div class="searchbox">';
	$return .= $content;
	$return .= '</div>';
	return $return;
}
add_shortcode('searchbox' , 'scd_searchbox' );
//USAGE:[searchbox]Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. [/searchbox]

//84. Event Boxes
function scd_eventbox($atts, $content=null, $code="") {
	$return = '<div class="eventbox">';
	$return .= $content;
	$return .= '</div>';
	return $return;
}
add_shortcode('eventbox' , 'scd_eventbox' );
//USAGE:[eventbox]Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. [/eventbox]


//85. Thumbs Up Boxes
function scd_thumbsupbox($atts, $content=null, $code="") {
	$return = '<div class="thumbsupbox">';
	$return .= $content;
	$return .= '</div>';
	return $return;
}
add_shortcode('thumbsupbox' , 'scd_thumbsupbox' );
//USAGE:[thumbsupbox]Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. [/thumbsupbox]


//86. Cancel Boxes
function scd_cancelbox($atts, $content=null, $code="") {
	$return = '<div class="cancelbox">';
	$return .= $content;
	$return .= '</div>';
	return $return;
}
add_shortcode('cancelbox' , 'scd_cancelbox' );
//USAGE:[cancelbox]Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. [/cancelbox]


//87. Add Boxes
function scd_addbox($atts, $content=null, $code="") {
	$return = '<div class="addbox">';
	$return .= $content;
	$return .= '</div>';
	return $return;
}
add_shortcode('addbox' , 'scd_addbox' );
//USAGE:[addbox]Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. [/addbox]


//88. Warning Boxes
function scd_warningbox($atts, $content=null, $code="") {
	$return = '<div class="warningbox">';
	$return .= $content;
	$return .= '</div>';
	return $return;
}
add_shortcode('warningbox' , 'scd_warningbox' );
//USAGE:[warningbox]Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. [/warningbox]


//89. Empty Boxes
function scd_emptybox($atts, $content=null, $code="") {
	$return = '<div class="emptybox">';
	$return .= $content;
	$return .= '</div>';
	return $return;
}
add_shortcode('emptybox' , 'scd_emptybox' );
//USAGE:[emptybox]Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. [/emptybox]


//90. Divider
function scd_divider( $atts, $content = null ) {
   return '<div class="divider"><a href="#top">' . do_shortcode($content) . '</a></div>';
}
add_shortcode('divider', 'scd_divider');
//USAGE:[divider][/divider]


//91. Google Maps Iframe
function scd_googlemapiframe_shortcode($atts, $content=null) {
extract(shortcode_atts(array(
        'src' => '<iframe width="200" height="120" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/?ie=UTF8&amp;ll=37.230328,-95.625&amp;spn=20.94321,26.455078&amp;z=4&amp;output=embed"></iframe>',
        'align' => ' '
    ), $atts));

    $return = '<div style="width:100%;display:block;margin-bottom:10px;padding:5px;"><div class="'.$align.'" style="margin-top:10px;">';
    $return .= ''.$src.'';
	$return .= '</div>';
    $return .= ''.$content.'</div>';
	return $return;
}
add_shortcode('googlemapiframe' , 'scd_googlemapiframe_shortcode' );
//USAGE:[googlemapiframe src="<iframe width="300" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/?ie=UTF8&amp;ll=37.230328,-95.625&amp;spn=20.94321,26.455078&amp;z=4&amp;output=embed"></iframe>" align=""]


//92. Get Widget In Content
function scd_display_widget($atts) {

    global $wp_widget_factory;

    extract(shortcode_atts(array(
        'widget_name' => FALSE
    ), $atts));

    $widget_name = esc_html($widget_name);

    if (!is_a($wp_widget_factory->widgets[$widget_name], 'WP_Widget')):
        $wp_class = 'WP_Widget_'.ucwords(strtolower($class));

        if (!is_a($wp_widget_factory->widgets[$wp_class], 'WP_Widget')):
            return '<p>'.sprintf(__("%s: Widget class not found. Make sure this widget exists and the class name is correct"),'<strong>'.$class.'</strong>').'</p>';
        else:
            $class = $wp_class;
        endif;
    endif;

    ob_start();
    the_widget($widget_name, $instance, array('widget_id'=>'arbitrary-instance-'.$id,
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => ''
    ));
    $output = ob_get_contents();
    ob_end_clean();
    return $output;

}
add_shortcode('widget','scd_display_widget');
//USAGE: [widget widget_name="Your_Custom_Widget"]



//93. Include Any Content In Content
function scd_include_content($atts) {

  $thepostid = intval($atts[postidparam]);
  $output = '';

  query_posts("p=$thepostid&post_type=page");
  if (have_posts()) : while (have_posts()) : the_post();
    $output .= get_the_content($post->ID);
  endwhile; else:
    // if failed, output nothing
  endif;
  wp_reset_query();

  return $output;

}
add_shortcode("seasonic", "scd_include_content");
// USAGE : In the post content, you can use [seasonic postidparam="12"]  / "12" would be the WordPress ID of the Page you are trying to include



//94. Only Users View This Content
function scd_private_content($atts, $content = null) {
	if (current_user_can('create_users'))
		return '<div class="private-content">' . $content . '</div>';
	return '';
}
add_shortcode('private', 'scd_private_content');

//USAGE: [private] Note: this post contains some private content! [/private]


//95. Custom Query Shortcode
function scd_custom_query_shortcode($atts) {

   // Defaults
   extract(shortcode_atts(array(
      "the_query" => ''
   ), $atts));

   // de-funkify query
   $the_query = preg_replace('~&#x0*([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $the_query);
   $the_query = preg_replace('~&#0*([0-9]+);~e', 'chr(\\1)', $the_query);

   // query is made
   query_posts($the_query);

   // Reset and setup variables
   $output = '';
   $temp_title = '';
   $temp_link = '';

   // the loop
   if (have_posts()) : while (have_posts()) : the_post();

      $temp_title = get_the_title($post->ID);
      $temp_link = get_permalink($post->ID);

      // output all findings - CUSTOMIZE TO YOUR LIKING
      $output .= "<li><a href='$temp_link'>$temp_title</a></li>";

   endwhile; else:

      $output .= "nothing found.";

   endif;

   wp_reset_query();
   return $output;

}
add_shortcode("loop", "scd_custom_query_shortcode");
// USAGE: [loop the_query="showposts=10&post_type=page&post_parent=1"]


//96. Tabs Shortcode
add_shortcode( 'tabgroup', 'scd_tab_group' );
function scd_tab_group( $atts, $content=null ){
$GLOBALS['tab_count'] = 0;
echo'<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function($){
$(".tab_content").hide();
$("ul.tabs li:first").addClass("active").show();
$(".tab_content:first").show();
$("ul.tabs li").click(function() {
$("ul.tabs li").removeClass("active");
$(this).addClass("active");
$(".tab_content").hide();
var activeTab = $(this).find("a").attr("href");
$(activeTab).fadeIn();
return false;
});
});
</script>';
do_shortcode( $content );

if( is_array( $GLOBALS['tabs'] ) ){
foreach( $GLOBALS['tabs'] as $tab ){
$tabs[] = '<li><a href="#'.$tab['id'].'">'.$tab['title'].'</a></li>';
$panes[] = '<div id="'.$tab['id'].'" class="tab_content">'.$tab['content'].'</div>';
}
$return = "\n".'<div id="tabscontainer"><ul class="tabs">'.implode( "\n", $tabs ).'</ul>'."\n".'<div class="tab_container">'.implode( "\n", $panes ).'</div></div>'."\n";
}
return $return;
}

add_shortcode( 'tab', 'scd_tab' );
function scd_tab( $atts, $content=null ){
extract(shortcode_atts(array(
'title' => 'Tab %d',
'id' => ''
), $atts));

$x = $GLOBALS['tab_count'];
$GLOBALS['tabs'][$x] = array( 'title' => sprintf( $title, $GLOBALS['tab_count'] ), 'content' =>  do_shortcode($content), 'id' =>  $id );

$GLOBALS['tab_count']++;
}

//USAGE: [tabgroup][tab title="Tab 1" id="1"]Tab 1 content goes here.[/tab][tab title="Tab 2" id="2"]Tab 2 content goes here.[/tab] [tab title="Tab 3" id="3"]Tab 3 content goes here.[/tab][/tabgroup]


//97. Display Content For Visitors
add_shortcode( 'visitor', 'scd_visitor_check_shortcode' );

function scd_visitor_check_shortcode( $atts, $content = null ) {
	 if ( ( !is_user_logged_in() && !is_null( $content ) ) || is_feed() )
		return $content;
	return '';
}
//USAGE: [visitor] Some content for the people just browsing your site. [/visitor]


//98. Display Content For Members
add_shortcode( 'member', 'scd_member_check_shortcode' );

function scd_member_check_shortcode( $atts, $content = null ) {
	 if ( is_user_logged_in() && !is_null( $content ) && !is_feed() )
		return $content;
	return '';
}
//USAGE: [member] This is members-only content. [/member]


//99. Display Flickr Stream
function scd_flickr_stream_shortcode($atts, $content=null){

//Here's our defaults
$query_atts = shortcode_atts(array('count' => '6', 'display' => 'latest', 'source' => 'user', 'size' => 't', 'user' => '', 'layout' => 'x', 'tag' => '', 'group' => '', 'set' => ''), $atts);

return sprintf('<div class="flickrstream"><h2 class="widgettitle">%s</h2><script src="http://www.flickr.com/badge_code_v2.gne?%s" type="text/javascript"></script></div>', $content, http_build_query($query_atts));
}

add_shortcode('flickrstream', 'scd_flickr_stream_shortcode');
//USAGE: [flickrstream count="3" layout="h" display="random" size="s" source="all_tag" tag="sea"]Flickr Photos[/flickrstream]


//100. Display Twitter
function scd_twitter($atts, $content=null) {
 extract(shortcode_atts(array(
        "user" => '',
        "limit" => '',
        ), $atts));
    return '<ul id="twitter_update_list"><li></li></ul> <script type="text/javascript" src="'.get_bloginfo('template_directory').'/lib/shortcodes/js/twitter.js"></script> <script type="text/javascript" src="http://twitter.com/statuses/user_timeline/'.$user.'.json?callback=twitterCallback2&amp;count='.$limit.'" ></script>';
}
add_shortcode('twitter', 'scd_twitter');
//USAGE: [twitter user="YourTwitterUserName" limit="5"]


//101. Share Twitter and Facebook
function scd_share_this($content){
    if(!is_feed() && !is_home()) {
        $content .= '<div class="share-this"><a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal">Tweet</a>
                    <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>  <div class="facebook-share-button"><iframe src="http://www.facebook.com/plugins/like.php?href='.urlencode(get_permalink($post->ID)).'&amp;layout=button_count&amp;show_faces=false&amp;width=200&amp;action=like&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:21px;" allowTransparency="true"></iframe> </div></div>';
    }
    return $content;
}
add_shortcode("sharethis", "scd_share_this");

//USAGE:[sharethis]

//102. Get custom posts from WordPress Database
function scd_recentposts($atts, $content = null) {
        extract(shortcode_atts(array(
        "num" => '',
        "cat" => ''
        ), $atts));
        global $post, $image_id,$image_url ;

        $myposts = get_posts('numberposts='.$num.'&order=DESC&orderby=post_date&category='.$cat);
        $shortcode='<ul>';
        if ( has_post_thumbnail() ) {
        $image_id = get_post_thumbnail_id();
		$image_url = wp_get_attachment_image_src($image_id,'medium', true);
        foreach($myposts as $post) :
                setup_postdata($post);
        $shortcode.='<li><a href="'.get_permalink().'"  class="prettyPhoto[mixed]" title="'.the_title("","",false).'"><img src="'.get_bloginfo('template_directory').'/timthumb.php?src=' . $image_url[0] . '&amp;h=40&amp;w=40&amp;zc=1" class="alignleft" alt="'.the_title("","",false).'" /> '.the_title("","",false).'</a> <br> '.excerpt(8).'</li>';
        endforeach;} else {}
        $shortcode.='</ul> ';

        return $shortcode;
        wp_reset_query();
}
add_shortcode("recentposts", "scd_recentposts");
//USAGE: [recentposts num="3" cat="1"]


//103. Animated Custom Post Types
function scd_animated_custom_posttypes($atts, $content = null) {
        extract(shortcode_atts(array(
        "posttype" => '',
        "limit" => '',
        'exclude' 	=> '',
        'bgcolor' 	=> '#fbffec',
        'bordercolor' 	=> '#edffaf',
        "order" => 'DESC'
        ), $atts));
        global $posts, $image_id,$image_url ;

        $shortcode.='<script type="text/javascript" src="'.get_bloginfo('template_directory').'/lib/shortcodes/js/jquery.quovolver.js"></script><script type="text/javascript">jQuery.noConflict(); jQuery(document).ready(function($){ $(\'#customquery p\').quovolver(); }); </script><style type="text/css">#quote_wrap {background:' . $bgcolor. ';border: 1px solid ' . $bordercolor. ';} </style>';

		$shortcode.='<div id="customquery">';

        query_posts('post_type=' . $posttype. '&posts_per_page=' . $limit. '&exclude=' . $exclude. '&post_status=publish&order=' . $order. '&paged=paged');
        if ( have_posts() ) : while ( have_posts() ) : the_post();

        if ( has_post_thumbnail() ) {
        $image_id = get_post_thumbnail_id();
		$image_url = wp_get_attachment_image_src($image_id,'medium', true);

         } else {}

        $shortcode.= '<p><a href="'.get_permalink().'"><img src="'.get_bloginfo('template_directory').'/timthumb.php?src=' . $image_url[0] . '&amp;h=50&amp;w=70&amp;zc=1" class="alignleft" alt="'.the_title("","",false).'" /></a> '.excerpt(100).'</p>';

       	endwhile; else:
		endif;
        $shortcode.='</div>';
        wp_reset_query();
        return $shortcode;
}
add_shortcode("custompostslider", "scd_animated_custom_posttypes");
//USAGE: [custompostslider posttype="page" bordercolor="#efefef" bgcolor="#efefef" limit="3" exclude="" order="DESC"]



//104. Custom Post Type 3 Column Query
function scd_3column_query($atts, $content = null){
	extract(shortcode_atts(array(
    		"posttype" => '',
	        'limit' => '',
            'orderby' => '',
            'order' => ''
	    ), $atts));
	//The Query
    $c = 0;
	query_posts('post_type=' . $posttype. '&posts_per_page=' . $limit. '&post_status=publish&orderby=' . $orderby. '&order=' . $order. '');
	//The Loop
	if ( have_posts() ) : while ( have_posts() ) : the_post();
    	$c++;
        if( $c % 3 == 0 ) $extra_class = ' last';
		else $extra_class = '';
	   $shortcode.= '<div class="one_third' . $extra_class . '"><h4><a href="' . post_permalink() . '">'.the_title("","",false).'</a></h4>';
       if ( has_post_thumbnail() ) {
        $image_id = get_post_thumbnail_id();
		$image_url = wp_get_attachment_image_src($image_id,'large', true);
        $shortcode.= 	'<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]" title="'.the_title("","",false).'"><img src="'.get_bloginfo('template_directory').'/timthumb.php?src=' . $image_url[0] . '&amp;h=90&amp;w=150&amp;zc=1" class="alignleft" alt="'.the_title("","",false).'" title="'.the_title("","",false).'" /></a>';
        } else {
       $shortcode.=  '<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]"><img src="'.get_bloginfo('template_directory').'/images/default/comingsoon.png" height="90" width="150" class="alignleft" alt="'.the_title("","",false).'" title="'.the_title("","",false).'" /></a>';
        }

	   $shortcode.=  excerpt(40);
       $shortcode.=  '<div class="readmore"><a href="' . post_permalink() . '" alt="'.the_title("","",false).'" title="'.the_title("","",false).'">read more...</a></div>';
       $shortcode.= '</div>';
	endwhile; else:
	endif;

	//Reset Query
   wp_reset_query();
    return $shortcode;
}
add_shortcode('query3c', 'scd_3column_query');
//USAGE:[query3c posttype="page" limit="3" orderby="menu_order" order="ASC"] [/query3c]


//105. Custom Post Type 4 Column Query
function scd_4column_query($atts, $content = null){
	extract(shortcode_atts(array(
    		"posttype" => '',
	        'limit' => '',
            'orderby' => '',
            'order' => ''
	    ), $atts));
	//The Query
    $c = 0;
	query_posts('post_type=' . $posttype. '&posts_per_page=' . $limit. '&post_status=publish&orderby=' . $orderby. '&order=' . $order. '');
	//The Loop
	if ( have_posts() ) : while ( have_posts() ) : the_post();
    	$c++;
        if( $c % 4 == 0 ) $extra_class = ' last';
		else $extra_class = '';
	   $shortcode.= '<div class="one_fourth' . $extra_class . '"><h5><a href="' . post_permalink() . '">'.the_title("","",false).'</a></h5>';
       if ( has_post_thumbnail() ) {
        $image_id = get_post_thumbnail_id();
		$image_url = wp_get_attachment_image_src($image_id,'large', true);
        $shortcode.= 	'<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]" title="'.the_title("","",false).'"><img src="'.get_bloginfo('template_directory').'/timthumb.php?src=' . $image_url[0] . '&amp;h=70&amp;w=100&amp;zc=1" class="alignleft blogimg" alt="'.the_title("","",false).'" title="'.the_title("","",false).'" /></a>';
        } else {
       $shortcode.=  '<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]"><img src="'.get_bloginfo('template_directory').'/images/default/comingsoon.png" height="70" width="100" class="alignleft blogimg" alt="'.the_title("","",false).'" title="'.the_title("","",false).'" /></a>';
        }

	   $shortcode.=  excerpt(40);
       $shortcode.=  '<div class="readmore"><a href="' . post_permalink() . '" alt="'.the_title("","",false).'" title="'.the_title("","",false).'">read more &rarr;</a></div>';
       $shortcode.= '</div>';
	endwhile; else:
	endif;

	//Reset Query
   wp_reset_query();
    return $shortcode;
}
add_shortcode('query4c', 'scd_4column_query');
//USAGE:[query4c posttype="page" limit="3" orderby="menu_order" order="ASC"] [/query3c]

//106. Custom Post Type 4 Column Query
function recent_portfolio_query($atts, $content = null){
	extract(shortcode_atts(array(
    		"posttype" => '',
	        'limit' => '',
            'orderby' => '',
            'order' => ''
	    ), $atts));
	//The Query
    $c = 0;
	query_posts('post_type=' . $posttype. '&posts_per_page=' . $limit. '&post_status=publish&orderby=' . $orderby. '&order=' . $order. '');
	//The Loop
	if ( have_posts() ) : while ( have_posts() ) : the_post();
    	$c++;
        if( $c % 4 == 0 ) $extra_class = ' last';
		else $extra_class = '';
	   $shortcode.= '<div class="one_fourth' . $extra_class . '"><h6><a href="' . post_permalink() . '">'.the_title("","",false).'</a></h6>';
       if ( has_post_thumbnail() ) {
        $image_id = get_post_thumbnail_id();
		$image_url = wp_get_attachment_image_src($image_id,'large', true);
        $shortcode.= 	'<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]" title="'.the_title("","",false).'"><img src="'.get_bloginfo('template_directory').'/timthumb.php?src=' . $image_url[0] . '&amp;h=70&amp;w=100&amp;zc=1" class="alignleft blogimg" alt="'.the_title("","",false).'" title="'.the_title("","",false).'" /></a>';
        } else {
       $shortcode.=  '<a href="' . $image_url[0] . '" class="prettyPhoto[mixed]"><img src="'.get_bloginfo('template_directory').'/images/default/comingsoon.png" height="70" width="100" class="alignleft blogimg" alt="'.the_title("","",false).'" title="'.the_title("","",false).'" /></a>';
        }

	   $shortcode.=  excerpt(40);
       $shortcode.=  '<div class="readmore"><a href="' . post_permalink() . '" alt="'.the_title("","",false).'" title="'.the_title("","",false).'">read more &rarr;</a></div>';
       $shortcode.= '</div>';
	endwhile; else:
	endif;

	//Reset Query
   wp_reset_query();
    return $shortcode;
}
add_shortcode('rp4c', 'recent_portfolio_query');
//USAGE:[rp4c posttype="portfolio" limit="4" orderby="menu_order" order="ASC"] [/rp4c]


function theme_shortcode_blockquote($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'align' => false,
		'cite' => false,
	), $atts));

	return '<blockquote' . ($align ? ' class="align' . $align . '"' : '') . '>' . do_shortcode($content) . ($cite ? '<p><cite>- ' . $cite . '</cite></p>' : '') . '</blockquote>';
}
add_shortcode('blockquote', 'theme_shortcode_blockquote');

// Display shortcodes without execution
function scd_showshortcode($atts, $content = null){
	extract(shortcode_atts(array('linebreak'=>''),$atts));
	$brackets = array();
	$brackets[0] = "/\[/";
	$brackets[1] = "/\]/";
	$replace_with = array();
	$replace_with[0] = "&#91;";
	$replace_with[1] = "&#93;";
	$content  = preg_replace($brackets, $replace_with, trim($content));
	$content .= (!empty($linebreak))? "<br />" : "";
	return $content;
} // end scd_showshortcode
add_shortcode('ssc', 'scd_showshortcode');
//USAGE:[ssc] [mycustomshorcode] [/ssc]
?>