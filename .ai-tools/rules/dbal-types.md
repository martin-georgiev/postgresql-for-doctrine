---
description: "DBAL types: completeness checklist, class conventions, BaseArray subclass pattern, integration test generics, scalar+array doc pairing"
alwaysApply: true
trigger: always_on
applyTo: "**"
type: always_apply
---

# DBAL Types

See `exceptions.md` for exception classes thrown by DBAL types. See `test-naming-patterns.md` for test-method naming and required test methods on array types.

## Completeness Checklist

Required for every new DBAL type. Missing any one of these leaves the type unusable in apps, undiscoverable in docs, or untested.

### 1. Source registration — `src/MartinGeorgiev/Doctrine/DBAL/Type.php`

**Required**: Add a `public const` for the PostgreSQL type name.

```php
// ✓ Correct
public const CITEXT = 'citext';
public const CITEXT_ARRAY = 'citext[]';
```

This constant is referenced by `protected const TYPE_NAME` in the DBAL type class. Skipping it breaks the type's `getName()`.

### 2. Integration TestCase registration — `tests/Integration/MartinGeorgiev/TestCase.php`

**Required**: Add a `use` import and an entry to the `$typesMap` array inside `registerCustomTypes()`, alphabetical by key.

```php
use MartinGeorgiev\Doctrine\DBAL\Types\Citext;
use MartinGeorgiev\Doctrine\DBAL\Types\CitextArray;
// ...
'citext'   => Citext::class,
'citext[]' => CitextArray::class,
```

```php
// ❌ Wrong — type registered in Type.php but not in TestCase.php
// Result: integration tests cannot bind values, no end-to-end coverage
```

Scalar and array variants must be registered as adjacent pairs (see § Documentation: Pair Scalar + Array Registrations below).

### 3. Integration documentation — the files below, every time

**Required** for every new type (scalar AND array variant):

| File | What to add |
|------|-------------|
| `docs/AVAILABLE-TYPES.md` | Row in the type catalog table |
| `docs/INTEGRATING-WITH-DOCTRINE.md` | `Type::addType(...)` registration example, paired with array variant |
| `docs/INTEGRATING-WITH-SYMFONY.md` | YAML `doctrine.dbal.types` entry |
| `docs/INTEGRATING-WITH-LARAVEL.md` | PHP config entry |

### 4. Conditional documentation

| File | When to update |
|------|---------------|
| `README.md` | Only when introducing a brand-new feature **group** (e.g. first range type, first PostGIS type). Routine additions within an existing group: do **not** touch README. |
| `docs/{GROUP}-TYPE.md` / `docs/{GROUP}-TYPES.md` | If the type belongs to a group with its own dedicated doc (`RANGE-TYPES.md`, `SPATIAL-TYPES.md`, `GEOMETRY-ARRAYS.md`, `LTREE-TYPE.md`, `ENUM-TYPE.md`, etc.) |

## Class Conventions

### Class-Level PHPDoc

The description should call out non-obvious PostgreSQL semantics (case-insensitivity, dimensional modifiers, infinity handling). Point `@see` at the PostgreSQL documentation page for the type itself — `https://www.postgresql.org/docs/18/citext.html` for `citext`, `.../rangetypes.html` for the range family, the PostGIS manual for the spatial types. See `since-annotations.md` for choosing the `@since` value.

### Method PHPDoc: Minimal Only

Running prose above the tags is for the non-obvious PostgreSQL reason only — `Lquery::convertToPHPValue()` explains why it does not re-validate. A restatement of the signature is not that.

### Data Provider Shape: The Named Key IS the Label

**Principle**: Each dataset row is keyed by a string that names the case. That key **is** the human-readable label. The row itself contains only the values the test method consumes.

**Forbidden**: Re-stating the label inside the row — whether as a leading `string $testName`, `string $label`, `string $description`, or any other "name" parameter. Every such variant duplicates information PHPUnit already prints from the array key.

```php
// ❌ Wrong — the row's first element repeats the array key
public function roundtrips_value(string $testName, array $arrayValue): void { ... }

public static function provideValidTransformations(): array
{
    return [
        'simple object' => ['simple object', ['foo' => 'bar']],
    ];
}

// ✓ Correct — the array key carries the label; the row holds only test inputs
public function roundtrips_value(array $arrayValue): void { ... }

public static function provideValidTransformations(): array
{
    return [
        'simple object' => [['foo' => 'bar']],
    ];
}
```

See `test-naming-patterns.md` § Data Provider Conventions for the full provider rules.

---

## BaseArray Subclass Pattern

### Required Methods

All five methods are required on every `BaseArray` subclass. Never remove any:

```php
public function isValidArrayItemForDatabase(mixed $item): bool
protected function transformArrayItemForPostgres(mixed $item): string
public function transformArrayItemForPHP(mixed $item): ?ValueObject
protected function throwInvalidTypeException(mixed $value): never
protected function throwInvalidItemException(mixed $item): never
```

The `if (!$item instanceof X) throw` guard inside `transformArrayItemForPostgres` is intentionally unreachable via normal flow — it guards direct calls. Never remove `isValidArrayItemForDatabase` or `throwInvalidItemException` to make it reachable.

### Required Unit Test Methods

See `test-naming-patterns.md` → **Required Unit Test Methods: Array DBAL Types** for the canonical method/provider/exception tables, `provideValidTransformations` content rules, and PHPStan ignore-line policy. The guard inside `transformArrayItemForPostgres` (described above) is the single intentionally uncovered line.

---

## Documentation: Pair Scalar + Array Registrations

In `docs/INTEGRATING-WITH-DOCTRINE.md`, `docs/INTEGRATING-WITH-SYMFONY.md`, and `docs/INTEGRATING-WITH-LARAVEL.md`, the scalar type and its array variant must be registered as adjacent lines in the same section. Follow the multirange pattern — never create a separate "array types" section:

```php
// ✓ Correct — scalar and array adjacent
Type::addType('daterange',   "...\\DateRange");
Type::addType('daterange[]', "...\\DateRangeArray");
Type::addType('int4range',   "...\\Int4Range");
Type::addType('int4range[]', "...\\Int4RangeArray");
```
