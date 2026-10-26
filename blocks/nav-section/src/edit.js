import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Button, SelectControl, TextControl } from '@wordpress/components';
import LinkTarget from '../../_shared/LinkTarget';
import ImagePicker from '../../_shared/ImagePicker';

const LAYOUTS = [
	{ value: 'link', label: __( 'Plain link', 'cb-peoplesafe2027' ) },
	{ value: 'button', label: __( 'Button', 'cb-peoplesafe2027' ) },
	{ value: 'list', label: __( 'List of links', 'cb-peoplesafe2027' ) },
	{ value: 'tiles', label: __( 'Tiles', 'cb-peoplesafe2027' ) },
	{ value: 'panels', label: __( 'Panels: a list on the left, content on the right', 'cb-peoplesafe2027' ) },
];

const DISPLAYS = [
	{ value: 'products', label: __( 'Image tiles with text', 'cb-peoplesafe2027' ) },
	{ value: 'cards', label: __( 'Cards with a large image', 'cb-peoplesafe2027' ) },
	{ value: 'cols', label: __( 'Links in two columns', 'cb-peoplesafe2027' ) },
];

const EMPTY_ITEM = { label: '', desc: '', image: 0, imageUrl: '', link: {} };
const EMPTY_PANEL = { title: '', heading: '', intro: '', display: 'products', items: [], more: {} };

function move( rows, index, by ) {
	const next = rows.slice();
	const [ row ] = next.splice( index, 1 );
	next.splice( index + by, 0, row );
	return next;
}

/** Up / down / remove buttons for one row. */
function RowActions( { rows, index, onChange } ) {
	return (
		<div className="cb-peoplesafe2027-repeater-field__row-actions">
			<Button size="small" label={ __( 'Move up', 'cb-peoplesafe2027' ) } disabled={ 0 === index } onClick={ () => onChange( move( rows, index, -1 ) ) }>&#9650;</Button>
			<Button size="small" label={ __( 'Move down', 'cb-peoplesafe2027' ) } disabled={ index === rows.length - 1 } onClick={ () => onChange( move( rows, index, 1 ) ) }>&#9660;</Button>
			<Button
				size="small"
				isDestructive
				label={ __( 'Remove', 'cb-peoplesafe2027' ) }
				onClick={ () => {
					// eslint-disable-next-line no-alert -- a plain confirm() is enough friction for an irreversible remove.
					if ( window.confirm( __( 'Remove this row?', 'cb-peoplesafe2027' ) ) ) {
						onChange( rows.filter( ( row, i ) => i !== index ) );
					}
				} }
			>
				&times;
			</Button>
		</div>
	);
}

/**
 * A list of links. `media` adds an image, `desc` a description.
 * A blank label shows the linked page's own title; a blank image, its featured image.
 */
function ItemList( { label, items, onChange, media = false, desc = false } ) {
	const rows = items || [];

	function update( index, patch ) {
		const next = rows.slice();
		next[ index ] = { ...next[ index ], ...patch };
		onChange( next );
	}

	return (
		<div className="cb-peoplesafe2027-nav-items">
			<label className="cb-peoplesafe2027-editor-field__label">{ label }</label>
			{ rows.map( ( row, index ) => (
				<div className="cb-peoplesafe2027-related-items__row" key={ index }>
					<span className="cb-peoplesafe2027-repeater-field__number">{ index + 1 }</span>
					<div className="cb-peoplesafe2027-faq-rows__fields">
						<LinkTarget label={ __( 'Link', 'cb-peoplesafe2027' ) } value={ row.link } onChange={ ( link ) => update( index, { link } ) } />
						<TextControl
							label={ __( 'Label', 'cb-peoplesafe2027' ) }
							value={ row.label || '' }
							onChange={ ( value ) => update( index, { label: value } ) }
							help={ __( 'Leave blank to use the page\'s own title.', 'cb-peoplesafe2027' ) }
						/>
						{ desc && (
							<TextControl
								label={ __( 'Description', 'cb-peoplesafe2027' ) }
								value={ row.desc || '' }
								onChange={ ( value ) => update( index, { desc: value } ) }
							/>
						) }
						{ media && (
							<ImagePicker
								id={ row.image }
								url={ row.imageUrl }
								size={ 80 }
								onSelect={ ( image ) => update( index, { image: image.id, imageUrl: image.url } ) }
								onRemove={ () => update( index, { image: 0, imageUrl: '' } ) }
							/>
						) }
					</div>
					<RowActions rows={ rows } index={ index } onChange={ onChange } />
				</div>
			) ) }
			<Button variant="secondary" onClick={ () => onChange( [ ...rows, { ...EMPTY_ITEM } ] ) }>
				{ __( 'Add link', 'cb-peoplesafe2027' ) }
			</Button>
		</div>
	);
}

/** The "View all ..." link under a panel. */
function MoreLink( { value, onChange } ) {
	const more = value || {};

	return (
		<div className="cb-peoplesafe2027-nav-more">
			<TextControl
				label={ __( '"View all" link text', 'cb-peoplesafe2027' ) }
				value={ more.text || '' }
				onChange={ ( text ) => onChange( { ...more, text } ) }
				help={ __( 'Optional. Shown under the links.', 'cb-peoplesafe2027' ) }
			/>
			{ more.text && <LinkTarget label={ __( '"View all" link', 'cb-peoplesafe2027' ) } value={ more } onChange={ ( link ) => onChange( { ...link, text: more.text } ) } /> }
		</div>
	);
}

export default function Edit( { attributes, setAttributes } ) {
	const { layout, position, width, link, dialog, icon, items, more, panels } = attributes;
	const blockProps = useBlockProps( { className: 'cb-peoplesafe2027-editor-block' } );
	const hasPanel = [ 'list', 'tiles', 'panels' ].includes( layout );
	const rows = panels || [];

	function updatePanel( index, patch ) {
		const next = rows.slice();
		next[ index ] = { ...next[ index ], ...patch };
		setAttributes( { panels: next } );
	}

	return (
		<div { ...blockProps }>
			<p className="description">
				{ __( 'The post title is the name shown in the navigation. Drag posts in the list to change their order.', 'cb-peoplesafe2027' ) }
			</p>
			<SelectControl label={ __( 'Type', 'cb-peoplesafe2027' ) } value={ layout } options={ LAYOUTS } onChange={ ( value ) => setAttributes( { layout: value } ) } />
			<SelectControl
				label={ __( 'Where', 'cb-peoplesafe2027' ) }
				value={ position }
				options={ [
					{ value: 'main', label: __( 'Lower row, spread beside the logo', 'cb-peoplesafe2027' ) },
					{ value: 'extras', label: __( 'Top row, right-hand side (smaller, grey)', 'cb-peoplesafe2027' ) },
				] }
				onChange={ ( value ) => setAttributes( { position: value } ) }
			/>
			<SelectControl
				label={ __( 'Icon beside the name', 'cb-peoplesafe2027' ) }
				value={ icon }
				options={ [
					{ value: '', label: __( 'None', 'cb-peoplesafe2027' ) },
					{ value: 'lock', label: __( 'Padlock', 'cb-peoplesafe2027' ) },
				] }
				onChange={ ( value ) => setAttributes( { icon: value } ) }
			/>
			<LinkTarget
				label={ hasPanel ? __( 'Heading link', 'cb-peoplesafe2027' ) : __( 'Link', 'cb-peoplesafe2027' ) }
				value={ link }
				onChange={ ( value ) => setAttributes( { link: value } ) }
				showTarget={ ! hasPanel }
			/>
			{ hasPanel && (
				<p className="description">
					{ __( 'Where the heading goes on a phone, and with JavaScript off. On a computer, it opens the panel.', 'cb-peoplesafe2027' ) }
				</p>
			) }
			{ 'button' === layout && (
				<TextControl
					label={ __( 'Dialog to open (optional)', 'cb-peoplesafe2027' ) }
					value={ dialog }
					onChange={ ( value ) => setAttributes( { dialog: value } ) }
					help={ __( 'The id of a dialog on the page. It opens instead of following the link.', 'cb-peoplesafe2027' ) }
				/>
			) }
			{ hasPanel && (
				<TextControl
					type="number"
					label={ __( 'Panel width in px', 'cb-peoplesafe2027' ) }
					value={ width || '' }
					onChange={ ( value ) => setAttributes( { width: Number( value ) || 0 } ) }
					help={ __( 'Leave blank for the standard width.', 'cb-peoplesafe2027' ) }
				/>
			) }
			{ 'list' === layout && <ItemList label={ __( 'Links', 'cb-peoplesafe2027' ) } items={ items } onChange={ ( value ) => setAttributes( { items: value } ) } /> }
			{ 'tiles' === layout && (
				<>
					<ItemList label={ __( 'Tiles', 'cb-peoplesafe2027' ) } items={ items } media desc onChange={ ( value ) => setAttributes( { items: value } ) } />
					<MoreLink value={ more } onChange={ ( value ) => setAttributes( { more: value } ) } />
				</>
			) }
			{ 'panels' === layout && (
				<div className="cb-peoplesafe2027-nav-panels">
					<label className="cb-peoplesafe2027-editor-field__label">{ __( 'Panels', 'cb-peoplesafe2027' ) }</label>
					{ rows.map( ( panel, index ) => (
						<div className="cb-peoplesafe2027-related-items__row" key={ index }>
							<span className="cb-peoplesafe2027-repeater-field__number">{ index + 1 }</span>
							<div className="cb-peoplesafe2027-faq-rows__fields">
								<TextControl
									label={ __( 'Name in the list on the left', 'cb-peoplesafe2027' ) }
									value={ panel.title || '' }
									onChange={ ( value ) => updatePanel( index, { title: value } ) }
								/>
								<SelectControl label={ __( 'Content', 'cb-peoplesafe2027' ) } value={ panel.display } options={ DISPLAYS } onChange={ ( value ) => updatePanel( index, { display: value } ) } />
								{ 'cards' !== panel.display && (
									<>
										<TextControl label={ __( 'Heading', 'cb-peoplesafe2027' ) } value={ panel.heading || '' } onChange={ ( value ) => updatePanel( index, { heading: value } ) } help={ __( 'Optional. Above the links.', 'cb-peoplesafe2027' ) } />
										<TextControl label={ __( 'Intro', 'cb-peoplesafe2027' ) } value={ panel.intro || '' } onChange={ ( value ) => updatePanel( index, { intro: value } ) } />
									</>
								) }
								<ItemList
									label={ __( 'Links', 'cb-peoplesafe2027' ) }
									items={ panel.items }
									media={ 'cols' !== panel.display }
									desc={ 'cols' !== panel.display }
									onChange={ ( value ) => updatePanel( index, { items: value } ) }
								/>
								{ 'cards' !== panel.display && <MoreLink value={ panel.more } onChange={ ( value ) => updatePanel( index, { more: value } ) } /> }
							</div>
							<RowActions rows={ rows } index={ index } onChange={ ( value ) => setAttributes( { panels: value } ) } />
						</div>
					) ) }
					<Button variant="secondary" onClick={ () => setAttributes( { panels: [ ...rows, { ...EMPTY_PANEL } ] } ) }>
						{ __( 'Add panel', 'cb-peoplesafe2027' ) }
					</Button>
				</div>
			) }
		</div>
	);
}
