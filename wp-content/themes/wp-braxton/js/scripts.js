jQuery(document).ready(function($) {
"use strict";


	// iosslider
	
	$(document).ready(function() {
				
		$('.iosslider').iosSlider({
		snapToChildren: true,
		desktopClickDrag: true,
		infiniteSlider: true,
		snapSlideCenter: true,
		onSlideChange: slideChange,
		navNextSelector: $('.next'),
		navPrevSelector: $('.prev'),
		autoSlide: true,
		autoSlideTimer: 5000,
		autoSlideHoverPause: true
		});
				
	});
			
	function slideChange(args) {
			
		try {
			console.log('changed: ' + (args.currentSlideNumber - 1));
		} catch(err) {
		}
			
	}
			
			
	  
  	// Sticky Navigation
  	
	var aboveHeight = $('#featured-wrapper').outerHeight();
	    $(window).scroll(function(){
	    	if ($(window).scrollTop() > aboveHeight){
	    	$('#nav-wrapper').addClass('fixed').css('top','0').next()
	    	.css('margin-top','54px');
	    	} else {
	    	$('#nav-wrapper').removeClass('fixed').next()
	    	.css('margin-top','0');
	    	}
		});
		
		
	// Sticky Sidebar
		
	$(window).load(function(){
	    $('#sidebar-wrapper').stickyMojo({
		footerID: '#footer-wrapper',
		contentID: '#content-main'
		});
		
	});
	

	// Mobi nav menu  
	
  	$("#mobi-nav select").change(function() {
	 window.location = $(this).find("option:selected").val();
	});
	

	// Lazy Load

	
	$("img.lazy").show().lazyload({ 
	    effect: "fadeIn",
	    threshold : 200
	}).removeClass("lazy");
	$(document).ajaxStop(function(){
	"use strict";
	    $("img.lazy").lazyload({ 
	        effect: "fadeIn" 
	    }).removeClass("lazy");
	});
	
	
	// Search Toggle
	$("#search-button").click(function(){
	  $("#search-bar").slideToggle();
  	});



});