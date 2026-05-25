---
description: "BaseArray subclass pattern: required methods, defensive guard, unit test methods, integration registration"
alwaysApply: true
trigger: always_on
applyTo: "**"
type: always_apply
---

# DBAL Array Types

## Required Method Set for BaseArray Subclasses
Every `BaseArray` subclass MUST implement all of these methods. Do NOT remove any of them to improve coverage or eliminate "dead code":

```php
public function isValidArrayItemForDatabase(mixed $item): bool
protected function transformArrayItemForPostgres(mixed $item): string // guard inside is intentional
public function transformArrayItemForPHP(mixed $item): ?ValueObject
protected function transformPostgresArrayToPHPArray(string $postgresArray): array
protected function throwInvalidTypeException(mixed $value): never
protected function throwInvalidItemException(mixed $item): never
```

The guard (`if (!$item instanceof X)` throw) inside `transformArrayItemForPostgres` is **intentional defensive code**. `convertToDatabaseValue` pre-filters via `isValidArrayItemForDatabase` + `throwInvalidItemException`, so the guard is unreachable through normal flow. It exists to protect direct calls to the protected method. Do NOT remove `isValidArrayItemForDatabase` or `throwInvalidItemException` to make the guard reachable — that inverts the pattern.

## Integration Test Registration
Every new DBAL type MUST be registered in `tests/Integration/MartinGeorgiev/TestCase.php::registerCustomTypes()` or integration tests will fail with `UnknownColumnType`. Add both the `use` import and the `$typesMap` entry:

```php
use MartinGeorgiev\Doctrine\DBAL\Types\FooArray;
// ...
'foo[]' => FooArray::class,
```

## Required Unit Test Methods for Array Types
When writing unit tests for a `BaseArray` subclass, include these methods (canonical names — do not rename):

```php
// covers isValidArrayItemForDatabase
#[DataProvider('provideValidArrayItemsForDatabase')]
public function can_validate_valid_array_item_for_database(mixed $value): void

public static function provideValidArrayItemsForDatabase(): array  // include null, VO instances

#[DataProvider('provideInvalidArrayItemsForDatabase')]
public function can_validate_invalid_array_item_for_database(mixed $value): void

public static function provideInvalidArrayItemsForDatabase(): array  // string, int, bool, stdClass

// covers transformArrayItemForPHP null branch
public function can_transform_null_item_for_php(): void

// covers transformArrayItemForPHP non-string guard
public function throws_exception_for_non_string_item_from_database(): void
// calls $this->fixture->transformArrayItemForPHP(42) — direct call, not via convertToPHPValue
```

The guard in `transformArrayItemForPostgres` is the only line intentionally left uncovered. Do not write tests that try to reach it through protected method access.

## PHPStan Ignore on convertToDatabaseValue Calls in Tests
Direct calls to `convertToDatabaseValue($phpValue, $this->platform)` in unit tests require `// @phpstan-ignore-line` because PHPStan cannot verify the mixed-typed argument at level max.
