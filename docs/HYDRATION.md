# Query results

This page tells you which PHP value you get back from a query, and what to do when it is a string you did not expect.

> **See also:** [Available types](AVAILABLE-TYPES.md) · [Infinity values](INFINITY.md) · [Window functions](WINDOW-FUNCTIONS.md)

## In short

Doctrine converts a value only when it knows its type.

- **A mapped field** such as `p.tags` is converted by [its DBAL type](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/basic-mapping.html#doctrine-mapping-types). A `text[]` column arrives as a PHP array.
- **Anything the query computes** - a function, an aggregate, arithmetic - is not converted. Doctrine does not know what type it returns, so you get the value exactly as the PostgreSQL driver hands it over, usually a string.

```dql
SELECT p.tags, ARRAY_APPEND(p.tags, 'new') AS extended FROM App\Entity\Product p WHERE p.id = 1
```

```text
['tags' => ['php', 'postgres'], 'extended' => '{php,postgres,new}']
```

Two things to know on top of that:

1. **`getScalarResult()`, `getSingleColumnResult()` and `getSingleScalarResult()` convert nothing**, not even a mapped field: `p.tags` arrives as `'{php,postgres}'` and `p.price` as `'42.00'`. Use `getResult()`, `getSingleResult()` or `getOneOrNullResult()` when you want mapped fields converted.
2. **The driver converts a few types by itself**, even for computed values: `boolean` becomes `bool`; `smallint`, `integer` and `bigint` become `int`; `real` and `double precision` become `float` [from PHP 8.4](https://www.php.net/manual/en/migration84.other-changes.php#migration84.other-changes.functions.pdo-pgsql) (numeric strings before).

The values on this page were measured on PostgreSQL 18 and PostGIS 3.6 with PHP 8.5, DBAL 4.4 and ORM 3.7, with the session time zone set to `UTC`.

## Converting a computed value yourself

If a computed value has the same PostgreSQL type as one of your mapped columns, pass it through that DBAL type:

```dql
SELECT ARRAY_APPEND(p.tags, 'new') AS tags FROM App\Entity\Product p WHERE p.id = 1
```

```php
$row = $query->getSingleResult();
$tags = $entityManager->getConnection()->convertToPHPValue($row['tags'], 'text[]'); // ['php', 'postgres', 'new']
```

The same works with `'tstzmultirange'` for a `RANGE_AGG(…)` result, `'jsonb'` for a `JSONB_BUILD_OBJECT(…)` result and `'interval'` for an `AGE(…)` result.

For an array type that has no DBAL type, use the parser the array types use:

```php
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray('{1,2,3}');                             // [1, 2, 3]
PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray('{1,2,3}', preserveStringTypes: true);  // ['1', '2', '3']
```

Pass `preserveStringTypes: true` for text arrays; without it, `{php,1.0,true}` becomes `['php', 1.0, true]`. The parser handles one-dimensional arrays only.

For a geometry, convert inside the query: wrap it in `ST_ASTEXT` or `ST_ASGEOJSON`. The raw value is [EWKB hex](https://postgis.net/docs/using_postgis_dbmanagement.html#EWKB_EWKT).

## What a computed value arrives as

The PHP value depends only on the PostgreSQL type the expression returns:

| PostgreSQL result type | PHP value | Examples |
|---|---|---|
| `smallint`, `integer`, `bigint` | `int` | `COUNT(s.id)` → `3`, `OVER(ROW_NUMBER(), ORDER BY s.amount DESC)` → `1`, `SUM(c.points)` → `150` |
| `boolean` | `bool` | `CONTAINS(p.tags, ARRAY('php'))` → `true` |
| `real`, `double precision` | `float` (a numeric string before PHP 8.4) | `SQRT(c.points)` → `10.954451150103322`, `DATE_PART('year', s.placedAt)` → `2026.0` |
| [`numeric`](https://www.postgresql.org/docs/18/datatype-numeric.html#DATATYPE-NUMERIC-DECIMAL) | `string` | `SUM(s.amount)` → `'76.99'`, `DATE_EXTRACT('year', s.placedAt)` → `'2026'` |
| `bytea` | [a stream `resource`](https://www.php.net/manual/en/ref.pdo-pgsql.php#ref.pdo-pgsql.general-notes) | `DECODE('0102', 'hex')` |
| anything else | `string`, in PostgreSQL's text form | see below |

"Anything else" covers [arrays](https://www.postgresql.org/docs/18/arrays.html#ARRAYS-IO), [ranges](https://www.postgresql.org/docs/18/rangetypes.html#RANGETYPES-IO), JSON, [timestamps](https://www.postgresql.org/docs/18/datatype-datetime.html#DATATYPE-DATETIME-OUTPUT), [intervals](https://www.postgresql.org/docs/18/datatype-datetime.html#DATATYPE-INTERVAL-OUTPUT), `ltree` and geometry:

| Expression | PHP value |
|---|---|
| `ARRAY_AGG(p.id)` | `'{1,2,3}'` |
| `JSON_GET_FIELD(p.attributes, 'color')` | `'"red"'`, with the quotes, because `->` returns `jsonb` |
| `JSON_GET_FIELD_AS_TEXT(p.attributes, 'color')` | `'red'` |
| `JSONB_BUILD_OBJECT('name', p.name)` | `'{"name": "Dune"}'` |
| `DATE_TRUNC('day', s.placedAt)` | `'2026-09-26 00:00:00+00'` |
| `AGE(s.placedAt, '2026-01-01')` | `'8 mons 25 days 10:30:00'` |
| `SUBPATH(p.category, 0, 1)` | `'books'` |
| `ST_CENTROID(st.location)` | `'0101000020E6100000…'` (EWKB hex) |
| `ST_ASTEXT(st.location)` | `'POINT(23.32 42.69)'` |

A few results that surprise people:

- [`AVG` of an integer column is `numeric`](https://www.postgresql.org/docs/18/functions-aggregate.html#FUNCTIONS-AGGREGATE-TABLE), so `AVG(c.points)` returns `'75.0000000000000000'`. Round it in the query: `ROUND(AVG(c.points), 2)` returns `'75.00'`.
- [`ARRAY_AGG` over no rows returns `null`](https://www.postgresql.org/docs/18/functions-aggregate.html#FUNCTIONS-AGGREGATE-TABLE), not `'{}'`.
- Floats carry [the usual binary rounding](https://www.postgresql.org/docs/18/datatype-numeric.html#DATATYPE-FLOAT): `CAST('0.1' AS FLOAT8) + CAST('0.2' AS FLOAT8)` returns `0.30000000000000004`. [Compare floats with a tolerance](https://www.php.net/manual/en/language.types.float.php#language.types.float.comparison), or compute in `numeric` when you need exact decimals.

### An entity and a computed value in one row

Selecting both gives [a mixed row](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#pure-and-mixed-results): the entity under key `0` and each computed value under its alias.

```dql
SELECT s, OVER(SUM(s.amount), PARTITION BY s.customer ORDER BY s.placedAt) AS runningTotal FROM App\Entity\Sale s ORDER BY s.id
```

```text
[0 => Sale {id: 1, …}, 'runningTotal' => '42.00']
[0 => Sale {id: 2, …}, 'runningTotal' => '51.99']
```

### DTOs with SELECT NEW

[`SELECT NEW`](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html#new-operator-syntax) follows the same rule: a mapped field arrives converted, a computed value arrives as the driver returns it. Type each constructor parameter by what it receives:

```dql
SELECT NEW App\Dto\CustomerTotal(c.name, SUM(s.amount)) FROM App\Entity\Sale s JOIN s.customer c GROUP BY c.id, c.name
```

```php
final class CustomerTotal
{
    public function __construct(
        public readonly string $name,
        public readonly string $total, // SUM of a numeric column arrives as '51.99', not as a float
    ) {}
}
```

## What a mapped column arrives as

What an entity property holds, and what a mapped field holds in a `getResult()` or `getArrayResult()` row. [Available types](AVAILABLE-TYPES.md) lists the DBAL type for each column type.

| Column type | PHP value | Good to know |
|---|---|---|
| `text[]`, `varchar[]`, `citext[]` | `list<?string>` | Items stay strings: `{php,1.0,true}` reads as `['php', '1.0', 'true']` |
| `smallint[]`, `integer[]`, `bigint[]` | `list<?int>` | |
| `real[]`, `double precision[]` | `list<?float>` | `Infinity` and `NaN` become `INF` and `NAN` ([Infinity values](INFINITY.md)) |
| `numeric[]` | `list<?string>` | Keeps the scale: `{1.50,NaN}` reads as `['1.50', 'NaN']` |
| `boolean[]` | `list<?bool>` | |
| `uuid[]`, `inet[]`, `cidr[]`, `macaddr[]` | `list<?string>` | |
| `date[]`, `timestamp[]`, `timestamptz[]` | a `list` of `\DateTimeImmutable`, `DateTimeInfinity` or `null` | `infinity` reads as `DateTimeInfinity::POSITIVE` |
| `interval[]` | `list<?Interval>` | |
| `jsonb[]`, `json[]` | a `list` of [jsonb](#jsonb) values | |
| `ltree[]` | `list<?Ltree>` | |
| `hstore[]` | `list<array<string, ?string>>` | |
| `int4range[]` and the other range arrays | `list<?Int4Range>`, … | |
| an enum array | `list<?YourEnum>` | See [PostgreSQL enum types](ENUM-TYPE.md) |
| `geometry[]`, `geography[]` | `list<?WktSpatialData>` | |
| `int4range`, `int8range` | `Int4Range`, `Int8Range` | |
| `numrange` | `NumericRange` | Bounds are PHP `int` or `float` |
| `daterange`, `tsrange`, `tstzrange` | `DateRange`, `TsRange`, `TstzRange` | |
| `int4multirange` and the other multiranges | `Int4Multirange`, … | See [PostgreSQL range types](RANGE-TYPES.md) |
| `interval` | `Interval`, not `\DateInterval` | `toDateInterval()` gives you a `\DateInterval` |
| `jsonb` | `array`, `int`, `float`, `string`, `bool` or `null` | See [jsonb](#jsonb) |
| `geometry`, `geography` | `WktSpatialData` | `SRID=4326;POINT(23.32 42.69)`; [a `geography` value always has an SRID](https://postgis.net/docs/using_postgis_dbmanagement.html#Create_Geography_Tables) |
| a user-defined enum | the enum case | |
| a user-defined composite | see [PostgreSQL composite types](COMPOSITE-TYPE.md) | |
| `ltree` | `Ltree` | `lquery` and `ltxtquery` read as strings |
| `hstore` | `array<string, ?string>` | |
| `vector`, `halfvec` | `list<float>` | |
| `sparsevec`, `cube` | `Sparsevec`, `Cube` | |
| `point`, `box`, `circle`, `line`, `lseg`, `path`, `polygon` | the value object of the same name | |
| `bytea` | `string` (binary), not a stream | |
| `money` | `string` | [Formatted by the `lc_monetary` setting](https://www.postgresql.org/docs/18/datatype-money.html#DATATYPE-MONEY): `'$1,234.56'` |
| `citext`, `inet`, `cidr`, `macaddr`, `macaddr8`, `tsvector`, `tsquery`, `xml`, `bit`, `bit varying`, `timetz` | `string` | |
| `numeric` | `string` | [Doctrine's `decimal` type](https://www.doctrine-project.org/projects/doctrine-dbal/en/current/reference/types.html#decimal), not this library: `'42.00'` |

A `NULL` array item reads as `null`.

### jsonb

A `jsonb` column returns whatever JSON it holds - not always an array. If a row can hold a plain number or string, type the property `mixed` (or `array|int|float|string|bool|null`), or PHP throws `Cannot assign int to property … of type ?array`.

| Stored JSON | PHP value |
|---|---|
| `{"color": "red", "size": 42}` | `['size' => 42, 'color' => 'red']`, an array, never an object ([PostgreSQL reorders the keys](https://www.postgresql.org/docs/18/datatype-json.html#DATATYPE-JSON)) |
| `[1, 2]` | `[1, 2]` |
| `42` | `42` |
| `1.5`, `1.0` | `1.5`, `1.0` |
| `"x"` | `'x'` |
| `true` | `true` |
| `null` | `null`, the same as SQL `NULL` |
| `9223372036854775808` and larger | `'9223372036854775808'`, a string, because it does not fit in an `int` |

### Values that read back differently

What you save is not always what you load after `$entityManager->clear()`:

- `text[]` stores non-strings as text: `[1, true, 1.5]` reads back as `['1', 'true', '1.5']`.
- `jsonb` drops a zero fraction: `['weight' => 1.0]` reads back as `['weight' => 1]`.
- `int4range` and `int8range` [are normalised by PostgreSQL](https://www.postgresql.org/docs/18/rangetypes.html#RANGETYPES-DISCRETE): `[1,5]` reads back as `[1,6)`.
- `numrange` bounds lose trailing zeros: `[1.50,9.99)` reads back as `[1.5,9.99)`. Select `CAST(… AS TEXT)` when the digits matter.
- `tstzrange` reads back in the session time zone: the same instants, with a different offset.
- `box` [is stored upper-right corner first](https://www.postgresql.org/docs/18/datatype-geometric.html#DATATYPE-GEOMETRIC-BOXES): `(1,2),(3,4)` reads back as `(3,4),(1,2)`.
- An empty `bytea`, `bit varying` or `xml` value reads back as `null`, the same as SQL `NULL`. An empty `hstore` reads back as `[]` and an empty `ltree` as an `Ltree` with no labels.

## Time zone and interval style

Dates and intervals that arrive as strings are formatted by the connection's session settings, so the same query can return different text on two servers. [`TimeZone`](https://www.postgresql.org/docs/18/runtime-config-client.html#GUC-TIMEZONE) changes every `timestamptz` string, and [`IntervalStyle`](https://www.postgresql.org/docs/18/runtime-config-client.html#GUC-INTERVALSTYLE) changes every `interval` string. With `TimeZone = 'Europe/Sofia'` and `IntervalStyle = 'iso_8601'`, `AGE(s.placedAt, '2026-01-01')` returns `'P8M25DT13H30M'` instead of `'8 mons 25 days 10:30:00'` - a different answer, not just a different format, because the day starts at a different instant.

If you read these strings, set the time zone when you connect (`SET TIME ZONE 'UTC'`). Mapped date and range fields are safe: they keep their offset, so they compare correctly under any zone. The `Interval` type reads every `IntervalStyle`, so `convertToPHPValue($row['age'], 'interval')` works on any server.

## Errors

- **`getSingleScalarResult()` returns `'{php,postgres}'` for `p.tags`.** The scalar result methods skip the DBAL type. Use `getSingleResult()` and take the column from the row.
- **`Cannot assign int to property App\Entity\Product::$attributes of type ?array`.** That row's `jsonb` value is a plain number. Type the property `mixed`; see [jsonb](#jsonb).
- **`Type "jsonb" already exists`, or a big `jsonb` integer arrives as a `float`.** DBAL 4.3+ [ships its own `jsonb` type](https://www.doctrine-project.org/projects/doctrine-dbal/en/current/reference/types.html#jsonb). Register this library's type with `Type::overrideType('jsonb', Jsonb::class)`.
