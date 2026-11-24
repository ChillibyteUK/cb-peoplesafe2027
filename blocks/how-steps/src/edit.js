import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl, TextareaControl, Button } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RichField from '../../_shared/RichField';
import SiteUrlInput from '../../_shared/SiteUrlInput';
import ImagePicker from '../../_shared/ImagePicker';
import useRowMotion from '../../_shared/useRowMotion';

const MAX_STEPS = 4;
const EMPTY_STEP = { image: 0, imageUrl: '', title: '', text: '' };

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { title, intro, videoUrl, videoLabel, steps } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );
	const rows = steps || [];

	function updateRow( index, patch ) {
		const next = rows.slice();
		next[ index ] = { ...next[ index ], ...patch };
		setAttributes( { steps: next } );
	}

	const { keys, setRowRef, movedKey, addRow, moveRow, removeRow } = useRowMotion( rows, ( next ) => setAttributes( { steps: next } ) );

	function confirmRemove( index ) {
		// eslint-disable-next-line no-alert -- a plain confirm() is enough friction for an irreversible remove.
		if ( window.confirm( __( 'Remove this step?', 'cb-peoplesafe2027' ) ) ) {
			removeRow( index );
		}
	}

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="How steps" textDomain="cb-peoplesafe2027">
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
			<div className="cb-peoplesafe2027-editor-field">
				<span className="cb-peoplesafe2027-editor-field__label">{ __( 'Video link', 'cb-peoplesafe2027' ) }</span>
				<SiteUrlInput
					value={ videoUrl }
					onChange={ ( url ) => setAttributes( { videoUrl: url } ) }
					placeholder={ __( 'Paste a YouTube or Vimeo link', 'cb-peoplesafe2027' ) }
				/>
				<p className="cb-peoplesafe2027-editor-field__help">
					{ __( 'Adds a Watch video button beside the title that opens the video in a pop-out. Leave empty for no button.', 'cb-peoplesafe2027' ) }
				</p>
			</div>
			<TextControl
				label={ __( 'Video button label', 'cb-peoplesafe2027' ) }
				value={ videoLabel }
				onChange={ ( value ) => setAttributes( { videoLabel: value } ) }
				help={ __( 'Leave empty for "Watch video".', 'cb-peoplesafe2027' ) }
			/>
			<div className="cb-peoplesafe2027-faq-rows">
				<label className="cb-peoplesafe2027-editor-field__label">{ __( 'Steps (up to 4)', 'cb-peoplesafe2027' ) }</label>
				<p className="cb-peoplesafe2027-editor-field__help">
					{ __( 'Numbered in this order. On large screens the numbers sit on a timeline above the cards and the cards appear one after another. Alt text comes from the media library.', 'cb-peoplesafe2027' ) }
				</p>
				{ rows.map( ( row, index ) => (
					<div
						className={ 'cb-peoplesafe2027-related-items__row' + ( keys[ index ] === movedKey ? ' is-moved' : '' ) }
						key={ keys[ index ] }
						ref={ setRowRef( keys[ index ] ) }
					>
						<span className="cb-peoplesafe2027-repeater-field__number">{ index + 1 }</span>
						<div className="cb-peoplesafe2027-faq-rows__fields">
							<div className="cb-peoplesafe2027-editor-field">
								<span className="cb-peoplesafe2027-editor-field__label">{ __( 'Image', 'cb-peoplesafe2027' ) }</span>
								<ImagePicker
									id={ row.image }
									url={ row.imageUrl }
									onSelect={ ( media ) => updateRow( index, { image: media.id, imageUrl: media.url } ) }
									onRemove={ () => updateRow( index, { image: 0, imageUrl: '' } ) }
								/>
							</div>
							<TextControl
								label={ __( 'Title', 'cb-peoplesafe2027' ) }
								value={ row.title || '' }
								onChange={ ( value ) => updateRow( index, { title: value } ) }
							/>
							<TextareaControl
								label={ __( 'Text', 'cb-peoplesafe2027' ) }
								value={ row.text || '' }
								rows={ 3 }
								onChange={ ( value ) => updateRow( index, { text: value } ) }
							/>
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
				{ rows.length < MAX_STEPS && (
					<Button variant="secondary" onClick={ () => addRow( EMPTY_STEP ) }>
						{ __( 'Add step', 'cb-peoplesafe2027' ) }
					</Button>
				) }
			</div>
		</EditorBlockShell>
	);
}
