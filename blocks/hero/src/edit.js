import { __ } from '@wordpress/i18n';
import { useBlockProps, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { TextControl, ToggleControl, Button } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RichField from '../../_shared/RichField';
import CtaButtonsField from '../../_shared/CtaButtonsField';
import useMediaUrl from '../../_shared/useMediaUrl';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { label, title, hideTitle, intro, buttons, imageId, imageUrl, hideBreadcrumbs } = attributes;
	const imagePreview = useMediaUrl( imageId, imageUrl );
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Hero" textDomain="cb-peoplesafe2027">
			<TextControl
				label={ __( 'Label', 'cb-peoplesafe2027' ) }
				value={ label }
				onChange={ ( value ) => setAttributes( { label: value } ) }
				help={ __( 'Optional small text above the title, for product pages (e.g. "Dashcam Integration"). It becomes the page heading (H1) and the title below it is shown as a large H2.', 'cb-peoplesafe2027' ) }
			/>
			<TextControl
				label={ __( 'Title', 'cb-peoplesafe2027' ) }
				value={ title }
				onChange={ ( value ) => setAttributes( { title: value } ) }
				help={ __( "Leave empty to use the page's own title.", 'cb-peoplesafe2027' ) }
			/>
			<ToggleControl
				label={ __( 'Hide the title', 'cb-peoplesafe2027' ) }
				checked={ hideTitle }
				onChange={ ( value ) => setAttributes( { hideTitle: value } ) }
			/>
			<RichField
				label={ __( 'Intro', 'cb-peoplesafe2027' ) }
				value={ intro }
				onChange={ ( value ) => setAttributes( { intro: value } ) }
			/>
			<CtaButtonsField value={ buttons } onChange={ ( rows ) => setAttributes( { buttons: rows } ) } />
			<div className="cb-peoplesafe2027-editor-field">
				<label className="cb-peoplesafe2027-editor-field__label">{ __( 'Image', 'cb-peoplesafe2027' ) }</label>
				<MediaUploadCheck>
					<MediaUpload
						onSelect={ ( media ) =>
							setAttributes( { imageId: media.id, imageUrl: media.url } )
						}
						allowedTypes={ [ 'image' ] }
						value={ imageId }
						render={ ( { open } ) => (
							<div className="cb-peoplesafe2027-editor-field__control">
								{ imagePreview && (
									<img src={ imagePreview } alt="" style={ { maxWidth: '200px', display: 'block', marginBottom: '8px' } } />
								) }
								<Button variant="secondary" onClick={ open }>
									{ imagePreview ? __( 'Replace image', 'cb-peoplesafe2027' ) : __( 'Select image', 'cb-peoplesafe2027' ) }
								</Button>
								{ imagePreview && (
									<Button
										variant="link"
										isDestructive
										onClick={ () => setAttributes( { imageId: 0, imageUrl: '' } ) }
										style={ { marginLeft: '12px' } }
									>
										{ __( 'Remove', 'cb-peoplesafe2027' ) }
									</Button>
								) }
							</div>
						) }
					/>
				</MediaUploadCheck>
				<p className="cb-peoplesafe2027-editor-field__help">
					{ __( "Leave empty to use the page's featured image.", 'cb-peoplesafe2027' ) }
				</p>
			</div>
			<ToggleControl
				label={ __( 'Hide the breadcrumbs', 'cb-peoplesafe2027' ) }
				checked={ hideBreadcrumbs }
				onChange={ ( value ) => setAttributes( { hideBreadcrumbs: value } ) }
				help={ __( 'For landing pages.', 'cb-peoplesafe2027' ) }
			/>
		</EditorBlockShell>
	);
}
