/*!
 * cb-peoplesafe2027 v1.0.0 (https://github.com/ChillibyteUK/cb-peoplesafe2027)
 * Copyright 2026 Chillibyte - DS
 * Licensed under GPL-3.0
 */
(function () {
	'use strict';

	/**
	 * The shared slider: one accessible carousel for every slider block, built on
	 * Embla (vendored in js/vendor/, DEC-029). Embla only moves the slides; this
	 * file adds the arrows, the dots and the accessibility behaviour, so every
	 * slider looks and works the same.
	 *
	 * Markup (see blocks/quote/render.php for a worked example):
	 *
	 *   <div class="slider" data-slider data-slider-label="Customer quotes">
	 *     <div class="slider__viewport">
	 *       <div class="slider__track">
	 *         <div class="slider__slide">...</div>
	 *         <div class="slider__slide">...</div>
	 *       </div>
	 *     </div>
	 *   </div>
	 *
	 * Optional data attributes on the .slider element:
	 *   data-slider-loop="true"   loop round (the slides must fill the viewport)
	 *   data-slider-autoplay="ms" move on by itself every ms milliseconds
	 *   data-slider-step="1"      the arrows and dots move one slide at a time, not a screenful
	 *   data-slider-controls="none" no arrows, dots or pause button (a logo row that just drifts by)
	 *
	 * Slides per view and the gap between slides are CSS: set --slides-per-view
	 * and --slider-gap on the .slider element, per breakpoint, in the block's CSS.
	 * Without JavaScript the viewport simply scrolls sideways.
	 *
	 * Autoplay is opt-in per slider: data-slider-autoplay="3000" moves it on every
	 * 3 seconds. It pauses while the pointer or focus is on the slider, while the
	 * slider is off screen or the tab is hidden, and for good once the visitor uses
	 * the arrows, the dots or drags. A pause/play button is added so it can always
	 * be stopped. There is no autoplay, and no movement, for visitors who prefer
	 * reduced motion.
	 */
	(function () {

	  var REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)');
	  function button(className, label, symbol) {
	    var el = document.createElement('button');
	    el.type = 'button';
	    el.className = className;
	    el.setAttribute('aria-label', label);
	    el.innerHTML = symbol;
	    return el;
	  }
	  function init(root) {
	    var viewport = root.querySelector('.slider__viewport');
	    var slides = Array.prototype.slice.call(root.querySelectorAll('.slider__slide'));
	    var label = root.getAttribute('data-slider-label') || 'Carousel';
	    if (!viewport || !window.EmblaCarousel || slides.length < 2) {
	      root.classList.add('slider--static');
	      return;
	    }
	    var embla = window.EmblaCarousel(viewport, {
	      loop: 'true' === root.getAttribute('data-slider-loop'),
	      align: 'start',
	      containScroll: 'trimSnaps',
	      // A rotating row moves one slide at a time, and a little slower; the rest move a screenful.
	      slidesToScroll: root.hasAttribute('data-slider-autoplay') || '1' === root.getAttribute('data-slider-step') ? 1 : 'auto',
	      inViewThreshold: 0.5,
	      duration: REDUCED.matches ? 0 : root.hasAttribute('data-slider-autoplay') ? 40 : 25
	    });

	    // A looping slider needs more slides than fit in view. Embla quietly stops looping when there are too few, which
	    // made the slider run to the end and jump back. So, when it should loop and can scroll, add hidden copies of the
	    // slides until it can loop for real. The copies are marked hidden from assistive technology and cannot be focused.
	    var wantsLoop = 'true' === root.getAttribute('data-slider-loop');
	    if (wantsLoop && embla.scrollSnapList().length > 1 && !embla.internalEngine().options.loop) {
	      var track = slides[0].parentNode;
	      for (var round = 0; round < 4 && !embla.internalEngine().options.loop; round++) {
	        slides.forEach(function (slide) {
	          var copy = slide.cloneNode(true);
	          copy.setAttribute('aria-hidden', 'true');
	          copy.setAttribute('data-slider-copy', '');
	          copy.inert = true;
	          track.appendChild(copy);
	        });
	        embla.reInit({
	          loop: true
	        });
	      }
	    }
	    root.classList.add('slider--ready');
	    root.setAttribute('role', 'region');
	    root.setAttribute('aria-roledescription', 'carousel');
	    root.setAttribute('aria-label', label);
	    viewport.setAttribute('tabindex', '0');
	    viewport.setAttribute('aria-live', 'off');
	    slides.forEach(function (slide, index) {
	      slide.setAttribute('role', 'group');
	      slide.setAttribute('aria-roledescription', 'slide');
	      slide.setAttribute('aria-label', index + 1 + ' of ' + slides.length);
	    });
	    var controls = document.createElement('div');
	    controls.className = 'slider__controls';
	    var prev = button('slider__arrow slider__arrow--prev', 'Previous', '<span aria-hidden="true">&#8249;</span>');
	    var next = button('slider__arrow slider__arrow--next', 'Next', '<span aria-hidden="true">&#8250;</span>');
	    var dots = document.createElement('div');
	    dots.className = 'slider__dots';
	    controls.appendChild(prev);
	    controls.appendChild(dots);
	    controls.appendChild(next);
	    if ('none' !== root.getAttribute('data-slider-controls')) {
	      root.appendChild(controls);
	    }
	    var dotButtons = [];

	    // Autoplay.
	    var delay = parseInt(root.getAttribute('data-slider-autoplay'), 10) || 0;
	    var playing = delay > 0 && !REDUCED.matches;
	    var hovering = false;
	    var onScreen = true;
	    var pauseButton = null;
	    function setPlaying(value) {
	      playing = value;
	      if (pauseButton) {
	        pauseButton.classList.toggle('is-paused', !playing);
	        pauseButton.setAttribute('aria-label', playing ? 'Pause rotation' : 'Start rotation');
	      }
	    }
	    function tick() {
	      if (!playing || hovering || !onScreen || document.hidden || root.contains(document.activeElement)) {
	        return;
	      }
	      if (embla.canScrollNext()) {
	        embla.scrollNext();
	      } else if (!wantsLoop) {
	        embla.scrollTo(0);
	      }
	    }
	    if (playing) {
	      pauseButton = button('slider__pause', 'Pause rotation', '<span aria-hidden="true"></span>');
	      controls.appendChild(pauseButton);
	      pauseButton.addEventListener('click', function () {
	        setPlaying(!playing);
	      });
	      root.addEventListener('pointerenter', function () {
	        hovering = true;
	      });
	      root.addEventListener('pointerleave', function () {
	        hovering = false;
	      });
	      if ('IntersectionObserver' in window) {
	        new IntersectionObserver(function (entries) {
	          onScreen = entries[0].isIntersecting;
	        }).observe(root);
	      }
	      window.setInterval(tick, delay);
	    }

	    // Using the controls, or dragging, stops it for good (the pause button can restart it).
	    function stopForGood() {
	      if (playing) {
	        setPlaying(false);
	      }
	    }
	    embla.on('pointerDown', stopForGood);
	    function buildDots() {
	      dots.innerHTML = '';
	      dotButtons = embla.scrollSnapList().map(function (snap, index) {
	        var dot = button('slider__dot', 'Go to slide ' + (index + 1), '');
	        dot.addEventListener('click', function () {
	          stopForGood();
	          embla.scrollTo(index);
	        });
	        dots.appendChild(dot);
	        return dot;
	      });
	      controls.hidden = dotButtons.length < 2;
	    }
	    function update() {
	      var selected = embla.selectedScrollSnap();
	      var inView = embla.slidesInView();
	      dotButtons.forEach(function (dot, index) {
	        if (index === selected) {
	          dot.setAttribute('aria-current', 'true');
	        } else {
	          dot.removeAttribute('aria-current');
	        }
	      });
	      prev.disabled = !embla.canScrollPrev();
	      next.disabled = !embla.canScrollNext();

	      // Slides out of view can't be tabbed to or read.
	      slides.forEach(function (slide, index) {
	        slide.inert = -1 === inView.indexOf(index);
	      });
	    }
	    prev.addEventListener('click', function () {
	      stopForGood();
	      embla.scrollPrev();
	    });
	    next.addEventListener('click', function () {
	      stopForGood();
	      embla.scrollNext();
	    });
	    viewport.addEventListener('keydown', function (event) {
	      if ('ArrowLeft' === event.key) {
	        embla.scrollPrev();
	        event.preventDefault();
	      } else if ('ArrowRight' === event.key) {
	        embla.scrollNext();
	        event.preventDefault();
	      }
	    });
	    embla.on('reInit', function () {
	      buildDots();
	      update();
	    });
	    embla.on('select', update);
	    embla.on('slidesInView', update);
	    buildDots();
	    update();
	  }
	  function initAll() {
	    Array.prototype.forEach.call(document.querySelectorAll('[data-slider]'), init);
	  }
	  if ('loading' === document.readyState) {
	    document.addEventListener('DOMContentLoaded', initAll);
	  } else {
	    initAll();
	  }
	})();

})();
//# sourceMappingURL=slider.js.map
