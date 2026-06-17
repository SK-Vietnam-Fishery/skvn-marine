#!/usr/bin/env bash
set -euo pipefail

script_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
repo_root="$(cd "$script_dir/.." && pwd)"

version=""
dry_run=0
bump_only=0

print_usage() {
	cat <<'EOF'
Usage: bash tools/release-artifact.sh <semver> [--dry-run] [--bump-only]

Runs the SKVN Marine release workflow from the repo root:
  1. Bump theme/plugin/block metadata versions
  2. Build deploy artifact (plugin npm build + copy theme/plugin)
  3. Package theme and plugin zip files

Options:
  --dry-run    Preview version file changes only
  --bump-only  Bump versions without build or zip steps

Examples:
  bash tools/release-artifact.sh 1.3.4
  bash tools/release-artifact.sh 1.3.4 --dry-run
  bash tools/release-artifact.sh 1.3.4 --bump-only
EOF
}

for arg in "$@"; do
	case "$arg" in
		--dry-run)
			dry_run=1
			;;
		--bump-only)
			bump_only=1
			;;
		-h | --help)
			print_usage
			exit 0
			;;
		-*)
			echo "Unknown option: $arg" >&2
			print_usage >&2
			exit 1
			;;
		*)
			if [[ -n "$version" ]]; then
				echo "Unexpected argument: $arg" >&2
				print_usage >&2
				exit 1
			fi
			version="$arg"
			;;
	esac
done

if [[ -z "$version" ]]; then
	print_usage >&2
	exit 1
fi

if ! [[ "$version" =~ ^[0-9]+\.[0-9]+\.[0-9]+([+-][0-9A-Za-z.-]+)?$ ]]; then
	echo "Invalid semver: $version" >&2
	exit 1
fi

if [[ "$dry_run" -eq 1 && "$bump_only" -eq 1 ]]; then
	echo "Use either --dry-run or --bump-only, not both." >&2
	exit 1
fi

cd "$repo_root"

if [[ -s "${NVM_DIR:-$HOME/.nvm}/nvm.sh" ]]; then
	# shellcheck source=/dev/null
	source "${NVM_DIR:-$HOME/.nvm}/nvm.sh"
	nvm use 20 >/dev/null
fi

bump_args=("$version")
if [[ "$dry_run" -eq 1 ]]; then
	bump_args+=("--dry-run")
fi

echo "==> Step 1/3: Bump project versions to $version"
node tools/bump-project-version.mjs "${bump_args[@]}"

if [[ "$dry_run" -eq 1 || "$bump_only" -eq 1 ]]; then
	exit 0
fi

echo
echo "==> Step 2/3: Build deploy artifact"
node tools/build-deploy-artifact.mjs

echo
echo "==> Step 3/3: Package theme and plugin zip files"
bash tools/package-theme-zip.sh
bash tools/package-plugin-zip.sh

echo
echo "Release artifacts ready:"
echo "- build/skvn-marine.zip"
echo "- build/skvn-marine-blocks.zip"
echo "- build/wp-content/themes/skvn-marine/"
echo "- build/wp-content/plugins/skvn-marine-blocks/"