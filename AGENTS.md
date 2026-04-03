# PostgreSQL for Doctrine

PHP library enhancing Doctrine ORM with PostgreSQL-specific DBAL types, DQL functions, and PostGIS support. PHP 8.2+, PostgreSQL 9.4+.

## Project Layout

```
src/MartinGeorgiev/Doctrine/
  DBAL/Types/              # Custom DBAL types (arrays, ranges, geometric, network, etc.)
  DBAL/Types/Exceptions/   # Domain-specific exceptions per type
  DBAL/Types/ValueObject/  # Immutable value objects for composite types
  DBAL/Types/Traits/       # Shared validation/conversion traits
  ORM/Query/AST/Functions/ # DQL function implementations (~340 functions)
    Network/               # Network address functions (inet, cidr, macaddr)
    PostGIS/               # Spatial functions (ST_*)
    Ltree/                 # Hierarchical label tree functions
    Vector/                # pgvector similarity search functions
    Fuzzystrmatch/         # Fuzzy string matching functions
    Trgm/                  # Trigram similarity functions
tests/
  Unit/                    # PHPUnit unit tests (mirrors src/ structure)
  Integration/             # Docker-based integration tests (real PostgreSQL + PostGIS)
fixtures/                  # Centralized test fixture entities
ci/
  phpstan/config.neon      # PHPStan level max
  phpunit/                 # PHPUnit configs (unit + integration)
```

## Key Workflows

**Unit tests**: `composer run-unit-tests`
**Integration tests**: `composer run-integration-tests` (requires Docker)
**PHPStan**: `./bin/phpstan analyse --configuration=ci/phpstan/config.neon <files> --memory-limit=512M`
**Code style check**: `composer check-code-style`
**Code style fix**: `composer fix-code-style`
**Full static analysis**: `composer run-static-analysis`
**All tests**: `composer run-all-tests`

## Rules

Detailed coding standards and rules with examples live in `.ai-tools/rules/`, auto-loaded by Claude Code, Cursor, Windsurf, AugmentCode, and GitHub Copilot via symlinks.
