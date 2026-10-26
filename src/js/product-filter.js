/**
 * Product cards: the type buttons show only the cards whose data-types include
 * the chosen type. The buttons are hidden until this script runs (without it
 * every card simply shows). ?q=type-slug opens with that type chosen.
 */
export function initProductFilters() {
	document.querySelectorAll('.product-cards').forEach((root) => {
		const bar = root.querySelector('.product-cards__filters');
		const buttons = Array.from(root.querySelectorAll('.product-cards__filter'));
		const cards = Array.from(root.querySelectorAll('.product-cards__card'));

		if (!bar || !buttons.length) {
			return;
		}

		function choose(filter) {
			buttons.forEach((button) => {
				const on = button.getAttribute('data-filter') === filter;

				button.classList.toggle('is-active', on);
				button.setAttribute('aria-pressed', on ? 'true' : 'false');
			});

			cards.forEach((card) => {
				card.hidden = 'all' !== filter && !(card.getAttribute('data-types') || '').split(' ').includes(filter);
			});
		}

		bar.hidden = false;
		buttons.forEach((button) => button.addEventListener('click', () => choose(button.getAttribute('data-filter'))));

		const wanted = new URLSearchParams(window.location.search).get('q');

		if (wanted && buttons.some((button) => button.getAttribute('data-filter') === wanted)) {
			choose(wanted);
		}
	});
}
