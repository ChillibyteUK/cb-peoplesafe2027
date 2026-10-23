/**
 * Video behind a play button. The block prints a button holding a poster
 * image; the real player is only created when it is clicked, so a page with a
 * video loads no third-party iframe until the visitor asks for it.
 *
 * Markup: <button class="video-facade" data-play="modal|inline" data-embed="https://…" data-title="…">
 *
 * data-play="modal" (the default) opens the video in a pop-out player on
 * screens from md up; below md, and with data-play="inline", the video plays
 * in place of the poster. The pop-out is one shared <dialog> built on first
 * use; its player is removed on close, which stops the video.
 */
const DESKTOP = window.matchMedia('(min-width: 768px)');

let modal = null;

function buildModal() {
	const dialog = document.createElement('dialog');
	dialog.className = 'video-modal';

	const close = document.createElement('button');
	close.type = 'button';
	close.className = 'video-modal__close';
	close.setAttribute('aria-label', 'Close video');
	close.innerHTML = '<span aria-hidden="true">&times;</span>';
	close.addEventListener('click', () => dialog.close());

	const inner = document.createElement('div');
	inner.className = 'video-modal__inner';

	dialog.append(close, inner);

	// A click on the backdrop lands on the dialog itself; the player fills the dialog, so a click on it never does.
	dialog.addEventListener('click', (event) => {
		if (event.target === dialog) {
			dialog.close();
		}
	});

	dialog.addEventListener('close', () => {
		inner.innerHTML = '';

		// Lenis was stopped while the pop-out was open.
		if (window.cbLenis) {
			window.cbLenis.start();
		}

		if (dialog.opener && document.contains(dialog.opener)) {
			dialog.opener.focus();
		}
	});

	document.body.appendChild(dialog);

	return dialog;
}

function createFrame(facade) {
	const frame = document.createElement('iframe');
	frame.className = 'video-facade__frame';
	frame.src = facade.getAttribute('data-embed');
	frame.title = facade.getAttribute('data-title') || 'Video';
	frame.allow = 'autoplay; fullscreen; picture-in-picture';
	frame.allowFullscreen = true;

	return frame;
}

const warmed = new Set();

/**
 * Open the connection to the video provider as soon as a visitor points at
 * (or tabs to) a poster, so the player is ready sooner when they click.
 */
function warmUp(facade) {
	let origin = '';

	try {
		origin = new URL(facade.getAttribute('data-embed')).origin;
	} catch (e) {
		return;
	}

	if (warmed.has(origin)) {
		return;
	}

	warmed.add(origin);

	const link = document.createElement('link');
	link.rel = 'preconnect';
	link.href = origin;
	document.head.appendChild(link);
}

export function initVideoFacades() {
	['pointerover', 'focusin'].forEach((type) => {
		document.addEventListener(type, (event) => {
			const facade = event.target.closest && event.target.closest('.video-facade');

			if (facade) {
				warmUp(facade);
			}
		});
	});

	document.addEventListener('click', (event) => {
		const facade = event.target.closest('.video-facade');

		if (!facade) {
			return;
		}

		if ('inline' !== facade.getAttribute('data-play') && DESKTOP.matches && 'function' === typeof HTMLDialogElement) {
			modal = modal || buildModal();
			modal.querySelector('.video-modal__inner').replaceChildren(createFrame(facade));
			modal.setAttribute('aria-label', facade.getAttribute('data-title') || 'Video');
			modal.opener = facade;
			modal.showModal();

			// Lenis would keep scrolling the page behind the pop-out.
			if (window.cbLenis) {
				window.cbLenis.stop();
			}

			return;
		}

		const frame = createFrame(facade);

		facade.replaceWith(frame);
		frame.focus();
	});
}
