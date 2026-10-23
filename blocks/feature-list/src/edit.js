import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Button, SelectControl, TextControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RichField from '../../_shared/RichField';
import useRowMotion from '../../_shared/useRowMotion';

const EMPTY_ITEM = { term: '', definition: '' };

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { preTitle, title, items, tickColour } = attributes;
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
		if ( window.confirm( __( 'Remove this item?', 'cb-peoplesafe2027' ) ) ) {
			removeRow( index );
		}
	}

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Feature list" textDomain="cb-peoplesafe2027">
			<div className="cb-peoplesafe2027-cta-buttons__pair">
				<TextControl
					label={ __( 'Pre-title (optional)', 'cb-peoplesafe2027' ) }
					value={ preTitle }
					onChange={ ( value ) => setAttributes( { preTitle: value } ) }
				/>
				<TextControl
					label={ __( 'Title', 'cb-peoplesafe2027' ) }
					value={ title }
					onChange={ ( value ) => setAttributes( { title: value } ) }
				/>
			</div>
			<SelectControl
				label={ __( 'Tick colour', 'cb-peoplesafe2027' ) }
				value={ tickColour || '' }
				options={ [
					{ label: __( 'Teal', 'cb-peoplesafe2027' ), value: '' },
					{ label: __( 'Orange', 'cb-peoplesafe2027' ), value: 'orange' },
				] }
				onChange={ ( value ) => setAttributes( { tickColour: value } ) }
			/>
			<div className="cb-peoplesafe2027-faq-rows">
				<label className="cb-peoplesafe2027-editor-field__label">{ __( 'Items', 'cb-peoplesafe2027' ) }</label>
				{ rows.map( ( row, index ) => (
					<div
						className={ 'cb-peoplesafe2027-related-items__row' + ( keys[ index ] === movedKey ? ' is-moved' : '' ) }
						key={ keys[ index ] }
						ref={ setRowRef( keys[ index ] ) }
					>
						<span className="cb-peoplesafe2027-repeater-field__number">{ index + 1 }</span>
						<div className="cb-peoplesafe2027-faq-rows__fields">
							<TextControl
								label={ __( 'Term', 'cb-peoplesafe2027' ) }
								value={ row.term || '' }
								onChange={ ( value ) => updateRow( index, { term: value } ) }
							/>
							<RichField
								label={ __( 'Definition', 'cb-peoplesafe2027' ) }
								value={ row.definition }
								onChange={ ( value ) => updateRow( index, { definition: value } ) }
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
				<Button variant="secondary" onClick={ () => addRow( EMPTY_ITEM ) }>
					{ __( 'Add item', 'cb-peoplesafe2027' ) }
				</Button>
			</div>
		</EditorBlockShell>
	);
}
