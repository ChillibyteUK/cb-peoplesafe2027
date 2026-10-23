import { __, sprintf } from '@wordpress/i18n';
import { useBlockProps, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { Button, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';
import useMediaUrl from '../../_shared/useMediaUrl';

/** One logo thumbnail in the gallery, with move left / right and remove. */
function LogoTile( { logo, index, count, onMove, onRemove } ) {
	const preview = useMediaUrl( logo.logo, logo.logoUrl );

	return (
		<li className="cb-peoplesafe2027-logo-gallery__tile">
			{ preview ? <img src={ preview } alt="" /> : <span className="cb-peoplesafe2027-logo-gallery__missing">?</span> }
			<span className="cb-peoplesafe2027-logo-gallery__actions">
				<Button size="small" label={ __( 'Move earlier', 'cb-peoplesafe2027' ) } onClick={ () => onMove( index, -1 ) } disabled={ 0 === index }>
					&#9664;
				</Button>
				<Button size="small" label={ __( 'Move later', 'cb-peoplesafe2027' ) } onClick={ () => onMove( index, 1 ) } disabled={ index === count - 1 }>
					&#9654;
				</Button>
				<Button size="small" isDestructive label={ __( 'Remove', 'cb-peoplesafe2027' ) } onClick={ () => onRemove( index ) }>
					&times;
				</Button>
			</span>
		</li>
	);
}

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { title, logos, autoplay, caseStudies } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );
	const rows = logos || [];

	const sectors = useSelect(
		( select ) => select( coreStore ).getEntityRecords( 'taxonomy', 'sector', { per_page: 100 } ),
		[]
	);

	function move( index, direction ) {
		const next = rows.slice();
		const target = index + direction;

		[ next[ index ], next[ target ] ] = [ next[ target ], next[ index ] ];
		setAttributes( { logos: next } );
	}

	function remove( index ) {
		setAttributes( { logos: rows.filter( ( row, i ) => i !== index ) } );
	}

	// The media library hands back the whole selection each time: keep the order already set, add the new ones at the end.
	function select( medias ) {
		const chosen = medias.map( ( media ) => ( { logo: media.id, logoUrl: media.url } ) );
		const kept = rows.filter( ( row ) => chosen.some( ( c ) => c.logo === row.logo ) );
		const added = chosen.filter( ( c ) => ! rows.some( ( row ) => row.logo === c.logo ) );

		setAttributes( { logos: [ ...kept, ...added ] } );
	}

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Logo slider" textDomain="cb-peoplesafe2027">
			<TextControl
				label={ __( 'Title', 'cb-peoplesafe2027' ) }
				value={ title }
				onChange={ ( value ) => setAttributes( { title: value } ) }
				help={ __( 'Leave empty for "Some of our clients".', 'cb-peoplesafe2027' ) }
			/>
			<div className="cb-peoplesafe2027-editor-field">
				<span className="cb-peoplesafe2027-editor-field__label">{ __( 'Logos', 'cb-peoplesafe2027' ) }</span>
				{ rows.length > 0 && (
					<ul className="cb-peoplesafe2027-logo-gallery">
						{ rows.map( ( logo, index ) => (
							<LogoTile key={ logo.logo } logo={ logo } index={ index } count={ rows.length } onMove={ move } onRemove={ remove } />
						) ) }
					</ul>
				) }
				<MediaUploadCheck>
					<MediaUpload
						onSelect={ select }
						allowedTypes={ [ 'image' ] }
						multiple="add"
						gallery
						value={ rows.map( ( row ) => row.logo ) }
						render={ ( { open } ) => (
							<Button variant="secondary" onClick={ open }>
								{ rows.length ? __( 'Add or remove logos', 'cb-peoplesafe2027' ) : __( 'Select logos', 'cb-peoplesafe2027' ) }
							</Button>
						) }
					/>
				</MediaUploadCheck>
				<p className="cb-peoplesafe2027-editor-field__help">
					{ __( "Pick several logos at once in the media library. Each logo's alt text comes from the library: give it the client's name there. Square images work best.", 'cb-peoplesafe2027' ) }
				</p>
			</div>
			<ToggleControl
				label={ __( 'Rotate automatically', 'cb-peoplesafe2027' ) }
				checked={ autoplay }
				onChange={ ( value ) => setAttributes( { autoplay: value } ) }
				help={ __( 'Only applies when there are more logos than fit across. It pauses while the pointer is on it, and is off for anyone who prefers reduced motion. There are no arrows or dots.', 'cb-peoplesafe2027' ) }
			/>
			<SelectControl
				label={ __( 'Case studies button', 'cb-peoplesafe2027' ) }
				value={ caseStudies }
				options={ [
					{ label: __( 'No button', 'cb-peoplesafe2027' ), value: '' },
					{ label: __( 'All case studies', 'cb-peoplesafe2027' ), value: 'all' },
					...( sectors || [] ).map( ( term ) => ( {
						/* translators: %s: sector name */
						label: sprintf( __( 'Case studies in %s', 'cb-peoplesafe2027' ), term.name ),
						value: term.slug,
					} ) ),
				] }
				onChange={ ( value ) => setAttributes( { caseStudies: value } ) }
				help={ __( 'A "View our case studies" button under the logos.', 'cb-peoplesafe2027' ) }
			/>
		</EditorBlockShell>
	);
}
