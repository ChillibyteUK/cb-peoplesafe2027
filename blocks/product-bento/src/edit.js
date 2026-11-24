import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, SelectControl, Button } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RichField from '../../_shared/RichField';
import CtaButtonsField from '../../_shared/CtaButtonsField';
import SiteUrlInput from '../../_shared/SiteUrlInput';
import ImagePicker from '../../_shared/ImagePicker';
import useRowMotion from '../../_shared/useRowMotion';

const MAX_CARDS = 6;
const SIZES = [
	{ label: __( 'Half of the row', 'cb-peoplesafe2027' ), value: 'half' },
	{ label: __( 'A third of the row', 'cb-peoplesafe2027' ), value: 'third' },
];

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { title, intro, buttons, cards } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );
	const rows = cards || [];

	function updateRow( index, patch ) {
		const next = rows.slice();
		next[ index ] = { ...next[ index ], ...patch };
		setAttributes( { cards: next } );
	}

	const { keys, setRowRef, movedKey, addRow, moveRow, removeRow } = useRowMotion( rows, ( next ) => setAttributes( { cards: next } ) );

	function confirmRemove( index ) {
		// eslint-disable-next-line no-alert -- a plain confirm() is enough friction for an irreversible remove.
		if ( window.confirm( __( 'Remove this card?', 'cb-peoplesafe2027' ) ) ) {
			removeRow( index );
		}
	}

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Product bento" textDomain="cb-peoplesafe2027">
			<TextControl
				label={ __( 'Title', 'cb-peoplesafe2027' ) }
				value={ title }
				onChange={ ( value ) => setAttributes( { title: value } ) }
			/>
			<RichField
				label={ __( 'Intro', 'cb-peoplesafe2027' ) }
				value={ intro }
				onChange={ ( value ) => setAttributes( { intro: value } ) }
			/>
			<CtaButtonsField value={ buttons } onChange={ ( next ) => setAttributes( { buttons: next } ) } />
			<div className="cb-peoplesafe2027-faq-rows">
				<label className="cb-peoplesafe2027-editor-field__label">{ __( 'Cards (up to 6)', 'cb-peoplesafe2027' ) }</label>
				<p className="cb-peoplesafe2027-editor-field__help">
					{ __( 'On large screens a half card takes half of a row and a third card a third, so two halves, or three thirds, make a full row. On smaller screens the cards stack. Images are shown whole, never cropped.', 'cb-peoplesafe2027' ) }
				</p>
				{ rows.map( ( row, index ) => (
					<div
						className={ 'cb-peoplesafe2027-related-items__row' + ( keys[ index ] === movedKey ? ' is-moved' : '' ) }
						key={ keys[ index ] }
						ref={ setRowRef( keys[ index ] ) }
					>
						<span className="cb-peoplesafe2027-repeater-field__number">{ index + 1 }</span>
						<div className="cb-peoplesafe2027-faq-rows__fields">
							<RichField
								label={ __( 'Statement', 'cb-peoplesafe2027' ) }
								value={ row.title || '' }
								allowedFormats={ [ 'core/bold', 'core/italic' ] }
								onChange={ ( value ) => updateRow( index, { title: value } ) }
							/>
							<p className="cb-peoplesafe2027-editor-field__help">
								{ __( 'Make the first few words bold: they show dark, and the rest shows lighter.', 'cb-peoplesafe2027' ) }
							</p>
							<div className="cb-peoplesafe2027-editor-field">
								<span className="cb-peoplesafe2027-editor-field__label">{ __( 'Image', 'cb-peoplesafe2027' ) }</span>
								<ImagePicker
									id={ row.image }
									url={ row.imageUrl }
									onSelect={ ( media ) => updateRow( index, { image: media.id, imageUrl: media.url } ) }
									onRemove={ () => updateRow( index, { image: 0, imageUrl: '' } ) }
								/>
							</div>
							<SelectControl
								label={ __( 'Width', 'cb-peoplesafe2027' ) }
								value={ row.size || ( index < 2 ? 'half' : 'third' ) }
								options={ SIZES }
								onChange={ ( value ) => updateRow( index, { size: value } ) }
							/>
							<div className="cb-peoplesafe2027-editor-field">
								<span className="cb-peoplesafe2027-editor-field__label">{ __( 'Link (optional)', 'cb-peoplesafe2027' ) }</span>
								<SiteUrlInput
									value={ row.link || '' }
									onChange={ ( url ) => updateRow( index, { link: url } ) }
									placeholder={ __( 'Search pages and posts, or type a URL', 'cb-peoplesafe2027' ) }
								/>
								<p className="cb-peoplesafe2027-editor-field__help">
									{ __( 'Makes the whole card a link and adds an arrow in its corner.', 'cb-peoplesafe2027' ) }
								</p>
							</div>
						</div>
						<div className="cb-peoplesafe2027-cta-buttons__actions">
							<Button size="small" label={ __( 'Move up', 'cb-peoplesafe2027' ) } onClick={ () => moveRow( index, -1 ) } disabled={ 0 === index }>
								&#9650;
							</Button>
							<Button size="small" label={ __( 'Move down', 'cb-peoplesafe2027' ) } onClick={ () => moveRow( index, 1 ) } disabled={ index === rows.length - 1 }>
								&#9660;
							</Button>
							<Button size="small" isDestructive label={ __( 'Remove', 'cb-peoplesafe2027' ) } onClick={ () => confirmRemove( index ) }>
								&times;
							</Button>
						</div>
					</div>
				) ) }
				{ rows.length < MAX_CARDS && (
					<Button
						variant="secondary"
						onClick={ () => addRow( { title: '', image: 0, imageUrl: '', size: rows.length < 2 ? 'half' : 'third', link: '' } ) }
					>
						{ __( 'Add card', 'cb-peoplesafe2027' ) }
					</Button>
				) }
			</div>
		</EditorBlockShell>
	);
}
