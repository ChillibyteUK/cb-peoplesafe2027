import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, Button, SelectControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RichField from '../../_shared/RichField';
import CtaButtonsField from '../../_shared/CtaButtonsField';
import ImagePicker from '../../_shared/ImagePicker';
import useRowMotion from '../../_shared/useRowMotion';

const MAX_SLIDES = 5;
const MAX_CARDS = 3;
const EMPTY_SLIDE = { image: 0, imageUrl: '', video: '', cards: [] };
const EMPTY_CARD = { icon: 'tick', tone: 'blue', title: '', text: '' };

const ICONS = [
	{ label: __( 'Tick', 'cb-peoplesafe2027' ), value: 'tick' },
	{ label: __( 'Bell', 'cb-peoplesafe2027' ), value: 'bell' },
	{ label: __( 'Shield', 'cb-peoplesafe2027' ), value: 'shield' },
];

const TONES = [
	{ label: __( 'Blue', 'cb-peoplesafe2027' ), value: 'blue' },
	{ label: __( 'Navy', 'cb-peoplesafe2027' ), value: 'navy' },
	{ label: __( 'Teal', 'cb-peoplesafe2027' ), value: 'teal' },
];

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { title, wordOne, wordTwo, content, buttons, slides, mobileImageUrl } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );
	const rows = slides || [];

	function updateRow( index, patch ) {
		const next = rows.slice();
		next[ index ] = { ...next[ index ], ...patch };
		setAttributes( { slides: next } );
	}

	function updateCard( index, cardIndex, patch ) {
		const cards = ( rows[ index ].cards || [] ).slice();
		cards[ cardIndex ] = { ...cards[ cardIndex ], ...patch };
		updateRow( index, { cards } );
	}

	function removeCard( index, cardIndex ) {
		updateRow( index, { cards: ( rows[ index ].cards || [] ).filter( ( card, i ) => i !== cardIndex ) } );
	}

	const { keys, setRowRef, movedKey, addRow, moveRow, removeRow } = useRowMotion( rows, ( next ) => setAttributes( { slides: next } ) );

	function confirmRemove( index ) {
		// eslint-disable-next-line no-alert -- a plain confirm() is enough friction for an irreversible remove.
		if ( window.confirm( __( 'Remove this slide?', 'cb-peoplesafe2027' ) ) ) {
			removeRow( index );
		}
	}

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Home hero" textDomain="cb-peoplesafe2027">
			<TextControl
				label={ __( 'Title (first line)', 'cb-peoplesafe2027' ) }
				value={ title }
				onChange={ ( value ) => setAttributes( { title: value } ) }
			/>
			<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
				<div style={ { flex: '1 1 0%' } }>
					<TextControl
						label={ __( 'Second line, first word', 'cb-peoplesafe2027' ) }
						value={ wordOne }
						onChange={ ( value ) => setAttributes( { wordOne: value } ) }
						help={ __( 'The two words share the second line of the headline.', 'cb-peoplesafe2027' ) }
					/>
				</div>
				<div style={ { flex: '1 1 0%' } }>
					<TextControl
						label={ __( 'Second line, second word', 'cb-peoplesafe2027' ) }
						value={ wordTwo }
						onChange={ ( value ) => setAttributes( { wordTwo: value } ) }
					/>
				</div>
			</div>
			<RichField
				label={ __( 'Intro', 'cb-peoplesafe2027' ) }
				value={ content }
				onChange={ ( value ) => setAttributes( { content: value } ) }
			/>
			<CtaButtonsField value={ buttons } onChange={ ( rows2 ) => setAttributes( { buttons: rows2 } ) } />
			<div className="cb-peoplesafe2027-faq-rows">
				<label className="cb-peoplesafe2027-editor-field__label">{ __( 'Slides (up to 5)', 'cb-peoplesafe2027' ) }</label>
				<p className="cb-peoplesafe2027-editor-field__help">
					{ __( 'The photos fade from one to the next. Each photo can carry up to three event cards that pop up on it, one after another, on large screens. Alt text comes from the media library.', 'cb-peoplesafe2027' ) }
					{ ! rows.length && mobileImageUrl ? ' ' + __( 'Until a slide is added, the image from before slideshows is shown.', 'cb-peoplesafe2027' ) : '' }
				</p>
				{ rows.map( ( row, index ) => (
					<div
						className={ 'cb-peoplesafe2027-related-items__row' + ( keys[ index ] === movedKey ? ' is-moved' : '' ) }
						key={ keys[ index ] }
						ref={ setRowRef( keys[ index ] ) }
					>
						<span className="cb-peoplesafe2027-repeater-field__number">{ index + 1 }</span>
						<div className="cb-peoplesafe2027-faq-rows__fields">
							<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
								<div className="cb-peoplesafe2027-editor-field" style={ { flex: '1 1 0%' } }>
									<span className="cb-peoplesafe2027-editor-field__label">{ __( 'Photo', 'cb-peoplesafe2027' ) }</span>
									<ImagePicker
										id={ row.image }
										url={ row.imageUrl }
										onSelect={ ( media ) => updateRow( index, { image: media.id, imageUrl: media.url } ) }
										onRemove={ () => updateRow( index, { image: 0, imageUrl: '' } ) }
									/>
								</div>
								<div style={ { flex: '1 1 0%' } }>
									<TextControl
										label={ __( 'Vimeo video (optional)', 'cb-peoplesafe2027' ) }
										value={ row.video || '' }
										onChange={ ( value ) => updateRow( index, { video: value } ) }
										help={ __( 'A Vimeo ID (e.g. 1225646327) or link. If set, it plays in place of the photo: muted, looping and without cookies.', 'cb-peoplesafe2027' ) }
									/>
								</div>
							</div>
							<div className="cb-peoplesafe2027-editor-field">
								<span className="cb-peoplesafe2027-editor-field__label">{ __( 'Event cards on this photo (up to 3)', 'cb-peoplesafe2027' ) }</span>
								{ ( row.cards || [] ).map( ( card, cardIndex ) => (
									<div key={ cardIndex } style={ { border: '1px solid #ddd', borderRadius: '4px', padding: '8px', marginBottom: '8px' } }>
										<div style={ { display: 'flex', flexWrap: 'wrap', gap: '8px' } }>
											<div style={ { flex: '1 1 0%' } }>
												<SelectControl
													label={ __( 'Icon', 'cb-peoplesafe2027' ) }
													value={ card.icon || 'tick' }
													options={ ICONS }
													onChange={ ( value ) => updateCard( index, cardIndex, { icon: value } ) }
												/>
											</div>
											<div style={ { flex: '1 1 0%' } }>
												<SelectControl
													label={ __( 'Colour', 'cb-peoplesafe2027' ) }
													value={ card.tone || 'blue' }
													options={ TONES }
													onChange={ ( value ) => updateCard( index, cardIndex, { tone: value } ) }
												/>
											</div>
										</div>
										<TextControl
											label={ __( 'Title', 'cb-peoplesafe2027' ) }
											value={ card.title || '' }
											onChange={ ( value ) => updateCard( index, cardIndex, { title: value } ) }
										/>
										<TextControl
											label={ __( 'Small text', 'cb-peoplesafe2027' ) }
											value={ card.text || '' }
											onChange={ ( value ) => updateCard( index, cardIndex, { text: value } ) }
										/>
										<Button size="small" isDestructive variant="link" onClick={ () => removeCard( index, cardIndex ) }>
											{ __( 'Remove card', 'cb-peoplesafe2027' ) }
										</Button>
									</div>
								) ) }
								{ ( row.cards || [] ).length < MAX_CARDS && (
									<Button variant="secondary" size="small" onClick={ () => updateRow( index, { cards: [ ...( row.cards || [] ), EMPTY_CARD ] } ) }>
										{ __( 'Add event card', 'cb-peoplesafe2027' ) }
									</Button>
								) }
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
				{ rows.length < MAX_SLIDES && (
					<Button variant="secondary" onClick={ () => addRow( EMPTY_SLIDE ) }>
						{ __( 'Add slide', 'cb-peoplesafe2027' ) }
					</Button>
				) }
			</div>
		</EditorBlockShell>
	);
}
