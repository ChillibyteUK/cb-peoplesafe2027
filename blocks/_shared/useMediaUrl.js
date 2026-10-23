import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';

/**
 * The URL to preview a media-library image in a block's editor fields.
 *
 * Blocks store an image's ID (and, when it was picked in the editor, its URL
 * too). Pages built by a script or imported carry only the ID, so the editor
 * looks the image up from its ID when no URL is stored, and shows a thumbnail
 * size where there is one.
 *
 * @param {number} id       Media ID, or 0 for none.
 * @param {string} [stored] URL saved with the block, if any.
 * @return {string} URL to preview, or '' for none.
 */
export default function useMediaUrl( id, stored ) {
	return useSelect(
		( select ) => {
			if ( stored ) {
				return stored;
			}

			if ( ! id ) {
				return '';
			}

			const media = select( coreStore ).getMedia( id );

			if ( ! media ) {
				return '';
			}

			return media.media_details?.sizes?.medium?.source_url || media.source_url || '';
		},
		[ id, stored ]
	);
}
