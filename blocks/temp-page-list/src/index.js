import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import metadata from '../block.json';

function Edit( { clientId } ) {
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Page list (temporary)" textDomain="cb-peoplesafe2027">
			<div className="cb-peoplesafe2027-editor-field">
				<p className="cb-peoplesafe2027-editor-field__help">
					{ __( "A temporary list of this region's pages and content, shown to logged-in editors only while the site is built. Delete it before launch.", 'cb-peoplesafe2027' ) }
				</p>
			</div>
		</EditorBlockShell>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
