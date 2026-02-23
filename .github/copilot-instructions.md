# PostgreSQL for Doctrine — Copilot Instructions

This library enhances Doctrine ORM with PostgreSQL-specific DBAL types, DQL functions, and PostGIS support. PHP 8.2+, PostgreSQL 9.4+.

## Project Layout

```
src/MartinGeorgiev/Doctrine/
  DBAL/Types/              # Custom DBAL types (arrays, ranges, geometric, etc.)
  DBAL/Types/Exceptions/   # Domain-specific exceptions per type
  ORM/Query/AST/Functions/ # DQL function implementations
tests/
  Integration/             # Docker-based integration tests (real PostgreSQL + PostGIS)
  Unit/                    # PHPUnit unit tests
ci/
  phpstan/config.neon      # PHPStan level max
  phpunit/                 # PHPUnit configs (unit + integration)
```

## Key Rules

Detailed instructions are in `.github/instructions/` and are automatically applied. Summary:

- **Commits**: Conventional Commits (`feat(#123): add ...`, `fix: ...`, `chore: ...`). PR titles use the same format.
- **Exceptions**: Never use generic exceptions. Use `Invalid{Type}For{PHP|Database}Exception` in `src/MartinGeorgiev/Doctrine/DBAL/Types/Exceptions/`.
- **PHPStan**: Always run with `ci/phpstan/config.neon` at level max. Assert mixed query result types before use.
- **@since tags**: Check GitHub for the next release version before adding `@since X.Y` to new classes.
- **Tests**: Use central fixture data. Filter to specific IDs. Max 3 attempts to fix failing tests.
- **Variadic DQL functions**: Booleans use `StringPrimary` (not `ArithmeticPrimary`). Use `BooleanValidationTrait`. Order node patterns longest-first.
- **Code quality**: No obvious comments. Strong, precise assertions (`assertEqualsWithDelta` over `assertStringContainsString`).
- **Geometry results**: PostGIS returns WKB hex — never assert on string content; use `ST_EQUALS`, `ST_LENGTH`, etc.
