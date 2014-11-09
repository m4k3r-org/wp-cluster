/*
 * Copyright 2012, Theia Post Slider, Liviu Cristian Mirea Ghiban.
 */
var tps = tps || {};
tps.slideshowsOnPage = 0;
tps.createSlideshowDefaults = {
	src: '',
	dest: '',
	nav: [],
	navText: '%{currentSlide} of %{totalSlides}',
	helperText: '',
	defaultSlide: 0,
	transitionEffect: 'none',
	transitionSpeed: 400,
	keyboardShortcuts: false,
	numberOfSlides: 0, // Total number of slides, including the ones not loaded.
	slides: [],
	prevPost: null,
	nextPost: null,
    prevText: null,
    nextText: null,
    buttonWidth: 0,
    prevText_post: null,
    nextText_post: null,
    buttonWidth_post: 0,
	onShowPost: null,
	postUrl: null,
	postId: null,
	refreshAds: false,
	refreshAdsEveryNSlides: 1,
	siteUrl: '/'
};
tps.createSlideshow = function(options) {
	var i,
		me = this,
		$ = jQuery,
		defaults = tps.createSlideshowDefaults;
	me.options = $.extend(defaults, options);
	me.srcE = $(me.options.src);
	me.destE = $(me.options.dest);
	if (me.destE.length == 0) {
		return;
	}

	tps.slideshowsOnPage++;
	me.navEl = [];
	for (i = 0; i < me.options.nav.length; i++) {
		var e = $(me.options.nav[i]);
		if (e.length == 1) {
			me.navEl.push({
				container: e
			});
		}
	}

	// Initialize variables.
	me.currentPostId = me.options.postId;
	me.isLoading = false;
	me.cachedPosts = {};
	me.slidesSinceLastAdRefresh = 0;

	// Remote loading queue.
	me.remoteLoadingQueue = async.queue(function(task, callback) {
	    callback();
	}, 1);

	// The current slide
	me.currentSlide = null;

	// The slide that is currently displayed. This may lag behind me.currentSlide because of the animations.
	me.currentlyDisplayedSlide = null;

	// The number of animations that are currently running.
	me.animations = 0;
	me.navEffect = tps.transitions[me.options.transitionEffect];

	// A queue that is executed when no animation is running.
	me.queue = [];
	me.asyncQueue = async.queue(function(task, callback) {
	    callback();
	}, 1);

	// Initialization function
	me.init = function() {
		var i;

		// Get slides from me.options.slides
		me.slides = [];
		for (i in me.options.slides) {
			if (me.options.defaultSlide != i) {
				var e = $('<div>');
				e.html(me.options.slides[i].content);
				me.options.slides[i].content = e;
			}

			me.slides[i] = me.options.slides[i];
		}

		// Get slides from me.options.src element.
		$(me.options.src).each(function(index, slide) {
			slide = $(slide);
			slide.detach();
			index = me.options.defaultSlide + index;
			me.slides[index] = me.slides[index] || {};
			me.slides[index].title = document.title;
			me.slides[index].permalink = document.location.href;
			me.slides[index].content = slide;
		});

		// Count the slides.
		me.numberOfSlides = me.options.numberOfSlides;

		// Setup the navigation bars.
		for (i = 0; i < me.navEl.length; i++) {
			var navEl = me.navEl[i];
			navEl.text = navEl.container.find('._text');
			navEl.prev = navEl.container.find('._prev')
				.click(function(that) {
					return function() {
						that.setPrev();
						return false;
					}
				}(me));
			navEl.next = navEl.container.find('._next')
				.click(function(that) {
					return function() {
						that.setNext();
						return false;
					}
				}(me));

			navEl.title = navEl.container.find('._title');

			// Get the default slide's title. The title will be the same for all navigation bars, so get it only from the first.
			if (i === 0) {
				me.slides[me.options.defaultSlide].shortCodeTitle = navEl.title.html();
			}

			/*
			 Add _active class on mousedown. This is a fix for IE and Opera which don't match :active on container elements.
			 Also, return false to prevent double click context menu in Opera.
			*/
			navEl.container.find('._prev, ._next')
				.mousedown(function() {
					$(this).addClass('_active');
					return false;
				})
				.mouseup(function() {
					$(this).removeClass('_active');
				});
		}

		// Show the first slide
		me.setSlide(me.options.defaultSlide);

		// Enable keyboard shortcuts
		if (me.options.keyboardShortcuts) {
			$(document).keydown(function(me) {
				return function(e) {
					// Disable shortcut if there is more than one slideshow on the page.
					if (tps.slideshowsOnPage > 1) {
						return;
					}

					// Disable shortcut if the target element is editable (input boxes, textareas, etc.).
					if (
						this !== e.target &&
						(
							/textarea|select/i.test( e.target.nodeName ) ||
							e.target.type === "text" ||
							(
								$(e.target).prop &&
								$(e.target).prop('contenteditable') == 'true'
							)
						)
					) {
						return;
					}

					switch (e.which) {
						case 37:
							me.setPrev();
							return false;

						case 39:
							me.setNext();
							return false;
					}
				};
			}(me));
		}

		// Add history handler.
		var history = window.History;
		if (history.enabled) {
			history.Adapter.bind(window, 'statechange', function(me) {
				return function() {
					var state = History.getState();
					if (state.data.currentPostId != undefined) {
						if (state.data.currentSlide != undefined) {
							me.setSlide(state.data.currentSlide);
						}
						else {
							me.setSlide(me.options.defaultSlide);
						}
					}
				};
			}(me));
		}
	};

	// Enqueue a function to be executed when no animation is running.
	me.addToQueue = function(type, merge, func) {
		if (merge) {
			// If there is another call enqueued of the same type, then remove it.
			// This is useful in case the user clicks the back/next buttons very fast and the slider lags behind because of the animations.
			// Basically, this will skip the intermediary slides and display the last one.
			for (var i = 0; i < me.queue.length; i++) {
				if (me.queue[i].type == type) {
					me.queue.splice(i, 1);
				}
			}
		}
		me.queue.push({
			type: type,
			func: func
		});
		me.executeQueue();
	};

	// Execute the queue while there are no active animations.
	me.executeQueue = function() {
		me.asyncQueue.push({name: ''}, function(me) {
			return function(err) {
				while (me.animations == 0 && me.queue.length > 0) {
					var q = me.queue.shift();
					q.func(me);
				}
			};
		}(me));
	};

	// Attach an animation (i.e. an animation has started). The queue won't be executed until the animations have all finished.
	me.attachAnimation = function() {
		me.animations++;
	};

	// Detach an animation (i.e. an animation has finished).
	me.detachAnimation = function() {
		me.animations--;
		me.executeQueue();
	};

	// Load slides remotely
	me.loadSlides = function(slides, callback) {
		me.remoteLoadingQueue.push({name: ''}, function(me, slides, callback) {
			return function(err) {
				// Check if the slides are already loaded.
				var allSlidesAreLoaded = true;
				for (var i in slides) {
					if (!(slides[i] in me.slides)) {
						allSlidesAreLoaded = false;
						break;
					}
				}
				if (allSlidesAreLoaded) {
					if (callback) {
						callback();
					}
					return;
				}

				// Load the slides and don't load anything else in the meantime.
				me.remoteLoadingQueue.concurrency = 0;
				$.ajax({
					dataType: 'json',
					data: {
						theiaPostSlider: 'get-slides',
						postId: me.currentPostId,
						slides: slides
					},
					url: me.options.siteUrl,
					success: function(me) {
						return function(data) {
							if (!data) {
								return;
							}
							if (data.postId == me.currentPostId) {
								// Add each slide that isn't already loaded.
								for (var i in data.slides) {
									// Add to the current slides.
									if (!(i in me.slides)) {
										var e = $('<div>');
										e.html(data.slides[i].content);
										data.slides[i].content = e;
										me.slides[i] = data.slides[i];
									}
									// Add to the cached version of this post.
									if (
										me.currentPostId in me.cachedPosts &&
										!(i in me.cachedPosts[me.currentPostId].slides)
									) {
										me.cachedPosts[me.currentPostId].slides[i] = data.slides[i];
									}
								}
							}
						};
					}(me),
					complete: function(me, callback) {
						return function() {
							if (callback) {
								callback();
							}
							me.remoteLoadingQueue.concurrency = 1;
							me.remoteLoadingQueue.push({});
						};
					}(me, callback)
				});
			};
		}(me, slides, callback));
	};

	// Set the current slide.
	me.setSlide = function(index, isCallback) {
		if (me.isLoading == true && isCallback != true) {
			return;
		}

		// Is the slide already displayed?
		if (me.currentSlide == index) {
			return;
		}

		// Is the slide not yet loaded?
		if (!(index in me.slides)) {
			if (!isCallback) {
				me.showLoading();
				me.loadSlides([index], function(me, index) {
					return function() {
						me.hideLoading();
						me.setSlide(index, true);
					}
				}(me, index));
				return;
			}
			else {
				// The slide could not be loaded via AJAX. Abort.
				return;
			}
		}

		var previousSlide = me.currentSlide;
		me.currentSlide = index;

		if (previousSlide != null) {
            // Scroll the window up, if the beginning of the slide is out-of-view.            
			// Get the lowest offset.top
			var scrollTop = me.destE.offset().top;
			for (var i = 0; i < me.navEl.length; i++) {
				scrollTop = Math.min(scrollTop, me.navEl[i].container.offset().top);
			}

			if ($(window).scrollTop() > scrollTop) {
				//$('body,html').scrollTop(scrollTop);
				$('body,html').animate({scrollTop: scrollTop}, me.options.transitionSpeed);
			}
		}

		// Set the title text.
		me.updateTitleText();

		// Set the navigation bars.
		me.updateNavigationBars();

		// Change URL, but only if this isn't the first slide set (i.e. the default slide).
		var history = window.History;
		if (history.enabled) {
			var url = me.slides[me.currentSlide].permalink;
			if (url && previousSlide !== null) {
				var f;
				if (url == window.location) {
					f = history.replaceState;
				}
				else {
					f = history.pushState;
				}
				f({
					currentSlide: index,
					currentPostId: me.currentPostId
				}, me.slides[me.currentSlide].title, url);
			}
		}

		// Show the slide.
		me.addToQueue('showSlide', true, function(me) {
			me.showSlide();
		});

		// Refresh ads.
		if (previousSlide != null) {
			if (me.options.refreshAds) {
				me.slidesSinceLastAdRefresh++;

				if (me.slidesSinceLastAdRefresh >= me.options.refreshAdsEveryNSlides) {
					me.slidesSinceLastAdRefresh = 0;

					var p = null;

					if (typeof pubService === 'undefined') {
					    if (typeof googletag === 'undefined') {
					        return;
					    }
					    else {
					        p = googletag.pubads();
					    }
					}
					else {
					    p = pubService;
					}

					if (p) {
					    p.refresh();
					}
				}
			}
		}

		// Preload
		var direction;
		if (previousSlide == null) {
			direction = 1;
		}
		else {
			direction = me.currentSlide - previousSlide;
			direction = Math.max(Math.min(direction, 1), -1);
		}
		var slideToPreload = me.currentSlide + direction;
		if (
			slideToPreload >= 0 &&
			slideToPreload < me.numberOfSlides &&
			!(slideToPreload in me.slides)
		) {
			me.loadSlides([slideToPreload]);
		}
	};

	// Show (display) the current slide using the chosen animation.
	me.showSlide = function() {
		// Don't do anything if the current slide is already shown.
		if (me.currentlyDisplayedSlide == me.currentSlide) {
			return;
		}

		// Track the pageview if this isn't the first slide displayed.
		if (me.currentlyDisplayedSlide != null && me.slides[me.currentSlide]['permalink']) {
			// URL Path
			var path = me.slides[me.currentSlide]['permalink'].split('/');
			if (path.length >= 4) {
				path = '/' + path.slice(3).join('/');

				// Google Analytics Async.
				if (typeof _gaq !== 'undefined' && typeof _gaq.push !== 'undefined') {
					_gaq.push(['_trackPageview', path]);
				}
				// Google Analytics Traditional (ga.js).
				else if (typeof pageTracker !== 'undefined' && typeof pageTracker._trackPageview !== 'undefined') {
					pageTracker._trackPageview(path);
				}
				else if (typeof ga !== 'undefined') {
					ga('send', 'pageview', path);
				}

				// Piwik
				if (typeof piwikTracker !== 'undefined' && typeof piwikTracker.trackPageView !== 'undefined') {
					piwikTracker.trackPageView(path);
				}

				// StatCounter
				if (typeof sc_project !== 'undefined' && typeof sc_security !== 'undefined') {
					var img = new Image();
					img.src = '//c.statcounter.com/' + sc_project + '/0/' + sc_security + '/1/';
				}

				// Quantcast
				if (typeof _qacct !== 'undefined') {
					var img = new Image();
					img.src = '//pixel.quantserve.com/pixel/' + _qacct + '.gif';
				}
			}
		}

		var previousIndex = me.currentlyDisplayedSlide;
		me.currentlyDisplayedSlide = me.currentSlide;

		// Change the slide while applying a certain effect/animation.
		me.navEffect(me, previousIndex, me.currentlyDisplayedSlide);
	};

	// Function that is called right after a new slide has been added to the DOM, and right before the animation (if available) has started.
	me.onNewSlide = function() {
		// "BJ Lazy Load" plugin.
		$(".lazy-hidden").not(".data-lazy-ready").one("scrollin.bj_lazy_load", {
			distance: 200
		}, function() {
			var b =$(this),
				d = b.attr("data-lazy-type");
			if (d == "image") {
				b.hide().attr("src", b.attr("data-lazy-src")).removeClass("lazy-hidden").fadeIn()
			} else if (d == "iframe") {
				b.replaceWith(c(b.attr("data-lazy-src")))
			}
		}).addClass("data-lazy-ready");

		// "Lazy Load" plugin.
		var events = $('body').data('events');
		if (events && events['post-load']) {
			for (var i = 0; i < events['post-load'].length; i++) {
				if (events['post-load'][i].handler.name == 'lazy_load_init') {
					events['post-load'][i].handler();
				}
			}
		}		

		// Fire events.
		$(document).trigger('theiaPostSlider.changeSlide', [me.currentSlide]);
	};

	// Update the title text.
	me.updateTitleText = function() {
		var shortCodeTitle = me.slides[me.currentSlide].shortCodeTitle;
		if (!shortCodeTitle) {
			shortCodeTitle = '<span class="_helper">' + me.options['helperText'] + '</span>';
		}
		for (i = 0; i < me.navEl.length; i++) {
			me.navEl[i].title.html(shortCodeTitle);
		}
	};

	// Update the navigation bar's text and buttons.
	me.updateNavigationBars = function() {
		for (var i = 0; i < me.navEl.length; i++) {
			var navEl = me.navEl[i];
			var navText;
			if (me.numberOfSlides == 1) {
				navText = '';
			}
			else {
				navText = me.options.navText;
				navText = navText.replace('%{currentSlide}', me.currentSlide + 1);
				navText = navText.replace('%{totalSlides}', me.numberOfSlides);
			}
			navEl.text.html(navText);

			// Update buttons.
			me.updateNavigationBarButton(navEl, false);
			me.updateNavigationBarButton(navEl, true);
		}
	};

	// Update a button from a navigation bar.
	me.updateNavigationBarButton = function(navEl, direction) {
		var width,
			html,
			directionName = direction ? 'next' : 'prev',
			buttonEl = navEl[directionName];
		if (
			(direction == false && me.options.prevPost && me.currentSlide == 0) ||
			(direction == true  && me.options.nextPost && me.currentSlide == me.numberOfSlides - 1)
		) {
			width = me.options.buttonWidth_post;
			html = me.options[directionName + 'Text_post'];
		}
		else {
			width = me.options.buttonWidth;
			html = me.options[directionName + 'Text'];
		}
		buttonEl.find('._2')
			.css('width', width > 0 ? width : '')
			.html(html);

		// Disable or enable
		if (
			(direction == false && me.options.prevPost == null && me.currentSlide == 0) ||
			(direction == true  && me.options.nextPost == null && me.currentSlide == me.numberOfSlides - 1)
		) {
			buttonEl.addClass('_disabled');
		}
		else {
			buttonEl.removeClass('_disabled');
		}
	};

	// Go to the previous slide.
	me.setPrev = function() {
		if (me.currentSlide == 0) {
			if (me.options.prevPost) {
				me.showPost(me.options.prevPost);
			}
		}
		else {
			me.setSlide(me.currentSlide - 1);
		}
	};

	// Go to the next slide.
	me.setNext = function() {
		if (me.currentSlide == me.numberOfSlides - 1) {
			if (me.options.nextPost) {
				me.showPost(me.options.nextPost);
			}
		}
		else {
			me.setSlide(me.currentSlide + 1);
		}
	};

	me.showPost = function(postUrl) {
		document.location = postUrl;
	};

	// Set the transition properties (used in Live Preview).
	me.setTransition = function(options) {
		var defaults = {
			'effect': me.options.transitionEffect,
			'speed': me.options.transitionSpeed
		};
		options = $.extend(defaults, options);
		me.options.transitionEffect = options.effect;
		me.options.transitionSpeed = options.speed;
		me.navEffect = tps.transitions[me.options.transitionEffect];
	};

	// Set the navigation bar's text template (used in Live Preview).
	me.setNavText = function(text) {
		me.options.navText = text;
		me.updateNavigationBars();
	};

	// Set the title for all slides (used in Live Preview).
	me.setTitleText = function(text) {
		for (i = 0; i < me.slides.length; i++) {
			me.slides[i].shortCodeTitle = '';
		}
		me.options.helperText = text;
		me.updateTitleText();
	};

	me.showLoading = function() {
		me.isLoading = true;
		for (i = 0; i < me.navEl.length; i++) {
			me.navEl[i].container
				.append($('<div class="_loading"></div>'))
				.find('._buttons > a').addClass('_disabled');
		}
	};

	me.hideLoading = function() {
		me.isLoading = false;
		for (i = 0; i < me.navEl.length; i++) {
			me.navEl[i].container.find('._loading').remove();
		}
		me.updateNavigationBars();
	};

	// Initialize the slider.
	me.init();

	return me;
};