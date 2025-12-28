# Testing and Iteration

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

