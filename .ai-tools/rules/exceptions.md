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

**Location**: three namespaces, by usage:

| Namespace | Use for |
|-----------|---------|
| `src/MartinGeorgiev/Doctrine/DBAL/Types/Exceptions/` | Exceptions thrown by DBAL type classes themselves (`convertToDatabaseValue`, `convertToPHPValue`, `transformArrayItemForPostgres`, `transformArrayItemForPHP`). The `Invalid{TypeName}For{PHP|Database}Exception` family lives here. |
| `src/MartinGeorgiev/Doctrine/DBAL/Types/ValueObject/Exceptions/` | Exceptions thrown by value objects under `Types/ValueObject/` (e.g. `InvalidBoxException`, `InvalidLtreeException`). These signal construction/format errors at VO level, before any DBAL type sees them. |
| `src/MartinGeorgiev/Utils/Exception/` | Exceptions thrown by the `Utils` transformers (e.g. `InvalidArrayFormatException`, `InvalidRecordFormatException`). |

## A Namespace Only Throws Its Own Exceptions

**Required**: `Utils` classes throw `Utils\Exception\*` and nothing else. A DBAL type calling a transformer catches that and rethrows its own domain exception, so callers still see the documented `Invalid{TypeName}For{PHP|Database}Exception`.

**Reason**: `Utils` sits below the types and must not depend on them. Deptrac enforces this (`composer run-static-analysis`), so a breach fails the build rather than review.

```php
// ❌ Wrong — a Utils transformer reaching up into the DBAL type exceptions
throw InvalidJsonArrayItemForPHPException::forInvalidFormat($postgresValue);

// ✓ Correct — throw at the Utils level, translate in the caller
throw InvalidJsonFormatException::invalidFormat('the value is not decodable JSON');

// ...and in the DBAL type:
try {
    return PostgresJsonToPHPArrayTransformer::transformPostgresJsonEncodedValueToPHPValue($item);
} catch (InvalidJsonFormatException) {
    throw InvalidJsonArrayItemForPHPException::forInvalidFormat($item);
}
```

The Utils exception carries the *reason* (`'the value is not decodable JSON'`); the domain exception adds the offending value. Do not duplicate the value in both.

**Default parent class**: `Doctrine\DBAL\Types\ConversionException` — for the `Types/Exceptions/` and `Utils/Exception/` families.

**The `Types/ValueObject/Exceptions/` family is different**: its parent is set by `ParentByNamespaceRector`, not chosen here. Everything else in this file applies to it unchanged, except the parent-deviation PHPDoc requirement.

**Deviation allowed if justified**: extending a different exception (PHP SPL, another Doctrine class, or a domain-specific base) is permitted only with a concrete reason that does not fit `ConversionException`. The reason **must** be stated in the class-level PHPDoc — otherwise a future agent will "normalize" it back.

**Does not apply to `Types/ValueObject/Exceptions/`** — see above.

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
- Use the `create()` helper when the message interpolates offending values only. Build inline when it also carries a pattern, a description or a list of accepted values, or when the factory takes a previous throwable — `InvalidCircleException::forInvalidFormat()`, `InvalidLtreeException::forInvalidNodeFormat()` and `InvalidWktSpatialDataException::forUnsupportedGeometryType()` are the standing examples. The clause is per factory, not per class: a class can owe the helper for one message and build another inline.
- Format the offending value with `\var_export($value, true)` (delivered via `create()`).
- Message shape: `"<What it must be>, %s given"` for type errors, `"Invalid <thing> format: %s"` for format errors.

```php
// ❌ Wrong — bypasses create()
return new self(\sprintf('Value must be a Ltree, %s given', \var_export($value, true)));

// ✓ Correct
return self::create('Value must be a Ltree, %s given', $value);
```

