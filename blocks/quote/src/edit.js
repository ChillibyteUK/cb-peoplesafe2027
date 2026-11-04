import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Button, TextareaControl, TextControl } from '@wordpress/components';
import CtaButtonsField from '../../_shared/CtaButtonsField';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import ImagePicker from '../../_shared/ImagePicker';
import useRowMotion from '../../_shared/useRowMotion';

const EMPTY_QUOTE = { quote: '', attribution: '', logo: 0, logoUrl: '', image: 0, imageUrl: '' };

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { quotes, preTitle, title, intro, buttons } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );
	const rows = quotes || [];

	function updateRow( index, patch ) {
		const next = rows.slice();
		next[ index ] = { ...next[ index ], ...patch };
		setAttributes( { quotes: next } );
	}

	const { keys, setRowRef, movedKey, addRow, moveRow, removeRow } = useRowMotion( rows, ( next ) => setAttributes( { quotes: next } ) );

	function confirmRemove( index ) {
		// eslint-disable-next-line no-alert -- a plain confirm() is enough friction for an irreversible remove.
		if ( window.confirm( __( 'Remove this quote?', 'cb-peoplesafe2027' ) ) ) {
			removeRow( index );
		}
	}

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Quote" textDomain="cb-peoplesafe2027">
			<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
				<div style={ { flex: '1 1 0%' } }>
					<TextControl label={ __( 'Pre-title (optional)', 'cb-peoplesafe2027' ) } value={ preTitle } onChange={ ( value ) => setAttributes( { preTitle: value } ) } />
				</div>
				<div style={ { flex: '2 1 0%' } }>
					<TextControl label={ __( 'Title (optional)', 'cb-peoplesafe2027' ) } value={ title } onChange={ ( value ) => setAttributes( { title: value } ) } />
				</div>
			</div>
			<TextareaControl
				label={ __( 'Text (optional)', 'cb-peoplesafe2027' ) }
				rows={ 3 }
				value={ intro }
				onChange={ ( value ) => setAttributes( { intro: value } ) }
				help={ __( 'With a pre-title, title, text or button, they sit in a column beside the quotes.', 'cb-peoplesafe2027' ) }
			/>
			<CtaButtonsField value={ buttons } onChange={ ( rows ) => setAttributes( { buttons: rows } ) } />
			<div className="cb-peoplesafe2027-faq-rows">
				<label className="cb-peoplesafe2027-editor-field__label">{ __( 'Quotes', 'cb-peoplesafe2027' ) }</label>
				{ rows.map( ( row, index ) => (
					<div
						className={ 'cb-peoplesafe2027-related-items__row' + ( keys[ index ] === movedKey ? ' is-moved' : '' ) }
						key={ keys[ index ] }
						ref={ setRowRef( keys[ index ] ) }
					>
						<span className="cb-peoplesafe2027-repeater-field__number">{ index + 1 }</span>
						<div className="cb-peoplesafe2027-faq-rows__fields">
							<TextareaControl
								label={ __( 'Quote', 'cb-peoplesafe2027' ) }
								rows={ 4 }
								value={ row.quote || '' }
								onChange={ ( value ) => updateRow( index, { quote: value } ) }
							/>
							<TextControl
								label={ __( 'Attribution', 'cb-peoplesafe2027' ) }
								value={ row.attribution || '' }
								onChange={ ( value ) => updateRow( index, { attribution: value } ) }
								help={ __( 'Name, role and organisation.', 'cb-peoplesafe2027' ) }
							/>
							<div className="cb-peoplesafe2027-image-pair">
								<div>
									<span className="cb-peoplesafe2027-editor-field__label">{ __( 'Logo (optional)', 'cb-peoplesafe2027' ) }</span>
									<ImagePicker
										id={ row.logo }
										url={ row.logoUrl }
										onSelect={ ( media ) => updateRow( index, { logo: media.id, logoUrl: media.url } ) }
										onRemove={ () => updateRow( index, { logo: 0, logoUrl: '' } ) }
									/>
								</div>
								<div>
									<span className="cb-peoplesafe2027-editor-field__label">{ __( 'Photo (optional)', 'cb-peoplesafe2027' ) }</span>
									<ImagePicker
										id={ row.image }
										url={ row.imageUrl }
										onSelect={ ( media ) => updateRow( index, { image: media.id, imageUrl: media.url } ) }
										onRemove={ () => updateRow( index, { image: 0, imageUrl: '' } ) }
									/>
								</div>
							</div>
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
				<Button variant="secondary" onClick={ () => addRow( EMPTY_QUOTE ) }>
					{ __( 'Add quote', 'cb-peoplesafe2027' ) }
				</Button>
			</div>
			<p className="cb-peoplesafe2027-editor-field__help">
				{ __( 'One quote shows as a panel; two or more become a slider. The panel colour cycles blue, orange, pink.', 'cb-peoplesafe2027' ) }
			</p>
		</EditorBlockShell>
	);
}
