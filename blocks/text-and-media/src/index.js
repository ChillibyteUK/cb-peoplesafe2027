import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks } from '@wordpress/block-editor';
import Edit from './edit';
import metadata from '../block.json';

registerBlockType( metadata.name, {
	edit: Edit,
	// The content is nested blocks, which are saved with the block.
	save: () => <InnerBlocks.Content />,
} );
