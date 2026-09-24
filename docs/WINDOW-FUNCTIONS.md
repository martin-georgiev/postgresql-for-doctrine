# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> Window Functions

> 📖 **See also**: [PostgreSQL window functions](https://www.postgresql.org/docs/18/functions-window.html) and [window function calls](https://www.postgresql.org/docs/18/sql-expressions.html#SYNTAX-WINDOW-FUNCTIONS)

DQL cannot parse anything after a function's closing parenthesis, so `OVER (...)` cannot follow the call the way it does in SQL. Each window function is registered under a `_OVER` name instead, and the window specification goes **inside** the call, after the function's own arguments:

| DQL | Generated SQL |
|---|---|
| `ROW_NUMBER_OVER()` | `row_number() OVER ()` |
| `ROW_NUMBER_OVER(PARTITION BY e.a ORDER BY e.b DESC)` | `row_number() OVER (PARTITION BY ... ORDER BY ... DESC)` |
| `RANK_OVER(ORDER BY e.b)` | `rank() OVER (ORDER BY ... ASC)` |
| `NTILE_OVER(4, PARTITION BY e.a ORDER BY e.b)` | `ntile(4) OVER (PARTITION BY ... ORDER BY ... ASC)` |

- `PARTITION BY` takes one or more comma-separated expressions: fields, arithmetic, function calls, literals and parameters.
- `ORDER BY` takes the same items as a DQL `ORDER BY`, each with an optional `ASC` / `DESC`.
- Both clauses are optional, and `PARTITION BY` comes first when both are given.
- When the function takes arguments, a comma separates the last argument from the window specification.

## Ranking Functions

| PostgreSQL function | Register for DQL as | Arguments | Implemented by |
|---|---|---|---|
| cume_dist | CUME_DIST_OVER | none | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\CumeDistOver` |
| dense_rank | DENSE_RANK_OVER | none | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DenseRankOver` |
| ntile | NTILE_OVER | number of buckets | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NtileOver` |
| percent_rank | PERCENT_RANK_OVER | none | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PercentRankOver` |
| rank | RANK_OVER | none | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RankOver` |
| row_number | ROW_NUMBER_OVER | none | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RowNumberOver` |

## Usage Examples

```sql
-- Number the rows of each category, newest first
SELECT e.id, ROW_NUMBER_OVER(PARTITION BY e.category ORDER BY e.createdAt DESC) AS position FROM App\Entity\Article e

-- Leaderboard ranks: RANK_OVER leaves gaps after ties, DENSE_RANK_OVER does not
SELECT p.name, RANK_OVER(ORDER BY p.score DESC) AS rank, DENSE_RANK_OVER(ORDER BY p.score DESC) AS denseRank FROM App\Entity\Player p

-- Several partition expressions
SELECT s.id, ROW_NUMBER_OVER(PARTITION BY s.region, s.year ORDER BY s.amount DESC) AS position FROM App\Entity\Sale s

-- Relative standing within a partition (0..1)
SELECT s.id, PERCENT_RANK_OVER(PARTITION BY s.region ORDER BY s.amount) AS percentRank, CUME_DIST_OVER(PARTITION BY s.region ORDER BY s.amount) AS cumeDist FROM App\Entity\Sale s

-- Split each region into quartiles; the bucket count can also be a parameter
SELECT s.id, NTILE_OVER(4, PARTITION BY s.region ORDER BY s.amount) AS quartile FROM App\Entity\Sale s
SELECT s.id, NTILE_OVER(:buckets, ORDER BY s.amount) AS bucket FROM App\Entity\Sale s

-- Next to a selected entity: each result row is [0 => Article, 'position' => int]
SELECT e, ROW_NUMBER_OVER(ORDER BY e.createdAt) AS position FROM App\Entity\Article e ORDER BY e.createdAt
```

## Limitations

- **You cannot filter on a window result in DQL.** PostgreSQL computes window functions after `WHERE`, `GROUP BY` and `HAVING`, so `WHERE position = 1` is invalid SQL, not just invalid DQL. The standard fix - wrapping the query in a subquery in `FROM` and filtering outside it - is something DQL cannot express. For "top N per group" queries use a native query with a `ResultSetMapping`, or filter the rows in PHP.
- **No named windows.** There is no `WINDOW w AS (...)` clause; every call spells out its own specification, even when several calls share it.
- **No frame clauses yet.** `ROWS`, `RANGE` and `GROUPS` frames are not supported. The ranking functions ignore frames anyway.
- **No `NULLS FIRST` / `NULLS LAST`.** The window `ORDER BY` reuses DQL's `ORDER BY` items, which do not support them.
- **Results hydrate as scalars.** A window result is a scalar column: `row_number`, `rank`, `dense_rank` and `ntile` come back as integers, `percent_rank` and `cume_dist` as floats. Selected next to an entity, each result row is a mixed array such as `[0 => $entity, 'position' => 1]`.
- **`Paginator` cannot sort by a window result by default.** With its defaults (`fetchJoinCollection: true` and output walkers enabled), Doctrine's `Paginator` rewrites the query's outer `ORDER BY` into its own `ROW_NUMBER() OVER (ORDER BY ...)`. Ordering by a window result alias then nests one window function inside another, which PostgreSQL rejects with `window functions are not allowed in window definitions`. Ordering by entity fields works. To order by the window result, construct the paginator with `new Paginator($query, false)` or call `$paginator->setUseOutputWalkers(false)`.
