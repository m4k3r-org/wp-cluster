/*
 * Copyright 2012, Theia Post Slider, Liviu Cristian Mirea Ghiban.
 */
var tps = tps || {};
tps.transitions = tps.transitions || {};
tps.transitions.slide = function(me, previousIndex, newIndex) {
	var $ = jQuery;

    // Init
    var width = me.destE.innerWidth(),
        diff = newIndex - previousIndex,
        direction = diff > 0 ? 1 : -1;

	// Remove previous slide
    var previousSlide = previousIndex !== null ? $(me.slides[previousIndex].content) : null;
    if (previousSlide) {
	    me.destE.css('height', previousSlide.innerHeight());
	    me.attachAnimation();
	    previousSlide
	        .css('width', width)
		    .css('position', 'absolute')
		    .css('left', 0)
			.animate({
				left: -direction * width
			}, me.options.transitionSpeed, function(me) {
			    return function() {
					$(this)
						.detach()
						.css('position', '')
						.css('left', '');
				    me.detachAnimation();
			    }
			}(me));
    }

    var slide = me.slides[newIndex].content;

    if (previousSlide == null) {
	    // Don't animate the first shown slide
        me.destE.append(slide);
    }
    else {
	    slide.css('width', width);
	    slide.css('visibility', 'hidden');
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
		    .css('left', direction * width)
	        .css('position', 'absolute')
	        .css('visibility', 'visible')
	        .animate({
				left: '0'
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