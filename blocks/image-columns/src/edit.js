import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Button, SelectControl, TextControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import ImagePicker from '../../_shared/ImagePicker';
import RichField from '../../_shared/RichField';
import useRowMotion from '../../_shared/useRowMotion';

const MIN_ITEMS = 2;
const MAX_ITEMS = 5;
const EMPTY_ITEM = { image: 0, imageUrl: '', title: '', text: '' };

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { items, style } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );
	const rows = items || [];

	function updateRow( index, patch ) {
		const next = rows.slice();
		next[ index ] = { ...next[ index ], ...patch };
		setAttributes( { items: next } );
	}

	const { keys, setRowRef, movedKey, addRow, moveRow, removeRow } = useRowMotion( rows, ( next ) => setAttributes( { items: next } ) );

	function confirmRemove( index ) {
		// eslint-disable-next-line no-alert -- a plain confirm() is enough friction for an irreversible remove.
		if ( window.confirm( __( 'Remove this column?', 'cb-peoplesafe2027' ) ) ) {
			removeRow( index );
		}
	}

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Image columns" textDomain="cb-peoplesafe2027">
			<div className="cb-peoplesafe2027-editor-field">
				<p className="cb-peoplesafe2027-editor-field__help">
					{ __( 'The title and introduction go above this block as an ordinary heading and paragraph.', 'cb-peoplesafe2027' ) }
				</p>
			</div>
			<SelectControl
				label={ __( 'Style', 'cb-peoplesafe2027' ) }
				value={ style }
				options={ [
					{ value: 'plain', label: __( 'Plain: centred text under each image', 'cb-peoplesafe2027' ) },
					{ value: 'cards', label: __( 'Cards: image on white, left-aligned text on yellow', 'cb-peoplesafe2027' ) },
				] }
				onChange={ ( value ) => setAttributes( { style: value } ) }
			/>
			<div className="cb-peoplesafe2027-faq-rows">
				<label className="cb-peoplesafe2027-editor-field__label">{ __( 'Columns (2 to 5)', 'cb-peoplesafe2027' ) }</label>
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
							<RichField
								label={ __( 'Content', 'cb-peoplesafe2027' ) }
								value={ row.text }
								onChange={ ( value ) => updateRow( index, { text: value } ) }
								allowedFormats={ [ 'core/bold', 'core/italic', 'core/link', 'core/text-color' ] }
							/>
						</div>
						<div className="cb-peoplesafe2027-cta-buttons__actions">
							<Button size="small" label={ __( 'Move up', 'cb-peoplesafe2027' ) } onClick={ () => moveRow( index, -1 ) } disabled={ 0 === index }>
								&#9650;
							</Button>
							<Button size="small" label={ __( 'Move down', 'cb-peoplesafe2027' ) } onClick={ () => moveRow( index, 1 ) } disabled={ index === rows.length - 1 }>
								&#9660;
							</Button>
							<Button size="small" isDestructive label={ __( 'Remove', 'cb-peoplesafe2027' ) } onClick={ () => confirmRemove( index ) } disabled={ rows.length <= MIN_ITEMS }>
								&times;
							</Button>
						</div>
					</div>
				) ) }
				{ rows.length < MAX_ITEMS && (
					<Button variant="secondary" onClick={ () => addRow( EMPTY_ITEM ) }>
						{ __( 'Add column', 'cb-peoplesafe2027' ) }
					</Button>
				) }
			</div>
		</EditorBlockShell>
	);
}
