# @since Version Annotations

## When Adding New Functionality

NEVER guess or assume version numbers. ALWAYS check GitHub for open release PRs before adding any `@since` annotation.

Before adding `@since` tags to new code in the `src/` directory, determine the correct version number:

### 1. Check GitHub for Version Context
- Look for open PRs targeting the main branch that update CHANGELOG.md or prepare a release
- Check the most recent release tag in the repository
- Review any unreleased version numbers mentioned in CHANGELOG.md

### 2. Determine the Next Version Number
- If a release PR exists with a version number, use that version
- If no release is being prepared, increment the minor version from the latest release (e.g., if latest is 3.5.0, use 3.6.0)
- Follow semantic versioning principles based on the type of change

### 3. Apply @since Tags Consistently
- Add `@since X.Y` (without patch version) to all new classes in `src/`
- Use the determined next release version number
- Place the `@since` tag in the class-level PHPDoc block for new classes

**Example**:
```php
/**
 * Implements PostgreSQL MD5() function.
 *
 * @see https://www.postgresql.org/docs/18/functions-binarystring.html
 *
 * @since 3.6
 */
final class Md5 extends BaseFunction
```

### 4. Scope
This rule applies to all new functionality added to the codebase, ensuring consistent version documentation across the project.

