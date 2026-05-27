---
description: "@since version annotations: check GitHub for next release version before adding tags"
alwaysApply: true
trigger: always_on
applyTo: "**"
type: always_apply
---

# @since Version Annotations

**Required for every new class in `src/`**: `@since X.Y` (no patch version) in the class-level PHPDoc.

**Forbidden**: guessing the version number.

### How to determine the version

1. Check GitHub for an open release PR (it sets the next version).
2. If none, read CHANGELOG.md for the most recent release and increment the minor (e.g. `3.5.0` → `3.6`).
3. Use semver based on the change type.

```php
// ❌ Wrong — version guessed, not verified from GitHub
/** @since 3.0 */
final class Md5 extends BaseFunction

// ✓ Correct — version confirmed from open release PR or CHANGELOG
/**
 * Implements PostgreSQL MD5() function.
 *
 * @see https://www.postgresql.org/docs/18/functions-binarystring.html
 *
 * @since 3.6
 */
final class Md5 extends BaseFunction
```

