/**
 * How steps: the staggered reveal.
 *
 * From lg the numbered timeline draws across and each step's dot and card
 * appear as the line reaches them. The line and the cards are timed in CSS
 * (src/blocks/how-steps.css, --how-step per step); this only says when to
 * start: once the steps scroll into view. Below lg there is no timeline, so
 * each card appears as it scrolls into view. Without IntersectionObserver, or
 * with reduced motion, nothing is hidden at all.
 */
export function initHowSteps() {
	const blocks = document.querySelectorAll('[data-how-steps]');

	if (!blocks.length) {
		return;
	}

	if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
		return;
	}

	const wide = window.matchMedia('(min-width: 992px)');

	blocks.forEach((block) => {
		const list = block.querySelector('.how-steps__steps');
		const steps = block.querySelectorAll('.how-steps__step');

		if (!list || !steps.length) {
			return;
		}

		// Hidden only now that this script is running, so nothing is lost without it.
		block.classList.add('is-armed');

		const observer = new IntersectionObserver(
			(entries) => {
				entries.forEach((entry) => {
					if (!entry.isIntersecting) {
						return;
					}

					observer.unobserve(entry.target);

					if (entry.target === list) {
						block.classList.add('is-in');
						steps.forEach((step) => step.classList.add('is-in'));
					} else {
						entry.target.classList.add('is-in');
					}
				});
			},
			{ threshold: wide.matches ? 0.3 : 0.2, rootMargin: '0px 0px -5% 0px' }
		);

		if (wide.matches) {
			observer.observe(list);
		} else {
			steps.forEach((step) => observer.observe(step));
		}
	});
}
