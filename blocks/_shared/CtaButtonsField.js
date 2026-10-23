import { __ } from '@wordpress/i18n';
import { Button, RadioControl, TextControl, ToggleControl } from '@wordpress/components';
import useRowMotion from './useRowMotion';
import SiteUrlInput from './SiteUrlInput';

/**
 * The shared button fields for any block that prints its buttons with
 * cb_peoplesafe2027_cta_buttons() (inc/cta-buttons.php).
 *
 * Each row: button title and URL side by side (the URL is a typeahead that
 * suggests pages and posts, and takes ordinary text when nothing matches),
 * then an optional dialog id and the style side by side, with the style
 * radios inline.
 *
 * Store the result in a block attribute of type "array" (default []).
 *
 * @param {Object}   props
 * @param {Object[]} props.value    Button rows.
 * @param {Function} props.onChange ( rows ) => void
 * @param {string}   [props.label]  Field label.
 */

const EMPTY_ROW = { linkText: '', link: '', linkTarget: false, dialog: '', variant: '' };

export default function CtaButtonsField( { value, onChange, label } ) {
	const rows = value || [];

	function updateRow( index, patch ) {
		const next = rows.slice();
		next[ index ] = { ...next[ index ], ...patch };
		onChange( next );
	}

	const { keys, setRowRef, movedKey, addRow, moveRow, removeRow } = useRowMotion( rows, onChange );

	function confirmRemove( index ) {
		// eslint-disable-next-line no-alert -- a plain confirm() is enough friction for an irreversible remove.
		if ( window.confirm( __( 'Remove this button?', 'cb-peoplesafe2027' ) ) ) {
			removeRow( index );
		}
	}

	return (
		<div className="cb-peoplesafe2027-cta-buttons">
			<label className="cb-peoplesafe2027-editor-field__label">{ label || __( 'Buttons', 'cb-peoplesafe2027' ) }</label>
			{ rows.map( ( row, index ) => (
				<div
					className={ 'cb-peoplesafe2027-cta-buttons__row' + ( keys[ index ] === movedKey ? ' is-moved' : '' ) }
					key={ keys[ index ] }
					ref={ setRowRef( keys[ index ] ) }
				>
					<span className="cb-peoplesafe2027-repeater-field__number">{ index + 1 }</span>
					<div className="cb-peoplesafe2027-cta-buttons__fields">
					<div className="cb-peoplesafe2027-cta-buttons__pair">
						<TextControl
							label={ __( 'Button title', 'cb-peoplesafe2027' ) }
							value={ row.linkText || '' }
							onChange={ ( v ) => updateRow( index, { linkText: v } ) }
						/>
						<div className="cb-peoplesafe2027-cta-buttons__url">
							<span className="cb-peoplesafe2027-editor-field__label">{ __( 'URL', 'cb-peoplesafe2027' ) }</span>
							<SiteUrlInput
								value={ row.link }
								onChange={ ( url ) => updateRow( index, { link: url } ) }
								placeholder={ __( 'Search pages and posts, or type a URL', 'cb-peoplesafe2027' ) }
							/>
							<ToggleControl
								label={ __( 'Open in a new tab', 'cb-peoplesafe2027' ) }
								checked={ !! row.linkTarget }
								onChange={ ( v ) => updateRow( index, { linkTarget: v } ) }
								__nextHasNoMarginBottom
							/>
						</div>
					</div>
					<div className="cb-peoplesafe2027-cta-buttons__pair">
						<TextControl
							label={ __( 'Dialog id', 'cb-peoplesafe2027' ) }
							value={ row.dialog || '' }
							onChange={ ( v ) => updateRow( index, { dialog: v } ) }
							help={ __( 'Opens this dialog instead of a link. Leave the URL empty.', 'cb-peoplesafe2027' ) }
						/>
						<RadioControl
							className="cb-peoplesafe2027-cta-buttons__style"
							label={ __( 'Style', 'cb-peoplesafe2027' ) }
							selected={ row.variant || '' }
							options={ [
								{ label: __( 'Auto', 'cb-peoplesafe2027' ), value: '' },
								{ label: __( 'Primary', 'cb-peoplesafe2027' ), value: 'primary' },
								{ label: __( 'Outline', 'cb-peoplesafe2027' ), value: 'outline' },
								{ label: __( 'Outline white', 'cb-peoplesafe2027' ), value: 'outline-white' },
								{ label: __( 'White', 'cb-peoplesafe2027' ), value: 'white' },
							] }
							help={ __( 'Auto: the first button primary, the rest outline.', 'cb-peoplesafe2027' ) }
							onChange={ ( v ) => updateRow( index, { variant: v } ) }
						/>
					</div>
					</div>
						<div className="cb-peoplesafe2027-cta-buttons__actions">
							<Button
								size="small"
								data-move="up"
								label={ __( 'Move up', 'cb-peoplesafe2027' ) }
								onClick={ () => moveRow( index, -1 ) }
								disabled={ 0 === index }
							>
								&#9650;
							</Button>
							<Button
								size="small"
								data-move="down"
								label={ __( 'Move down', 'cb-peoplesafe2027' ) }
								onClick={ () => moveRow( index, 1 ) }
								disabled={ index === rows.length - 1 }
							>
								&#9660;
							</Button>
							<Button size="small" isDestructive label={ __( 'Remove', 'cb-peoplesafe2027' ) } onClick={ () => confirmRemove( index ) }>
								&times;
							</Button>
						</div>
				</div>
			) ) }
			<Button variant="primary" onClick={ () => addRow( EMPTY_ROW ) }>
				{ __( 'Add button', 'cb-peoplesafe2027' ) }
			</Button>
		</div>
	);
}
