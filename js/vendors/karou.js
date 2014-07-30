/*!
 * The MIT License (MIT)
 *
 * Copyright (c) 2012 bu Paul Sore (paul.sore@gmail.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/* - 2012-2014 - @bulgrz @kappuccinoweb */

;(function($, document, window) {

	"use strict";

	// construct
	var Karou = function(settings, elem) {

		// remplacer les options par les paramètres user
		this.settings = $.extend({}, this.settings, settings);

		this.settings.$karou = $(elem);
		this.$scroller	= $(elem).children();
		this.$wrapper	= $(elem); // Shall be unused

		this.$els	= $(elem).find('.karouel');
		this.settings.elCount = this.$els.length;

		// finds out ou prefix for css transforms/transitions
		this.getVendorPrefix();

		// find user agent
		var userAgent = navigator.userAgent.toLowerCase();
		this.isAndroid = /android/i.test(userAgent);

		// If we need a nav, we must add it before we handle the dom/styles
		if (this.settings.hasNav) this.addNav();
		// enable touch event if Hammer.js is present
		if (typeof Hammer === 'function') this.enableTouch();
		// calculate elements sizes and apply base styles
		this.prepareDom(this.settings.animation);
		// ... and bind the resize event
		$(window).on('resize', this.prepareDom.bind(this, this.settings.animation))

		// handle specific options (navigation, autoplay, etc...)
		if (this.settings.hasPrevNext) this.addPrevNext();
		if (this.settings.autoplay) this.autoplay();
		if (this.settings.keyboard) this.bindKeypress();

		// fin de l'init, appeller le callback onMove pour plus de consistence coté user
		if (typeof this.settings.onMove === 'function') {
			this.settings.onMove.apply(this, [this, 0]);
		}

		if (typeof this.settings.onInit === 'function') {
			this.settings.onInit.apply(this, [this, 0]);
		}

	}

	Karou.prototype = {

		settings: {
			$karou : $('#caroucontainer'), // default
			$papa : {}, // parent direct de $karou
			$navbar : {}, // ref barre de nav

			elCount : 0, // nb d'elements
			carouW : 0, // width du carou
			current : 0, // elem courant
			navBtnW : 28, // width des puces nav
			duration : 300, // duration autoplay
			interval: 0, // Intervant dans le autoplau

			hasNav : true, // afficher les puces de nav
			hasPrevNext : false, // afficher precedent/suivant
			autoplay: false,

			animation : 'slide', // default animation
			keyboard  : false // keyboard slide
		},

		$k : {}, // shortcut de settings.$karou
		$n : {}, // shortcut de navbar
		vendorPrefix : '-webkit-', // default prefix


		/**
		 * Sets up the DOM of our caroussel with everything we may need
		 * for the required animation.
		 * Using different animations should not impact how the the base HTML is written.
		 *
		 * @param type // different animations may need a different DOM
		 */
		prepareDom: function(type) {

			if (type === 'slide') {
				// If "slide", every element ('karouel') should inherit the REAL size of the parent container ('karou')
				// Karouels will be stacked next to each other and Karou will size up to contain them all.

				var karouelWidth = this.$wrapper.width(); // calculate widths
				var karouWidth = karouelWidth * this.settings.elCount;

				this.$karouelPercentSize = (karouelWidth / karouWidth) * 100; // useful ?
				this.$els.width(karouelWidth); // apply widths
				this.$scroller.width(karouWidth);
				this.$els.css({ // apply styles
					float: 'left'
				}).removeClass('hidden'); // show

				// save settings
				this.settings.carouW = karouelWidth;
				// center navigation bar if we have one
				if (this.settings.hasNav) this.centerNav();

				return;
			}

			if (type === 'fade') {
				// TODO "fade" preparation
				return;
			}

		},


		getVendorPrefix: function() {

			this.vendorPrefix = (function () {
				var styles = window.getComputedStyle(document.documentElement, ''),
					pre = (Array.prototype.slice
						.call(styles)
						.join('')
						.match(/-(moz|webkit|ms)-/) || (styles.OLink === '' && ['', 'o'])
						)[1];
				return '-' + pre + '-';
			})();

		},

		/**
		 * Enable touch event support for the caroussel
		 *
		 */
		enableTouch: function() {

			var options = {
				drag_lock_to_axis: true
			}

			// fix scroll release on android
			//if (this.isAndroid) options.prevent_default = true

			this.$wrapper.hammer(options)
				.on("release dragleft dragright", this.dragTo.bind(this));
		},

		dragTo: function(event) {

			// prevent scroll during drag
			event.gesture.preventDefault()

			if (event.type != 'release') {
				var scrollerOffset = -(100/this.settings.elCount)*this.settings.current;
				var dragOffset = ((100/this.settings.carouW) * event.gesture.deltaX) / this.settings.elCount;

				// slow down when reaching borders
				var thresholdRight = (this.$karouelPercentSize * (this.settings.elCount - 1)) * -1;
				var thresholdLeft = 0
				var speed = 1; // speed modifier (default 1 )

				// adapt speed factor when reaching the borders
				var movement = dragOffset + scrollerOffset;
				if (movement > thresholdLeft || movement < thresholdRight) {
					speed = 0.3;
				}
				// recalc movement after speed ponderation
				var movement = (dragOffset * speed) + scrollerOffset;

				this.setOffset(movement, false);
			}

			if (event.type === 'release') {
				// if at least 1/3 the element was dragged
				if (Math.abs(event.gesture.deltaX) > this.settings.carouW / 3) {
					if (event.gesture.direction === 'right') {
						this.animateTo(this.settings.current - 1)
					} else {
						this.animateTo(this.settings.current + 1)
					}
				} else {
					// didn't drag that much, reposition the scroller to its origin
					this.animateTo(this.settings.current)
				}
			}

		},

		addNav: function() {

			// ajouter l'elem de nav
			var carounav = $('<div class="carounav" />').insertAfter(this.$scroller);

			// Binder les puces
			for (var i=0; i < this.settings.elCount; i++) {
				if (i === 0) {
					var nav = $('<div class="navon karoupuce" data-goto="'+i+'" />').appendTo(carounav);
				} else {
					var nav = $('<div class="navoff karoupuce" data-goto="'+i+'" />').appendTo(carounav);
				}

				this.settings.$navbar = carounav;
				this.$n = carounav;

				// event proxy pour garder le scope de l'instance
				nav.on('click', $.proxy(function(e) {
					var to = $(e.target).attr('data-goto'); // récupère le $(this) de l'event
					this.animateTo(to);
				}, this));
			};

			// centrer les puces
			this.centerNav();
		},

		bindKeypress: function() {

			$(document).on('keyup', $.proxy(function(e) {
				if (e.keyCode == 37) {
					this.animateTo(this.settings.current - 1);
				}
				if (e.keyCode == 39) {
					this.animateTo(this.settings.current + 1);
				}
			}, this ));
		},

		addPrevNext: function() {

			// ajouter l'elem de prevnext
			var carouprev = $('<div class="carounav clearfix" />').appendTo(this.$wrapper);

			var prev = $('<div class="navprev" data-goto="prev" />').appendTo(carouprev);
			var next = $('<div class="navnext" data-goto="next" />').appendTo(carouprev);

			prev.on('click', $.proxy(function() {
				this.animateTo(this.settings.current - 1);
			}, this));

			next.on('click', $.proxy(function() {
				this.animateTo(this.settings.current + 1);
			}, this));
		},

		animateTo: function(to) {

			switch(this.settings.animation) {
				case 'slide' :
					this.slideTo(to);
					break;
				case 'fade' :
					this.fadeTo(to);
					break;
				default:
					this.slideTo(to);
			}

		},

		/**
		 * Apply an offset to the scroller ( via translate3d transform )
		 * @param percent // float
		 * @param forceAnimate // force sliding animation
		 */
		setOffset: function(percent, forceAnimate) {

			if (forceAnimate) { // no swipe/drag, forces a smooth transition
				this.$scroller.css('transition', this.vendorPrefix+'transform '+ this.settings.duration +'ms ease-in-out');
			} else { // drag event, we don't need a transition
				this.$scroller.css('transition', 'none 0s linear');
			}
			// apply transform
			this.$scroller.css(this.vendorPrefix+'transform', 'translate3d('+ percent +'%,0,0) scale3d(1,1,1)');
		},

		/**
		 * Make to scroller slide to the desired pane with an animation
		 *
		 * @param to // index of target pane
		 */
		slideTo: function(to) {
			//if (to > (this.settings.elCount - 1) || to < 0) return false;
			if (to > (this.settings.elCount - 1)) return this.slideTo(this.settings.elCount - 1);
			if (to < 0) return this.slideTo(0);

			this.setOffset(((this.$karouelPercentSize * to)*-1), true);

			this.settings.current = to;
			this.updateNav();

			// Le karouel va bouger, mettre à jour la classe "active"
			this.$els.removeClass('active').eq(to).addClass('active');

			if (typeof this.settings.onMove === 'function') {
				this.settings.onMove.apply(this, [this, to]);
			}
		},

		/**
		 * Old fade animation (jQuery, no css transitions. Will be ugly on mobile)
		 * @param to
		 */
		fadeTo: function(to) {

			if (this.settings.current < this.settings.elCount-1) {
				this.settings.current += 1;
			} else {
				this.settings.current = 0;
			}

			this.$randel = this.$els.eq(this.settings.current);

			this.$randel.animate({
				'opacity' : 1
			}, { duration : this.settings.duration, queue : false });

			this.$scroller.find('.karouel').not(this.$randel).animate({
				'opacity' : 0
			}, { duration : this.settings.duration, queue : false });

		},

		updateNav: function() {
			if (!this.settings.hasNav) return false;

			this.$n.find('.karoupuce').removeClass('navon').addClass('navoff');
			this.$n.find('.karoupuce[data-goto="'+this.settings.current+'"]').addClass('navon').removeClass('navoff')
		},

		centerNav: function() {

			console.log(this.settings.$navbar)

			this.settings.navBtnW = $('.karoupuce').outerWidth();
			var totalNavW 	= this.settings.elCount * this.settings.navBtnW;
			var spaceLeft 	= this.settings.$navbar.width() - totalNavW;
			var margin		= Math.round(spaceLeft/2);
			this.$n.find('.karoupuce[data-goto="0"]').css('margin-left', margin+'px');

		},

		autoplay : function() {

			this.settings.autoplay = setInterval($.proxy(function() {

				if (this.settings.current < this.settings.elCount-1) {
					this.animateTo(this.settings.current + 1);
				} else {
					this.animateTo(0);
				}

			}, this), this.settings.interval)
		}

	};

	$.fn.karou = function(opts) {
		return this.each(function() {
			new Karou(opts, this);
		});
	};

})(jQuery, document, window);
