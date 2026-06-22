import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';

const root = resolve( import.meta.dirname, '..' );
const buttonHoverPhp = readFileSync(
	resolve(
		root,
		'wp-content/plugins/skvn-marine-blocks/modules/core-control/features/button-hover.php',
	),
	'utf8',
);

assert.doesNotMatch(
	buttonHoverPhp,
	/return\s+\$inline_style\s*\.\s*\$block_content/,
	'button-hover.php must not prepend a <style> tag into button markup',
);

assert.match(
	buttonHoverPhp,
	/--skvn-btn-hover-text:/,
	'button-hover.php must define --skvn-btn-hover-text on the wrapper scope',
);

assert.match(
	buttonHoverPhp,
	/--skvn-btn-hover-bg:/,
	'button-hover.php must define --skvn-btn-hover-bg on the wrapper scope',
);

assert.match(
	buttonHoverPhp,
	/style="/,
	'button-hover.php must inject hover vars via inline style on .wp-block-button',
);

assert.match(
	buttonHoverPhp,
	/skvn_marine_blocks_enqueue_button_hover_frontend_style/,
	'button-hover.php must enqueue shared frontend hover rules',
);

console.log( 'core-control-button-hover.test.mjs: ok' );