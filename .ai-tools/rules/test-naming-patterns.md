---
description: "Test naming: PHPUnit attribute order, method verbs, provider conventions, class structure"
alwaysApply: true
trigger: always_on
applyTo: "**"
type: always_apply
---

# Test Naming Patterns

## Class Declaration

All test classes are `final class`, not just `class`.

## PHPUnit Attribute Order

`#[DataProvider]` always comes **before** `#[Test]`, each on its own line:

```php
#[DataProvider('provideValidTransformations')]
#[Test]
public function converts_to_database_value(...): void
```

## Method Naming

All test methods use **snake_case**. The verb expresses what the test verifies:

| Verb prefix | When to use |
|-------------|-------------|
| `has_*` | Type name / configuration existence — e.g., `has_name()` |
| `converts_*` | Conversion operations — e.g., `converts_to_database_value()`, `converts_null_to_php_value()` |
| `converts_null_to_*_value` | Explicit null handling (standalone, no provider) — e.g., `converts_null_to_database_value()` |
| `throws_exception_for_*` | Error handling — e.g., `throws_exception_for_invalid_type_inputs()` |
| `validates_*` | Validation methods — e.g., `validates_valid_array_item_for_database()` |
| `roundtrips_*` | Integration: DB round-trip — e.g., `roundtrips_value()`, `roundtrips_null_value()`, `roundtrips_empty_array()` |
| `rejects_*` | Integration: rejection/error — e.g., `rejects_invalid_value()`, `rejects_string_instead_of_value_object()` |
| `parses_*` | Parsing from string — e.g., `parses_from_string()`, `parses_hstore_string_to_array()` |
| `returns_*` | Return-value assertions — e.g., `returns_correct_coordinates_via_getters()` |
| `preserves_*` | Preservation of precision/state — e.g., `preserves_string_representation()` |
| `normalizes_*` | Normalization — e.g., `normalizes_various_input_formats()` |
| `creates_*` | Creation/factory — e.g., `creates_simple_range()` |
| `accepts_*` | Input acceptance — e.g., `accepts_high_precision_coordinates()` |
| `is_*` | State/boolean checks — e.g., `is_constructed_with_float_values()` |

## Unit Test Structure: Scalar DBAL Types

```php
final class FooTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    private Foo $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Foo();
    }

    #[Test]
    public function has_name(): void { ... }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_database_value(...): void { ... }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function converts_to_php_value(...): void { ... }

    // Only when null is NOT in provideValidTransformations:
    #[Test]
    public function converts_null_to_database_value(): void { ... }

    #[Test]
    public function converts_null_to_php_value(): void { ... }

    #[DataProvider('provideInvalidDatabaseValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_database_value_inputs(mixed $value): void { ... }

    #[DataProvider('provideInvalidPHPValueInputs')]
    #[Test]
    public function throws_exception_for_invalid_php_value_inputs(mixed $value): void { ... }
}
```

**Exception convention for scalar types**:
- `throws_exception_for_invalid_database_value_inputs` → tests `convertToDatabaseValue` with bad PHP input
- `throws_exception_for_invalid_php_value_inputs` → tests `convertToPHPValue` with bad DB string

## Integration Test Structure: Scalar DBAL Types

All extend `ScalarTypeTestCase` (which provides `roundtrips_null_value()`):

```php
final class FooTypeTest extends ScalarTypeTestCase
{
    protected function getTypeName(): string { return 'foo'; }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value(string $testValue): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();
        $this->runDbalBindingRoundTrip($typeName, $columnType, $testValue);
    }

    public static function provideValidTransformations(): array { ... }

    // Single known bad value → standalone (no provider):
    #[Test]
    public function rejects_empty_string(): void
    {
        $this->expectException(InvalidFooForPHPException::class);
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();
        $this->runDbalBindingRoundTrip($typeName, $columnType, '');
    }

    // Multiple distinct bad values → use a provider:
    #[DataProvider('provideInvalidValues')]
    #[Test]
    public function rejects_invalid_value(mixed $value): void
    {
        $this->expectException(InvalidFooForPHPException::class);
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();
        $this->runDbalBindingRoundTrip($typeName, $columnType, $value);
    }

    public static function provideInvalidValues(): array { ... }
}
```

`runDbalBindingRoundTrip` **always** uses the verbose style — local variables, never inlined:

```php
// Correct:
$typeName = $this->getTypeName();
$columnType = $this->getPostgresTypeName();
$this->runDbalBindingRoundTrip($typeName, $columnType, $value);

// Wrong — do not inline:
$this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $value);
```

## Integration Test Structure: Array DBAL Types

All extend `ArrayTypeTestCase` (which provides `roundtrips_null_value()`, `roundtrips_empty_array()`, `roundtrips_value()`). Concrete classes only need:

```php
final class FooArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string { return 'foo[]'; }

    public static function provideValidTransformations(): array { ... }

    // Optional: rejection test when type expects value objects
    #[Test]
    public function rejects_string_instead_of_value_object(): void { ... }
}
```

## Required Unit Test Methods: Array DBAL Types

### Core methods — all array types

These exact method and provider names are required on every array type unit test:

| Method | Provider | Exception | Tests |
|--------|----------|-----------|-------|
| `has_name` | — | — | `assertSame('foo[]', $this->fixture->getName())` |
| `converts_to_database_value` | `provideValidTransformations` | — | round-trip including null and empty array cases |
| `converts_to_php_value` | `provideValidTransformations` | — | same provider as above |
| `throws_exception_for_invalid_type_inputs` | `provideInvalidTypeInputs` | `ForPHPException` | non-array scalar passed to `convertToDatabaseValue` — provider: `['string instead of array' => ['not-an-array']]` |
| `throws_exception_for_invalid_database_value_inputs` | `provideInvalidDatabaseValueInputs` | `ForDatabaseException` | arrays whose items fail validation — passed to `convertToDatabaseValue` |
| `validates_valid_array_item_for_database` | `provideValidArrayItemsForDatabase` | — | `assertTrue($this->fixture->isValidArrayItemForDatabase($value))` |
| `validates_invalid_array_item_for_database` | `provideInvalidArrayItemsForDatabase` | — | `assertFalse($this->fixture->isValidArrayItemForDatabase($value))` |
| `converts_null_item_to_php_value` | — | — | `assertNull($this->fixture->transformArrayItemForPHP(null))` |
| `throws_exception_for_non_string_item_from_database` | — | `ForPHPException` | direct call: `$this->fixture->transformArrayItemForPHP(123)` |

`provideValidTransformations` **must** include `'null'` (phpValue=null, postgresValue=null) and `'empty array'` (phpValue=[], postgresValue='{}') cases alongside real data.

**`provideValidArrayItemsForDatabase`**:
- For **primitive/string item types** (e.g., `MoneyArray`, `TextArray`): include `null` plus valid string items.
- For **value-object item types** (e.g., `PointArray`, `LineArray`): include only valid VO instances — **no null**.

### Additional methods for value-object item types

Array types whose items are value objects require these additional tests:

| Method | Provider | Exception | Tests |
|--------|----------|-----------|-------|
| `throws_exception_for_invalid_php_value_inputs` | `provideInvalidPHPValueInputs` | `ForPHPException` | malformed postgres strings passed to `convertToPHPValue` |
| `throws_exception_for_non_string_inputs_to_database_conversion` | `provideInvalidPHPValueTypes` | `ForPHPException` | int, object, bool passed to `convertToDatabaseValue` |
| `throws_exception_when_invalid_{type}_format_provided` | — | `ForPHPException` | direct call: `transformArrayItemForPHP('(invalid,string)')` |
| `throws_exception_for_malformed_{type}_strings_in_database` | — | `ForPHPException` | `convertToPHPValue` with a malformed embedded item |
| `throws_exception_for_invalid_{type}_array_items` | `provideInvalid{Type}ArrayItems` | `ForDatabaseException` | arrays of invalid items passed to `convertToDatabaseValue` |
| `returns_empty_array_for_malformed_input` | — | — | `convertToPHPValue('{}')`, `convertToPHPValue('{invalid}')`, `convertToPHPValue('{""}')` all return `[]` |

`{type}` is replaced by the type name (e.g., `line`, `point`).

### PHPStan ignore lines on array type tests

**When required**: `convertToDatabaseValue($val, $platform)` calls in exception-testing methods, where `mixed $phpValue` would otherwise trigger an argument-type error.

**When NOT required**:
- Valid round-trip calls (the value matches the parameter type).
- Direct calls to `transformArrayItemForPHP()` — the parameter is already `mixed`, no ignore needed.

```php
// ✓ Required — exception-testing path
$this->fixture->convertToDatabaseValue('not-an-array', $this->platform); // @phpstan-ignore-line

// ✓ Not required — direct call, parameter is mixed
$this->fixture->transformArrayItemForPHP(123);

// ❌ Wrong — adding the ignore to a valid round-trip
$this->fixture->convertToDatabaseValue($validArray, $this->platform); // @phpstan-ignore-line
```

The guard inside `transformArrayItemForPostgres` (see `dbal-types.md` § BaseArray Subclass Pattern) is the only intentionally uncovered line.

## When to Use a Data Provider vs a Standalone Test

**Use `#[DataProvider]` when** two or more distinct inputs exercise the same code path:
- Multiple valid transformations (different value shapes, edge values)
- Multiple invalid inputs that each trigger the same exception
- Multiple items for `isValidArrayItemForDatabase` (valid or invalid)
- Multiple integration round-trip values

**Use a standalone `#[Test]` (no provider) when** there is exactly one meaningful input or the case is semantically isolated:
- Single hardcoded special value: `null`, `''`, `123`, `true`
- Type-name check: `has_name()`
- One unique boundary condition: `converts_null_to_database_value()`, `roundtrips_null_value()`, `roundtrips_empty_array()`
- Unique behavior that doesn't vary by input: `throws_exception_for_non_string_item_from_database()` (always `transformArrayItemForPHP(123)`)
- Multiple assertions that test one cohesive scenario: `returns_empty_array_for_malformed_input()` (tests 3 bad strings, all expecting `[]`)

**Exception — single-case provider is still correct** when the method belongs to a family of provider-driven methods for consistency. Example: `provideInvalidTypeInputs` always contains exactly one entry (`'string instead of array'`) but uses provider form because all sibling exception-test methods also use providers.

## Data Provider Conventions

- Naming: `provide*` prefix, e.g., `provideValidTransformations`, `provideInvalidDatabaseValueInputs`
- Visibility: always `public static function`
- Return type: `array`, `\Generator`, or `iterable` (document with PHPDoc `@return`)
- **Always use named string keys** for each dataset entry:

```php
/** @return array<string, array{phpValue: string|null, postgresValue: string|null}> */
public static function provideValidTransformations(): array
{
    return [
        'null' => ['phpValue' => null, 'postgresValue' => null],
        'IPv4 address' => ['phpValue' => '192.168.1.1', 'postgresValue' => '192.168.1.1'],
    ];
}
```

Use `yield` form for generators:

```php
/** @return \Generator<string, array{string, Range}> */
public static function provideFromStringTestCases(): \Generator
{
    yield 'simple range' => ['[1,10)', $expectedRange];
}
```

## MockObject Declaration

```php
/**
 * @var AbstractPlatform&MockObject
 */
private MockObject $platform;
```

Use the multiline docblock with intersection type `PlatformClass&MockObject`. Most types use `AbstractPlatform`; use a concrete platform (e.g., `PostgreSQLPlatform`) when the DBAL type requires platform-specific SQL.
