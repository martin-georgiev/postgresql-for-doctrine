# Writing DQL

Being Doctrine's query language, DQL syntax is different from the PostgreSQL's one you already know. PostgreSQL's syntax often does not parse as written in DQL. This page covers rules and patterns that the library follows:

- what each PostgreSQL operator and function is called in DQL;
- why boolean functions need `= TRUE`;
- how clauses written after a closing parenthesis move inside it;
- what each argument accepts; and
- what to do when DQL cannot express a query at all.

> **See also:** [Available functions and operators](AVAILABLE-FUNCTIONS-AND-OPERATORS.md) · [Window functions](WINDOW-FUNCTIONS.md) · [Doctrine setup](INTEGRATING-WITH-DOCTRINE.md)

The examples we used for the query examples follow a simplified entity model for a small shop:

| Entity | Alias | Fields |
|---|---|---|
| `App\Entity\Product` | `p` | `name`, `price`, `tags` (`text[]`), `attributes` (`jsonb`), `category` (`ltree`), `status` |
| `App\Entity\Customer` | `c` | `name`, `points`, `roles` (`jsonb`) |
| `App\Entity\Sale` | `s` | `reference`, `customer`, `amount`, `status`, `placedAt` (`timestamptz`) |
| `App\Entity\Store` | `st` | `name`, `location` (`geography`) |
| `App\Entity\Booking` | `b` | `store`, `slot` (`tstzrange`) |
| `App\Entity\Article` | `a` | `title`, `body`, `search` (`tsvector`), `embedding` (`vector`) |

Every function is registered under the DQL name the setup guides use, as in [Doctrine setup](INTEGRATING-WITH-DOCTRINE.md).

## Function and operator names

DQL has a [fixed set of operators](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#other-expressions-1) (`=`, `<>`, `!=`, `<`, `<=`, `>`, `>=`, `LIKE`, `IN`, `BETWEEN`, `IS NULL`, `MEMBER OF`, `INSTANCE OF`, `EXISTS`) and no way to add another. Every PostgreSQL operator the library supports is therefore a function, which the library renders back into the operator:

```sql
SELECT p.name FROM product p WHERE p.tags @> ARRAY['sport']
```

```dql
SELECT p.name FROM App\Entity\Product p WHERE CONTAINS(p.tags, ARRAY('sport')) = TRUE
```

### The naming rule

You choose the name when you call [`addCustomStringFunction()`](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#adding-your-own-functions-to-the-dql-language). The setup guides, the examples and the issue tracker all use the same rule, so keep to it unless you have a reason not to:

- A PostgreSQL function usually keeps its name in DQL but upper-cased: `jsonb_path_exists` is `JSONB_PATH_EXISTS`, PostGIS's `ST_DWithin` is `ST_DWITHIN`, pg_trgm's `similarity` is `SIMILARITY`.
- An operator or an SQL construct gets a name that says what it does: `@>` is `CONTAINS`, `->>` is `JSON_GET_FIELD_AS_TEXT`, `@@` is `TSMATCH`, `AT TIME ZONE` is `AT_TIME_ZONE`, `(a, b) OVERLAPS (c, d)` is `DATE_OVERLAPS`, `value = ANY(array)` is `IN_ARRAY`, and `ARRAY[...]` is `ARRAY(...)`.
- A few functions are renamed, so that the name says what it works on or does not collide with DQL:

| PostgreSQL | DQL name | Why |
|---|---|---|
| `any(array)`, `all(array)` | `ANY_OF`, `ALL_OF` | [`ANY` and `ALL` are DQL keywords that only take a subquery](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#other-expressions-1) |
| `extract(field FROM source)` | `DATE_EXTRACT` | Named for what it works on |
| `array_dims`, `array_ndims`, `cardinality` | `ARRAY_DIMENSIONS`, `ARRAY_NUMBER_OF_DIMENSIONS`, `ARRAY_CARDINALITY` | Named for what they work on |
| `generate_series` | `GENERATE_NUMERIC_SERIES`, `GENERATE_TIME_SERIES` | One DQL name per signature |
| hstore's `akeys`, `avals`, `defined`, `delete`, `skeys`, `svals` | `HSTORE_AKEYS`, `HSTORE_AVALS`, `HSTORE_DEFINED`, `HSTORE_DELETE`, `HSTORE_SKEYS`, `HSTORE_SVALS` | The bare names are too generic |
| `reverse(bytea)` | `REVERSE_BYTES` | `REVERSE` is the text function |
| `CAST(value AS type)` | `CAST` | [DQL has no cast of its own](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#functions-1) |

Three DQL names are also [Doctrine built-ins](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#functions-1): `DATE_ADD`, `BIT_AND` and `BIT_OR`. Doctrine looks up registered functions first, so once you register the library's versions, they replace Doctrine's everywhere in the application: `DATE_ADD(s.placedAt, 1, 'day')` stops parsing (`got '1'`), and `BIT_AND` becomes the aggregate rather than Doctrine's two-argument bitwise AND. **Register them only if you want PostgreSQL's meaning.**

### One operator, several DQL names

[PostgreSQL picks an operator's implementation from the types of its operands](https://www.postgresql.org/docs/18/typeconv-oper.html), so one symbol can mean several things. Where the meanings are far apart, the library registers one DQL name per meaning, so the query says what it asks:

| Operator | DQL name | Description | Class |
|---|---|---|---|
| `@>` | `CONTAINS` | [Array contains array](https://www.postgresql.org/docs/18/functions-array.html#ARRAY-OPERATORS-TABLE), [`jsonb` contains `jsonb`](https://www.postgresql.org/docs/18/functions-json.html#FUNCTIONS-JSONB-OP-TABLE), [range contains a value or a range](https://www.postgresql.org/docs/18/functions-range.html#RANGE-OPERATORS-TABLE), [`ltree` is an ancestor](https://www.postgresql.org/docs/18/ltree.html#LTREE-OP-TABLE) | `Contains` |
| `<@` | `IS_CONTAINED_BY` | The same relations the other way round | `IsContainedBy` |
| `&&` | `OVERLAPS` | [Arrays share an element](https://www.postgresql.org/docs/18/functions-array.html#ARRAY-OPERATORS-TABLE), [ranges overlap](https://www.postgresql.org/docs/18/functions-range.html#RANGE-OPERATORS-TABLE), [geometry or geography bounding boxes intersect](https://postgis.net/docs/geometry_overlaps.html) | `Overlaps` |
| `~` | `REGEXP` | [Text matches a POSIX regular expression](https://www.postgresql.org/docs/18/functions-matching.html#FUNCTIONS-POSIX-REGEXP) | `Regexp` |
| `~` | `SPATIAL_CONTAINS` | [Geometry bounding box contains the other's](https://postgis.net/docs/ST_Geometry_Contain.html) | `PostGIS\SpatialContains` |
| `~` | `MATCHES_LQUERY` | [`ltree` matches an `lquery`](https://www.postgresql.org/docs/18/ltree.html#LTREE-OP-TABLE); casts the pattern to `lquery` | `Ltree\MatchesLquery` |
| `@` | `SPATIAL_CONTAINED_BY` | [Geometry bounding box is contained by the other's](https://postgis.net/docs/ST_Geometry_Contained.html) | `PostGIS\SpatialContainedBy` |
| `@` | `MATCHES_LTXTQUERY` | [`ltree` matches an `ltxtquery`](https://www.postgresql.org/docs/18/ltree.html#LTREE-OP-TABLE); casts the pattern to `ltxtquery` | `Ltree\MatchesLtxtquery` |
| `?` | `RIGHT_EXISTS_ON_LEFT` | [`jsonb` has the key, or the top-level string](https://www.postgresql.org/docs/18/functions-json.html#FUNCTIONS-JSONB-OP-TABLE) | `TheRightExistsOnTheLeft` |
| `?` | `MATCHES_ANY_LQUERY` | [`ltree` matches any `lquery` in the array](https://www.postgresql.org/docs/18/ltree.html#LTREE-OP-TABLE); casts the patterns to `lquery[]` | `Ltree\MatchesAnyLquery` |
| `<->` | `GEOMETRY_DISTANCE` | [Distance between two geometries](https://postgis.net/docs/geometry_distance_knn.html) | `PostGIS\GeometryDistance` |
| `<->` | `SIMILARITY_DISTANCE` | pg_trgm: [one minus the similarity of two strings](https://www.postgresql.org/docs/18/pgtrgm.html#PGTRGM-OP-TABLE) | `Trgm\SimilarityDistance` |

Except for the _ltree_ casts, names that share an operator render the same SQL, so it is still the operand types that decide what runs: `SPATIAL_CONTAINS(p.name, 'x')` runs a regular expression. Two consequences follow:

- `@>` and `<@` have no geometry variant; for spatial containment use `SPATIAL_CONTAINS` (bounding boxes) or `ST_CONTAINS` (exact shapes).
- When both operands are bare string literals, PostgreSQL [resolves them as `text`](https://www.postgresql.org/docs/18/typeconv-oper.html#OP-RESOL-BEST-MATCH). `'POLYGON((0 0,2 0,2 2,0 2,0 0))' ~ 'POINT(1 1)'` is a regular-expression match and returns false, while the same call with the polygon cast to `geometry` returns true. Keep a column on one side, or give one literal a type with `CAST(... AS GEOMETRY)`.

[_pgvector_'s distance operators](https://github.com/pgvector/pgvector#querying) are reached through the functions `L2_DISTANCE`, `COSINE_DISTANCE` and `INNER_PRODUCT` instead. In the SQL Doctrine logs, the `?` operators appear doubled, as `??`: PDO would read a single `?` as a placeholder, and [turns `??` back into `?`](https://www.php.net/manual/en/pdo.prepare.php#refsect1-pdo.prepare-description) before PostgreSQL sees it.

### Coming from MySQL or DoctrineExtensions

Some habits carry over with a different argument order or spelling:

| You may know | DQL with this library | Note |
|---|---|---|
| `MONTH(s.placedAt)`, `EXTRACT(MONTH FROM s.placedAt)` | `DATE_EXTRACT('month', s.placedAt)` | The field comes first, in single quotes |
| `DATE(s.placedAt)` | `CAST(s.placedAt AS DATE)` | `DATE_TRUNC('day', s.placedAt)` keeps a timestamp |
| `DATE_FORMAT(s.placedAt, '%Y-%m')` | `TO_CHAR(s.placedAt, 'YYYY-MM')` | [PostgreSQL template patterns](https://www.postgresql.org/docs/18/functions-formatting.html#FUNCTIONS-FORMATTING-DATETIME-TABLE) |
| `DATE_ADD(s.placedAt, INTERVAL 1 DAY)` | `DATE_ADD(s.placedAt, '1 day')` | The interval is a string; see the note on built-ins above |
| `NOW()` | `CURRENT_TIMESTAMP()` | [Built into DQL](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#dql-functions) |
| `CAST(s.amount AS SIGNED)` | `CAST(s.amount AS INTEGER)` | The type name is not quoted |
| `GROUP_CONCAT(s.reference ORDER BY s.placedAt SEPARATOR ', ')` | `STRING_AGG(s.reference, ', ' ORDER BY s.placedAt)` | |
| `p.name REGEXP '^Road'` | `REGEXP(p.name, '^Road') = TRUE` | |
| `FIND_IN_SET(:tag, ...)` | `IN_ARRAY(:tag, p.tags) = TRUE` or `:tag = ANY_OF(p.tags)` | |

```dql
SELECT DATE_EXTRACT('month', s.placedAt) AS month, COUNT(s.id) AS sales FROM App\Entity\Sale s GROUP BY month
```

Both arguments of `DATE_EXTRACT` are strings to DQL, so swapping them still parses. [PostgreSQL rejects the result](https://www.postgresql.org/docs/18/functions-datetime.html#FUNCTIONS-DATETIME-EXTRACT):

```text
-- parses, PostgreSQL rejects it: syntax error at or near "."
SELECT DATE_EXTRACT(s.placedAt, 'month') FROM App\Entity\Sale s
```

## Boolean functions need a comparison

A `WHERE` or `HAVING` clause in DQL is [built from conditions](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#conditional-expressions): comparisons, `LIKE`, `IN`, `IS NULL`, `EXISTS` and the like. A function call on its own is a value, not a condition, so the parser stops after it and asks for a comparison operator. Compare a boolean function with `TRUE`:

```sql
SELECT s.* FROM sale s WHERE s.reference ILIKE 'A-%'
```

```dql
SELECT s FROM App\Entity\Sale s WHERE ILIKE(s.reference, :q) = TRUE
```

Without the comparison, and with the SQL operator, the query does not parse:

```text
-- does not parse: Error: Expected =, <, <=, <>, >, >=, !=, got 'ORDER'
SELECT s FROM App\Entity\Sale s WHERE ILIKE(s.reference, :q) ORDER BY s.id
```

```text
-- does not parse: Error: Expected =, <, <=, <>, >, >=, !=, got 'ILIKE'
SELECT s FROM App\Entity\Sale s WHERE s.reference ILIKE :q
```

When nothing follows the function, the message ends in a `got end of string` error instead.

The comparison costs nothing. PostgreSQL removes `= true` before planning, so an index on the operator is used as if you had written the bare operator:

```sql
EXPLAIN (COSTS OFF) SELECT id FROM product WHERE (tags @> ARRAY['sport']) = true
```

```text
Bitmap Heap Scan on product
  Recheck Cond: (tags @> '{sport}'::text[])
  ->  Bitmap Index Scan on product_tags_gin
        Index Cond: (tags @> '{sport}'::text[])
```

`= FALSE` negates. PostgreSQL turns it into `NOT (...)`, which, as in plain SQL, an index on the operator cannot serve.

The rule is about conditions only. In `SELECT` a boolean function needs no comparison, and the column hydrates as a PHP `bool`:

```dql
SELECT p.name, CONTAINS(p.tags, ARRAY('sport')) AS isSport FROM App\Entity\Product p
```

In `HAVING` the boolean aggregates take the same comparison:

```dql
SELECT c.name FROM App\Entity\Sale s JOIN s.customer c GROUP BY c.name HAVING BOOL_AND(ILIKE(s.reference, 'A%')) = TRUE
```

These DQL names return a boolean:

| Family | DQL names |
|---|---|
| Arrays, JSON and ranges | `CONTAINS`, `IS_CONTAINED_BY`, `OVERLAPS`, `IN_ARRAY`, `RIGHT_EXISTS_ON_LEFT`, `ANY_ON_RIGHT_EXISTS_ON_LEFT`, `ALL_ON_RIGHT_EXIST_ON_LEFT`, `RETURNS_VALUE_FOR_JSON_VALUE`, `JSONB_EXISTS`, `JSON_EXISTS`, `JSONB_PATH_EXISTS`, `JSONB_PATH_MATCH` |
| Dates | `DATE_OVERLAPS`, `ISFINITE` |
| Text | `ILIKE`, `SIMILAR_TO`, `NOT_SIMILAR_TO`, `REGEXP`, `IREGEXP`, `NOT_REGEXP`, `NOT_IREGEXP`, `REGEXP_LIKE`, `STARTS_WITH`, `TSMATCH` |
| pg_trgm | `ARE_SIMILAR`, `IS_WORD_SIMILAR_TO`, `CONTAINS_WORD_SIMILAR_TO`, `IS_STRICT_WORD_SIMILAR_TO`, `CONTAINS_STRICT_WORD_SIMILAR_TO` |
| ltree | `MATCHES_LQUERY`, `MATCHES_ANY_LQUERY`, `MATCHES_LTXTQUERY` |
| hstore | `HSTORE_DEFINED` |
| Network | `INET_SAME_FAMILY` |
| XML | `XMLEXISTS`, `XPATH_EXISTS`, `XML_IS_WELL_FORMED`, `XML_IS_WELL_FORMED_CONTENT`, `XML_IS_WELL_FORMED_DOCUMENT` |
| PostGIS bounding boxes | `OVERLAPS_LEFT`, `OVERLAPS_RIGHT`, `OVERLAPS_ABOVE`, `OVERLAPS_BELOW`, `STRICTLY_LEFT`, `STRICTLY_RIGHT`, `STRICTLY_ABOVE`, `STRICTLY_BELOW`, `SPATIAL_CONTAINS`, `SPATIAL_CONTAINED_BY`, `SPATIAL_SAME`, `ND_OVERLAPS` |
| PostGIS relationships | `ST_3DDFULLYWITHIN`, `ST_3DDWITHIN`, `ST_3DINTERSECTS`, `ST_CONTAINS`, `ST_CONTAINSPROPERLY`, `ST_COVEREDBY`, `ST_COVERS`, `ST_CROSSES`, `ST_DFULLYWITHIN`, `ST_DISJOINT`, `ST_DWITHIN`, `ST_EQUALS`, `ST_INTERSECTS`, `ST_ORDERINGEQUALS`, `ST_OVERLAPS`, `ST_POINTINSIDECIRCLE`, `ST_RELATEMATCH`, `ST_TOUCHES`, `ST_WITHIN`, and `ST_RELATE` with three arguments |
| PostGIS properties | `ST_HASM`, `ST_HASZ`, `ST_ISCLOSED`, `ST_ISEMPTY`, `ST_ISVALID` |
| Aggregates | `BOOL_AND`, `BOOL_OR`, `EVERY` |

The distance operators and the JSON accessors return values, not booleans; compare them like any other value, as in `GEOMETRY_DISTANCE(...) < 1000`.

## After the closing parenthesis

PostgreSQL attaches some clauses to a call after its closing parenthesis: [`FILTER (WHERE ...)`](https://www.postgresql.org/docs/18/sql-expressions.html#SYNTAX-AGGREGATES), [`OVER (...)`](https://www.postgresql.org/docs/18/sql-expressions.html#SYNTAX-WINDOW-FUNCTIONS) and [`WITHIN GROUP (ORDER BY ...)`](https://www.postgresql.org/docs/18/sql-expressions.html#SYNTAX-AGGREGATES). DQL reads a function call up to its closing parenthesis and then expects the next part of the query, so none of them can follow a call. The library moves each one inside a call instead:

| SQL | DQL |
|---|---|
| `count(s.id) FILTER (WHERE s.status = 'paid')` | `FILTER(COUNT(s.id), WHERE s.status = 'paid')` |
| `sum(s.amount) OVER (PARTITION BY s.customer_id ORDER BY s.placed_at)` | `OVER(SUM(s.amount), PARTITION BY s.customer ORDER BY s.placedAt)` |
| `sum(s.amount) FILTER (WHERE s.status = 'paid') OVER (PARTITION BY s.customer_id)` | `OVER(FILTER(SUM(s.amount), WHERE s.status = 'paid'), PARTITION BY s.customer)` |
| `percentile_cont(0.5) WITHIN GROUP (ORDER BY s.amount)` | `PERCENTILE_CONT(0.5 WITHIN GROUP ORDER BY s.amount)` |
| `mode() WITHIN GROUP (ORDER BY s.status)` | `MODE(WITHIN GROUP ORDER BY s.status)` |

Written the SQL way, DQL takes the clause keyword for a column alias and fails on the parenthesis after it:

```text
-- does not parse: Error: Expected Doctrine\ORM\Query\TokenType::T_FROM, got '('
SELECT c.name, COUNT(s.id) FILTER (WHERE s.status = 'paid') FROM App\Entity\Sale s JOIN s.customer c GROUP BY c.name
```

### FILTER wraps the aggregate

`FILTER` takes the aggregate, a comma, `WHERE` and the condition. The condition takes anything a DQL `WHERE` does, parameters included, and needs no parentheses. It works in `SELECT` and in `HAVING`, and counts several subsets in one pass over the rows:

```dql
SELECT c.name, COUNT(s.id) AS allSales, FILTER(COUNT(s.id), WHERE s.status = 'paid') AS paidSales, FILTER(SUM(s.amount), WHERE s.placedAt >= :startOfYear) AS paidThisYear
FROM App\Entity\Sale s JOIN s.customer c
GROUP BY c.name
```

### OVER wraps the call

`OVER` takes the call, a comma and the window specification: `PARTITION BY`, `ORDER BY` and a frame, in that order, each optional, with no parentheses around them. [Without a specification the window is the whole result](https://www.postgresql.org/docs/18/sql-expressions.html#SYNTAX-WINDOW-FUNCTIONS). `FILTER` goes inside `OVER`, as it comes before `OVER` in SQL.

```dql
SELECT s.reference, OVER(SUM(s.amount), PARTITION BY s.customer ORDER BY s.placedAt ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS runningTotal
FROM App\Entity\Sale s
```

The window specification, the frame clauses and the ranking and value functions are covered in [Window functions](WINDOW-FUNCTIONS.md).

### WITHIN GROUP moves inside the call

The fraction, then `WITHIN GROUP ORDER BY` and the sort expression, with no comma before `WITHIN` and no parentheses around `ORDER BY`. `MODE` takes no fraction, so its call starts with `WITHIN GROUP`. The fraction is a literal, a parameter, or [an expression over grouped columns](https://www.postgresql.org/docs/18/sql-expressions.html#SYNTAX-AGGREGATES).

```dql
SELECT c.name, PERCENTILE_CONT(0.5 WITHIN GROUP ORDER BY s.amount) AS medianAmount, MODE(WITHIN GROUP ORDER BY s.status) AS usualStatus
FROM App\Entity\Sale s JOIN s.customer c
GROUP BY c.name
```

`ORDER BY` takes exactly one item, [as PostgreSQL requires](https://www.postgresql.org/docs/18/functions-aggregate.html#FUNCTIONS-ORDEREDSET-TABLE):

```text
-- does not parse: Error: Expected a single ORDER BY item, got ')'
SELECT PERCENTILE_CONT(0.5 WITHIN GROUP ORDER BY s.amount, s.id) FROM App\Entity\Sale s
```

[An ordered-set aggregate is an aggregate like any other](https://www.postgresql.org/docs/18/sql-expressions.html#SYNTAX-AGGREGATES), so `FILTER` wraps it:

```dql
SELECT FILTER(PERCENTILE_CONT(0.5 WITHIN GROUP ORDER BY s.amount), WHERE s.status = 'paid') AS medianPaid FROM App\Entity\Sale s
```

### DISTINCT and ORDER BY inside an aggregate

[These already sit inside the parentheses in SQL](https://www.postgresql.org/docs/18/sql-expressions.html#SYNTAX-AGGREGATES), so DQL takes them as written, with no comma before `ORDER BY`:

```dql
SELECT c.name, STRING_AGG(DISTINCT s.status, ', ' ORDER BY s.status) AS statuses
FROM App\Entity\Sale s JOIN s.customer c
GROUP BY c.name
```

| Takes | Aggregates |
|---|---|
| `DISTINCT` and `ORDER BY` | `ARRAY_AGG`, `STRING_AGG`, `JSON_AGG`, `JSONB_AGG`, `RANGE_AGG`, `RANGE_INTERSECT_AGG`, `BIT_AND`, `BIT_OR`, `BIT_XOR`, `BOOL_AND`, `BOOL_OR`, `EVERY`, `STDDEV`, `STDDEV_POP`, `VARIANCE`, `VAR_POP` |
| `ORDER BY` only | `XMLAGG` |
| Neither | `ANY_VALUE`, `CORR`, `COVAR_POP`, `COVAR_SAMP`, `JSON_OBJECT_AGG`, `JSONB_OBJECT_AGG` |
| `DISTINCT` only | [DQL's own `AVG`, `COUNT`, `MAX`, `MIN` and `SUM`](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#aggregate-expressions) |

The sort items are DQL `ORDER BY` items, which [have no `NULLS FIRST` or `NULLS LAST`](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#items). The aggregated value is a field, a string, a parameter or a function call; arithmetic stops the parser at the operator with an error. To overcome this, wrap it in a function that takes arithmetic, such as `CAST`:

```dql
SELECT ARRAY_AGG(CAST(s.amount * 2 AS NUMERIC)) AS doubled FROM App\Entity\Sale s
```

### What FILTER and OVER accept

`FILTER` takes an aggregate: DQL's own `AVG`, `COUNT`, `MAX`, `MIN` and `SUM`, `DISTINCT` included, or a function implementing `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AggregateFunction`, which every aggregate of this library does. `OVER` takes the same, a `FILTER(...)`, or a function implementing `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\WindowFunction`, which the ranking and value functions do. Implement the interface on a function of your own to wrap that one too.

Anything else, including a nested `FILTER` or `OVER`, throws a `ParserException` while the query is parsed, since PostgreSQL would reject it anyway:

```text
-- does not parse: FILTER() requires an aggregate as its first argument, upper() given
SELECT FILTER(UPPER(s.status), WHERE s.status = 'paid') FROM App\Entity\Sale s
```

The PostGIS aggregates, `ST_COVERAGEUNION` and the one-argument `ST_MAKELINE`, do not implement `AggregateFunction`, so neither can be wrapped.

## Literals and parameters

Each argument of a library function is parsed as one kind of DQL expression, fixed by the function's class. The kind decides what you can write in that position:

| Argument kind | Takes | Used by |
|---|---|---|
| [String primary](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#scalar-and-type-expressions) | Fields, single-quoted strings, parameters, function calls and `CASE`; not numbers, arithmetic, `NULL` or `TRUE` | Most arguments: `CONTAINS`, `ILIKE`, `ARRAY`, `NUMRANGE`, `TSTZRANGE`, `DATE_EXTRACT`, the value of an aggregate |
| [Arithmetic](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#arithmetic-expressions) | Fields, numbers, strings, arithmetic, parameters and function calls | `ROUND`, `NTILE`, the fraction of `PERCENTILE_CONT` |
| [Input parameter](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#input-parameter) | A `:name` parameter only | The first argument of `IN_ARRAY` |
| [New value](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#items) | Everything arithmetic takes, plus `NULL` | The element of `ARRAY_APPEND`, `ARRAY_PREPEND`, `ARRAY_REMOVE`, `ARRAY_REPLACE`, `ARRAY_POSITION` and `ARRAY_POSITIONS`; the fill value of `ARRAY_FILL`; `TO_JSON`, `TO_JSONB`; the new value of `JSONB_SET_LAX`; the default of `LAG` and `LEAD` |

### Strings and numbers

A DQL string is [single-quoted](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#terminals). A double-quoted word is not a string:

```text
-- does not parse: Error: Expected StateFieldPathExpression | string | InputParameter | FunctionsReturningStrings | AggregateExpression, got '"'
SELECT DATE_EXTRACT("month", s.placedAt) FROM App\Entity\Sale s
```

A number in a string-primary position fails with the same message, `got '20'`. Quote it, and [PostgreSQL converts the string to the type it needs](https://www.postgresql.org/docs/18/sql-syntax-lexical.html#SQL-SYNTAX-CONSTANTS-GENERIC):

```dql
SELECT NUMRANGE('20', '50') AS priceBand FROM App\Entity\Product p
```

`ARRAY('php', 'postgres')` renders `ARRAY['php', 'postgres']`, which [PostgreSQL types as `text[]`](https://www.postgresql.org/docs/18/typeconv-union-case.html). A `text[]` cannot be compared with an `integer[]` column (`operator does not exist: integer[] && text[]`); there, pass a [PostgreSQL array literal](https://www.postgresql.org/docs/18/arrays.html#ARRAYS-INPUT) as a string instead, such as `'{1,2}'`.

### NULL

`NULL` is accepted only in the new-value positions listed above. Anywhere else it fails with `got 'NULL'`; bind a parameter set to `null` instead:

```dql
SELECT TSTZRANGE(s.placedAt, :null) AS fromThenOn FROM App\Entity\Sale s
```

### Boolean and time-zone arguments

Some functions take an optional boolean or time-zone argument as their last one. Both positions take a string: DQL's `TRUE` and `FALSE` literals are not accepted there, and DQL has no time-zone literal. So both are written as string literals and checked while the query is parsed:

- Booleans are `'true'` or `'false'` and they are supported in `ARRAY_TO_JSON`, `ROW_TO_JSON`, `JSON_STRIP_NULLS`, `JSONB_STRIP_NULLS`, `JSONB_SET`, `JSONB_INSERT`, `JSONB_PATH_EXISTS`, `JSONB_PATH_MATCH`, `JSONB_PATH_QUERY`, `JSONB_PATH_QUERY_ARRAY`, `JSONB_PATH_QUERY_FIRST`, `ST_AREA`, `ST_LENGTH`, `ST_DISTANCE`, `ST_CLOSESTPOINT`, `ST_LINELOCATEPOINT`, `ST_LINEINTERPOLATEPOINT`, `ST_CONCAVEHULL`, `ST_SIMPLIFYPOLYGONHULL`, `ST_REMOVEIRRELEVANTPOINTSFORVIEW`. A bare `TRUE`, a parameter or any other string is rejected.
- Time zones are checked with PHP's `DateTimeZone`. A spelling that only PostgreSQL knows, such as the [POSIX `'UTC+2'`](https://www.postgresql.org/docs/18/datetime-posix-timezone-specs.html), is rejected in `DATE_TRUNC`, `DATE_ADD`, `DATE_SUBTRACT`, `MAKE_TIMESTAMPTZ`.

```dql
SELECT JSONB_SET(p.attributes, '{color}', '"blue"', 'false') AS recoloured FROM App\Entity\Product p
```

```text
-- does not parse: Invalid timezone "UTC+3" provided for date_trunc. Must be a valid PHP timezone identifier.
SELECT DATE_TRUNC('day', s.placedAt, 'UTC+3') FROM App\Entity\Sale s
```

### Binding parameters

A parameter reaches PostgreSQL untyped, and [PostgreSQL gives it the type the other operand needs](https://www.postgresql.org/docs/18/sql-prepare.html). That is why a WKT string can stand for a `geography` value and a `'[1,2,3]'` string for a vector:

```dql
SELECT st.name FROM App\Entity\Store st WHERE ST_DWITHIN(st.location, :here, 20000) = TRUE
```

```php
$query->setParameter('here', 'SRID=4326;POINT(23.7 37.9)');
```

A PHP array is different: Doctrine expands an untyped array parameter into a list, as it does for `IN (:ids)`, and the query fails with `operator does not exist: record = boolean`. [Pass the DBAL type name as the third argument](https://www.doctrine-project.org/projects/doctrine-dbal/en/current/reference/data-retrieval-and-manipulation.html#doctrine-dbal-types-conversion), and the type converts the array into one PostgreSQL literal. Do the same for a value object, so its DBAL type writes it:

```dql
SELECT p.name FROM App\Entity\Product p WHERE OVERLAPS(p.tags, :tags) = TRUE
```

```php
$query->setParameter('tags', ['sport', 'garden'], 'text[]');
```

```dql
SELECT b.id FROM App\Entity\Booking b WHERE OVERLAPS(b.slot, :slot) = TRUE
```

```php
$query->setParameter('slot', new TstzRange($from, $to), 'tstzrange');
```

A list for `IN` is the opposite case, and takes [DBAL's array parameter type](https://www.doctrine-project.org/projects/doctrine-dbal/en/current/reference/data-retrieval-and-manipulation.html#list-of-parameters-conversion): `setParameter('ids', [1, 3], ArrayParameterType::INTEGER)`.

`CONTAINS` on a `jsonb` column [needs a JSON document on the right](https://www.postgresql.org/docs/18/datatype-json.html#JSON-CONTAINMENT). Bind `json_encode(['ROLE_ADMIN'])`, not `'ROLE_ADMIN'`, which PostgreSQL rejects with `invalid input syntax for type json`.

[A jsonpath filter contains `?`](https://www.postgresql.org/docs/18/functions-json.html#FUNCTIONS-SQLJSON-PATH), which is also PDO's placeholder character. Binding the whole path as a parameter keeps it out of the SQL text:

```dql
SELECT p.name FROM App\Entity\Product p WHERE JSONB_PATH_EXISTS(p.attributes, :path) = TRUE
```

```php
$query->setParameter('path', '$.sizes[*] ? (@ == "M")');
```

### CAST

`CAST` takes the target type as an unquoted, one-word identifier, with up to two integer parameters and an optional `[]`: `CAST(s.amount AS INTEGER)`, `CAST(s.amount AS NUMERIC(12, 4))`, `CAST('{1,2}' AS INTEGER[])`. For two-word types use [the one-word alias PostgreSQL provides](https://www.postgresql.org/docs/18/datatype.html#DATATYPE-TABLE), such as `FLOAT8` for `double precision` or `TIMESTAMPTZ` for `timestamp with time zone`:

```text
-- does not parse: Error: Expected Doctrine\ORM\Query\TokenType::T_CLOSE_PARENTHESIS, got 'PRECISION'
SELECT CAST(s.amount AS DOUBLE PRECISION) FROM App\Entity\Sale s
```

## What DQL cannot do

DQL maps queries onto entities, so some SQL has no DQL form at all. For those, drop to a native query, which still hydrates entities through a [`ResultSetMappingBuilder`](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/native-sql.html#resultsetmappingbuilder), or to DBAL:

| You want | Why DQL cannot | Instead |
|---|---|---|
| `DISTINCT ON (...)` | [DQL has no `DISTINCT ON`](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#clauses) | Number the rows with `OVER(ROW_NUMBER(), PARTITION BY ... ORDER BY ...)` and keep the first in PHP; or a native query |
| `WHERE` on a window result | [PostgreSQL computes windows after `WHERE`](https://www.postgresql.org/docs/18/tutorial-window.html); the fix is a subquery in `FROM`, [which DQL does not have](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#from-join-and-index-by) | Filter in PHP, or a native query |
| A set-returning function as a row source, such as `FROM jsonb_array_elements(...)` | DQL's `FROM` takes entities only (`Class 'JSONB_ARRAY_ELEMENTS' is not defined`) | Call it in `SELECT`, [where it returns one row per element](https://www.postgresql.org/docs/18/xfunc-sql.html#XFUNC-SQL-FUNCTIONS-RETURNING-SET), or a native query |
| `WITH` (common table expressions) | [Not in DQL](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#statements) | A native query |
| `INSERT ... ON CONFLICT` | [DQL has no `INSERT`](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#types-of-dql-queries) | DBAL's [`executeStatement()`](https://www.doctrine-project.org/projects/doctrine-dbal/en/current/reference/data-retrieval-and-manipulation.html#executestatement) |
| `INTERVAL '30 days'` | [DQL has no interval literal](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#literal-values) (`Expected end of string, got '30 days'`) | Pass the interval as a string, `DATE_SUBTRACT(CURRENT_TIMESTAMP(), '30 days')`, or compute the bound in PHP and bind it |
| `JSON_TABLE` | Not implemented | A native query |

The latest sale of each customer with a window function. [Each row holds the entity at index 0 and the scalar under its alias](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#pure-and-mixed-results):

```php
$rows = $entityManager->createQuery(
    'SELECT s, OVER(ROW_NUMBER(), PARTITION BY s.customer ORDER BY s.placedAt DESC) AS recency FROM App\Entity\Sale s'
)->getResult();

$latestSales = array_column(array_filter($rows, fn (array $row): bool => $row['recency'] === 1), 0);
```

The same with `DISTINCT ON` in a native query, which leaves the other rows in the database:

```php
$rsm = new ResultSetMappingBuilder($entityManager);
$rsm->addRootEntityFromClassMetadata(Sale::class, 's');

$sql = 'SELECT DISTINCT ON (s.customer_id) '.$rsm->generateSelectClause()
    .' FROM sale s ORDER BY s.customer_id, s.placed_at DESC';

$latestSales = $entityManager->createNativeQuery($sql, $rsm)->getResult();
```

An upsert through DBAL:

```php
$entityManager->getConnection()->executeStatement(
    'INSERT INTO product_view (product_id, views) VALUES (:id, 1)
     ON CONFLICT (product_id) DO UPDATE SET views = product_view.views + 1',
    ['id' => $product->getId()],
);
```

## Gotchas

- **`Expected =, <, <=, <>, >, >=, !=, got 'ORDER'`** (or `got end of string.`): a boolean function stands alone in `WHERE` or `HAVING`. Add `= TRUE`, see [Boolean functions need a comparison](#boolean-functions-need-a-comparison).
- **`Expected =, <, <=, <>, >, >=, !=, got 'ILIKE'`**, or `got '@'`: PostgreSQL operator syntax in DQL. Use the function, such as `ILIKE(s.reference, :q) = TRUE`.
- **`Expected Doctrine\ORM\Query\TokenType::T_SELECT, got 'p'`** (ORM 2: `Doctrine\ORM\Query\Lexer::T_SELECT`) for `:tag = ANY(p.tags)`: `ANY` is DQL's subquery keyword. Use `:tag = ANY_OF(p.tags)` or `IN_ARRAY(:tag, p.tags) = TRUE`.
- **`Expected Doctrine\ORM\Query\TokenType::T_FROM, got '('`** after `COUNT(s.id) FILTER` or `SUM(s.amount) OVER`: an SQL clause after the closing parenthesis. Wrap the call, see [After the closing parenthesis](#after-the-closing-parenthesis).
- **`Expected StateFieldPathExpression | string | InputParameter | FunctionsReturningStrings | AggregateExpression, got '"'`**, `got '20'` or `got 'NULL'`: a double-quoted string, a number or `NULL` where a string primary goes. Single-quote strings and numbers; bind `null` as a parameter.
- **`operator does not exist: geometry @> geometry`**: `CONTAINS` on geometries. Use `SPATIAL_CONTAINS` or `ST_CONTAINS`.
- **`operator does not exist: record = boolean`**: a PHP array bound without a type expanded into a list. Pass the type name, `setParameter('tags', $tags, 'text[]')`.
- **`syntax error at or near "."`** from `EXTRACT(s0_.placed_at FROM 'month')`: `DATE_EXTRACT` with its arguments swapped. The field comes first: `DATE_EXTRACT('month', s.placedAt)`.
- **`Invalid boolean value "yes" provided for ST_Distance. Must be "true" or "false".`** or **`The boolean parameter for ST_Distance must be a string literal, got Doctrine\ORM\Query\AST\InputParameter`**: write the boolean as the literal `'true'` or `'false'`.
- **A `catch (QueryException $e)` misses `date_trunc() requires at least 2 arguments`**: argument-count errors throw `InvalidArgumentForVariadicFunctionException`, an `\InvalidArgumentException`, and wrapper errors throw `ParserException`, a `\RuntimeException`. Catch those too.

## Reference

- [Available functions and operators](AVAILABLE-FUNCTIONS-AND-OPERATORS.md): every DQL name, by category
- [Window functions](WINDOW-FUNCTIONS.md): the window specification, frames, and the ranking and value functions
- [Mathematical and statistical functions](MATHEMATICAL-FUNCTIONS.md#within-group-goes-inside-the-parentheses-in-dql): the ordered-set aggregates
- [Array and JSON functions and operators](ARRAY-AND-JSON-FUNCTIONS.md): the array and JSON operators and aggregates
- [Doctrine setup](INTEGRATING-WITH-DOCTRINE.md) · [Symfony setup](INTEGRATING-WITH-SYMFONY.md) · [Laravel setup](INTEGRATING-WITH-LARAVEL.md): the registered DQL names
