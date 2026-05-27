---
description: "Mandatory non-code touchpoints when adding a DQL function: group TestCase registration, README + AVAILABLE-FUNCTIONS + 3 integration docs + group-specific doc"
alwaysApply: true
trigger: always_on
applyTo: "**"
type: always_apply
---

# DQL Function Completeness Checklist

Required for every new DQL function. Missing any one of these silently breaks discoverability, app registration, or integration test coverage.

## 1. Source location — `src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/`

| If the function… | Place it in… |
|------------------|-------------|
| Belongs to a PostgreSQL extension or named domain (PostGIS, pg_trgm, pgvector, ltree, fuzzystrmatch, network) | `Functions/{Group}/{FunctionName}.php` |
| Is core PostgreSQL with no group subdirectory | `Functions/{FunctionName}.php` |
| Is in a brand-new group | Create `Functions/{NewGroup}/{FunctionName}.php` AND the matching `tests/Integration/.../Functions/{NewGroup}/TestCase.php` |

## 2. Integration test registration — group `TestCase::getStringFunctions()`

**Required**: Register the new function in the group's integration TestCase so DQL queries can use it.

```php
// ✓ Correct — adding to existing group TestCase
final class GcdTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'GCD' => Gcd::class,
        ];
    }
}

// ❌ Wrong — function class exists but never registered
// Result: DQL parser cannot resolve the function, all integration tests fail
```

When creating a **new group**: create `tests/Integration/.../Functions/{Group}/TestCase.php` extending the base integration TestCase, and override `getStringFunctions()` to register the group's functions. If the group operates on a data shape that has no fixture entity, also add one in `fixtures/MartinGeorgiev/Doctrine/Entity/`.

## 3. Integration documentation — the files below, every time

**Required** for every new function:

| File | What to add |
|------|-------------|
| `docs/AVAILABLE-FUNCTIONS-AND-OPERATORS.md` | Row in the function index / quick reference |
| `docs/INTEGRATING-WITH-DOCTRINE.md` | `addCustomStringFunction()` registration call |
| `docs/INTEGRATING-WITH-SYMFONY.md` | YAML entry under `doctrine.orm.dql.string_functions` |
| `docs/INTEGRATING-WITH-LARAVEL.md` | PHP array entry under the equivalent config key |

## 4. Conditional documentation

| File | When to update |
|------|---------------|
| `README.md` | Only when introducing a brand-new function **group** (e.g. first PostGIS function, first vector function). Routine additions within an existing group: do **not** touch README. |
| Group-specific doc (`docs/NETWORK-FUNCTIONS.md`, `docs/SPATIAL-FUNCTIONS-AND-OPERATORS.md`, `docs/ARRAY-AND-JSON-FUNCTIONS.md`, `docs/DATE-AND-RANGE-FUNCTIONS.md`, `docs/TEXT-AND-PATTERN-FUNCTIONS.md`, `docs/MATHEMATICAL-FUNCTIONS.md`) | If a doc exists for your group → update it. If introducing a brand-new group with no doc → create one AND link it from `docs/AVAILABLE-FUNCTIONS-AND-OPERATORS.md` and `README.md`. |

## 5. Verify completeness before declaring done

```bash
# All must show a match for the new function name:
grep -l "{FUNCTION_NAME}" docs/AVAILABLE-FUNCTIONS-AND-OPERATORS.md docs/INTEGRATING-WITH-DOCTRINE.md docs/INTEGRATING-WITH-SYMFONY.md docs/INTEGRATING-WITH-LARAVEL.md

# Registration in the group TestCase:
grep -r "'{FUNCTION_NAME}' =>" tests/Integration/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/
```
