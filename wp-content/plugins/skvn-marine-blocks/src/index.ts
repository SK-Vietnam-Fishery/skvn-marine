import { registerBlockType } from '@wordpress/blocks';

import accordionMetadata from './accordion/block.json';
import { Edit as AccordionEdit } from './accordion/edit';
import { save as accordionSave } from './accordion/save';

import slideMetadata from './slide/block.json';
import { Edit as SlideEdit } from './slide/edit';
import { save as slideSave } from './slide/save';

import sliderMetadata from './slider/block.json';
import { Edit as SliderEdit } from './slider/edit';
import { save as sliderSave } from './slider/save';

registerBlockType(accordionMetadata.name, {
	...accordionMetadata,
	edit: AccordionEdit,
	save: accordionSave,
});

registerBlockType(slideMetadata.name, {
	...slideMetadata,
	edit: SlideEdit,
	save: slideSave,
});

registerBlockType(sliderMetadata.name, {
	...sliderMetadata,
	edit: SliderEdit,
	save: sliderSave,
});
