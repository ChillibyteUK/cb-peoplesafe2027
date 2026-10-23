import { __ } from '@wordpress/i18n';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { TextControl, TextareaControl, ToggleControl, Button, RadioControl } from '@wordpress/components';
import useRowMotion from './useRowMotion';
import useMediaUrl from './useMediaUrl';

/**
 * A small preview of an image row field, looked up from its ID when the row
 * has no stored URL.
 */
function MediaThumb( { id, url } ) {
	const src = useMediaUrl( id, url );

	return src ? <img src={ src } alt="" /> : null;
}

/**
 * Generic repeater UI for a block attribute holding an array of row objects.
 * The block-editor equivalent of the `repeater` field type in
 * inc/options.php — same sub-field vocabulary (text/textarea/image), separate
 * implementation since one runs in wp-admin and the other inside the block
 * editor's React tree.
 *
 * Rows lay out inline by default (`layout: 'row'`): each sub-field takes an
 * equal-width slot, with compact move-up/move-down/remove icon buttons at
 * the row's end. Sub-field labels render once, as column headers above the
 * rows, rather than repeating per row — `hideLabelFromVision` keeps them
 * screen-reader accessible on each control without rendering visually
 * twice.
 *
 * `layout: 'column'` stacks each row's sub-fields vertically instead —
 * there's no shared column header in that layout (it wouldn't line up with
 * anything), so each sub-field's own label renders visibly above its
 * control instead of being screen-reader-only.
 *
 * @param {Object}   props
 * @param {string}   props.label    Field group label.
 * @param {Object[]} props.value    Current rows.
 * @param {Function} props.onChange ( rows ) => void
 * @param {Object[]} props.fields   [ { name, label, type: 'text'|'number'|'textarea'|'image'|'file'|'link'|'radio', help, mimeTypes, linkTarget, options } ]
 *                                  `linkTarget` (link fields only) adds an "open in new tab" toggle,
 *                                  storing `{name}Target` on the row — same opt-in shape as the
 *                                  top-level `link` field type's `link_target` option. `options`
 *                                  (radio fields only) is `[ { label, value } ]`, mirroring the
 *                                  top-level `select`/`radio` field types' options shape.
 * @param {Object}   props.emptyRow Shape of a freshly-added row, e.g. { stat: '', title: '' }.
 * @param {string}   [props.layout] 'row' (default) or 'column'.
 */
export default function RepeaterField( { label, value, onChange, fields, emptyRow, layout = 'row' } ) {
	const isColumn = 'column' === layout;
	const rows = value || [];

	function updateRow( index, patch ) {
		const next = rows.slice();
		next[ index ] = { ...next[ index ], ...patch };
		onChange( next );
	}

	const { keys, setRowRef, movedKey, addRow, moveRow, removeRow } = useRowMotion( rows, onChange );

	function confirmRemove( index ) {
		// eslint-disable-next-line no-alert -- a plain confirm() is enough
		// friction for an irreversible remove; no undo exists for this field.
		if ( window.confirm( __( 'Remove this row?', 'cb-peoplesafe2027' ) ) ) {
			removeRow( index );
		}
	}

	return (
		<div
			className={
				isColumn
					? 'cb-peoplesafe2027-repeater-field cb-peoplesafe2027-repeater-field--column'
					: 'cb-peoplesafe2027-repeater-field'
			}
		>
			<label className="cb-peoplesafe2027-editor-field__label">{ label }</label>
			{ ! isColumn && rows.length > 0 && (
				<div className="cb-peoplesafe2027-repeater-field__header">
					<span className="cb-peoplesafe2027-repeater-field__number-spacer" />
					{ fields.map( ( field ) => (
						<span
							key={ field.name }
							className={
								'image' === field.type || 'file' === field.type
									? 'cb-peoplesafe2027-repeater-field__header-cell cb-peoplesafe2027-repeater-field__header-cell--image'
									: 'cb-peoplesafe2027-repeater-field__header-cell'
							}
						>
							{ field.label }
						</span>
					) ) }
					<span className="cb-peoplesafe2027-repeater-field__row-actions-spacer" />
				</div>
			) }
			<div className="cb-peoplesafe2027-repeater-field__rows">
			{ rows.map( ( row, index ) => (
				<div
					className={ 'cb-peoplesafe2027-repeater-field__row' + ( keys[ index ] === movedKey ? ' is-moved' : '' ) }
					key={ keys[ index ] }
					ref={ setRowRef( keys[ index ] ) }
				>
					<span className="cb-peoplesafe2027-repeater-field__number">{ index + 1 }</span>
					{ fields.map( ( field ) => {
						if ( 'image' === field.type ) {
							return (
								<MediaUploadCheck key={ field.name }>
									<MediaUpload
										onSelect={ ( media ) =>
											updateRow( index, {
												[ field.name ]: media.id,
												[ `${ field.name }Url` ]: media.url,
											} )
										}
										allowedTypes={ [ 'image' ] }
										value={ row[ field.name ] }
										render={ ( { open } ) => (
											<div className="cb-peoplesafe2027-repeater-field__image">
												<MediaThumb id={ row[ field.name ] } url={ row[ `${ field.name }Url` ] } />
												<Button variant="secondary" size="small" onClick={ open }>
													{ row[ field.name ]
														? __( 'Replace', 'cb-peoplesafe2027' )
														: __( 'Select', 'cb-peoplesafe2027' ) }
												</Button>
											</div>
										) }
									/>
								</MediaUploadCheck>
							);
						}

						if ( 'link' === field.type ) {
							return (
								<div className="cb-peoplesafe2027-repeater-field__link" key={ field.name }>
									<TextControl
										label={ __( `${ field.label } Title`, 'cb-peoplesafe2027' ) }
										hideLabelFromVision={ ! isColumn }
										value={ row[ `${ field.name }Text` ] || '' }
										onChange={ ( v ) => updateRow( index, { [ `${ field.name }Text` ]: v } ) }
									/>
									<TextControl
										type="url"
										label={ __( `${ field.label } URL`, 'cb-peoplesafe2027' ) }
										hideLabelFromVision={ ! isColumn }
										value={ row[ field.name ] || '' }
										onChange={ ( v ) => updateRow( index, { [ field.name ]: v } ) }
										help={ field.help }
									/>
									{ field.linkTarget && (
										<ToggleControl
											label={ __( `Open ${ field.label } in a new tab`, 'cb-peoplesafe2027' ) }
											checked={ !! row[ `${ field.name }Target` ] }
											onChange={ ( v ) => updateRow( index, { [ `${ field.name }Target` ]: v } ) }
										/>
									) }
								</div>
							);
						}

						if ( 'file' === field.type ) {
							return (
								<MediaUploadCheck key={ field.name }>
									<MediaUpload
										onSelect={ ( media ) =>
											updateRow( index, {
												[ field.name ]: media.id,
												[ `${ field.name }Name` ]: media.filename || media.title || '',
											} )
										}
										allowedTypes={ field.mimeTypes || [] }
										value={ row[ field.name ] }
										render={ ( { open } ) => (
											<div className="cb-peoplesafe2027-repeater-field__image">
												{ row[ `${ field.name }Name` ] && (
													<span className="cb-peoplesafe2027-repeater-field__file-name">
														{ row[ `${ field.name }Name` ] }
													</span>
												) }
												<Button variant="secondary" size="small" onClick={ open }>
													{ row[ field.name ]
														? __( 'Replace', 'cb-peoplesafe2027' )
														: __( 'Select', 'cb-peoplesafe2027' ) }
												</Button>
											</div>
										) }
									/>
								</MediaUploadCheck>
							);
						}

						if ( 'textarea' === field.type ) {
							return (
								<TextareaControl
									key={ field.name }
									label={ field.label }
									hideLabelFromVision={ ! isColumn }
									value={ row[ field.name ] || '' }
									onChange={ ( v ) => updateRow( index, { [ field.name ]: v } ) }
									help={ field.help }
								/>
							);
						}

						if ( 'radio' === field.type ) {
							return (
								<RadioControl
									key={ field.name }
									label={ field.label }
									hideLabelFromVision={ ! isColumn }
									selected={ row[ field.name ] || '' }
									options={ field.options || [] }
									onChange={ ( v ) => updateRow( index, { [ field.name ]: v } ) }
								/>
							);
						}

						if ( 'number' === field.type ) {
							return (
								<TextControl
									key={ field.name }
									type="number"
									label={ field.label }
									hideLabelFromVision={ ! isColumn }
									value={ row[ field.name ] ?? '' }
									onChange={ ( v ) => updateRow( index, { [ field.name ]: '' === v ? '' : Number( v ) } ) }
									help={ field.help }
								/>
							);
						}

						return (
							<TextControl
								key={ field.name }
								label={ field.label }
								hideLabelFromVision={ ! isColumn }
								value={ row[ field.name ] || '' }
								onChange={ ( v ) => updateRow( index, { [ field.name ]: v } ) }
								help={ field.help }
							/>
						);
					} ) }
					<div className="cb-peoplesafe2027-repeater-field__row-actions">
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
						<Button
							size="small"
							isDestructive
							label={ __( 'Remove', 'cb-peoplesafe2027' ) }
							onClick={ () => confirmRemove( index ) }
						>
							&times;
						</Button>
					</div>
				</div>
			) ) }
			</div>
			<Button variant="secondary" onClick={ () => addRow( emptyRow ) }>
				{ __( 'Add row', 'cb-peoplesafe2027' ) }
			</Button>
		</div>
	);
}
