/**
 * Header navigation (markup: inc/nav-render.php, styles: src/css/site-nav.css).
 *
 * Phones and small screens: the burger opens a full-screen list of plain links.
 * Larger screens: a button that has a panel opens it, and a panel with a list on
 * its left shows the content for the list item chosen. Escape or a click
 * elsewhere closes the open panel. On small screens the button is replaced by a
 * link to the section's page (see cb_peoplesafe2027_nav_toggle()).
 */
export function initSiteNav() {
	const header = document.querySelector('.site-header');
	const nav = document.querySelector('.site-nav');
	if (!header || !nav) return;

	// Keep in sync with the max-width in src/css/site-nav.css.
	const desktop = window.matchMedia('(min-width: 1200px)');

	const burger = document.getElementById('site-nav-burger');
	const items = document.getElementById('site-nav-items');
	const toggles = Array.from(nav.querySelectorAll('.site-nav__toggle[data-panel]'));
	const menus = document.getElementById('site-nav-menus');

	function closePanels() {
		toggles.forEach((toggle) => {
			toggle.setAttribute('aria-expanded', 'false');
			document.getElementById(toggle.dataset.panel)?.classList.remove('active');
		});
	}

	function showSubpanel(panel, button) {
		panel.querySelectorAll('.left button').forEach((other) => {
			const on = other === button;
			other.setAttribute('aria-expanded', String(on));
			other.parentElement.classList.toggle('active', on);
			document.getElementById(other.getAttribute('aria-controls'))?.classList.toggle('active', on);
		});
	}

	// Line a panel up under its link, without running off the right of the page.
	function place(panel, toggle) {
		const base = menus.getBoundingClientRect();
		const left = toggle.getBoundingClientRect().left - base.left;
		const room = base.width - panel.offsetWidth;
		panel.style.setProperty('--panel-left', Math.max(0, Math.min(left, room)) + 'px');
	}

	toggles.forEach((toggle) => {
		const panel = document.getElementById(toggle.dataset.panel);
		if (!panel) return;

		function open() {
			if (!desktop.matches) return;
			const wasOpen = toggle.getAttribute('aria-expanded') === 'true';
			closePanels();
			if (wasOpen) return;

			toggle.setAttribute('aria-expanded', 'true');
			panel.classList.add('active');
			place(panel, toggle);

			const first = panel.querySelector('.left button');
			if (first) showSubpanel(panel, first);
		}

		// A button: Enter and Space click it, so one handler does for the mouse and the keyboard.
		toggle.addEventListener('click', open);

		panel.querySelectorAll('.left button').forEach((button) => {
			button.addEventListener('click', () => showSubpanel(panel, button));
			button.addEventListener('mouseenter', () => showSubpanel(panel, button));
		});
	});

	if (burger && items) {
		burger.addEventListener('click', () => {
			const open = burger.getAttribute('aria-expanded') !== 'true';
			burger.setAttribute('aria-expanded', String(open));
			items.classList.toggle('active', open);
		});
	}

	desktop.addEventListener('change', () => {
		closePanels();
		items?.classList.remove('active');
		burger?.setAttribute('aria-expanded', 'false');
	});

	document.addEventListener('click', (event) => {
		if (!event.target.closest('.site-nav__toggle, .site-nav__panel')) closePanels();
	});

	document.addEventListener('keydown', (event) => {
		if (event.key !== 'Escape') return;
		const open = toggles.find((toggle) => toggle.getAttribute('aria-expanded') === 'true');
		closePanels();
		open?.focus();
	});

	// White bar once the page has moved.
	function onScroll() {
		header.classList.toggle('scrolled', window.scrollY > 100);
	}

	onScroll();
	window.addEventListener('scroll', onScroll, { passive: true });
}
