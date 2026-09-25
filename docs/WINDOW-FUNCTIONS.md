# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> Window Functions

> 📖 **See also**: [PostgreSQL window functions](https://www.postgresql.org/docs/18/functions-window.html) and [window function calls](https://www.postgresql.org/docs/18/sql-expressions.html#SYNTAX-WINDOW-FUNCTIONS)

| PostgreSQL syntax | Register for DQL as | Implemented by |
|---|---|---|
| function OVER (window specification) | OVER | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Over` |

## `OVER` wraps the call in DQL

In SQL, `OVER (...)` follows the function's closing parenthesis. DQL cannot parse anything after a function's closing parenthesis, so in DQL `OVER` becomes a function **around** the call, with the window specification as its second argument:

| SQL | DQL |
|---|---|
| `COUNT(e.id) OVER ()` | `OVER(COUNT(e.id))` |
| `SUM(e.amount) OVER (PARTITION BY e.customer)` | `OVER(SUM(e.amount), PARTITION BY e.customer)` |
| `SUM(e.amount) OVER (PARTITION BY e.customer ORDER BY e.createdAt ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW)` | `OVER(SUM(e.amount), PARTITION BY e.customer ORDER BY e.createdAt ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW)` |
| `array_agg(e.tag) OVER (PARTITION BY e.post)` | `OVER(ARRAY_AGG(e.tag), PARTITION BY e.post)` |
| `SUM(e.amount) FILTER (WHERE e.refunded = false) OVER (PARTITION BY e.customer)` | `OVER(FILTER(SUM(e.amount), WHERE e.refunded = FALSE), PARTITION BY e.customer)` |

- A comma separates the call from the window specification, and the specification has no parentheses around it.
- Without a window specification the window is the whole result, so `OVER(COUNT(e.id))` repeats the total row count on every row.
- `FILTER` goes inside `OVER`, mirroring SQL, where `FILTER (WHERE ...)` comes before `OVER (...)`. See [`FILTER` wraps the aggregate in DQL](ARRAY-AND-JSON-FUNCTIONS.md#filter-wraps-the-aggregate-in-dql).

### What `OVER` accepts

- DQL's own `AVG`, `COUNT`, `MAX`, `MIN` and `SUM`.
- Any aggregate from this library, such as `ARRAY_AGG`, `STRING_AGG` or `BOOL_AND`, and a `FILTER(...)` around one.
- A window-only function: the [value functions](#value-functions) `LAG`, `LEAD`, `FIRST_VALUE`, `LAST_VALUE` and `NTH_VALUE`, or any other function implementing `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\WindowFunction`. Implement it, or `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AggregateFunction` for an aggregate, on a function of your own to window that one too.

Anything else, including a scalar function or a nested `OVER`, throws a `ParserException`, as PostgreSQL would reject it.

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
- `FIRST_VALUE`, `LAST_VALUE` and `NTH_VALUE` read the window frame, not the whole partition. With an `ORDER BY` the default frame ends at the current row, so `LAST_VALUE` returns the current row's value and `NTH_VALUE` returns NULL until the frame reaches its n-th row. Add `ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING` to read the whole partition.
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
- **No named windows.** There is no `WINDOW w AS (...)` clause; every call spells out its own specification, even when several calls share it.
- **No `NULLS FIRST` / `NULLS LAST`.** The window `ORDER BY` reuses DQL's `ORDER BY` items, which do not support them.
- **No `DISTINCT` in a window aggregate.** PostgreSQL rejects `OVER(COUNT(DISTINCT e.id))`.
- **Results hydrate as scalars.** A window result is a scalar column, hydrated as the driver returns the aggregate's or function's type. Selected next to an entity, each result row is a mixed array such as `[0 => $entity, 'runningTotal' => '42.00']`.
- **`Paginator` cannot sort by a window result by default.** With its defaults (`fetchJoinCollection: true` and output walkers enabled), Doctrine's `Paginator` rewrites the query's outer `ORDER BY` into its own `ROW_NUMBER() OVER (ORDER BY ...)`. Ordering by a window result alias then nests one window function inside another, which PostgreSQL rejects with `window functions are not allowed in window definitions`. Ordering by entity fields works. To order by the window result, construct the paginator with `new Paginator($query, false)` or call `$paginator->setUseOutputWalkers(false)`.
