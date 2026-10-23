import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Button, TextControl, ToggleControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import ImagePicker from '../../_shared/ImagePicker';
import useRowMotion from '../../_shared/useRowMotion';

const MIN_STATS = 2;
const MAX_STATS = 4;
const EMPTY_STAT = { prefix: '', value: '', suffix: '', label: '', description: '' };

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { title, imageId, imageUrl, hideImage, stats } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );
	const rows = stats || [];

	function updateRow( index, patch ) {
		const next = rows.slice();
		next[ index ] = { ...next[ index ], ...patch };
		setAttributes( { stats: next } );
	}

	const { keys, setRowRef, movedKey, addRow, moveRow, removeRow } = useRowMotion( rows, ( next ) => setAttributes( { stats: next } ) );

	function confirmRemove( index ) {
		// eslint-disable-next-line no-alert -- a plain confirm() is enough friction for an irreversible remove.
		if ( window.confirm( __( 'Remove this stat?', 'cb-peoplesafe2027' ) ) ) {
			removeRow( index );
		}
	}

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Stats counter" textDomain="cb-peoplesafe2027">
			<TextControl
				label={ __( 'Title', 'cb-peoplesafe2027' ) }
				value={ title }
				onChange={ ( value ) => setAttributes( { title: value } ) }
				help={ __( 'Leave empty for "Protection you can count on".', 'cb-peoplesafe2027' ) }
			/>
			<div className="cb-peoplesafe2027-faq-rows">
				<label className="cb-peoplesafe2027-editor-field__label">{ __( 'Stats (2 to 4)', 'cb-peoplesafe2027' ) }</label>
				{ rows.map( ( row, index ) => (
					<div
						className={ 'cb-peoplesafe2027-related-items__row' + ( keys[ index ] === movedKey ? ' is-moved' : '' ) }
						key={ keys[ index ] }
						ref={ setRowRef( keys[ index ] ) }
					>
						<span className="cb-peoplesafe2027-repeater-field__number">{ index + 1 }</span>
						<div className="cb-peoplesafe2027-faq-rows__fields">
							<div className="cb-peoplesafe2027-stat-number">
								<TextControl
									label={ __( 'Before', 'cb-peoplesafe2027' ) }
									value={ row.prefix || '' }
									onChange={ ( value ) => updateRow( index, { prefix: value } ) }
									placeholder="£"
								/>
								<TextControl
									label={ __( 'Number', 'cb-peoplesafe2027' ) }
									value={ row.value || '' }
									onChange={ ( value ) => updateRow( index, { value } ) }
									placeholder="246"
								/>
								<TextControl
									label={ __( 'After', 'cb-peoplesafe2027' ) }
									value={ row.suffix || '' }
									onChange={ ( value ) => updateRow( index, { suffix: value } ) }
									placeholder="m"
								/>
							</div>
							<TextControl
								label={ __( 'Label', 'cb-peoplesafe2027' ) }
								value={ row.label || '' }
								onChange={ ( value ) => updateRow( index, { label: value } ) }
							/>
							<TextControl
								label={ __( 'Description', 'cb-peoplesafe2027' ) }
								value={ row.description || '' }
								onChange={ ( value ) => updateRow( index, { description: value } ) }
							/>
						</div>
						<div className="cb-peoplesafe2027-cta-buttons__actions">
							<Button size="small" label={ __( 'Move up', 'cb-peoplesafe2027' ) } onClick={ () => moveRow( index, -1 ) } disabled={ 0 === index }>
								&#9650;
							</Button>
							<Button size="small" label={ __( 'Move down', 'cb-peoplesafe2027' ) } onClick={ () => moveRow( index, 1 ) } disabled={ index === rows.length - 1 }>
								&#9660;
							</Button>
							<Button size="small" isDestructive label={ __( 'Remove', 'cb-peoplesafe2027' ) } onClick={ () => confirmRemove( index ) } disabled={ rows.length <= MIN_STATS }>
								&times;
							</Button>
						</div>
					</div>
				) ) }
				{ rows.length < MAX_STATS && (
					<Button variant="secondary" onClick={ () => addRow( EMPTY_STAT ) }>
						{ __( 'Add stat', 'cb-peoplesafe2027' ) }
					</Button>
				) }
			</div>
			<div className="cb-peoplesafe2027-editor-field">
				<span className="cb-peoplesafe2027-editor-field__label">{ __( 'Image', 'cb-peoplesafe2027' ) }</span>
				<ToggleControl
					label={ __( 'Hide the image', 'cb-peoplesafe2027' ) }
					checked={ !! hideImage }
					onChange={ ( value ) => setAttributes( { hideImage: value } ) }
					__nextHasNoMarginBottom
				/>
				{ ! hideImage && (
					<ImagePicker
						id={ imageId }
						url={ imageUrl }
						onSelect={ ( media ) => setAttributes( { imageId: media.id, imageUrl: media.url } ) }
						onRemove={ () => setAttributes( { imageId: 0, imageUrl: '' } ) }
					/>
				) }
				<p className="cb-peoplesafe2027-editor-field__help">
					{ __( 'Leave the image empty to use the standard cut-out.', 'cb-peoplesafe2027' ) }
				</p>
			</div>
		</EditorBlockShell>
	);
}
