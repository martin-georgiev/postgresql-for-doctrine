# Create a new DQL function: $ARGUMENTS

## 1. Find the right pattern

Search `src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/` for an existing function most similar to $ARGUMENTS. Read it and its base class. Read its tests too.

Subdirectories map to PostgreSQL extensions or built-in domains: `PostGIS/`, `Network/`, `Ltree/`, `Vector/` (pgvector), `Fuzzystrmatch/`, `Trgm/` (pg_trgm). Functions from a new PostgreSQL extension get their own subdirectory. Core PostgreSQL functions go in the root `Functions/` directory.

## 2. Create the function class

Follow the reference function's structure. Key rules from `.ai-tools/rules/variadic-functions.md`:
- Boolean parameters MUST use `StringPrimary`, not `ArithmeticPrimary`
- Use `BooleanValidationTrait` for boolean validation
- Order node mapping patterns longest-first

**PHPDoc**: `@see` PostgreSQL docs, `@since X.Y` (check GitHub for next release — never guess), `@example` DQL usage.

## 3. Create tests

**Unit test** in `tests/Unit/.../Functions/{Group}/`. Read existing tests in the same directory to match patterns.

**Integration test** in `tests/Integration/.../Functions/{Group}/`. Check if a group-specific `TestCase.php` exists in that directory — it registers the DQL functions via `getStringFunctions()`. If you're adding a function to an existing group, extend that TestCase. If creating a new group:
- Create `tests/Integration/.../Functions/{Group}/TestCase.php` extending the base integration TestCase
- Override `getStringFunctions()` to register the new functions
- If the group operates on a data type that has no fixture entity, create one in `fixtures/MartinGeorgiev/Doctrine/Entity/`

Use existing fixture data, filter by specific IDs. PostGIS results are WKB hex — assert via measurement/comparison functions.

## 4. Update documentation

These are NOT auto-generated — agents must update them manually:
- `docs/AVAILABLE-FUNCTIONS-AND-OPERATORS.md` — add to the function index (always)
- Group-specific doc if one exists (e.g., `docs/NETWORK-FUNCTIONS.md`, `docs/SPATIAL-FUNCTIONS-AND-OPERATORS.md`, `docs/ARRAY-AND-JSON-FUNCTIONS.md`, `docs/DATE-AND-RANGE-FUNCTIONS.md`, `docs/TEXT-AND-PATTERN-FUNCTIONS.md`, `docs/MATHEMATICAL-FUNCTIONS.md`)
- If this is a brand new function group: create a new doc in `docs/`, link it from `docs/AVAILABLE-FUNCTIONS-AND-OPERATORS.md` and `README.md`

Read each doc before editing to match the existing format.

## 5. Verify

1. `./bin/phpstan analyse --configuration=ci/phpstan/config.neon <new-src-files> --memory-limit=512M`
2. `./bin/phpunit --filter "{FunctionName}Test" --configuration ci/phpunit/config-unit.xml`

Max 3 attempts before asking for guidance.
