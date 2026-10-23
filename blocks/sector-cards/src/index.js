import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import metadata from '../block.json';

function Edit( { clientId } ) {
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Sector cards" textDomain="cb-peoplesafe2027">
			<div className="cb-peoplesafe2027-editor-field">
				<p className="cb-peoplesafe2027-editor-field__help">
					{ __( "A card for every page under this page, in page order, with Public and Private filter buttons. There is nothing to fill in here: each card uses the page's featured image and title. Set a sector's group (public, private or both) in that page's sidebar.", 'cb-peoplesafe2027' ) }
				</p>
			</div>
		</EditorBlockShell>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
