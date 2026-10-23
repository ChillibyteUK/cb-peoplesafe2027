/*!
 * cb-peoplesafe2027 v1.0.0 (https://github.com/ChillibyteUK/cb-peoplesafe2027)
 * Copyright 2026 Chillibyte - DS
 * Licensed under GPL-3.0
 */
(function () {
	'use strict';

	/**
	 * GSAP and ScrollTrigger, set up once for any block that needs them. Loads
	 * after js/vendor/gsap.min.js and ScrollTrigger.min.js (see
	 * cb_peoplesafe2027_enqueue_motion()), and exposes window.cbMotion:
	 *
	 *   const { gsap, ScrollTrigger, mm } = window.cbMotion;
	 *   mm.add('(prefers-reduced-motion: no-preference)', () => { ...animations... });
	 *
	 * Use mm (gsap.matchMedia) for every animation so reduced motion is honoured
	 * and everything is cleaned up if the condition changes. Prefer CSS where it
	 * does the job; reach for GSAP for scrubbing, pinning and timelines.
	 */
	(function () {
	  const {
	    gsap,
	    ScrollTrigger
	  } = window;
	  if (!gsap || !ScrollTrigger) {
	    return;
	  }
	  gsap.registerPlugin(ScrollTrigger);

	  // Lenis owns the scrolling, so tell ScrollTrigger whenever it moves.
	  if (window.cbLenis) {
	    window.cbLenis.on('scroll', ScrollTrigger.update);
	  }
	  window.cbMotion = {
	    gsap,
	    ScrollTrigger,
	    mm: gsap.matchMedia()
	  };
	  document.dispatchEvent(new CustomEvent('cb:motion-ready'));
	})();

})();
//# sourceMappingURL=motion.js.map
