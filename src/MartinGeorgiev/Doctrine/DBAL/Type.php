<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL;

final class Type
{
    // Array Types
    public const BIGINT_ARRAY = 'bigint[]';
    public const BOOLEAN_ARRAY = 'bool[]';
    public const CIDR_ARRAY = 'cidr[]';
    public const DOUBLE_PRECISION_ARRAY = 'double precision[]';
    public const GEOGRAPHY_ARRAY = 'geography[]';
    public const GEOMETRY_ARRAY = 'geometry[]';
    public const INET_ARRAY = 'inet[]';
    public const INTEGER_ARRAY = 'integer[]';
    public const JSONB_ARRAY = 'jsonb[]';
    public const MACADDR_ARRAY = 'macaddr[]';
    public const POINT_ARRAY = 'point[]';
    public const REAL_ARRAY = 'real[]';
    public const SMALLINT_ARRAY = 'smallint[]';
    public const TEXT_ARRAY = 'text[]';

    // Scalar Types
    public const CIDR = 'cidr';
    public const GEOGRAPHY = 'geography';
    public const GEOMETRY = 'geometry';
    public const INET = 'inet';
    public const JSONB = 'jsonb';
    public const LTREE = 'ltree';
    public const MACADDR = 'macaddr';
    public const POINT = 'point';

    // Range Types
    public const DATERANGE = 'daterange';
    public const INT4RANGE = 'int4range';
    public const INT8RANGE = 'int8range';
    public const NUMRANGE = 'numrange';
    public const TSRANGE = 'tsrange';
    public const TSTZRANGE = 'tstzrange';
}
