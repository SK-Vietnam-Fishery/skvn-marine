import assert from 'node:assert/strict';
import { execFileSync } from 'node:child_process';

const artifactPath = '/mnt/c/Users/VPF-Champion/OneDrive/WhySchools/Bussiness/Obsidian Knowledge Base/SKVN/Minhhai Fishery/Design/03_Hero_layout.html';

const output = execFileSync(
	process.execPath,
	[
		'tools/layout-translator/translate-layout.mjs',
		'--input',
		artifactPath,
		'--title',
		'Cold Storage IQF Hero',
	],
	{
		cwd: '/mnt/d/Github/skvn-marine',
		encoding: 'utf8',
	}
);

assert.match(output, /<!-- wp:image \{"sizeSlug":"large","className":"skvn-translated__media"\} -->/);
assert.match(output, /<figure class="wp-block-image size-large skvn-translated__media">/);
assert.match(output, /https:\/\/placehold\.co\/900x700\/eef6ff\/0a2a4a\?text=Replace\+Image/);
assert.doesNotMatch(output, /<!-- wp:image [^>]*\/-->/);
assert.doesNotMatch(output, /src="data:/);
assert.match(output, /Inline data URI images: replaced with paste-safe placeholder image/);

console.log('layout translator paste-safe image test passed');
