import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { useDebounce } from '@wordpress/compose';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
import { ComboboxControl, TextControl, ToggleControl } from '@wordpress/components';
import { decodeEntities } from '@wordpress/html-entities';

/**
 * A link to either a post (any public post type, found by searching) or a URL.
 * A post is stored by ID, so the front end can swap it for its translation in
 * the visitor's region; a path such as /products/ is kept as typed. Choosing
 * one clears the other.
 *
 * @param {Object}   props
 * @param {string}   [props.label]     Field label.
 * @param {Object}   props.value       { post, postTitle, url, target }
 * @param {Function} props.onChange    ( link ) => void
 * @param {boolean}  [props.showTarget] Offer "open in a new tab".
 */
export default function LinkTarget( { label, value, onChange, showTarget = true } ) {
	const link = value || {};
	const [ results, setResults ] = useState( [] );

	const search = useDebounce( ( input ) => {
		apiFetch( {
			path: addQueryArgs( '/wp/v2/search', { search: input, per_page: 15, type: 'post', subtype: 'any' } ),
		} )
			.then( ( found ) => setResults( found.map( ( post ) => ( { value: post.id, label: decodeEntities( post.title ) || `#${ post.id }` } ) ) ) )
			.catch( () => setResults( [] ) );
	}, 300 );

	const options = results.slice();

	if ( link.post && ! options.some( ( option ) => option.value === link.post ) ) {
		options.unshift( { value: link.post, label: link.postTitle || `#${ link.post }` } );
	}

	return (
		<div className="cb-peoplesafe2027-link-target">
			<ComboboxControl
				label={ label ? `${ label }: ${ __( 'page', 'cb-peoplesafe2027' ) }` : __( 'Page', 'cb-peoplesafe2027' ) }
				value={ link.post || null }
				options={ options }
				onFilterValueChange={ search }
				onChange={ ( id ) => {
					const picked = options.find( ( option ) => option.value === Number( id ) );
					onChange( { ...link, post: id ? Number( id ) : 0, postTitle: picked ? picked.label : '', url: id ? '' : link.url } );
				} }
			/>
			<TextControl
				label={ __( 'or URL / path', 'cb-peoplesafe2027' ) }
				value={ link.url || '' }
				onChange={ ( url ) => onChange( { ...link, url, post: url ? 0 : link.post, postTitle: url ? '' : link.postTitle } ) }
				help={ __( 'A path such as /products/ follows the visitor\'s region. A full URL is used as typed.', 'cb-peoplesafe2027' ) }
			/>
			{ showTarget && (
				<ToggleControl
					label={ __( 'Open in a new tab', 'cb-peoplesafe2027' ) }
					checked={ !! link.target }
					onChange={ ( target ) => onChange( { ...link, target } ) }
				/>
			) }
		</div>
	);
}
