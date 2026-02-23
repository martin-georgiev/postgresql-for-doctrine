# PostgreSQL for Doctrine — AI Instructions

This library enhances Doctrine ORM with PostgreSQL-specific DBAL types, DQL functions, and PostGIS support. PHP 8.2+, PostgreSQL 9.4+.

## Project Layout

```
src/MartinGeorgiev/Doctrine/
  DBAL/Types/          # Custom DBAL types (arrays, ranges, geometric, etc.)
  DBAL/Types/Exceptions/  # Domain-specific exceptions per type
  ORM/Query/AST/Functions/  # DQL function implementations
tests/
  Integration/         # Docker-based integration tests (real PostgreSQL + PostGIS)
  Unit/                # PHPUnit unit tests
ci/
  phpstan/config.neon  # PHPStan level max
  phpunit/             # PHPUnit configs (unit + integration)
```

## Key Workflows

**Run unit tests**: `composer test:unit`
**Run integration tests**: `composer test:integration` (requires Docker)
**PHPStan**: `./bin/phpstan analyse --configuration=ci/phpstan/config.neon <files> --memory-limit=512M`

## Rules

All detailed rules live in `.ai-tools/rules/` (auto-loaded by each tool via symlinks):

- **code-quality** — No obvious comments; strong, precise assertions
- **commit-messages** — Conventional Commits format; feat/fix/chore types; issue scope
- **decision-making** — Drop hard features early; ask before expanding scope
- **exceptions** — Domain-specific `Invalid{Type}For{PHP|Database}Exception` pattern
- **meta** — Propose rule updates after repeated in-session corrections
- **phpstan-compliance** — Always use `ci/phpstan/config.neon`; assert types before use
- **since-annotations** — Check GitHub for next release version; add `@since X.Y` to new classes
- **test-data-management** — Use central fixture data; design tests resilient to fixture growth
- **testing-and-iteration** — Version checks only when needed; 3-attempt failure limit; WKB geometry
- **variadic-functions** — `StringPrimary` for booleans in DQL; `BooleanValidationTrait`; order patterns longest-first
