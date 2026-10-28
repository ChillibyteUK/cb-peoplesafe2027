/**
 * The share dialog's two extras: Copy link (with a short "Link copied" confirmation) and, where the browser has one
 * (most phones), a More options button that opens the device's own share sheet. The network links are plain links.
 */
export function initShare() {
	const dialog = document.getElementById('share-dialog');

	if (!dialog) {
		return;
	}

	const url = dialog.getAttribute('data-share-url') || window.location.href;
	const title = dialog.getAttribute('data-share-title') || document.title;

	const copy = dialog.querySelector('[data-share-copy]');
	const label = dialog.querySelector('[data-share-copy-label]');

	if (copy && label) {
		const original = label.textContent;

		copy.addEventListener('click', async () => {
			let copied = false;

			if (navigator.clipboard) {
				try {
					await navigator.clipboard.writeText(url);
					copied = true;
				} catch (error) {
					copied = false;
				}
			}

			if (!copied) {
				const field = document.createElement('textarea');
				field.value = url;
				field.setAttribute('readonly', '');
				field.style.position = 'absolute';
				field.style.left = '-9999px';
				document.body.appendChild(field);
				field.select();
				try {
					copied = document.execCommand('copy');
				} catch (error) {
					copied = false;
				}
				document.body.removeChild(field);
			}

			if (copied) {
				label.textContent = copy.getAttribute('data-copied') || 'Link copied';
				window.setTimeout(() => {
					label.textContent = original;
				}, 2000);
			}
		});
	}

	const native = dialog.querySelector('[data-share-native]');

	if (native && navigator.share) {
		native.hidden = false;
		native.querySelector('button').addEventListener('click', async () => {
			try {
				await navigator.share({ title, url });
				dialog.close();
			} catch (error) {
				// The sheet was dismissed: not an error.
			}
		});
	}
}
