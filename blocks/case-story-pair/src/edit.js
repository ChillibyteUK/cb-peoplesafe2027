import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Button, TextControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import PostTypePicker from '../../_shared/PostTypePicker';

function PostList( { label, help, rows, max, onChange } ) {
	const list = rows || [];

	function update( index, id ) {
		const next = list.slice();
		next[ index ] = { id };
		onChange( next );
	}

	return (
		<div className="cb-peoplesafe2027-faq-rows">
			<label className="cb-peoplesafe2027-editor-field__label">{ label }</label>
			<p className="cb-peoplesafe2027-editor-field__help">{ help }</p>
			{ list.map( ( row, index ) => (
				<div className="cb-peoplesafe2027-related-items__row" key={ index }>
					<span className="cb-peoplesafe2027-repeater-field__number">{ index + 1 }</span>
					<div className="cb-peoplesafe2027-faq-rows__fields">
						<PostTypePicker label={ __( 'Item', 'cb-peoplesafe2027' ) } postType="case_study" value={ row.id } onChange={ ( id ) => update( index, id ) } />
					</div>
					<div className="cb-peoplesafe2027-cta-buttons__actions">
						<Button size="small" label={ __( 'Move up', 'cb-peoplesafe2027' ) } disabled={ 0 === index } onClick={ () => {
							const next = list.slice();
							[ next[ index - 1 ], next[ index ] ] = [ next[ index ], next[ index - 1 ] ];
							onChange( next );
						} }>
							&#9650;
						</Button>
						<Button size="small" label={ __( 'Move down', 'cb-peoplesafe2027' ) } disabled={ index === list.length - 1 } onClick={ () => {
							const next = list.slice();
							[ next[ index + 1 ], next[ index ] ] = [ next[ index ], next[ index + 1 ] ];
							onChange( next );
						} }>
							&#9660;
						</Button>
						<Button size="small" isDestructive label={ __( 'Remove', 'cb-peoplesafe2027' ) } onClick={ () => onChange( list.filter( ( r, i ) => i !== index ) ) }>
							&times;
						</Button>
					</div>
				</div>
			) ) }
			{ list.length < max && (
				<Button variant="secondary" onClick={ () => onChange( [ ...list, { id: 0 } ] ) }>
					{ __( 'Add', 'cb-peoplesafe2027' ) }
				</Button>
			) }
		</div>
	);
}

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { caseTitle, storyTitle, caseStudies, stories } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Case studies and stories" textDomain="cb-peoplesafe2027">
			<TextControl label={ __( 'Case studies title', 'cb-peoplesafe2027' ) } value={ caseTitle } onChange={ ( value ) => setAttributes( { caseTitle: value } ) } />
			<PostList
				label={ __( 'Case studies (up to 3)', 'cb-peoplesafe2027' ) }
				help={ __( 'Search by name. Shown as cards.', 'cb-peoplesafe2027' ) }
				rows={ caseStudies }
				max={ 3 }
				onChange={ ( rows ) => setAttributes( { caseStudies: rows } ) }
			/>
			<TextControl label={ __( 'Stories title', 'cb-peoplesafe2027' ) } value={ storyTitle } onChange={ ( value ) => setAttributes( { storyTitle: value } ) } />
			<PostList
				label={ __( 'User stories (up to 6)', 'cb-peoplesafe2027' ) }
				help={ __( 'Search by name (stories are in the same list as case studies). Shown one at a time, in full.', 'cb-peoplesafe2027' ) }
				rows={ stories }
				max={ 6 }
				onChange={ ( rows ) => setAttributes( { stories: rows } ) }
			/>
		</EditorBlockShell>
	);
}
