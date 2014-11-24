/*
 * Copyright 2012, Theia Post Slider, Liviu Cristian Mirea Ghiban.
 */
var tps = tps || {};
tps.transitions = tps.transitions || {};
tps.transitions.fade = function(me, previousIndex, index) {
	var $ = jQuery;

    // Init
    var width = me.destE.innerWidth(),
        diff = index - previousIndex,
        direction = diff > 0 ? 1 : -1;

	// Remove previous slide
    var previousSlide = previousIndex !== null ? $(me.slides[previousIndex].content) : null;
    if (previousSlide) {
	    me.destE.css('height', previousSlide.innerHeight());
	    me.attachAnimation();
	    previousSlide
		    .css('position', 'absolute')
			.animate({
				opacity: 0
			}, me.options.transitionSpeed, function(me) {
			    return function() {
					$(this)
						.detach()
						.css('position', '')
						.css('opacity', 1);
				    me.detachAnimation();
			    }
			}(me));
    }

    // Set the current slide
    var slide = $(me.slides[index].content);

    if (previousSlide == null) {
	    // Don't animate the first shown slide
	    slide.css('width', width);
        me.destE.append(slide);
    }
    else {
	    slide.css('width', width);
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
					.css('position', '');
			    me.detachAnimation();
		    }
		}(me));

	    // Animate the new slide
	    me.attachAnimation();
	    slide
		    .css('opacity', 0)
	        .animate({
				opacity: 1
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