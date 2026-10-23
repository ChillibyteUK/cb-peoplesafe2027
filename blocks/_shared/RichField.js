import { RichText } from '@wordpress/block-editor';

/**
 * Paragraphs stored as HTML with <br> between them ("Enter" in the field adds a
 * <br>), shown in the editor as that. Older values stored as <p>…</p> are
 * turned back into the same form so they edit cleanly.
 *
 * @param {string} html Stored value.
 * @return {string} Value to edit.
 */
function toEditable( html ) {
	return ( html || '' )
		.replace( /<\/p>\s*<p[^>]*>/gi, '<br>' )
		.replace( /^\s*<p[^>]*>/i, '' )
		.replace( /<\/p>\s*$/i, '' );
}

/**
 * A labelled rich-text field (bold, italic, links) for a block's edit form.
 *
 * It is one plain rich-text field, not WordPress's multi-line mode (which
 * re-parses the field on every keystroke and sends the cursor back to the
 * start). Enter starts a new line; the page turns each line into a paragraph
 * (see cb_peoplesafe2027_rich_paragraphs()). The format buttons go in the
 * field's own floating toolbar, so a block with several fields does not stack a
 * copy of them in the block toolbar.
 *
 * @param {Object}   props
 * @param {string}   props.label
 * @param {string}   props.value
 * @param {Function} props.onChange
 * @param {string}   [props.placeholder]
 * @param {string[]} [props.allowedFormats] Formats offered (default bold, italic and link).
 */
export default function RichField( { label, value, onChange, placeholder, allowedFormats = [ 'core/bold', 'core/italic', 'core/link' ] } ) {
	return (
		<div className="cb-peoplesafe2027-editor-field">
			<span className="cb-peoplesafe2027-editor-field__label">{ label }</span>
			<div className="cb-peoplesafe2027-editor-field__control">
				<RichText
					tagName="div"
					aria-label={ label }
					placeholder={ placeholder || label }
					allowedFormats={ allowedFormats }
					inlineToolbar
					value={ toEditable( value ) }
					onChange={ onChange }
				/>
			</div>
		</div>
	);
}
