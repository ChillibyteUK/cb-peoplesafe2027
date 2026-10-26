import { __ } from '@wordpress/i18n';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button } from '@wordpress/components';

/**
 * One file from the media library: its name with Select / Replace and Remove.
 *
 * @param {Object}   props
 * @param {number}   props.id         Media ID, or 0 for none.
 * @param {string}   [props.name]     File name saved with the block, shown while set.
 * @param {string[]} [props.mimeTypes] Allowed types, e.g. [ 'application/pdf' ].
 * @param {Function} props.onSelect   ( media ) => void
 * @param {Function} props.onRemove   () => void
 */
export default function FilePicker( { id, name, mimeTypes, onSelect, onRemove } ) {
	return (
		<MediaUploadCheck>
			<MediaUpload
				onSelect={ onSelect }
				allowedTypes={ mimeTypes || [] }
				value={ id }
				render={ ( { open } ) => (
					<div className="cb-peoplesafe2027-image-picker">
						{ id ? <span>{ name || `#${ id }` }</span> : null }
						<div className="cb-peoplesafe2027-image-picker__actions">
							<Button variant="secondary" size="small" onClick={ open }>
								{ id ? __( 'Replace', 'cb-peoplesafe2027' ) : __( 'Select file', 'cb-peoplesafe2027' ) }
							</Button>
							{ id ? (
								<Button variant="link" size="small" isDestructive onClick={ onRemove }>
									{ __( 'Remove', 'cb-peoplesafe2027' ) }
								</Button>
							) : null }
						</div>
					</div>
				) }
			/>
		</MediaUploadCheck>
	);
}
