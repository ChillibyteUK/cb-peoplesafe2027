import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { TextControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import RichField from '../../_shared/RichField';
import ImagePicker from '../../_shared/ImagePicker';
import SiteUrlInput from '../../_shared/SiteUrlInput';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { title, content, buttonLabel, signupUrl, imageId, imageUrl } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Newsletter signup" textDomain="cb-peoplesafe2027">
			<TextControl label={ __( 'Title', 'cb-peoplesafe2027' ) } value={ title } onChange={ ( value ) => setAttributes( { title: value } ) } />
			<RichField label={ __( 'Text', 'cb-peoplesafe2027' ) } value={ content } onChange={ ( value ) => setAttributes( { content: value } ) } />
			<TextControl label={ __( 'Button text', 'cb-peoplesafe2027' ) } value={ buttonLabel } onChange={ ( value ) => setAttributes( { buttonLabel: value } ) } help={ __( 'Leave empty for "Sign up".', 'cb-peoplesafe2027' ) } />
			<div className="cb-peoplesafe2027-editor-field">
				<span className="cb-peoplesafe2027-editor-field__label">{ __( 'Signup page (optional)', 'cb-peoplesafe2027' ) }</span>
				<SiteUrlInput value={ signupUrl } onChange={ ( value ) => setAttributes( { signupUrl: value } ) } placeholder="/newsletter-signup/" />
				<p className="cb-peoplesafe2027-editor-field__help">{ __( 'Where the email address goes. Leave empty for the newsletter signup page.', 'cb-peoplesafe2027' ) }</p>
			</div>
			<div className="cb-peoplesafe2027-editor-field">
				<span className="cb-peoplesafe2027-editor-field__label">{ __( 'Image', 'cb-peoplesafe2027' ) }</span>
				<ImagePicker
					id={ imageId }
					url={ imageUrl }
					onSelect={ ( media ) => setAttributes( { imageId: media.id, imageUrl: media.url } ) }
					onRemove={ () => setAttributes( { imageId: 0, imageUrl: '' } ) }
				/>
			</div>
		</EditorBlockShell>
	);
}
