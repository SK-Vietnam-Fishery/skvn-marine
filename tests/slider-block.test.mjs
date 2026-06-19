import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';

const root = resolve(import.meta.dirname, '..');
const pluginRoot = resolve(root, 'wp-content/plugins/skvn-marine-blocks');

const read = (path) => readFileSync(resolve(pluginRoot, path), 'utf8');
const readJson = (path) => JSON.parse(read(path));

const packageJson = readJson('package.json');
const sliderBlock = readJson('src/slider/block.json');
const slideBlock = readJson('src/slide/block.json');
const pluginPhp = read('skvn-marine-blocks.php');
const indexTs = read('src/index.ts');
const sliderEdit = read('src/slider/edit.tsx');
const sliderDeprecated = read('src/slider/deprecated.tsx');
const sliderSave = read('src/slider/save.tsx');
const sliderView = read('src/slider/view.ts');
const sliderStyle = read('src/slider/style.css');
const slideEdit = read('src/slide/edit.tsx');
const slideSave = read('src/slide/save.tsx');
const sliderRenderer = read('modules/slider-render/slider-render.php');
const sliderDecision = readFileSync(
	resolve(root, 'docs/decisions/slider-completion-spec-1.3.0.md'),
	'utf8',
);
const controlsDecision = readFileSync(
	resolve(root, 'docs/decisions/slider-navigation-and-pagination-controls.md'),
	'utf8',
);

assert.equal(sliderBlock.name, 'skvn-marine/slider');
assert.equal(slideBlock.name, 'skvn-marine/slide');
assert.deepEqual(sliderBlock.allowedBlocks, ['skvn-marine/slide']);
assert.deepEqual(slideBlock.parent, ['skvn-marine/slider']);

for (const attribute of [
	'autoplay',
	'autoplayDelay',
	'loop',
	'showArrows',
	'arrowStyle',
	'arrowPosition',
	'showPagination',
	'paginationStyle',
	'paginationPosition',
	'effect',
	'slidesPerView',
]) {
	assert.ok(sliderBlock.attributes?.[attribute], `slider block missing attribute: ${attribute}`);
}
assert.equal(sliderBlock.attributes.autoplayDelay.default, 7000);
assert.equal(sliderBlock.attributes.dots, undefined, 'dots must not remain in the active schema');
assert.equal(sliderBlock.attributes.arrows, undefined, 'arrows must not remain in the active schema');
assert.equal(sliderBlock.attributes.delay, undefined, 'delay must not remain in the active schema');

assert.match(indexTs, /registerBlockType\(\s*sliderMetadata\.name/);
assert.match(indexTs, /registerBlockType\(\s*slideMetadata\.name/);
assert.match(indexTs, /deprecated:\s*sliderDeprecated/);

assert.match(sliderEdit, /skvn-slider--editor/);
assert.match(sliderEdit, /skvn-slider__editor-stack/);
assert.match(sliderEdit, /GOVERNED_DELAYS\s*=\s*\[\s*5000,\s*7000,\s*9000,\s*12000\s*\]/);
assert.match(sliderEdit, /legacy duration/i);
assert.match(sliderEdit, /arrowPosition\s*===\s*[\r\n\t ]*'side-center'/);
assert.match(sliderEdit, /arrowStyle === 'pill'/);
assert.doesNotMatch(sliderEdit, /from 'swiper'|new\s+Swiper/, 'editor must not initialize Swiper');

assert.match(sliderSave, /data-skvn-slider/);
assert.match(sliderSave, /JSON\.stringify/);
assert.match(sliderSave, /swiper-wrapper/);
assert.match(slideSave, /swiper-slide/);
assert.match(sliderDeprecated, /dots:\s*\{\s*type:\s*'boolean'/);
assert.match(sliderDeprecated, /showPagination:\s*attributes\.dots/);
assert.match(sliderDeprecated, /showArrows:\s*attributes\.arrows/);
assert.match(sliderDeprecated, /autoplayDelay:\s*attributes\.delay/);
assert.match(sliderDeprecated, /paginationStyle:\s*'dots'/);

assert.match(sliderView, /prefersReducedMotion\(\)/);
assert.match(sliderView, /pauseOnMouseEnter:\s*false/);
assert.match(sliderView, /disableOnInteraction:\s*false/);
assert.match(sliderView, /keyboard:\s*\{\s*enabled:\s*true\s*\}/);
assert.match(sliderView, /querySelectorAll<\s*SliderElement\s*>\(\s*'\[data-skvn-slider\]'\s*\)/);
assert.match(sliderView, /try\s*\{[\s\S]*JSON\.parse/, 'frontend config parsing must be guarded');
assert.match(sliderView, /new Set<\s*PauseReason\s*>\(\)/);
assert.match(sliderView, /autoplayTimeLeft/);
assert.match(sliderView, /swiper\.realIndex \+ 1/);
assert.match(sliderView, /renderSegments\(\s*config\.slideCount\s*\)/);
assert.match(sliderView, /swiper\.slideToLoop\(\s*index\s*\)/);
assert.match(sliderView, /sliderFirstMove/);
assert.match(sliderView, /navigationNext/);
assert.match(sliderView, /navigationPrev/);
assert.doesNotMatch(sliderView, /setInterval\s*\(/, 'Swiper must remain the only Slider timer');
assert.doesNotMatch(sliderView, /setAttributes\s*\(/, 'frontend progress must not write Gutenberg state');
assert.match(sliderView, /removeEventListener\(\s*'visibilitychange'/);
assert.match(sliderView, /swiper\.on\(\s*'destroy',\s*cleanup\s*\)/);
assert.match(sliderView, /slider\.swiper\?\.destroyed/);
assert.match(sliderView, /classList\.remove\(\s*'skvn-slider--initialized'/);
assert.match(sliderStyle, /skvn-slider__controls--cluster/);
assert.match(sliderStyle, /prefers-reduced-motion:\s*reduce/);
assert.match(
	sliderStyle,
	/\.skvn-slider--height-viewport-below-header:not\(\.skvn-slider--editor\)\s*\{[\s\S]*--skvn-slider-viewport-height/,
	'viewport-below-header must own explicit slider height',
);
assert.match(
	sliderStyle,
	/\.skvn-slider--height-viewport-below-header:not\(\.skvn-slider--editor\)\s+\.skvn-slider__wrapper[\s\S]*height:\s*100%/,
	'viewport-below-header must propagate height through the Swiper wrapper',
);
assert.match(
	sliderStyle,
	/\.skvn-slider--height-viewport-below-header:not\(\.skvn-slider--editor\)\s+\.skvn-slide__media[\s\S]*height:\s*100%/,
	'viewport-below-header must stretch the media frame to the slide height',
);
assert.match(
	sliderView,
	/syncViewportHeight\(\s*swiper\s*\)/,
	'Swiper init must resync viewport height after the height chain is active',
);
assert.match(
	sliderView,
	/activeSwiper\.updateSize\(\)/,
	'viewport offset changes must refresh Swiper geometry',
);
assert.match(
	sliderStyle,
	/\.skvn-slider--hero \.skvn-slide\s*\{[\s\S]*align-items:\s*center/,
	'hero slide content column must center on the cross axis',
);
assert.match(
	sliderStyle,
	/\.skvn-slider--hero \.skvn-slide__content > \*\s*\{[\s\S]*margin-inline:\s*auto/,
	'hero copy blocks must center inside the slide frame',
);
assert.match(slideEdit, /select\(\s*coreDataStore\s*\)/);
assert.match(slideEdit, /attachment\?\.source_url\s*\|\|\s*attributes\.backgroundImageUrl/);
assert.match(sliderRenderer, /count\(\s*\$block->inner_blocks\s*\)/);
assert.match(sliderRenderer, /\$show_arrows\s*=\s*\$show_arrows && \$has_multiple_slides/);
assert.match(sliderRenderer, /\$show_pagination\s*=\s*\$show_pagination && \$has_multiple_slides/);
assert.match(sliderRenderer, /skvn-slider__controls--cluster/);
assert.match(
	sliderRenderer,
	/skvn_marine_blocks_render_slider_arrows[\s\S]*skvn-slider__controls-separator[\s\S]*skvn_marine_blocks_render_slider_pagination/,
	'cluster markup order must be arrows, separator, pagination',
);
assert.match(sliderRenderer, /array_key_exists\(\s*'delay',\s*\$raw_attributes\s*\)/);
assert.match(sliderRenderer, /\$attributes\['autoplayDelay'\]\s*=\s*5000/);

assert.ok(packageJson.dependencies?.swiper, 'Swiper dependency must be declared');
assert.match(sliderDecision, /Keep one Swiper runtime/);
assert.match(sliderDecision, /Dynamic PHP rendering/);
assert.match(sliderDecision, /Do not run Swiper or autoplay in Gutenberg/);
assert.match(controlsDecision, /5s \| 7s \| 9s \| 12s/);
assert.match(controlsDecision, /arrows \| pagination/);
assert.match(controlsDecision, /Swiper remains the only Slider movement and autoplay controller/);
assert.match(packageJson.scripts?.build ?? '', /src\/index\.ts/);
assert.match(packageJson.scripts?.build ?? '', /src\/slider\/view\.ts/);

assert.match(pluginPhp, /build\/index\.ts\.js/, 'PHP must register actual editor build output');
assert.match(pluginPhp, /build\/view\.ts\.js/, 'PHP must register actual slider view build output');
assert.match(
	pluginPhp,
	/build\/style-view\.ts\.css/,
	'PHP must register the SKVN CSS filename emitted for the Slider view entry',
);
assert.match(
	pluginPhp,
	/build\/view\.ts\.css/,
	'PHP must register the Swiper core CSS filename emitted for the Slider view entry',
);
assert.match(
	pluginPhp,
	/array\(\s*'skvn-marine-slider-core'\s*\)/,
	'SKVN Slider CSS must load after its Swiper core CSS dependency',
);
assert.match(pluginPhp, /__DIR__\s*\.\s*'\/build\/'\s*\.\s*\$block/, 'PHP must register deployable build block metadata');
assert.match(pluginPhp, /'slider' === \$block[\s\S]*'view_script'[\s\S]*'skvn-marine-slider-view'/);
assert.match(
	pluginPhp,
	/'editor_style_handles'\]\[\]\s*=\s*'skvn-marine-slider-view'/,
	'Slider frontend CSS must also be available in the editor',
);
assert.match(
	pluginPhp,
	/function_exists\(\s*'register_block_type'\s*\)/,
	'plugin block registration must be guarded for older WordPress installs',
);
