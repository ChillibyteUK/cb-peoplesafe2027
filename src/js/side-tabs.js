/**
 * Side tabs: the tabs are native <details> elements sharing a name (one open at
 * a time); this shows the image that goes with the open one, and keeps one tab
 * open (clicking the open tab does not close it).
 *
 * Markup: [data-side-tabs] > .side-tabs__images > .side-tabs__image (one per tab)
 *                          > .side-tabs__tabs   > details.side-tabs__tab (one per tab)
 */
export function initSideTabs() {
	document.querySelectorAll('[data-side-tabs]').forEach((root) => {
		const images = Array.from(root.querySelectorAll('.side-tabs__image'));
		const tabs = Array.from(root.querySelectorAll('.side-tabs__tab'));

		function show() {
			const open = tabs.findIndex((tab) => tab.open);

			images.forEach((image, index) => {
				image.classList.toggle('is-active', index === open);
			});
		}

		tabs.forEach((tab) => {
			tab.addEventListener('toggle', show);
			tab.querySelector('summary').addEventListener('click', (event) => {
				if (tab.open) {
					event.preventDefault();
				}
			});
		});

		show();
	});
}
