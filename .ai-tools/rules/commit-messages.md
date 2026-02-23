---
description: "Conventional commits format, types, changelog visibility, and PR title rules"
alwaysApply: true
trigger: always_on
applyTo: "**"
type: always_apply
---

# Commit Messages

## Conventional Commits Format
This repository uses [Conventional Commits](https://www.conventionalcommits.org/) with release-please for automated releases.

### Format
```
<type>(<scope>): <description>
```

### Types (in order of changelog visibility)
**Visible in changelog:**
- `feat` - New features (triggers minor version bump)
- `feat!` - Breaking changes (triggers major version bump)
- `fix` - Bug fixes (triggers patch version bump)
- `perf` - Performance improvements
- `refactor` - Code refactoring
- `revert` - Reverts

**Hidden from changelog:**
- `test` - Adding or updating tests
- `docs` - Documentation changes
- `chore` - Maintenance tasks, dependency updates
- `build` - Build system changes
- `ci` - CI/CD changes
- `style` - Code style changes

### Scope
- Use GitHub issue number when applicable: `feat(#510): add support for UUID[] data type`
- Omit scope for general changes: `chore: Update dependency rector/rector to ^2.3.0`

### Description
- Use sentence case (first letter capitalized, enforced by release-please plugin)
- Be concise but descriptive
- Start with a verb: "add", "fix", "update", "remove", "improve"

### Examples from this repository
```
feat(#510): add support for `UUID[]` data type
feat!: drop support for PHP 8.1
fix(#482): always preserve strings when transforming a `TEXTARRAY` value
test: use exact match assertions unless a delta is needed
chore: Update dependency friendsofphp/php-cs-fixer to ^3.90.0
refactor: Code Refactoring
```

### PR Titles
PR titles follow the same format and become the squash commit message when merged.

### Breaking Changes
Use `!` after the type to indicate breaking changes:
```
feat!: modernize with PHP 8.2 features (like read-only classes)
feat!: remove deprecations scheduled for v4.0
```

