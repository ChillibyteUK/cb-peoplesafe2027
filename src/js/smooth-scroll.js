/**
 * Smooth scrolling with Lenis (js/vendor/lenis.min.js, loaded before this).
 * The instance is window.cbLenis, so other code can stop() and start() it.
 *
 * Left off for visitors who ask for reduced motion, and on touch devices Lenis
 * does not take over (syncTouch is off), so phones and tablets scroll natively.
 *
 * What it changes, and what to do about it:
 *  - An element with its own scrollbar (a long menu, a dialog) needs
 *    data-lenis-prevent, or the page scrolls instead of the element.
 *  - Anything that locks the page (a dialog, a consent banner, a mobile menu)
 *    should call window.cbLenis.stop() and start() again. dialog.js does.
 *  - In-page links (#section, the skip link) are handled here so the page
 *    glides and keyboard focus still moves to the target.
 */
export function initSmoothScroll() {
	const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	if (typeof window.Lenis !== 'function' || reduce) {
		return;
	}

	// autoRaf: Lenis only moves the page when something calls its raf(); this
	// lets it run its own animation loop. (Without it the wheel is intercepted
	// but nothing scrolls.)
	const lenis = new window.Lenis({ autoRaf: true, anchors: false });
	window.cbLenis = lenis;

	document.addEventListener('click', (event) => {
		const link = event.target.closest('a[href^="#"]');

		if (!link || event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey) {
			return;
		}

		const id = decodeURIComponent(link.getAttribute('href').slice(1));
		const target = id ? document.getElementById(id) : document.body;

		if (!target) {
			return;
		}

		event.preventDefault();
		// Lenis honours the target's scroll-margin-top (set on [id] in base.css), so it lands below the fixed nav.
		lenis.scrollTo(target, {
			onComplete: () => {
				if (id) {
					if (!target.hasAttribute('tabindex') && !/^(A|BUTTON|INPUT|SELECT|TEXTAREA)$/.test(target.tagName)) {
						target.setAttribute('tabindex', '-1');
					}

					target.focus({ preventScroll: true });
					window.history.pushState(null, '', `#${id}`);
				}
			},
		});
	});
}
