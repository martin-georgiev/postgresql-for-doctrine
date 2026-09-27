# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> Common use cases and examples

This page shows common tasks as DQL, each with the result it returns. The results come from the sample rows below, measured on PostgreSQL 18 and PostGIS 3.6 with PHP 8.5, DBAL 4.5 and ORM 3.7, and the session time zone set to UTC. Each `-- →` line under a query is one row of `getResult()`; [What comes back: hydration](HYDRATION.md) explains the PHP types. A query without its own `ORDER BY` returns rows in no set order; the results list them in the order of the sample rows, or by group after a `GROUP BY`.

<details markdown="1">
<summary>The sample rows behind the results</summary>

| EmailEntity id | subject |
|---|---|
| 1 | Test Email |
| 2 | test email |
| 3 | Welcome |

`User` 1 has `userName` `ada` and `userEmail` `ada@example.com`; `Employee` 7 has `userType` `contractor`.

| Entity id | category | status | active | jsonData | text | createdAt | timestampWithTz | tags |
|---|---|---|---|---|---|---|---|---|
| 1 | books | active | true | `{"items": [{"id": 1, "name": "Dune", "price": 120, "featured": false}, {"id": 2, "name": "Emma", "price": 60, "featured": true}]}` | Call 555-12-3456 or 555-98-7654 before https://example.com/docs, it is important | 2026-09-27 10:37:15 | 2026-03-28 22:30 | `{tag1,tag2}` |
| 2 | books | draft | true | `{"items": [{"id": 3, "name": "Ulysses", "price": 30, "featured": false}]}` | No numbers here | 2026-09-27 10:52 | 2026-10-24 12:00 | `{tag2}` |
| 3 | music | active | false | `{"items": []}` | important: see http://example.org | 2026-09-27 11:05 | 2026-09-27 08:15 | `{}` |

| Entity id | geometry | polygon | point | geometry1 | geometry2 | geometry3d | geometry3d1 | geometry3d2 | trajectory1 | trajectory2 |
|---|---|---|---|---|---|---|---|---|---|---|
| 1 | `POINT(-3 1)` | `POLYGON((0 0,4 0,4 4,0 4,0 0))` | `POINT(1 1)` | `LINESTRING(0 0,2 2)` | `LINESTRING(0 0,2 2)` | `POINT Z(0.5 0.5 0.5)` | `POINT Z(0 0 0)` | `POINT Z(3 4 12)` | `LINESTRING M(0 0 1,10 0 11)` | `LINESTRING M(0 5 1,10 5 11)` |
| 2 | `POINT(3 12)` | `POLYGON((10 10,12 10,12 12,10 12,10 10))` | `POINT(5 5)` | `LINESTRING(0 0,2 2)` | `LINESTRING(5 5,6 6)` | `POINT Z(5 5 5)` | `POINT Z(0 0 0)` | `POINT Z(600 800 0)` | | |
| 3 | `POINT(30 1)` | `POLYGON((0 0,1 0,1 1,0 1,0 0))` | `POINT(0.5 0.5)` | `POINT(1 1)` | `POINT(1 1)` | `POINT Z(9 9 9)` | `POINT Z(1 1 1)` | `POINT Z(1 1 2)` | | |

| Order id | customer | total and amount | status | placedAt and createdAt |
|---|---|---|---|---|
| 1 | 1 | 40.00 | paid | 2025-12-20 10:00 |
| 2 | 1 | 60.00 | paid | 2026-02-01 10:00 |
| 3 | 1 | 20.00 | refunded | 2026-03-01 10:00 |
| 4 | 2 | 100.00 | paid | 2026-04-01 10:00 |

`Request` holds 20 rows, with `durationMs` from 10 to 200 in steps of 10.

| Post id | tags |
|---|---|
| 1 | php, legacy, doctrine |
| 2 | legacy |

Of the `Tag` rows, only `legacy` is archived.

| DailyStat day | visits |
|---|---|
| 2026-09-01 | 100 |
| 2026-09-02 | 140 |
| 2026-09-03 | 90 |
| 2026-09-08 | 70 |

| Player id | name | score |
|---|---|---|
| 1 | Ada | 90 |
| 2 | Grace | 95 |
| 3 | Linus | 90 |

| Product id | priceRange | availabilityPeriod | item |
|---|---|---|---|
| 1 | `[10.50,99.99)` | `[2024-01-01,2024-12-31)` | `(Widget,1,9.99)` |
| 2 | `[60,80)` | `[2024-06-10,2024-07-10)` | `(Gadget,2,24.50)` |

</details>


## Operators such as `ILIKE` are functions in DQL

`Error: Expected =, <, <=, <>, >, >=, !=, got 'ILIKE'` (the column number depends on your query) is one of the most common DQL errors with this library. `ILIKE` is not one of [the operators DQL knows](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#other-expressions-1), and Doctrine has no way to add one, so this library registers `ILIKE` as a boolean function instead. [A boolean function needs a comparison](DQL-DIALECT.md#boolean-functions-need-a-comparison).

In PostgreSQL you would write:
```sql
SELECT * FROM emails WHERE subject ILIKE 'Test email';
```

The same in DQL does not parse:
```sql
SELECT e
FROM EmailEntity e
WHERE e.subject ILIKE 'Test email'
```

Call `ILIKE` as a function and compare the result with `TRUE`:
```sql
SELECT e
FROM EmailEntity e
WHERE ILIKE(e.subject, 'Test email') = TRUE
-- → [EmailEntity {id: 1}, EmailEntity {id: 2}]
```

`CONTAINS`, `IS_CONTAINED_BY`, `DATE_OVERLAPS` and the other operators this library adds work the same way.

## Using JSON_BUILD_OBJECT and JSONB_BUILD_OBJECT

Both functions take keys and values in turns. Each argument can be a string literal, a field, or an expression such as an aggregate. A number, `TRUE`, `FALSE` or `NULL` written straight into the DQL does not parse. A bare parameter parses, but PostgreSQL rejects it with `could not determine data type of parameter`, so wrap it in `CAST`:

> **See also:** [Array and JSON functions](ARRAY-AND-JSON-FUNCTIONS.md) for every JSON and JSONB function

```sql
-- Fields and string literals
SELECT JSON_BUILD_OBJECT('name', e.userName, 'email', e.userEmail) FROM User e
-- → [1 => '{"name" : "ada", "email" : "ada@example.com"}']
SELECT JSONB_BUILD_OBJECT('id', e.id, 'status', 'active', 'type', e.userType) FROM Employee e
-- → [1 => '{"id": 7, "type": "contractor", "status": "active"}']

-- An aggregate as a value
SELECT JSON_BUILD_OBJECT('category', e.category, 'count', COUNT(e.id)) FROM Entity e GROUP BY e.category
-- → [1 => '{"category" : "books", "count" : 2}']
--   [1 => '{"category" : "music", "count" : 1}']

-- A number from a parameter
SELECT JSONB_BUILD_OBJECT('limit', CAST(:limit AS INTEGER)) FROM Entity e
-- → [1 => '{"limit": 10}']
--   [1 => '{"limit": 10}']
--   [1 => '{"limit": 10}']

-- Does not parse: a number, boolean or NULL literal
SELECT JSONB_BUILD_OBJECT('number', 123) FROM Entity e
```

## Using JSON path functions

The [JSON path functions](https://www.postgresql.org/docs/18/functions-json.html#FUNCTIONS-SQLJSON-PATH) query a `jsonb` value with a path expression. They need PostgreSQL 12 or newer, and the two that return a boolean need `= TRUE`:

> **See also:** [Array and JSON functions](ARRAY-AND-JSON-FUNCTIONS.md) for every JSON path function

```sql
-- Check if a JSON path exists with a condition
SELECT e FROM Entity e WHERE JSONB_PATH_EXISTS(e.jsonData, '$.items[*] ? (@.price > 100)') = TRUE
-- → [Entity {id: 1}]

-- Check if a JSON path matches a condition
SELECT e FROM Entity e WHERE JSONB_PATH_MATCH(e.jsonData, 'exists($.items[*] ? (@.price >= 50 && @.price <= 100))') = TRUE
-- → [Entity {id: 1}]

-- Extract all items matching a path query
SELECT e.id, JSONB_PATH_QUERY(e.jsonData, '$.items[*].name') FROM Entity e
-- → ['id' => 1, 1 => '"Dune"']
--   ['id' => 1, 1 => '"Emma"']
--   ['id' => 2, 1 => '"Ulysses"']

-- Extract all items as an array
SELECT e.id, JSONB_PATH_QUERY_ARRAY(e.jsonData, '$.items[*].id') FROM Entity e
-- → ['id' => 1, 1 => '[1, 2]']
--   ['id' => 2, 1 => '[3]']
--   ['id' => 3, 1 => '[]']

-- Extract the first item matching a path query
SELECT e.id, JSONB_PATH_QUERY_FIRST(e.jsonData, '$.items[*] ? (@.featured == true)') FROM Entity e
-- → ['id' => 1, 1 => '{"id": 2, "name": "Emma", "price": 60, "featured": true}']
--   ['id' => 2, 1 => null]
--   ['id' => 3, 1 => null]
```

## Using regular expression functions

`REGEXP_COUNT`, `REGEXP_INSTR` and `REGEXP_SUBSTR` need PostgreSQL 15 or newer:

> **See also:** [Text and pattern functions](TEXT-AND-PATTERN-FUNCTIONS.md) for every regular expression and text function

```sql
-- Count occurrences of a pattern
SELECT e.id, REGEXP_COUNT(e.text, '\d{3}-\d{2}-\d{4}') as ssn_count FROM Entity e
-- → ['id' => 1, 'ssn_count' => 2]
--   ['id' => 2, 'ssn_count' => 0]
--   ['id' => 3, 'ssn_count' => 0]

-- Find position of a pattern
SELECT e.id, REGEXP_INSTR(e.text, 'important') as position FROM Entity e
-- → ['id' => 1, 'position' => 72]
--   ['id' => 2, 'position' => 0]
--   ['id' => 3, 'position' => 1]

-- Extract substring matching a pattern
SELECT e.id, REGEXP_SUBSTR(e.text, 'https?://[\w.-]+') as url FROM Entity e
-- → ['id' => 1, 'url' => 'https://example.com']
--   ['id' => 2, 'url' => null]
--   ['id' => 3, 'url' => 'http://example.org']
```

## Using date functions

`DATE_BIN` needs PostgreSQL 14 or newer, and `DATE_ADD` and `DATE_SUBTRACT` need PostgreSQL 16. The time zone argument is optional:

> **See also:** [Date and range functions](DATE-AND-RANGE-FUNCTIONS.md) for every date, time and range function

```sql
-- Bin timestamps into 15-minute intervals
SELECT DATE_BIN('15 minutes', e.createdAt, '2001-01-01') FROM Entity e
-- → [1 => '2026-09-27 10:30:00+00']
--   [1 => '2026-09-27 10:45:00+00']
--   [1 => '2026-09-27 11:00:00+00']

-- Add an interval to a timestamp (timezone parameter is optional)
SELECT DATE_ADD(e.timestampWithTz, '1 day') FROM Entity e
-- → [1 => '2026-03-29 22:30:00+00']
--   [1 => '2026-10-25 12:00:00+00']
--   [1 => '2026-09-28 08:15:00+00']
SELECT DATE_ADD(e.timestampWithTz, '1 day', 'Europe/London') FROM Entity e
-- → [1 => '2026-03-29 21:30:00+00']
--   [1 => '2026-10-25 13:00:00+00']
--   [1 => '2026-09-28 08:15:00+00']

-- Subtract an interval from a timestamp (timezone parameter is optional)
SELECT DATE_SUBTRACT(e.timestampWithTz, '2 hours') FROM Entity e
-- → [1 => '2026-03-28 20:30:00+00']
--   [1 => '2026-10-24 10:00:00+00']
--   [1 => '2026-09-27 06:15:00+00']
SELECT DATE_SUBTRACT(e.timestampWithTz, '2 hours', 'UTC') FROM Entity e
-- → [1 => '2026-03-28 20:30:00+00']
--   [1 => '2026-10-24 10:00:00+00']
--   [1 => '2026-09-27 06:15:00+00']

-- Truncate a timestamp to a specified precision (timezone parameter is optional)
SELECT DATE_TRUNC('day', e.timestampWithTz) FROM Entity e
-- → [1 => '2026-03-28 00:00:00+00']
--   [1 => '2026-10-24 00:00:00+00']
--   [1 => '2026-09-27 00:00:00+00']
SELECT DATE_TRUNC('day', e.timestampWithTz, 'UTC') FROM Entity e
-- → [1 => '2026-03-28 00:00:00+00']
--   [1 => '2026-10-24 00:00:00+00']
--   [1 => '2026-09-27 00:00:00+00']
```

## Medians, percentiles and the most common value

PostgreSQL computes these with ordered-set aggregates, written in SQL as `percentile_cont(0.5) WITHIN GROUP (ORDER BY e.value)`. DQL cannot parse anything after a function's closing parenthesis, so the `WITHIN GROUP ORDER BY` part moves **inside** the call, with no comma before it and no parentheses around it:

```sql
-- SQL:  percentile_cont(0.5) WITHIN GROUP (ORDER BY o.total)
-- DQL:  PERCENTILE_CONT(0.5 WITHIN GROUP ORDER BY o.total)
```

> **See also:** [Mathematical functions](MATHEMATICAL-FUNCTIONS.md#within-group-goes-inside-the-parentheses-in-dql) for the full list of rules

```sql
-- Median order value per customer (interpolated between the two middle values)
SELECT c.id, PERCENTILE_CONT(0.5 WITHIN GROUP ORDER BY o.total) AS medianTotal
FROM Order o JOIN o.customer c GROUP BY c.id
-- → ['id' => 1, 'medianTotal' => 40.0]
--   ['id' => 2, 'medianTotal' => 100.0]

-- 95th percentile response time, always a value that was actually recorded
SELECT PERCENTILE_DISC(0.95 WITHIN GROUP ORDER BY r.durationMs) AS p95 FROM Request r
-- → ['p95' => 190]

-- The fraction can be a parameter
SELECT PERCENTILE_CONT(:fraction WITHIN GROUP ORDER BY r.durationMs) AS percentile FROM Request r
-- → ['percentile' => 181.0]

-- Most common status per category; MODE takes no fraction, so the call starts with WITHIN GROUP
SELECT e.category, MODE(WITHIN GROUP ORDER BY e.status) AS commonStatus FROM Entity e GROUP BY e.category
-- → ['category' => 'books', 'commonStatus' => 'active']
--   ['category' => 'music', 'commonStatus' => 'active']
```

- `ORDER BY` takes exactly one item; PostgreSQL rejects more.
- The fraction must not read an ungrouped column: use a literal, a parameter, or columns listed in `GROUP BY`.

## Aggregating only some rows with FILTER

PostgreSQL restricts the rows a single aggregate reads with `FILTER (WHERE ...)`, written in SQL after the aggregate: `COUNT(o.id) FILTER (WHERE o.status = 'paid')`. DQL cannot parse anything after a function's closing parenthesis, so `FILTER` wraps the aggregate instead, with the condition as its second argument:

```sql
-- SQL:  COUNT(o.id) FILTER (WHERE o.status = 'paid')
-- DQL:  FILTER(COUNT(o.id), WHERE o.status = 'paid')
```

> **See also:** [Array and JSON functions](ARRAY-AND-JSON-FUNCTIONS.md#filter-wraps-the-aggregate-in-dql) for the full list of rules

```sql
-- Several conditional counts in one pass, instead of one query per status
SELECT c.id,
       COUNT(o.id) AS allOrders,
       FILTER(COUNT(o.id), WHERE o.status = 'paid') AS paidOrders,
       FILTER(COUNT(o.id), WHERE o.status = 'refunded') AS refundedOrders
FROM Order o JOIN o.customer c GROUP BY c.id
-- → ['id' => 1, 'allOrders' => 3, 'paidOrders' => 2, 'refundedOrders' => 1]
--   ['id' => 2, 'allOrders' => 1, 'paidOrders' => 1, 'refundedOrders' => 0]

-- Revenue this year next to all-time revenue; the condition can take parameters
SELECT c.id, SUM(o.total) AS allTime, FILTER(SUM(o.total), WHERE o.placedAt >= :startOfYear) AS thisYear
FROM Order o JOIN o.customer c GROUP BY c.id
-- → ['id' => 1, 'allTime' => '120.00', 'thisYear' => '80.00']
--   ['id' => 2, 'allTime' => '100.00', 'thisYear' => '100.00']

-- The library's own aggregates wrap the same way
SELECT p.id, FILTER(ARRAY_AGG(t.name ORDER BY t.name), WHERE t.archived = FALSE) AS activeTags
FROM Post p JOIN p.tags t GROUP BY p.id
-- → ['id' => 1, 'activeTags' => '{doctrine,php}']
--   ['id' => 2, 'activeTags' => null]

-- Filter in HAVING too: categories with more than ten active items
SELECT e.category FROM Entity e GROUP BY e.category HAVING FILTER(COUNT(e.id), WHERE e.active = TRUE) > 10
-- → no rows: no category has more than ten active entities in the sample
```

- The first argument must be an aggregate; a scalar function or a nested `FILTER` throws a `ParserException`.

## Running totals and moving averages with OVER

PostgreSQL runs an aggregate over a window of rows with `OVER (...)`, written in SQL after the call: `SUM(o.amount) OVER (PARTITION BY o.customer ORDER BY o.createdAt)`. DQL cannot parse anything after a function's closing parenthesis, so `OVER` wraps the call instead, with the window specification as its second argument:

```sql
-- SQL:  SUM(o.amount) OVER (PARTITION BY o.customer ORDER BY o.createdAt)
-- DQL:  OVER(SUM(o.amount), PARTITION BY o.customer ORDER BY o.createdAt)
```

> **See also:** [Window functions](WINDOW-FUNCTIONS.md#over-wraps-the-call-in-dql) for the full list of rules

```sql
-- Running total per customer
SELECT o.id, OVER(SUM(o.amount), PARTITION BY o.customer ORDER BY o.createdAt ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS runningTotal
FROM Order o
-- → ['id' => 1, 'runningTotal' => '40.00']
--   ['id' => 2, 'runningTotal' => '100.00']
--   ['id' => 3, 'runningTotal' => '120.00']
--   ['id' => 4, 'runningTotal' => '100.00']

-- Seven-day moving average
SELECT d.day, OVER(AVG(d.visits), ORDER BY d.day RANGE BETWEEN '6 days' PRECEDING AND CURRENT ROW) AS weeklyAverage FROM DailyStat d
-- → ['day' => DateTimeImmutable('2026-09-01'), 'weeklyAverage' => '100.0000000000000000']
--   ['day' => DateTimeImmutable('2026-09-02'), 'weeklyAverage' => '120.0000000000000000']
--   ['day' => DateTimeImmutable('2026-09-03'), 'weeklyAverage' => '110.0000000000000000']
--   ['day' => DateTimeImmutable('2026-09-08'), 'weeklyAverage' => '100.0000000000000000']

-- Each order's share of its customer's total
SELECT o.id, o.amount / OVER(SUM(o.amount), PARTITION BY o.customer) AS share FROM Order o
-- → ['id' => 1, 'share' => '0.33333333333333333333']
--   ['id' => 2, 'share' => '0.50000000000000000000']
--   ['id' => 3, 'share' => '0.16666666666666666667']
--   ['id' => 4, 'share' => '1.00000000000000000000']

-- Paid revenue per customer next to every order, with FILTER inside OVER as in SQL
SELECT o.id, OVER(FILTER(SUM(o.amount), WHERE o.status = 'paid'), PARTITION BY o.customer) AS paidTotal FROM Order o
-- → ['id' => 1, 'paidTotal' => '100.00']
--   ['id' => 2, 'paidTotal' => '100.00']
--   ['id' => 3, 'paidTotal' => '100.00']
--   ['id' => 4, 'paidTotal' => '100.00']

-- Each order next to the customer's previous order amount, 0 for their first order
SELECT o.id, o.amount, OVER(LAG(o.amount, 1, 0), PARTITION BY o.customer ORDER BY o.createdAt) AS previousAmount FROM Order o
-- → ['id' => 1, 'amount' => '40.00', 'previousAmount' => '0']
--   ['id' => 2, 'amount' => '60.00', 'previousAmount' => '40.00']
--   ['id' => 3, 'amount' => '20.00', 'previousAmount' => '60.00']
--   ['id' => 4, 'amount' => '100.00', 'previousAmount' => '0']
```

The ranking functions (`ROW_NUMBER`, `RANK`, `DENSE_RANK`, `PERCENT_RANK`, `CUME_DIST`, `NTILE`) exist only inside `OVER`:

```sql
-- Number each customer's orders, newest first
SELECT o.id, OVER(ROW_NUMBER(), PARTITION BY o.customer ORDER BY o.createdAt DESC) AS orderNumber FROM Order o
-- → ['id' => 1, 'orderNumber' => 3]
--   ['id' => 2, 'orderNumber' => 2]
--   ['id' => 3, 'orderNumber' => 1]
--   ['id' => 4, 'orderNumber' => 1]

-- Rank players by score; tied players share a rank
SELECT p.name, OVER(RANK(), ORDER BY p.score DESC) AS scoreRank FROM Player p
-- → ['name' => 'Ada', 'scoreRank' => 2]
--   ['name' => 'Grace', 'scoreRank' => 1]
--   ['name' => 'Linus', 'scoreRank' => 2]
```

> **See also:** [Ranking functions](WINDOW-FUNCTIONS.md#ranking-functions)

- Filtering on a window result (`WHERE runningTotal > 100`) is not possible in DQL; use a native query or filter in PHP.

## Using range types

Map a range column to its value object, then query it with the range operators:

> **See also:** [Range types](RANGE-TYPES.md) for the range value objects and [Date and range functions](DATE-AND-RANGE-FUNCTIONS.md) for range functions

```php
// Entity with range fields
#[ORM\Entity]
class Product
{
    #[ORM\Column(type: 'numrange')]
    private ?NumericRange $priceRange = null;

    #[ORM\Column(type: 'daterange')]
    private ?DateRange $availabilityPeriod = null;
}

// Create ranges
$product = new Product();
$product->setPriceRange(new NumericRange(10.50, 99.99));
$product->setAvailabilityPeriod(new DateRange(
    new \DateTimeImmutable('2024-01-01'),
    new \DateTimeImmutable('2024-12-31')
));

// Check if values are in range
if ($product->getPriceRange()->contains(25.00)) {
    echo "Price is in range";
}
```

```sql
-- Find products with overlapping price ranges
SELECT p FROM Product p WHERE OVERLAPS(p.priceRange, NUMRANGE('20', '50')) = TRUE
-- → [Product {id: 1}]

-- Find products available in a specific period
SELECT p FROM Product p WHERE CONTAINS(p.availabilityPeriod, DATERANGE('2024-06-01', '2024-06-30')) = TRUE
-- → [Product {id: 1}]

-- Find products whose price range contains 25.0 (a bare '25.0' would be read as a range literal and rejected)
SELECT p FROM Product p WHERE CONTAINS(p.priceRange, NUMRANGE('25.0', '25.0', '[]')) = TRUE
-- → [Product {id: 1}]
```

## Using PostgreSQL composite types

A composite type groups named fields into one column. `COMPOSITE_FIELD` reads a single field of it in DQL.

> **See also:** [PostgreSQL composite types](COMPOSITE-TYPE.md) · [composite types in the PostgreSQL manual](https://www.postgresql.org/docs/18/rowtypes.html)

### Creating composite types in PostgreSQL

```sql
-- Create a composite type for inventory items
CREATE TYPE inventory_item AS (
    name TEXT,
    supplier_id INTEGER,
    price NUMERIC(10,2)
);

-- Create a table using the composite type
CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    item inventory_item
);

-- Insert data using ROW constructor
INSERT INTO products (item) VALUES (ROW('Widget', 1, 9.99));
```

### Accessing composite fields in DQL

```sql
-- Access a field from a composite type
SELECT COMPOSITE_FIELD(p.item, 'name') FROM Product p
-- → [1 => 'Widget']
--   [1 => 'Gadget']

-- Use composite fields in WHERE clauses
SELECT p FROM Product p WHERE COMPOSITE_FIELD(p.item, 'price') > 10.00
-- → [Product {id: 2}]
```

### Entity configuration

Map the column to a subclass of `Composite` registered under the composite type's name - here an `InventoryItemType` registered as `inventory_item`. [Composite types](COMPOSITE-TYPE.md) shows how to create and register it.

```php
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var array{name: string|null, supplier_id: int|null, price: string|null}|null
     */
    #[ORM\Column(type: 'inventory_item', nullable: true)]
    private ?array $item = null;
}
```

## Using PostGIS types

> **Requires the [`postgis`](https://postgis.net/docs/postgis_installation.html) extension:** `CREATE EXTENSION IF NOT EXISTS postgis;`

### Using PostGIS types with Doctrine DBAL (geometry/geography)

Register the types, then bind `WktSpatialData` values. They travel to and from PostgreSQL as WKT or EWKT text.

```sql
CREATE TABLE places (
    id SERIAL PRIMARY KEY,
    location GEOMETRY,
    boundary GEOGRAPHY
);
```

```php
use Doctrine\DBAL\Types\Type as DoctrineType;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

DoctrineType::addType('geometry', MartinGeorgiev\Doctrine\DBAL\Types\Geometry::class);
DoctrineType::addType('geometry[]', MartinGeorgiev\Doctrine\DBAL\Types\GeometryArray::class);
DoctrineType::addType('geography', MartinGeorgiev\Doctrine\DBAL\Types\Geography::class);

// Insert a single geometry value
$qb = $connection->createQueryBuilder();
$qb->insert('places')->values(['location' => ':wktSpatialData']);
$qb->setParameter('wktSpatialData', WktSpatialData::fromString('POINT(1 2)'), 'geometry');
$qb->executeStatement();

// Insert a single geography value with SRID
$qb = $connection->createQueryBuilder();
$qb->insert('places')->values(['boundary' => ':wktSpatialData']);
$qb->setParameter('wktSpatialData', WktSpatialData::fromString('SRID=4326;POINT(-122.4194 37.7749)'), 'geography');
$qb->executeStatement();

// Insert a single-item geometry[] array
$qb = $connection->createQueryBuilder();
$qb->insert('routes')->values(['geometriesLines' => ':wktSpatialData']);
$qb->setParameter('wktSpatialData', [WktSpatialData::fromString('LINESTRING(0 0, 1 1)')], 'geometry[]');
$qb->executeStatement();
```

A value reads back with its dimensional modifier spelled `POINT Z(1 2 3)`, whether you wrote `POINT Z (1 2 3)` or `POINTZ(1 2 3)`; see [How dimensional modifiers are spelled](SPATIAL-TYPES.md#how-dimensional-modifiers-are-spelled).

For array columns, see [Geometry and geography arrays](GEOMETRY-ARRAYS.md).

### Using PostGIS spatial operators in DQL

The PostGIS operators compare bounding boxes or measure distances. The bounding box operators return booleans and must be compared with `= TRUE` or `= FALSE` in DQL; the distance operators return numbers.

> **See also:** [PostGIS spatial functions and operators](SPATIAL-FUNCTIONS-AND-OPERATORS.md) for every spatial function and operator

#### Bounding box spatial relationships

```sql
-- Find geometries to the left of a reference point
SELECT e FROM Entity e WHERE STRICTLY_LEFT(e.geometry, 'POINT(0 0)') = TRUE
-- → [Entity {id: 1}]

-- Find geometries that spatially contain a point (bounding box level)
SELECT e FROM Entity e WHERE SPATIAL_CONTAINS(e.polygon, 'POINT(1 1)') = TRUE
-- → [Entity {id: 1}, Entity {id: 3}]

-- Find geometries contained within a bounding box
SELECT e FROM Entity e WHERE SPATIAL_CONTAINED_BY(e.geometry, 'POLYGON((0 0, 20 0, 20 20, 0 20, 0 0))') = TRUE
-- → [Entity {id: 2}]

-- Check if two geometries have the same bounding box
SELECT e FROM Entity e WHERE SPATIAL_SAME(e.geometry1, e.geometry2) = TRUE
-- → [Entity {id: 1}, Entity {id: 3}]

-- Vertical relationships
SELECT e FROM Entity e WHERE STRICTLY_ABOVE(e.geometry, 'LINESTRING(0 0, 5 0)') = TRUE
-- → [Entity {id: 1}, Entity {id: 2}, Entity {id: 3}]
SELECT e FROM Entity e WHERE OVERLAPS_BELOW(e.geometry, 'POLYGON((0 5, 5 5, 5 10, 0 10, 0 5))') = TRUE
-- → [Entity {id: 1}, Entity {id: 3}]

-- n-D bounding boxes overlap
SELECT e FROM Entity e WHERE ND_OVERLAPS(e.geometry3d, 'POLYGON Z((0 0 0, 1 0 0, 1 1 1, 0 0 0))') = TRUE
-- → [Entity {id: 1}]
```

#### Distance-based queries

```sql
-- Find the nearest geometries to a point
SELECT e, GEOMETRY_DISTANCE(e.geometry, 'POINT(0 0)') as distance
FROM Entity e
ORDER BY distance
-- → [0 => Entity {id: 1}, 'distance' => 3.1622776601683795]
--   [0 => Entity {id: 2}, 'distance' => 12.36931687685298]
--   [0 => Entity {id: 3}, 'distance' => 30.01666203960727]

-- Geometries whose bounding box lies within 1000 units of the point; for an exact distance use ST_DWITHIN
SELECT e FROM Entity e WHERE BOUNDING_BOX_DISTANCE(e.geometry, 'POINT(0 0)') < 1000
-- → [Entity {id: 1}, Entity {id: 2}, Entity {id: 3}]

-- Calculate trajectory distances (for linestrings with measure values)
SELECT TRAJECTORY_DISTANCE(e.trajectory1, e.trajectory2) as closest_approach
FROM Entity e
WHERE e.trajectory1 IS NOT NULL
-- → ['closest_approach' => 5.0]

-- n-D distance between the bounding box centroids
SELECT e, ND_CENTROID_DISTANCE(e.geometry3d1, e.geometry3d2) as distance3d
FROM Entity e
WHERE ND_CENTROID_DISTANCE(e.geometry3d1, e.geometry3d2) < 500
-- → [0 => Entity {id: 1}, 'distance3d' => 13.0]
--   [0 => Entity {id: 3}, 'distance3d' => 1.0]
```

#### Operators with several meanings

Containment is `@>` for arrays and `~` for geometries, and `~` also matches a regular expression against text. Each meaning has its own DQL name:

```sql
SELECT e FROM Entity e WHERE CONTAINS(e.tags, ARRAY('tag1')) = TRUE      -- Array containment
SELECT e FROM Entity e WHERE SPATIAL_CONTAINS(e.polygon, e.point) = TRUE -- Spatial containment
SELECT e FROM Entity e WHERE REGEXP(e.text, 'pattern') = TRUE            -- Text pattern matching
```

#### Using the spatial index

```sql
-- ST_INTERSECTS checks the bounding boxes against the spatial index by itself; no pre-filter is needed
SELECT e FROM Entity e WHERE ST_INTERSECTS(e.geometry, 'POLYGON((0 0, 20 0, 20 20, 0 20, 0 0))') = TRUE
-- → [Entity {id: 2}]

-- Nearest first; with setMaxResults() PostgreSQL reads the nearest rows straight from the GiST index
SELECT e FROM Entity e ORDER BY GEOMETRY_DISTANCE(e.geometry, 'POINT(0 0)')
-- → [Entity {id: 1}, Entity {id: 2}, Entity {id: 3}]
```

## Hierarchical data with ltree

> **See also:** [PostgreSQL ltree types](LTREE-TYPE.md) for type reference and DQL functions

An entity that points to its parent and keeps its `ltree` path in step with it, plus a Symfony listener that moves the descendants along when a node moves.

### Entity

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

/**
 * Manually edit `my_entity_path_gist_idx` in migration to use GiST.
 * Declaring the index using Doctrine attributes prevents its removal during migrations.
 */
#[ORM\Entity]
#[ORM\Index(columns: ['path'], name: 'my_entity_path_gist_idx')]
class MyEntity implements \Stringable
{
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Id]
    private Uuid $id;

    #[ORM\Column(type: 'ltree')]
    private Ltree $path;

    /** @var Collection<array-key, MyEntity> */
    #[ORM\OneToMany(targetEntity: MyEntity::class, mappedBy: 'parent')]
    private Collection $children;

    public function __construct(
        #[ORM\Column(unique: true, length: 128)]
        private string $name,

        #[ORM\ManyToOne(targetEntity: MyEntity::class, inversedBy: 'children')]
        private ?MyEntity $parent = null,
    ) {
        $this->id = Uuid::v7();
        $this->children = new ArrayCollection();
        $this->path = Ltree::fromString($this->id->toBase58());

        if ($parent instanceof MyEntity) {
            $this->setParent($parent);
        }
    }

    public function __toString(): string { return $this->name; }
    public function getId(): Uuid { return $this->id; }
    public function getParent(): ?MyEntity { return $this->parent; }
    public function getName(): string { return $this->name; }
    public function getPath(): Ltree { return $this->path; }

    /** @return Collection<array-key, MyEntity> */
    public function getChildren(): Collection { return $this->children; }

    public function setName(string $name): void { $this->name = $name; }

    public function setParent(MyEntity $parent): void
    {
        if ($parent->getId()->equals($this->id)) {
            throw new \InvalidArgumentException("Parent can't be self");
        }

        if ($parent->getPath()->isDescendantOf($this->getPath())) {
            throw new \InvalidArgumentException("Parent can't be a descendant of the current node");
        }

        $this->parent = $parent;
        $this->path = $parent->getPath()->withLeaf($this->id->toBase58());
    }
}
```

Doctrine cannot declare an index with an `ltree` operator class, so create it in a migration:

```sql
CREATE INDEX my_entity_path_gist_idx ON my_entity USING GIST (path gist_ltree_ops(siglen=100));
-- Alternative: GIN index
CREATE INDEX my_entity_path_gin_idx ON my_entity USING GIN (path gin_ltree_ops);
```

### Cascading path updates

When a node gets a new parent, every descendant's path has to change too. Doctrine does not do that for you; an [`onFlush`](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/events.html#onflush) listener can:

```php
<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\MyEntity;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;

#[AsDoctrineListener(event: Events::onFlush, priority: 500, connection: 'default')]
final readonly class MyEntityOnFlushListener
{
    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $entityManager = $eventArgs->getObjectManager();
        $unitOfWork = $entityManager->getUnitOfWork();
        $entityMetadata = $entityManager->getClassMetadata(MyEntity::class);

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            $this->processEntity($entity, $entityMetadata, $unitOfWork);
        }
    }

    /** @param ClassMetadata<MyEntity> $entityMetadata */
    private function processEntity(object $entity, ClassMetadata $entityMetadata, UnitOfWork $unitOfWork): void
    {
        if (!$entity instanceof MyEntity || !isset($unitOfWork->getEntityChangeSet($entity)['path'])) {
            return;
        }

        $this->updateChildrenPaths($entity, $entityMetadata, $unitOfWork);
    }

    /** @param ClassMetadata<MyEntity> $entityMetadata */
    private function updateChildrenPaths(MyEntity $entity, ClassMetadata $entityMetadata, UnitOfWork $unitOfWork): void
    {
        foreach ($entity->getChildren() as $child) {
            $child->setParent($entity);
            $unitOfWork->recomputeSingleEntityChangeSet($entityMetadata, $child);
            $this->updateChildrenPaths($child, $entityMetadata, $unitOfWork);
        }
    }
}
