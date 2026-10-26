import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { Button, SelectControl, TextControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import ImagePicker from '../../_shared/ImagePicker';
import RichField from '../../_shared/RichField';
import metadata from '../block.json';

const COLOURS = [
	{ value: 'yellow', label: __( 'Yellow', 'cb-peoplesafe2027' ) },
	{ value: 'pink', label: __( 'Pink', 'cb-peoplesafe2027' ) },
	{ value: 'orange', label: __( 'Orange', 'cb-peoplesafe2027' ) },
	{ value: 'green', label: __( 'Green', 'cb-peoplesafe2027' ) },
	{ value: 'grey', label: __( 'Grey', 'cb-peoplesafe2027' ) },
];

const EMPTY = { year: '', content: '', icon: 0, iconUrl: '', colour: 'yellow' };

function move( rows, index, by ) {
	const next = rows.slice();
	const [ row ] = next.splice( index, 1 );
	next.splice( index + by, 0, row );
	return next;
}

function Edit( { attributes, setAttributes, clientId } ) {
	const rows = attributes.entries || [];
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );

	function update( index, patch ) {
		setAttributes( { entries: rows.map( ( row, i ) => ( i === index ? { ...row, ...patch } : row ) ) } );
	}

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Timeline" textDomain="cb-peoplesafe2027">
			<div className="cb-peoplesafe2027-faq-rows">
				<label className="cb-peoplesafe2027-editor-field__label">{ __( 'Entries, oldest first', 'cb-peoplesafe2027' ) }</label>
				{ rows.map( ( row, index ) => (
					<div className="cb-peoplesafe2027-related-items__row" key={ index }>
						<span className="cb-peoplesafe2027-repeater-field__number">{ index + 1 }</span>
						<div className="cb-peoplesafe2027-faq-rows__fields">
							<TextControl label={ __( 'Year', 'cb-peoplesafe2027' ) } value={ row.year || '' } onChange={ ( value ) => update( index, { year: value } ) } />
							<RichField label={ __( 'Text', 'cb-peoplesafe2027' ) } value={ row.content } onChange={ ( value ) => update( index, { content: value } ) } allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] } />
							<SelectControl label={ __( 'Colour', 'cb-peoplesafe2027' ) } value={ row.colour || 'yellow' } options={ COLOURS } onChange={ ( value ) => update( index, { colour: value } ) } />
							<div className="cb-peoplesafe2027-editor-field">
								<span className="cb-peoplesafe2027-editor-field__label">{ __( 'Icon (optional)', 'cb-peoplesafe2027' ) }</span>
								<ImagePicker id={ row.icon } url={ row.iconUrl } size={ 60 } onSelect={ ( media ) => update( index, { icon: media.id, iconUrl: media.url } ) } onRemove={ () => update( index, { icon: 0, iconUrl: '' } ) } />
							</div>
						</div>
						<div className="cb-peoplesafe2027-repeater-field__row-actions">
							<Button size="small" label={ __( 'Move up', 'cb-peoplesafe2027' ) } disabled={ 0 === index } onClick={ () => setAttributes( { entries: move( rows, index, -1 ) } ) }>&#9650;</Button>
							<Button size="small" label={ __( 'Move down', 'cb-peoplesafe2027' ) } disabled={ index === rows.length - 1 } onClick={ () => setAttributes( { entries: move( rows, index, 1 ) } ) }>&#9660;</Button>
							<Button size="small" isDestructive label={ __( 'Remove', 'cb-peoplesafe2027' ) } onClick={ () => setAttributes( { entries: rows.filter( ( r, i ) => i !== index ) } ) }>&times;</Button>
						</div>
					</div>
				) ) }
				<Button variant="secondary" onClick={ () => setAttributes( { entries: [ ...rows, { ...EMPTY } ] } ) }>
					{ __( 'Add entry', 'cb-peoplesafe2027' ) }
				</Button>
			</div>
		</EditorBlockShell>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
