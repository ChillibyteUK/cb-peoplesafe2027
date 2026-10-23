import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { Button, RangeControl, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import PostTypePicker from '../../_shared/PostTypePicker';
import useRowMotion from '../../_shared/useRowMotion';

const TYPES = [
	{ label: __( 'Products', 'cb-peoplesafe2027' ), value: 'product' },
	{ label: __( 'Case studies', 'cb-peoplesafe2027' ), value: 'case_study' },
	{ label: __( 'Resources', 'cb-peoplesafe2027' ), value: 'resource' },
	{ label: __( 'Posts', 'cb-peoplesafe2027' ), value: 'post' },
];

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { title, hideTitle, source, items, postType, count, sector } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );
	const rows = items || [];

	const sectors = useSelect(
		( select ) => select( coreStore ).getEntityRecords( 'taxonomy', 'sector', { per_page: 100 } ),
		[]
	);

	function updateRow( index, patch ) {
		const next = rows.slice();
		next[ index ] = { ...next[ index ], ...patch };
		setAttributes( { items: next } );
	}

	const { keys, setRowRef, movedKey, addRow, moveRow, removeRow } = useRowMotion( rows, ( next ) => setAttributes( { items: next } ) );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Related content" textDomain="cb-peoplesafe2027">
			<SelectControl
				label={ __( 'Show', 'cb-peoplesafe2027' ) }
				value={ source }
				options={ [
					{ label: __( 'Items I choose', 'cb-peoplesafe2027' ), value: 'curated' },
					{ label: __( 'The latest of one type', 'cb-peoplesafe2027' ), value: 'latest' },
				] }
				onChange={ ( value ) => setAttributes( { source: value } ) }
			/>
			<TextControl
				label={ __( 'Title', 'cb-peoplesafe2027' ) }
				value={ title }
				onChange={ ( value ) => setAttributes( { title: value } ) }
				help={ __( 'Leave empty for a default such as "Related products".', 'cb-peoplesafe2027' ) }
			/>
			<ToggleControl
				label={ __( 'Hide the title', 'cb-peoplesafe2027' ) }
				checked={ hideTitle }
				onChange={ ( value ) => setAttributes( { hideTitle: value } ) }
			/>
			{ 'latest' === source ? (
				<>
					<SelectControl
						label={ __( 'Type', 'cb-peoplesafe2027' ) }
						value={ postType }
						options={ TYPES }
						onChange={ ( value ) => setAttributes( { postType: value, sector: 0 } ) }
					/>
					<RangeControl
						label={ __( 'How many', 'cb-peoplesafe2027' ) }
						value={ count }
						min={ 1 }
						max={ 24 }
						onChange={ ( value ) => setAttributes( { count: value } ) }
					/>
					{ 'case_study' === postType && (
						<SelectControl
							label={ __( 'Sector', 'cb-peoplesafe2027' ) }
							value={ sector }
							options={ [
								{ label: __( 'All sectors', 'cb-peoplesafe2027' ), value: 0 },
								...( sectors || [] ).map( ( term ) => ( { label: term.name, value: term.id } ) ),
							] }
							onChange={ ( value ) => setAttributes( { sector: Number( value ) } ) }
						/>
					) }
				</>
			) : (
				<div className="cb-peoplesafe2027-related-items">
					<label className="cb-peoplesafe2027-editor-field__label">{ __( 'Items', 'cb-peoplesafe2027' ) }</label>
					{ rows.map( ( row, index ) => (
						<div
							className={ 'cb-peoplesafe2027-related-items__row' + ( keys[ index ] === movedKey ? ' is-moved' : '' ) }
							key={ keys[ index ] }
							ref={ setRowRef( keys[ index ] ) }
						>
							<span className="cb-peoplesafe2027-repeater-field__number">{ index + 1 }</span>
							<SelectControl
								label={ __( 'Type', 'cb-peoplesafe2027' ) }
								value={ row.type || 'product' }
								options={ TYPES }
								onChange={ ( value ) => updateRow( index, { type: value, id: 0 } ) }
							/>
							<PostTypePicker
								label={ __( 'Item', 'cb-peoplesafe2027' ) }
								postType={ row.type || 'product' }
								value={ row.id }
								onChange={ ( id ) => updateRow( index, { id } ) }
							/>
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
					<Button variant="secondary" onClick={ () => addRow( { type: 'product', id: 0 } ) }>
						{ __( 'Add item', 'cb-peoplesafe2027' ) }
					</Button>
				</div>
			) }
		</EditorBlockShell>
	);
}
