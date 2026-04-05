# Create a new DQL function: $ARGUMENTS

## 1. Find the right pattern

Search `src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/` for an existing function most similar to $ARGUMENTS. Read it and its base class. Read its tests too.

Subdirectories map to PostgreSQL extensions or built-in domains: `PostGIS/`, `Network/`, `Ltree/`, `Vector/` (pgvector), `Fuzzystrmatch/`, `Trgm/` (pg_trgm). Functions from a new PostgreSQL extension get their own subdirectory. Core PostgreSQL functions go in the root `Functions/` directory.

## 2. Create the function class

Follow the reference function's structure. Key rules:
- Pure numeric functions: extend `BaseArithmeticFunction` (uses `SimpleArithmeticExpression`), override `getMinArgumentCount()`/`getMaxArgumentCount()` for multi-arg. Do NOT extend `BaseVariadicFunction` with `ArithmeticPrimary` for numeric-only functions.
- Only use `BaseVariadicFunction` directly when different node types are needed per argument position (e.g., `StringPrimary` mixed with `ArithmeticPrimary`).
- Boolean parameters MUST use `StringPrimary`, not `ArithmeticPrimary` (see `.ai-tools/rules/variadic-functions.md`)
- Use `BooleanValidationTrait` for boolean validation
- Order node mapping patterns longest-first

**PHPDoc**: `@see` PostgreSQL docs, `@since X.Y` (check GitHub for next release — never guess), `@example` DQL usage.

## 3. Create tests

**Unit test** in `tests/Unit/.../Functions/{Group}/`. Read existing tests in the same directory to match patterns.

**Integration test** in `tests/Integration/.../Functions/{Group}/`. Check if a group-specific `TestCase` exists in that directory — it registers the DQL functions via `getStringFunctions()`. If you're adding a function to an existing group, extend that TestCase. If creating a new group:
- Create `tests/Integration/.../Functions/{Group}/TestCase` extending the base integration TestCase
- Override `getStringFunctions()` to register the new functions
- If the group operates on a data type that has no fixture entity, create one in `fixtures/MartinGeorgiev/Doctrine/Entity/`

Integration test method naming and structure:
- At least 2 tests per function: one with literal(s), one with entity property/properties
- Method names: `can_{verb}_{function}_of_literal` and `can_{verb}_{function}_with_entity_property` (plural for multi-arg)
- Use `can_calculate_` for deterministic functions, `can_generate_` for random/non-deterministic
- **NEVER embed exact test data in method names** (e.g., `can_calculate_gcd_of_literals` NOT `can_calculate_gcd_of_twelve_and_eight`)
- **ALWAYS assert exact expected values** for deterministic functions — use `assertEquals`, `assertSame`, or `assertEqualsWithDelta` (for floats). NEVER use lazy `assertNotNull` when the result is known. Only use `assertNotNull` for truly non-deterministic functions (RANDOM, RANDOM_NORMAL).
- Don't add extra tests for edge cases — but the tests you do write must assert precise results
- For optional-arg functions: test each distinct arity

Use existing fixture data, filter by specific IDs. PostGIS results are WKB hex — assert via measurement/comparison functions.

## 4. Update documentation

These are NOT auto-generated — agents must update them manually (check last 3-4 releases for scope):
- `README.md` — add feature bullets under the relevant category
- `docs/AVAILABLE-FUNCTIONS-AND-OPERATORS.md` — add to the function index and quick reference
- Group-specific doc if one exists (e.g., `docs/NETWORK-FUNCTIONS.md`, `docs/SPATIAL-FUNCTIONS-AND-OPERATORS.md`, `docs/ARRAY-AND-JSON-FUNCTIONS.md`, `docs/DATE-AND-RANGE-FUNCTIONS.md`, `docs/TEXT-AND-PATTERN-FUNCTIONS.md`, `docs/MATHEMATICAL-FUNCTIONS.md`)
- `docs/INTEGRATING-WITH-DOCTRINE.md` — add `addCustomStringFunction()` registration
- `docs/INTEGRATING-WITH-SYMFONY.md` — add YAML config registration
- `docs/INTEGRATING-WITH-LARAVEL.md` — add PHP array config registration
- If this is a brand new function group: create a new doc in `docs/`, link it from `docs/AVAILABLE-FUNCTIONS-AND-OPERATORS.md` and `README.md`

Read each doc before editing to match the existing format.

## 5. Verify

1. `./bin/phpstan analyse --configuration=ci/phpstan/config.neon <new-src-files> --memory-limit=512M`
2. `./bin/phpunit --filter "{FunctionName}Test" --configuration ci/phpunit/config-unit.xml`

Max 3 attempts before asking for guidance.
