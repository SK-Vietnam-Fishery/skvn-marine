import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';

const root = resolve(import.meta.dirname, '..');
const themeStyle = readFileSync(
	resolve(root, 'wp-content/themes/skvn-marine/style.css'),
	'utf8',
);
const pluginPhp = readFileSync(
	resolve(root, 'wp-content/plugins/skvn-marine-blocks/skvn-marine-blocks.php'),
	'utf8',
);
const deployWorkflow = readFileSync(
	resolve(root, 'docs/workflows/deploy-artifact-workflow.md'),
	'utf8',
);

for (const requiredHeader of [
	'Theme Name: SKVN Marine',
	'Template: generatepress',
	'Requires at least:',
	'Requires PHP:',
	'Update URI:',
]) {
	assert.ok(themeStyle.includes(requiredHeader), `theme style.css missing ${requiredHeader}`);
}

for (const requiredHeader of [
	'Plugin Name: SKVN Marine Blocks',
	'Requires at least:',
	'Requires PHP:',
	'Update URI:',
]) {
	assert.ok(pluginPhp.includes(requiredHeader), `plugin header missing ${requiredHeader}`);
}

for (const requiredNote of [
	'WP_MEMORY_LIMIT',
	'WP_MAX_MEMORY_LIMIT',
	'256M',
	'512M',
	'GeneratePress parent theme is installed',
	'Backup',
	'Rollback',
	'debug.log',
	'template-skvn-full-width.php',
]) {
	assert.ok(deployWorkflow.includes(requiredNote), `deploy workflow missing ${requiredNote}`);
}
