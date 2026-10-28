/**
 * Form iframes (Pardot). The address sits in data-form-src so nothing loads
 * until it is wanted: a form on the page loads as it nears the screen, and the
 * Book a Demo dialog loads its form when it first opens. The page's campaign
 * parameters (utm_*, gclid, msclkid, fbclid) are passed on to the form so a
 * lead keeps its source. The newsletter signup box passes the email address the same way (?email=).
 */
const FORWARDED = /^(utm_|gclid|msclkid|fbclid|gad_|email$)/;

export function loadFormFrame(frame) {
	const src = frame.getAttribute('data-form-src');

	if (!src || frame.getAttribute('src')) {
		return;
	}

	const url = new URL(src, window.location.href);

	new URLSearchParams(window.location.search).forEach((value, key) => {
		if (FORWARDED.test(key) && !url.searchParams.has(key)) {
			url.searchParams.set(key, value);
		}
	});

	frame.setAttribute('src', url.toString());
}

export function initFormFrames() {
	const frames = Array.from(document.querySelectorAll('iframe[data-form-src]:not(.is-in-dialog)'));

	if (!('IntersectionObserver' in window)) {
		frames.forEach(loadFormFrame);
		return;
	}

	const observer = new IntersectionObserver(
		(entries) => {
			entries.forEach((entry) => {
				if (entry.isIntersecting) {
					loadFormFrame(entry.target);
					observer.unobserve(entry.target);
				}
			});
		},
		{ rootMargin: '400px' }
	);

	frames.forEach((frame) => observer.observe(frame));
}
