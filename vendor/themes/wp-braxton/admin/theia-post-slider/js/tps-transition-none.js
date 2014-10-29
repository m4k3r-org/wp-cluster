/*
 * Copyright 2012, Theia Post Slider, Liviu Cristian Mirea Ghiban.
 */
var tps = tps || {};
tps.transitions = tps.transitions || {};
tps.transitions.none = function(me, previousIndex, index) {
	var $ = jQuery;

	// Remove previous slide
    var previousSlide = previousIndex !== null ? $(me.slides[previousIndex].content) : null;
    if (previousSlide) {
	    previousSlide.detach();
    }

    // Set the current slide
    var slide = $(me.slides[index].content);
    me.destE.append(slide);

	if (previousSlide) {
	    // Call event handlers
		me.onNewSlide();
	}
}