import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Button, SelectControl, TextControl, TextareaControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RichField from '../../_shared/RichField';
import useRowMotion from '../../_shared/useRowMotion';

const MIN_TILES = 2;
const MAX_TILES = 6;
const EMPTY_TILE = { text: '', colour: 'yellow' };

const COLOURS = [
	{ value: 'yellow', label: __( 'Yellow', 'cb-peoplesafe2027' ) },
	{ value: 'teal', label: __( 'Teal', 'cb-peoplesafe2027' ) },
	{ value: 'blue', label: __( 'Blue', 'cb-peoplesafe2027' ) },
	{ value: 'pink', label: __( 'Pink', 'cb-peoplesafe2027' ) },
	{ value: 'orange', label: __( 'Orange', 'cb-peoplesafe2027' ) },
];

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { title, intro, tiles } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );
	const rows = tiles || [];

	function updateRow( index, patch ) {
		const next = rows.slice();
		next[ index ] = { ...next[ index ], ...patch };
		setAttributes( { tiles: next } );
	}

	const { keys, setRowRef, movedKey, addRow, moveRow, removeRow } = useRowMotion( rows, ( next ) => setAttributes( { tiles: next } ) );

	function confirmRemove( index ) {
		// eslint-disable-next-line no-alert -- a plain confirm() is enough friction for an irreversible remove.
		if ( window.confirm( __( 'Remove this tile?', 'cb-peoplesafe2027' ) ) ) {
			removeRow( index );
		}
	}

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Principle tiles" textDomain="cb-peoplesafe2027">
			<TextControl label={ __( 'Title', 'cb-peoplesafe2027' ) } value={ title } onChange={ ( value ) => setAttributes( { title: value } ) } />
			<RichField label={ __( 'Introduction', 'cb-peoplesafe2027' ) } value={ intro } onChange={ ( value ) => setAttributes( { intro: value } ) } />
			<div className="cb-peoplesafe2027-faq-rows">
				<label className="cb-peoplesafe2027-editor-field__label">{ __( 'Tiles (2 to 6)', 'cb-peoplesafe2027' ) }</label>
				{ rows.map( ( row, index ) => (
					<div
						className={ 'cb-peoplesafe2027-related-items__row' + ( keys[ index ] === movedKey ? ' is-moved' : '' ) }
						key={ keys[ index ] }
						ref={ setRowRef( keys[ index ] ) }
					>
						<span className="cb-peoplesafe2027-repeater-field__number">{ index + 1 }</span>
						<div className="cb-peoplesafe2027-faq-rows__fields">
							<TextareaControl label={ __( 'Text', 'cb-peoplesafe2027' ) } value={ row.text || '' } rows={ 2 } onChange={ ( value ) => updateRow( index, { text: value } ) } />
							<SelectControl label={ __( 'Colour', 'cb-peoplesafe2027' ) } value={ row.colour || 'yellow' } options={ COLOURS } onChange={ ( value ) => updateRow( index, { colour: value } ) } />
						</div>
						<div className="cb-peoplesafe2027-cta-buttons__actions">
							<Button size="small" label={ __( 'Move up', 'cb-peoplesafe2027' ) } onClick={ () => moveRow( index, -1 ) } disabled={ 0 === index }>
								&#9650;
							</Button>
							<Button size="small" label={ __( 'Move down', 'cb-peoplesafe2027' ) } onClick={ () => moveRow( index, 1 ) } disabled={ index === rows.length - 1 }>
								&#9660;
							</Button>
							<Button size="small" isDestructive label={ __( 'Remove', 'cb-peoplesafe2027' ) } onClick={ () => confirmRemove( index ) } disabled={ rows.length <= MIN_TILES }>
								&times;
							</Button>
						</div>
					</div>
				) ) }
				{ rows.length < MAX_TILES && (
					<Button variant="secondary" onClick={ () => addRow( EMPTY_TILE ) }>
						{ __( 'Add tile', 'cb-peoplesafe2027' ) }
					</Button>
				) }
			</div>
		</EditorBlockShell>
	);
}
