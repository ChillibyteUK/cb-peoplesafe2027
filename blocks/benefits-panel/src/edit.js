import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Button, SelectControl, TextControl, TextareaControl, ToggleControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import ImagePicker from '../../_shared/ImagePicker';
import RichField from '../../_shared/RichField';
import useRowMotion from '../../_shared/useRowMotion';

const EMPTY_ITEM = { type: 'card', image: 0, imageUrl: '', title: '', content: '', span: 3, offset: 0, stack: false };

const TYPES = [
	{ value: 'card', label: __( 'Benefit card (icon, title, text)', 'cb-peoplesafe2027' ) },
	{ value: 'photo', label: __( 'Photo', 'cb-peoplesafe2027' ) },
];

const SPANS = [
	{ value: 3, label: __( 'A quarter of the width', 'cb-peoplesafe2027' ) },
	{ value: 4, label: __( 'A third of the width', 'cb-peoplesafe2027' ) },
	{ value: 6, label: __( 'Half the width', 'cb-peoplesafe2027' ) },
];

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { title, intro, footnote, items } = attributes;
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
		if ( window.confirm( __( 'Remove this tile?', 'cb-peoplesafe2027' ) ) ) {
			removeRow( index );
		}
	}

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Benefits panel" textDomain="cb-peoplesafe2027">
			<TextControl label={ __( 'Title', 'cb-peoplesafe2027' ) } value={ title } onChange={ ( value ) => setAttributes( { title: value } ) } />
			<RichField label={ __( 'Introduction', 'cb-peoplesafe2027' ) } value={ intro } onChange={ ( value ) => setAttributes( { intro: value } ) } />
			<RichField label={ __( 'Footnote', 'cb-peoplesafe2027' ) } value={ footnote } onChange={ ( value ) => setAttributes( { footnote: value } ) } />
			<div className="cb-peoplesafe2027-faq-rows">
				<label className="cb-peoplesafe2027-editor-field__label">{ __( 'Tiles', 'cb-peoplesafe2027' ) }</label>
				<p className="cb-peoplesafe2027-editor-field__help">
					{ __( 'Tiles fill a row of twelve columns: a quarter is 3, a third is 4 and half is 6. A card can sit under the card before it.', 'cb-peoplesafe2027' ) }
				</p>
				{ rows.map( ( row, index ) => {
					const isCard = 'photo' !== row.type;

					return (
						<div
							className={ 'cb-peoplesafe2027-related-items__row' + ( keys[ index ] === movedKey ? ' is-moved' : '' ) }
							key={ keys[ index ] }
							ref={ setRowRef( keys[ index ] ) }
						>
							<span className="cb-peoplesafe2027-repeater-field__number">{ index + 1 }</span>
							<div className="cb-peoplesafe2027-faq-rows__fields">
								<SelectControl label={ __( 'Tile', 'cb-peoplesafe2027' ) } value={ row.type || 'card' } options={ TYPES } onChange={ ( value ) => updateRow( index, { type: value, ...( 'photo' === value ? { stack: false } : {} ) } ) } />
								<div className="cb-peoplesafe2027-editor-field">
									<span className="cb-peoplesafe2027-editor-field__label">{ isCard ? __( 'Icon', 'cb-peoplesafe2027' ) : __( 'Photo', 'cb-peoplesafe2027' ) }</span>
									<ImagePicker
										id={ row.image }
										url={ row.imageUrl }
										size={ isCard ? 56 : 96 }
										onSelect={ ( media ) => updateRow( index, { image: media.id, imageUrl: media.url } ) }
										onRemove={ () => updateRow( index, { image: 0, imageUrl: '' } ) }
									/>
								</div>
								{ isCard && (
									<>
										<TextControl label={ __( 'Title', 'cb-peoplesafe2027' ) } value={ row.title || '' } onChange={ ( value ) => updateRow( index, { title: value } ) } />
										<TextareaControl label={ __( 'Text', 'cb-peoplesafe2027' ) } value={ row.content || '' } rows={ 2 } onChange={ ( value ) => updateRow( index, { content: value } ) } />
										<ToggleControl
											label={ __( 'Sit under the previous card', 'cb-peoplesafe2027' ) }
											checked={ !! row.stack }
											onChange={ ( value ) => updateRow( index, { stack: value } ) }
											disabled={ 0 === index }
											__nextHasNoMarginBottom
										/>
									</>
								) }
								{ ! row.stack && (
									<>
										<SelectControl label={ __( 'Width', 'cb-peoplesafe2027' ) } value={ row.span || 3 } options={ SPANS } onChange={ ( value ) => updateRow( index, { span: parseInt( value, 10 ) } ) } />
										<ToggleControl
											label={ __( 'Leave a quarter-width gap before this tile', 'cb-peoplesafe2027' ) }
											checked={ !! row.offset }
											onChange={ ( value ) => updateRow( index, { offset: value ? 3 : 0 } ) }
											__nextHasNoMarginBottom
										/>
									</>
								) }
							</div>
							<div className="cb-peoplesafe2027-cta-buttons__actions">
								<Button size="small" label={ __( 'Move up', 'cb-peoplesafe2027' ) } onClick={ () => moveRow( index, -1 ) } disabled={ 0 === index }>
									&#9650;
								</Button>
								<Button size="small" label={ __( 'Move down', 'cb-peoplesafe2027' ) } onClick={ () => moveRow( index, 1 ) } disabled={ index === rows.length - 1 }>
									&#9660;
								</Button>
								<Button size="small" isDestructive label={ __( 'Remove', 'cb-peoplesafe2027' ) } onClick={ () => confirmRemove( index ) } disabled={ rows.length <= 1 }>
									&times;
								</Button>
							</div>
						</div>
					);
				} ) }
				<Button variant="secondary" onClick={ () => addRow( EMPTY_ITEM ) }>
					{ __( 'Add tile', 'cb-peoplesafe2027' ) }
				</Button>
			</div>
		</EditorBlockShell>
	);
}
