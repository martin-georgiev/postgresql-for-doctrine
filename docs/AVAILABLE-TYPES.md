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
|---|---|---|
| cidr | cidr | `MartinGeorgiev\Doctrine\DBAL\Types\Cidr` |
| cidr[] | _cidr | `MartinGeorgiev\Doctrine\DBAL\Types\CidrArray` |
| inet | inet | `MartinGeorgiev\Doctrine\DBAL\Types\Inet` |
| inet[] | _inet | `MartinGeorgiev\Doctrine\DBAL\Types\InetArray` |
| macaddr | macaddr | `MartinGeorgiev\Doctrine\DBAL\Types\Macaddr` |
| macaddr[] | _macaddr | `MartinGeorgiev\Doctrine\DBAL\Types\MacaddrArray` |
|---|---|---|
| daterange | daterange | `MartinGeorgiev\Doctrine\DBAL\Types\DateRange` |
| int4range | int4range | `MartinGeorgiev\Doctrine\DBAL\Types\Int4Range` |
| int8range | int8range | `MartinGeorgiev\Doctrine\DBAL\Types\Int8Range` |
| numrange | numrange | `MartinGeorgiev\Doctrine\DBAL\Types\NumRange` |
| tsrange | tsrange | `MartinGeorgiev\Doctrine\DBAL\Types\TsRange` |
| tstzrange | tstzrange | `MartinGeorgiev\Doctrine\DBAL\Types\TstzRange` |
|---|---|---|
| geography | geography | `MartinGeorgiev\Doctrine\DBAL\Types\Geography` |
| geography[] | geography[] | `MartinGeorgiev\Doctrine\DBAL\Types\GeographyArray` |
| geometry | geometry | `MartinGeorgiev\Doctrine\DBAL\Types\Geometry` |
| geometry[] | geometry[] | `MartinGeorgiev\Doctrine\DBAL\Types\GeometryArray` |
| point | point | `MartinGeorgiev\Doctrine\DBAL\Types\Point` |
| point[] | _point | `MartinGeorgiev\Doctrine\DBAL\Types\PointArray` |
