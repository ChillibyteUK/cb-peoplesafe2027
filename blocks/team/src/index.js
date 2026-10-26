import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { Button, SelectControl, TextControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import PostTypePicker from '../../_shared/PostTypePicker';
import metadata from '../block.json';

function move( rows, index, by ) {
	const next = rows.slice();
	const [ row ] = next.splice( index, 1 );
	next.splice( index + by, 0, row );
	return next;
}

function Edit( { attributes, setAttributes, clientId } ) {
	const { layout, title, people } = attributes;
	const rows = people || [];
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Team" textDomain="cb-peoplesafe2027">
			<SelectControl
				label={ __( 'Show people as', 'cb-peoplesafe2027' ) }
				value={ layout }
				options={ [
					{ value: 'profiles', label: __( 'Profiles: photo beside name, role and bio', 'cb-peoplesafe2027' ) },
					{ value: 'cards', label: __( 'Photo cards: photo with name and role', 'cb-peoplesafe2027' ) },
				] }
				onChange={ ( value ) => setAttributes( { layout: value } ) }
			/>
			<TextControl label={ __( 'Title', 'cb-peoplesafe2027' ) } value={ title } onChange={ ( value ) => setAttributes( { title: value } ) } help={ __( 'Optional heading above the people.', 'cb-peoplesafe2027' ) } />
			<div className="cb-peoplesafe2027-faq-rows">
				<label className="cb-peoplesafe2027-editor-field__label">{ __( 'People, in order', 'cb-peoplesafe2027' ) }</label>
				{ rows.map( ( row, index ) => (
					<div className="cb-peoplesafe2027-related-items__row" key={ index }>
						<span className="cb-peoplesafe2027-repeater-field__number">{ index + 1 }</span>
						<div className="cb-peoplesafe2027-faq-rows__fields">
							<PostTypePicker
								label={ __( 'Person', 'cb-peoplesafe2027' ) }
								postType="person"
								value={ row.id }
								onChange={ ( id ) => setAttributes( { people: rows.map( ( r, i ) => ( i === index ? { id } : r ) ) } ) }
							/>
						</div>
						<div className="cb-peoplesafe2027-repeater-field__row-actions">
							<Button size="small" label={ __( 'Move up', 'cb-peoplesafe2027' ) } disabled={ 0 === index } onClick={ () => setAttributes( { people: move( rows, index, -1 ) } ) }>&#9650;</Button>
							<Button size="small" label={ __( 'Move down', 'cb-peoplesafe2027' ) } disabled={ index === rows.length - 1 } onClick={ () => setAttributes( { people: move( rows, index, 1 ) } ) }>&#9660;</Button>
							<Button size="small" isDestructive label={ __( 'Remove', 'cb-peoplesafe2027' ) } onClick={ () => setAttributes( { people: rows.filter( ( r, i ) => i !== index ) } ) }>&times;</Button>
						</div>
					</div>
				) ) }
				<Button variant="secondary" onClick={ () => setAttributes( { people: [ ...rows, { id: 0 } ] } ) }>
					{ __( 'Add person', 'cb-peoplesafe2027' ) }
				</Button>
				<p className="cb-peoplesafe2027-editor-field__help">{ __( 'People are added under People in the admin menu: name, photo, role and bio.', 'cb-peoplesafe2027' ) }</p>
			</div>
		</EditorBlockShell>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => null,
} );
