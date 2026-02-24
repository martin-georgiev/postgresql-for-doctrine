# Available types

| PostgreSQL type in practical use | PostgreSQL internal system catalogue name | Implemented by |
|---|---|---|
| bool[] | _bool | `MartinGeorgiev\Doctrine\DBAL\Types\BooleanArray` |
| smallint[] | _int2 | `MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray` |
| integer[] | _int4 | `MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray` |
| bigint[] | _int8 | `MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray` |
| real[] | _float4 | `MartinGeorgiev\Doctrine\DBAL\Types\RealArray` |
| double precision[] | _float8 | `MartinGeorgiev\Doctrine\DBAL\Types\DoublePrecisionArray` |
|---|---|---|
| jsonb | jsonb | `MartinGeorgiev\Doctrine\DBAL\Types\Jsonb` |
| jsonb[] | _jsonb | `MartinGeorgiev\Doctrine\DBAL\Types\JsonbArray` |
| text[] | _text | `MartinGeorgiev\Doctrine\DBAL\Types\TextArray` |
| uuid[] | _uuid | `MartinGeorgiev\Doctrine\DBAL\Types\UuidArray` (see [note](#uuid-array-type)) |
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
| tsvector | tsvector | `MartinGeorgiev\Doctrine\DBAL\Types\Tsvector` |
|---|---|---|
| daterange | daterange | `MartinGeorgiev\Doctrine\DBAL\Types\DateRange` |
| int4range | int4range | `MartinGeorgiev\Doctrine\DBAL\Types\Int4Range` |
| int8range | int8range | `MartinGeorgiev\Doctrine\DBAL\Types\Int8Range` |
| numrange | numrange | `MartinGeorgiev\Doctrine\DBAL\Types\NumRange` |
| tsrange | tsrange | `MartinGeorgiev\Doctrine\DBAL\Types\TsRange` |
| tstzrange | tstzrange | `MartinGeorgiev\Doctrine\DBAL\Types\TstzRange` |
|---|---|---|
| geography | geography | `MartinGeorgiev\Doctrine\DBAL\Types\Geography` |
| geography[] | _geography | `MartinGeorgiev\Doctrine\DBAL\Types\GeographyArray` |
| geometry | geometry | `MartinGeorgiev\Doctrine\DBAL\Types\Geometry` |
| geometry[] | _geometry | `MartinGeorgiev\Doctrine\DBAL\Types\GeometryArray` |
| point | point | `MartinGeorgiev\Doctrine\DBAL\Types\Point` |
| point[] | _point | `MartinGeorgiev\Doctrine\DBAL\Types\PointArray` |
|---|---|---|
| ltree | ltree | `MartinGeorgiev\Doctrine\DBAL\Types\Ltree` |

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
