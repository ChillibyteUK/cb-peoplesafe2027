import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import metadata from '../block.json';

function Edit( { clientId } ) {
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Product cards" textDomain="cb-peoplesafe2027">
			<div className="cb-peoplesafe2027-editor-field">
				<p className="cb-peoplesafe2027-editor-field__help">
					{ __( "A card for every product, with filter buttons for the product types. There is nothing to fill in here: each card uses the product's featured image, title and the intro from its Hero. Put the page title and introduction above it as an ordinary heading and paragraph.", 'cb-peoplesafe2027' ) }
				</p>
			</div>
		</EditorBlockShell>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
