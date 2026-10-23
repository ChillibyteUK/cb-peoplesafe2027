/**
 * Stats counter: each .stats-counter__number is printed at its final value; as
 * its block scrolls into view it counts up from 0 to that value and stops on
 * it. Nothing happens for visitors who prefer reduced motion, so they see the
 * final numbers. Markup: <span class="stats-counter__number" data-count="246" data-decimals="0">
 */
const DURATION = 2000;

function ease(t) {
	return 1 - Math.pow(1 - t, 3);
}

function run(el) {
	const target = parseFloat(el.getAttribute('data-count'));
	const decimals = parseInt(el.getAttribute('data-decimals'), 10) || 0;
	const start = performance.now();

	function frame(now) {
		const t = Math.min((now - start) / DURATION, 1);

		el.textContent = (target * ease(t)).toFixed(decimals);

		if (t < 1) {
			requestAnimationFrame(frame);
		} else {
			el.textContent = target.toFixed(decimals);
		}
	}

	requestAnimationFrame(frame);
}

export function initStatsCounters() {
	const numbers = document.querySelectorAll('.stats-counter__number');

	if (!numbers.length || window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
		return;
	}

	const observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					run(entry.target);
					observer.unobserve(entry.target);
				}
			});
		},
		{ threshold: 0.6 }
	);

	numbers.forEach((el) => {
		const decimals = parseInt(el.getAttribute('data-decimals'), 10) || 0;

		el.textContent = (0).toFixed(decimals);
		observer.observe(el);
	});
}
