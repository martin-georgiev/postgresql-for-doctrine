# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> Available Types

| PostgreSQL type in practical use | PostgreSQL internal system catalogue name | Implemented by |
|---|---|---|
| bytea | bytea | `MartinGeorgiev\Doctrine\DBAL\Types\Bytea` |
| bytea[] | _bytea | `MartinGeorgiev\Doctrine\DBAL\Types\ByteaArray` |
|---|---|---|
| bit | bit | `MartinGeorgiev\Doctrine\DBAL\Types\Bit` (see [note](#bit-string-types) |
| bit[] | _bit | `MartinGeorgiev\Doctrine\DBAL\Types\BitArray` |
| bit varying | varbit | `MartinGeorgiev\Doctrine\DBAL\Types\BitVarying` (see [note](#bit-string-types) |
| bit varying[] | _varbit | `MartinGeorgiev\Doctrine\DBAL\Types\BitVaryingArray` |
|---|---|---|
| bool[] | _bool | `MartinGeorgiev\Doctrine\DBAL\Types\BooleanArray` |
| smallint[] | _int2 | `MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray` |
| integer[] | _int4 | `MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray` |
| bigint[] | _int8 | `MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray` |
| real[] | _float4 | `MartinGeorgiev\Doctrine\DBAL\Types\RealArray` |
| double precision[] | _float8 | `MartinGeorgiev\Doctrine\DBAL\Types\DoublePrecisionArray` |
| numeric[] | _numeric | `MartinGeorgiev\Doctrine\DBAL\Types\NumericArray` (see [note](#numeric-array-type)) |
|---|---|---|
| date[] | _date | `MartinGeorgiev\Doctrine\DBAL\Types\DateArray` |
| interval | interval | `MartinGeorgiev\Doctrine\DBAL\Types\Interval` |
| interval[] | _interval | `MartinGeorgiev\Doctrine\DBAL\Types\IntervalArray` |
| time[] | _time | `MartinGeorgiev\Doctrine\DBAL\Types\TimeArray` |
| timestamp[] | _timestamp | `MartinGeorgiev\Doctrine\DBAL\Types\TimestampArray` |
| timestamptz[] | _timestamptz | `MartinGeorgiev\Doctrine\DBAL\Types\TimestampTzArray` |
| timetz | timetz | `MartinGeorgiev\Doctrine\DBAL\Types\Timetz` |
| timetz[] | _timetz | `MartinGeorgiev\Doctrine\DBAL\Types\TimetzArray` |
|---|---|---|
| json[] | _json | `MartinGeorgiev\Doctrine\DBAL\Types\JsonArray` |
| jsonb | jsonb | `MartinGeorgiev\Doctrine\DBAL\Types\Jsonb` |
| jsonb[] | _jsonb | `MartinGeorgiev\Doctrine\DBAL\Types\JsonbArray` |
| text[] | _text | `MartinGeorgiev\Doctrine\DBAL\Types\TextArray` |
| varchar[] | _varchar | `MartinGeorgiev\Doctrine\DBAL\Types\VarcharArray` |
| uuid[] | _uuid | `MartinGeorgiev\Doctrine\DBAL\Types\UuidArray` (see [note](#uuid-array-type)) |
| citext | citext | `MartinGeorgiev\Doctrine\DBAL\Types\Citext` (see [note](#citext-type)) |
| citext[] | _citext | `MartinGeorgiev\Doctrine\DBAL\Types\CitextArray` |
| ulid | ulid | `MartinGeorgiev\Doctrine\DBAL\Types\Ulid` (see [note](#ulid-type)) |
| ulid[] | _ulid | `MartinGeorgiev\Doctrine\DBAL\Types\UlidArray` |
|---|---|---|
| cidr | cidr | `MartinGeorgiev\Doctrine\DBAL\Types\Cidr` |
| cidr[] | _cidr | `MartinGeorgiev\Doctrine\DBAL\Types\CidrArray` |
| inet | inet | `MartinGeorgiev\Doctrine\DBAL\Types\Inet` |
| inet[] | _inet | `MartinGeorgiev\Doctrine\DBAL\Types\InetArray` |
| macaddr | macaddr | `MartinGeorgiev\Doctrine\DBAL\Types\Macaddr` |
| macaddr[] | _macaddr | `MartinGeorgiev\Doctrine\DBAL\Types\MacaddrArray` |
| macaddr8 | macaddr8 | `MartinGeorgiev\Doctrine\DBAL\Types\Macaddr8` |
| macaddr8[] | _macaddr8 | `MartinGeorgiev\Doctrine\DBAL\Types\Macaddr8Array` |
|---|---|---|
| tsquery | tsquery | `MartinGeorgiev\Doctrine\DBAL\Types\Tsquery` |
| tsquery[] | _tsquery | `MartinGeorgiev\Doctrine\DBAL\Types\TsqueryArray` |
| tsvector | tsvector | `MartinGeorgiev\Doctrine\DBAL\Types\Tsvector` |
| tsvector[] | _tsvector | `MartinGeorgiev\Doctrine\DBAL\Types\TsvectorArray` |
|---|---|---|
| daterange | daterange | `MartinGeorgiev\Doctrine\DBAL\Types\DateRange` |
| daterange[] | _daterange | `MartinGeorgiev\Doctrine\DBAL\Types\DateRangeArray` |
| int4range | int4range | `MartinGeorgiev\Doctrine\DBAL\Types\Int4Range` |
| int4range[] | _int4range | `MartinGeorgiev\Doctrine\DBAL\Types\Int4RangeArray` |
| int8range | int8range | `MartinGeorgiev\Doctrine\DBAL\Types\Int8Range` |
| int8range[] | _int8range | `MartinGeorgiev\Doctrine\DBAL\Types\Int8RangeArray` |
| numrange | numrange | `MartinGeorgiev\Doctrine\DBAL\Types\NumRange` |
| numrange[] | _numrange | `MartinGeorgiev\Doctrine\DBAL\Types\NumRangeArray` |
| tsrange | tsrange | `MartinGeorgiev\Doctrine\DBAL\Types\TsRange` |
| tsrange[] | _tsrange | `MartinGeorgiev\Doctrine\DBAL\Types\TsRangeArray` |
| tstzrange | tstzrange | `MartinGeorgiev\Doctrine\DBAL\Types\TstzRange` |
| tstzrange[] | _tstzrange | `MartinGeorgiev\Doctrine\DBAL\Types\TstzRangeArray` |
|---|---|---|
| datemultirange | datemultirange | `MartinGeorgiev\Doctrine\DBAL\Types\DateMultirange` |
| datemultirange[] | _datemultirange | `MartinGeorgiev\Doctrine\DBAL\Types\DateMultirangeArray` |
| int4multirange | int4multirange | `MartinGeorgiev\Doctrine\DBAL\Types\Int4Multirange` |
| int4multirange[] | _int4multirange | `MartinGeorgiev\Doctrine\DBAL\Types\Int4MultirangeArray` |
| int8multirange | int8multirange | `MartinGeorgiev\Doctrine\DBAL\Types\Int8Multirange` |
| int8multirange[] | _int8multirange | `MartinGeorgiev\Doctrine\DBAL\Types\Int8MultirangeArray` |
| nummultirange | nummultirange | `MartinGeorgiev\Doctrine\DBAL\Types\NumMultirange` |
| nummultirange[] | _nummultirange | `MartinGeorgiev\Doctrine\DBAL\Types\NumMultirangeArray` |
| tsmultirange | tsmultirange | `MartinGeorgiev\Doctrine\DBAL\Types\TsMultirange` |
| tsmultirange[] | _tsmultirange | `MartinGeorgiev\Doctrine\DBAL\Types\TsMultirangeArray` |
| tstzmultirange | tstzmultirange | `MartinGeorgiev\Doctrine\DBAL\Types\TstzMultirange` |
| tstzmultirange[] | _tstzmultirange | `MartinGeorgiev\Doctrine\DBAL\Types\TstzMultirangeArray` |
|---|---|---|
| box | box | `MartinGeorgiev\Doctrine\DBAL\Types\Box` |
| box[] | _box | `MartinGeorgiev\Doctrine\DBAL\Types\BoxArray` |
| circle | circle | `MartinGeorgiev\Doctrine\DBAL\Types\Circle` |
| circle[] | _circle | `MartinGeorgiev\Doctrine\DBAL\Types\CircleArray` |
| line | line | `MartinGeorgiev\Doctrine\DBAL\Types\Line` |
| line[] | _line | `MartinGeorgiev\Doctrine\DBAL\Types\LineArray` |
| lseg | lseg | `MartinGeorgiev\Doctrine\DBAL\Types\Lseg` |
| lseg[] | _lseg | `MartinGeorgiev\Doctrine\DBAL\Types\LsegArray` |
| path | path | `MartinGeorgiev\Doctrine\DBAL\Types\Path` |
| path[] | _path | `MartinGeorgiev\Doctrine\DBAL\Types\PathArray` |
| point | point | `MartinGeorgiev\Doctrine\DBAL\Types\Point` |
| point[] | _point | `MartinGeorgiev\Doctrine\DBAL\Types\PointArray` |
| polygon | polygon | `MartinGeorgiev\Doctrine\DBAL\Types\Polygon` |
| polygon[] | _polygon | `MartinGeorgiev\Doctrine\DBAL\Types\PolygonArray` |
|---|---|---|
| geography | geography | `MartinGeorgiev\Doctrine\DBAL\Types\Geography` (see [note](#postgis-spatial-types)) |
| geography[] | _geography | `MartinGeorgiev\Doctrine\DBAL\Types\GeographyArray` |
| geometry | geometry | `MartinGeorgiev\Doctrine\DBAL\Types\Geometry` (see [note](#postgis-spatial-types)) |
| geometry[] | _geometry | `MartinGeorgiev\Doctrine\DBAL\Types\GeometryArray` |
|---|---|---|
| cube | cube | `MartinGeorgiev\Doctrine\DBAL\Types\Cube` (see [note](#cube-type)) |
| cube[] | _cube | `MartinGeorgiev\Doctrine\DBAL\Types\CubeArray` |
|---|---|---|
| hstore | hstore | `MartinGeorgiev\Doctrine\DBAL\Types\Hstore` (see [note](#hstore-type)) |
| hstore[] | _hstore | `MartinGeorgiev\Doctrine\DBAL\Types\HstoreArray` |
|---|---|---|
| lquery | lquery | `MartinGeorgiev\Doctrine\DBAL\Types\Lquery` |
| lquery[] | _lquery | `MartinGeorgiev\Doctrine\DBAL\Types\LqueryArray` |
| ltree | ltree | `MartinGeorgiev\Doctrine\DBAL\Types\Ltree` |
| ltree[] | _ltree | `MartinGeorgiev\Doctrine\DBAL\Types\LtreeArray` |
| ltxtquery | ltxtquery | `MartinGeorgiev\Doctrine\DBAL\Types\Ltxtquery` |
| ltxtquery[] | _ltxtquery | `MartinGeorgiev\Doctrine\DBAL\Types\LtxtqueryArray` |
|---|---|---|
| money | money | `MartinGeorgiev\Doctrine\DBAL\Types\Money` (see [note](#money-type)) |
| money[] | _money | `MartinGeorgiev\Doctrine\DBAL\Types\MoneyArray` |
|---|---|---|
| xml | xml | `MartinGeorgiev\Doctrine\DBAL\Types\Xml` |
| xml[] | _xml | `MartinGeorgiev\Doctrine\DBAL\Types\XmlArray` |
|---|---|---|
| halfvec | halfvec | `MartinGeorgiev\Doctrine\DBAL\Types\Halfvec` (see [note](#pgvector-types)) |
| sparsevec | sparsevec | `MartinGeorgiev\Doctrine\DBAL\Types\Sparsevec` (see [note](#pgvector-types)) |
| vector | vector | `MartinGeorgiev\Doctrine\DBAL\Types\Vector` (see [note](#pgvector-types)) |
|---|---|---|
| *(user-defined enum)* | *(any)* | `MartinGeorgiev\Doctrine\DBAL\Types\Enum` (see [Enum Types](ENUM-TYPE.md)) |
| *(user-defined enum)[]* | *(any)* | `MartinGeorgiev\Doctrine\DBAL\Types\EnumArray` (see [Enum Types](ENUM-TYPE.md)) |
| *(user-defined composite)* | *(any)* | `MartinGeorgiev\Doctrine\DBAL\Types\Composite` (see [Composite Types](COMPOSITE-TYPE.md)) |
| *(user-defined composite)[]* | *(any)* | `MartinGeorgiev\Doctrine\DBAL\Types\CompositeArray` (see [Composite Types](COMPOSITE-TYPE.md)) |

## PostGIS Spatial Types

The `geometry` and `geography` types accept the `geometry_type` and `srid` column options, which emit a PostGIS type modifier so the subtype and spatial reference system are enforced by the database:

```php
use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

#[ORM\Entity]
class Place
{
    // GEOGRAPHY(POINT,4326) — only WGS 84 points are accepted
    #[ORM\Column(type: 'geography', options: ['geometry_type' => 'Point', 'srid' => 4326])]
    private WktSpatialData $location;

    // GEOMETRY — unconstrained, accepts any geometry
    #[ORM\Column(type: 'geometry')]
    private WktSpatialData $shape;
}
```

Omitting both options keeps the bare `GEOMETRY` / `GEOGRAPHY` declaration.

> 📖 **See also**: [Spatial Types](SPATIAL-TYPES.md#column-options-for-ddl) for the full option reference

---

## pgvector Types

The `vector`, `halfvec`, and `sparsevec` types use the `length` column option to specify the number of dimensions:

```php
use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Sparsevec;

#[ORM\Entity]
class Embedding
{
    // VECTOR(1536) — fixed 1536-dimensional float vector
    #[ORM\Column(type: 'vector', length: 1536)]
    private array $embedding;

    // HALFVEC(1024) — half-precision float vector
    #[ORM\Column(type: 'halfvec', length: 1024)]
    private array $smallEmbedding;

    // SPARSEVEC(4096) — sparse vector with up to 4096 dimensions
    #[ORM\Column(type: 'sparsevec', length: 4096)]
    private Sparsevec $sparseEmbedding;
}
```

**Important:** Omitting `length` produces a dimensionless column (`VECTOR` with no size), which is valid DDL but cannot be indexed with HNSW or IVFFlat indexes. Always specify `length` for production use.

---

## Bit String Types

The `bit` and `bit varying` types support an optional `length` parameter via column attribute:

```php
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Permissions
{
    // BIT(1) — fixed single bit (default when no length specified)
    #[ORM\Column(type: 'bit')]
    private string $active;

    // BIT(8) — fixed 8-bit flags
    #[ORM\Column(type: 'bit', length: 8)]
    private string $flags;

    // BIT VARYING — unlimited length (default when no length specified)
    #[ORM\Column(type: 'bit varying')]
    private string $mask;

    // BIT VARYING(64) — variable length, up to 64 bits
    #[ORM\Column(type: 'bit varying', length: 64)]
    private string $features;
}
```

**Important:** `BIT` without a length defaults to `BIT(1)` in PostgreSQL, which stores exactly one bit. Use `BIT VARYING` for variable-length bit strings, or specify an explicit length with `BIT(n)`.

---

## Numeric Array Type

The `numeric[]` type maps array items to PHP strings (e.g. `'502.00'`) rather than floats. PostgreSQL's [`numeric`](https://www.postgresql.org/docs/18/datatype-numeric.html#DATATYPE-NUMERIC-DECIMAL) is an arbitrary-precision type, and converting its values to PHP floats would silently lose precision and trailing zeros — the same reason Doctrine's own `decimal` type uses strings.

- Array items written to the database must be numeric strings (or `null`); PHP integers and floats are rejected
- `decimal[]` is a PostgreSQL alias of `numeric[]` — columns declared as `DECIMAL[]` are reported by PostgreSQL as `numeric[]`, so this type covers both

---

## UUID Array Type

The `uuid[]` type validates UUID format and returns `string[]` rather than UUID value objects. This design decision keeps the library lightweight and framework-agnostic:

- **No additional dependencies** - Works without requiring `ramsey/uuid` or `symfony/uid`
- **Consistent with other array types** - Follows the same pattern as `TextArray`, `IntegerArray`, etc.
- **Framework agnostic** - Compatible with any UUID library of your choice

If you need UUID objects, you can easily convert the strings:

```php
// With ramsey/uuid
use Ramsey\Uuid\Uuid;
$uuids = array_map(fn(string $uuid) => Uuid::fromString($uuid), $entity->getUuidArray());

// With symfony/uid
use Symfony\Component\Uid\Uuid;
$uuids = array_map(fn(string $uuid) => Uuid::fromString($uuid), $entity->getUuidArray());
```

---

## Money Type

The `money` type maps PostgreSQL's [`money`](https://www.postgresql.org/docs/18/datatype-money.html) data type and returns locale-formatted strings (e.g. `$1,234.56`). PostgreSQL formats money values according to the server's `lc_monetary` locale setting, so the exact output format depends on your database configuration.

**Important considerations:**

- PostgreSQL's `money` type does **not** store currency information — the currency symbol is purely a formatting artifact of the server locale
- If you need multi-currency support, consider using `numeric` with application-level currency handling instead
- Values written to the database must contain at least one digit; full format validation is deferred to PostgreSQL

If you need rich money objects for arithmetic or multi-currency support, you can convert the string after retrieval:

```php
// With moneyphp/money (requires parsing the locale-formatted string)
use Money\Money;
use Money\Currency;
$amount = (int) round((float) preg_replace('/[^0-9.\-]/', '', $entity->getPrice()) * 100);
$money = new Money($amount, new Currency('USD'));

// With brick/money
use Brick\Money\Money;
$money = Money::of(preg_replace('/[^0-9.\-]/', '', $entity->getPrice()), 'USD');
```

Note that both examples above assume USD — you must know the currency independently since PostgreSQL does not store it.

---

## Hstore Type

The `hstore` type requires the PostgreSQL [`hstore`](https://www.postgresql.org/docs/18/hstore.html) extension. Enable it with:

```sql
CREATE EXTENSION IF NOT EXISTS hstore;
```

It maps to `array<string, string|null>` in PHP. Keys and values are always strings; a `NULL` value in hstore is represented as `null` in PHP.

---

## Citext Type

The `citext` type requires the PostgreSQL [`citext`](https://www.postgresql.org/docs/18/citext.html) extension. Enable it with:

```sql
CREATE EXTENSION IF NOT EXISTS citext;
```

It is a case-insensitive text type: comparisons are case-insensitive in PostgreSQL while the original casing of values is preserved. It maps to `string` in PHP and behaves identically to `text` for storage and retrieval — the difference is purely in how PostgreSQL evaluates equality and ordering.

Use `citext` when you want case-insensitive lookups (e.g. usernames, email addresses) without lowercasing values on write.

---

## ULID Type

The `ulid` type requires the third-party [`pgx_ulid`](https://github.com/pksunkara/pgx_ulid) PostgreSQL extension. Enable it with:

```sql
CREATE EXTENSION IF NOT EXISTS ulid;
```

A ULID is a 26-character [Crockford base32](https://github.com/ulid/spec) identifier (uppercase, first character `0`–`7`) stored as a compact 128-bit binary value. It maps to `string` in PHP. PostgreSQL outputs the canonical uppercase form on retrieval; the DBAL type normalizes values to uppercase on write as well, so round-trips are stable even for lowercase input.

Use `ulid` when you want sortable, timestamp-prefixed identifiers that are shorter and more index-friendly than UUIDs.

---

## Cube Type

The `cube` type requires the PostgreSQL [`cube`](https://www.postgresql.org/docs/18/cube.html) extension. Enable it with:

```sql
CREATE EXTENSION IF NOT EXISTS cube;
```

A cube is a multidimensional value that is either a point — `(1, 2, 3)` — or a box spanned by two opposite corners — `(1, 2, 3),(4, 5, 6)`. Both corners always carry the same number of dimensions. It maps to the `MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Cube` value object in PHP:

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Cube;

$point = Cube::point(1.0, 2.0, 3.0);               // (1, 2, 3)
$box = new Cube([1.0, 2.0, 3.0], [4.0, 5.0, 6.0]); // (1, 2, 3),(4, 5, 6)

$box->getFirstCorner();  // [1.0, 2.0, 3.0]
$box->getSecondCorner(); // [4.0, 5.0, 6.0]
$box->getDimensions();   // 3
$point->isPoint();       // true
```
