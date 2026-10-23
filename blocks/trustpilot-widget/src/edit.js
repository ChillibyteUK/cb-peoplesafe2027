import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { SelectControl } from '@wordpress/components';
import EditorBlockShell from '../../_shared/EditorBlockShell';

export default function Edit( { attributes, setAttributes, clientId } ) {
	const { widget, card } = attributes;
	const blockProps = useBlockProps( { className: 'container cb-peoplesafe2027-editor-block' } );
	const configured = window.cbPeoplesafe2027Trustpilot || [];
	const options = [ { label: __( 'First widget in the settings', 'cb-peoplesafe2027' ), value: '' }, ...configured ];

	return (
		<EditorBlockShell blockProps={ blockProps } clientId={ clientId } title="Trustpilot widget" textDomain="cb-peoplesafe2027">
			<SelectControl
				label={ __( 'Widget', 'cb-peoplesafe2027' ) }
				value={ widget }
				options={ options }
				onChange={ ( value ) => setAttributes( { widget: value } ) }
				help={
					configured.length
						? __( 'The widgets are defined under Site-Wide Settings, Trustpilot.', 'cb-peoplesafe2027' )
						: __( 'No widgets are set up yet. Add them under Site-Wide Settings, Trustpilot.', 'cb-peoplesafe2027' )
				}
			/>
			<SelectControl
				label={ __( 'Card', 'cb-peoplesafe2027' ) }
				value={ card }
				options={ [
					{ label: __( "Follow the widget's setting", 'cb-peoplesafe2027' ), value: '' },
					{ label: __( 'On a card', 'cb-peoplesafe2027' ), value: 'yes' },
					{ label: __( 'No card', 'cb-peoplesafe2027' ), value: 'no' },
				] }
				onChange={ ( value ) => setAttributes( { card: value } ) }
				help={ __( 'A white rounded card, so the widget stands out from the page background.', 'cb-peoplesafe2027' ) }
			/>
		</EditorBlockShell>
	);
}
