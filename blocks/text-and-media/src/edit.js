import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps, RichText, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { TextControl, SelectControl, ToggleControl, Button } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import CtaButtonsField from '../../_shared/CtaButtonsField';
import SiteUrlInput from '../../_shared/SiteUrlInput';
import useMediaUrl from '../../_shared/useMediaUrl';
import RichField from '../../_shared/RichField';
import useRowMotion from '../../_shared/useRowMotion';

function ImageField( { label, help, id, url, alt, onSelect, onRemove } ) {
	return (
		<div className="cb-peoplesafe2027-editor-field">
			<label className="cb-peoplesafe2027-editor-field__label">{ label }</label>
			<MediaUploadCheck>
				<MediaUpload
					onSelect={ onSelect }
					allowedTypes={ [ 'image' ] }
					value={ id }
					render={ ( { open } ) => (
						<div className="cb-peoplesafe2027-editor-field__control">
							{ url && <img src={ url } alt={ alt } style={ { maxWidth: '200px', display: 'block', marginBottom: '8px' } } /> }
							<Button variant="secondary" onClick={ open }>
								{ url ? __( 'Replace image', 'cb-peoplesafe2027' ) : __( 'Select image', 'cb-peoplesafe2027' ) }
							</Button>
							{ url && (
								<Button variant="link" isDestructive onClick={ onRemove } style={ { marginLeft: '12px' } }>
									{ __( 'Remove', 'cb-peoplesafe2027' ) }
								</Button>
							) }
						</div>
					) }
				/>
			</MediaUploadCheck>
			{ help && <p className="cb-peoplesafe2027-editor-field__help">{ help }</p> }
		</div>
	);
}

const EMPTY_ITEM = { text: '' };

function TickListField( { value, onChange } ) {
	const rows = value || [];

	function updateRow( index, patch ) {
		const next = rows.slice();
		next[ index ] = { ...next[ index ], ...patch };
		onChange( next );
	}

	const { keys, setRowRef, movedKey, addRow, moveRow, removeRow } = useRowMotion( rows, onChange );

	return (
		<div className="cb-peoplesafe2027-editor-field">
			<label className="cb-peoplesafe2027-editor-field__label">{ __( 'Tick list', 'cb-peoplesafe2027' ) }</label>
			{ rows.map( ( row, index ) => (
				<div
					className={ 'cb-peoplesafe2027-related-items__row' + ( keys[ index ] === movedKey ? ' is-moved' : '' ) }
					key={ keys[ index ] }
					ref={ setRowRef( keys[ index ] ) }
				>
					<span className="cb-peoplesafe2027-repeater-field__number">{ index + 1 }</span>
					<div className="cb-peoplesafe2027-faq-rows__fields">
						<div className="cb-peoplesafe2027-editor-field__control">
							<RichText
								tagName="div"
								aria-label={ __( 'List item', 'cb-peoplesafe2027' ) }
								placeholder={ __( 'List item', 'cb-peoplesafe2027' ) }
								allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
								inlineToolbar
								value={ row.text || '' }
								onChange={ ( text ) => updateRow( index, { text } ) }
							/>
						</div>
					</div>
					<div className="cb-peoplesafe2027-cta-buttons__actions">
						<Button size="small" label={ __( 'Move up', 'cb-peoplesafe2027' ) } onClick={ () => moveRow( index, -1 ) } disabled={ 0 === index }>
							&#9650;
						</Button>
						<Button size="small" label={ __( 'Move down', 'cb-peoplesafe2027' ) } onClick={ () => moveRow( index, 1 ) } disabled={ index === rows.length - 1 }>
							&#9660;
						</Button>
						<Button size="small" isDestructive label={ __( 'Remove', 'cb-peoplesafe2027' ) } onClick={ () => removeRow( index ) }>
							&times;
						</Button>
					</div>
				</div>
			) ) }
			<Button variant="secondary" onClick={ () => addRow( EMPTY_ITEM ) }>
				{ __( 'Add item', 'cb-peoplesafe2027' ) }
			</Button>
		</div>
	);
}

export default function Edit( { attributes, setAttributes, clientId } ) {
	const {
		preTitle,
		title,
		buttons,
		mediaType,
		imageId,
		imageUrl,
		videoUrl,
		playIn,
		framed,
		listItems,
		tickColour,
		posterUrl,
		posterId,
		mediaPosition,
		split,
		belowMedia,
		transcript,
	} = attributes;
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );
	const imagePreview = useMediaUrl( imageId, imageUrl );
	const posterPreview = useMediaUrl( posterId, posterUrl );
	const contentProps = useInnerBlocksProps(
		{ className: 'cb-peoplesafe2027-editor-field__control' },
		{
			allowedBlocks: [ 'core/paragraph', 'core/list', 'core/heading' ],
			template: [ [ 'core/paragraph', {} ] ],
		}
	);

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Text and media" textDomain="cb-peoplesafe2027">
			<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
				<div style={ { flex: '1 1 0%' } }>
					<TextControl
						label={ __( 'Pre-title', 'cb-peoplesafe2027' ) }
						value={ preTitle }
						onChange={ ( value ) => setAttributes( { preTitle: value } ) }
					/>
				</div>
				<div style={ { flex: '2 1 0%' } }>
					<TextControl
						label={ __( 'Title', 'cb-peoplesafe2027' ) }
						value={ title }
						onChange={ ( value ) => setAttributes( { title: value } ) }
					/>
				</div>
			</div>
			<div className="cb-peoplesafe2027-editor-field">
				<label className="cb-peoplesafe2027-editor-field__label">{ __( 'Content', 'cb-peoplesafe2027' ) }</label>
				<div { ...contentProps } />
				<p className="cb-peoplesafe2027-editor-field__help">
					{ __( 'Paragraphs, lists and sub-headings.', 'cb-peoplesafe2027' ) }
				</p>
			</div>
			<CtaButtonsField value={ buttons } onChange={ ( rows ) => setAttributes( { buttons: rows } ) } />
			<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
				<div style={ { flex: '1 1 0%' } }>
					<SelectControl
						label={ __( 'Media', 'cb-peoplesafe2027' ) }
						value={ mediaType }
						options={ [
							{ label: __( 'Image', 'cb-peoplesafe2027' ), value: 'image' },
							{ label: __( 'Video', 'cb-peoplesafe2027' ), value: 'video' },
							{ label: __( 'Tick list', 'cb-peoplesafe2027' ), value: 'list' },
							{ label: __( 'None (text only)', 'cb-peoplesafe2027' ), value: 'none' },
						] }
						onChange={ ( value ) => setAttributes( { mediaType: value } ) }
					/>
				</div>
				<div style={ { flex: '1 1 0%' } }>
					<SelectControl
						label={ __( 'Media position', 'cb-peoplesafe2027' ) }
						value={ mediaPosition }
						options={ [
							{ label: __( 'Right of the text', 'cb-peoplesafe2027' ), value: 'right' },
							{ label: __( 'Left of the text', 'cb-peoplesafe2027' ), value: 'left' },
							{ label: __( 'Below the text, centred', 'cb-peoplesafe2027' ), value: 'below' },
						] }
						onChange={ ( value ) => setAttributes( { mediaPosition: value } ) }
					/>
				</div>
				<div style={ { flex: '1 1 0%' } }>
					<SelectControl
						label={ __( 'Width', 'cb-peoplesafe2027' ) }
						value={ split }
						options={ [
							{ label: __( 'Half and half', 'cb-peoplesafe2027' ), value: 'half' },
							{ label: __( 'Wide text, narrow media', 'cb-peoplesafe2027' ), value: 'wide-text' },
						] }
						onChange={ ( value ) => setAttributes( { split: value } ) }
					/>
				</div>
			</div>
			{ 'list' === mediaType && (
				<>
					<TickListField value={ listItems } onChange={ ( rows ) => setAttributes( { listItems: rows } ) } />
					<SelectControl
						label={ __( 'Tick colour', 'cb-peoplesafe2027' ) }
						value={ tickColour || '' }
						options={ [
							{ label: __( 'Teal', 'cb-peoplesafe2027' ), value: '' },
							{ label: __( 'Orange', 'cb-peoplesafe2027' ), value: 'orange' },
						] }
						onChange={ ( value ) => setAttributes( { tickColour: value } ) }
					/>
				</>
			) }
			{ 'image' === mediaType && (
				<>
					<ImageField
						label={ __( 'Image', 'cb-peoplesafe2027' ) }
						id={ imageId }
						url={ imagePreview }
						alt=""
						onSelect={ ( media ) => setAttributes( { imageId: media.id, imageUrl: media.url } ) }
						onRemove={ () => setAttributes( { imageId: 0, imageUrl: '' } ) }
					/>
					<ToggleControl
						label={ __( 'Frame the image', 'cb-peoplesafe2027' ) }
						checked={ false !== framed }
						onChange={ ( value ) => setAttributes( { framed: value } ) }
						help={ __( 'On: rounded corners, a shadow and a white background. Switch it off for an image with a transparent background, so it sits on the page with no shadow or white box.', 'cb-peoplesafe2027' ) }
					/>
				</>
			) }
			{ 'video' === mediaType && (
				<>
					<div className="cb-peoplesafe2027-editor-field">
						<span className="cb-peoplesafe2027-editor-field__label">{ __( 'Video link', 'cb-peoplesafe2027' ) }</span>
						<SiteUrlInput
							value={ videoUrl }
							onChange={ ( url ) => setAttributes( { videoUrl: url } ) }
							placeholder={ __( 'Paste a YouTube or Vimeo link', 'cb-peoplesafe2027' ) }
						/>
					</div>
					<SelectControl
						label={ __( 'Play the video', 'cb-peoplesafe2027' ) }
						value={ playIn || 'modal' }
						options={ [
							{ label: __( 'In a pop-out on desktop (phones play in place)', 'cb-peoplesafe2027' ), value: 'modal' },
							{ label: __( 'In place', 'cb-peoplesafe2027' ), value: 'inline' },
						] }
						onChange={ ( value ) => setAttributes( { playIn: value } ) }
					/>
					<ImageField
						label={ __( 'Poster image', 'cb-peoplesafe2027' ) }
						help={ __( "Optional. Leave empty to use the video's own thumbnail.", 'cb-peoplesafe2027' ) }
						id={ posterId }
						url={ posterPreview }
						alt=""
						onSelect={ ( media ) => setAttributes( { posterId: media.id, posterUrl: media.url } ) }
						onRemove={ () => setAttributes( { posterId: 0, posterUrl: '' } ) }
					/>
					<RichField
						label={ __( 'Text under the video', 'cb-peoplesafe2027' ) }
						value={ belowMedia }
						onChange={ ( value ) => setAttributes( { belowMedia: value } ) }
					/>
					<RichField
						label={ __( 'Video transcript', 'cb-peoplesafe2027' ) }
						value={ transcript }
						onChange={ ( value ) => setAttributes( { transcript: value } ) }
					/>
				</>
			) }
		</EditorBlockShell>
	);
}
