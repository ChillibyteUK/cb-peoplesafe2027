import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Button, TextControl, ToggleControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import SiteUrlInput from '../../_shared/SiteUrlInput';
import ImagePicker from '../../_shared/ImagePicker';
import RichField from '../../_shared/RichField';
import useRowMotion from '../../_shared/useRowMotion';

const MIN_TABS = 2;
const MAX_TABS = 5;
const EMPTY_TAB = { title: '', icon: 0, iconUrl: '', preTitle: '', heading: '', content: '', linkText: '', link: '', linkTarget: false, image: 0, imageUrl: '' };

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { tabs } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );
	const rows = tabs || [];

	function updateRow( index, patch ) {
		const next = rows.slice();
		next[ index ] = { ...next[ index ], ...patch };
		setAttributes( { tabs: next } );
	}

	const { keys, setRowRef, movedKey, addRow, moveRow, removeRow } = useRowMotion( rows, ( next ) => setAttributes( { tabs: next } ) );

	function confirmRemove( index ) {
		// eslint-disable-next-line no-alert -- a plain confirm() is enough friction for an irreversible remove.
		if ( window.confirm( __( 'Remove this tab?', 'cb-peoplesafe2027' ) ) ) {
			removeRow( index );
		}
	}

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Top tabs" textDomain="cb-peoplesafe2027">
			<div className="cb-peoplesafe2027-faq-rows">
				<label className="cb-peoplesafe2027-editor-field__label">{ __( 'Tabs (2 to 5)', 'cb-peoplesafe2027' ) }</label>
				{ rows.map( ( row, index ) => (
					<div
						className={ 'cb-peoplesafe2027-related-items__row' + ( keys[ index ] === movedKey ? ' is-moved' : '' ) }
						key={ keys[ index ] }
						ref={ setRowRef( keys[ index ] ) }
					>
						<span className="cb-peoplesafe2027-repeater-field__number">{ index + 1 }</span>
						<div className="cb-peoplesafe2027-faq-rows__fields">
							<TextControl
								label={ __( 'Tab title', 'cb-peoplesafe2027' ) }
								value={ row.title || '' }
								onChange={ ( value ) => updateRow( index, { title: value } ) }
							/>
							<div className="cb-peoplesafe2027-editor-field">
								<span className="cb-peoplesafe2027-editor-field__label">{ __( 'Tab icon', 'cb-peoplesafe2027' ) }</span>
								<ImagePicker
									id={ row.icon }
									url={ row.iconUrl }
									size={ 56 }
									onSelect={ ( media ) => updateRow( index, { icon: media.id, iconUrl: media.url } ) }
									onRemove={ () => updateRow( index, { icon: 0, iconUrl: '' } ) }
								/>
							</div>
							<TextControl
								label={ __( 'Pre-title (optional)', 'cb-peoplesafe2027' ) }
								value={ row.preTitle || '' }
								onChange={ ( value ) => updateRow( index, { preTitle: value } ) }
							/>
							<TextControl
								label={ __( 'Title', 'cb-peoplesafe2027' ) }
								value={ row.heading || '' }
								onChange={ ( value ) => updateRow( index, { heading: value } ) }
							/>
							<RichField
								label={ __( 'Text', 'cb-peoplesafe2027' ) }
								value={ row.content }
								onChange={ ( value ) => updateRow( index, { content: value } ) }
							/>
							<div className="cb-peoplesafe2027-cta-buttons__pair">
								<TextControl
									label={ __( 'Button title (optional)', 'cb-peoplesafe2027' ) }
									value={ row.linkText || '' }
									onChange={ ( value ) => updateRow( index, { linkText: value } ) }
								/>
								<div className="cb-peoplesafe2027-cta-buttons__url">
									<span className="cb-peoplesafe2027-editor-field__label">{ __( 'Button URL', 'cb-peoplesafe2027' ) }</span>
									<SiteUrlInput
										value={ row.link }
										onChange={ ( url ) => updateRow( index, { link: url } ) }
										placeholder={ __( 'Search pages and posts, or type a URL', 'cb-peoplesafe2027' ) }
									/>
									<ToggleControl
										label={ __( 'Open in a new tab', 'cb-peoplesafe2027' ) }
										checked={ !! row.linkTarget }
										onChange={ ( value ) => updateRow( index, { linkTarget: value } ) }
										__nextHasNoMarginBottom
									/>
								</div>
							</div>
							<div className="cb-peoplesafe2027-editor-field">
								<span className="cb-peoplesafe2027-editor-field__label">{ __( 'Image', 'cb-peoplesafe2027' ) }</span>
								<ImagePicker
									id={ row.image }
									url={ row.imageUrl }
									onSelect={ ( media ) => updateRow( index, { image: media.id, imageUrl: media.url } ) }
									onRemove={ () => updateRow( index, { image: 0, imageUrl: '' } ) }
								/>
							</div>
						</div>
						<div className="cb-peoplesafe2027-cta-buttons__actions">
							<Button size="small" label={ __( 'Move up', 'cb-peoplesafe2027' ) } onClick={ () => moveRow( index, -1 ) } disabled={ 0 === index }>
								&#9650;
							</Button>
							<Button size="small" label={ __( 'Move down', 'cb-peoplesafe2027' ) } onClick={ () => moveRow( index, 1 ) } disabled={ index === rows.length - 1 }>
								&#9660;
							</Button>
							<Button size="small" isDestructive label={ __( 'Remove', 'cb-peoplesafe2027' ) } onClick={ () => confirmRemove( index ) } disabled={ rows.length <= MIN_TABS }>
								&times;
							</Button>
						</div>
					</div>
				) ) }
				{ rows.length < MAX_TABS && (
					<Button variant="secondary" onClick={ () => addRow( EMPTY_TAB ) }>
						{ __( 'Add tab', 'cb-peoplesafe2027' ) }
					</Button>
				) }
			</div>
		</EditorBlockShell>
	);
}
