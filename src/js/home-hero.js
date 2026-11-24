/**
 * Home hero slideshow: the photos fade from one to the next. A pause button
 * (WCAG 2.2.2) stops and restarts it. It also holds while keyboard focus is in
 * the hero (not for the pointer: hovering does not pause it), while the tab is
 * hidden and while the hero is off screen. With reduced motion nothing moves:
 * the first photo stays and the button stays hidden. Each slide's event cards pop up by CSS when the slide
 * gets .is-active.
 *
 * Markup: [data-home-hero] > .home-hero__slides > .home-hero__slide (per photo)
 *         and button[data-home-hero-pause] with data-label-pause / data-label-play.
 */
const INTERVAL = 7000;

export function initHomeHero() {
	document.querySelectorAll('[data-home-hero]').forEach((root) => {
		const slides = Array.from(root.querySelectorAll('.home-hero__slide'));
		const button = root.querySelector('[data-home-hero-pause]');

		if (slides.length < 2 || !button) {
			return;
		}

		if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
			return;
		}

		const label = button.querySelector('.visually-hidden');
		let index = 0;
		let timer = null;
		let paused = false;
		let held = false;
		let inView = true;

		function show(next) {
			slides[index].classList.remove('is-active');
			slides[index].setAttribute('aria-hidden', 'true');
			index = next;
			slides[index].classList.add('is-active');
			slides[index].removeAttribute('aria-hidden');
		}

		function update() {
			window.clearInterval(timer);
			timer = null;

			if (!paused && !held && inView && !document.hidden) {
				timer = window.setInterval(() => show((index + 1) % slides.length), INTERVAL);
			}
		}

		button.hidden = false;
		button.addEventListener('click', () => {
			paused = !paused;
			button.setAttribute('aria-pressed', paused ? 'true' : 'false');

			if (label) {
				label.textContent = paused ? button.dataset.labelPlay : button.dataset.labelPause;
			}

			update();
		});

		// Only keyboard focus holds it: a mouse click on the pause button leaves focus there, which must not keep it paused.
		root.addEventListener('focusin', (event) => {
			held = event.target.matches(':focus-visible');
			update();
		});
		root.addEventListener('focusout', () => {
			held = false;
			update();
		});
		document.addEventListener('visibilitychange', update);

		if ('IntersectionObserver' in window) {
			new IntersectionObserver((entries) => {
				inView = entries[0].isIntersecting;
				update();
			}).observe(root);
		}

		update();
	});
}
