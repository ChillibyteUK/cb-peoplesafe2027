import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import metadata from '../block.json';

function Edit( { clientId } ) {
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Child page index" textDomain="cb-peoplesafe2027">
			<div className="cb-peoplesafe2027-editor-field">
				<p className="cb-peoplesafe2027-editor-field__help">
					{ __( "A list of the pages directly under this page. It updates itself when child pages are added, moved or renamed, so there is nothing to fill in here.", 'cb-peoplesafe2027' ) }
				</p>
			</div>
		</EditorBlockShell>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
