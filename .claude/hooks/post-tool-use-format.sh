#!/usr/bin/env bash
# PostToolUse hook: auto-formats PHP files after Edit/Write operations.
# Receives the tool input as JSON on stdin or via $1 file path.

# Extract the file path from the tool input
if [ -n "$1" ] && [ -f "$1" ]; then
    FILE_PATH=$(python3 -c "import json,sys; data=json.load(open(sys.argv[1])); print(data.get('file_path',''))" "$1" 2>/dev/null)
else
    FILE_PATH=$(python3 -c "import json,sys; data=json.load(sys.stdin); print(data.get('file_path',''))" 2>/dev/null)
fi

# Only run on PHP files
if [[ "$FILE_PATH" != *.php ]]; then
    exit 0
fi

# Only run if the file exists
if [ ! -f "$FILE_PATH" ]; then
    exit 0
fi

# Only run if php-cs-fixer is available
if [ ! -x "./bin/php-cs-fixer" ] && [ ! -x "./vendor/bin/php-cs-fixer" ]; then
    exit 0
fi

# Run php-cs-fixer silently on the single file
FIXER="./bin/php-cs-fixer"
if [ ! -x "$FIXER" ]; then
    FIXER="./vendor/bin/php-cs-fixer"
fi

"$FIXER" fix "$FILE_PATH" --config=.php-cs-fixer.dist.php --quiet 2>/dev/null

exit 0
