/**
 * Sector cards: the All / Private sector / Public sector buttons show only the
 * cards whose data-groups include the chosen group. The buttons are hidden
 * until this script runs (without it every card simply shows).
 */
export function initSectorFilters() {
	document.querySelectorAll('.sector-cards').forEach((root) => {
		const bar = root.querySelector('.sector-cards__filters');
		const buttons = Array.from(root.querySelectorAll('.sector-cards__filter'));
		const cards = Array.from(root.querySelectorAll('.sector-cards__card'));

		if (!bar || !buttons.length) {
			return;
		}

		bar.hidden = false;

		buttons.forEach((button) => {
			button.addEventListener('click', () => {
				const filter = button.getAttribute('data-filter');

				buttons.forEach((other) => {
					const on = other === button;

					other.classList.toggle('is-active', on);
					other.setAttribute('aria-pressed', on ? 'true' : 'false');
				});

				cards.forEach((card) => {
					const groups = (card.getAttribute('data-groups') || '').split(' ');

					card.hidden = 'all' !== filter && !groups.includes(filter);
				});
			});
		});
	});
}
