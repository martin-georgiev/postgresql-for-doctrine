#!/usr/bin/env bash
# PostCompact hook: re-injects critical rules after context compaction.

cat <<'RULES'
## Critical Rules (re-injected after compaction)

- **Exceptions**: `*ForPHPException` in `convertToPHPValue`; `*ForDatabaseException` in `convertToDatabaseValue` — NEVER swap, NEVER use generic exceptions
- **PHPStan**: ALWAYS use `--configuration=ci/phpstan/config.neon` — never run without it
- **DQL booleans**: `StringPrimary` not `ArithmeticPrimary`; `BooleanValidationTrait`; patterns longest-first
- **PostGIS**: Results are WKB hex — use `ST_EQUALS`/`ST_LENGTH`/`assertEqualsWithDelta`, not string assertions
- **Tests**: Max 3 fix attempts then ask user; use central fixtures; filter by specific IDs
- **@since**: Check GitHub for next release version — NEVER guess
- **Commits**: Conventional Commits: `feat(#123): description`, `fix: ...`, `chore: ...`
- **Code quality**: No obvious comments; strong assertions over loose pattern matching
- **Full rules**: `.ai-tools/rules/`
RULES
