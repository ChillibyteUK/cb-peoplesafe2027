import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { SelectControl, TextControl, TextareaControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

const THEMES = [
	{ value: 'yellow', label: __( 'Yellow', 'cb-peoplesafe2027' ) },
	{ value: 'navy', label: __( 'Navy', 'cb-peoplesafe2027' ) },
	{ value: 'light', label: __( 'Light', 'cb-peoplesafe2027' ) },
];

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { quote, attribution, theme } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Pull quote" textDomain="cb-peoplesafe2027">
			<TextareaControl label={ __( 'Quote', 'cb-peoplesafe2027' ) } value={ quote } rows={ 3 } onChange={ ( value ) => setAttributes( { quote: value } ) } help={ __( 'Type the quote marks if you want them.', 'cb-peoplesafe2027' ) } />
			<TextControl label={ __( 'Attribution', 'cb-peoplesafe2027' ) } value={ attribution } onChange={ ( value ) => setAttributes( { attribution: value } ) } help={ __( 'Who said it, for example a name and job title.', 'cb-peoplesafe2027' ) } />
			<SelectControl label={ __( 'Colour', 'cb-peoplesafe2027' ) } value={ theme || 'yellow' } options={ THEMES } onChange={ ( value ) => setAttributes( { theme: value } ) } />
		</EditorBlockShell>
	);
}
