import assert from 'node:assert/strict';
import { readdirSync, readFileSync, statSync } from 'node:fs';
import { resolve } from 'node:path';

const root = resolve(import.meta.dirname, '..');
const targets = [
	resolve(root, 'AGENTS.md'),
	resolve(root, '.context'),
	resolve(root, 'docs'),
];
const textExtensions = new Set(['.md', '.html']);

function collectFiles(path) {
	const stat = statSync(path);
	if (stat.isFile()) {
		return [path];
	}

	return readdirSync(path)
		.flatMap((entry) => collectFiles(resolve(path, entry)));
}

function hasTextExtension(path) {
	return [...textExtensions].some((extension) => path.endsWith(extension));
}

const mojibakePattern =
	/[\u00c2-\u00c6\u00c5]|\u00e2[\u0080-\u00bf]|\u00e1[\u00ba-\u00bf]/;

for (const file of targets.flatMap(collectFiles).filter(hasTextExtension)) {
	const content = readFileSync(file, 'utf8');
	assert.doesNotMatch(content, mojibakePattern, `${file} contains mojibake`);
}
