---
description: "Mandatory steps when adding a DQL function: source location + PHPDoc, group TestCase registration, AVAILABLE-FUNCTIONS + the always-required integration docs, group-specific doc, conditional README/new-group doc"
alwaysApply: true
trigger: always_on
applyTo: "**"
type: always_apply
---

# DQL Function Completeness Checklist

Required for every new DQL function. Missing any one of these silently breaks discoverability, app registration, or integration test coverage.

## 1. Source location — `src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/`

Subfolders exist only for **PostgreSQL extensions**. Core PostgreSQL functions always go in the root `Functions/` folder regardless of category.

| If the function… | Place it in… |
|------------------|-------------|
| Is core PostgreSQL (no extension required) | `Functions/{FunctionName}.php` |
| Belongs to a known PostgreSQL extension | `Functions/{Group}/{FunctionName}.php` — use the **PHP folder name** from the table below |
| Is the **first function** from a new PostgreSQL extension | Create `Functions/{NewExtension}/{FunctionName}.php` AND `tests/Integration/.../Functions/{NewExtension}/TestCase.php` — see §5 for the additional steps a new extension triggers |

**Known group folder names** (use these exactly — do not invent new names):

| PostgreSQL extension | PHP folder | Group doc |
|----------------------|-----------|-----------|
| PostGIS spatial | `PostGIS/` | `docs/SPATIAL-FUNCTIONS-AND-OPERATORS.md` |
| pg_trgm | `Trgm/` | `docs/TEXT-AND-PATTERN-FUNCTIONS.md` |
| pgvector | `Vector/` | `docs/AVAILABLE-FUNCTIONS-AND-OPERATORS.md` only |
| ltree | `Ltree/` | `docs/LTREE-TYPE.md` |
| fuzzystrmatch | `Fuzzystrmatch/` | `docs/TEXT-AND-PATTERN-FUNCTIONS.md` |

> **`Network/` is a one-off exception.** Network address functions (`inet`, `cidr`) are core PostgreSQL — no extension needed — but they were placed in `Functions/Network/` before the extension-only rule was established. Use `Functions/Network/` for new network functions. Do **not** create other core-PostgreSQL subfolders; this folder is the only planned deviation from the rule.

**Core function → group doc mapping** (no subfolder; update the matching doc):

| Category | Group doc |
|----------|-----------|
| Array, JSON, JSONB | `docs/ARRAY-AND-JSON-FUNCTIONS.md` |
| Date, time, range | `docs/DATE-AND-RANGE-FUNCTIONS.md` |
| Mathematical, trigonometric | `docs/MATHEMATICAL-FUNCTIONS.md` |
| Network address (`inet`, `cidr`) | `docs/NETWORK-FUNCTIONS.md` |
| Text, pattern matching, hashing | `docs/TEXT-AND-PATTERN-FUNCTIONS.md` |
| Type conversion, formatting, UUID | `docs/UTILITY-FUNCTIONS.md` |
| XML | `docs/XML-FUNCTIONS.md` |

## 2. Class-level PHPDoc

**Required**: every DQL function class has a class-level docblock with these fields in this exact order:

```php
/**
 * Implementation of PostgreSQL FUNCTION_NAME().
 *
 * One-sentence description of what the function does or returns.
 *
 * @see https://www.postgresql.org/docs/18/functions-<category>.html
 * @since X.Y
 *
 * @author First Last <email@example.com>
 *
 * @example Using it in DQL: "SELECT FUNCTION_NAME(e.column) FROM Entity e"
 */
```

### First-line casing

**Required**: the PostgreSQL function name is always **UPPERCASE** followed by `()`.

```php
// ✓ Correct
 * Implementation of PostgreSQL XPATH().
 * Implementation of PostgreSQL XML_IS_WELL_FORMED().
 * Implementation of PostgreSQL MIN_SCALE().

// ❌ Wrong — lowercase
 * Implementation of PostgreSQL xpath().
 * Implementation of PostgreSQL xml_is_well_formed().
```

**Exceptions** — no `()` when there is no callable function name:
- SQL operators: `* Implementation of PostgreSQL @> operator.`
- Extension-prefixed operators: `* Implementation of PostgreSQL pg_trgm % operator.`
- Non-function features: `* Implementation of PostgreSQL composite type field access.`

### `@example` format

Pick the form that best describes the usage shown:

| Form | When to use |
|------|-------------|
| `Using it in DQL: "SELECT FUNC(e.col) FROM Entity e"` | Standard SELECT — use for most functions |
| `Using it in DQL with WHERE: "WHERE FUNC(e.col) > 0"` | Function appears only in WHERE, never SELECT |
| `Using it in DQL with boolean comparison: "WHERE FUNC(e.x, e.y) = TRUE"` | Boolean-returning operators and predicates |
| `Using it in DQL (geography): "SELECT FUNC(e.col) FROM Entity e"` | Multiple overloads distinguishable by input type — replace `geography` with the actual qualifier (`3D`, `PROJ`, etc.) |
| `Using it in DQL with normalization: "SELECT FUNC(e.col, 1) FROM Entity e"` | Optional parameter variant — replace `normalization` with the actual qualifier (`config`, `custom costs`, `weights`, etc.) |

Rules:
- DQL snippet is always in double quotes
- SELECT examples include `FROM Entity e` (or `FROM Entity g` for geometry)
- WHERE examples show only the condition, not a full DQL query

## 3. Integration test registration — group `TestCase::getStringFunctions()`

**Required**: register the new function in the group's integration TestCase.

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

When this is the **first function from a new PostgreSQL extension**: create `tests/Integration/.../Functions/{Extension}/TestCase.php` extending the base integration TestCase, and override `getStringFunctions()`. If the extension's functions operate on a data shape that has no fixture entity yet, add one in `fixtures/MartinGeorgiev/Doctrine/Entity/`.

## 4. Required documentation — update every time

Update **all** for every new function:

| File | What to add |
|------|-------------|
| `docs/AVAILABLE-FUNCTIONS-AND-OPERATORS.md` | Entry in the Quick Reference section under the function's category |
| `docs/INTEGRATING-WITH-DOCTRINE.md` | `addCustomStringFunction()` call under the matching `# <category> functions` comment |
| `docs/INTEGRATING-WITH-SYMFONY.md` | YAML entry under `doctrine.orm.dql.string_functions`, same section comment |
| `docs/INTEGRATING-WITH-LARAVEL.md` | PHP array entry, same section comment |
| Group-specific doc (see §1 mapping tables) | Row in the function table; add a usage example if the file has an examples section |

## 5. Conditional documentation — first function from a new PostgreSQL extension only

Only applies when this is the **first function being added from a previously unsupported PostgreSQL extension** (e.g. adding the first PostGIS function, or the first pgvector function). Skip entirely for routine additions to an existing extension group or to core PostgreSQL functions.

| File | What to do |
|------|------------|
| `README.md` | Add a new bullet for the extension under `### Functions`. Do **not** touch README for routine additions within an existing extension. |
| New group doc | Create `docs/{EXTENSION-NAME}-FUNCTIONS.md` (or equivalent) and link it from `docs/AVAILABLE-FUNCTIONS-AND-OPERATORS.md` and `README.md`. |

## 6. Verify completeness before declaring done

```bash
# All must show a match for the new function name:
grep -l "{FUNCTION_NAME}" docs/AVAILABLE-FUNCTIONS-AND-OPERATORS.md docs/INTEGRATING-WITH-DOCTRINE.md docs/INTEGRATING-WITH-SYMFONY.md docs/INTEGRATING-WITH-LARAVEL.md

# Registration in the group TestCase:
grep -r "'{FUNCTION_NAME}' =>" tests/Integration/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/
```
