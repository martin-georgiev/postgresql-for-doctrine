# Date, Time, and Range Functions

This document covers PostgreSQL date, time, and range functions available in this library.

> 📖 **See also**: [Range Types](RANGE-TYPES.md) for range value objects and [Common Use Cases and Examples](USE-CASES-AND-EXAMPLES.md) for practical date and range examples

## Date and Time Functions

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| age | AGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Age` |
| clock_timestamp | CLOCK_TIMESTAMP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ClockTimestamp` |
| date_add | DATE_ADD | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateAdd` |
| date_bin | DATE_BIN | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateBin` |
| date_diff | DATE_DIFF | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateDiff` |
| date_part | DATE_PART | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DatePart` |
| date_subtract | DATE_SUBTRACT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateSubtract` |
| date_trunc | DATE_TRUNC | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateTrunc` |
| extract | DATE_EXTRACT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateExtract` |
| generate_series | GENERATE_TIME_SERIES | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\GenerateTimeSeries` |
| isfinite | ISFINITE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Isfinite` |
| justify_days | JUSTIFY_DAYS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JustifyDays` |
| justify_hours | JUSTIFY_HOURS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JustifyHours` |
| justify_interval | JUSTIFY_INTERVAL | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JustifyInterval` |
| make_date | MAKE_DATE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeDate` |
| make_time | MAKE_TIME | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeTime` |
| make_timestamp | MAKE_TIMESTAMP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeTimestamp` |
| make_timestamptz | MAKE_TIMESTAMPTZ | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeTimestamptz` |
| overlaps | DATE_OVERLAPS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateOverlaps` |
| statement_timestamp | STATEMENT_TIMESTAMP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StatementTimestamp` |
| to_date | TO_DATE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToDate` |
| to_timestamp | TO_TIMESTAMP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTimestamp` |
| transaction_timestamp | TRANSACTION_TIMESTAMP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TransactionTimestamp` |

## Date and Time Operators

| PostgreSQL operator | Register for DQL as | Description | Implemented by |
|---|---|---|---|
| at time zone | AT_TIME_ZONE | Converts time data between different time zones (behavior depends on whether the input has a time zone offset)
 | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AtTimeZone` |

## Range Functions

PostgreSQL provides several range types for representing ranges of values. These functions create and work with range types.

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| daterange | DATERANGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Daterange` |
| int4range | INT4RANGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int4range` |
| int8range | INT8RANGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int8range` |
| numrange | NUMRANGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Numrange` |
| tsrange | TSRANGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsrange` |
| tstzrange | TSTZRANGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tstzrange` |

## Range Aggregate Functions

These aggregate functions operate on range values.

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| range_agg | RANGE_AGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RangeAgg` |
| range_intersect_agg | RANGE_INTERSECT_AGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RangeIntersectAgg` |

## Range Operators

Range types work with the general operators for containment and overlap testing:

| PostgreSQL operator | Register for DQL as | Description | Implemented by |
|---|---|---|---|
| @> | CONTAINS | Tests if range contains element or other range | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Contains` |
| <@ | IS_CONTAINED_BY | Tests if element or range is contained by range | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IsContainedBy` |
| && | OVERLAPS | Tests if ranges overlap | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Overlaps` |

## Usage Examples

```sql
-- Find gaps in date ranges
SELECT e1.end_date, e2.start_date,
       DATERANGE(e1.end_date, e2.start_date) as gap_range
FROM Entity e1, Entity e2
WHERE e1.end_date < e2.start_date
  AND NOT EXISTS (
 SELECT 1 FROM Entity e3
 WHERE OVERLAPS(DATERANGE(e1.end_date, e2.start_date),
                DATERANGE(e3.start_date, e3.end_date)) = TRUE
)

-- GENERATE_TIME_SERIES: optional 4th argument outputs all timestamps in a target timezone
SELECT GENERATE_TIME_SERIES(e.start_tz, e.end_tz, '1 hour', 'Europe/Sofia') as hour FROM Entity e WHERE e.id = 1

-- DATE_BIN: snap a timestamp to the nearest interval boundary relative to an origin
SELECT DATE_BIN('1 month', e.created_at, '2023-01-01') as month_start FROM Entity e

-- Range bounds: third argument controls inclusivity — default is '[)' (inclusive lower, exclusive upper)
SELECT DATERANGE(e.start_date, e.end_date, '[]') as inclusive_range FROM Entity e

-- Range operators must be compared with = TRUE / = FALSE in Doctrine DQL
SELECT e FROM Entity e WHERE OVERLAPS(e.active_period, DATERANGE('2023-01-01', '2023-12-31')) = TRUE
SELECT e FROM Entity e WHERE CONTAINS(DATERANGE(DATE_SUBTRACT(CURRENT_DATE, 30), CURRENT_DATE), e.created_at) = TRUE

-- Group by calendar month using DATE_BIN + DATE_ADD
SELECT DATERANGE(DATE_BIN('1 month', e.created_at, '2023-01-01'),
                 DATE_ADD(DATE_BIN('1 month', e.created_at, '2023-01-01'), 30)) as month_range,
       COUNT(*) as entity_count
FROM Entity e
GROUP BY month_range
ORDER BY month_range
```

**📝 Range Type Notes:**

### Range Bounds
PostgreSQL ranges support different bound types:
- `'[)'` - Lower bound inclusive, upper bound exclusive (default)
- `'()'` - Both bounds exclusive
- `'[]'` - Both bounds inclusive
- `'(]'` - Lower bound exclusive, upper bound inclusive

### Range Types Available
- **daterange**: Date ranges (without time)
- **tsrange**: Timestamp ranges (without timezone)
- **tstzrange**: Timestamp ranges (with timezone)
- **int4range**: 32-bit integer ranges
- **int8range**: 64-bit integer ranges
- **numrange**: Numeric ranges (decimal/float)

### Empty and Infinite Ranges
- Empty ranges: `DATERANGE(NULL, NULL)`
- Infinite ranges: Use `NULL` for unbounded sides
- Example: `DATERANGE('2023-01-01', NULL)` represents "from 2023-01-01 onwards"

**💡 Tips for Usage:**
1. **Range operators** should be used with `= TRUE` or `= FALSE` in DQL
2. **Date functions** work with PostgreSQL's rich date/time types
3. **Range types** provide efficient storage and querying for value ranges
4. **Overlaps testing** is optimized with proper indexes on range columns
5. **Date extraction** supports many field types: YEAR, MONTH, DAY, HOUR, MINUTE, SECOND, DOW (day of week), DOY (day of year)
6. **Range bounds** default to `[)` (inclusive lower, exclusive upper) if not specified
