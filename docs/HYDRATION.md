# What comes back: hydration

This page answers "what PHP value do I get?" for a mapped column and for a value a DQL query computes. Read it when a total arrives as a string, an array arrives as `'{a,b}'`, or a `jsonb` property typed `array` receives an `int`.

> **See also:** [Available Types](AVAILABLE-TYPES.md) · [Infinity Values](INFINITY.md) · [Window Functions](WINDOW-FUNCTIONS.md)

## The rule

A **field path** goes through [its DBAL type](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/basic-mapping.html#doctrine-mapping-types); **anything else** is whatever `pdo_pgsql` returns.

`p.tags` is a field path, so the `text[]` DBAL type converts it in a hydrated entity, in `getArrayResult()`, in a scalar `SELECT` read with `getResult()`, `getSingleResult()` or `getOneOrNullResult()`, and as an argument of `SELECT NEW`. Wrap the same field in a function and the conversion is gone, because Doctrine does not know what type the function returns:

```dql
SELECT p.tags, ARRAY_APPEND(p.tags, 'new') AS extended FROM App\Entity\Product p WHERE p.id = 1
```

```text
['tags' => ['php', 'postgres'], 'extended' => '{php,postgres,new}']
```

The scalar result methods are the exception. `getScalarResult()`, `getSingleColumnResult()` and `getSingleScalarResult()` return a selected field path as the database sent it - `'{php,postgres}'` for `p.tags`, `'42.00'` for `p.price`, `'2026-09-26 10:30:00+00'` for `s.placedAt`. Only the fields of a selected entity (`SELECT p`) are still converted by `getScalarResult()`.

The driver types only three families itself: `boolean` arrives as `bool`, `smallint`, `integer` and `bigint` as `int`, [`real` and `double precision` as `float`](https://www.php.net/manual/en/migration84.other-changes.php#migration84.other-changes.functions.pdo-pgsql) from PHP 8.4 (as numeric strings before). Every other PostgreSQL type - `numeric`, [timestamps](https://www.postgresql.org/docs/18/datatype-datetime.html#DATATYPE-DATETIME-OUTPUT), [intervals](https://www.postgresql.org/docs/18/datatype-datetime.html#DATATYPE-INTERVAL-OUTPUT), [arrays](https://www.postgresql.org/docs/18/arrays.html#ARRAYS-IO), [ranges](https://www.postgresql.org/docs/18/rangetypes.html#RANGETYPES-IO), JSON, `ltree`, `hstore`, geometry - arrives as its PostgreSQL text form.

Every value on this page was measured against PostgreSQL 18 and PostGIS 3.6 with PHP 8.5, DBAL 4.4 and ORM 3.7, with the session time zone set to `UTC`.

## Mapped columns by family

What an entity property, a `getArrayResult()` row or a field path read with `getResult()` holds. The family pages carry the details; this table only says what you get.

| Column type | DBAL type | PHP value | Notes |
|---|---|---|---|
| `text[]`, `varchar[]`, `citext[]` | `TextArray`, `VarcharArray`, `CitextArray` | `list<string\|null>` | Items stay strings: `{php,1.0,true}` reads as `['php', '1.0', 'true']` |
| `smallint[]`, `integer[]`, `bigint[]` | `SmallIntArray`, `IntegerArray`, `BigIntArray` | `list<int\|null>` | |
| `real[]`, `double precision[]` | `RealArray`, `DoublePrecisionArray` | `list<float\|null>` | `Infinity` and `NaN` become `INF` and `NAN` ([Infinity Values](INFINITY.md)) |
| `numeric[]` | `NumericArray` | `list<string\|null>` | `{1.50,NaN}` reads as `['1.50', 'NaN']`; the scale is kept ([Available Types](AVAILABLE-TYPES.md#numeric-array-type)) |
| `boolean[]` | `BooleanArray` | `list<bool\|null>` | |
| `uuid[]`, `inet[]`, `cidr[]`, `macaddr[]` | `UuidArray`, `InetArray`, … | `list<string\|null>` | ([Available Types](AVAILABLE-TYPES.md#uuid-array-type)) |
| `date[]`, `timestamp[]`, `timestamptz[]` | `DateArray`, `TimestampArray`, `TimestampTzArray` | `list<\DateTimeImmutable\|DateTimeInfinity\|null>` | `infinity` reads as `DateTimeInfinity::POSITIVE` ([Available Types](AVAILABLE-TYPES.md#datetime-array-types)) |
| `interval[]` | `IntervalArray` | `list<Interval\|null>` | |
| `jsonb[]`, `json[]` | `JsonbArray`, `JsonArray` | `list<array\|int\|float\|string\|bool\|null>` | JSON `null` and SQL `NULL` both read as `null` |
| `ltree[]` | `LtreeArray` | `list<Ltree\|null>` | |
| `hstore[]` | `HstoreArray` | `list<array<string, string\|null>>` | |
| `int4range[]` and the other range arrays | `Int4RangeArray`, … | `list<Int4Range\|null>`, … | |
| an enum array | your `EnumArray` subclass | `list<YourEnum\|null>` | ([PostgreSQL Enum Types](ENUM-TYPE.md)) |
| `geometry[]`, `geography[]` | `GeometryArray`, `GeographyArray` | none - reading throws | See [Gotchas](#gotchas) |
| `int4range`, `int8range` | `Int4Range`, `Int8Range` | `Int4Range`, `Int8Range` | [PostgreSQL canonicalises](https://www.postgresql.org/docs/18/rangetypes.html#RANGETYPES-DISCRETE) `[1,5]` to `[1,6)` |
| `numrange` | `NumRange` | `NumericRange` | Bounds are PHP `int`/`float`: `[1.50,9.99)` reads as `[1.5,9.99)` |
| `daterange`, `tsrange`, `tstzrange` | `DateRange`, `TsRange`, `TstzRange` | `DateRange`, `TsRange`, `TstzRange` | `tstzrange` bounds carry [the session time zone](https://www.postgresql.org/docs/18/datatype-datetime.html#DATATYPE-TIMEZONES) |
| `int4multirange` and the other multiranges | `Int4Multirange`, … | `Int4Multirange`, … | ([PostgreSQL Range Types](RANGE-TYPES.md)) |
| `interval` | `Interval` | `Interval` value object, not `\DateInterval` | `toDateInterval()` gives you a `\DateInterval` |
| `jsonb` | `Jsonb` | `array\|int\|float\|string\|bool\|null` | See [jsonb values](#jsonb-values) |
| `geometry`, `geography` | `Geometry`, `Geography` | `WktSpatialData` | `SRID=4326;POINT(23.32 42.69)`; [a `geography` value always carries its SRID](https://postgis.net/docs/using_postgis_dbmanagement.html#Create_Geography_Tables) |
| a user-defined enum | your `Enum` subclass | the enum case | |
| a user-defined composite | your `Composite` subclass | see [PostgreSQL Composite Types](COMPOSITE-TYPE.md) | |
| `ltree` | `Ltree` | `Ltree` value object | `lquery` and `ltxtquery` read as strings |
| `hstore` | `Hstore` | `array<string, string\|null>` | |
| `vector`, `halfvec` | `Vector`, `Halfvec` | `list<float>` | |
| `sparsevec` | `Sparsevec` | `Sparsevec` value object | |
| `cube` | `Cube` | `Cube` value object | |
| `point`, `box`, `circle`, `line`, `lseg`, `path`, `polygon` | `Point`, `Box`, … | the value object of the same name | [PostgreSQL stores a box upper-right first](https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-GEOMETRIC-BOXES): `(1,2),(3,4)` reads as `(3,4),(1,2)` |
| `bytea` | `Bytea` | `string` (binary) | Not a stream |
| `money` | `Money` | `string` | [Formatted by the `lc_monetary` setting](https://www.postgresql.org/docs/18/datatype-money.html#DATATYPE-MONEY): `'$1,234.56'` ([Available Types](AVAILABLE-TYPES.md#money-type)) |
| `citext`, `inet`, `cidr`, `macaddr`, `macaddr8`, `tsvector`, `tsquery`, `xml`, `bit`, `bit varying`, `timetz` | `Citext`, `Inet`, … | `string` | |
| `numeric` | [Doctrine's `decimal`](https://www.doctrine-project.org/projects/doctrine-dbal/en/current/reference/types.html#decimal) | `string` | Doctrine's choice, not this library's: `'42.00'` |

A `NULL` element reads as `null` in every array type listed.

### jsonb values

`Jsonb` returns whatever the stored JSON is, so a property typed `array` breaks on the first row that holds a scalar. Type the property `mixed`, or `array|int|float|string|bool|null`, unless every row is guaranteed to be an object or a list.

| Stored JSON | PHP value |
|---|---|
| `{"color": "red", "size": 42}` | `['size' => 42, 'color' => 'red']` - an associative array, never an object; [PostgreSQL reorders the keys](https://www.postgresql.org/docs/18/datatype-json.html#DATATYPE-JSON) |
| `[1, 2]` | `[1, 2]` |
| `42` | `42` (`int`) |
| `1.5`, `1.0` | `1.5`, `1.0` (`float`) |
| `"x"` | `'x'` |
| `true` | `true` |
| `null` | `null`, the same as a SQL `NULL` |
| `9223372036854775807` | `9223372036854775807` (`int`, `PHP_INT_MAX`) |
| `9223372036854775808` and larger | `'9223372036854775808'` (`string`) |

### Empty strings

Most column types reject `''` outright, so an empty string can only come back from the few that accept it:

| Column type | Stored as | PHP value |
|---|---|---|
| `citext`, `tsvector`, `tsquery` | `''` | `''` |
| `ltree` | `''` | an `Ltree` with no labels |
| `hstore` | `''` | `[]` |
| `money` | `$0.00` | `'$0.00'` |
| `bytea` | [`\x`](https://www.postgresql.org/docs/18/datatype-binary.html#DATATYPE-BINARY-BYTEA-HEX-FORMAT) (no bytes) | `null` |
| `bit varying` | `''` | `null` |
| `xml` | `''` | `null` |

An empty `bytea`, `bit varying` or `xml` value cannot be told apart from `NULL` after hydration.

### Values that change on the way back

What you write is not always what you read after `$entityManager->clear()`:

- `text[]` writes non-strings as their text: `[1, true, 1.5]` reads back as `['1', 'true', '1.5']`.
- `jsonb` drops a zero fraction on write: `['weight' => 1.0]` reads back as `['weight' => 1]`.
- `tstzrange` reads back in the session time zone: a `TstzRange` written with `+03:00` bounds comes back as the same instants in `+00:00` under a `UTC` session.
- `int4range` and `numrange` change as described in the table above.

## DQL scalar results

A value computed by the query - an aggregate, a window function, any function of this library - is not converted by any DBAL type. These are the values the book's queries return:

> **Since 4.9:** `OVER`, `FILTER` and `WITHIN GROUP` are new; see [Window Functions](WINDOW-FUNCTIONS.md).

| Expression | PostgreSQL type | PHP value |
|---|---|---|
| [`COUNT(s.id)`](https://www.postgresql.org/docs/18/functions-aggregate.html#FUNCTIONS-AGGREGATE-TABLE), `FILTER(COUNT(s.id), WHERE s.status = 'paid')`, [`LENGTH(p.name)`](https://www.postgresql.org/docs/18/functions-string.html#FUNCTIONS-STRING-OTHER) | `bigint`, `integer` | `3` (`int`) |
| [`OVER(ROW_NUMBER(), ORDER BY s.amount DESC)`](https://www.postgresql.org/docs/18/functions-window.html#FUNCTIONS-WINDOW-TABLE), [`OVER(RANK(), …)`](https://www.postgresql.org/docs/18/functions-window.html#FUNCTIONS-WINDOW-TABLE) | `bigint` | `1` (`int`) |
| [`SUM(c.points)`](https://www.postgresql.org/docs/18/functions-aggregate.html#FUNCTIONS-AGGREGATE-TABLE) on an `integer` column | `bigint` | `150` (`int`) |
| [`SUM(s.amount)`](https://www.postgresql.org/docs/18/functions-aggregate.html#FUNCTIONS-AGGREGATE-TABLE), [`MAX(s.amount)`](https://www.postgresql.org/docs/18/functions-aggregate.html#FUNCTIONS-AGGREGATE-TABLE), [`s.amount * 2`](https://www.postgresql.org/docs/18/functions-math.html#FUNCTIONS-MATH-OP-TABLE), [`ROUND(s.amount, 1)`](https://www.postgresql.org/docs/18/functions-math.html#FUNCTIONS-MATH-FUNC-TABLE) | `numeric` | `'76.99'` (`string`) |
| [`AVG(c.points)`](https://www.postgresql.org/docs/18/functions-aggregate.html#FUNCTIONS-AGGREGATE-TABLE) on an `integer` column | `numeric` | `'75.0000000000000000'` (`string`) |
| `OVER(SUM(s.amount), PARTITION BY s.customer ORDER BY s.placedAt)` | `numeric` | `'42.00'` (`string`) |
| [`DATE_EXTRACT('year', s.placedAt)`](https://www.postgresql.org/docs/18/functions-datetime.html#FUNCTIONS-DATETIME-EXTRACT), [`CAST(c.points AS DECIMAL(10, 2))`](https://www.postgresql.org/docs/18/datatype-numeric.html#DATATYPE-NUMERIC-DECIMAL) | `numeric` | `'2026'`, `'120.00'` (`string`) |
| [`DATE_PART('year', s.placedAt)`](https://www.postgresql.org/docs/18/functions-datetime.html#FUNCTIONS-DATETIME-EXTRACT), [`SQRT(c.points)`](https://www.postgresql.org/docs/18/functions-math.html#FUNCTIONS-MATH-FUNC-TABLE), [`PERCENTILE_CONT(0.5 WITHIN GROUP ORDER BY s.amount)`](https://www.postgresql.org/docs/18/functions-aggregate.html#FUNCTIONS-ORDEREDSET-TABLE) | `double precision` | `2026.0`, `10.954451150103322`, `25.0` (`float`) |
| [`OVER(PERCENT_RANK(), …)`](https://www.postgresql.org/docs/18/functions-window.html#FUNCTIONS-WINDOW-TABLE), [`ST_DISTANCE(st.location, …)`](https://postgis.net/docs/ST_Distance.html), [`COSINE_DISTANCE(a.embedding, …)`](https://github.com/pgvector/pgvector#user-content-vector-functions), [`TS_RANK(a.search, …)`](https://www.postgresql.org/docs/18/textsearch-controls.html#TEXTSEARCH-RANKING) | `double precision`, `real` | `float` |
| [`CONTAINS(p.tags, ARRAY('php'))`](https://www.postgresql.org/docs/18/functions-array.html#ARRAY-OPERATORS-TABLE), [`IN_ARRAY(:tag, p.tags)`](https://www.postgresql.org/docs/18/functions-comparisons.html#FUNCTIONS-COMPARISONS-ANY-SOME), [`ST_DWITHIN(st.location, …, 200000)`](https://postgis.net/docs/ST_DWithin.html) | `boolean` | `true` (`bool`) |
| [`ARRAY_AGG(p.name)`](https://www.postgresql.org/docs/18/functions-aggregate.html#FUNCTIONS-AGGREGATE-TABLE), [`ARRAY_APPEND(p.tags, 'new')`](https://www.postgresql.org/docs/18/functions-array.html#ARRAY-FUNCTIONS-TABLE) | `text[]` | `'{"The Pragmatic Programmer",Dune,"Gift card"}'` (`string`) |
| `ARRAY_AGG(p.id)` | `integer[]` | `'{1,2,3}'` (`string`) |
| [`RANGE_AGG(b.slot)`](https://www.postgresql.org/docs/18/functions-aggregate.html#FUNCTIONS-AGGREGATE-TABLE) | `tstzmultirange` | `'{["2026-09-26 10:00:00+00","2026-09-26 11:00:00+00")}'` (`string`) |
| [`JSON_GET_FIELD(p.attributes, 'color')`](https://www.postgresql.org/docs/18/functions-json.html#FUNCTIONS-JSON-OP-TABLE) | `jsonb` | `'"red"'` (`string`, JSON-encoded) |
| [`JSON_GET_FIELD_AS_TEXT(p.attributes, 'color')`](https://www.postgresql.org/docs/18/functions-json.html#FUNCTIONS-JSON-OP-TABLE) | `text` | `'red'` (`string`) |
| `JSON_GET_FIELD_AS_INTEGER(p.attributes, 'size')` | `bigint` | `42` (`int`) |
| [`JSONB_BUILD_OBJECT('name', p.name)`](https://www.postgresql.org/docs/18/functions-json.html#FUNCTIONS-JSON-CREATION-TABLE), [`JSONB_AGG(p.name)`](https://www.postgresql.org/docs/18/functions-aggregate.html#FUNCTIONS-AGGREGATE-TABLE) | `jsonb` | `'{"name": "Dune"}'` (`string`) |
| [`SUBPATH(p.category, 0, 1)`](https://www.postgresql.org/docs/18/ltree.html#LTREE-FUNC-TABLE) | `ltree` | `'books'` (`string`) |
| [`ST_CENTROID(st.location)`](https://postgis.net/docs/ST_Centroid.html) | `geography` | `'0101000020E6100000…'` ([EWKB hex](https://postgis.net/docs/using_postgis_dbmanagement.html#EWKB_EWKT) `string`) |
| [`ST_ASTEXT(st.location)`](https://postgis.net/docs/ST_AsText.html), [`ST_ASGEOJSON(st.location)`](https://postgis.net/docs/ST_AsGeoJSON.html) | `text` | `'POINT(23.32 42.69)'`, `'{"type":"Point","coordinates":[23.32,42.69]}'` |
| `MAX(s.placedAt)`, [`DATE_TRUNC('day', s.placedAt)`](https://www.postgresql.org/docs/18/functions-datetime.html#FUNCTIONS-DATETIME-TRUNC) | `timestamptz` | `'2026-09-26 00:00:00+00'` (`string`) |
| [`AGE(s.placedAt, '2026-01-01')`](https://www.postgresql.org/docs/18/functions-datetime.html#FUNCTIONS-DATETIME-TABLE) | `interval` | `'8 mons 25 days 10:30:00'` (`string`) |
| [`TO_TSVECTOR('english', a.body)`](https://www.postgresql.org/docs/18/functions-textsearch.html#TEXTSEARCH-FUNCTIONS-TABLE) | `tsvector` | `"'cat':3 'fat':2 'sat':4"` (`string`) |
| [`DECODE('0102', 'hex')`](https://www.postgresql.org/docs/18/functions-binarystring.html#FUNCTION-DECODE) | `bytea` | [a stream `resource`](https://www.php.net/manual/en/ref.pdo-pgsql.php#ref.pdo-pgsql.general-notes) |

> **Note:** before PHP 8.4, pdo_pgsql returns `double precision` and `real` as numeric strings, so the `float` rows above arrive as strings.

[`ARRAY_AGG` over no rows returns `null`](https://www.postgresql.org/docs/18/functions-aggregate.html#FUNCTIONS-AGGREGATE-TABLE), not `'{}'`.

### Turn a string back into the mapped value

When a computed value has the same PostgreSQL type as one of your mapped columns, convert it with the DBAL type yourself. `Connection::convertToPHPValue()` takes the DBAL type name:

```dql
SELECT ARRAY_APPEND(p.tags, 'new') AS tags FROM App\Entity\Product p WHERE p.id = 1
```

```php
$row = $query->getSingleResult();
$tags = $entityManager->getConnection()->convertToPHPValue($row['tags'], 'text[]'); // ['php', 'postgres', 'new']
```

The same call turns a `RANGE_AGG(b.slot)` string into a `TstzMultirange` with `'tstzmultirange'`, a `JSONB_BUILD_OBJECT(…)` string into an array with `'jsonb'`, and an `AGE(…)` string into an `Interval` with `'interval'`.

For an array whose element type has no DBAL type of its own, the parser the array types use is public:

```php
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray('{1,2,3}');                             // [1, 2, 3]
PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray('{1,2,3}', preserveStringTypes: true);  // ['1', '2', '3']
```

Without `preserveStringTypes` the parser guesses each item's type, so `{php,1.0,true}` becomes `['php', 1.0, true]`. Pass `true` for text arrays. It handles one-dimensional arrays only, and returns `[]` for `'{}'`.

For a geometry, convert inside the query instead: wrap the function in `ST_ASTEXT` or `ST_ASGEOJSON`, or compare with [`ST_EQUALS`](https://postgis.net/docs/ST_Equals.html) and read the boolean. The raw value is EWKB hex.

### Floating-point results

From PHP 8.4, `double precision` and `real` results are PHP floats with [the usual binary rounding](https://www.postgresql.org/docs/18/datatype-numeric.html#DATATYPE-FLOAT): `CAST('0.1' AS FLOAT8) + CAST('0.2' AS FLOAT8)` arrives as `0.30000000000000004`. [Compare them with a tolerance](https://www.php.net/manual/en/language.types.float.php#language.types.float.comparison), never with `===`:

```php
self::assertEqualsWithDelta(132095.96, $row['metres'], 0.01);
```

If you need exact decimals, [compute in `numeric`](https://www.postgresql.org/docs/18/datatype-numeric.html#DATATYPE-NUMERIC-DECIMAL) and keep the string.

### Entities next to scalars

Selecting an entity together with a computed value gives [a mixed row](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#pure-and-mixed-results): the entity under key `0`, each scalar under its alias.

```dql
SELECT s, OVER(SUM(s.amount), PARTITION BY s.customer ORDER BY s.placedAt) AS runningTotal FROM App\Entity\Sale s ORDER BY s.id
```

```text
[0 => Sale {id: 1, …}, 'runningTotal' => '42.00']
[0 => Sale {id: 2, …}, 'runningTotal' => '51.99']
```

### DTOs with SELECT NEW

[`SELECT NEW` passes each value to the constructor](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#new-operator-syntax) after the same rule: a field path arrives converted, a computed value arrives as the driver returns it.

```dql
SELECT NEW App\Dto\CustomerTotal(c.name, SUM(s.amount)) FROM App\Entity\Sale s JOIN s.customer c GROUP BY c.id, c.name
```

```php
final class CustomerTotal
{
    public function __construct(
        public readonly string $name,
        public readonly string $total, // numeric arrives as '51.99', not as a float
    ) {}
}
```

Type a constructor parameter that receives an aggregate, a window function or any other computed value by the table above, not by the column it was computed from.

### Session settings leak into strings

Scalar strings are formatted by the connection's session settings, so the same query returns different text on two servers. [`TimeZone`](https://www.postgresql.org/docs/18/runtime-config-client.html#GUC-TIMEZONE) changes every `timestamptz` value, and [`IntervalStyle`](https://www.postgresql.org/docs/18/runtime-config-client.html#GUC-INTERVALSTYLE) changes every `interval` string:

| Expression | `TimeZone = 'UTC'`, `IntervalStyle = 'postgres'` | `TimeZone = 'Europe/Sofia'`, `IntervalStyle = 'iso_8601'` |
|---|---|---|
| `s.placedAt` (field) | `2026-09-26 10:30:00+00:00` | `2026-09-26 13:30:00+03:00` |
| `DATE_TRUNC('day', s.placedAt)` | `'2026-09-26 00:00:00+00'` | `'2026-09-26 00:00:00+03'` |
| `b.slot` (field) | `[2026-09-26 10:00:00+00:00,2026-09-26 11:00:00+00:00)` | `[2026-09-26 13:00:00+03:00,2026-09-26 14:00:00+03:00)` |
| a mapped `interval` field | `Interval` `1 year 2 mons 3 days 04:05:06` | the same `Interval` |
| `AGE(s.placedAt, '2026-01-01')` | `'8 mons 25 days 10:30:00'` | `'P8M25DT13H30M'` |

Mapped values move too, but they carry their offset, so they still compare correctly; strings only change their text. [`DATE_TRUNC`](https://www.postgresql.org/docs/18/functions-datetime.html#FUNCTIONS-DATETIME-TRUNC) and `AGE` also change their answer, not just their format, because the day boundary is local to the session zone. Set the session zone explicitly (`SET TIME ZONE 'UTC'` on connect) if you parse these strings. The `Interval` type reads every `IntervalStyle`, so `$connection->convertToPHPValue($row['age'], 'interval')` works whichever one the server uses.

## Gotchas

- **`JSON_GET_FIELD` returns `'"red"'` with the quotes** → the `->` operator returns `jsonb`, and its text form is JSON → use `JSON_GET_FIELD_AS_TEXT` (`->>`) for text, or `JSON_GET_FIELD_AS_INTEGER` for a number.
- **A `jsonb` integer above `9223372036854775807` arrives as a `float`, or `1.0` survives a round trip** → the class registered as `jsonb` is not this library's `Jsonb`: DBAL 4.4 [registers its own `Doctrine\DBAL\Types\JsonbType`](https://www.doctrine-project.org/projects/doctrine-dbal/en/current/reference/types.html#jsonb) under that name, and `Type::addType('jsonb', …)` then fails with `Type "jsonb" already exists` → register with `Type::overrideType('jsonb', Jsonb::class)`, and check `Type::getType('jsonb')::class`.
- **Loading an entity with a `geometry[]` or `geography[]` column throws `Invalid Geometry value object format: '0101000020E6…'`** → PostgreSQL sends each element as EWKB hex and the array types read only WKT (measured on DBAL 4.4 / ORM 3.7) → read the column with a native query that selects `ARRAY(SELECT CASE WHEN ST_SRID(e) = 0 THEN ST_AsText(e) ELSE 'SRID=' || ST_SRID(e) || ';' || ST_AsText(e) END FROM unnest(col) AS e)` as a `geometry[]` scalar, and fetch it with `getResult()`.
- **`getSingleScalarResult()` returns `'{php,postgres}'` for `p.tags`** → the scalar result methods skip the DBAL type, both for a DQL field path and for a native query scalar mapped with a type → use `getSingleResult()` or `getResult()` and take the column from the row.
- **`AVG(c.points)` returns `'75.0000000000000000'`** → PostgreSQL averages integers as `numeric` → round in SQL: `ROUND(AVG(c.points), 2)` returns `'75.00'`.
- **`Cannot assign int to property App\Entity\Product::$attributes of type ?array`** → the row holds a JSON scalar, which `Jsonb` returns as a scalar → type the property `mixed`, or keep scalars out of the column.
- **A `numrange` bound loses digits** → `NumericRange` holds PHP `int`/`float` → select the column with `CAST(… AS TEXT)`, which keeps `'[1.50,9.99)'`, when the digits matter.

## Reference

- [Available Types](AVAILABLE-TYPES.md) - every DBAL type and its column type
- [PostgreSQL Range Types](RANGE-TYPES.md), [Infinity Values](INFINITY.md) - range value objects, infinite bounds
- [Spatial Types (Foundations)](SPATIAL-TYPES.md) - `WktSpatialData`
- [PostgreSQL ltree Types](LTREE-TYPE.md), [PostgreSQL Enum Types](ENUM-TYPE.md), [PostgreSQL Composite Types](COMPOSITE-TYPE.md)
- [Array and JSON Functions and Operators](ARRAY-AND-JSON-FUNCTIONS.md) - the `JSON_GET_FIELD` family
- [Window Functions](WINDOW-FUNCTIONS.md) - `OVER`, `FILTER` and their result rows
