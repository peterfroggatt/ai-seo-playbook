#!/bin/bash
# SessionStart hook for claude-seo.
# Installs Python dependencies and bridges the pre-installed Playwright
# Chromium build to the revision the pinned Playwright expects, so the SEO
# scripts (HTML parsing, screenshots, headless rendering, PDF reports) work.
#
# Runs synchronously: the session waits until setup is complete, which avoids
# race conditions where Claude runs a script before its deps are ready.
set -euo pipefail

# Only run in the remote (Claude Code on the web) environment. Locally,
# developers manage their own venv.
if [ "${CLAUDE_CODE_REMOTE:-}" != "true" ]; then
  exit 0
fi

PROJECT_DIR="${CLAUDE_PROJECT_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)}"
cd "$PROJECT_DIR"

echo "[session-start] Installing Python dependencies..."
python3 -m pip install --root-user-action=ignore --quiet -r requirements.txt

# Dev tooling for lint/tests (not in requirements.txt). Best-effort.
echo "[session-start] Installing dev tools (ruff, pytest)..."
python3 -m pip install --root-user-action=ignore --quiet ruff pytest || true

# --- Playwright Chromium bridge ---------------------------------------------
# This environment ships a pre-installed Chromium under PLAYWRIGHT_BROWSERS_PATH
# but at a build number that may differ from the one the pinned Playwright
# expects, and `playwright install` is disallowed here. Symlink the expected
# revision's directory layout onto the installed build so launch() resolves.
bridge_chromium() {
  local browsers_dir="${PLAYWRIGHT_BROWSERS_PATH:-/opt/pw-browsers}"
  [ -d "$browsers_dir" ] || { echo "[session-start] No browsers dir at $browsers_dir; skipping Chromium bridge."; return 0; }

  # Revision the pinned Playwright wants.
  local expected_rev
  expected_rev=$(python3 - <<'PY' 2>/dev/null || true
import json, os, playwright
p = os.path.join(os.path.dirname(playwright.__file__), "driver", "package", "browsers.json")
d = json.load(open(p))
print(next(b["revision"] for b in d["browsers"] if b["name"] == "chromium"))
PY
)
  [ -n "$expected_rev" ] || { echo "[session-start] Could not determine expected Chromium revision; skipping bridge."; return 0; }

  # Installed full-chrome and headless-shell binaries (any build present).
  local chrome_bin headless_bin
  chrome_bin=$(find "$browsers_dir"/chromium-* -maxdepth 2 -type f -name chrome 2>/dev/null | head -1 || true)
  headless_bin=$(find "$browsers_dir"/chromium_headless_shell-* -maxdepth 2 -type f \( -name headless_shell -o -name chrome-headless-shell \) 2>/dev/null | head -1 || true)

  if [ -n "$chrome_bin" ]; then
    local src_dir target_dir installed_rev
    src_dir=$(dirname "$chrome_bin")
    installed_rev=$(echo "$src_dir" | sed -E 's#.*/chromium-([0-9]+)/.*#\1#')
    target_dir="$browsers_dir/chromium-$expected_rev"
    if [ "$expected_rev" != "$installed_rev" ]; then
      mkdir -p "$target_dir"
      ln -sfn "$src_dir" "$target_dir/chrome-linux64"
      cp -f "$browsers_dir/chromium-$installed_rev"/INSTALLATION_COMPLETE "$target_dir/" 2>/dev/null || true
      cp -f "$browsers_dir/chromium-$installed_rev"/DEPENDENCIES_VALIDATED "$target_dir/" 2>/dev/null || true
      echo "[session-start] Bridged chromium-$expected_rev -> chromium-$installed_rev"
    fi
  fi

  if [ -n "$headless_bin" ]; then
    local src_dir target_dir installed_rev bin_name
    src_dir=$(dirname "$headless_bin")
    bin_name=$(basename "$headless_bin")
    installed_rev=$(echo "$src_dir" | sed -E 's#.*/chromium_headless_shell-([0-9]+)/.*#\1#')
    target_dir="$browsers_dir/chromium_headless_shell-$expected_rev"
    if [ "$expected_rev" != "$installed_rev" ]; then
      mkdir -p "$target_dir"
      ln -sfn "$src_dir" "$target_dir/chrome-headless-shell-linux64"
      # Playwright expects a binary named chrome-headless-shell.
      [ "$bin_name" = "chrome-headless-shell" ] || ln -sfn "$bin_name" "$src_dir/chrome-headless-shell"
      cp -f "$browsers_dir/chromium_headless_shell-$installed_rev"/INSTALLATION_COMPLETE "$target_dir/" 2>/dev/null || true
      cp -f "$browsers_dir/chromium_headless_shell-$installed_rev"/DEPENDENCIES_VALIDATED "$target_dir/" 2>/dev/null || true
      echo "[session-start] Bridged headless-shell-$expected_rev -> $installed_rev"
    fi
  fi
}
bridge_chromium || echo "[session-start] Chromium bridge skipped (non-fatal)."

echo "[session-start] Setup complete."
