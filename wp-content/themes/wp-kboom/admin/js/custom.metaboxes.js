(function($) {

function HideAll() {
$('#aside_post, #gallery_post, #image_post, #quote_post, #link_post, #video_post, #audio_post').css('display', 'none');
}

function checkMetaboxSettings() {
	HideAll(null);
if	($('#post-format-aside').is(":checked")) {
		$('#aside_post').css('display', 'block');
        $('#postdivrich').css('display', 'none');
}

else if($('#post-format-gallery').is(":checked")) {
        $('#gallery_post, #postdivrich').css('display', 'block');
    }

else if($('#post-format-image').is(":checked")) {
        $('#image_post, #postdivrich').css('display', 'block');
    }

else if($('#post-format-quote').is(":checked")) {
        $('#quote_post').css('display', 'block');
        $('#postdivrich').css('display', 'none');
    }

else if($('#post-format-link').is(":checked")) {
        $('#link_post').css('display', 'block');
        $('#postdivrich').css('display', 'none');
    }

else if($('#post-format-video').is(":checked")) {
        $('#video_post, #postdivrich').css('display', 'block');
    }

else if($('#post-format-audio').is(":checked")) {
        $('#audio_post, #postdivrich').css('display', 'block');
    }

else {
$('#aside_post').css('display', 'none');
$('#quote_post').css('display', 'none');
$('#image_post').css('display', 'none');
$('#link_post').css('display', 'none');
$('#gallery_post').css('display', 'none');
$('#video_post').css('display', 'none');
$('#postdivrich').css('display', 'block');
$('#audio_post').css('display', 'none');
}
}

checkMetaboxSettings() ;
$('.post-format').change(function () {
checkMetaboxSettings() ;

});
})(jQuery);