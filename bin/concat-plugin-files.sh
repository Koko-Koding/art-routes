#!/bin/bash
# Concatenate all PHP, JS, CSS, and HTML files in the plugin into one file with file path markers.
# This version fixes the "ignore directories" logic using find -prune and arrays so
# directories listed in IGNORE_DIRS (relative to the plugin root) are excluded.

set -euo pipefail
IFS=$'\n\t'

PLUGIN_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DEFAULT_OUTPUT_FILE="all-plugin-code.txt"

# Add directories (relative to PLUGIN_DIR) to ignore here:
# Examples: "build", "bin", "vendor/some-package", "node_modules"
IGNORE_DIRS=("build" "bin" "@route-info-rest-client")

read -r -p "Output to a file? (y/n): " output_to_file
if [[ "$output_to_file" =~ ^[Yy]$ ]]; then
  read -r -p "Enter output file path (default: $DEFAULT_OUTPUT_FILE): " output_file
  output_file="${output_file:-$DEFAULT_OUTPUT_FILE}"
  exec > "$output_file"
  echo "Writing output to $output_file..."
fi

# Build the find command as an array so quoting is preserved.
# We will add -path "$PLUGIN_DIR/<dir>" -prune -o for each ignored dir,
# followed by -type f \( -name '*.php' -o -name '*.js' -o -name '*.css' -o -name '*.html' \) -print
FIND_CMD=(find "$PLUGIN_DIR")

# Append prune expressions for ignore dirs (if any)
if [[ ${#IGNORE_DIRS[@]} -gt 0 ]]; then
  for d in "${IGNORE_DIRS[@]}"; do
    # skip empty entries
    [[ -z "$d" ]] && continue
    # Normalize (remove trailing slash if present)
    d="${d%/}"
    # Prune the exact path under the plugin dir (this prunes the directory and its contents)
    FIND_CMD+=(-path "$PLUGIN_DIR/$d" -prune -o)
  done
fi

# Add the file selection and print action
FIND_CMD+=(-type f \( -name '*.php' -o -name '*.js' -o -name '*.css' -o -name '*.html' \) -print)

# Debug: you can uncomment the next line to see the constructed find command before running
# printf '%q ' "${FIND_CMD[@]}"; echo

# Run the find, sort results and concatenate with markers
"${FIND_CMD[@]}" | sort | while IFS= read -r file; do
  # Make sure we only process regular files (avoid broken symlinks etc.)
  [[ -f "$file" ]] || continue
  relpath="${file#$PLUGIN_DIR/}"
  echo -e "\n==================== FILE: $relpath ====================\n"
  cat "$file"
  echo -e "\n==================== END FILE: $relpath ====================\n"
done
