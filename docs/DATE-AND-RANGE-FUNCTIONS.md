# Date, Time, and Range Functions

This document covers PostgreSQL date, time, and range functions available in this library.

> 📖 **See also**: [Range Types](RANGE-TYPES.md) for range value objects and [Common Use Cases and Examples](USE-CASES-AND-EXAMPLES.md) for practical date and range examples

## Date and Time Functions

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| date_add | DATE_ADD | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateAdd` |
| date_bin | DATE_BIN | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateBin` |
| date_subtract | DATE_SUBTRACT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateSubtract` |
| date_trunc | DATE_TRUNC | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateTrunc` |
| extract | DATE_EXTRACT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateExtract` |
| overlaps | DATE_OVERLAPS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateOverlaps` |
| to_date | TO_DATE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToDate` |
| to_timestamp | TO_TIMESTAMP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTimestamp` |

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

## Range Operators

Range types work with the general operators for containment and overlap testing:

| PostgreSQL operator | Register for DQL as | Description | Implemented by |
|---|---|---|---|
| @> | CONTAINS | Tests if range contains element or other range | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Contains` |
| <@ | IS_CONTAINED_BY | Tests if element or range is contained by range | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IsContainedBy` |
| && | OVERLAPS | Tests if ranges overlap | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Overlaps` |

## Usage Examples

```sql
-- Date and time operations
-- Add days to date
SELECT e, DATE_ADD(e.created_at, 30) as expiry_date FROM Entity e

-- Subtract days from date
SELECT e, DATE_SUBTRACT(e.created_at, 7) as week_ago FROM Entity e

-- Extract date components
SELECT e, DATE_EXTRACT(e.timestamp, 'YEAR') as year,
       DATE_EXTRACT(e.timestamp, 'MONTH') as month,
       DATE_EXTRACT(e.timestamp, 'DAY') as day FROM Entity e

-- Check date overlaps
SELECT e FROM Entity e WHERE DATE_OVERLAPS(e.period1, e.period2) = TRUE

-- Convert string to date
SELECT e, TO_DATE(e.date_string, 'YYYY-MM-DD') as parsed_date FROM Entity e

-- Convert string to timestamp
SELECT e, TO_TIMESTAMP(e.timestamp_string, 'YYYY-MM-DD HH24:MI:SS') as parsed_timestamp FROM Entity e

-- Bin dates into intervals
SELECT e, DATE_BIN('1 day', e.created_at, '2023-01-01') as day_bin FROM Entity e

-- Range operations
-- Create date ranges
SELECT e, DATERANGE(e.start_date, e.end_date) as date_range FROM Entity e

-- Create inclusive date ranges
SELECT e, DATERANGE(e.start_date, e.end_date, '[]') as inclusive_range FROM Entity e

-- Create timestamp ranges
SELECT e, TSRANGE(e.start_time, e.end_time) as time_range FROM Entity e

-- Create timestamp with timezone ranges
SELECT e, TSTZRANGE(e.start_time_tz, e.end_time_tz) as tz_range FROM Entity e

-- Create integer ranges
SELECT e, INT4RANGE(e.min_value, e.max_value) as int_range FROM Entity e

-- Create numeric ranges
SELECT e, NUMRANGE(e.min_price, e.max_price) as price_range FROM Entity e

-- Test if range contains value
SELECT e FROM Entity e WHERE CONTAINS(e.age_range, 25) = TRUE

-- Test if range contains another range
SELECT e FROM Entity e WHERE CONTAINS(e.outer_range, e.inner_range) = TRUE

-- Test if value is in range
SELECT e FROM Entity e WHERE IS_CONTAINED_BY(e.age, e.valid_age_range) = TRUE

-- Test if ranges overlap
SELECT e FROM Entity e WHERE OVERLAPS(e.period1, e.period2) = TRUE

-- Find entities with overlapping date ranges
SELECT e1, e2 FROM Entity e1, Entity e2 
WHERE e1.id != e2.id AND OVERLAPS(e1.active_period, e2.active_period) = TRUE

-- Find entities active during a specific period
SELECT e FROM Entity e 
WHERE OVERLAPS(e.active_period, DATERANGE('2023-01-01', '2023-12-31')) = TRUE

-- Find entities with prices in a specific range
SELECT e FROM Entity e 
WHERE OVERLAPS(e.price_range, NUMRANGE(100, 500)) = TRUE

-- Complex date queries
-- Find entities created in the last 30 days
SELECT e FROM Entity e 
WHERE CONTAINS(DATERANGE(DATE_SUBTRACT(CURRENT_DATE, 30), CURRENT_DATE), e.created_at) = TRUE

-- Find entities with overlapping business hours
SELECT e FROM Entity e 
WHERE OVERLAPS(e.business_hours, TSRANGE('09:00:00', '17:00:00')) = TRUE

-- Group by date ranges
SELECT DATERANGE(DATE_BIN('1 month', e.created_at, '2023-01-01'), 
                 DATE_ADD(DATE_BIN('1 month', e.created_at, '2023-01-01'), 30)) as month_range,
       COUNT(*) as entity_count
FROM Entity e 
GROUP BY month_range
ORDER BY month_range

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
