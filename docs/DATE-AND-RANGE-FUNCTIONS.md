# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> Date, time, and range functions

This page covers PostgreSQL [date, time](https://www.postgresql.org/docs/18/functions-datetime.html), and [range functions](https://www.postgresql.org/docs/18/functions-range.html) available in this library.

> **See also:** [Range types](RANGE-TYPES.md) for range value objects and [Examples](USE-CASES-AND-EXAMPLES.md) for these functions in whole queries

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

The results come from these rows of `Entity`, with times in UTC:

| id | start_date | end_date | start_tz | end_tz | created_at | active_period |
|---|---|---|---|---|---|---|
| 1 | 2026-01-01 | 2026-01-10 | 2026-10-24 21:00 | 2026-10-26 22:00 | 2026-01-14 10:30 | `[2023-03-01,2023-06-01)` |
| 2 | 2026-01-20 | 2026-01-31 | 2026-08-01 08:00 | 2026-08-01 09:00 | 2026-01-28 16:00 | `[2024-01-01,2024-02-01)` |
| 3 | 2026-02-15 | 2026-02-20 | 2026-09-20 08:00 | 2026-09-20 08:30 | 2026-02-03 12:00 | `[2023-12-15,2024-01-15)` |

Each `-- →` line under a query is one row of its result. A query without its own `ORDER BY` has its rows listed in sample-row order, or by group.

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
-- → ['end_date' => DateTimeImmutable('2026-01-10'), 'start_date' => DateTimeImmutable('2026-01-20'), 'gap_range' => '[2026-01-10,2026-01-20)']
--   ['end_date' => DateTimeImmutable('2026-01-31'), 'start_date' => DateTimeImmutable('2026-02-15'), 'gap_range' => '[2026-01-31,2026-02-15)']

-- GENERATE_TIME_SERIES: the optional 4th argument is the time zone the steps are counted in,
-- so a daily series stays on local midnight across a daylight-saving change
SELECT GENERATE_TIME_SERIES(e.start_tz, e.end_tz, '1 day', 'Europe/Sofia') as day FROM Entity e WHERE e.id = 1
-- → ['day' => '2026-10-24 21:00:00+00']
--   ['day' => '2026-10-25 22:00:00+00']
--   ['day' => '2026-10-26 22:00:00+00']

-- DATE_BIN: snap a timestamp to the nearest interval boundary relative to an origin
SELECT DATE_BIN('7 days', e.created_at, '2023-01-02') as week_start FROM Entity e
-- → ['week_start' => '2026-01-12 00:00:00+00']
--   ['week_start' => '2026-01-26 00:00:00+00']
--   ['week_start' => '2026-02-02 00:00:00+00']

-- Range bounds: third argument controls inclusivity - default is '[)' (inclusive lower, exclusive upper)
SELECT DATERANGE(e.start_date, e.end_date, '[]') as inclusive_range FROM Entity e
-- → ['inclusive_range' => '[2026-01-01,2026-01-11)']
--   ['inclusive_range' => '[2026-01-20,2026-02-01)']
--   ['inclusive_range' => '[2026-02-15,2026-02-21)']

-- Range operators must be compared with = TRUE / = FALSE in Doctrine DQL
SELECT e FROM Entity e WHERE OVERLAPS(e.active_period, DATERANGE('2023-01-01', '2023-12-31')) = TRUE
-- → Entity {id: 1}
--   Entity {id: 3}
SELECT e FROM Entity e WHERE CONTAINS(TSTZRANGE(DATE_SUBTRACT(CURRENT_TIMESTAMP(), '30 days'), CURRENT_TIMESTAMP()), e.start_tz) = TRUE
-- → the rows whose start_tz falls in the 30 days before the query runs

-- Group by calendar month using DATE_TRUNC + DATE_ADD
SELECT TSTZRANGE(DATE_TRUNC('month', e.created_at),
                 DATE_ADD(DATE_TRUNC('month', e.created_at), '1 month')) as month_range,
       COUNT(e.id) as entity_count
FROM Entity e
GROUP BY month_range
ORDER BY month_range
-- → ['month_range' => '["2026-01-01 00:00:00+00","2026-02-01 00:00:00+00")', 'entity_count' => 2]
--   ['month_range' => '["2026-02-01 00:00:00+00","2026-03-01 00:00:00+00")', 'entity_count' => 1]
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
- Compare the range operators with `= TRUE` or `= FALSE` in DQL (see [Writing DQL](DQL-DIALECT.md#boolean-functions-need-a-comparison)).
- A [GiST index](https://www.postgresql.org/docs/18/rangetypes.html#RANGETYPES-INDEXING) on a range column speeds up the overlap and containment operators.
- `DATE_PART` and `DATE_EXTRACT` take [any field PostgreSQL knows](https://www.postgresql.org/docs/18/functions-datetime.html#FUNCTIONS-DATETIME-EXTRACT), such as `year`, `month`, `dow` (day of week) and `doy` (day of year).
