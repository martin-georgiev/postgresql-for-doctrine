# Run quality gate on changed files

Run the full quality gate to verify code is ready for PR.

## Steps

1. **Identify changed files**:
   ```bash
   git diff --name-only main -- '*.php'
   ```

2. **Fix code style** (auto-fix mode):
   ```bash
   composer fix-code-style
   ```

3. **Run PHPStan** on changed source files:
   ```bash
   ./bin/phpstan analyse --configuration=ci/phpstan/config.neon <changed-src-files> --memory-limit=512M
   ```
   If new types or functions were added, include their exception files too.

4. **Run targeted unit tests** for changed files:
   ```bash
   ./bin/phpunit --filter "<TestClassNames>" --configuration ci/phpunit/config-unit.xml
   ```
   Map changed source files to their test counterparts (same path under `tests/Unit/`).

If any step fails, fix the issues and re-run that step. Max 3 attempts per step before asking for guidance.
