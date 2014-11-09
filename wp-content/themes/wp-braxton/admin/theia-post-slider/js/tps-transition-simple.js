/*
 * Copyright 2012, Theia Post Slider, Liviu Cristian Mirea Ghiban.
 */
var tps = tps || {};
tps.transitions = tps.transitions || {};
tps.transitions.simple = function(me, previousIndex, index) {
	var $ = jQuery;

    // Init
    var width = me.destE.innerWidth(),
        diff = index - previousIndex;

	// Remove previous slide
    var previousSlide = previousIndex !== null ? $(me.slides[previousIndex].content) : null;
    if (previousSlide) {
	    me.destE.css('height', previousSlide.innerHeight());
	    previousSlide.detach();
    }

    // Set the current slide
    var slide = $(me.slides[index].content);

    if (previousSlide == null) {
	    // Don't animate the first shown slide
        me.destE.append(slide);
    }
    else {
        me.destE.append(slide);

	    // Call event handlers
		me.onNewSlide();

	    // Animate the height
	    me.attachAnimation();
	    me.destE.animate({
		    'height': slide.innerHeight()
	    }, me.options.transitionSpeed, function(me) {
		    return function() {
				$(this)
					.css('position', '')
					.css('width', '');
			    me.destE.css('height', '');
			    me.detachAnimation();
		    }
		}(me));
    }
}