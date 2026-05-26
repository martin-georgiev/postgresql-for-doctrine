---
description: "Value Object conventions for src/.../Types/ValueObject/: immutability, fromString factory, __toString, class-level PHPDoc, VO-specific exceptions"
alwaysApply: true
trigger: always_on
applyTo: "**"
type: always_apply
---

# DBAL Value Object Conventions

Value Objects under `src/MartinGeorgiev/Doctrine/DBAL/Types/ValueObject/` — the PHP representation of composite PostgreSQL types (geometric, ranges, intervals, ltree, …). DBAL type classes consume them via `transformArrayItemForPHP()` / `convertToPHPValue()`.

See `exceptions.md` for VO-specific exceptions (`Types/ValueObject/Exceptions/` namespace).

## Immutability

**Required**: VOs are immutable. Use one of:

- `final readonly class Foo` — preferred for value-only VOs with no inheritance (`Box`, `Circle`, `Point`).
- `final class Foo` with `private readonly` properties — for VOs that extend an abstract base (`DateRange extends Range`, `Box extends BaseGeometricValue`).
- `class Foo implements \Stringable` with `private readonly` properties — only when the VO is **designed to be extended** via `static` returns; mark with `@phpstan-consistent-constructor` (see `Ltree`).

```php
// ✓ Correct — final readonly, promoted constructor properties
final readonly class Box extends BaseGeometricValue
{
    public function __construct(
        private Point $upperRight,
        private Point $lowerLeft,
    ) {}
}

// ✓ Correct — extendable base with @phpstan-consistent-constructor
/**
 * @phpstan-consistent-constructor
 */
class Ltree implements \Stringable, \JsonSerializable
{
    public function __construct(
        private readonly array $pathFromRoot,
    ) {
        self::assertListOfValidLtreeNodes($pathFromRoot);
    }
}

// ❌ Wrong — mutable VO with public setters
final class Box
{
    public Point $upperRight;
    public function setUpperRight(Point $p): void { $this->upperRight = $p; }
}
```

## Class-Level PHPDoc

**Required**: description, `@see` to PostgreSQL docs, `@since`, `@author`. Same shape as `dbal-types.md` § Class-Level PHPDoc.

```php
// ✓ Correct
/**
 * Represents a PostgreSQL box geometric type.
 *
 * Format: (x1,y1),(x2,y2) — upper-right and lower-left corners.
 *
 * @see https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-GEOMETRIC-BOXES
 * @since 4.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final readonly class Box extends BaseGeometricValue
```

When the class is generic (extends a `@template` base), add `@extends` on the class block:

```php
/**
 * Represents a PostgreSQL date range.
 *
 * @extends Range<\DateTimeInterface>
 *
 * @since 3.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class DateRange extends Range
```

## Required Methods

| Method | Required | Purpose |
|--------|----------|---------|
| `__construct` | always | Takes the VO's components. Validate them here; throw a VO-specific exception on bad input. |
| `__toString(): string` | always | Returns the **PostgreSQL string representation** (the form PostgreSQL will accept on write and produce on read). This is what `convertToDatabaseValue` on the DBAL type writes out. |
| `fromString(string $value): self` (or `: static`) | always | Parses a PostgreSQL string representation into a VO. Throws a VO-specific exception on malformed input. |
| Getters for each component | as needed | `getX()`, `getUpperRight()`, `getPathFromRoot()`, etc. |
| Semantic factories (`year()`, `month()`, `singleDay()`, …) | optional | Add when there's a recurring construction pattern with a clear domain name. |
| `jsonSerialize(): array` | when API/JSON exposure expected | Implement `\JsonSerializable`. See `Ltree::jsonSerialize()`. |

```php
// ✓ Correct — Box VO shape
final readonly class Box extends BaseGeometricValue
{
    public function __toString(): string
    {
        return \sprintf('(%s,%s),(%s,%s)', ...);
    }

    public function getUpperRight(): Point { return $this->upperRight; }
    public function getLowerLeft(): Point  { return $this->lowerLeft; }

    public static function fromString(string $value): self
    {
        if (!\preg_match(self::BOX_REGEX, $value)) {
            throw InvalidBoxException::forInvalidFormat($value, self::BOX_REGEX);
        }
        $points = self::extractPoints($value);
        return new self($points[0], $points[1]);
    }
}
```

## Validation and Exceptions

**Required**: VO-specific exceptions live in `src/MartinGeorgiev/Doctrine/DBAL/Types/ValueObject/Exceptions/`. Naming: `Invalid{VOName}Exception`. Pattern: extend `\Doctrine\DBAL\Types\ConversionException` and follow the factory shape in `exceptions.md`.

```php
// ✓ Correct — throw VO-specific exception with for*-named factory
throw InvalidBoxException::forInvalidFormat($value, self::BOX_REGEX);
throw InvalidLtreeException::forInvalidNodeFormat($value, 'non-empty string');

// ❌ Wrong — generic exception, no class-level PHPDoc, no for* factory
throw new \InvalidArgumentException('Bad box');
```

Validation runs in the constructor and in `fromString()`. A successfully-constructed VO is guaranteed valid — downstream code does not re-validate.

## Roundtrip Invariant

**Required**: `Foo::fromString((string) $foo)` must reconstruct an equivalent VO for every valid `$foo`. This is exercised by VO unit tests and by integration round-trip tests on the DBAL type.

```php
// ✓ Round-trip invariant — what every VO must satisfy
$original = new Box(new Point(1, 2), new Point(3, 4));
$asString = (string) $original;            // "(1,2),(3,4)"
$reconstructed = Box::fromString($asString);
// $reconstructed equals $original
```

If a VO has multiple valid string forms (e.g. PostgreSQL accepts `POINTZ(1 2 3)` and normalizes to `POINT Z(1 2 3)`), `__toString()` returns the **normalized** form so that round-trips are stable.
