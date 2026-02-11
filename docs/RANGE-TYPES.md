# PostgreSQL Range Types

PostgreSQL range types represent ranges of values of some element type (called the range's subtype). This library provides support for all PostgreSQL built-in range types.

> 📖 **See also**: [Date and Range Functions](DATE-AND-RANGE-FUNCTIONS.md) for range functions and operators in DQL queries

## Available Range Types

| Range Type | PostgreSQL Type | Value Type | Description |
|---|---|---|---|
| DateRange | DATERANGE | DateTimeInterface | Date ranges (without time) |
| Int4Range | INT4RANGE | int | 4-byte integer ranges |
| Int8Range | INT8RANGE | int | 8-byte integer ranges |
| NumRange | NUMRANGE | int/float | Numeric ranges with arbitrary precision |
| TsRange | TSRANGE | DateTimeInterface | Timestamp ranges without timezone |
| TstzRange | TSTZRANGE | DateTimeInterface | Timestamp ranges with timezone |

## Basic Usage

### Registration

First, register the range types you need:

```php
use Doctrine\DBAL\Types\Type;

Type::addType('daterange', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\DateRange");
Type::addType('int4range', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Int4Range");
Type::addType('int8range', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Int8Range");
Type::addType('numrange', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\NumRange");
Type::addType('tsrange', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\TsRange");
Type::addType('tstzrange', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\TstzRange");
```

### Entity Usage

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

## Range Construction

### Inclusive vs Exclusive Bounds

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

### Infinite Ranges

Ranges can be unbounded on either side:

```php
// [10, ∞) - from 10 to infinity
$range = new NumericRange(10, null, true, false);

// (-∞, 100] - from negative infinity to 100
$range = new NumericRange(null, 100, false, true);

// (-∞, ∞) - infinite range
$range = NumericRange::infinite();
```

### Empty Ranges

```php
// Create an explicitly empty range
$range = NumericRange::empty();

// Check if a range is empty
if ($range->isEmpty()) {
    // Handle empty range
}
```

## Numeric Ranges (NUMRANGE)

For arbitrary precision numeric values:

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

## Integer Ranges

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

## Date Ranges (DATERANGE)

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

## Timestamp Ranges

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

## Range Operations

### Contains Check

```php
$range = new NumericRange(1, 10);

if ($range->contains(5)) {
    echo "5 is in the range [1, 10)";
}
```

### String Representation

```php
$range = new NumericRange(1.5, 10.7);
echo $range; // Outputs: [1.5,10.7)

$range = new DateRange(
    new \DateTimeImmutable('2024-01-01'),
    new \DateTimeImmutable('2024-12-31')
);
echo $range; // Outputs: [2024-01-01,2024-12-31)
```

### Parsing from String Values

```php
// Parse PostgreSQL range strings
$numRange = NumericRange::fromString('[1.5,10.7)');
$dateRange = DateRange::fromString('[2024-01-01,2024-12-31)');
$emptyRange = NumericRange::fromString('empty');
```

### Infinity Support

PostgreSQL distinguishes between **unbounded** ranges and ranges **bounded by infinity**:

- **Unbounded**: `[0,)` - no upper bound
- **Bounded by infinity**: `[0,infinity)` - explicitly bounded by the infinity value

All range types that support infinity (NUMRANGE, TSRANGE, TSTZRANGE, DATERANGE) provide a unified API:

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange;

// Using infinity flags in constructor (7th parameter = upper infinity)
$numRange = new NumericRange(0, null, true, false, false, false, true);
$dateRange = new DateRange(new \DateTimeImmutable('2024-01-01'), null, true, false, false, false, true);

echo $numRange;   // [0,infinity)
echo $dateRange;  // [2024-01-01,infinity)

// Parsing from PostgreSQL format
$range = NumericRange::fromString('[0,infinity)');
$range->isUpperBoundedInfinity(); // true
$range->isLowerBoundedInfinity(); // false
```

**NumericRange convenience**: Accepts PHP's `INF` constant as shorthand:

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;

$range = new NumericRange(0, INF);
echo $range; // [0,infinity)

// Equivalent to using flags explicitly
$same = new NumericRange(0, null, true, false, false, false, true);
```

**Note**: Integer ranges (INT4RANGE, INT8RANGE) do not support infinity values in PostgreSQL.

## DQL Usage with Range Functions

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
    WHERE OVERLAPS(p.priceRange, NUMRANGE(20, 50)) = TRUE
";

// Find events in a date range
$dql = "
    SELECT e 
    FROM Event e 
    WHERE CONTAINS(e.period, DATERANGE('2024-06-01', '2024-06-30')) = TRUE
";
```

## Common Use Cases

### Price Ranges

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

### Availability Periods

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

### Age Restrictions

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
