# Testing and Iteration

## Handle Version-Specific Features
When adding functions that require specific PostgreSQL or PostGIS versions:
1. Check the function's availability in the official documentation
2. Review the CI matrix in `.github/workflows/integration-tests.yml` to understand which versions are tested
3. Add version requirements **only when necessary**:
   - If the minimum version tested in CI already includes the required feature, do NOT add version checks
   - Only add version checks if the CI matrix tests versions older than the feature's minimum requirement
4. Use version numbers in numeric format: PostgreSQL uses `XXYYZZ` (e.g., 180000 for 18.0.0), PostGIS uses `XXYYZZ` (e.g., 30400 for 3.4.0)

**Example - When to add version checks**:
```php
// CI tests PostGIS 3.4, 3.5, 3.6 but ST_HasM requires PostGIS 3.5+
// ADD version check because PostGIS 3.4 doesn't have this function
protected function setUp(): void
{
    parent::setUp();
    $this->requirePostgisVersion(30500, 'ST_HasM');
}
```

**Example - When NOT to add version checks**:
```php
// CI tests PostGIS 3.4, 3.5, 3.6 and ST_LineExtend requires PostGIS 3.4+
// DO NOT add version check - all tested versions support this function
```

## Run Targeted Tests After Code Changes
When you complete developing new functionality, always run tests selectively on the affected code:

1. **Identify affected test files**: Determine which unit and integration test files correspond to the changed source files
2. **Run targeted tests**: Use PHPUnit's `--filter` option to run only the relevant test classes or methods
3. **Use project infrastructure**: Execute tests through the project's Docker and Composer setup as defined in `composer.json` scripts (e.g., `composer test:unit`, `composer test:integration`)
4. **Keep test runs lightweight**: Filter to specific test classes/methods rather than running entire test suites when possible

## Iteration Limit on Test Failures
When tests fail after your changes:
- You have a maximum of **3 attempts** to fix failing tests by modifying the implementation code
- After 3 failed attempts, stop changing the active code and ask the user for guidance
- This prevents going in circles and accumulating technical debt through repeated trial-and-error fixes

**Example**: After implementing `ST_HasZ` function, run:
```bash
bin/phpunit --filter "ST_HasZ" --configuration ci/phpunit/config-integration.xml
```
instead of running the full integration test suite.

