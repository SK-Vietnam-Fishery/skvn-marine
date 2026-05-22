import assert from 'node:assert/strict';
import { readdirSync, readFileSync } from 'node:fs';
import { resolve } from 'node:path';

const root = resolve(import.meta.dirname, '..');
const themeJson = JSON.parse(
	readFileSync(resolve(root, 'wp-content/themes/skvn-marine/theme.json'), 'utf8'),
);
const styleCss = readFileSync(
	resolve(root, 'wp-content/themes/skvn-marine/style.css'),
	'utf8',
);
const blockStylesPhp = readFileSync(
	resolve(root, 'wp-content/themes/skvn-marine/inc/block-styles.php'),
	'utf8',
);
const patternsDir = resolve(root, 'wp-content/themes/skvn-marine/patterns');
const patternFiles = readdirSync(patternsDir).filter((file) => file.endsWith('.php'));
const patterns = Object.fromEntries(
	patternFiles.map((file) => [
		file,
		readFileSync(resolve(patternsDir, file), 'utf8'),
	]),
);

const palette = themeJson.settings?.color?.palette ?? [];
const paletteSlugs = new Set(palette.map((color) => color.slug));

for (const slug of [
	'skvn-blue-950',
	'skvn-blue-900',
	'skvn-blue-700',
	'skvn-mint-100',
	'skvn-gold-300',
	'skvn-teal-600',
	'skvn-sky-50',
	'skvn-slate-700',
	'skvn-white',
]) {
	assert.ok(paletteSlugs.has(slug), `theme.json missing color slug: ${slug}`);
}

assert.equal(themeJson.settings?.layout?.contentSize, '760px');
assert.equal(themeJson.settings?.layout?.wideSize, '1200px');

const fontSizes = themeJson.settings?.typography?.fontSizes ?? [];
for (const fontSize of fontSizes) {
	assert.ok(
		!/clamp\(|vw/i.test(fontSize.size),
		`font size ${fontSize.slug} must not use viewport scaling`,
	);
}

for (const className of [
	'.skvn-section',
	'.skvn-container',
	'.skvn-button',
	'.skvn-button--primary',
	'.skvn-card',
	'.is-style-skvn-card',
	'.is-style-skvn-rounded-media',
	'.skvn-site-header',
	'.skvn-site-footer',
	'.site-header',
	'.main-navigation',
	'.inside-header',
	'.footer-widgets',
	'.skvn-trust-strip',
	'.skvn-trust-strip__grid',
	'.skvn-newsletter-band',
	'.skvn-newsletter-media',
	'.skvn-newsletter-media--overhang',
]) {
	assert.ok(styleCss.includes(className), `style.css missing ${className}`);
}

for (const customProperty of [
	'--skvn-color-blue-950',
	'--skvn-color-blue-700',
	'--skvn-color-mint-100',
	'--skvn-color-gold-300',
	'--skvn-accent-support',
	'--skvn-accent-premium',
	'--skvn-color-teal-600',
	'--skvn-radius-card',
	'--skvn-shadow-card',
]) {
	assert.ok(styleCss.includes(customProperty), `style.css missing ${customProperty}`);
}

assert.match(
	styleCss,
	/--skvn-accent-support:\s*var\(--skvn-color-mint-100\)/,
	'soft mint must be the primary support accent',
);
assert.match(
	styleCss,
	/--skvn-accent-premium:\s*var\(--skvn-color-gold-300\)/,
	'bright gold must remain a limited premium accent',
);
assert.match(
	styleCss,
	/\.skvn-button--primary[\s\S]*background:\s*var\(--skvn-color-blue-700\)/,
	'primary CTA must remain blue, not mint or gold',
);
assert.match(
	styleCss,
	/\.skvn-trust-strip__icon[\s\S]*background:\s*var\(--skvn-accent-support\)/,
	'trust strip icons must use mint support accent',
);
assert.match(
	styleCss,
	/\.skvn-premium-accent[\s\S]*var\(--skvn-accent-premium\)/,
	'gold must be exposed only as an explicit premium accent utility',
);

for (const blockStyle of [
	"'name'  => 'skvn-primary'",
	"'name'  => 'skvn-section'",
	"'name'  => 'skvn-card'",
	"'name'  => 'skvn-rounded-media'",
]) {
	assert.ok(blockStylesPhp.includes(blockStyle), `block-styles.php missing ${blockStyle}`);
}

for (const file of [
	'section-intro.php',
	'trust-strip.php',
	'newsletter-band.php',
	'site-footer.php',
]) {
	assert.ok(patternFiles.includes(file), `missing pattern file: ${file}`);
}

assert.match(patterns['section-intro.php'], /Slug: skvn-marine\/section-intro/);
assert.match(patterns['section-intro.php'], /skvn-section/);

assert.match(patterns['trust-strip.php'], /Slug: skvn-marine\/trust-strip/);
assert.match(patterns['trust-strip.php'], /skvn-trust-strip/);
assert.equal(
	(patterns['trust-strip.php'].match(/<div class="wp-block-group skvn-trust-strip__item">/g) ?? []).length,
	4,
	'trust strip must include 4 feature items',
);

assert.match(patterns['newsletter-band.php'], /Slug: skvn-marine\/newsletter-band/);
assert.match(patterns['newsletter-band.php'], /skvn-newsletter-band/);
assert.match(patterns['newsletter-band.php'], /<!-- wp:image/);
assert.match(patterns['newsletter-band.php'], /skvn-newsletter-media--overhang/);
assert.doesNotMatch(
	patterns['newsletter-band.php'],
	/<form|wp_ajax|admin-post|wpcf7_mail_sent|action=/i,
	'newsletter pattern must not include a custom form handler',
);

assert.match(patterns['site-footer.php'], /Slug: skvn-marine\/site-footer/);
assert.match(patterns['site-footer.php'], /skvn-site-footer/);
assert.match(patterns['site-footer.php'], /wp:navigation/);
assert.match(patterns['site-footer.php'], /skvn-footer-brand/);
