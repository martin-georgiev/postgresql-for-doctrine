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

`composer run-static-analysis` holds a VO final — or `@phpstan-consistent-constructor` when it is deliberately open for extension — with private readonly properties, and keeps an abstract base's state readonly too. Setters need no separate ban: writing to a readonly property outside the constructor is already an error.

What it cannot pick for you is which of the three shapes fits:

- `final readonly class Foo` — preferred for value-only VOs with no inheritance (`Box`, `Circle`, `Point`).
- `final class Foo` with `private readonly` properties — for VOs extending an abstract base that is not itself readonly (`DateRange extends Range`).
- `class Foo implements \Stringable` with `private readonly` properties — only when the VO is **designed to be extended** via `static` returns; mark with `@phpstan-consistent-constructor` (see `Ltree`).

## Class-Level PHPDoc

The description names the PostgreSQL type the VO represents and, where the string form is not obvious, shows it — `Format: (x1,y1),(x2,y2) — upper-right and lower-left corners.` for `Box`. Point `@see` at the anchor for that specific type, not the page it sits on: `datatype-geometric.html#DATATYPE-GEOMETRIC-BOXES`, not `datatype-geometric.html`.

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

`__toString()` and a public static `fromString()` are kept present on every concrete VO by `composer run-static-analysis` — inheriting them from an abstract base counts, as the range VOs do. What they must **mean** is yours to get right:

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

**Required**: VO-specific exceptions live in `src/MartinGeorgiev/Doctrine/DBAL/Types/ValueObject/Exceptions/` as a `final class` with `for*`-named static factories. Both the name and the parent are settled by tooling — `Invalid{VOName}Exception` by `composer run-static-analysis`, the parent by `ParentByNamespaceRector`; the reason the parent matters is in `ci/rector/config.php`.

Message building follows `exceptions.md` § Message Formatting.

Keep the class PHPDoc description to one line. Do not explain the parent or that these are not DBAL conversion exceptions — the namespace says it, and `exceptions.md` exempts this family from the parent-deviation PHPDoc requirement.

```php
// ✓ Correct — throw VO-specific exception with for*-named factory
throw InvalidBoxException::forInvalidFormat($value, self::BOX_REGEX);
throw InvalidLtreeException::forInvalidNodeFormat($value, 'non-empty string');

// ❌ Wrong — generic exception, no for* factory
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
