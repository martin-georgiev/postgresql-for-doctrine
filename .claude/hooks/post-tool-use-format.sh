#!/usr/bin/env bash
# PostToolUse hook: auto-formats PHP files after Edit/Write operations.

set -euo pipefail

TOOL_INPUT="${1:-}"

# Extract the file path from the tool input JSON
if [ -z "$TOOL_INPUT" ] || [ ! -f "$TOOL_INPUT" ]; then
    exit 0
fi

FILE_PATH=$(jq -r '.file_path // empty' "$TOOL_INPUT" 2>/dev/null)

# Only run on existing PHP files
if [[ "$FILE_PATH" != *.php ]] || [ ! -f "$FILE_PATH" ]; then
    exit 0
fi

# Find php-cs-fixer
FIXER="./bin/php-cs-fixer"
if [ ! -x "$FIXER" ]; then
    FIXER="./vendor/bin/php-cs-fixer"
fi
if [ ! -x "$FIXER" ]; then
    exit 0
fi

# Use the project's actual config
CONFIG="./ci/php-cs-fixer/config.php"
if [ ! -f "$CONFIG" ]; then
    exit 0
fi

"$FIXER" fix "$FILE_PATH" --config="$CONFIG" --quiet 2>/dev/null

exit 0
