#!/usr/bin/env bash
set -euo pipefail

script_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
repo_root="$(cd "$script_dir/.." && pwd)"

version=""
dry_run=0
bump_only=0

print_usage() {
	cat <<'EOF'
Usage: bash tools/release-artifact.sh [<semver>] [--dry-run] [--bump-only]

Runs the SKVN Marine release workflow from the repo root:
  1. Bump theme/plugin/block metadata versions  (skipped when no semver)
  2. Build deploy artifact (plugin npm build + copy theme/plugin)
  3. Package theme and plugin zip files
  4. Sync build output to local WP runtime       (only when no semver)

When called without a semver, steps 2-4 run immediately — useful for
testing CSS/PHP changes without bumping versions.

Options:
  --dry-run    Preview version file changes only (requires semver)
  --bump-only  Bump versions without build or zip steps (requires semver)

Examples:
  bash tools/release-artifact.sh              # build + zip + local sync
  bash tools/release-artifact.sh 1.3.4        # full release flow
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

no_version=0
if [[ -z "$version" ]]; then
	no_version=1
fi

if [[ "$no_version" -eq 1 && ( "$dry_run" -eq 1 || "$bump_only" -eq 1 ) ]]; then
	echo "--dry-run and --bump-only require a semver argument." >&2
	print_usage >&2
	exit 1
fi

if [[ "$no_version" -eq 0 ]]; then
	if ! [[ "$version" =~ ^[0-9]+\.[0-9]+\.[0-9]+([+-][0-9A-Za-z.-]+)?$ ]]; then
		echo "Invalid semver: $version" >&2
		exit 1
	fi
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

if [[ "$no_version" -eq 0 ]]; then
	bump_args=("$version")
	if [[ "$dry_run" -eq 1 ]]; then
		bump_args+=("--dry-run")
	fi

	echo "==> Step 1/3: Bump project versions to $version"
	node tools/bump-project-version.mjs "${bump_args[@]}"

	if [[ "$dry_run" -eq 1 || "$bump_only" -eq 1 ]]; then
		exit 0
	fi
else
	echo "==> No version provided — skipping version bump"
fi

echo
echo "==> Step 2/3: Build deploy artifact"
node tools/build-deploy-artifact.mjs

echo
echo "==> Step 3/3: Package theme and plugin zip files"
bash tools/package-theme-zip.sh
bash tools/package-plugin-zip.sh

echo
echo "Artifacts ready:"
echo "- build/skvn-marine.zip"
echo "- build/skvn-marine-blocks.zip"
echo "- build/wp-content/themes/skvn-marine/"
echo "- build/wp-content/plugins/skvn-marine-blocks/"

if [[ "$no_version" -eq 1 ]]; then
	local_env="$repo_root/.local/ENVIRONMENT.md"
	wp_runtime=""
	if [[ -f "$local_env" ]]; then
		wp_runtime="$(grep '^WP_RUNTIME_ROOT_WSL=' "$local_env" | cut -d= -f2-)"
	fi

	if [[ -z "$wp_runtime" ]]; then
		echo
		echo "Warning: WP_RUNTIME_ROOT_WSL not found in .local/ENVIRONMENT.md — skipping local sync." >&2
		exit 0
	fi

	echo
	echo "==> Step 4/3: Sync to local WP at $wp_runtime"
	rsync -a --delete \
		"$repo_root/build/wp-content/themes/skvn-marine/" \
		"$wp_runtime/wp-content/themes/skvn-marine/"
	rsync -a --delete \
		"$repo_root/build/wp-content/plugins/skvn-marine-blocks/" \
		"$wp_runtime/wp-content/plugins/skvn-marine-blocks/"
	echo "Local sync done."
fi