import { __ } from '@wordpress/i18n';
import { useBlockProps, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { TextControl, SelectControl, Button } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RichField from '../../_shared/RichField';
import CtaButtonsField from '../../_shared/CtaButtonsField';
import useMediaUrl from '../../_shared/useMediaUrl';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { theme, title, content, buttons, imageId, imageUrl } = attributes;
	const imagePreview = useMediaUrl( imageId, imageUrl );
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="CTA banner" textDomain="cb-peoplesafe2027">
			<SelectControl
				label={ __( 'Colour', 'cb-peoplesafe2027' ) }
				value={ theme }
				options={ [
					{ label: __( 'Orange', 'cb-peoplesafe2027' ), value: 'orange' },
					{ label: __( 'Pink', 'cb-peoplesafe2027' ), value: 'pink' },
					{ label: __( 'Blue', 'cb-peoplesafe2027' ), value: 'blue' },
					{ label: __( 'Plain', 'cb-peoplesafe2027' ), value: 'plain' },
				] }
				onChange={ ( value ) => setAttributes( { theme: value } ) }
			/>
			<TextControl
				label={ __( 'Title', 'cb-peoplesafe2027' ) }
				value={ title }
				onChange={ ( value ) => setAttributes( { title: value } ) }
			/>
			<RichField
				label={ __( 'Text', 'cb-peoplesafe2027' ) }
				value={ content }
				onChange={ ( value ) => setAttributes( { content: value } ) }
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
					{ __( 'Optional. Shown on the left from the large breakpoint up; hidden on phones and tablets.', 'cb-peoplesafe2027' ) }
				</p>
			</div>
		</EditorBlockShell>
	);
}
