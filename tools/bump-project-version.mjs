#!/usr/bin/env node
import { existsSync, readdirSync, readFileSync, writeFileSync } from 'node:fs';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { spawnSync } from 'node:child_process';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const argv = process.argv.slice(2);
const flags = new Set(argv.filter((arg) => arg.startsWith('--')));
const positional = argv.filter((arg) => !arg.startsWith('--'));
const version = positional[0];
const dryRun = flags.has('--dry-run');
const release = flags.has('--release') || flags.has('--package');

function printUsage() {
	console.error('Usage: node tools/bump-project-version.mjs <semver> [--dry-run] [--release]');
	console.error('');
	console.error('Options:');
	console.error('  --dry-run   Preview version file changes without writing');
	console.error('  --release   After bump: build deploy artifact and create theme/plugin zip files');
	console.error('  --package   Alias for --release');
	console.error('');
	console.error('Examples:');
	console.error('  node tools/bump-project-version.mjs 1.3.4');
	console.error('  node tools/bump-project-version.mjs 1.3.4 --dry-run');
	console.error('  node tools/bump-project-version.mjs 1.3.4 --release');
}

if (flags.has('--help') || flags.has('-h')) {
	printUsage();
	process.exit(0);
}

if (!version || !/^\d+\.\d+\.\d+(?:[-+][0-9A-Za-z.-]+)?$/.test(version)) {
	printUsage();
	process.exit(1);
}

const touched = [];
const preview = [];

function writeIfChanged(filePath, content) {
	const current = readFileSync(filePath, 'utf8');
	if (current === content) {
		return;
	}
	if (dryRun) {
		preview.push(filePath);
		return;
	}
	writeFileSync(filePath, content, 'utf8');
	touched.push(filePath);
}

function updateTextFile(relativePath, replacers) {
	const filePath = join(root, relativePath);
	let content = readFileSync(filePath, 'utf8');
	for (const [pattern, replacement] of replacers) {
		content = content.replace(pattern, replacement);
	}
	writeIfChanged(filePath, content);
}

function updateJsonFile(relativePath, updater) {
	const filePath = join(root, relativePath);
	const data = JSON.parse(readFileSync(filePath, 'utf8'));
	updater(data);
	writeIfChanged(filePath, `${JSON.stringify(data, null, 2)}\n`);
}

function updateBlockJsonFiles(baseRelativePath) {
	const basePath = join(root, baseRelativePath);
	if (!existsSync(basePath)) {
		return;
	}
	for (const entry of readdirSync(basePath, { withFileTypes: true })) {
		if (!entry.isDirectory()) {
			continue;
		}
		const blockPath = join(basePath, entry.name, 'block.json');
		if (!existsSync(blockPath)) {
			continue;
		}
		const relativePath = blockPath.slice(root.length + 1).replaceAll('\\', '/');
		updateJsonFile(relativePath, (data) => {
			data.version = version;
		});
	}
}

function formatRelativePath(filePath) {
	return filePath.slice(root.length + 1).replaceAll('\\', '/');
}

function runStep(label, command, args, options = {}) {
	console.log(`\n==> ${label}`);
	const result = spawnSync(command, args, {
		cwd: options.cwd ?? root,
		stdio: 'inherit',
		shell: process.platform === 'win32',
	});
	if (result.status !== 0) {
		throw new Error(`${label} failed`);
	}
}

updateTextFile('wp-content/themes/skvn-marine/style.css', [
	[/^Version:\s*.+$/m, `Version: ${version}`],
]);

updateTextFile('wp-content/plugins/skvn-marine-blocks/skvn-marine-blocks.php', [
	[/^ \* Version:\s*.+$/m, ` * Version: ${version}`],
]);

updateJsonFile('wp-content/plugins/skvn-marine-blocks/package.json', (data) => {
	data.version = version;
});

updateJsonFile('wp-content/plugins/skvn-marine-blocks/package-lock.json', (data) => {
	data.version = version;
	if (data.packages && data.packages['']) {
		data.packages[''].version = version;
	}
});

updateBlockJsonFiles('wp-content/plugins/skvn-marine-blocks/src');
updateBlockJsonFiles('wp-content/plugins/skvn-marine-blocks/build');

if (dryRun) {
	if (preview.length === 0) {
		console.log(`[dry-run] Project versions already set to ${version}.`);
	} else {
		console.log(`[dry-run] Would update project versions to ${version}:`);
		for (const filePath of preview) {
			console.log(`- ${formatRelativePath(filePath)}`);
		}
	}
} else if (touched.length === 0) {
	console.log(`Project versions already set to ${version}.`);
} else {
	console.log(`Updated project versions to ${version}:`);
	for (const filePath of touched) {
		console.log(`- ${formatRelativePath(filePath)}`);
	}
}

if (release) {
	if (dryRun) {
		console.log('\n[dry-run] Skipping build and zip steps.');
		process.exit(0);
	}

	try {
		runStep('Build deploy artifact', 'node', ['tools/build-deploy-artifact.mjs']);
		runStep('Package theme zip', 'bash', ['tools/package-theme-zip.sh']);
		runStep('Package plugin zip', 'bash', ['tools/package-plugin-zip.sh']);
	} catch (error) {
		console.error(`\n${error.message}`);
		process.exit(1);
	}

	console.log('\nRelease artifacts ready:');
	console.log('- build/skvn-marine.zip');
	console.log('- build/skvn-marine-blocks.zip');
	console.log('- build/wp-content/themes/skvn-marine/');
	console.log('- build/wp-content/plugins/skvn-marine-blocks/');
}