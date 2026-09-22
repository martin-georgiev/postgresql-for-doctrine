# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> Upgrade Instructions

## How to Upgrade to Version 4.9

This release corrects the exceptions in two places. The value object ones are internal construction details, never part of the documented surface; the `Invalid{Type}For{PHP|Database}Exception` family is. Both ship as a minor — this library treats exception changes as backwards compatible.

| Was | Now |
|---|---|
| range value objects threw `Types\Exceptions\InvalidRangeForPHPException`, a `ConversionException`, with `::forInvalidNumericBound()`, `::forInvalidIntegerBound()`, `::forInvalidDateTimeBound()` and `::forUnsupportedBoundedInfinity()` | `Types\ValueObject\Exceptions\InvalidRangeException`, an `\InvalidArgumentException` — catch either; the factories are removed there and keep their names here |
| `InvalidCubeException extends ConversionException` | `extends \InvalidArgumentException` |
| `InvalidPointException::forInvalidPointFormat()`, `InvalidWktSpatialDataException::forInvalidWktFormat()` | both `::forInvalidFormat()` |
| range and multirange value objects threw a bare `\InvalidArgumentException` | `InvalidRangeException`, `InvalidMultirangeException` |
| their messages named the bound (`Lower bound must be ...`) and typed it with `gettype()` | one wording per reason, offending value shown verbatim |
| `Sparsevec::fromString()` threw `Types\Exceptions\InvalidSparsevecForPHPException` | `InvalidSparsevecException`; the `sparsevec` type still surfaces the former |
| `box`, `circle`, `line`, `lseg`, `path`, `point`, `polygon`, `tsquery` and `tsvector` threw `Invalid{Type}ForPHPException` on write and `Invalid{Type}ForDatabaseException` on read | the two families swap, matching every other type |

Only those nine change what the DBAL types throw — same messages, different class; every other type translates value object failures as before.

## How to Upgrade to Version 3.0

### 1. Review type handling in your code
If your application relies on automatic type conversion between PostgreSQL and PHP (e.g., expecting string numbers to be converted to actual numbers or vice versa), you'll need to update your code to explicitly handle type conversion where needed.

```php
// Before: Might convert '1.0' to integer 1
$tags = $entity- >getTags(); // ['1.0', '2.5']
$numericValue = $tags[0] + 2; // Would work even if string

// After: Preserves '1.0' as string
$tags = $entity->getTags(); // ['1.0', '2.5']
$numericValue = (float)$tags[0] + 2; // Explicit conversion needed
```

### 2. Update your code to handle exceptions
If you're catching specific exception types when working with `JsonbArray`, update your exception handling to catch the new `InvalidJsonItemForPHPException` and `InvalidJsonArrayItemForPHPException`.

```php
// Before
try {
    $jsonArray = $jsonbArrayType->convertToPHPValue($postgresValue, $platform);
} catch (\MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\TypeException $e) {
    // Handle exception
}

// After
try {
    $jsonArray = $jsonbArrayType->convertToPHPValue($postgresValue, $platform);
} catch (\MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidJsonArrayItemForPHPException $e) {
    // Handle exception
}
```

### 3. Test thoroughly
Since these changes affect data type handling at a fundamental level, thoroughly test all database interactions, especially those involving array types, to ensure your application handles the preserved types correctly.

