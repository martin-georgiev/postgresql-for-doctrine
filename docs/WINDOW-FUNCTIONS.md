# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> Window Functions

> 📖 **See also**: [PostgreSQL window functions](https://www.postgresql.org/docs/18/functions-window.html) and [window function calls](https://www.postgresql.org/docs/18/sql-expressions.html#SYNTAX-WINDOW-FUNCTIONS)

| PostgreSQL syntax | Register for DQL as | Implemented by |
|---|---|---|
| function OVER (window specification) | OVER | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Over` |

## `OVER` wraps the call in DQL

DQL cannot parse `OVER (...)` after the call's closing parenthesis, so `OVER` wraps the call instead, with the window specification as its second argument; [OVER wraps the call](DQL-DIALECT.md#over-wraps-the-call) explains the shape.

| SQL | DQL |
|---|---|
| `COUNT(e.id) OVER ()` | `OVER(COUNT(e.id))` |
| `SUM(e.amount) OVER (PARTITION BY e.customer)` | `OVER(SUM(e.amount), PARTITION BY e.customer)` |
| `SUM(e.amount) OVER (PARTITION BY e.customer ORDER BY e.createdAt ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW)` | `OVER(SUM(e.amount), PARTITION BY e.customer ORDER BY e.createdAt ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW)` |
| `array_agg(e.tag) OVER (PARTITION BY e.post)` | `OVER(ARRAY_AGG(e.tag), PARTITION BY e.post)` |
| `SUM(e.amount) FILTER (WHERE e.refunded = false) OVER (PARTITION BY e.customer)` | `OVER(FILTER(SUM(e.amount), WHERE e.refunded = FALSE), PARTITION BY e.customer)` |

- Without a window specification the window is the whole result, so `OVER(COUNT(e.id))` repeats the total row count on every row.

### What `OVER` accepts

DQL's own aggregates, any aggregate from this library and a `FILTER(...)` around one, and the window-only [ranking functions](#ranking-functions) and [value functions](#value-functions). [What FILTER and OVER accept](DQL-DIALECT.md#what-filter-and-over-accept) covers the rest, including windowing a function of your own.

## Window Specification

```
[PARTITION BY expression [, ...]] [ORDER BY expression [ASC | DESC] [, ...]] [frame clause]
```

- `PARTITION BY` takes one or more comma-separated expressions: fields, arithmetic, function calls, literals and parameters.
- `ORDER BY` takes the same items as a DQL `ORDER BY`, each with an optional `ASC` / `DESC`.
- Every clause is optional, and they come in the order `PARTITION BY`, `ORDER BY`, frame.

## Frame Clauses

A frame narrows the rows of the partition a function reads for the current row. It follows `ORDER BY` (or stands on its own) and is written exactly as in SQL:

```
{ROWS | RANGE | GROUPS} frame_start [exclusion]
{ROWS | RANGE | GROUPS} BETWEEN frame_start AND frame_end [exclusion]
```

| `frame_start` / `frame_end` | Meaning |
|---|---|
| `UNBOUNDED PRECEDING` | The first row of the partition |
| `offset PRECEDING` | `offset` rows, peer groups or values before the current row |
| `CURRENT ROW` | The current row, or its peer group in `RANGE` and `GROUPS` mode |
| `offset FOLLOWING` | `offset` rows, peer groups or values after the current row |
| `UNBOUNDED FOLLOWING` | The last row of the partition |

| Exclusion | Leaves out |
|---|---|
| `EXCLUDE CURRENT ROW` | The current row |
| `EXCLUDE GROUP` | The current row and its peers |
| `EXCLUDE TIES` | The peers of the current row, keeping the row itself |
| `EXCLUDE NO OTHERS` | Nothing (the default) |

- `offset` is an integer, a decimal, a string literal or a parameter. A string literal serves `RANGE` over dates and timestamps, e.g. `RANGE BETWEEN '7 days' PRECEDING AND CURRENT ROW`.
- The keywords are case-insensitive.
- Only the syntax is checked in DQL. PostgreSQL rejects the combinations it does not allow, such as `UNBOUNDED FOLLOWING` as the start, a frame end before its start, or an offset `RANGE` without exactly one `ORDER BY` column.

## Ranking Functions

These are window-only functions: they exist only inside `OVER`, and number or rank each row within its partition in the order of the window's `ORDER BY`. Rows with equal `ORDER BY` values are peers.

| PostgreSQL function | Register for DQL as | Arguments | Implemented by |
|---|---|---|---|
| row_number | ROW_NUMBER | none | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RowNumber` |
| rank | RANK | none | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Rank` |
| dense_rank | DENSE_RANK | none | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DenseRank` |
| percent_rank | PERCENT_RANK | none | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PercentRank` |
| cume_dist | CUME_DIST | none | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\CumeDist` |
| ntile | NTILE | number of buckets | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ntile` |

- `ROW_NUMBER` numbers the rows 1, 2, 3, ... with no ties; peers are numbered in an unspecified order.
- `RANK` gives peers the same rank and leaves a gap after them (1, 1, 3); `DENSE_RANK` leaves none (1, 1, 2).
- `PERCENT_RANK` is `(rank - 1) / (rows in the partition - 1)`, or `0.0` for a single-row partition, and `CUME_DIST` is the fraction of rows in the partition that precede the current row or are its peers. Both return `double precision`.
- `NTILE(n)` divides the partition into `n` buckets as equally as possible and returns the current row's bucket, from 1 to `n`. The bucket count is an integer, a field, an arithmetic expression or a parameter.
- Frame clauses do not affect ranking functions; PostgreSQL ignores them.

```sql
-- Number each customer's orders, newest first
SELECT o.id, OVER(ROW_NUMBER(), PARTITION BY o.customer ORDER BY o.createdAt DESC) AS orderNumber FROM App\Entity\Order o

-- A leaderboard: RANK skips after ties (1, 1, 3), DENSE_RANK does not (1, 1, 2)
SELECT p.name, p.score, OVER(RANK(), ORDER BY p.score DESC) AS scoreRank, OVER(DENSE_RANK(), ORDER BY p.score DESC) AS denseScoreRank FROM App\Entity\Player p

-- Split each region's sales into quartiles
SELECT s.id, OVER(NTILE(4), PARTITION BY s.region ORDER BY s.amount) AS quartile FROM App\Entity\Sale s

-- Where each score sits in the distribution, from 0 to 1
SELECT p.name, OVER(PERCENT_RANK(), ORDER BY p.score) AS percentile FROM App\Entity\Player p
```

## Value Functions

A value function returns a value read from another row of the window.

| PostgreSQL function | Register for DQL as | Arguments | Implemented by |
|---|---|---|---|
| lag(value [, offset [, default]]) | LAG | 1 to 3 | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Lag` |
| lead(value [, offset [, default]]) | LEAD | 1 to 3 | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Lead` |
| first_value(value) | FIRST_VALUE | 1 | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\FirstValue` |
| last_value(value) | LAST_VALUE | 1 | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\LastValue` |
| nth_value(value, n) | NTH_VALUE | 2 | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NthValue` |

| SQL | DQL |
|---|---|
| `lag(e.price) OVER (PARTITION BY e.product ORDER BY e.day)` | `OVER(LAG(e.price), PARTITION BY e.product ORDER BY e.day)` |
| `lag(e.price, 2, 0) OVER (ORDER BY e.day)` | `OVER(LAG(e.price, 2, 0), ORDER BY e.day)` |
| `lead(e.price, 1, NULL) OVER (ORDER BY e.day)` | `OVER(LEAD(e.price, 1, NULL), ORDER BY e.day)` |
| `first_value(e.price) OVER (ORDER BY e.day)` | `OVER(FIRST_VALUE(e.price), ORDER BY e.day)` |
| `last_value(e.price) OVER (ORDER BY e.day ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING)` | `OVER(LAST_VALUE(e.price), ORDER BY e.day ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING)` |
| `nth_value(e.price, 2) OVER (ORDER BY e.day)` | `OVER(NTH_VALUE(e.price, 2), ORDER BY e.day)` |

- `LAG` and `LEAD` read the row `offset` rows before or after the current one within the partition. `offset` defaults to 1, and `default` (NULL unless given) is returned when that row does not exist. `default` may be a literal `NULL`.
- `FIRST_VALUE`, `LAST_VALUE` and `NTH_VALUE` read the window frame, not the whole partition. With an `ORDER BY` the default frame is `RANGE BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW`, which ends at the current row's last peer (the last row with the same `ORDER BY` values). So `LAST_VALUE` returns that peer's value, which differs from the current row's when peers hold different values, and `NTH_VALUE` returns NULL until the frame reaches its n-th row. Add a tie-breaker to `ORDER BY` (e.g. the id) or use an explicit `ROWS` frame to make the result deterministic, and `ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING` to read the whole partition.
- The value argument is polymorphic, so PostgreSQL cannot resolve a bare string literal such as `LAG('none')`, nor a parameter, and fails with `could not determine polymorphic type because input has type unknown`. Pass a field, an expression over one, or a numeric literal. A string literal or a parameter as `default` is fine, as the value argument already decides the type.

## Usage Examples

```sql
-- Running total per customer, in order of creation
SELECT o.id, OVER(SUM(o.amount), PARTITION BY o.customer ORDER BY o.createdAt ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS runningTotal FROM App\Entity\Order o

-- Seven-day moving average over dates
SELECT d.day, OVER(AVG(d.visits), ORDER BY d.day RANGE BETWEEN '6 days' PRECEDING AND CURRENT ROW) AS weeklyAverage FROM App\Entity\DailyStat d

-- Each sale next to its region's total
SELECT s.id, s.amount, OVER(SUM(s.amount), PARTITION BY s.region) AS regionTotal FROM App\Entity\Sale s

-- Next to a selected entity: each result row is [0 => Order, 'runningTotal' => ...]
SELECT o, OVER(SUM(o.amount), ORDER BY o.createdAt) AS runningTotal FROM App\Entity\Order o ORDER BY o.createdAt

-- Day-over-day change per product, 0 on each product's first day
SELECT p.day, p.price - OVER(LAG(p.price, 1, p.price), PARTITION BY p.product ORDER BY p.day) AS change FROM App\Entity\DailyPrice p

-- Each sale next to the highest amount in its region
SELECT s.id, s.amount, OVER(LAST_VALUE(s.amount), PARTITION BY s.region ORDER BY s.amount ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING) AS regionHighest FROM App\Entity\Sale s
```

## Limitations

- **You cannot filter on a window result in DQL.** PostgreSQL computes window functions after `WHERE`, `GROUP BY` and `HAVING`, so `WHERE runningTotal > 100` is invalid SQL, not just invalid DQL. The standard fix - wrapping the query in a subquery in `FROM` and filtering outside it - is something DQL cannot express. Use a native query with a `ResultSetMapping`, or filter the rows in PHP.
- **A window-only function outside `OVER` parses, but PostgreSQL rejects it when the query runs** (`window function row_number requires an OVER clause`).
- **No named windows.** There is no `WINDOW w AS (...)` clause; every call spells out its own specification, even when several calls share it.
- **No `NULLS FIRST` / `NULLS LAST`.** The window `ORDER BY` reuses DQL's `ORDER BY` items, which do not support them.
- **No `DISTINCT` in a window aggregate.** PostgreSQL rejects `OVER(COUNT(DISTINCT e.id))`.
- **Results hydrate as scalars.** A window result is a scalar column, hydrated as the driver returns the aggregate's or function's type. Selected next to an entity, each result row is a mixed array such as `[0 => $entity, 'runningTotal' => '42.00']`.
- **`Paginator` cannot sort by a window result by default.** With its defaults (`fetchJoinCollection: true` and output walkers enabled), Doctrine's `Paginator` rewrites the query's outer `ORDER BY` into its own `ROW_NUMBER() OVER (ORDER BY ...)`. Ordering by a window result alias then nests one window function inside another, which PostgreSQL rejects with `window functions are not allowed in window definitions`. Ordering by entity fields works. To order by the window result, construct the paginator with `new Paginator($query, false)` or call `$paginator->setUseOutputWalkers(false)`.
