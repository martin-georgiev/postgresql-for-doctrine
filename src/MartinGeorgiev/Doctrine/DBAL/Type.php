<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL;

final class Type
{
    public const BIGINT_ARRAY = 'bigint[]';

    public const BOOL_ARRAY = 'bool[]';

    public const BOX = 'box';

    public const CIDR = 'cidr';

    public const CIDR_ARRAY = 'cidr[]';

    public const CIRCLE = 'circle';

    public const DATE_ARRAY = 'date[]';

    public const DATEMULTIRANGE = 'datemultirange';

    public const DATERANGE = 'daterange';

    public const DOUBLE_PRECISION_ARRAY = 'double precision[]';

    public const GEOGRAPHY = 'geography';

    public const GEOGRAPHY_ARRAY = 'geography[]';

    public const GEOMETRY = 'geometry';

    public const GEOMETRY_ARRAY = 'geometry[]';

    public const HALFVEC = 'halfvec';

    public const INET = 'inet';

    public const INET_ARRAY = 'inet[]';

    public const INT4MULTIRANGE = 'int4multirange';

    public const INT4RANGE = 'int4range';

    public const INT8MULTIRANGE = 'int8multirange';

    public const INT8RANGE = 'int8range';

    public const INTEGER_ARRAY = 'integer[]';

    public const INTERVAL = 'interval';

    public const INTERVAL_ARRAY = 'interval[]';

    public const JSONB = 'jsonb';

    public const JSONB_ARRAY = 'jsonb[]';

    public const LINE = 'line';

    public const LSEG = 'lseg';

    public const LTREE = 'ltree';

    public const LTREE_ARRAY = 'ltree[]';

    public const MACADDR = 'macaddr';

    public const MACADDR8 = 'macaddr8';

    public const MACADDR8_ARRAY = 'macaddr8[]';

    public const MACADDR_ARRAY = 'macaddr[]';

    public const MONEY = 'money';

    public const MONEY_ARRAY = 'money[]';

    public const NUMMULTIRANGE = 'nummultirange';

    public const NUMRANGE = 'numrange';

    public const PATH = 'path';

    public const POINT = 'point';

    public const POINT_ARRAY = 'point[]';

    public const POLYGON = 'polygon';

    public const REAL_ARRAY = 'real[]';

    public const SMALLINT_ARRAY = 'smallint[]';

    public const SPARSEVEC = 'sparsevec';

    public const TEXT_ARRAY = 'text[]';

    public const TIMESTAMP_ARRAY = 'timestamp[]';

    public const TIMESTAMPTZ_ARRAY = 'timestamptz[]';

    public const TSQUERY = 'tsquery';

    public const TSQUERY_ARRAY = 'tsquery[]';

    public const TSRANGE = 'tsrange';

    public const TSMULTIRANGE = 'tsmultirange';

    public const TSVECTOR = 'tsvector';

    public const TSVECTOR_ARRAY = 'tsvector[]';

    public const TSTZRANGE = 'tstzrange';

    public const TSTZMULTIRANGE = 'tstzmultirange';

    public const UUID_ARRAY = 'uuid[]';

    public const VECTOR = 'vector';

    public const XML = 'xml';

    public const XML_ARRAY = 'xml[]';

    private function __construct()
    {
        // Prevent instantiation
    }
}
