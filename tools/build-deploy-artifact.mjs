import { cpSync, existsSync, mkdirSync, rmSync } from 'node:fs';
import { relative, resolve, sep } from 'node:path';
import { spawnSync } from 'node:child_process';

const root = resolve(import.meta.dirname, '..');
const artifactRoot = resolve(root, 'build');
const pluginRoot = resolve(root, 'wp-content/plugins/skvn-marine-blocks');
const themeSource = resolve(root, 'wp-content/themes/skvn-marine');
const pluginSource = resolve(root, 'wp-content/plugins/skvn-marine-blocks');
const themeTarget = resolve(artifactRoot, 'wp-content/themes/skvn-marine');
const pluginTarget = resolve(artifactRoot, 'wp-content/plugins/skvn-marine-blocks');

function assertInsideRoot(path) {
	const rel = relative(root, path);
	if (rel === '' || rel.startsWith('..') || rel.includes(`..${sep}`)) {
		throw new Error(`Refusing to operate outside repo root: ${path}`);
	}
}

function run(command, args, options = {}) {
	const result = spawnSync(command, args, {
		cwd: options.cwd ?? root,
		stdio: 'inherit',
		shell: process.platform === 'win32',
	});

	if (result.status !== 0) {
		throw new Error(`${command} ${args.join(' ')} failed`);
	}
}

assertInsideRoot(artifactRoot);
assertInsideRoot(themeTarget);
assertInsideRoot(pluginTarget);

run('npm', ['run', 'build'], { cwd: pluginRoot });

if (existsSync(artifactRoot)) {
	rmSync(artifactRoot, { recursive: true, force: true });
}

mkdirSync(resolve(artifactRoot, 'wp-content/themes'), { recursive: true });
mkdirSync(resolve(artifactRoot, 'wp-content/plugins'), { recursive: true });

cpSync(themeSource, themeTarget, {
	recursive: true,
	filter: (source) => !source.includes(`${sep}node_modules${sep}`),
});

mkdirSync(pluginTarget, { recursive: true });
cpSync(resolve(pluginSource, 'skvn-marine-blocks.php'), resolve(pluginTarget, 'skvn-marine-blocks.php'));
cpSync(resolve(pluginSource, 'build'), resolve(pluginTarget, 'build'), { recursive: true });
if (existsSync(resolve(pluginSource, 'assets'))) {
	cpSync(resolve(pluginSource, 'assets'), resolve(pluginTarget, 'assets'), { recursive: true });
}

console.log(`Deploy artifact ready: ${artifactRoot}`);
console.log('Copy these two folders to the target WordPress site:');
console.log(`- ${themeTarget}`);
console.log(`- ${pluginTarget}`);
