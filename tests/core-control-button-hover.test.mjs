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
	/has-skvn-button-hover/,
	'button-hover.php must add has-skvn-button-hover class on the wrapper',
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
	/wp_add_inline_style\s*\(\s*['"]skvn-marine-core-button-hover['"]/,
	'button-hover.php must emit frontend hover CSS via wp_add_inline_style on skvn-marine-core-button-hover',
);

assert.match(
	buttonHoverPhp,
	/wp_register_style\s*\(\s*['"]skvn-marine-core-button-hover['"]/,
	'button-hover.php must register skvn-marine-core-button-hover without a bundle file URL',
);

assert.doesNotMatch(
	buttonHoverPhp,
	/style-index\.ts\.css/,
	'button-hover.php must not enqueue the full plugin style bundle for hover alone',
);

assert.match(
	buttonHoverPhp,
	/skvn-marine-style/,
	'button-hover.php must depend on the theme skvn-marine-style handle for cascade order',
);

/**
 * Mirror PHP render transform for behavioral assertions (no WordPress runtime).
 */
function sanitizeHexColor( value ) {
	if ( ! value ) {
		return '';
	}

	return /^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test( value ) ? value : '';
}

function sanitizeHoverBg( raw ) {
	let bg = sanitizeHexColor( raw );

	if ( ! bg && /^(?:linear|radial|conic)-gradient\(/i.test( raw ) ) {
		const cleaned = raw.replace( /[^a-zA-Z0-9\s\-,#().%\/]+/g, '' );
		bg = /^(?:linear|radial|conic)-gradient\(/i.test( cleaned ) ? cleaned : '';
	}

	return bg;
}

function mockRenderButtonHover( blockContent, attrs ) {
	const hoverText = sanitizeHexColor( attrs.skvnHoverTextColor ?? '' );
	const hoverBg = sanitizeHoverBg( attrs.skvnHoverBgColor ?? '' );

	if ( ! hoverText && ! hoverBg ) {
		return blockContent;
	}

	const cssVars = [];

	if ( hoverText ) {
		cssVars.push( `--skvn-btn-hover-text:${ hoverText }` );
	}
	if ( hoverBg ) {
		cssVars.push( `--skvn-btn-hover-bg:${ hoverBg }` );
	}

	const styleAttr = `${ cssVars.join( ';' ) };`;

	return blockContent.replace(
		/(<div\s+class="([^"]*wp-block-button[^"]*)")(\s+style="([^"]*)")?/i,
		( _match, _open, classes, _stylePart, existing ) => {
			let updatedClasses = classes;

			if ( ! updatedClasses.includes( 'has-skvn-button-hover' ) ) {
				updatedClasses += ' has-skvn-button-hover';
			}

			let merged = existing ?? '';

			if ( merged !== '' && ! merged.trimEnd().endsWith( ';' ) ) {
				merged += ';';
			}

			merged += styleAttr;

			return `<div class="${ updatedClasses }" style="${ merged }"`;
		},
	);
}

const input =
	'<div class="wp-block-button skvn-button--primary"><a class="wp-block-button__link">Quote</a></div>';

const output = mockRenderButtonHover( input, {
	skvnHoverTextColor: '#ffffff',
	skvnHoverBgColor: '#112233',
} );

assert.match(
	output,
	/has-skvn-button-hover/,
	'render output must include has-skvn-button-hover class',
);

assert.match(
	output,
	/--skvn-btn-hover-text:#ffffff/,
	'render output must include --skvn-btn-hover-text inline var',
);

assert.match(
	output,
	/--skvn-btn-hover-bg:#112233/,
	'render output must include --skvn-btn-hover-bg inline var',
);

const gradientOutput = mockRenderButtonHover( input, {
	skvnHoverBgColor:
		'linear-gradient(135deg, #112233 0%, #445566 100%)',
} );

assert.match(
	gradientOutput,
	/--skvn-btn-hover-bg:linear-gradient\(135deg, #112233 0%, #445566 100%\)/,
	'render output must preserve sanitized gradient hover background',
);

const unchanged = mockRenderButtonHover( input, {} );

assert.equal(
	unchanged,
	input,
	'render output must be unchanged when no hover attrs are set',
);

console.log( 'core-control-button-hover.test.mjs: ok' );