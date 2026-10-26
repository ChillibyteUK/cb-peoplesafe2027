import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { SelectControl, TextControl, TextareaControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import FilePicker from '../../_shared/FilePicker';
import metadata from '../block.json';

function Edit( { attributes, setAttributes, clientId } ) {
	const { kind, title, code, file, fileName } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Embed" textDomain="cb-peoplesafe2027">
			<SelectControl
				label={ __( 'What to show', 'cb-peoplesafe2027' ) }
				value={ kind }
				options={ [
					{ value: 'turtl', label: __( 'A Turtl document', 'cb-peoplesafe2027' ) },
					{ value: 'pdf', label: __( 'A PDF', 'cb-peoplesafe2027' ) },
				] }
				onChange={ ( value ) => setAttributes( { kind: value } ) }
			/>
			<TextControl label={ __( 'Title', 'cb-peoplesafe2027' ) } value={ title } onChange={ ( value ) => setAttributes( { title: value } ) } help={ __( 'Optional heading above it.', 'cb-peoplesafe2027' ) } />
			{ 'turtl' === kind ? (
				<TextareaControl
					label={ __( 'Turtl embed code', 'cb-peoplesafe2027' ) }
					rows={ 6 }
					value={ code }
					onChange={ ( value ) => setAttributes( { code: value } ) }
					help={ __( 'Paste the embed code from Turtl (the <a class="turtl-embed"> link). The Turtl script is added by the theme.', 'cb-peoplesafe2027' ) }
				/>
			) : (
				<div className="cb-peoplesafe2027-editor-field">
					<span className="cb-peoplesafe2027-editor-field__label">{ __( 'PDF', 'cb-peoplesafe2027' ) }</span>
					<FilePicker
						id={ file }
						name={ fileName }
						mimeTypes={ [ 'application/pdf' ] }
						onSelect={ ( media ) => setAttributes( { file: media.id, fileName: media.filename || media.title || '' } ) }
						onRemove={ () => setAttributes( { file: 0, fileName: '' } ) }
					/>
				</div>
			) }
		</EditorBlockShell>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
