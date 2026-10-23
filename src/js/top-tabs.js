/**
 * Top tabs: the pills are buttons, each controlling one panel. Showing a tab
 * hides the others; clicking the open tab leaves it open.
 *
 * Markup: [data-top-tabs] > button.top-tabs__pill[aria-controls] + .top-tabs__panel (per tab)
 */
export function initTopTabs() {
	document.querySelectorAll('[data-top-tabs]').forEach((root) => {
		const pills = Array.from(root.querySelectorAll('.top-tabs__pill'));

		function show(selected) {
			pills.forEach((pill) => {
				const open = pill === selected;
				const panel = document.getElementById(pill.getAttribute('aria-controls'));

				pill.classList.toggle('is-active', open);
				pill.setAttribute('aria-expanded', open ? 'true' : 'false');

				if (panel) {
					panel.classList.toggle('is-active', open);
					panel.hidden = !open;
				}
			});
		}

		pills.forEach((pill) => {
			pill.addEventListener('click', () => show(pill));
		});
	});
}
