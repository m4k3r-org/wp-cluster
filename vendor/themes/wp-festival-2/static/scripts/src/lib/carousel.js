define(['jquery', 'components/imagesloaded/imagesloaded-built'], function ($, imgLoaded) {

	var circularCarousel = {

		container: null,
		items: null,
		wrapperTotalWidth: 0,
		slideAmount: 0,
		wrapper: null,
		timer: null,
		hoverTimeoutHandle: null,
		resumeTimeoutHandle: null,
		artistExpandedTimeoutHandle: null,
		speed: 20000,

		/**
		 * Setup the container
		 */
		setupContainer: function () {
			this.container.css({
				'overflow': 'hidden'
			});
		},

		/**
		 * Setup the items
		 */
		setupItems: function () {
			this.items.css({
				'float': 'left'
			});
		},

		/**
		 * Wrap the items into another container
		 */
		wrapItems: function () {
			this.wrapper = $('<div class="circular-carousel-wrapper clearfix"></div>');
			this.items.wrapAll(this.wrapper);

			// Reinit after DOM append
			this.wrapper = $('.circular-carousel-wrapper');
			this.wrapper.css({
				'webkit-transition': 'all ' + this.speed + 'ms linear',
				'-moz-transition': 'all ' + this.speed + 'ms linear',
				'-o-transition': 'all ' + this.speed + 'ms linear',
				'transition': 'all ' + this.speed + 'ms linear'
			});
		},

		calculateWrapperWidth: function () {
			var that = this;

			this.wrapperTotalWidth = 0;
			this.items.each(function () {

				that.wrapperTotalWidth += $(this).outerWidth(true);

			});

			this.wrapper.width(this.wrapperTotalWidth);
		},


		startTransition: function (isFirstSlide) {
			var that = this;

			this.wrapper.css({
				'webkit-transition': 'all ' + this.speed + 'ms linear',
				'-moz-transition': 'all ' + this.speed + 'ms linear',
				'-o-transition': 'all ' + this.speed + 'ms linear',
				'transition': 'all ' + this.speed + 'ms linear'
			});


			// Start immediately
			this.slideItems(isFirstSlide);

			// Set interval for the transition
			this.timer = setInterval(function () {

				that.slideItems();

			}, this.speed);
		},

		pauseTransition: function () {
			var computedLeftValue = this.wrapper.css('left');

			this.wrapper.addClass('circular-carousel-no-trans');
			this.wrapper.css({
				'left': computedLeftValue
			});

			clearTimeout(this.timer);
			clearTimeout(this.resumeTimeoutHandle);
		},

		resumeTransition: function () {
			// Calculate the remaining pixels until the item needs to be moved to be last
			var firstItemWidth = $(this.items).first().outerWidth(true);
			var slideOffPixels = parseInt(this.wrapper.css('left'));

			var remainingPixels = firstItemWidth + slideOffPixels;

			// Calculate the new transition speed until the next iteration;
			var newSpeed = remainingPixels * this.speed / firstItemWidth;

			// Slide off the remaining pixels with the new speed, then restart the whole transition
			this.wrapper.css({
				'webkit-transition': 'all ' + newSpeed + 'ms linear',
				'-moz-transition': 'all ' + newSpeed + 'ms linear',
				'-o-transition': 'all ' + newSpeed + 'ms linear',
				'transition': 'all ' + newSpeed + 'ms linear'
			});
			this.wrapper.removeClass('circular-carousel-no-trans');
			this.wrapper.css({
				'left': (-1 * firstItemWidth) + 'px'
			});


			// Set a timeout when to restart the whole transition and resume normal iterations
			var that = this;
			this.resumeTimeoutHandle = setTimeout(function () {

				that.wrapper.addClass('circular-carousel-no-trans');

				// Resume the normal transition
				that.startTransition();

			}, newSpeed);

		},

		slideItems: function (isFirstSlide) {
			// Set the slider to the initial position (left=0)
			this.wrapper.addClass('circular-carousel-no-trans');
			this.wrapper.css('left', '0px');

			// If this is not the first slide transition, then move the first item to the last position
			if (!isFirstSlide) {
				this.moveFirstItemToLast();
			}

			// Get the first item's width and slide that off-screen (this needs to be a negative value)
			var slideOffPixels = -1 * $(this.items).first().outerWidth(true);

			this.wrapper.removeClass('circular-carousel-no-trans');
			this.wrapper.css({
				'left': slideOffPixels + 'px'
			});
		},

		moveFirstItemToLast: function () {
			var firstItem = $(this.items).first();
			firstItem.parent().append(firstItem);

			this.items = $('.artist', this.container);
		},

		eventExpandItem: function () {
			var that = this;

			this.items.on('mouseover', function () {

				$('.artist-out').remove();

				var t = $(this);
				var offset = t.offset();

				if ((offset.left >= 0) && ((offset.left + t.width()) < $(window).width())) {
					// Clone the element and place it under the mouse cursor to create the effect of hover
					var newElem = t.clone();

					newElem.addClass('artist-out');
					newElem.css({
						position: 'absolute',
						top: offset.top - 295,
						left: offset.left,
						zIndex: 99999,
						height: '370px',
						opacity: 1
					});

					$('img', newElem).css('margin-top', '-5px');
					$('#doc').append(newElem);
				}
			});

			this.items.on('mouseout', function (e) {

				that.artistExpandedTimeoutHandle = window.setTimeout(function () {

					$('.artist-out').remove();

				}, 250);

			});

			$('#doc').on('mouseover', '.artist-out', function (e) {

				e.stopPropagation();

				clearTimeout(that.artistExpandedTimeoutHandle);
				clearTimeout(that.hoverTimeoutHandle);

				that.pauseTransition();

			});

			$('#doc').on('mouseout', '.artist-out', function () {

				var t = $(this);

				that.hoverTimeoutHandle = setTimeout(function () {

					t.remove();

					that.resumeTransition();
				}, 250);

			});
		}
	};

	return {

		init: function () {
			circularCarousel.container = $('.artists-carousel');
			circularCarousel.items = $('.artist', circularCarousel.container);

			imgLoaded( circularCarousel.container, function() {

				// Calculate the carousel's height and see if it has enough space on the bottom of the header
				var artistShareButton = $('.buttons .artist-share');

				if ( artistShareButton.length > 0 )
				{
					var header = $('#doc > header');
					var remainingSpace = header.height() - (artistShareButton.outerHeight( true ) + artistShareButton.position().top);

					if ( remainingSpace < circularCarousel.container.height() )
					{
						var spaceDelta = circularCarousel.container.height() - remainingSpace;

						header.height( header.height() + spaceDelta + 40 );
					}
				}


				circularCarousel.container.css({
					'opacity': 1
				});

				// Setup the carousel
				circularCarousel.setupContainer();
				circularCarousel.setupItems();
				circularCarousel.wrapItems();
				circularCarousel.calculateWrapperWidth();

				circularCarousel.eventExpandItem();

				circularCarousel.startTransition(true);
			});


		}
	}
});