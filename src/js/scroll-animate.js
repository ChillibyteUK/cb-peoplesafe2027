/**
 * Entrance animations. Anything with data-animate="fade-up|fade-left|fade-right"
 * (see src/css/animate.css) fades in once, the first time it scrolls into view.
 * Without IntersectionObserver, or with reduced motion, it is just shown.
 */
export function initScrollAnimate() {
	const items = document.querySelectorAll('[data-animate]');

	if (!items.length) {
		return;
	}

	const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	if (reduce || !('IntersectionObserver' in window)) {
		items.forEach((item) => item.classList.add('is-visible'));
		return;
	}

	// Anything already on screen at load is shown at once: a fade-in on the first view reads as a glitch, not an entrance.
	items.forEach((item) => {
		const box = item.getBoundingClientRect();

		if (box.top < window.innerHeight * 0.9 && box.bottom > 0) {
			item.classList.add('is-instant', 'is-visible');
		}
	});

	const observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-visible');
					observer.unobserve(entry.target);
				}
			});
		},
		{ threshold: 0.15, rootMargin: '0px 0px -5% 0px' }
	);

	items.forEach((item) => {
		if (!item.classList.contains('is-visible')) {
			observer.observe(item);
		}
	});
}
