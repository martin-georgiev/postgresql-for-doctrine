#!/usr/bin/env bash
set -e

# Get the directory of this script
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/../.." && pwd)"

# Default test directories to scan
TEST_DIRS=(
  "tests/MartinGeorgiev/Doctrine/DBAL/Types"
  "tests/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions"
  "tests/MartinGeorgiev/Utils"
)

# Parse command line arguments
COVERAGE_ARGS=""
if [[ "$@" == *"--coverage-clover"* ]]; then
  COVERAGE_ARGS="--coverage-clover=./var/logs/test-coverage/clover.xml"
fi

# Additional PHPUnit arguments
PHPUNIT_ARGS="--testdox --display-deprecations --display-errors --display-incomplete"

# Create a temporary directory for individual coverage reports if needed
if [[ -n "$COVERAGE_ARGS" ]]; then
  mkdir -p "${PROJECT_ROOT}/var/logs/test-coverage/split"
fi

# Function to run tests for a specific file
run_test_for_file() {
  local test_file=$1
  local relative_path=${test_file#"$PROJECT_ROOT/"}
  
  echo "Running tests for: $relative_path"
  
  if [[ -n "$COVERAGE_ARGS" ]]; then
    # Generate a unique name for the coverage file
    local coverage_file="split/$(basename "$test_file" .php).xml"
    php "${PROJECT_ROOT}/vendor/bin/phpunit" --configuration="${PROJECT_ROOT}/ci/phpunit/config.xml" $PHPUNIT_ARGS --coverage-clover="${PROJECT_ROOT}/var/logs/test-coverage/$coverage_file" "$test_file"
  else
    php "${PROJECT_ROOT}/vendor/bin/phpunit" --configuration="${PROJECT_ROOT}/ci/phpunit/config.xml" $PHPUNIT_ARGS "$test_file"
  fi
  
  echo "Completed: $relative_path"
  echo "----------------------------------------"
}

# Find and run tests for each file
for dir in "${TEST_DIRS[@]}"; do
  if [[ -d "${PROJECT_ROOT}/${dir}" ]]; then
    echo "Scanning directory: ${dir}"
    
    # Find all test files in the directory
    while IFS= read -r -d '' test_file; do
      run_test_for_file "$test_file"
    done < <(find "${PROJECT_ROOT}/${dir}" -name "*Test.php" -type f -print0)
  else
    echo "Warning: Directory ${dir} does not exist, skipping."
  fi
done

# If we're generating coverage, we need to merge the reports
if [[ -n "$COVERAGE_ARGS" ]]; then
  echo "Merging coverage reports..."
  # You would need a tool like phpcov to merge the reports
  # For now, we'll just use the last generated report as the main one
  
  # Future enhancement: Install phpcov and use it to merge reports
  # vendor/bin/phpcov merge --clover="${PROJECT_ROOT}/var/logs/test-coverage/clover.xml" "${PROJECT_ROOT}/var/logs/test-coverage/split"
  
  echo "Note: Coverage reports are generated individually per test file."
  echo "To get a combined report, consider installing phpcov."
fi

echo "All tests completed!"
