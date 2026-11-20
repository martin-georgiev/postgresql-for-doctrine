<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL;

final class Type
{
    public const string BIGINT_ARRAY = 'bigint[]';

    public const string BIT = 'bit';

    public const string BIT_ARRAY = 'bit[]';

    public const string BIT_VARYING = 'bit varying';

    public const string BIT_VARYING_ARRAY = 'bit varying[]';

    public const string BOOL_ARRAY = 'bool[]';

    public const string BOX = 'box';

    public const string BOX_ARRAY = 'box[]';

    public const string BYTEA = 'bytea';

    public const string BYTEA_ARRAY = 'bytea[]';

    public const string CITEXT = 'citext';

    public const string CITEXT_ARRAY = 'citext[]';

    public const string CIDR = 'cidr';

    public const string CIDR_ARRAY = 'cidr[]';

    public const string CIRCLE = 'circle';

    public const string CIRCLE_ARRAY = 'circle[]';

    public const string CUBE = 'cube';

    public const string CUBE_ARRAY = 'cube[]';

    public const string DATE_ARRAY = 'date[]';

    public const string DATEMULTIRANGE = 'datemultirange';

    public const string DATEMULTIRANGE_ARRAY = 'datemultirange[]';

    public const string DATERANGE = 'daterange';

    public const string DATERANGE_ARRAY = 'daterange[]';

    public const string DOUBLE_PRECISION_ARRAY = 'double precision[]';

    public const string GEOGRAPHY = 'geography';

    public const string GEOGRAPHY_ARRAY = 'geography[]';

    public const string GEOMETRY = 'geometry';

    public const string GEOMETRY_ARRAY = 'geometry[]';

    public const string HALFVEC = 'halfvec';

    public const string HSTORE = 'hstore';

    public const string HSTORE_ARRAY = 'hstore[]';

    public const string INET = 'inet';

    public const string INET_ARRAY = 'inet[]';

    public const string INT4MULTIRANGE = 'int4multirange';

    public const string INT4MULTIRANGE_ARRAY = 'int4multirange[]';

    public const string INT4RANGE = 'int4range';

    public const string INT4RANGE_ARRAY = 'int4range[]';

    public const string INT8MULTIRANGE = 'int8multirange';

    public const string INT8MULTIRANGE_ARRAY = 'int8multirange[]';

    public const string INT8RANGE = 'int8range';

    public const string INT8RANGE_ARRAY = 'int8range[]';

    public const string INTEGER_ARRAY = 'integer[]';

    public const string INTERVAL = 'interval';

    public const string INTERVAL_ARRAY = 'interval[]';

    public const string JSON_ARRAY = 'json[]';

    public const string JSONB = 'jsonb';

    public const string JSONB_ARRAY = 'jsonb[]';

    public const string LINE = 'line';

    public const string LINE_ARRAY = 'line[]';

    public const string LSEG = 'lseg';

    public const string LSEG_ARRAY = 'lseg[]';

    public const string LQUERY = 'lquery';

    public const string LQUERY_ARRAY = 'lquery[]';

    public const string LTREE = 'ltree';

    public const string LTREE_ARRAY = 'ltree[]';

    public const string LTXTQUERY = 'ltxtquery';

    public const string LTXTQUERY_ARRAY = 'ltxtquery[]';

    public const string MACADDR = 'macaddr';

    public const string MACADDR8 = 'macaddr8';

    public const string MACADDR8_ARRAY = 'macaddr8[]';

    public const string MACADDR_ARRAY = 'macaddr[]';

    public const string MONEY = 'money';

    public const string MONEY_ARRAY = 'money[]';

    public const string NUMERIC_ARRAY = 'numeric[]';

    public const string NUMMULTIRANGE = 'nummultirange';

    public const string NUMMULTIRANGE_ARRAY = 'nummultirange[]';

    public const string NUMRANGE = 'numrange';

    public const string NUMRANGE_ARRAY = 'numrange[]';

    public const string PATH = 'path';

    public const string PATH_ARRAY = 'path[]';

    public const string POINT = 'point';

    public const string POINT_ARRAY = 'point[]';

    public const string POLYGON = 'polygon';

    public const string POLYGON_ARRAY = 'polygon[]';

    public const string REAL_ARRAY = 'real[]';

    public const string SMALLINT_ARRAY = 'smallint[]';

    public const string SPARSEVEC = 'sparsevec';

    public const string TEXT_ARRAY = 'text[]';

    public const string TIME_ARRAY = 'time[]';

    public const string TIMETZ = 'timetz';

    public const string TIMETZ_ARRAY = 'timetz[]';

    public const string TIMESTAMP_ARRAY = 'timestamp[]';

    public const string TIMESTAMPTZ_ARRAY = 'timestamptz[]';

    public const string TSQUERY = 'tsquery';

    public const string TSQUERY_ARRAY = 'tsquery[]';

    public const string TSRANGE = 'tsrange';

    public const string TSRANGE_ARRAY = 'tsrange[]';

    public const string TSMULTIRANGE = 'tsmultirange';

    public const string TSMULTIRANGE_ARRAY = 'tsmultirange[]';

    public const string TSVECTOR = 'tsvector';

    public const string TSVECTOR_ARRAY = 'tsvector[]';

    public const string TSTZRANGE = 'tstzrange';

    public const string TSTZRANGE_ARRAY = 'tstzrange[]';

    public const string TSTZMULTIRANGE = 'tstzmultirange';

    public const string TSTZMULTIRANGE_ARRAY = 'tstzmultirange[]';

    public const string ULID = 'ulid';

    public const string ULID_ARRAY = 'ulid[]';

    public const string UUID_ARRAY = 'uuid[]';

    public const string VARCHAR_ARRAY = 'varchar[]';

    public const string VECTOR = 'vector';

    public const string XML = 'xml';

    public const string XML_ARRAY = 'xml[]';

    private function __construct()
    {
        // Prevent instantiation
    }
}
