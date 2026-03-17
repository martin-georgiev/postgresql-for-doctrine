<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL;

final class Type
{
    public const BIGINT_ARRAY = 'bigint[]';

    public const BOOL_ARRAY = 'bool[]';

    public const CIDR = 'cidr';

    public const CIDR_ARRAY = 'cidr[]';

    public const DATERANGE = 'daterange';

    public const DATEMULTIRANGE = 'datemultirange';

    public const DOUBLE_PRECISION_ARRAY = 'double precision[]';

    public const GEOGRAPHY = 'geography';

    public const GEOGRAPHY_ARRAY = 'geography[]';

    public const GEOMETRY = 'geometry';

    public const GEOMETRY_ARRAY = 'geometry[]';

    public const INET = 'inet';

    public const INET_ARRAY = 'inet[]';

    public const INT4MULTIRANGE = 'int4multirange';

    public const INT4RANGE = 'int4range';

    public const INT8MULTIRANGE = 'int8multirange';

    public const INT8RANGE = 'int8range';

    public const INTEGER_ARRAY = 'integer[]';

    public const INTERVAL = 'interval';

    public const JSONB = 'jsonb';

    public const JSONB_ARRAY = 'jsonb[]';

    public const LTREE = 'ltree';

    public const MACADDR = 'macaddr';

    public const MACADDR8 = 'macaddr8';

    public const MACADDR8_ARRAY = 'macaddr8[]';

    public const MACADDR_ARRAY = 'macaddr[]';

    public const MONEY = 'money';

    public const NUMMULTIRANGE = 'nummultirange';

    public const NUMRANGE = 'numrange';

    public const POINT = 'point';

    public const POINT_ARRAY = 'point[]';

    public const REAL_ARRAY = 'real[]';

    public const SMALLINT_ARRAY = 'smallint[]';

    public const TEXT_ARRAY = 'text[]';

    public const TSQUERY = 'tsquery';

    public const TSRANGE = 'tsrange';

    public const TSMULTIRANGE = 'tsmultirange';

    public const TSVECTOR = 'tsvector';

    public const TSTZRANGE = 'tstzrange';

    public const TSTZMULTIRANGE = 'tstzmultirange';

    public const UUID_ARRAY = 'uuid[]';

    public const VECTOR = 'vector';

    public const XML = 'xml';

    private function __construct()
    {
        // Prevent instantiation
    }
}
