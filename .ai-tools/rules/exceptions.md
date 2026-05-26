---
description: "Domain-specific exception patterns for DBAL types: naming, location, factory methods"
alwaysApply: true
trigger: always_on
applyTo: "**"
type: always_apply
---

# Exception Handling

## Use Domain-Specific Exceptions
NEVER use generic exceptions like `ConversionException`, `InvalidArgumentException`, or `\Throwable` directly in DBAL types. ALWAYS create and use domain-specific exceptions.

**Pattern**: `Invalid{TypeName}For{PHP|Database}Exception`

For array types: `Invalid{TypeName}ArrayItemFor{PHP|Database}Exception`

Examples:
- `InvalidUuidArrayItemForPHPException` - for PHP conversion errors in UUID arrays
- `InvalidUuidArrayItemForDatabaseException` - for database conversion errors in UUID arrays
- `InvalidPointForPHPException` - for PHP conversion errors in Point type
- `InvalidPointForDatabaseException` - for database conversion errors in Point type

**Method mapping**:
- `convertToPHPValue` / `transformArrayItemForPHP` → use `*ForPHPException`
- `convertToDatabaseValue` / `transformArrayItemForPostgres` → use `*ForDatabaseException`

**Location**: two namespaces, by usage:

| Namespace | Use for |
|-----------|---------|
| `src/MartinGeorgiev/Doctrine/DBAL/Types/Exceptions/` | Exceptions thrown by DBAL type classes themselves (`convertToDatabaseValue`, `convertToPHPValue`, `transformArrayItemForPostgres`, `transformArrayItemForPHP`). The `Invalid{TypeName}For{PHP|Database}Exception` family lives here. |
| `src/MartinGeorgiev/Doctrine/DBAL/Types/ValueObject/Exceptions/` | Exceptions thrown by value objects under `Types/ValueObject/` (e.g. `InvalidBoxException`, `InvalidLtreeException`). These signal construction/format errors at VO level, before any DBAL type sees them. |

**Default parent class**: `Doctrine\DBAL\Types\ConversionException`.

**Deviation allowed if justified**: extending a different exception (PHP SPL, another Doctrine class, or a domain-specific base) is permitted only with a concrete reason that does not fit `ConversionException`. The reason **must** be stated in the class-level PHPDoc — otherwise a future agent will "normalize" it back.

```php
// ❌ Wrong — non-Doctrine parent with no justification
final class InvalidRangeForDatabaseException extends \InvalidArgumentException

// ✓ Default — extend ConversionException
final class InvalidRangeForDatabaseException extends ConversionException

// ✓ Justified deviation — class PHPDoc explains the choice over ConversionException.
//   Class name below is a placeholder — substitute the real type/concern.
/**
 * Extends \LogicException because this signals a configuration/programmer
 * error caught at boot time, not a per-value runtime conversion failure.
 * Using ConversionException would mislead consumers who catch it expecting
 * recoverable per-row failures.
 *
 * @since X.Y
 *
 * @author ...
 */
final class Some<Concern>Exception extends \LogicException
```

## Class-level PHPDoc

**Required**: every exception class has a class-level docblock with `@since` and `@author`.

```php
/**
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class InvalidRangeForDatabaseException extends ConversionException
```

## Static Factory Methods

**Required structure**: a private `create()` helper plus `for*`-named public factories.

```php
// ✓ Correct — canonical factory shape
final class InvalidUuidArrayItemForDatabaseException extends ConversionException
{
    private static function create(string $message, mixed $value): self
    {
        return new self(\sprintf($message, \var_export($value, true)));
    }

    public static function forInvalidType(mixed $value): self
    {
        return self::create('Array items must be UUID strings, %s given', $value);
    }

    public static function forInvalidFormat(mixed $value): self
    {
        return self::create('Invalid UUID format in array: %s', $value);
    }
}
```

**Factory naming**: `for{Reason}()`. Standard verbs: `forInvalidType()`, `forInvalidFormat()`. Domain-specific reasons keep the `for*` prefix (e.g., `forUnsupportedBoundedInfinity()`).

```php
// ❌ Wrong — non-standard verb
public static function isNotAPoint(mixed $value): self

// ✓ Correct
public static function forInvalidType(mixed $value): self
```

## Message Formatting

**Required**:
- Use the `create()` helper — do **not** call `new self(\sprintf(...))` directly in each factory.
- Format the offending value with `\var_export($value, true)` (delivered via `create()`). Do **not** use `\gettype()` or `\get_debug_type()` — those drop the actual value.
- Message shape: `"<What it must be>, %s given"` for type errors, `"Invalid <thing> format: %s"` for format errors.

```php
// ❌ Wrong — bypasses create(), uses \gettype()
return new self(\sprintf('Value must be a Ltree, %s given', \gettype($value)));

// ❌ Wrong — bypasses create(), uses \get_debug_type()
return new self(\sprintf('Invalid type for range. Expected Range object or string, got %s', \get_debug_type($value)));

// ✓ Correct — routed through create(), \var_export reveals the actual value
return self::create('Value must be a Ltree, %s given', $value);
return self::create('Database value must be a Range object, %s given', $value);
```

