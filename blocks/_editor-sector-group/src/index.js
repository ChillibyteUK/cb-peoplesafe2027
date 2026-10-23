import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { SelectControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useEntityProp, store as coreStore } from '@wordpress/core-data';

/**
 * "Sector group" in the page sidebar, for the pages directly under the Sectors
 * page only: whether the sector is public, private or both, which the filter
 * buttons on the Sectors page use. Stored in the page's cb_sector_group meta.
 */
function SectorGroupPanel() {
	const { postType, parentId } = useSelect( ( select ) => {
		const editor = select( 'core/editor' );

		return {
			postType: editor.getCurrentPostType(),
			parentId: editor.getEditedPostAttribute( 'parent' ),
		};
	}, [] );

	const parent = useSelect(
		( select ) => ( parentId ? select( coreStore ).getEntityRecord( 'postType', 'page', parentId ) : null ),
		[ parentId ]
	);

	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

	// Only a page whose parent is the top-level Sectors page.
	if ( 'page' !== postType || ! parent || 'sectors' !== parent.slug || parent.parent ) {
		return null;
	}

	return (
		<PluginDocumentSettingPanel name="cb-peoplesafe2027-sector-group" title={ __( 'Sector group', 'cb-peoplesafe2027' ) }>
			<SelectControl
				label={ __( 'This sector is for', 'cb-peoplesafe2027' ) }
				value={ ( meta && meta.cb_sector_group ) || 'public' }
				options={ [
					{ label: __( 'Public sector', 'cb-peoplesafe2027' ), value: 'public' },
					{ label: __( 'Private sector', 'cb-peoplesafe2027' ), value: 'private' },
					{ label: __( 'Both', 'cb-peoplesafe2027' ), value: 'both' },
				] }
				onChange={ ( value ) => setMeta( { ...meta, cb_sector_group: value } ) }
				help={ __( 'Used by the Public / Private filter on the Sectors page.', 'cb-peoplesafe2027' ) }
			/>
		</PluginDocumentSettingPanel>
	);
}

registerPlugin( 'cb-peoplesafe2027-sector-group', { render: SectorGroupPanel } );
