# Run quality gate on changed files

Run the same checks CI will run, using the project's composer targets.

## Steps

1. **Fix code style** (rector + php-cs-fixer):
   ```bash
   composer fix-code-style
   ```

2. **Run static analysis** (PHPStan + deptrac):
   ```bash
   composer run-static-analysis
   ```

3. **Run unit tests** (parallel via paratest):
   ```bash
   composer run-unit-tests
   ```

4. **Run integration tests** (if changes affect DBAL types or DQL functions that interact with the database):
   ```bash
   docker compose up -d
   composer run-integration-tests
   ```
   Skip if Docker is not available or changes are unit-test-only.

If any step fails, fix the issues and re-run that step. Max 3 attempts per step before asking for guidance.
