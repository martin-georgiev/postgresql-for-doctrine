# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> PostgreSQL range types

PostgreSQL range types represent ranges of values of some element type (called the range's subtype). This library provides support for all [PostgreSQL built-in range types](https://www.postgresql.org/docs/18/rangetypes.html#RANGETYPES-BUILTIN).

> **See also:** [Date and range functions](DATE-AND-RANGE-FUNCTIONS.md) for range functions and operators in DQL queries

## Available range types

| Range Type | PostgreSQL Type | Value Type | Description |
|---|---|---|---|
| DateRange | DATERANGE | DateTimeInterface | Date ranges (without time) |
| Int4Range | INT4RANGE | int | 4-byte integer ranges |
| Int8Range | INT8RANGE | int | 8-byte integer ranges |
| NumRange | NUMRANGE | int/float | Numeric ranges |
| TsRange | TSRANGE | DateTimeInterface | Timestamp ranges without timezone |
| TstzRange | TSTZRANGE | DateTimeInterface | Timestamp ranges with timezone |

## Basic usage

### Registration

First, register the range types you need:

```php
use Doctrine\DBAL\Types\Type as DoctrineType;

DoctrineType::addType('daterange', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\DateRange");
DoctrineType::addType('int4range', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Int4Range");
DoctrineType::addType('int8range', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Int8Range");
DoctrineType::addType('numrange', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\NumRange");
DoctrineType::addType('tsrange', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\TsRange");
DoctrineType::addType('tstzrange', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\TstzRange");
```

### Entity usage

```php
use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;

#[ORM\Entity]
class Product
{
    #[ORM\Column(type: 'numrange')]
    private NumericRange $priceRange;
    
    #[ORM\Column(type: 'daterange')]
    private DateRange $availabilityPeriod;
    
    public function setPriceRange(float $min, float $max): void
    {
        $this->priceRange = new NumericRange($min, $max);
    }
    
    public function setAvailabilityPeriod(\DateTimeInterface $start, \DateTimeInterface $end): void
    {
        $this->availabilityPeriod = new DateRange($start, $end);
    }
}
```

## Range construction

### Inclusive vs exclusive bounds

Ranges support both inclusive `[` and exclusive `(` bounds:

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;

// [1.0, 10.0) - includes 1.0, excludes 10.0
$range = new NumericRange(1.0, 10.0, true, false);

// (0, 100] - excludes 0, includes 100
$range = new NumericRange(0, 100, false, true);

// [5, 15] - includes both bounds
$range = new NumericRange(5, 15, true, true);
```

### Infinite ranges

Ranges can be unbounded on either side:

```php
// [10, ∞) - from 10 to infinity
$range = new NumericRange(10, null, true, false);

// (-∞, 100] - from negative infinity to 100
$range = new NumericRange(null, 100, false, true);

// (-∞, ∞) - infinite range
$range = NumericRange::infinite();
```

### Empty ranges

```php
// Create an explicitly empty range
$range = NumericRange::empty();

// Check if a range is empty
if ($range->isEmpty()) {
    // Handle empty range
}
```

## Numeric ranges (NUMRANGE)

PostgreSQL keeps a `numeric` bound to any precision. PHP reads it back as an `int` when it is a whole number an `int` can hold, and as a `float` otherwise, so a bound with more than 17 significant digits comes back rounded: `[0.12345678901234567890,1)` reads as `[0.12345678901234568,1)`.

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;

// Price range from €10.50 to €99.99
$priceRange = new NumericRange(10.50, 99.99);

// Check if a price is in range
if ($priceRange->contains(25.00)) {
    echo "Price is in range";
}

// Create from PostgreSQL string
$range = NumericRange::fromString('[10.5,99.99)');
```

## Integer ranges

### Int4Range (4-byte integers)

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Range;

// Age range
$ageRange = new Int4Range(18, 65);

// Check if age is valid
if ($ageRange->contains(25)) {
    echo "Age is valid";
}
```

### Int8Range (8-byte integers)

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int8Range;

// Large number range
$range = new Int8Range(PHP_INT_MIN, PHP_INT_MAX);
```

## Date ranges (DATERANGE)

For date-only ranges without time components:

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange;

// Event period
$eventPeriod = new DateRange(
    new \DateTimeImmutable('2024-01-01'),
    new \DateTimeImmutable('2024-12-31')
);

// Convenience methods
$singleDay = DateRange::singleDay(new \DateTimeImmutable('2024-06-15'));
$year2024 = DateRange::year(2024);
$june2024 = DateRange::month(2024, 6);

// Check if a date falls within the range
$checkDate = new \DateTimeImmutable('2024-06-15');
if ($eventPeriod->contains($checkDate)) {
    echo "Date is within event period";
}
```

## Timestamp ranges

### TsRange (without timezone)

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsRange;

// Working hours
$workingHours = new TsRange(
    new \DateTimeImmutable('2024-01-01 09:00:00'),
    new \DateTimeImmutable('2024-01-01 17:00:00')
);
```

### TstzRange (with timezone)

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TstzRange;

// Meeting time across UTC timezone
$meetingTime = new TstzRange(
    new \DateTimeImmutable('2024-01-01 14:00:00+00:00'),
    new \DateTimeImmutable('2024-01-01 15:00:00+00:00')
);
```

## Range operations

### Contains check

```php
$range = new NumericRange(1, 10);

if ($range->contains(5)) {
    echo "5 is in the range [1, 10)";
}
```

### String representation

```php
$range = new NumericRange(1.5, 10.7);
echo $range; // Outputs: [1.5,10.7)

$range = new DateRange(
    new \DateTimeImmutable('2024-01-01'),
    new \DateTimeImmutable('2024-12-31')
);
echo $range; // Outputs: [2024-01-01,2024-12-31)
```

### Parsing from string values

```php
// Parse PostgreSQL range strings
$numRange = NumericRange::fromString('[1.5,10.7)');
$dateRange = DateRange::fromString('[2024-01-01,2024-12-31)');
$emptyRange = NumericRange::fromString('empty');
```

### Infinity support

[PostgreSQL distinguishes](https://www.postgresql.org/docs/18/rangetypes.html#RANGETYPES-INFINITE) between **unbounded** ranges and ranges **bounded by infinity**:

- **Unbounded**: `[0,)` - no upper bound
- **Bounded by infinity**: `[0,infinity)` - explicitly bounded by the infinity value

All range types that support infinity (NUMRANGE, TSRANGE, TSTZRANGE, DATERANGE) provide a unified API:

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange;

// Using infinity flags in constructor (7th parameter = upper infinity)
$numRange = new NumericRange(0, null, true, false, false, false, true);
$dateRange = new DateRange(new \DateTimeImmutable('2024-01-01'), null, true, false, false, false, true);

echo $numRange;   // [0,Infinity)
echo $dateRange;  // [2024-01-01,infinity)

// Parsing from PostgreSQL format
$range = NumericRange::fromString('[0,infinity)');
$range->isUpperBoundedInfinity(); // true
$range->isLowerBoundedInfinity(); // false
```

A bound of infinity usually takes the sign of its end, but PostgreSQL also takes `[infinity,)` and `(,-infinity]`, which hold no finite value. The sign is kept, and the 8th and 9th constructor parameters set it:

```php
$range = DateRange::fromString('[infinity,)');
echo $range; // [infinity,)
$range->contains(new \DateTimeImmutable('2024-01-01')); // false

echo new DateRange(null, null, true, false, false, true, false, false); // [infinity,)
echo new NumericRange(INF, null);                                         // [Infinity,)
```

**Spelling follows the element type**, matching PostgreSQL:

| Range type | Accepted bound spellings | Emitted spelling |
|------------|--------------------------|------------------|
| `NUMRANGE` | `infinity`, `-infinity`, `inf`, `-inf`, in any case; a positive bound may also be written `+infinity` or `+inf` | `Infinity` / `-Infinity` |
| `DATERANGE`, `TSRANGE`, `TSTZRANGE` | `infinity` and `-infinity`, in any case; a positive bound may also be written `+infinity`. The `inf` abbreviation is rejected | `infinity` / `-infinity` |

```php
// The numeric family reads the abbreviation, which a date or timestamp range rejects
echo NumericRange::fromString('[1,inf)'); // [1,Infinity)
DateRange::fromString('[2024-01-01,inf)'); // throws - PostgreSQL rejects it too
```

**NumericRange convenience**: Accepts PHP's `INF` constant as shorthand:

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;

$range = new NumericRange(0, INF);
echo $range; // [0,Infinity)

// Equivalent to using flags explicitly
$same = new NumericRange(0, null, true, false, false, false, true);
```

Integer ranges (INT4RANGE, INT8RANGE) do not support infinity values in PostgreSQL.

### NaN bounds

`NUMRANGE` and `NUMMULTIRANGE` also take `NaN` as a bound. It is not an open end: PostgreSQL gives `numeric` a [total order](https://www.postgresql.org/docs/18/datatype-numeric.html#DATATYPE-NUMERIC-DECIMAL) in which `NaN` sits above every other value, `Infinity` included, and equals itself. `NaN` therefore travels as the bound value, using PHP's `NAN` constant rather than a flag:

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;

$range = new NumericRange(1, NAN);
echo $range;                    // [1,NaN)
\is_nan($range->getUpper());    // true

NumericRange::fromString('[1,nan)'); // any case; a sign is rejected, as PostgreSQL rejects '-nan'::numeric
```

The ordering decides emptiness and containment, exactly as PostgreSQL does:

```php
NumericRange::fromString('[1,NaN]')->contains(NAN);     // true
NumericRange::fromString('[1,NaN)')->contains(NAN);     // false - the bound is exclusive
NumericRange::fromString('[1,NaN)')->contains(1e300);   // true - every finite value sorts below NaN
NumericRange::fromString('[1,Infinity)')->contains(NAN);// false - NaN sorts above Infinity
NumericRange::fromString('[1,)')->contains(NAN);        // true - an open end has nothing to sort against
echo NumericRange::fromString('[NaN,NaN)');             // empty - equal bounds, exclusive brackets
```

Only the numeric range types take a `NaN` bound. `NaN` belongs to PostgreSQL's numeric and floating-point types, so a date, timestamp or integer range rejects it - `DateRange::fromString('[2024-01-01,NaN)')` throws, exactly as `SELECT '[2024-01-01,NaN)'::daterange` errors.

> **See also:** [Infinity values](INFINITY.md) for why a bound of infinity is a flag here, a native `INF` for floats and an enum for datetime array items.

## DQL usage with range functions

Register range functions for DQL queries:

```php
$configuration->addCustomStringFunction('DATERANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Daterange::class);
$configuration->addCustomStringFunction('INT4RANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int4range::class);
$configuration->addCustomStringFunction('INT8RANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int8range::class);
$configuration->addCustomStringFunction('NUMRANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Numrange::class);
$configuration->addCustomStringFunction('TSRANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsrange::class);
$configuration->addCustomStringFunction('TSTZRANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tstzrange::class);
```

Use in DQL:

```php
// Find products with overlapping price ranges
$dql = "
    SELECT p 
    FROM Product p 
    WHERE OVERLAPS(p.priceRange, NUMRANGE('20', '50')) = TRUE
";

// Find events in a date range
$dql = "
    SELECT e 
    FROM Event e 
    WHERE CONTAINS(e.period, DATERANGE('2024-06-01', '2024-06-30')) = TRUE
";
```

## Common use cases

### Price ranges

```php
#[ORM\Entity]
class Product
{
    #[ORM\Column(type: 'numrange')]
    private ?NumericRange $priceRange = null;
    
    public function setPriceRange(float $min, float $max): void
    {
        $this->priceRange = new NumericRange($min, $max, true, false);
    }
    
    public function isInPriceRange(float $price): bool
    {
        return $this->priceRange?->contains($price) ?? false;
    }
}
```

### Availability periods

```php
#[ORM\Entity]
class Room
{
    #[ORM\Column(type: 'tstzrange')]
    private ?TstzRange $availabilityWindow = null;
    
    public function setAvailability(\DateTimeInterface $start, \DateTimeInterface $end): void
    {
        $this->availabilityWindow = new TstzRange($start, $end);
    }
    
    public function isAvailableAt(\DateTimeInterface $time): bool
    {
        return $this->availabilityWindow?->contains($time) ?? false;
    }
}
```

### Age restrictions

```php
#[ORM\Entity]
class Event
{
    #[ORM\Column(type: 'int4range')]
    private ?Int4Range $ageRestriction = null;
    
    public function setAgeRestriction(int $minAge, int $maxAge): void
    {
        $this->ageRestriction = new Int4Range($minAge, $maxAge, true, true);
    }
    
    public function isAgeAllowed(int $age): bool
    {
        return $this->ageRestriction?->contains($age) ?? true;
    }
}
```
