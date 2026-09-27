# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> Upgrade instructions

## How to upgrade to version 4.9

This release corrects the exceptions in two places. The value object ones are internal construction details, never part of the documented surface; the `Invalid{Type}For{PHP|Database}Exception` family is. Both ship as a minor - this library treats exception changes as backwards compatible.

| Was | Now |
|---|---|
| range value objects threw `Types\Exceptions\InvalidRangeForPHPException`, a `ConversionException`, with `::forInvalidNumericBound()`, `::forInvalidIntegerBound()`, `::forInvalidDateTimeBound()` and `::forUnsupportedBoundedInfinity()` | `Types\ValueObject\Exceptions\InvalidRangeException`, an `\InvalidArgumentException` - catch either; the factories are removed there and keep their names here |
| `InvalidCubeException extends ConversionException` | `extends \InvalidArgumentException` |
| `InvalidPointException::forInvalidPointFormat()`, `InvalidWktSpatialDataException::forInvalidWktFormat()` | both `::forInvalidFormat()` |
| range and multirange value objects threw a bare `\InvalidArgumentException` | `InvalidRangeException`, `InvalidMultirangeException` |
| their messages named the bound (`Lower bound must be ...`) and typed it with `gettype()` | one wording per reason, offending value shown verbatim |
| `Sparsevec::fromString()` threw `Types\Exceptions\InvalidSparsevecForPHPException` | `InvalidSparsevecException`; the `sparsevec` type still surfaces the former |
| `box`, `circle`, `line`, `lseg`, `path`, `point`, `polygon`, `tsquery` and `tsvector` threw `Invalid{Type}ForPHPException` on write and `Invalid{Type}ForDatabaseException` on read | the two families swap, matching every other type |

Only those nine change what the DBAL types throw - same messages, different class; every other type translates value object failures as before.

`ND_BOUNDING_BOX_DISTANCE` (`NDimensionalBoundingBoxDistance`) is removed. It rendered PostGIS's `<<#>>` operator, which no released PostGIS provides, so every query using it already failed with `operator does not exist: geometry <<#>> geometry`. Delete its registration line; for an n-D distance use `ND_CENTROID_DISTANCE` (`<<->>`).

## How to upgrade to version 3.0

### 1. Array items keep their type

Since 3.0 the array types keep each item's type in both directions: `1` stays an `int`, `1.5` a `float`, `'1'` a string and `true` a `bool`, and `'1.23e5'` keeps its notation. Before 3.0 a numeric string in a `text[]` could arrive as a number. Where your code relied on that, convert explicitly:

```php
$tags = $entity->getTags();    // a text[] holding {1.0,2.5} reads as ['1.0', '2.5']
$total = (float) $tags[0] + 2; // convert where you need a number
```

### 2. `JsonbArray` throws its own exceptions

A `JsonbArray` item that cannot be converted now throws `InvalidJsonItemForPHPException` or `InvalidJsonArrayItemForPHPException` instead of the generic `TypeException`. Update the `catch` blocks that caught the old one:

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

### 3. Test the array columns

The array columns are the ones whose values can change type, so run your tests against every query that reads or writes one.
