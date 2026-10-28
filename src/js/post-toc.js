/**
 * A single post's Quick links sidebar: marks the link for whichever h2 section is in view. An IntersectionObserver
 * (not a scroll listener) watches a band just below the header; the first heading is marked until one crosses it.
 */
export function initPostToc() {
	const links = document.querySelectorAll('.post-toc__link');

	if (!links.length || !('IntersectionObserver' in window)) {
		return;
	}

	const linksById = new Map();
	const targets = [];

	links.forEach((link) => {
		// An id can hold a literal %e2%80%9c (WordPress encodes curly quotes in a slug), so try it as written first.
		const raw = link.getAttribute('href').slice(1);
		const id = document.getElementById(raw) ? raw : decodeURIComponent(raw);
		const target = document.getElementById(id);

		if (!target) {
			return;
		}

		linksById.set(id, link);
		targets.push(target);
	});

	if (!targets.length) {
		return;
	}

	const setActive = (id) => {
		linksById.forEach((link, linkId) => {
			link.classList.toggle('is-active', linkId === id);
			link.toggleAttribute('aria-current', linkId === id);
		});
	};

	const observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					setActive(entry.target.id);
				}
			});
		},
		{ rootMargin: '-15% 0px -70% 0px' }
	);

	targets.forEach((target) => observer.observe(target));
	setActive(targets[0].id);
}
