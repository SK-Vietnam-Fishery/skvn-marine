import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';

const root = resolve(import.meta.dirname, '..');
const templatePhp = readFileSync(
	resolve(root, 'wp-content/themes/skvn-marine/template-skvn-full-width.php'),
	'utf8',
);
const styleCss = readFileSync(
	resolve(root, 'wp-content/themes/skvn-marine/style.css'),
	'utf8',
);

assert.match(
	templatePhp,
	/Template Name:\s*SKVN Full Width/,
	'full-width page template must be selectable in the page editor',
);
assert.match(
	templatePhp,
	/get_header\(\)/,
	'full-width template must keep the GeneratePress/header layer',
);
assert.match(
	templatePhp,
	/get_footer\(\)/,
	'full-width template must keep the GeneratePress/footer layer',
);
assert.match(
	templatePhp,
	/skvn-full-width-template/,
	'full-width template must expose a stable layout class',
);
assert.match(
	templatePhp,
	/the_content\(\)/,
	'full-width template must render Gutenberg page content',
);
assert.doesNotMatch(
	templatePhp,
	/themes\/generatepress|generatepress\//i,
	'full-width template must not reference GeneratePress parent internals directly',
);

for (const className of [
	'.skvn-full-width-template',
	'.skvn-full-width-content',
	'.skvn-full-width-content > .alignfull',
]) {
	assert.ok(styleCss.includes(className), `style.css missing ${className}`);
}

assert.match(
	styleCss,
	/\.skvn-full-width-content\s*>\s*\.alignfull[\s\S]*width:\s*100vw/,
	'alignfull sections inside SKVN Full Width must be able to reach viewport width',
);
assert.match(
	styleCss,
	/\.skvn-full-width-content\s*>\s*\.alignwide[\s\S]*max-width:\s*var\(--skvn-wide-width\)/,
	'alignwide sections inside SKVN Full Width must stay constrained to SKVN wide width',
);
assert.match(
	styleCss,
	/\.skvn-full-width-template[\s\S]*overflow-x:\s*clip/,
	'full-width template must guard against horizontal scroll',
);
