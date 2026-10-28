import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { TextControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import FilePicker from '../../_shared/FilePicker';
import metadata from '../block.json';

function Edit( { attributes, setAttributes, clientId } ) {
	const { file, fileName, buttonLabel } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Download panel" textDomain="cb-peoplesafe2027">
			<div className="cb-peoplesafe2027-editor-field">
				<span className="cb-peoplesafe2027-editor-field__label">{ __( 'File', 'cb-peoplesafe2027' ) }</span>
				<FilePicker
					id={ file }
					name={ fileName }
					onSelect={ ( media ) => setAttributes( { file: media.id, fileName: media.filename || media.title || '' } ) }
					onRemove={ () => setAttributes( { file: 0, fileName: '' } ) }
				/>
			</div>
			<TextControl label={ __( 'Button text', 'cb-peoplesafe2027' ) } value={ buttonLabel } onChange={ ( value ) => setAttributes( { buttonLabel: value } ) } help={ __( 'For example "Download the full case study". Leave empty for "Download".', 'cb-peoplesafe2027' ) } />
		</EditorBlockShell>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
