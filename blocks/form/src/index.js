import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { TextControl, TextareaControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import metadata from '../block.json';

function Edit( { attributes, setAttributes, clientId } ) {
	const { title, intro, formUrl, height } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Form" textDomain="cb-peoplesafe2027">
			<TextControl
				label={ __( 'Title', 'cb-peoplesafe2027' ) }
				value={ title }
				onChange={ ( value ) => setAttributes( { title: value } ) }
				help={ __( 'Optional heading above the form.', 'cb-peoplesafe2027' ) }
			/>
			<TextareaControl
				label={ __( 'Introduction', 'cb-peoplesafe2027' ) }
				value={ intro }
				onChange={ ( value ) => setAttributes( { intro: value } ) }
			/>
			<TextControl
				type="url"
				label={ __( 'Form address', 'cb-peoplesafe2027' ) }
				value={ formUrl }
				onChange={ ( value ) => setAttributes( { formUrl: value } ) }
				help={ __( 'The Pardot form\'s address. Leave empty for the Book a Demo form.', 'cb-peoplesafe2027' ) }
			/>
			<TextControl
				type="number"
				label={ __( 'Height in px', 'cb-peoplesafe2027' ) }
				value={ height }
				onChange={ ( value ) => setAttributes( { height: Number( value ) || 1200 } ) }
				help={ __( 'How tall the form area is. Make it taller if the form scrolls inside the page.', 'cb-peoplesafe2027' ) }
			/>
		</EditorBlockShell>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
