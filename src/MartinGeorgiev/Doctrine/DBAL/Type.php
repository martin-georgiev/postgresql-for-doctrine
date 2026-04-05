<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL;

final class Type
{
    /**
     * @var string
     */
    public const BIGINT_ARRAY = 'bigint[]';

    /**
     * @var string
     */
    public const BIT = 'bit';

    /**
     * @var string
     */
    public const BIT_ARRAY = 'bit[]';

    /**
     * @var string
     */
    public const BIT_VARYING = 'bit varying';

    /**
     * @var string
     */
    public const BIT_VARYING_ARRAY = 'bit varying[]';

    /**
     * @var string
     */
    public const BOOL_ARRAY = 'bool[]';

    /**
     * @var string
     */
    public const BOX = 'box';

    /**
     * @var string
     */
    public const BOX_ARRAY = 'box[]';

    /**
     * @var string
     */
    public const CIDR = 'cidr';

    /**
     * @var string
     */
    public const CIDR_ARRAY = 'cidr[]';

    /**
     * @var string
     */
    public const CIRCLE = 'circle';

    /**
     * @var string
     */
    public const CIRCLE_ARRAY = 'circle[]';

    /**
     * @var string
     */
    public const DATE_ARRAY = 'date[]';

    /**
     * @var string
     */
    public const DATEMULTIRANGE = 'datemultirange';

    /**
     * @var string
     */
    public const DATERANGE = 'daterange';

    /**
     * @var string
     */
    public const DOUBLE_PRECISION_ARRAY = 'double precision[]';

    /**
     * @var string
     */
    public const GEOGRAPHY = 'geography';

    /**
     * @var string
     */
    public const GEOGRAPHY_ARRAY = 'geography[]';

    /**
     * @var string
     */
    public const GEOMETRY = 'geometry';

    /**
     * @var string
     */
    public const GEOMETRY_ARRAY = 'geometry[]';

    /**
     * @var string
     */
    public const HALFVEC = 'halfvec';

    /**
     * @var string
     */
    public const INET = 'inet';

    /**
     * @var string
     */
    public const INET_ARRAY = 'inet[]';

    /**
     * @var string
     */
    public const INT4MULTIRANGE = 'int4multirange';

    /**
     * @var string
     */
    public const INT4RANGE = 'int4range';

    /**
     * @var string
     */
    public const INT8MULTIRANGE = 'int8multirange';

    /**
     * @var string
     */
    public const INT8RANGE = 'int8range';

    /**
     * @var string
     */
    public const INTEGER_ARRAY = 'integer[]';

    /**
     * @var string
     */
    public const INTERVAL = 'interval';

    /**
     * @var string
     */
    public const INTERVAL_ARRAY = 'interval[]';

    /**
     * @var string
     */
    public const JSONB = 'jsonb';

    /**
     * @var string
     */
    public const JSONB_ARRAY = 'jsonb[]';

    /**
     * @var string
     */
    public const LINE = 'line';

    /**
     * @var string
     */
    public const LINE_ARRAY = 'line[]';

    /**
     * @var string
     */
    public const LSEG = 'lseg';

    /**
     * @var string
     */
    public const LSEG_ARRAY = 'lseg[]';

    /**
     * @var string
     */
    public const LTREE = 'ltree';

    /**
     * @var string
     */
    public const LTREE_ARRAY = 'ltree[]';

    /**
     * @var string
     */
    public const MACADDR = 'macaddr';

    /**
     * @var string
     */
    public const MACADDR8 = 'macaddr8';

    /**
     * @var string
     */
    public const MACADDR8_ARRAY = 'macaddr8[]';

    /**
     * @var string
     */
    public const MACADDR_ARRAY = 'macaddr[]';

    /**
     * @var string
     */
    public const MONEY = 'money';

    /**
     * @var string
     */
    public const MONEY_ARRAY = 'money[]';

    /**
     * @var string
     */
    public const NUMMULTIRANGE = 'nummultirange';

    /**
     * @var string
     */
    public const NUMRANGE = 'numrange';

    /**
     * @var string
     */
    public const PATH = 'path';

    /**
     * @var string
     */
    public const PATH_ARRAY = 'path[]';

    /**
     * @var string
     */
    public const POINT = 'point';

    /**
     * @var string
     */
    public const POINT_ARRAY = 'point[]';

    /**
     * @var string
     */
    public const POLYGON = 'polygon';

    /**
     * @var string
     */
    public const POLYGON_ARRAY = 'polygon[]';

    /**
     * @var string
     */
    public const REAL_ARRAY = 'real[]';

    /**
     * @var string
     */
    public const SMALLINT_ARRAY = 'smallint[]';

    /**
     * @var string
     */
    public const SPARSEVEC = 'sparsevec';

    /**
     * @var string
     */
    public const TEXT_ARRAY = 'text[]';

    /**
     * @var string
     */
    public const TIMESTAMP_ARRAY = 'timestamp[]';

    /**
     * @var string
     */
    public const TIMESTAMPTZ_ARRAY = 'timestamptz[]';

    /**
     * @var string
     */
    public const TSQUERY = 'tsquery';

    /**
     * @var string
     */
    public const TSQUERY_ARRAY = 'tsquery[]';

    /**
     * @var string
     */
    public const TSRANGE = 'tsrange';

    /**
     * @var string
     */
    public const TSMULTIRANGE = 'tsmultirange';

    /**
     * @var string
     */
    public const TSVECTOR = 'tsvector';

    /**
     * @var string
     */
    public const TSVECTOR_ARRAY = 'tsvector[]';

    /**
     * @var string
     */
    public const TSTZRANGE = 'tstzrange';

    /**
     * @var string
     */
    public const TSTZMULTIRANGE = 'tstzmultirange';

    /**
     * @var string
     */
    public const UUID_ARRAY = 'uuid[]';

    /**
     * @var string
     */
    public const VECTOR = 'vector';

    /**
     * @var string
     */
    public const XML = 'xml';

    /**
     * @var string
     */
    public const XML_ARRAY = 'xml[]';

    private function __construct()
    {
        // Prevent instantiation
    }
}
