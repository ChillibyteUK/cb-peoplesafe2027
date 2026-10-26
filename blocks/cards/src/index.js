import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { Button, SelectControl, TextControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import ImagePicker from '../../_shared/ImagePicker';
import RichField from '../../_shared/RichField';
import SiteUrlInput from '../../_shared/SiteUrlInput';
import metadata from '../block.json';

const COLOURS = [
	{ value: 'white', label: __( 'White', 'cb-peoplesafe2027' ) },
	{ value: 'yellow', label: __( 'Yellow', 'cb-peoplesafe2027' ) },
	{ value: 'green', label: __( 'Green', 'cb-peoplesafe2027' ) },
	{ value: 'red', label: __( 'Red', 'cb-peoplesafe2027' ) },
	{ value: 'blue', label: __( 'Blue', 'cb-peoplesafe2027' ) },
];

const EMPTY = { image: 0, imageUrl: '', title: '', content: '', linkText: '', link: '', linkTarget: false, colour: 'white' };

function move( rows, index, by ) {
	const next = rows.slice();
	const [ row ] = next.splice( index, 1 );
	next.splice( index + by, 0, row );
	return next;
}

function Edit( { attributes, setAttributes, clientId } ) {
	const { title } = attributes;
	const rows = attributes.cards || [];
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );

	function update( index, patch ) {
		setAttributes( { cards: rows.map( ( row, i ) => ( i === index ? { ...row, ...patch } : row ) ) } );
	}

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Cards" textDomain="cb-peoplesafe2027">
			<TextControl label={ __( 'Title', 'cb-peoplesafe2027' ) } value={ title } onChange={ ( value ) => setAttributes( { title: value } ) } help={ __( 'Optional heading above the cards.', 'cb-peoplesafe2027' ) } />
			<div className="cb-peoplesafe2027-faq-rows">
				<label className="cb-peoplesafe2027-editor-field__label">{ __( 'Cards (2 to 4)', 'cb-peoplesafe2027' ) }</label>
				{ rows.map( ( row, index ) => (
					<div className="cb-peoplesafe2027-related-items__row" key={ index }>
						<span className="cb-peoplesafe2027-repeater-field__number">{ index + 1 }</span>
						<div className="cb-peoplesafe2027-faq-rows__fields">
							<div className="cb-peoplesafe2027-editor-field">
								<span className="cb-peoplesafe2027-editor-field__label">{ __( 'Image', 'cb-peoplesafe2027' ) }</span>
								<ImagePicker id={ row.image } url={ row.imageUrl } onSelect={ ( media ) => update( index, { image: media.id, imageUrl: media.url } ) } onRemove={ () => update( index, { image: 0, imageUrl: '' } ) } />
							</div>
							<TextControl label={ __( 'Title', 'cb-peoplesafe2027' ) } value={ row.title || '' } onChange={ ( value ) => update( index, { title: value } ) } />
							<RichField label={ __( 'Text or list', 'cb-peoplesafe2027' ) } value={ row.content } onChange={ ( value ) => update( index, { content: value } ) } />
							<SelectControl label={ __( 'Colour', 'cb-peoplesafe2027' ) } value={ row.colour || 'white' } options={ COLOURS } onChange={ ( value ) => update( index, { colour: value } ) } />
							<TextControl label={ __( 'Link text (optional)', 'cb-peoplesafe2027' ) } value={ row.linkText || '' } onChange={ ( value ) => update( index, { linkText: value } ) } />
							<div className="cb-peoplesafe2027-editor-field">
								<span className="cb-peoplesafe2027-editor-field__label">{ __( 'Link URL', 'cb-peoplesafe2027' ) }</span>
								<SiteUrlInput value={ row.link } onChange={ ( url ) => update( index, { link: url } ) } placeholder={ __( 'Search pages and posts, or type a URL', 'cb-peoplesafe2027' ) } />
							</div>
						</div>
						<div className="cb-peoplesafe2027-repeater-field__row-actions">
							<Button size="small" label={ __( 'Move up', 'cb-peoplesafe2027' ) } disabled={ 0 === index } onClick={ () => setAttributes( { cards: move( rows, index, -1 ) } ) }>&#9650;</Button>
							<Button size="small" label={ __( 'Move down', 'cb-peoplesafe2027' ) } disabled={ index === rows.length - 1 } onClick={ () => setAttributes( { cards: move( rows, index, 1 ) } ) }>&#9660;</Button>
							<Button size="small" isDestructive label={ __( 'Remove', 'cb-peoplesafe2027' ) } disabled={ rows.length <= 2 } onClick={ () => setAttributes( { cards: rows.filter( ( r, i ) => i !== index ) } ) }>&times;</Button>
						</div>
					</div>
				) ) }
				{ rows.length < 4 && (
					<Button variant="secondary" onClick={ () => setAttributes( { cards: [ ...rows, { ...EMPTY } ] } ) }>
						{ __( 'Add card', 'cb-peoplesafe2027' ) }
					</Button>
				) }
			</div>
		</EditorBlockShell>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
