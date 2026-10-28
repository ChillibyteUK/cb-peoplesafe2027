import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Button, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RichField from '../../_shared/RichField';
import useRowMotion from '../../_shared/useRowMotion';

const EMPTY_ROW = { name: '', status: 'operational' };

const STATUS_OPTIONS = [
	{ label: __( 'Operational', 'cb-peoplesafe2027' ), value: 'operational' },
	{ label: __( 'Degraded', 'cb-peoplesafe2027' ), value: 'degraded' },
	{ label: __( 'Down', 'cb-peoplesafe2027' ), value: 'down' },
];

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { message, title, showUpdated, services } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );
	const rows = services || [];

	function updateRow( index, patch ) {
		const next = rows.slice();
		next[ index ] = { ...next[ index ], ...patch };
		setAttributes( { services: next } );
	}

	const { keys, setRowRef, movedKey, addRow, moveRow, removeRow } = useRowMotion( rows, ( next ) => setAttributes( { services: next } ) );

	function confirmRemove( index ) {
		// eslint-disable-next-line no-alert -- a plain confirm() is enough friction for an irreversible remove.
		if ( window.confirm( __( 'Remove this service?', 'cb-peoplesafe2027' ) ) ) {
			removeRow( index );
		}
	}

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Service status" textDomain="cb-peoplesafe2027">
			<RichField
				label={ __( 'Message', 'cb-peoplesafe2027' ) }
				value={ message }
				onChange={ ( value ) => setAttributes( { message: value } ) }
			/>
			<p className="components-base-control__help">{ __( 'Optional notice above the status, such as an incident update. Enter starts a new paragraph. Leave empty to show nothing.', 'cb-peoplesafe2027' ) }</p>
			<TextControl
				label={ __( 'Title', 'cb-peoplesafe2027' ) }
				value={ title }
				onChange={ ( value ) => setAttributes( { title: value } ) }
				help={ __( 'Optional heading above the list of services.', 'cb-peoplesafe2027' ) }
			/>
			<ToggleControl
				label={ __( 'Show when the page was last updated', 'cb-peoplesafe2027' ) }
				checked={ !! showUpdated }
				onChange={ ( value ) => setAttributes( { showUpdated: value } ) }
				help={ __( 'The date and time the page was last saved. Change a status, then Update the page.', 'cb-peoplesafe2027' ) }
			/>
			<div className="cb-peoplesafe2027-faq-rows">
				<label className="cb-peoplesafe2027-editor-field__label">{ __( 'Services', 'cb-peoplesafe2027' ) }</label>
				{ rows.map( ( row, index ) => (
					<div
						className={ 'cb-peoplesafe2027-related-items__row' + ( keys[ index ] === movedKey ? ' is-moved' : '' ) }
						key={ keys[ index ] }
						ref={ setRowRef( keys[ index ] ) }
					>
						<span className="cb-peoplesafe2027-repeater-field__number">{ index + 1 }</span>
						<div className="cb-peoplesafe2027-faq-rows__fields">
							<TextControl label={ __( 'Service', 'cb-peoplesafe2027' ) } value={ row.name || '' } onChange={ ( value ) => updateRow( index, { name: value } ) } />
							<SelectControl label={ __( 'Status', 'cb-peoplesafe2027' ) } value={ row.status || 'operational' } options={ STATUS_OPTIONS } onChange={ ( value ) => updateRow( index, { status: value } ) } />
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
				<Button variant="secondary" onClick={ () => addRow( EMPTY_ROW ) }>
					{ __( 'Add service', 'cb-peoplesafe2027' ) }
				</Button>
			</div>
		</EditorBlockShell>
	);
}
