import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Button, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import SiteUrlInput from '../../_shared/SiteUrlInput';
import ImagePicker from '../../_shared/ImagePicker';
import useRowMotion from '../../_shared/useRowMotion';

const MIN_CARDS = 1;
const MAX_CARDS = 3;
const EMPTY_CARD = { image: 0, imageUrl: '', title: '', buttonLabel: 'Login', link: '', linkTarget: false, colour: 'blue' };

const COLOURS = [
	{ label: __( 'Blue to teal', 'cb-peoplesafe2027' ), value: 'blue' },
	{ label: __( 'Orange to pink', 'cb-peoplesafe2027' ), value: 'pink' },
	{ label: __( 'Yellow to orange', 'cb-peoplesafe2027' ), value: 'orange' },
];

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { cards } = attributes;
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
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Login cards" textDomain="cb-peoplesafe2027">
			<div className="cb-peoplesafe2027-faq-rows">
				<label className="cb-peoplesafe2027-editor-field__label">{ __( 'Cards', 'cb-peoplesafe2027' ) }</label>
				{ rows.map( ( row, index ) => (
					<div
						className={ 'cb-peoplesafe2027-related-items__row' + ( keys[ index ] === movedKey ? ' is-moved' : '' ) }
						key={ keys[ index ] }
						ref={ setRowRef( keys[ index ] ) }
					>
						<span className="cb-peoplesafe2027-repeater-field__number">{ index + 1 }</span>
						<div className="cb-peoplesafe2027-faq-rows__fields">
							<div className="cb-peoplesafe2027-editor-field">
								<span className="cb-peoplesafe2027-editor-field__label">{ __( 'Screenshot', 'cb-peoplesafe2027' ) }</span>
								<ImagePicker
									id={ row.image }
									url={ row.imageUrl }
									size={ 96 }
									onSelect={ ( media ) => updateRow( index, { image: media.id, imageUrl: media.url } ) }
									onRemove={ () => updateRow( index, { image: 0, imageUrl: '' } ) }
								/>
							</div>
							<TextControl label={ __( 'Title', 'cb-peoplesafe2027' ) } value={ row.title || '' } onChange={ ( value ) => updateRow( index, { title: value } ) } />
							<TextControl label={ __( 'Button label', 'cb-peoplesafe2027' ) } value={ row.buttonLabel || '' } onChange={ ( value ) => updateRow( index, { buttonLabel: value } ) } />
							<div className="cb-peoplesafe2027-cta-buttons__url">
								<span className="cb-peoplesafe2027-editor-field__label">{ __( 'Login address', 'cb-peoplesafe2027' ) }</span>
								<SiteUrlInput
									value={ row.link }
									onChange={ ( url ) => updateRow( index, { link: url } ) }
									placeholder={ __( 'Type the login URL', 'cb-peoplesafe2027' ) }
								/>
								<ToggleControl
									label={ __( 'Open in a new tab', 'cb-peoplesafe2027' ) }
									checked={ !! row.linkTarget }
									onChange={ ( value ) => updateRow( index, { linkTarget: value } ) }
									__nextHasNoMarginBottom
								/>
							</div>
							<SelectControl label={ __( 'Colour', 'cb-peoplesafe2027' ) } value={ row.colour || 'blue' } options={ COLOURS } onChange={ ( value ) => updateRow( index, { colour: value } ) } />
						</div>
						<div className="cb-peoplesafe2027-cta-buttons__actions">
							<Button size="small" label={ __( 'Move up', 'cb-peoplesafe2027' ) } onClick={ () => moveRow( index, -1 ) } disabled={ 0 === index }>
								&#9650;
							</Button>
							<Button size="small" label={ __( 'Move down', 'cb-peoplesafe2027' ) } onClick={ () => moveRow( index, 1 ) } disabled={ index === rows.length - 1 }>
								&#9660;
							</Button>
							<Button size="small" isDestructive label={ __( 'Remove', 'cb-peoplesafe2027' ) } onClick={ () => confirmRemove( index ) } disabled={ rows.length <= MIN_CARDS }>
								&times;
							</Button>
						</div>
					</div>
				) ) }
				{ rows.length < MAX_CARDS && (
					<Button variant="secondary" onClick={ () => addRow( EMPTY_CARD ) }>
						{ __( 'Add card', 'cb-peoplesafe2027' ) }
					</Button>
				) }
			</div>
		</EditorBlockShell>
	);
}
