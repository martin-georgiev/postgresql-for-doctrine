# Available types

| PostgreSQL type in practical use | PostgreSQL internal system catalogue name | Implemented by |
|---|---|---|
| bool[] | _bool | `MartinGeorgiev\Doctrine\DBAL\Types\BooleanArray` |
| smallint[] | _int2 | `MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray` |
| integer[] | _int4 | `MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray` |
| bigint[] | _int8 | `MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray` |
| real[] | _float4 | `MartinGeorgiev\Doctrine\DBAL\Types\RealArray` |
| double precision[] | _float8 | `MartinGeorgiev\Doctrine\DBAL\Types\DoublePrecisionArray` |
| text[] | _text | `MartinGeorgiev\Doctrine\DBAL\Types\TextArray` |
| jsonb[] | _jsonb | `MartinGeorgiev\Doctrine\DBAL\Types\JsonbArray` |
| jsonb | jsonb | `MartinGeorgiev\Doctrine\DBAL\Types\Jsonb` |
| inet | inet | `MartinGeorgiev\Doctrine\DBAL\Types\Inet` |
| inet[] | _inet | `MartinGeorgiev\Doctrine\DBAL\Types\InetArray` |
| cidr | cidr | `MartinGeorgiev\Doctrine\DBAL\Types\Cidr` |
| cidr[] | _cidr | `MartinGeorgiev\Doctrine\DBAL\Types\CidrArray` |
| macaddr | macaddr | `MartinGeorgiev\Doctrine\DBAL\Types\Macaddr` |
| macaddr[] | _macaddr | `MartinGeorgiev\Doctrine\DBAL\Types\MacaddrArray` |
| point | point | `MartinGeorgiev\Doctrine\DBAL\Types\Point` |
| point[] | _point | `MartinGeorgiev\Doctrine\DBAL\Types\PointArray` |
