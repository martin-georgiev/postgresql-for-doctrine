#!/usr/bin/env bash
# PostToolUse hook: auto-formats PHP files after Edit/Write operations.

# Find a Python interpreter for JSON parsing
PYTHON_CMD=""
if command -v python3 &>/dev/null; then
    PYTHON_CMD="python3"
elif command -v python &>/dev/null; then
    PYTHON_CMD="python"
else
    echo "post-tool-use-format: no python interpreter found, skipping" >&2
    exit 0
fi

# Extract the file path from the tool input
if [ -n "$1" ] && [ -f "$1" ]; then
    FILE_PATH=$("$PYTHON_CMD" -c "import json,sys; data=json.load(open(sys.argv[1])); print(data.get('file_path',''))" "$1" 2>/dev/null)
else
    FILE_PATH=$("$PYTHON_CMD" -c "import json,sys; data=json.load(sys.stdin); print(data.get('file_path',''))" 2>/dev/null)
fi

# Only run on PHP files
if [[ "$FILE_PATH" != *.php ]]; then
    exit 0
fi

# Only run if the file exists
if [ ! -f "$FILE_PATH" ]; then
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
    echo "post-tool-use-format: config $CONFIG not found, skipping" >&2
    exit 0
fi

"$FIXER" fix "$FILE_PATH" --config="$CONFIG" --quiet 2>/dev/null

exit 0
