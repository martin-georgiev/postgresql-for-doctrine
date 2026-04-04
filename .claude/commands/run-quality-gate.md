# Run quality gate on changed files

Run the same checks CI will run, scoped to changed files for speed.

## Steps

1. **Identify changed PHP files**:
   ```bash
   git diff --name-only main -- '*.php'
   ```

2. **Fix code style** (rector + php-cs-fixer — runs on full codebase, fast enough):
   ```bash
   composer fix-code-style
   ```

3. **Run PHPStan** on changed source files only:
   ```bash
   ./bin/phpstan analyse --configuration=ci/phpstan/config.neon <changed-src-files> --memory-limit=512M
   ```

4. **Run deptrac** (architecture analysis — runs on full codebase, fast):
   ```bash
   ./bin/deptrac analyze --config-file=./ci/deptrac/config.yml --no-interaction --no-progress
   ```

5. **Run targeted unit tests** for changed files:
   ```bash
   ./bin/phpunit --filter "<TestClassNames>" --configuration ci/phpunit/config-unit.xml
   ```

6. **Run integration tests** (if changes affect DBAL types or DQL functions):
   ```bash
   docker compose up -d && ./bin/phpunit --filter "<TestClassNames>" --configuration ci/phpunit/config-integration.xml
   ```
   Skip if Docker is not available or changes are unit-test-only.

If any step fails, fix the issues and re-run that step. Max 3 attempts per step before asking for guidance.
