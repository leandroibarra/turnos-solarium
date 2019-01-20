/**************************************************************************
 * Slider jQuery Plugin
**************************************************************************/
(function ($) {
	$.fn.extend({
		mySlider: function(poParams) {
			return this.each(function() {
				new mySlider(this, poParams);
			});
		},
		mySliderPause: function() {
			var oInstance = $(this).data('instance');
			oInstance.pause();
		},
		mySliderPrev: function() {
			var oInstance = $(this).data('instance');

			if (oInstance && !oInstance.options.bIsBusy) {
				oInstance.flowTo('left');
				oInstance.options.fnOnPrev();
			}
		},
		mySliderNext: function() {
			var oInstance = $(this).data('instance');

			if (oInstance && !oInstance.options.bIsBusy) {
				oInstance.flowTo('right');
				oInstance.options.fnOnNext();
			}
		},
		mySliderTo: function(piSlide) {
			piSlide--;

			var oInstance = $(this).data('instance');

			if (oInstance && typeof piSlide == 'number' && !oInstance.options.bIsBusy) {
				if (piSlide >= 0 && piSlide < oInstance.options.iAmountSlides && piSlide != oInstance.options.iCurrentSlideIndex) {
					oInstance.flowTo(piSlide);
					oInstance.options.fnOnSliderTo();
				}
			}
		},
		mySliderIn: function(poSlide) {
			var oInstance = $(this).data('instance');

			if (poSlide.hasClass('my-slider-slide') && !oInstance.options.bIsBusy)
				oInstance.mySliderIn(poSlide);
		},
		mySliderOut: function(poSlide) {
			var oInstance = $(this).data('instance');

			if (poSlide.hasClass('my-slider-slide') && !oInstance.options.bIsBusy)
				oInstance.mySliderOut(poSlide);
		},
		mySliderAnimate: function(poSlide, poToParams) {
			var oInstance = $(this).data('instance');

			if (poSlide.hasClass('my-slider-slide') && !oInstance.options.bIsBusy)
				oInstance.mySliderAnimate(poSlide, poToParams);
		}
	});

	var mySlider = function(psMySliderContainer, poOptions) {
		var sSelfUri = getSelfUrl();

		var oOptions = {
			// Responsive mode
			bResponsive: true,
			// Full width mode (If it's true, width option will be used for responsive mode switching under it)
			bFullWidth: false,
			// Full size mode
			bFullSize: false,

			// Slider will slide automatically
			bAutoSlide: true,
			// Pause when mouse is over
			bPauseOnHover: true,
			// Slide position to start
			iSlideToStart: 1,

			// Progress bar color
			sProgressBarColor: 'rgba(255, 255, 255, 0.5)',
			// Progress bar thick
			sProgressBarThickness: '3px',

			// Loading gif source
			sLoadingImage: sSelfUri + 'images/loading.gif',

			// Lazy load images mode
			bLazyLoadImages: isLowerThanIE8() ? false : true,
			// Keyboard navigation
			bKeyNavigation: true,

			oDefaults: {
				// Previous and next arrow fade effect duration
				iNavigationFadeDuration: 200,
				// Slide Delay
				iSlideDelay: 6000
			},

			oArrow: {
				// Allowed: hover, show, hide
				sVisibility: 'hover',
				// Arrows' horizontal offset from left and right edge of the slider. Allowed: inside, outside
				sPositionOffset: 30,
				// Previous image source for left arrow
				sPrevImage: sSelfUri + 'images/arrow-left.png',
				// Next image source for right arrow
				sNextImage: sSelfUri + 'images/arrow-right.png',
				// Previous image source for active left arrow
				sPrevImageActive: sSelfUri + 'images/arrow-left-active.png',
				// Next image source for active right arrow
				sNextImageActive: sSelfUri + 'images/arrow-right-active.png',
				// Responsive level for arrow
				iResponsiveLevel: 2
			},

			// Allowed: bullet, none
			sNavigationType: 'bullet',

			oBullet: {
				// Allowed: hover, show
				sVisibility: 'show',
				// Allowed: top_left, top_center, top_right, bottom_left, bottom_center, bottom_right
				sPosition: 'bottom_center',
				// Horizontal offset of the bullet container
				iOffsetX: 0,
				// Vertical offset of the bullet container
				iOffsetY: 10,
				// Opacity for no active bullet item
				iOpacity: 1,
				// Margin of each bullet
				iMargin: 5,
				// Bullet source image
				sImage: sSelfUri + 'images/bullet.png',
				// Bullet active source image
				sImageActive: sSelfUri + 'images/bullet-active.png',
				// Left image source for bullet container
				sImageLeft: '',
				// Right image source for bullet container
				sImageRight: '',
				// Opacity for active bullet
				iOpacityActive: 1,
				// Responsive level for bullet
				iResponsiveLevel: 2
			},
			
			// Slider resumed function
			fnOnResume: function() {},
			// Slider paused function
			fnOnPause: function() {},
			// Slider slides to prev (by arrow left) function
			fnOnPrev: function() {},
			// Slider slides to next (by arrow right) function
			fnOnNext: function() {},
			// Slider slides to any (by any arrow) function
			fnOnSliderTo: function() {},
			// Before sliding started function
			fnOnSlidingStart: function() {},
			// After sliding completed function
			fnOnSlidingComplete: function() {},
			// Slider clicked function
			fnOnSliderClick: function(poTarget) {},
			// Slider hovered function
			fnOnSliderHover: function(poTarget, pbIsOver) {},
			// Slider redraw function
			fnOnRedraw: function() {}
		};

		var oInstance = this;

		var psMySliderContainer = $(psMySliderContainer).addClass('slide-container').wrapInner('<div class="slide-wrapper"></div>');
		var oSlideWrapper = psMySliderContainer.find('.slide-wrapper');
		var oSlideOthers = $('<div class="slide-others" />').appendTo(psMySliderContainer);

		oInstance.init = function() {
			// Copy default options defined below
			var oOptionsTmp = oOptions;

			// Merge default options with parameters options
			oInstance.options = $.extend({}, oOptionsTmp, poOptions);

			// Merge arrow options
			if (poOptions.oArrow)
				oInstance.options.oArrow = $.extend({}, oOptionsTmp.oArrow, poOptions.oArrow);

			// Merge bullet options
			if (poOptions.oBullet)
				oInstance.options.oBullet = $.extend({}, oOptionsTmp.oBullet, poOptions.oBullet);

			// Set some data
			psMySliderContainer.data('instance', oInstance);
			psMySliderContainer.data('margin-bottom', psMySliderContainer.css('margin-bottom'));

			if (oInstance.options.bFullSize)
				oInstance.options.bFullWidth = true;

			// Obtain slider container style attribute
			var oMySliderContainerStyle = psMySliderContainer[0].style;

			// Check width and height options
			if (!oInstance.options.width)
				oInstance.options.width = oMySliderContainerStyle.width;

			if (!oInstance.options.height)
				oInstance.options.height = oMySliderContainerStyle.height;

			// Set responsive width and responsive height options from parameters options
			oInstance.options.iResponsiveWidth = oInstance.options.width;
			oInstance.options.iResponsiveHeight = oInstance.options.height;

			// Check type and set responsive width and responsive height
			if ($.isNumeric(oInstance.options.iResponsiveWidth))
				oInstance.options.iResponsiveWidth += 'px';

			if ($.isNumeric(oInstance.options.iResponsiveHeight))
				oInstance.options.iResponsiveHeight += 'px';

			// Parse width and height options
			if ((oInstance.options.width + '').indexOf('%') == -1)
				oInstance.options.width = parseInt(oInstance.options.width);

			if ((oInstance.options.height + '').indexOf('%') == -1)
				oInstance.options.height = parseInt(oInstance.options.height);

			// Set rest of options
			oInstance.options.bOriginalResponsive = oInstance.options.bResponsive;
			oInstance.options.iPaddingLeft = parseInt(psMySliderContainer.css('padding-left'));
			oInstance.options.iPaddingRight = parseInt(psMySliderContainer.css('padding-right'));
			oInstance.options.iPaddingTop = parseInt(psMySliderContainer.css('padding-top'));
			oInstance.options.iPaddingBottom = parseInt(psMySliderContainer.css('padding-bottom'));

			// Set new css styles to container
			psMySliderContainer.css({
				width: oInstance.options.iResponsiveWidth,
				height: oInstance.options.iResponsiveHeight,
				padding: 0
			});

			// Update width and height options
			oInstance.options.width = psMySliderContainer.width();
			oInstance.options.height = psMySliderContainer.height();

			// Set amount slides option
			oInstance.options.iAmountSlides = psMySliderContainer.find('.my-slide').length;

			if (oInstance.options.iAmountSlides < 2) {
				if (oInstance.options.iAmountSlides == 0)
					return false;

				// Update the options in case of there is one slide only
				oInstance.options.iSlideToStart = 0;
				oInstance.options.sNavigationType = 'none';
				oInstance.options.bKeyNavigation = false;
				oInstance.options.oArrow.sVisibility = 'hide';
			} else {
				// Update slide to start position
				oInstance.options.iSlideToStart = Math.max(0, Math.min(oInstance.options.iSlideToStart - 1, oInstance.options.iAmountSlides - 1));
			}

			// Prepare current slide index and current slide object
			oInstance.options.iCurrentSlideIndex = (oInstance.options.iSlideToStart == 0) ? oInstance.options.iAmountSlides - 1 : oInstance.options.iSlideToStart - 1;
			oInstance.options.oCurrentSlide = psMySliderContainer.find('.my-slide:eq(' + (oInstance.options.iCurrentSlideIndex) + ')');			

			// Build timeouts array
			oInstance.options.aTimeouts = [];

			// Parse each slide background (handle anchors)
			psMySliderContainer.find('img.my-slide-background').each(function() {
				var oBackground = $('<div class="my-slide-background" />');

				if ($(this).parent().is('a')) {
					var sTarget = (typeof $(this).parent().attr('target') != 'undefined') ? ' target="' + $(this).parent().attr('target') + '"' : '';

					$('<a class="my-slider-bg-link" href="' + $(this).parent().attr('href') + '"' + sTarget + '></a>').appendTo($(this).closest('.my-slide'));

					$(this).unwrap();
				}

				$(this).removeClass('my-slide-background').wrap(oBackground);
			});

			// Build loading gif
			oInstance.options.oLoading = $('<img class="my-slider-loading" src="' + oInstance.options.sLoadingImage + '" />').appendTo(psMySliderContainer);

			// Build progress bar
			oInstance.options.oProgressBar = $('<div class="my-slider-progress-bar my-slider-top">').appendTo(psMySliderContainer);

			// Complete rest of progress bar styles from options
			oInstance.options.oProgressBar.css({
				backgroundColor: oInstance.options.sProgressBarColor,
				height: parseInt(oInstance.options.sProgressBarThickness)
			});

			// Parse slide transition effects
			psMySliderContainer.find('.my-slide').each(function() {
				var sTransitionData = ($(this).data('transition') + '').toLowerCase();
				var aTransitionsData = sTransitionData.split(',');
				var aTransitions = [];
				var oMySlide = $(this);

				if (sTransitionData.indexOf('all') != -1) {
					aTransitions.push('all');
				}  else {
					for (var iIndex=0; iIndex<aTransitionsData.length; iIndex++) {
						var iTransition = aTransitionsData[iIndex];

						if ($.isNumeric(iTransition)) {
							var mTransition = getAllowedTransitions(iTransition);

							if (mTransition)
								aTransitions.push(iTransition);
						}
					}
				}

				// Set all in case of parameter is not valid
				if (aTransitions.length == 0 || isLowerThanIE8())
					aTransitions = ['all'];

				// Set transition list
				oMySlide.data('transitionlist', aTransitions);

				oMySlide.find('.my-slider-slide').wrapAll('<div class="my-slider-slides" />');

				// Set transition duration in case of data parameter is missing
				if (!oMySlide.data('duration'))
					oMySlide.data('duration', oInstance.options.oDefaults.iSlideDelay);

				oMySlide.find('img').each(function() {
					if (oInstance.options.bLazyLoadImages && (!$(this).data('src') || $(this).data('src') == ''))
						$(this).data('src', $(this).attr('src'));
				});
			});

			oSlideWrapper.mousedown(function(event) {
				oInstance.options.iSliderDragStartX = event.pageX;
			});

			$(document)
				.mousemove(function(event) {
					if (typeof oInstance.options.iSliderDragStartX != 'undefined' && oInstance.options.iSliderDragStartX != -1) {
						var iDragDistance = oInstance.options.iSliderDragStartX - event.pageX;

						if (!oInstance.options.bIsBusy)
							if (psMySliderContainer.find('.my-slide').length > 1)
								if (iDragDistance > 0)
									oInstance.flowTo('right');
								else if (iDragDistance < -0)
									oInstance.flowTo('left');

						oInstance.options.iSliderDragStartX = event.pageX;
					}
				})
				.mouseup(function(event) {
					oInstance.options.iSliderDragStartX = -1;
				});

			// Initializing for responsive
			psMySliderContainer.find('.my-slider-slide').each(function() {
				var oSlide = $(this);

				oSlide.data('bAnchored', false);

				if ((oSlide[0].style.width + '').indexOf('%') !== -1)
					oSlide.css('width', psMySliderContainer.width() / 100 * parseInt(oSlide[0].style.width));

				// Set data attributes to slide
				oSlide.attr({
					'data-left': oSlide.css('left'),
					'data-top': oSlide.css('top'),
					'data-width': oSlide.width(),
					'data-height': oSlide.height(),
					'data-margin_top': oSlide.css('margin-top'),
					'data-margin_right': oSlide.css('margin-right'),
					'data-margin_bottom': oSlide.css('margin-bottom'),
					'data-margin_left': oSlide.css('margin-left'),
					'data-padding_left': oSlide.css('padding-left'),
					'data-padding_right': oSlide.css('padding-right'),
					'data-padding_top': oSlide.css('padding-top'),
					'data-padding_bottom': oSlide.css('padding-bottom'),
					'data-border_left': oSlide.css('border-left-width'),
					'data-border_right': oSlide.css('border-right-width'),
					'data-border_top': oSlide.css('border-top-width'),
					'data-border_bottom': oSlide.css('border-bottom-width'),
					'data-font_size': oSlide.css('font-size'),
					'data-line_height': oSlide.css('line-height'),
					'data-opacity': 1
				});

				// Update opacity data
				if ($.isNumeric(oSlide.css('opacity')))
					oSlide.data('opacity', oSlide.css('opacity'));

				// Parse transition in and transition out effects
				$(['transitionin', 'transitionout']).each(function(iKey, sValue) {
					if (oSlide.data(sValue)) {
						var aTransitionData = (oSlide.data(sValue) + '').split(';');

						for (var iIndex=0; iIndex<aTransitionData.length; iIndex++) {
							var aTransition = aTransitionData[iIndex].split(':');

							if (aTransition.length == 2)
								oSlide.data(aTransition[0].replace(/ /g, '') + sValue.replace('transition', ''), aTransition[1]);
						}
					}
				});

				// Set default options for slide transition in
				oSlide.attr({
					'data-offsetxin': (typeof oSlide.data('offsetxin') == 'undefined') ? 0 : parseFloat(oSlide.data('offsetxin')),
					'data-offsetyin': (typeof oSlide.data('offsetyin') == 'undefined') ? 0 : parseFloat(oSlide.data('offsetyin')),
					'data-rotatein': (typeof oSlide.data('rotatein') == 'undefined') ? 0 : parseFloat(oSlide.data('rotatein')),
					'data-rotatexin': (typeof oSlide.data('rotatexin') == 'undefined') ? 0 : parseFloat(oSlide.data('rotatexin')),
					'data-rotateyin': (typeof oSlide.data('rotateyin') == 'undefined') ? 0 : parseFloat(oSlide.data('rotateyin')),
					'data-scalexin': (typeof oSlide.data('scalexin') == 'undefined') ? 1 : parseFloat(oSlide.data('scalexin')),
					'data-scaleyin': (typeof oSlide.data('scaleyin') == 'undefined') ? 1 : parseFloat(oSlide.data('scaleyin')),
					'data-skewxin': (typeof oSlide.data('skewxin') == 'undefined') ? 0 : parseFloat(oSlide.data('skewxin')),
					'data-skewyin': (typeof oSlide.data('skewyin') == 'undefined') ? 0 : parseFloat(oSlide.data('skewyin')),
					'data-delayin': (typeof oSlide.data('delayin') == 'undefined') ? 0 : parseFloat(oSlide.data('delayin')),
					'data-durationin': (typeof oSlide.data('durationin') == 'undefined') ? 800 : parseFloat(oSlide.data('durationin'))
				});

				if (typeof oSlide.data('easingin') == 'undefined')
					oSlide.data('easingin', 'easeInOutExpo');

				oSlide.data('perspectivein', (typeof oSlide.data('perspectivein') == 'undefined') ? 400 : parseFloat(oSlide.data('perspectivein')));

				if (typeof oSlide.data('transformoriginin') == 'undefined')
					oSlide.data('transformoriginin', '50% 50% 0');

				if (oSlide.data('fadein') == 'false' || oSlide.data('fadein') == false)
					oSlide.data('fadein', false);

				// Set default options for slide transition out
				oSlide.attr({
					'data-offsetxout': (typeof oSlide.data('offsetxout') == 'undefined') ? 0 : parseFloat(oSlide.data('offsetxout')),
					'data-offsetyout': (typeof oSlide.data('offsetyout') == 'undefined') ? 0 : parseFloat(oSlide.data('offsetyout')),
					'data-rotateout': (typeof oSlide.data('rotateout') == 'undefined') ? 0 : parseFloat(oSlide.data('rotateout')),
					'data-rotatexout': (typeof oSlide.data('rotatexout') == 'undefined') ? 0 : parseFloat(oSlide.data('rotatexout')),
					'data-rotateyout': (typeof oSlide.data('rotateyout') == 'undefined') ? 0 : parseFloat(oSlide.data('rotateyout')),
					'data-scalexout': (typeof oSlide.data('scalexout') == 'undefined') ? 1 : parseFloat(oSlide.data('scalexout')),
					'data-scaleyout': (typeof oSlide.data('scaleyout') == 'undefined') ? 1 : parseFloat(oSlide.data('scaleyout')),
					'data-skewxout': (typeof oSlide.data('skewxout') == 'undefined') ? 0 : parseFloat(oSlide.data('skewxout')),
					'data-skewyout': (typeof oSlide.data('skewyout') == 'undefined') ? 0 : parseFloat(oSlide.data('skewyout')),
					'data-delayout': (typeof oSlide.data('delayout') == 'undefined') ? 0 : parseFloat(oSlide.data('delayout')),
					'data-durationout': (typeof oSlide.data('durationout') == 'undefined') ? 400 : parseFloat(oSlide.data('durationout'))
				});

				if (typeof oSlide.data('easingout') == 'undefined')
					oSlide.data('easingout', 'easeInOutExpo');

				oSlide.data('perspectiveout', (typeof oSlide.data('perspectiveout') == 'undefined') ? 400 : parseFloat(oSlide.data('perspectiveout')));

				if (typeof oSlide.data('transformoriginout') == 'undefined')
					oSlide.data('transformoriginout', '50% 50% 0');

				if (oSlide.data('fadeout') == 'false' || oSlide.data('fadeout') == false)
					oSlide.data('fadeout', false);

				// Bind slider click function
				oSlide.click(function() {
					if (oSlide.data('bAnchored') === true)
						oInstance.options.fnOnSliderClick(oSlide);
				});

				// Bind slider hover functions
				oSlide.hover(
					function() {
						if (oSlide.data('bAnchored') === true)
							oInstance.options.fnOnSliderHover(oSlide, true);
					},
					function() {
						if (oSlide.data('bAnchored') === true)
							oInstance.options.fnOnSliderHover(oSlide, false);
					}
				);
			});

			// Build navigation arrows
			if (oInstance.options.oArrow.sVisibility != 'hide') {
				var sClass = (oInstance.options.oArrow.sVisibility != 'show') ? 'my-slider-hidden' : '';

				var oArrowPrev = $('<a class="my-slider-arrow-prev my-slider-arrow-navigation ' + sClass + '" href="javascript:void(0);" />').appendTo(oSlideOthers);
				var oArrowNext = $('<a class="my-slider-arrow-next my-slider-arrow-navigation ' + sClass + '" href="javascript:void(0);" />').appendTo(oSlideOthers);

				if (oInstance.options.oArrow.sPrevImageActive && oInstance.options.oArrow.sPrevImageActive.length > 0)
					$('<img src="' + oInstance.options.oArrow.sPrevImageActive + '" />').hide().appendTo(oSlideOthers);

				if (oInstance.options.oArrow.sNextImageActive && oInstance.options.oArrow.sNextImageActive.length > 0)
					$('<img src="' + oInstance.options.oArrow.sNextImageActive + '" />').hide().appendTo(oSlideOthers);

				oArrowPrev.css({'background-image': 'url(' + oInstance.options.oArrow.sPrevImage + ')'});
				oArrowNext.css({'background-image': 'url(' + oInstance.options.oArrow.sNextImage + ')'});

				// Add css properties for old IE browsers
				if (isLowerThanIE8()) {
					oArrowPrev.css({
						'-ms-filter': 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src="' + oInstance.options.oArrow.sPrevImage + '", sizingMethod="scale")'
					});
					oArrowNext.css({
						'-ms-filter': 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src="' + oInstance.options.oArrow.sNextImage + '", sizingMethod="scale")'
					});
				} else {
					oArrowPrev.css({'background-image': 'url(' + oInstance.options.oArrow.sPrevImage + ')'});
					oArrowNext.css({'background-image': 'url(' + oInstance.options.oArrow.sNextImage + ')'});
				}
				
				oSlideOthers.find('.my-slider-arrow-navigation').css('width', 0);
				oSlideOthers.find('.my-slider-arrow-navigation').css('height', 0);

				// Attach click event handler function
				$('.my-slider-arrow-navigation').on('click', function(event) {
					event.preventDefault();

					if ($(this).hasClass('my-slider-arrow-prev'))
						psMySliderContainer.mySliderPrev();
					else if ($(this).hasClass('my-slider-arrow-next'))
						psMySliderContainer.mySliderNext();
				});

				// Bind arrow hover functions
				$('.my-slider-arrow-navigation').hover(
					function() {
						if ($(this).hasClass('my-slider-arrow-prev') && oInstance.options.oArrow.sPrevImageActive && oInstance.options.oArrow.sPrevImageActive.length > 0)
							$(this).css({'background-image': 'url(' + oInstance.options.oArrow.sPrevImageActive + ')'});

						if ($(this).hasClass('my-slider-arrow-next') && oInstance.options.oArrow.sNextImageActive && oInstance.options.oArrow.sNextImageActive.length > 0)
							$(this).css({'background-image': 'url(' + oInstance.options.oArrow.sNextImageActive + ')'});
					},
					function() {
						$('.my-slider-arrow-prev').css({'background-image': 'url(' + oInstance.options.oArrow.sPrevImage + ')'});
						$('.my-slider-arrow-next').css({'background-image': 'url(' + oInstance.options.oArrow.sNextImage + ')'});
					}
				);
			}

			// Build navigation bullets
			if (oInstance.options.sNavigationType == 'bullet') {
				var sClass = (oInstance.options.oBullet.sVisibility != 'show') ? 'my-slider-hidden' : '';

				var oBulletContainer = $('<div class="my-slider-bullet-container ' + sClass + '" />').appendTo(oSlideOthers);

				if (oInstance.options.oBullet.sImageActive && oInstance.options.oBullet.sImageActive.length > 0)
					$('<img src="' + oInstance.options.oBullet.sImageActive + '" />')
						.hide()
						.appendTo(oSlideOthers);

				if (oInstance.options.oBullet.sImageLeft && oInstance.options.oBullet.sImageLeft.length > 0)
					$('<a href="javascript:void(0);" class="my-slider-bullet-left" />')
						.css({
							'background-image': 'url(' + oInstance.options.oBullet.sImageLeft + ')'
						})
						.appendTo(oBulletContainer);

				for (var iIndex=0; iIndex<oInstance.options.iAmountSlides; iIndex++)
					$('<a href="javascript:void(0);" class="my-slider-bullet" />')
						.data('ind', iIndex)
						.css({
							'margin': oInstance.options.oBullet.iMargin,
							'opacity': oInstance.options.oBullet.iOpacity,
							'background-image': 'url(' + oInstance.options.oBullet.sImage + ')'
						})
						.appendTo(oBulletContainer);

				if (oInstance.options.oBullet.sImageRight && oInstance.options.oBullet.sImageRight.length > 0)
					$('<a href="javascript:void(0);" class="my-slider-bullet-right" />')
						.css({
							'background-image': 'url(' + oInstance.options.oBullet.sImageRight + ')'
						})
						.appendTo(oBulletContainer);

				// Attach click event handler function
				$('.my-slider-bullet, .my-slider-bullet-left, .my-slider-bullet-right').on('click', function(event) {
					event.preventDefault();

					if ($(event.target).hasClass('my-slider-bullet'))
						psMySliderContainer.mySliderTo(parseInt($(this).data('ind')) + 1);
				});

				// Bind arrow hover functions
				$('.my-slider-bullet').hover(
					function() {
						if (!$(this).hasClass('my-slider-bullet-active') && oInstance.options.oBullet.sImageActive && oInstance.options.oBullet.sImageActive.length > 0)
							$(this).stop().css({'background-image': 'url(' + oInstance.options.oBullet.sImageActive + ')'});
					},
					function() {
						if (!$(this).hasClass('my-slider-bullet-active') && oInstance.options.oBullet.sImageActive && oInstance.options.oBullet.sImageActive.length > 0)
							$(this).stop().css({'background-image': 'url(' + oInstance.options.oBullet.sImage + ')'});
					}
				);
			}

			// Build window resize action for responsive support
			$(window).resize(function() {
				oInstance.pause();

				TweenMax.killTweensOf(oInstance.options.oCurrentSlide.find('.my-slide-background img'));

				// Set attribute data
				oInstance.options.oCurrentSlide.data('bIsBusy', false);

				if (oInstance.options.bIsBusy) {
					oInstance.options.bNeedRedraw = true;
				} else {
					oInstance.redrawAll(oInstance.options.oCurrentSlide);
					oInstance.redrawNavigation();
				}

				// Call to redraw function
				oInstance.options.iResizeTimeoutId = setTimeout(function() {
					oInstance.redrawAll(oInstance.options.oCurrentSlide);

					// Clear redraw timeout
					clearTimeout(oInstance.options.iResizeTimeoutId);
				}, 100);

				// Add redraw timeout ID to timeouts array
				oInstance.options.aTimeouts.push(oInstance.options.iResizeTimeoutId);
			});

			// Bind actions on main wrapper hover
			psMySliderContainer
				.mouseenter(function(event) {
					if (!oInstance.options.bMouseEntered) {
						oInstance.options.bMouseEntered = true;

						if (oInstance.options.oArrow.sVisibility == 'hover')
							if (isLowerThanIE8())
								psMySliderContainer.find('.my-slider-arrow-navigation').removeClass('my-slider-hidden');
							else
								psMySliderContainer.find('.my-slider-arrow-navigation').stop(true, true).fadeIn(oInstance.options.oDefaults.iNavigationFadeDuration);

						if (oInstance.options.oBullet.sVisibility == 'hover')
							if (isLowerThanIE8())
								psMySliderContainer.find('.my-slider-bullet-container').removeClass('my-slider-hidden');
							else
								psMySliderContainer.find('.my-slider-bullet-container').stop(true, true).fadeIn(oInstance.options.oDefaults.iNavigationFadeDuration);
						

						if (oInstance.options.bPauseOnHover) {
							oInstance.options.bPaused = true;

							oInstance.pause();
						}
					}
				})
				.mouseleave(function(event) {
					oInstance.options.bMouseEntered = false;

					if (oInstance.options.oArrow.sVisibility == 'hover')
						if (isLowerThanIE8())
							psMySliderContainer.find('.my-slider-arrow-navigation').addClass('my-slider-hidden');
						else
							psMySliderContainer.find('.my-slider-arrow-navigation').stop(true, true).fadeOut(oInstance.options.oDefaults.iNavigationFadeDuration);

					if (oInstance.options.oBullet.sVisibility == 'hover')
						if (isLowerThanIE8())
							psMySliderContainer.find('.my-slider-bullet-container').addClass('my-slider-hidden');
						else
							psMySliderContainer.find('.my-slider-bullet-container').stop(true, true).fadeOut(oInstance.options.oDefaults.iNavigationFadeDuration);

					if (oInstance.options.bPauseOnHover) {
						if (oInstance.options.bPaused) {
							oInstance.options.bPaused = false;

							oInstance.resume();

							oInstance.options.fnOnResume();
						}
					}
				});

			// Bind keys (left arrow and right arrow) navigation if correspond
			if (oInstance.options.bKeyNavigation)
				$(document).keydown(function(event) {
					if (!oInstance.options.bIsBusy)
						switch (event.keyCode) {
							case 39:
								psMySliderContainer.mySliderNext();
								break;
							case 37:
								psMySliderContainer.mySliderPrev();
								break;
						}
				});

			// Redraw container
			oInstance.redrawContainer();

			// Count all images for skin container
			var iCount = 0;

			if (!oSlideOthers.data('loaded'))
				oSlideOthers.find('*').addBack().each(function() {
					if (($(this).is('img')) || ($(this).css('background-image') && ($(this).css('background-image') + '').indexOf('url') != -1))
						iCount++;
				});

			// Load all images for skin container
			if (iCount > 0) {
				oInstance.options.bIsBusy = true;
				oInstance.options.oLoading.show();

				oSlideOthers.find('*').addBack().each(function() {
					var sImageSrc = '';
					var oImage = $(this);

					// Obtain image source
					if (!$(this).data('loaded'))
						if ($(this).is('img'))
							sImageSrc = $(this).attr('src');
						else if ($(this).css('background-image') && ($(this).css('background-image') + '').indexOf('url') != -1)
							sImageSrc = $(this).css('background-image').replace(/^url\([\"\']?/, '').replace(/[\"\']?\)$/, '');

					if (sImageSrc != '') {
						var oImageTmp = new Image();

						oImageTmp.onload = function() {
							if (oImage.hasClass('my-slider-arrow-navigation') || oImage.hasClass('my-slider-bullet') || oImage.hasClass('my-slider-bullet-left') || oImage.hasClass('my-slider-bullet-right')) {
								if (oImage.width() <= 0)
									oImage.css('width', (oImage.height() <= 0) ? oImageTmp.width : oImage.height() * oImageTmp.width / oImageTmp.height);

								if (oImage.height() <= 0)
									oImage.css('height', (oImage.width() <= 0) ? oImageTmp.height : oImage.width() * oImageTmp.height / oImageTmp.width);

								oImage.attr({
									'data-width': oImage.width(),
									'data-height': oImage.height()
								});
							}

							iCount--;

							if (iCount == 0) {
								var oImageLoadingTmp = new Image();

								oImageLoadingTmp.onload = function() {
									oInstance.options.oLoading.data('width', oImageLoadingTmp.width);
									oInstance.options.oLoading.data('height', oImageLoadingTmp.height);

									oSlideOthers.data('loaded', true);

									oInstance.options.bIsBusy = false;
									oInstance.options.oLoading.hide();

									// Calculate and set arrow properties if correspond
									if (oInstance.options.oArrow.visiblitiy != 'hide') {
										oSlideOthers.find('.my-slider-arrow-navigation').css({
											'top': '50%',
											'margin-top': - parseInt(oSlideOthers.find('.my-slider-arrow-navigation').height()) / 2
										});

										if (oInstance.options.oArrow.sPositionOffset == 'outside')
											oInstance.options.oArrow.sPositionOffset = - parseInt(oSlideOthers.find('.my-slider-arrow-navigation').width());
										else if (oInstance.options.oArrow.sPositionOffset == 'inside')
											oInstance.options.oArrow.sPositionOffset = 0;

										oSlideOthers.find('.my-slider-arrow-prev').css({left: oInstance.options.oArrow.sPositionOffset}).data('left', oInstance.options.oArrow.sPositionOffset);
										oSlideOthers.find('.my-slider-arrow-next').css({right: oInstance.options.oArrow.sPositionOffset}).data('right', oInstance.options.oArrow.sPositionOffset);
									}

									// Calculate and set bullet properties if correspond
									if (oInstance.options.sNavigationType == 'bullet') {
										// Obtain bullet container element
										var oBulletContainer = oSlideOthers.find('.my-slider-bullet-container');

										// Obtain first bullet element
										var oBullet = oBulletContainer.find('.my-slider-bullet').eq(0);

										// Calculate new bullet container width
										var iBulletContainerWidth = (oBullet.width() + parseInt(oBullet.css('margin-left')) + parseInt(oBullet.css('margin-right'))) * oBulletContainer.find('.my-slider-bullet').length;

										if (oBulletContainer.find('.my-slider-bullet-left').length > 0)
											iBulletContainerWidth += oBulletContainer.find('.my-slider-bullet-left').eq(0).width();

										if (oBulletContainer.find('.my-slider-bullet-right').length > 0)
											iBulletContainerWidth += oBulletContainer.find('.my-slider-bullet-right').eq(0).width();

										if (oInstance.options.oBullet.sPosition == 'top_center' || oInstance.options.oBullet.sPosition == 'bottom_center')
											oBulletContainer
												.css({
													'margin-left': - iBulletContainerWidth / 2
												})
												.data('margin_left', - iBulletContainerWidth / 2);

										// Set data attributes for bullet container element
										oBulletContainer.attr({
											'data-width': iBulletContainerWidth,
											'data-height': oBullet.height() + parseInt(oBullet.css('margin-top')) + parseInt(oBullet.css('margin-bottom')),
											'data-bullet_width': oBullet.width(),
											'data-bullet_height': oBullet.height(),
											'data-bullet_margin_left': parseInt(oBullet.css('margin-left')),
											'data-bullet_margin_right': parseInt(oBullet.css('margin-right')),
											'data-bullet_margin_top': parseInt(oBullet.css('margin-top')),
											'data-bullet_margin_bottom': parseInt(oBullet.css('margin-bottom')),
											'data-bullet_left_width': (oBulletContainer.find('.my-slider-bullet-left').length > 0) ? oBulletContainer.find('.my-slider-bullet-left').eq(0).width() : 0,
											'data-bullet_right_width': (oBulletContainer.find('.my-slider-bullet-right').length > 0) ? oBulletContainer.find('.my-slider-bullet-right').eq(0).width() : 0
										});
									}

									psMySliderContainer.mySliderNext();
								}

								oImageLoadingTmp.src = oInstance.options.oLoading.attr('src');
							}
						}

						oImageTmp.src = sImageSrc;
					}
				});
			} else {
				psMySliderContainer.mySliderNext();
			}
		};

		// Redraw all elements
		oInstance.redrawAll = function(poSlide) {
			if (!oInstance.options.bIsBusy) {
				// Redraw container
				oInstance.redrawContainer();

				var iCurrentWidth = parseInt(parseFloat(oSlideWrapper.css('width')));

				var iPaddingLeft = oInstance.options.iResponsiveRate * oInstance.options.iPaddingLeft;
				var iPaddingRight = oInstance.options.iResponsiveRate * oInstance.options.iPaddingRight;
				var iPaddingTop = oInstance.options.iResponsiveRate * oInstance.options.iPaddingTop;
				var iPaddingBottom = oInstance.options.iResponsiveRate * oInstance.options.iPaddingBottom;

				var iWidth = psMySliderContainer.width() - iPaddingLeft - iPaddingRight;
				var iHeight = psMySliderContainer.height() - iPaddingTop - iPaddingBottom;

				// Update slide wrapper css properties
				oSlideWrapper.css({
					'width': iWidth,
					'height': iHeight,
					'padding-left': iPaddingLeft,
					'padding-right': iPaddingRight,
					'padding-top': iPaddingTop,
					'padding-bottom': iPaddingBottom
				});

				// Update css properties for each slide
				psMySliderContainer
					.find('.my-slide')
					.css({
						width: iWidth,
						height: iHeight
					});

				// Update css properties for progress bar
				oInstance.options.oProgressBar.css({
					width: parseInt(oInstance.options.oProgressBar.width()) * parseInt(parseFloat(oSlideWrapper.css('width'))) / iCurrentWidth,
					left: iPaddingLeft,
					top: iPaddingTop
				});

				// Redraw each slide if correspond
				if (oInstance.options.bResponsive || oInstance.options.bFullWidth)
					poSlide.children().each(function() {
						var oSlide = $(this);

						if (!oSlide.hasClass('my-slide-background'))
							if (oSlide.hasClass('my-slider-slides'))
								oSlide.find('> *').each(function() {
									oInstance.redrawSlide($(this));
								});
							else
								oInstance.redrawSlide(oSlide);
					});

				// Update css properties for slide background elements
				psMySliderContainer
					.find('.my-slide-background')
					.css({
						width : iWidth,
						height : iHeight
					});

				// Calculate new background width and height
				var oBackground = poSlide.find('.my-slide-background img');
				var iBackgroundWidth = parseFloat(oSlideWrapper.css('width'));
				var iBackgroundHeight = oBackground.data('background_height') * parseFloat(oSlideWrapper.css('width')) / oBackground.data('background_width');

				if (oBackground.data('size') == 'contain') {
					if (oBackground.data('background_width') / oBackground.data('background_height') < parseFloat(oSlideWrapper.css('width')) / parseFloat(oSlideWrapper.css('height'))) {
						iBackgroundWidth = oBackground.data('background_width') * parseFloat(oSlideWrapper.css('height')) / oBackground.data('background_height');
						iBackgroundHeight = parseFloat(oSlideWrapper.css('height'));
					}
				} else {
					if (oBackground.data('background_width') / oBackground.data('background_height') > parseFloat(oSlideWrapper.css('width')) / parseFloat(oSlideWrapper.css('height'))) {
						iBackgroundWidth = oBackground.data('background_width') * parseFloat(oSlideWrapper.css('height')) / oBackground.data('background_height');
						iBackgroundHeight = parseFloat(oSlideWrapper.css('height'));
					}
				}

				// Update css properties for background elements
				oBackground.css({
					width: iBackgroundWidth,
					height: iBackgroundHeight,
					left: - (iBackgroundWidth - parseFloat(oSlideWrapper.css('width'))) / 2,
					top: - (iBackgroundHeight - parseFloat(oSlideWrapper.css('height'))) / 2,
					position: 'absolute'
				});
			}

			oInstance.options.fnOnRedraw();
		};

		// Redraw container element
		oInstance.redrawContainer = function() {
			var iMarginLeft = (typeof psMySliderContainer.parent().offset() != 'undefined') ? - psMySliderContainer.parent().offset().left - parseInt(psMySliderContainer.parent().css('padding-left')) : 0;
			var iMarginTop = (typeof psMySliderContainer.parent().offset() != 'undefined') ? psMySliderContainer.parent().offset().top + parseInt(psMySliderContainer.parent().css('padding-top')) : 0;

			// Container redraw
			if (oInstance.options.bFullWidth && $(window).width() <= parseInt(oInstance.options.width)) {
				oInstance.options.bResponsive = (oInstance.options.bOriginalResponsive == true);
				oInstance.options.iResponsiveWidth = oInstance.options.width;
			}

			if (oInstance.options.bResponsive) {
				oInstance.options.iResponsiveRate = Math.min(1, psMySliderContainer.parent().width() / parseInt(oInstance.options.iResponsiveWidth));

				if (oInstance.options.bFullWidth && $(window).width() <= parseInt(oInstance.options.width))
					oInstance.options.iResponsiveRate = Math.min(1, $(window).width() / parseInt(oInstance.options.iResponsiveWidth));
			} else {
				oInstance.options.iResponsiveRate = 1;
			}

			// Initialize css object
			var oCss = {
				width: (oInstance.options.iResponsiveRate * parseInt(oInstance.options.iResponsiveWidth)) + (oInstance.options.iResponsiveWidth + '').replace(/\d+/g, ''),
				height: (oInstance.options.iResponsiveRate * parseInt(oInstance.options.iResponsiveHeight)) + (oInstance.options.iResponsiveHeight + '').replace(/\d+/g, '')
			};

			// Process css styles to full size option
			if (oInstance.options.bFullSize) {
				if ($(window).width() > parseInt(oInstance.options.width)) {
					oInstance.options.bResponsive = false;
					oInstance.options.iResponsiveRate = 1;
					oInstance.options.iTotalHeight = parseInt(oInstance.options.iResponsiveHeight) + (oInstance.options.iResponsiveHeight + '').replace(/\d+/g, '');
				} else {
					if (oInstance.options.bResponsive == true) {
						oInstance.options.bResponsive = (oInstance.options.bOriginalResponsive == true);
						oInstance.options.iTotalHeight = $(window).width() * parseInt(oInstance.options.height) / parseInt(oInstance.options.width);
					} else {
						oInstance.options.iResponsiveRate = 1;
						oInstance.options.iTotalHeight = parseInt(oInstance.options.iResponsiveHeight) + (oInstance.options.iResponsiveHeight + '').replace(/\d+/g, '');
					}
				}

				// Update css object
				oCss = {
					'width': $(window).width(),
					'height': $(window).height() - iMarginTop,
					'margin-left': iMarginLeft,
					'overflow': 'hidden'
				};
			} else if (oInstance.options.bFullWidth && $(window).width() > parseInt(oInstance.options.width)) {
				oInstance.options.bResponsive = false;
				oInstance.options.iResponsiveRate = 1;

				// Update css object
				oCss = {
					'width': $(window).width(),
					'height': parseInt(oInstance.options.iResponsiveHeight) + (oInstance.options.iResponsiveHeight + '').replace(/\d+/g, ''),
					'margin-left': iMarginLeft,
					'overflow': 'hidden'
				};
			} else if (oInstance.options.bFullWidth) {
				// Update css object
				if (oInstance.options.bResponsive)
					oCss = {
						'width': $(window).width(),
						'height': $(window).width() * parseInt(oInstance.options.height) / parseInt(oInstance.options.width),
						'margin-left': iMarginLeft,
						'overflow': 'hidden'
					};
			}

			// Set new css styles to container
			psMySliderContainer.css(oCss);

			// Apply fade in effect if correspond
			if (!oInstance.options.bSlideTransitionNeeded)
				psMySliderContainer.fadeIn();

			// Process css styles to full width option
			if (oInstance.options.bFullWidth) {
				if (oInstance.options.bFullWidth && $(window).width() > parseInt(oInstance.options.width)) {
					psMySliderContainer.css({
						'margin-left': iMarginLeft,
						'width': $(window).width()
					});
				} else if (oInstance.options.bFullWidth) {
					if (oInstance.options.bFullSize) {
						psMySliderContainer.css({
							'margin-left': iMarginLeft,
							'width': $(window).width(),
							'height': $(window).height() - iMarginTop
						});

						oInstance.options.iTotalHeight = $(window).width() * parseInt(oInstance.options.height) / parseInt(oInstance.options.width);
					} else {
						psMySliderContainer.css({
							'margin-left': iMarginLeft
						});

						if (oInstance.options.bResponsive)
							psMySliderContainer.css({
								'width': $(window).width(),
								'height': $(window).width() * parseInt(oInstance.options.height) / parseInt(oInstance.options.width)
							});
					}
				}
			}

			// Update css styles for loading gif
			var iLoadingWidth = parseInt(oInstance.options.oLoading.data('width')) * oInstance.options.iResponsiveRate;
			var iLoadingHeight = parseInt(oInstance.options.oLoading.data('height')) * oInstance.options.iResponsiveRate;

			oInstance.options.oLoading.css({
				'width': iLoadingWidth,
				'height': iLoadingHeight,
				'margin-left': - iLoadingWidth / 2,
				'margin-top': - iLoadingHeight / 2
			});
		};

		// Redraw slide element
		oInstance.redrawSlide = function(poSlide) {
			// Obtain all data properties from slide element
			var mLeft = poSlide.data('left') ? poSlide.data('left') : '0';
			var mTop = poSlide.data('top') ? poSlide.data('top') : '0';

			var mWidth = poSlide.data('width') ? poSlide.data('width') : '';
			var mHeight = poSlide.data('height') ? poSlide.data('height') : '';

			var iFontSize = parseInt(poSlide.data('font_size'));
			var iLineHeight = parseInt(poSlide.data('line_height'));

			var iMarginTop = poSlide.data('margin_top') ? parseInt(poSlide.data('margin_top')) : 0;
			var iMarginRight = poSlide.data('margin_right') ? parseInt(poSlide.data('margin_right')) : 0;
			var iMarginBottom = poSlide.data('margin_bottom') ? parseInt(poSlide.data('margin_bottom')) : 0;
			var iMarginLeft = poSlide.data('margin_left') ? parseInt(poSlide.data('margin_left')) : 0;

			var iPaddingTop = poSlide.data('padding_top') ? parseInt(poSlide.data('padding_top')) : 0;
			var iPaddingRight = poSlide.data('padding_right') ? parseInt(poSlide.data('padding_right')) : 0;
			var iPaddingBottom = poSlide.data('padding_bottom') ? parseInt(poSlide.data('padding_bottom')) : 0;
			var iPaddingLeft = poSlide.data('padding_left') ? parseInt(poSlide.data('padding_left')) : 0;

			var iBorderTop = poSlide.data('border_top') ? parseInt(poSlide.data('border_top')) : 0;
			var iBorderRight = poSlide.data('border_right') ? parseInt(poSlide.data('border_right')) : 0;
			var iBorderBottom = poSlide.data('border_bottom') ? parseInt(poSlide.data('border_bottom')) : 0;
			var iBorderLeft = poSlide.data('border_left') ? parseInt(poSlide.data('border_left')) : 0;

			// Update left css style
			poSlide.css({
				'left': (mLeft.indexOf('%') != -1) ? mLeft : ((oInstance.options.bResponsive) ? parseInt(mLeft) * oInstance.options.iResponsiveRate : parseInt(mLeft) + (parseFloat(oSlideWrapper.css('width')) - parseInt(oInstance.options.width)) / 2)
			});

			// Update top css style
			poSlide.css({
				'top': (mTop.indexOf('%') != -1) ? mTop : ((parseInt(oInstance.options.iTotalHeight) > 0) ? (psMySliderContainer.height() - parseInt(oInstance.options.iTotalHeight)) / 2 : 0) + parseInt(mTop) * oInstance.options.iResponsiveRate
			});

			// Update width css style
			poSlide.css({
				'width': ($.isNumeric(mWidth)) ? parseInt(mWidth) * oInstance.options.iResponsiveRate : mWidth
			});

			// Update height css style
			poSlide.css({
				'height': ($.isNumeric(mHeight)) ? parseInt(mHeight) * oInstance.options.iResponsiveRate : mHeight
			});

			// Update font size, line height, margins, paddings, and borders css styles
			poSlide.css({
				'font-size'				: iFontSize * oInstance.options.iResponsiveRate + 'px',
				'line-height'			: iLineHeight * oInstance.options.iResponsiveRate + 'px',
				'margin-top'			: iMarginTop * oInstance.options.iResponsiveRate + 'px',
				'margin-right'			: iMarginRight * oInstance.options.iResponsiveRate + 'px',
				'margin-bottom'			: iMarginBottom * oInstance.options.iResponsiveRate + 'px',
				'margin-left'			: iMarginLeft * oInstance.options.iResponsiveRate + 'px',
				'padding-top'			: iPaddingTop * oInstance.options.iResponsiveRate + 'px',
				'padding-right'			: iPaddingRight * oInstance.options.iResponsiveRate + 'px',
				'padding-bottom'		: iPaddingBottom * oInstance.options.iResponsiveRate + 'px',
				'padding-left'			: iPaddingLeft * oInstance.options.iResponsiveRate + 'px',
				'border-top-width'		: iBorderTop * oInstance.options.iResponsiveRate + 'px',
				'border-right-width'	: iBorderRight * oInstance.options.iResponsiveRate + 'px',
				'border-bottom-width'	: iBorderBottom * oInstance.options.iResponsiveRate + 'px',
				'border-left-width'		: iBorderLeft * oInstance.options.iResponsiveRate + 'px'
			});
		};

		// Redraw navigation elements
		oInstance.redrawNavigation = function() {
			// Redraw bullet container
			oSlideOthers.find('.my-slider-bullet-container').each(function() {
				var oBulletContainer = $(this);
				var fBulletRate = (oInstance.options.oBullet.iResponsiveLevel - 1 + oInstance.options.iResponsiveRate) / oInstance.options.oBullet.iResponsiveLevel;

				// Set css properties to bullet container depending its position
				switch (oInstance.options.oBullet.sPosition) {
					case 'top_left':
						oBulletContainer.css({
							'left': parseInt(oInstance.options.oBullet.iOffsetX) * fBulletRate,
							'top': parseInt(oInstance.options.oBullet.iOffsetY) * fBulletRate
						});
						break;
					case 'top_center':
						oBulletContainer.css({
							'left': '50%',
							'top': parseInt(oInstance.options.oBullet.iOffsetY) * fBulletRate,
							'margin-left': fBulletRate * oBulletContainer.data('margin_left') + parseInt(oInstance.options.oBullet.iOffsetX) * oInstance.options.iResponsiveRate
						});
						break;
					case 'top_right':
						oBulletContainer.css({
							'right': parseInt(oInstance.options.oBullet.iOffsetX) * fBulletRate,
							'top': parseInt(oInstance.options.oBullet.iOffsetY) * fBulletRate
						});
						break;
					case 'bottom_left':
						oBulletContainer.css({
							'left': parseInt(oInstance.options.oBullet.iOffsetX) * fBulletRate,
							'bottom': parseInt(oInstance.options.oBullet.iOffsetY) * fBulletRate
						});
						break;
					case 'bottom_right':
						oBulletContainer.css({
							'right': parseInt(oInstance.options.oBullet.iOffsetX) * fBulletRate,
							'bottom': parseInt(oInstance.options.oBullet.iOffsetY) * fBulletRate
						});
						break;
					default:
						oBulletContainer.css({
							'left': '50%',
							'bottom': parseInt(oInstance.options.oBullet.iOffsetY) * fBulletRate,
							'margin-left': fBulletRate * oBulletContainer.data('margin_left') + parseInt(oInstance.options.oBullet.iOffsetX) * oInstance.options.iResponsiveRate
						});
						break;
				}

				// Set css properties to each bullet element
				oBulletContainer.find('.my-slider-bullet').css({
					'width': fBulletRate * oBulletContainer.data('bullet_width'),
					'height': fBulletRate * oBulletContainer.data('bullet_height'),
					'margin-top': fBulletRate * oBulletContainer.data('bullet_margin_top'),
					'margin-right': fBulletRate * oBulletContainer.data('bullet_margin_right'),
					'margin-bottom': fBulletRate * oBulletContainer.data('bullet_margin_bottom'),
					'margin-left': fBulletRate * oBulletContainer.data('bullet_margin_left')
				});

				// Set css properties to bullet left element
				oBulletContainer.find('.my-slider-bullet-left').css({
					'width': fBulletRate * oBulletContainer.data('bullet_left_width'),
					'height': fBulletRate * oBulletContainer.data('bullet_height')
				});

				// Set css properties to bullet right element
				oBulletContainer.find('.my-slider-bullet-right').css({
					'width': fBulletRate * oBulletContainer.data('bullet_right_width'),
					'height': fBulletRate * oBulletContainer.data('bullet_height')
				});
			});

			// Redraw arrow container
			oSlideOthers.find('.my-slider-arrow-navigation').each(function() {
				var oArrow = $(this);
				var fArrowRate = (oInstance.options.oArrow.iResponsiveLevel - 1 + oInstance.options.iResponsiveRate) / oInstance.options.oArrow.iResponsiveLevel;

				// Set css properties arrow element
				oArrow.css({
					'width': fArrowRate * parseInt(oArrow.data('width')),
					'height': fArrowRate * parseInt(oArrow.data('height')),
					'margin-left': fArrowRate * parseInt(oArrow.data('margin_left')),
					'right': fArrowRate * parseInt(oArrow.data('right')),
					'bottom': fArrowRate * parseInt(oArrow.data('bottom')),
					'top': '50%',
					'margin-top': - oArrow.height() / 2
				});

				if ((oArrow.data('left') + '').indexOf('%') === -1)
					oArrow.css({
						'left': fArrowRate * parseInt(oArrow.data('left'))
					});
			});
		};

		// Resume slider function
		oInstance.resume = function() {
			if (!oInstance.options.bIsBusy && oInstance.options.bAutoSlide) {
				if (oInstance.options.iAmountSlides > 1) {
					// Caculate remaining slide duration using progress bar
					var iRemainingSlideDuration = parseInt(psMySliderContainer.find('.my-slide-opened').data('duration')) * (parseFloat(oSlideWrapper.css('width')) - oInstance.options.oProgressBar.width()) / parseFloat(oSlideWrapper.css('width'));

					TweenMax.to(
						oInstance.options.oProgressBar,
						iRemainingSlideDuration / 1000,
						{
							css: {
								width : parseFloat(oSlideWrapper.css('width'))
							},
							ease: Linear.easeNone,
							onComplete: function() {
								if (psMySliderContainer.find('.slide-wrapper').length > 0) {
									oInstance.options.oProgressBar.css('width', 0);
									oInstance.flowTo('right');
								}
							}
						}
					);
				}
			}
		};
		
		// Pause slider function
		oInstance.pause = function() {
			// Stop progress bar if is running
			if (TweenMax.isTweening(oInstance.options.oProgressBar))
				TweenMax.killTweensOf(oInstance.options.oProgressBar);

			oInstance.options.fnOnPause();
		};

		// Flow to function
		oInstance.flowTo = function(pmTarget) {
			oInstance.options.bIsBusy = true;

			if (typeof pmTarget != 'number')
				if (pmTarget == 'left')
					pmTarget = (oInstance.options.iCurrentSlideIndex == 0) ? iLeftPosition = oInstance.options.iAmountSlides - 1 : oInstance.options.iCurrentSlideIndex - 1;
				else
					pmTarget = (oInstance.options.iCurrentSlideIndex == oInstance.options.iAmountSlides - 1) ? 0 : oInstance.options.iCurrentSlideIndex + 1;

			psMySliderContainer.css('visibility', 'visible');

			// Stop progress bar
			TweenMax.killTweensOf(oInstance.options.oProgressBar);

			oInstance.options.oProgressBar.css('width', 0);

			oInstance.options.iUpcomingSlideIndex = pmTarget;

			oInstance.options.oUpcomingSlide = psMySliderContainer.find('.my-slide').eq(oInstance.options.iUpcomingSlideIndex);

			// Count all images in upcoming slide
			var iCount = 0;

			if (!oInstance.options.oUpcomingSlide.data('loaded'))
				oInstance.options.oUpcomingSlide.find('*').addBack().each(function() {
					if (($(this).is('img')) || ($(this).css('background-image') && ($(this).css('background-image') + '').indexOf('url') != -1))
						iCount++;
				});

			// Load all images in upcoming slide
			if (iCount > 0) {
				oInstance.options.bIsBusy = true;
				oInstance.options.oLoading.show();

				oInstance.options.oUpcomingSlide.find('*').addBack().each(function() {
					var sImageSrc = '';
					var oImage = $(this);

					// Obtain image source
					if (!$(this).data('loaded')) {
						if ($(this).is('img')) {
							if (oInstance.options.bLazyLoadImages)
								$(this).attr('src', $(this).data('src'));

							sImageSrc = $(this).attr('src');
						} else if ($(this).css('background-image') && ($(this).css('background-image') + '').indexOf('url') != -1) {
							sImageSrc = $(this).css('background-image').replace(/^url\([\"\']?/, '').replace(/[\"\']?\)$/, '');
						}
					}

					if (sImageSrc != '') {
						var oImageTmp = new Image();

						oImageTmp.onload = function() {
							if (!oImage.data('width') || oImage.data('width') <= 0 || oImage.data('width') == 'auto')
								oImage.data('width', (oImage.data('height') > 0) ? oImage.data('height') * oImageTmp.width / oImageTmp.height : oImageTmp.width);

							if (!oImage.data('height') || oImage.data('height') <= 0 || oImage.data('height') == 'auto')
								oImage.data('height', (oImage.data('width') > 0) ? oImage.data('width') * oImageTmp.height / oImageTmp.width : oImageTmp.height);

							if (oImage.parent().hasClass('my-slide-background'))
								oImage.attr({
									'data-background_width': oImageTmp.width,
									'data-background_height': oImageTmp.height
								});

							iCount--;

							if (iCount == 0) {
								oInstance.options.oUpcomingSlide.data('loaded', true);

								oInstance.options.bIsBusy = false;
								oInstance.options.oLoading.hide();

								if (!oInstance.options.bSlideTransitionNeeded) {
									oInstance.redrawAll(oInstance.options.oUpcomingSlide);
									oInstance.redrawNavigation();
								}

								oInstance.flow();
							}
						}

						oImageTmp.src = sImageSrc;
					}
				});
			} else {
				if (!oInstance.options.bSlideTransitionNeeded) {
					oInstance.redrawAll(oInstance.options.oUpcomingSlide);
					oInstance.redrawNavigation();
				}

				oInstance.flow();
			}
		};

		// Flow function
		oInstance.flow = function() {
			oInstance.options.bIsBusy = true;
			oInstance.pause();

			if (oInstance.options.bSlideTransitionNeeded)
				oInstance.updateActiveBullet();

			oInstance.options.fnOnSlidingStart();

			if (!oInstance.options.bSlideTransitionNeeded) {
				oSlideOthers.show();

				oInstance.updateActiveSlide();

				oInstance.options.bSlideTransitionNeeded = true;
			} else {
				oInstance.slidesOut();

				var oTransition = getAllowedTransitions(parseInt(oInstance.getRandomArrayElement(oInstance.options.oUpcomingSlide.data('transitionlist'))));

				if (oInstance.options.oCurrentSlide.find('.my-slide-background img').length <= 0 || oInstance.options.oUpcomingSlide.find('.my-slide-background img').length <= 0) {
					// Apply fade transition
					oTransition = getAllowedTransitions(1);
					oTransition.animation[0].duration = 400;
				}

				var oFlowPanel = $('<div class="my-slider-flow-panel"/>')
					.css('overflow', oTransition.overflow)
					.prependTo(oInstance.options.oCurrentSlide.css('overflow', oTransition.overflow));

				var aDelay = [];
				var iRows = oTransition.rows;
				var iCols = oTransition.cols;

				if (iRows > 10)
					iRows = Math.floor(oTransition.rows * oInstance.options.iResponsiveRate);

				if (iCols > 10)
					iCols = Math.floor(oTransition.cols * oInstance.options.iResponsiveRate);

				if (iCols * iRows > 1)
					switch (oTransition.cellOrder) {
						case 'a-z':
							for (var iIndex=0; iIndex<iCols*iRows; iIndex++)
								aDelay.push(iIndex * oTransition.cellDelay);
							break;
						case 'z-a':
							for (var iIndex=(iCols*iRows)-1; iIndex>=0; iIndex--)
								aDelay.push(iIndex * oTransition.cellDelay);
							break;
						default:
							for (var iIndex=0; iIndex<iCols*iRows; iIndex++)
								aDelay.push(iIndex * oTransition.cellDelay);

							for (var mDelayTmp, bBinary, iTop=aDelay.length; iTop--;) {
								bBinary = (Math.random() * (iTop + 1)) << 0;
								mDelayTmp = aDelay[bBinary];

								aDelay[bBinary] = aDelay[iTop];
								aDelay[iTop] = mDelayTmp;
							}

							break;
					}
				else
					aDelay.push(0);

				var oCurrentBackground = oInstance.options.oCurrentSlide.find('.my-slide-background');
				var oUpcomingBackground = oInstance.options.oUpcomingSlide.find('.my-slide-background');

				var oUpcomingImage = oUpcomingBackground.find('img');

				var iWidth = parseFloat(oSlideWrapper.css('width'));
				var iHeight = oUpcomingImage.data('background_height') * parseFloat(oSlideWrapper.css('width')) / oUpcomingImage.data('background_width');

				if (oUpcomingImage.data('size') == 'contain') {
					if (oUpcomingImage.data('background_width') / oUpcomingImage.data('background_height') < parseFloat(oSlideWrapper.css('width')) / parseFloat(oSlideWrapper.css('height'))) {
						iWidth = oUpcomingImage.data('background_width') * parseFloat(oSlideWrapper.css('height')) / oUpcomingImage.data('background_height');
						iHeight = parseFloat(oSlideWrapper.css('height'));
					}
				} else {
					if (oUpcomingImage.data('background_width') / oUpcomingImage.data('background_height') > parseFloat(oSlideWrapper.css('width')) / parseFloat(oSlideWrapper.css('height'))) {
						iWidth = oUpcomingImage.data('background_width') * parseFloat(oSlideWrapper.css('height')) / oUpcomingImage.data('background_height');
						iHeight = parseFloat(oSlideWrapper.css('height'));
					}
				}

				oUpcomingImage.css({
					'width': iWidth,
					'height': iHeight,
					'left': - (iWidth - parseFloat(oSlideWrapper.css('width'))) / 2,
					'top': - (iHeight - parseFloat(oSlideWrapper.css('height'))) / 2
				});

				var fFullWidth = parseFloat(oSlideWrapper.css('width'));
				var fFullHeight = parseFloat(oSlideWrapper.css('height'));

				var fPerspective = Math.max(fFullWidth, fFullHeight);

				var fTotallWidth = 0;
				var fTotalHeight = 0;

				var oNextCell;

				if (oTransition.cellThick == 0)
					oCurrentBackground.clone().removeClass('my-slide-background').addClass('my-slider-current-bg').show().prependTo(oFlowPanel);					

				oInstance.options.oCurrentSlide.find('.my-slide-background').hide();

				for (var iIndex=0; iIndex<iCols*iRows; iIndex++) {
					var iWidth = Math.round(fFullWidth / iCols);
					var iHeight = Math.round(fFullHeight / iRows);

					var oCss = $.extend({}, oTransition.css);

					if (oTransition.css) {
						switch (oTransition.css.top) {
							case 'random':
								var aTmp = [iHeight, 0, -iHeight];
								oCss.top = aTmp[Math.floor(Math.random() * aTmp.length)];
								break;
							case 'top':
								oCss.top = -iHeight;
								break;
							case 'bottom':
								oCss.top = iHeight;
								break;
							default:
								oCss.top = 0;
								break;
						}

						switch (oTransition.css.left) {
							case 'random':
								if (oCss.top == 0) {
									var aTmp = [iWidth, -iWidth];
									oCss.left = aTmp[Math.floor(Math.random() * aTmp.length)];
								} else {
									oCss.left = 0;
								}
								break;
							case 'left':
								oCss.left = -iWidth;
								break;
							case 'right':
								oCss.left = iWidth;
								break;
							default:
								oCss.left = 0;
								break;
						}
					}

					var bScaleX = bScaleY = bScaleZ = false;
					var iRotationX = iRotationY = iRotationZ = 0;
					var bNeedFinalAnimation = true;

					var iKey = 0;

					while (oTransition.animation[iKey]) {
						if (oTransition.animation[iKey].toVars.css) {
							if (oTransition.animation[iKey].toVars.css.scaleX && oTransition.animation[iKey].toVars.css.scaleX == 1)
								bScaleX = true;

							if (oTransition.animation[iKey].toVars.css.scaleY && oTransition.animation[iKey].toVars.css.scaleY == 1)
								bScaleY = true;

							if (oTransition.animation[iKey].toVars.css.scaleZ && oTransition.animation[iKey].toVars.css.scaleZ == 1)
								bScaleZ = true;

							if (oTransition.animation[iKey].toVars.css.rotationX)
								iRotationX += oTransition.animation[iKey].toVars.css.rotationX;

							if (oTransition.animation[iKey].toVars.css.rotationY)
								iRotationY += oTransition.animation[iKey].toVars.css.rotationY;

							if (oTransition.animation[iKey].toVars.css.rotationZ)
								iRotationZ += oTransition.animation[iKey].toVars.css.rotationZ;
						} else {
							bNeedFinalAnimation = false;
						}

						iKey++;
					}

					var oCell = $('<div class="my-slider-cell" />').appendTo(oFlowPanel);

					oCell.css({
						'width': iWidth,
						'height': iHeight,
						'overflow': oTransition.overflow,
						'perspective': fPerspective,
						'-o-perspective': fPerspective,
						'-ms-perspective': fPerspective,
						'-moz-perspective': fPerspective,
						'-webkit-perspective': fPerspective
					});

					if (iIndex % iCols == iCols - 1) {
						iWidth = fFullWidth - fTotallWidth;
						fTotallWidth = 0;

						if (iRows * iCols - iIndex > iCols) {
							fTotalHeight += oCell.height();
						}
					} else {
						fTotallWidth += oCell.width();
					}

					if (iRows > 1 && iRows * iCols - iIndex <= iCols && fTotalHeight > 0)
						iHeight = fFullHeight - fTotalHeight;

					oCell.css({
						'width': iWidth,
						'height': iHeight
					});

					var iCellThick = oTransition.cellThick * oInstance.options.iResponsiveRate;
					var fCellThick = iCellThick / 2;
					var mOriginZ = (isMobileBrowser()) ?  - iCellThick : - fCellThick;

					var oShape = $('<div class="my-slider-shape" />')
						.css({
							'transform-origin'			: iWidth / 2 + 'px ' + iHeight / 2 + 'px ' + mOriginZ + 'px',
							'-ms-transform-origin'		: iWidth / 2 + 'px ' + iHeight / 2 + 'px ' + mOriginZ + 'px',
							'-webkit-transform-origin'	: iWidth / 2 + 'px ' + iHeight / 2 + 'px ' + mOriginZ + 'px'
						})
						.appendTo(oCell);

					var oBox = $('<div class="my-slider-front" />')
						.css({
							'width': iWidth,
							'height': iHeight
						})
						.appendTo(oShape);

					if (iCellThick > 0)
						if (iRotationX == -90)
							oNextCell = $('<div class="my-slider-box-top" />')
								.css({
									'width'				: iWidth,
									'height'			: iCellThick,
									'-webkit-transform'	: 'rotateX( 90deg ) translateY(-' + fCellThick + 'px) translateZ( ' + fCellThick + 'px )',
									'-moz-transform'	: 'rotateX( 90deg ) translateY(-' + fCellThick + 'px) translateZ( ' + fCellThick + 'px )',
									'-ms-transform'		: 'rotateX( 90deg ) translateY(-' + fCellThick + 'px) translateZ( ' + fCellThick + 'px )',
									'-o-transform'		: 'rotateX( 90deg ) translateY(-' + fCellThick + 'px) translateZ( ' + fCellThick + 'px )',
									'transform'			: 'rotateX( 90deg ) translateY(-' + fCellThick + 'px) translateZ( ' + fCellThick + 'px )'
								})
								.appendTo(oShape);
						else if (iRotationX == 90)
							oNextCell = $('<div class="my-slider-box-bottom" />')
								.css({
									'width' 			: iWidth,
									'height'			: iCellThick,
									'-webkit-transform'	: 'rotateX( -90deg ) translateY(' + fCellThick + 'px) translateZ( ' + (iHeight - fCellThick) + 'px )',
									'-moz-transform'	: 'rotateX( -90deg ) translateY(' + fCellThick + 'px) translateZ( ' + (iHeight - fCellThick) + 'px )',
									'-ms-transform'		: 'rotateX( -90deg ) translateY(' + fCellThick + 'px) translateZ( ' + (iHeight - fCellThick) + 'px )',
									'-o-transform'		: 'rotateX( -90deg ) translateY(' + fCellThick + 'px) translateZ( ' + (iHeight - fCellThick) + 'px )',
									'transform'			: 'rotateX( -90deg ) translateY(' + fCellThick + 'px) translateZ( ' + (iHeight - fCellThick) + 'px )'
								})
								.appendTo(oShape);
						else if (iRotationY == -90)
							oNextCell = $('<div class="my-slider-box-left" />')
								.css({
									'width'				: iCellThick,
									'height'			: iHeight,
									'-webkit-transform'	: 'rotateY( -90deg ) translateX(-' + fCellThick + 'px) translateZ( ' + fCellThick + 'px )',
									'-moz-transform'	: 'rotateY( -90deg ) translateX(-' + fCellThick + 'px) translateZ( ' + fCellThick + 'px )',
									'-ms-transform'		: 'rotateY( -90deg ) translateX(-' + fCellThick + 'px) translateZ( ' + fCellThick + 'px )',
									'-o-transform'		: 'rotateY( -90deg ) translateX(-' + fCellThick + 'px) translateZ( ' + fCellThick + 'px )',
									'transform'			: 'rotateY( -90deg ) translateX(-' + fCellThick + 'px) translateZ( ' + fCellThick + 'px )'
								})
								.appendTo(oShape);
						else if (iRotationY == 90)
							oNextCell = $('<div class="my-slider-box-right" />')
								.css({
									'width'				: iCellThick,
									'height'			: iHeight,
									'-webkit-transform'	: 'rotateY( 90deg ) translateX(' + fCellThick + 'px) translateZ( ' + (iWidth - fCellThick) + 'px )',
									'-moz-transform'	: 'rotateY( 90deg ) translateX(' + fCellThick + 'px) translateZ( ' + (iWidth - fCellThick) + 'px )',
									'-ms-transform'		: 'rotateY( 90deg ) translateX(' + fCellThick + 'px) translateZ( ' + (iWidth - fCellThick) + 'px )',
									'-o-transform'		: 'rotateY( 90deg ) translateX(' + fCellThick + 'px) translateZ( ' + (iWidth - fCellThick) + 'px )',
									'transform'			: 'rotateY( 90deg ) translateX(' + fCellThick + 'px) translateZ( ' + (iWidth - fCellThick) + 'px )'
								})
								.appendTo(oShape);
						else
							if (iRotationX > 90 || iRotationX < -90)
								oNextCell = $('<div class="my-slider-box-back" />')
									.css({
										'width'				: iWidth,
										'height'			: iHeight,
										'-webkit-transform'	: 'rotateX( -180deg ) translateZ( ' + iCellThick + 'px )',
										'-moz-transform'	: 'rotateX( -180deg ) translateZ( ' + iCellThick + 'px )',
										'-ms-transform'		: 'rotateX( -180deg ) translateZ( ' + iCellThick + 'px )',
										'-o-transform'		: 'rotateX( -180deg ) translateZ( ' + iCellThick + 'px )',
										'transform'			: 'rotateX( -180deg ) translateZ( ' + iCellThick + 'px )'
									})
									.appendTo(oShape);
							else
								oNextCell = $('<div class="my-slider-box-back" />')
									.css({
										'width'				: iWidth,
										'height'			: iHeight,
										'-webkit-transform'	: 'rotateY( -180deg ) translateZ( ' + iCellThick + 'px )',
										'-moz-transform'	: 'rotateY( -180deg ) translateZ( ' + iCellThick + 'px )',
										'-ms-transform'		: 'rotateY( -180deg ) translateZ( ' + iCellThick + 'px )',
										'-o-transform'		: 'rotateY( -180deg ) translateZ( ' + iCellThick + 'px )',
										'transform'			: 'rotateY( -180deg ) translateZ( ' + iCellThick + 'px )'
									})
									.appendTo(oShape);

					oShape.css(oCss);
					
					var fLeft = - parseFloat(oCell.position().left);
					var fTop = - parseFloat(oCell.position().top);

					if (oTransition.cellThick == 0) {
						oUpcomingBackground.clone()
							.css({
								'left': fLeft,
								'top': fTop,
								'position': 'absolute'
							})
							.appendTo(oBox);

						psMySliderContainer.find('.my-slider-front').css('background-color', 'transparent');
					} else {
						oCurrentBackground.clone()
							.css({
								'left': fLeft,
								'top': fTop,
								'position': 'absolute'
							})
							.appendTo(oBox);

						oUpcomingBackground.clone()
							.css({
								'left': fLeft,
								'top': fTop,
								'position': 'absolute',
								'display': 'block'
							})
							.appendTo(oNextCell);
					}

					var iKey = 0;
					var oTimeLineLite = new TimelineLite();

					while (oTransition.animation[iKey]) {
						var oToVariables = $.extend({}, oTransition.animation[iKey].toVars);

						oToVariables['delay'] = aDelay[iIndex] / 1000;

						if (!bNeedFinalAnimation)
							oToVariables['css'] = {
								top: 0,
								left: 0,
								opacity: 1,
								rotationX: 0,
								rotationY: 0,
								rotationZ: 0,
								scaleX: 1,
								scaleY: 1,
								scaleZ: 1
							}

						if (oTransition.shift && oTransition.cellThick == 0 && iRows == 1 && iCols == 1) {
							psMySliderContainer.find('.my-slider-front *').show();

							oTimeLineLite.fromTo(
								oShape,
								oTransition.animation[iKey].duration / 1000,
								oCss,
								oToVariables
							);

							TweenMax.to(
								oFlowPanel.find('.my-slider-current-bg'),
								oTransition.animation[iKey].duration / 1000,
								{
									top: - oCss.top,
									left: - oCss.left,
									ease: oTransition.animation[iKey].toVars.ease
								}
							);
						} else {
							oTimeLineLite.set(
								oShape,
								oCss
							);

							psMySliderContainer.find('.my-slider-front *').show();

							oTimeLineLite.to(
								oShape,
								oTransition.animation[iKey].duration / 1000,
								oToVariables
							);

							if (oTransition.cellThick == 0 && iRows == 1 && iCols == 1 && oTransition.css.opacity == 0)
								TweenMax.to(
									oFlowPanel.find('.my-slider-current-bg'),
									oTransition.animation[iKey].duration / 1000,
									{
										opacity: 0,
										ease: oTransition.animation[iKey].toVars.ease
									}
								);
						}

						iKey++;
					}

					var aToVariables = [];

					if (!bScaleX)
						aToVariables['scaleX'] = 1;

					if (!bScaleY)
						aToVariables['scaleY'] = 1;

					if (!bScaleZ)
						aToVariables['scaleZ'] = 1;

					if (iRotationX >= 0 && iRotationX < 90 || iRotationX > -90 && iRotationX <= 0)
						aToVariables['rotationX'] = 0;
					else if (iRotationX > 90 && iRotationX < 180)
						aToVariables['rotationX'] = 180;
					else if (iRotationX > -180 && iRotationX < -90)
						aToVariables['rotationX'] = -180;

					if (iRotationY >= 0 && iRotationY < 90 || iRotationY > -90 && iRotationY <= 0)
						aToVariables['rotationY'] = 0;
					else if (iRotationY > 90 && iRotationY < 180)
						aToVariables['rotationY'] = 180;
					else if (iRotationY > -180 && iRotationY < -90)
						aToVariables['rotationY'] = -180;

					if (iRotationZ >= 0 && iRotationZ < 90 || iRotationZ > -90 && iRotationZ <= 0)
						aToVariables['rotationZ'] = 0;
					else if (iRotationZ > 90 && iRotationZ < 180)
						aToVariables['rotationZ'] = 180;
					else if (iRotationZ > -180 && iRotationZ < -90)
						aToVariables['rotationZ'] = -180;

					if (aDelay[iIndex] == (iCols * iRows - 1) * oTransition.cellDelay)
						aToVariables['onComplete'] = function() {
							oInstance.updateActiveSlide();
						};

					oTimeLineLite.to(
						oShape,
						((!$.isEmptyObject(aToVariables) && bNeedFinalAnimation) ? 400 : 1) / 1000,
						aToVariables
					);
				}
			};
		};

		// Update active bullet classes
		oInstance.updateActiveBullet = function() {
			if (typeof oInstance.options.iCurrentSlideIndex != 'undefined' && typeof oInstance.options.iUpcomingSlideIndex != 'undefined') {
				if (oInstance.options.sNavigationType == 'bullet') {
					// Remove active class from current slide
					psMySliderContainer.find('.my-slider-bullet-container .my-slider-bullet:eq('+oInstance.options.iCurrentSlideIndex+')').removeClass('my-slider-bullet-active').css({
						'opacity': oInstance.options.oBullet.iOpacity,
						'background-image': 'url(' + oInstance.options.oBullet.sImage + ')'
					});

					// Add active class to upcoming slide
					psMySliderContainer.find('.my-slider-bullet-container .my-slider-bullet:eq('+oInstance.options.iUpcomingSlideIndex+')').addClass('my-slider-bullet-active').css({
						'opacity': oInstance.options.oBullet.iOpacityActive,
						'background-image': 'url(' + oInstance.options.oBullet.sImageActive + ')'
					});
				}
			}
		};

		// Update active slide visibility
		oInstance.updateActiveSlide = function() {
			oInstance.options.bIsBusy = false;
			oInstance.options.oCurrentSlide.data('bIsBusy', false);

			if (!oInstance.options.bSlideTransitionNeeded)
				oInstance.updateActiveBullet();

			var oSlideOld = oInstance.options.oCurrentSlide;
			var oSlideNew = oInstance.options.oCurrentSlide = oInstance.options.oUpcomingSlide;

			oInstance.options.iCurrentSlideIndex = oInstance.options.iUpcomingSlideIndex;

			oInstance.options.oCurrentSlide.css('overflow', 'hidden').find('.my-slide-background').show();

			// Clear all timeouts
			for (var iIndex=0; iIndex<oInstance.options.aTimeouts.length; iIndex++)
		        clearTimeout(oInstance.options.aTimeouts[iIndex]);

			oInstance.options.aTimeouts.length = 0;

			psMySliderContainer.find('.my-slider-flow-panel').find('*').each(function() {
				$(this).remove();
			});

			psMySliderContainer.find('.my-slider-flow-panel').remove();

			oSlideOld.removeClass('my-slide-opened');
			oSlideNew.addClass('my-slide-opened');

			oInstance.resume();

			oInstance.redrawAll(oInstance.options.oCurrentSlide);

			if (oInstance.options.bNeedRedraw) {
				oInstance.redrawNavigation();
				oInstance.options.bNeedRedraw = false;
			}

			oInstance.slidesIn();

			oInstance.options.fnOnSlidingComplete();
		};

		// Apply transition in for each slide element
		oInstance.slidesIn = function() {
			oInstance.options.oUpcomingSlide.find('.my-slider-slide').each(function() {
				oInstance.mySliderIn($(this));
			});
		};

		// Apply transition in for an specific slide
		oInstance.mySliderIn = function(poSlide) {
			TweenMax.killTweensOf(poSlide);

			TweenMax.fromTo(
				poSlide,
				poSlide.data('durationin') / 1000,
				{
					// From variables
					x: poSlide.data('offsetxin') * oInstance.options.iResponsiveRate,
					y: poSlide.data('offsetyin') * oInstance.options.iResponsiveRate,
					scaleX: poSlide.data('scalexin'),
					scaleY: poSlide.data('scaleyin'),
					rotation: poSlide.data('rotatein'),
					rotationX: poSlide.data('rotatexin'),
					rotationY: poSlide.data('rotateyin'),
					skewX: poSlide.data('skewxin'),
					skewY: poSlide.data('skewyin'),
					display: 'block',
					opacity: (poSlide.data('fadein') != false) ? 0 : 1,
					transformPerspective: poSlide.data('perspectivein'),
					transformOrigin: poSlide.data('transformoriginin')
				},
				{
					// To variables
					x: 0,
					y: 0,
					scaleX: 1,
					scaleY: 1,
					rotation: 0,
					rotationX: 0,
					rotationY: 0,
					skewX: 0,
					skewY: 0,
					opacity: (poSlide.data('fadein') != false) ? poSlide.data('opacity') : 1,
					ease: poSlide.data('easingin'),
					delay: poSlide.data('delayin') / 1000,
					onComplete: function() {
						poSlide.data('bAnchored', true);

						if (poSlide.data('delayout') > 0) {
							var iOutTimeoutId = setTimeout(function() {
								TweenMax.killTweensOf(poSlide);

								TweenMax.fromTo(
									poSlide,
									poSlide.data('durationout') / 1000,
									{
										// From variables
										transformOrigin: poSlide.data('transformoriginout'),
										transformPerspective: poSlide.data('perspectiveout')
									},
									{
										// To variables
										rotation: poSlide.data('rotateout'),
										rotationX: poSlide.data('rotatexout'),
										rotationY: poSlide.data('rotateyout'),
										skewX: poSlide.data('skewxout'),
										skewY: poSlide.data('skewyout'),
										scaleX: poSlide.data('scalexout'),
										scaleY: poSlide.data('scaleyout'),
										x: poSlide.data('offsetxout') * oInstance.options.iResponsiveRate,
										y: poSlide.data('offsetyout') * oInstance.options.iResponsiveRate,
										ease: poSlide.data('easingout'),
										opacity: (poSlide.data('fadeout') != false) ? 0 : 1,
										onComplete: function() {
											poSlide.css({
												display: 'none',
												opacity: (poSlide.data('fadeout') != false) ? poSlide.data('opacity') : 1
											});
										}
									}
								);
							}, poSlide.data('delayout'));

							oInstance.options.aTimeouts.push(iOutTimeoutId);

							poSlide.data('iOutTimeoutId', iOutTimeoutId);
						}
					}
				}
			);
		};

		// Apply transition out for each slide element
		oInstance.slidesOut = function() {
			oInstance.options.oCurrentSlide.find('.my-slider-slide').each(function() {
				oInstance.mySliderOut($(this));
			});
		};

		// Apply transition out for an specific slide
		oInstance.mySliderOut = function(poSlide) {
			poSlide.data('bAnchored', false);

			if (poSlide.data('iOutTimeoutId'))
				clearTimeout(poSlide.data('iOutTimeoutId'));

			TweenMax.killTweensOf(poSlide);

			TweenMax.fromTo(
				poSlide,
				poSlide.data('durationout') / 1000,
				{
					transformOrigin: poSlide.data('transformoriginout'),
					transformPerspective: poSlide.data('perspectiveout')
				},
				{
					rotation: poSlide.data('rotateout'),
					rotationX: poSlide.data('rotatexout'),
					rotationY: poSlide.data('rotateyout'),
					skewX: poSlide.data('skewxout'),
					skewY: poSlide.data('skewyout'),
					scaleX: poSlide.data('scalexout'),
					scaleY: poSlide.data('scaleyout'),
					x: poSlide.data('offsetxout') * oInstance.options.iResponsiveRate,
					y: poSlide.data('offsetyout') * oInstance.options.iResponsiveRate,
					ease: poSlide.data('easingout'),
					opacity: (poSlide.data('fadeout') != false) ? 0 : 1,
					onComplete: function() {
						poSlide.css({
							display: 'none',
							opacity: (poSlide.data('fadeout') != false) ? poSlide.data('opacity') : 1
						});
					}
				}
			);
		};

		// Animate specific slide
		oInstance.mySliderAnimate = function(poSlide, poVariables) {
			TweenMax.killTweensOf(poSlide);

			if (typeof poVariables.duration == 'undefined')
				poVariables.duration = 1000;

			if (typeof poVariables.easing == 'undefined')
				poVariables.easing = 'linear';

			if (typeof poVariables.offsetx == 'undefined')
				poVariables.offsetx = 0;

			if (typeof poVariables.offsety == 'undefined')
				poVariables.offsety = 0;

			if (typeof poVariables.scalex == 'undefined')
				poVariables.scalex = 1;

			if (typeof poVariables.scaley == 'undefined')
				poVariables.scaley = 1;

			if (typeof poVariables.rotate == 'undefined')
				poVariables.rotate = 0;

			if (typeof poVariables.rotatex == 'undefined')
				poVariables.rotatex = 0;

			if (typeof poVariables.rotatey == 'undefined')
				poVariables.rotatey = 0;

			if (typeof poVariables.skewx == 'undefined')
				poVariables.skewx = 0;

			if (typeof poVariables.skewy == 'undefined')
				poVariables.skewy = 0;

			if (typeof poVariables.opacity == 'undefined')
				poVariables.opacity = 1;

			if (typeof poVariables.delay == 'undefined')
				poVariables.delay = 0;

			if (typeof poVariables.perspective == 'undefined')
				poVariables.perspective = 400;

			if (typeof poVariables.transformorigin == 'undefined')
				poVariables.transformorigin = '50% 50% 0';

			TweenMax.set(
				poSlide,
				{
					transformPerspective: poVariables.perspective,
					transformOrigin: poVariables.transformorigin
				}
			);

			TweenMax.to(
				poSlide,
				poVariables.duration / 1000,
				{
					x: poVariables.offsetx * oInstance.options.iResponsiveRate,
					y: poVariables.offsety * oInstance.options.iResponsiveRate,
					scaleX: poVariables.scalex,
					scaleY: poVariables.scaley,
					rotation: poVariables.rotate,
					rotationX: poVariables.rotationx,
					rotationY: poVariables.rotationy,
					skewX: poVariables.skewx,
					skewY: poVariables.skewy,
					opacity: poVariables.opacity,
					delay: poVariables.delay / 1000,
					ease: poVariables.easing
				}
			);
		};

		// Return random element from array
		oInstance.getRandomArrayElement = function(paArray) {
			if (typeof paArray == 'undefined' || paArray == null)
				return false;

			if (paArray == '1,2')
				if (oInstance.options.iUpcomingSlideIndex - oInstance.options.iCurrentSlideIndex == 1 || (oInstance.options.iCurrentSlideIndex == oInstance.options.iAmountSlides - 1 && oInstance.options.iUpcomingSlideIndex == 0))
					return 1;
				else
					return 2;

			return paArray[Math.floor(Math.random() * paArray.length)];
		}

		// Initialize
		oInstance.init();
	};

	/*
	* Get url of this js file
	*/
	var getSelfUrl = function() {
	    var aScripts = document.getElementsByTagName('SCRIPT');
	    var sPath = '';

	    if (aScripts && aScripts.length > 0) {
	        for (var iIndex in aScripts) {
	            if (aScripts[iIndex].src && aScripts[iIndex].src.match(/\/my-slider/)) {
	                sPath = aScripts[iIndex].src.substring(0, (aScripts[iIndex].src + '').lastIndexOf('js/'));
	                break;
	            }
	        }
	    }

	    if (sPath == '')
			sPath = '../my-slider/';

	    return sPath;
	}

	/*
	* Check current browser is ie8 or lower
	*/
	var isLowerThanIE8 = function() {
		var userAgent = navigator.userAgent.toLowerCase();
		return (userAgent.indexOf('msie') != -1 && parseFloat((userAgent.match(/.*(?:rv|ie)[\/: ](.+?)([ \);]|$)/) || [])[1]) < 9) ? true : false;
	};

	/*
	* Check current browser is mobile
	*/
	var isMobileBrowser = function() {
	    var aMobile = ['iphone', 'ipad', 'iPod', 'android', 'blackberry', 'webos','nokia','opera mini','windows mobile','windows phone','iemobile'];

		for (var iIndex in aMobile)
			if (navigator.userAgent.toLowerCase().indexOf(aMobile[iIndex].toLowerCase()) > 0)
				return true;

	    return false;
	}
	
	/*
	* Return allowed transitions
	*/
	var getAllowedTransitions = function(pmId) {
		var aAllowedTransitions = [
			// 1
			{
				title: 'Fade',
				rows: 1,
				cols: 1,
				cellOrder: 'a-z',
				cellDelay: 0,
				cellThick: 0,
				overflow: 'hidden',
				shift: false,
				css: {
					top: 0,
					left: 0,
					opacity: 0
				 },
				animation:	[
					{
						duration: 800,
						toVars: {
							ease: 'easeInOutQuad'
						}
					}
				]
			},
			// 2
			{
				title: 'Fade Cells To Bottom',
				rows: 20,
				cols: 1,
				cellOrder: 'a-z',
				cellDelay: 20,
				cellThick: 0,
				overflow: 'hidden',
				shift: false,
				css: {
					top: 0,
					left: 0,
					opacity: 0
				 },
				animation:	[
					{
						duration: 500,
						toVars: {
							ease: 'easeInOutQuart'
						}
					}
				]
			}
		];

		if ($.isNumeric(pmId) && pmId >= 1 && pmId <= aAllowedTransitions.length)
			return aAllowedTransitions[pmId - 1];

		return aAllowedTransitions;
	}
})(jQuery);