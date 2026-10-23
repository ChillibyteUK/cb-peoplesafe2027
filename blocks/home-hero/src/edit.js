import { __ } from '@wordpress/i18n';
import { useBlockProps, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { TextControl, Button } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RichField from '../../_shared/RichField';
import CtaButtonsField from '../../_shared/CtaButtonsField';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { title, wordOne, wordTwo, content, buttons, mobileImageId, mobileImageUrl, mobileImageAlt } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Home hero" textDomain="cb-peoplesafe2027">
			<TextControl
				label={ __( 'Title', 'cb-peoplesafe2027' ) }
				value={ title }
				onChange={ ( value ) => setAttributes( { title: value } ) }
			/>
			<div style={ { display: 'flex', flexWrap: 'wrap', gap: '12px' } }>
				<div style={ { flex: '1 1 0%' } }>
					<TextControl
						label={ __( 'Animated word 1', 'cb-peoplesafe2027' ) }
						value={ wordOne }
						onChange={ ( value ) => setAttributes( { wordOne: value } ) }
						help={ __( 'Fades in first, in the accent colour.', 'cb-peoplesafe2027' ) }
					/>
				</div>
				<div style={ { flex: '1 1 0%' } }>
					<TextControl
						label={ __( 'Animated word 2', 'cb-peoplesafe2027' ) }
						value={ wordTwo }
						onChange={ ( value ) => setAttributes( { wordTwo: value } ) }
						help={ __( 'Fades in second.', 'cb-peoplesafe2027' ) }
					/>
				</div>
			</div>
			<RichField
				label={ __( 'Intro', 'cb-peoplesafe2027' ) }
				value={ content }
				onChange={ ( value ) => setAttributes( { content: value } ) }
			/>
			<CtaButtonsField value={ buttons } onChange={ ( rows ) => setAttributes( { buttons: rows } ) } />
			<div className="cb-peoplesafe2027-editor-field">
				<label className="cb-peoplesafe2027-editor-field__label">{ __( 'Image for small screens', 'cb-peoplesafe2027' ) }</label>
				<MediaUploadCheck>
					<MediaUpload
						onSelect={ ( media ) =>
							setAttributes( {
								mobileImageId: media.id,
								mobileImageUrl: media.url,
								mobileImageAlt: media.alt || '',
							} )
						}
						allowedTypes={ [ 'image' ] }
						value={ mobileImageId }
						render={ ( { open } ) => (
							<div className="cb-peoplesafe2027-editor-field__control">
								{ mobileImageUrl && (
									<img
										src={ mobileImageUrl }
										alt={ mobileImageAlt }
										style={ { maxWidth: '200px', display: 'block', marginBottom: '8px' } }
									/>
								) }
								<Button variant="secondary" onClick={ open }>
									{ mobileImageUrl ? __( 'Replace image', 'cb-peoplesafe2027' ) : __( 'Select image', 'cb-peoplesafe2027' ) }
								</Button>
							</div>
						) }
					/>
				</MediaUploadCheck>
				<p className="cb-peoplesafe2027-editor-field__help">
					{ __( 'Shown instead of the device animation below 576px wide.', 'cb-peoplesafe2027' ) }
				</p>
			</div>
		</EditorBlockShell>
	);
}
