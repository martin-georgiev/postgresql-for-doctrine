# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> Date, time, and range functions

This page covers PostgreSQL [date, time](https://www.postgresql.org/docs/18/functions-datetime.html), and [range functions](https://www.postgresql.org/docs/18/functions-range.html) available in this library.

> **See also:** [Range types](RANGE-TYPES.md) for range value objects and [Common use cases and examples](USE-CASES-AND-EXAMPLES.md) for these functions in whole queries

## Date and time functions

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| age | AGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Age` |
| clock_timestamp | CLOCK_TIMESTAMP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ClockTimestamp` |
| date_add | DATE_ADD | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateAdd` |
| date_bin | DATE_BIN | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateBin` |
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

## Date and time operators

| PostgreSQL operator | Register for DQL as | Description | Implemented by |
|---|---|---|---|
| at time zone | AT_TIME_ZONE | Converts time data between different time zones (behavior depends on whether the input has a time zone offset) | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AtTimeZone` |

## Range functions

PostgreSQL provides several range types for representing ranges of values. These functions create and work with range types.

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| daterange | DATERANGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Daterange` |
| int4range | INT4RANGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int4range` |
| int8range | INT8RANGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int8range` |
| numrange | NUMRANGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Numrange` |
| tsrange | TSRANGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsrange` |
| tstzrange | TSTZRANGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tstzrange` |

## Range aggregate functions

These aggregate functions operate on range values.

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| range_agg | RANGE_AGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RangeAgg` |
| range_intersect_agg | RANGE_INTERSECT_AGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RangeIntersectAgg` |

## Range operators

Range types work with the general operators for containment and overlap testing:

| PostgreSQL operator | Register for DQL as | Description | Implemented by |
|---|---|---|---|
| @> | CONTAINS | Tests if range contains element or other range | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Contains` |
| <@ | IS_CONTAINED_BY | Tests if element or range is contained by range | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IsContainedBy` |
| && | OVERLAPS | Tests if ranges overlap | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Overlaps` |

## Usage examples

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
SELECT DATE_BIN('7 days', e.created_at, '2023-01-02') as week_start FROM Entity e

-- Range bounds: third argument controls inclusivity - default is '[)' (inclusive lower, exclusive upper)
SELECT DATERANGE(e.start_date, e.end_date, '[]') as inclusive_range FROM Entity e

-- Range operators must be compared with = TRUE / = FALSE in Doctrine DQL
SELECT e FROM Entity e WHERE OVERLAPS(e.active_period, DATERANGE('2023-01-01', '2023-12-31')) = TRUE
SELECT e FROM Entity e WHERE CONTAINS(TSTZRANGE(DATE_SUBTRACT(CURRENT_TIMESTAMP(), '30 days'), CURRENT_TIMESTAMP()), e.start_tz) = TRUE

-- Group by calendar month using DATE_TRUNC + DATE_ADD
SELECT TSTZRANGE(DATE_TRUNC('month', e.created_at),
                 DATE_ADD(DATE_TRUNC('month', e.created_at), '1 month')) as month_range,
       COUNT(e.id) as entity_count
FROM Entity e
GROUP BY month_range
ORDER BY month_range
```

**Range Type Notes:**

### Range bounds
PostgreSQL ranges support [different bound types](https://www.postgresql.org/docs/18/rangetypes.html#RANGETYPES-INCLUSIVITY):
- `'[)'` - Lower bound inclusive, upper bound exclusive (default)
- `'()'` - Both bounds exclusive
- `'[]'` - Both bounds inclusive
- `'(]'` - Lower bound exclusive, upper bound inclusive

### Range types available
- **daterange**: Date ranges (without time)
- **tsrange**: Timestamp ranges (without timezone)
- **tstzrange**: Timestamp ranges (with timezone)
- **int4range**: 32-bit integer ranges
- **int8range**: 64-bit integer ranges
- **numrange**: Numeric ranges (decimal/float)

### Empty and infinite ranges
- Empty ranges: a range containing no values, which PostgreSQL prints as `empty` (such as `DATERANGE('2023-01-01', '2023-01-01')`). In PHP, use `DateRange::empty()` and `isEmpty()` - see [Empty ranges](RANGE-TYPES.md#empty-ranges). A range with two `NULL` bounds is not empty: it is `(,)`, [unbounded on both sides](https://www.postgresql.org/docs/18/rangetypes.html#RANGETYPES-INFINITE)
- Infinite ranges: Use a parameter set to `null` for unbounded sides (DQL does not accept a bare `NULL` argument)
- Example: `DATERANGE('2023-01-01', :noEnd)` with `:noEnd` set to `null` represents "from 2023-01-01 onwards"

**Tips:**
- Compare the range operators with `= TRUE` or `= FALSE` in DQL (see [The DQL dialect](DQL-DIALECT.md#boolean-functions-need-a-comparison)).
- A [GiST index](https://www.postgresql.org/docs/18/rangetypes.html#RANGETYPES-INDEXING) on a range column speeds up the overlap and containment operators.
- `DATE_PART` and `DATE_EXTRACT` take [any field PostgreSQL knows](https://www.postgresql.org/docs/18/functions-datetime.html#FUNCTIONS-DATETIME-EXTRACT), such as `year`, `month`, `dow` (day of week) and `doy` (day of year).
