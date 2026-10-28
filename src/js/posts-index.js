/**
 * The Blogs and News listings (archive.php): the search box, the category pills and Load more all ask cb/v1/posts for
 * cards (inc/posts-index.php draws them, so the page and the batches match). The pills are ordinary links without
 * JavaScript; here they filter in place and keep the address in step. The numbered pages are only for visitors without
 * JavaScript, so they are hidden once this runs.
 */
export function initPostsIndex() {
	document.querySelectorAll('[data-posts-index]').forEach((root) => {
		const grid = root.querySelector('[data-posts-grid]');
		const more = root.querySelector('[data-posts-more]');
		const search = root.querySelector('[data-posts-search]');
		const status = root.querySelector('[data-posts-status]');
		const empty = root.querySelector('[data-posts-empty]');
		const pagination = root.querySelector('[data-posts-pagination]');
		const filters = Array.from(root.querySelectorAll('[data-posts-filter]'));
		const endpoint = root.getAttribute('data-endpoint');
		// The Knowledge Hub: the grid is only for search results, and the rest of the page steps aside while searching.
		const searchOnly = root.hasAttribute('data-posts-search-only');
		const hideSelector = root.getAttribute('data-posts-hide');

		if (!grid || !more || !endpoint) {
			return;
		}

		const state = {
			query: '',
			category: root.getAttribute('data-category') || '',
			loaded: parseInt(root.getAttribute('data-loaded') || '0', 10),
			total: parseInt(root.getAttribute('data-total') || '0', 10),
		};

		let request = null;
		let timer = null;

		if (pagination) {
			pagination.hidden = true;
		}

		const syncMore = () => {
			more.hidden = state.loaded >= state.total;
			more.disabled = false;
		};

		const setStatus = () => {
			if (empty) {
				empty.hidden = !(state.query && 0 === state.total);
			}

			if (status) {
				status.textContent = state.query
					? 0 === state.total
						? 'No articles found.'
						: `${state.total} ${1 === state.total ? 'article' : 'articles'} found`
					: '';
			}
		};

		const showResults = (searching) => {
			if (!searchOnly) {
				return;
			}

			grid.hidden = !searching;

			if (hideSelector) {
				document.querySelectorAll(hideSelector).forEach((element) => {
					element.hidden = searching;
				});
			}
		};

		const load = (replace) => {
			request?.abort();

			if (searchOnly && !state.query) {
				// Search cleared: back to the page.
				grid.innerHTML = '';
				state.loaded = 0;
				state.total = 0;
				showResults(false);
				setStatus();
				syncMore();
				return;
			}

			request = new AbortController();

			const params = new URLSearchParams({
				stream: root.getAttribute('data-stream') || 'blogs',
				region: root.getAttribute('data-region') || '',
				category: state.category,
				q: state.query,
				offset: replace ? 0 : state.loaded,
				lead: !state.query && replace ? 1 : 0,
			});

			grid.setAttribute('aria-busy', 'true');
			more.disabled = true;

			fetch(`${endpoint}?${params.toString()}`, { signal: request.signal })
				.then((response) => (response.ok ? response.json() : Promise.reject(new Error(response.status))))
				.then((data) => {
					if (replace) {
						grid.innerHTML = data.html;
						state.loaded = data.count;
					} else {
						grid.insertAdjacentHTML('beforeend', data.html);
						state.loaded += data.count;
					}

					state.total = data.total;
					showResults(true);
					grid.removeAttribute('aria-busy');
					setStatus();
					syncMore();
				})
				.catch((error) => {
					if ('AbortError' !== error.name) {
						grid.removeAttribute('aria-busy');
						more.disabled = false;
					}
				});
		};

		more.addEventListener('click', () => load(false));

		if (search) {
			search.addEventListener('input', () => {
				clearTimeout(timer);
				timer = setTimeout(() => {
					const value = search.value.trim();
					state.query = value.length >= 2 ? value : '';
					load(true);
				}, 250);
			});
		}

		filters.forEach((link) => {
			link.addEventListener('click', (event) => {
				event.preventDefault();
				state.category = link.getAttribute('data-category') || '';

				filters.forEach((other) => {
					const on = other === link;
					other.classList.toggle('is-active', on);
					other.toggleAttribute('aria-current', on);
				});

				window.history.replaceState(null, '', link.getAttribute('href'));
				load(true);
			});
		});
	});
}
