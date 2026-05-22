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
const sliderSave = read('src/slider/save.tsx');
const sliderView = read('src/slider/view.ts');
const slideSave = read('src/slide/save.tsx');
const sliderDecision = readFileSync(resolve(root, 'docs/decisions/slider-block.md'), 'utf8');

assert.equal(sliderBlock.name, 'skvn-marine/slider');
assert.equal(slideBlock.name, 'skvn-marine/slide');
assert.deepEqual(sliderBlock.allowedBlocks, ['skvn-marine/slide']);
assert.deepEqual(slideBlock.parent, ['skvn-marine/slider']);

for (const attribute of ['autoplay', 'delay', 'loop', 'arrows', 'dots', 'effect', 'slidesPerView']) {
	assert.ok(sliderBlock.attributes?.[attribute], `slider block missing attribute: ${attribute}`);
}

assert.match(indexTs, /registerBlockType\(sliderMetadata\.name/);
assert.match(indexTs, /registerBlockType\(slideMetadata\.name/);

assert.match(sliderEdit, /skvn-slider--editor/);
assert.match(sliderEdit, /skvn-slider__editor-stack/);
assert.doesNotMatch(sliderEdit, /from 'swiper'|new\s+Swiper/, 'editor must not initialize Swiper');

assert.match(sliderSave, /data-skvn-slider/);
assert.match(sliderSave, /JSON\.stringify/);
assert.match(sliderSave, /swiper-wrapper/);
assert.match(slideSave, /swiper-slide/);

assert.match(sliderView, /matchMedia\('\(prefers-reduced-motion: reduce\)'\)/);
assert.match(sliderView, /pauseOnMouseEnter:\s*true/);
assert.match(sliderView, /keyboard:\s*\{\s*enabled:\s*true\s*\}/);
assert.match(sliderView, /querySelectorAll<HTMLElement>\('\[data-skvn-slider\]'\)/);
assert.match(sliderView, /try\s*\{[\s\S]*JSON\.parse/, 'frontend config parsing must be guarded');

assert.ok(packageJson.dependencies?.swiper, 'Swiper dependency must be declared');
assert.match(sliderDecision, /Use `swiper`/);
assert.match(sliderDecision, /Swiper must load only through the slider block frontend view script/);
assert.match(sliderDecision, /Editor uses stacked\/simplified preview/);
assert.match(packageJson.scripts?.build ?? '', /src\/index\.ts/);
assert.match(packageJson.scripts?.build ?? '', /src\/slider\/view\.ts/);

assert.match(pluginPhp, /build\/index\.ts\.js/, 'PHP must register actual editor build output');
assert.match(pluginPhp, /build\/view\.ts\.js/, 'PHP must register actual slider view build output');
assert.match(pluginPhp, /'slider' === \$block[\s\S]*'view_script'[\s\S]*'skvn-marine-slider-view'/);
