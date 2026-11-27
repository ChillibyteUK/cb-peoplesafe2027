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

/**
 * Fades each Vimeo background video in once it is actually playing, so the
 * slide's photo (its poster) covers the player's start-up. Listens for the
 * player's own postMessage "play" event; a timeout reveals it regardless.
 */
function revealVideos() {
	const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	document.querySelectorAll('.home-hero__video').forEach((frame) => {
		const slide = frame.closest('.home-hero__slide');

		// Reduced motion: the photo stays and the video never loads. A slide with no photo keeps its video, as there is nothing else to show.
		if (reduced && slide.querySelector('.home-hero__image')) {
			frame.remove();
			return;
		}

		if (slide.classList.contains('is-active')) {
			frame.src = frame.dataset.src;
		}

		const reveal = () => frame.classList.add('is-playing');

		window.addEventListener('message', (event) => {
			if (event.source !== frame.contentWindow || typeof event.data !== 'string') {
				return;
			}

			try {
				const data = JSON.parse(event.data);

				if (data.event === 'play' || data.event === 'playProgress') {
					reveal();
				}
			} catch (e) {
				// Not a player message.
			}
		});

		const listen = () => {
			['play', 'playProgress'].forEach((name) => {
				frame.contentWindow.postMessage(JSON.stringify({ method: 'addEventListener', value: name }), 'https://player.vimeo.com');
			});
		};

		// A deferred video gets its src later, so this runs on each load rather than once up front.
		frame.addEventListener('load', () => {
			listen();
			window.setTimeout(reveal, 8000);
		});
	});
}

export function initHomeHero() {
	revealVideos();

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
			sync();
		}

		const command = (frame, method) => {
			if (frame.src) {
				frame.contentWindow.postMessage(JSON.stringify({ method }), 'https://player.vimeo.com');
			}
		};

		// Only the showing slide's video plays, and only while the slideshow runs; the next one is loaded ahead of its turn.
		function sync() {
			const running = !paused && inView && !document.hidden;

			slides.forEach((slide, i) => {
				const frame = slide.querySelector('.home-hero__video');

				if (!frame) {
					return;
				}

				if (!frame.src && (i === index || i === (index + 1) % slides.length)) {
					frame.src = frame.dataset.src;
					frame.addEventListener('load', sync);
				}

				command(frame, i === index && running ? 'play' : 'pause');
			});
		}

		function update() {
			sync();
			window.clearInterval(timer);
			timer = null;

			if (!paused && !held && inView && !document.hidden) {
				timer = window.setInterval(() => show((index + 1) % slides.length), INTERVAL);
			}
		}

		sync();
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
