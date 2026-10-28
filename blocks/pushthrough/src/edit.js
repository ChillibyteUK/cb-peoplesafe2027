import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, TextareaControl, SelectControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RichField from '../../_shared/RichField';
import CtaButtonsField from '../../_shared/CtaButtonsField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { layout, theme, label, title, content, facts, buttons } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Pushthrough" textDomain="cb-peoplesafe2027">
			<SelectControl
				label={ __( 'Layout', 'cb-peoplesafe2027' ) }
				value={ layout }
				options={ [
					{ label: __( 'Panel: label, title, text, facts and a button, stacked', 'cb-peoplesafe2027' ), value: 'panel' },
					{ label: __( 'Strip: title and text beside a button', 'cb-peoplesafe2027' ), value: 'strip' },
				] }
				onChange={ ( value ) => setAttributes( { layout: value } ) }
			/>
			<SelectControl
				label={ __( 'Colour', 'cb-peoplesafe2027' ) }
				value={ theme }
				options={ [
					{ label: __( 'Navy', 'cb-peoplesafe2027' ), value: 'navy' },
					{ label: __( 'Teal', 'cb-peoplesafe2027' ), value: 'teal' },
					{ label: __( 'Orange', 'cb-peoplesafe2027' ), value: 'orange' },
					{ label: __( 'Light', 'cb-peoplesafe2027' ), value: 'light' },
				] }
				onChange={ ( value ) => setAttributes( { theme: value } ) }
			/>
			{ 'panel' === layout && (
				<TextControl
					label={ __( 'Label (optional)', 'cb-peoplesafe2027' ) }
					value={ label }
					onChange={ ( value ) => setAttributes( { label: value } ) }
					help={ __( 'The small line above the title, for example "Readiness checklist".', 'cb-peoplesafe2027' ) }
				/>
			) }
			<TextControl label={ __( 'Title', 'cb-peoplesafe2027' ) } value={ title } onChange={ ( value ) => setAttributes( { title: value } ) } />
			<RichField label={ __( 'Text', 'cb-peoplesafe2027' ) } value={ content } onChange={ ( value ) => setAttributes( { content: value } ) } />
			{ 'panel' === layout && (
				<TextareaControl
					label={ __( 'Short facts (optional)', 'cb-peoplesafe2027' ) }
					value={ facts }
					onChange={ ( value ) => setAttributes( { facts: value } ) }
					help={ __( 'One per line, for example "About 3 minutes". They show as a row of small points.', 'cb-peoplesafe2027' ) }
				/>
			) }
			<CtaButtonsField value={ buttons } onChange={ ( rows ) => setAttributes( { buttons: rows } ) } />
		</EditorBlockShell>
	);
}
