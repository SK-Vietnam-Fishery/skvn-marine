import assert from 'node:assert/strict';
import { existsSync, readFileSync, readdirSync } from 'node:fs';
import { resolve } from 'node:path';

const root = resolve(import.meta.dirname, '..');
const themeRoot = resolve(root, 'wp-content/themes/skvn-marine');
const pluginRoot = resolve(root, 'wp-content/plugins/skvn-marine-blocks');
const patternsDir = resolve(themeRoot, 'patterns');

const styleCss = readFileSync(resolve(themeRoot, 'style.css'), 'utf8');
const productDataDecision = readFileSync(
	resolve(root, 'docs/decisions/product-data-model.md'),
	'utf8',
);
const patternFiles = readdirSync(patternsDir).filter((file) => file.endsWith('.php'));

assert.ok(patternFiles.includes('woo-product-grid.php'), 'missing Woo product grid pattern');
assert.ok(patternFiles.includes('woo-category-strip.php'), 'missing Woo category strip pattern');

const productGridPattern = readFileSync(resolve(patternsDir, 'woo-product-grid.php'), 'utf8');
const categoryStripPattern = readFileSync(resolve(patternsDir, 'woo-category-strip.php'), 'utf8');

assert.match(productGridPattern, /Slug: skvn-marine\/woo-product-grid/);
assert.match(productGridPattern, /woocommerce\/product-collection/);
assert.match(productGridPattern, /woocommerce\/product-template/);
assert.match(productGridPattern, /woocommerce\/product-image/);
assert.match(productGridPattern, /wp:post-title/);
assert.match(productGridPattern, /woocommerce\/product-button/);
assert.match(productGridPattern, /skvn-product-grid/);
assert.match(productGridPattern, /skvn-product-card/);
assert.match(productGridPattern, /skvn-product-card__cta/);

assert.match(categoryStripPattern, /Slug: skvn-marine\/woo-category-strip/);
assert.match(categoryStripPattern, /woocommerce\/product-categories/);
assert.match(categoryStripPattern, /skvn-product-category-strip/);

for (const className of [
	'.skvn-product-section',
	'.skvn-product-grid',
	'.skvn-product-card',
	'.skvn-product-card__cta',
	'.skvn-product-category-strip',
	'.wc-block-grid__product',
	'.wp-block-woocommerce-product-template',
]) {
	assert.ok(styleCss.includes(className), `style.css missing ${className}`);
}

assert.match(
	styleCss,
	/\.skvn-product-card__cta[\s\S]*opacity:\s*1/,
	'product card CTA must remain visible by default',
);
assert.match(
	styleCss,
	/@media \(hover: hover\)[\s\S]*\.skvn-product-card:hover[\s\S]*\.skvn-product-card__cta/,
	'desktop hover enhancement must be optional',
);
assert.match(
	styleCss,
	/@media \(max-width: 767px\)[\s\S]*\.skvn-product-card__cta[\s\S]*opacity:\s*1/,
	'mobile product CTA must always remain visible',
);

assert.doesNotMatch(
	productGridPattern + categoryStripPattern,
	/registerBlockType|skvn-marine\/product-grid|skvn-marine\/product-list|custom SQL/i,
	'V1 product sections must not introduce custom product grid/list blocks or custom SQL',
);

for (const deferredPath of ['src/product-grid/block.json', 'src/product-list/block.json']) {
	assert.equal(
		existsSync(resolve(pluginRoot, deferredPath)),
		false,
		`V1 must not add custom ${deferredPath}`,
	);
}

assert.match(productDataDecision, /Use WooCommerce native products/);
assert.match(productDataDecision, /Mobile rule: CTA must always be visible/);
