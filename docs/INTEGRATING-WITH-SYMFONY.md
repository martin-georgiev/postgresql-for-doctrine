# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> Integration with Symfony

This guide covers integration with Symfony 6.4+ and 7.x using the DoctrineBundle.

### Register DBAL Types

Register the DBAL types you plan to use. The full set of available types can be found in [AVAILABLE-TYPES.md](AVAILABLE-TYPES.md).

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            # Binary types
            bytea: MartinGeorgiev\Doctrine\DBAL\Types\Bytea
            'bytea[]': MartinGeorgiev\Doctrine\DBAL\Types\ByteaArray

            # Bit types
            bit: MartinGeorgiev\Doctrine\DBAL\Types\Bit
            'bit[]': MartinGeorgiev\Doctrine\DBAL\Types\BitArray
            'bit varying': MartinGeorgiev\Doctrine\DBAL\Types\BitVarying
            'bit varying[]': MartinGeorgiev\Doctrine\DBAL\Types\BitVaryingArray

            # Array types
            'bool[]': MartinGeorgiev\Doctrine\DBAL\Types\BooleanArray
            'smallint[]': MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray
            'integer[]': MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray
            'bigint[]': MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray
            'double precision[]': MartinGeorgiev\Doctrine\DBAL\Types\DoublePrecisionArray
            'numeric[]': MartinGeorgiev\Doctrine\DBAL\Types\NumericArray
            'real[]': MartinGeorgiev\Doctrine\DBAL\Types\RealArray
            'text[]': MartinGeorgiev\Doctrine\DBAL\Types\TextArray
            'uuid[]': MartinGeorgiev\Doctrine\DBAL\Types\UuidArray
            'varchar[]': MartinGeorgiev\Doctrine\DBAL\Types\VarcharArray

            # Date and time types
            'date[]': MartinGeorgiev\Doctrine\DBAL\Types\DateArray
            interval: MartinGeorgiev\Doctrine\DBAL\Types\Interval
            'interval[]': MartinGeorgiev\Doctrine\DBAL\Types\IntervalArray
            'time[]': MartinGeorgiev\Doctrine\DBAL\Types\TimeArray
            'timestamp[]': MartinGeorgiev\Doctrine\DBAL\Types\TimestampArray
            'timestamptz[]': MartinGeorgiev\Doctrine\DBAL\Types\TimestampTzArray
            timetz: MartinGeorgiev\Doctrine\DBAL\Types\Timetz
            'timetz[]': MartinGeorgiev\Doctrine\DBAL\Types\TimetzArray

            # JSON types
            'json[]': MartinGeorgiev\Doctrine\DBAL\Types\JsonArray
            jsonb: MartinGeorgiev\Doctrine\DBAL\Types\Jsonb
            'jsonb[]': MartinGeorgiev\Doctrine\DBAL\Types\JsonbArray

            # Network types
            cidr: MartinGeorgiev\Doctrine\DBAL\Types\Cidr
            'cidr[]': MartinGeorgiev\Doctrine\DBAL\Types\CidrArray
            inet: MartinGeorgiev\Doctrine\DBAL\Types\Inet
            'inet[]': MartinGeorgiev\Doctrine\DBAL\Types\InetArray
            macaddr: MartinGeorgiev\Doctrine\DBAL\Types\Macaddr
            'macaddr[]': MartinGeorgiev\Doctrine\DBAL\Types\MacaddrArray
            macaddr8: MartinGeorgiev\Doctrine\DBAL\Types\Macaddr8
            'macaddr8[]': MartinGeorgiev\Doctrine\DBAL\Types\Macaddr8Array

            # Geometric types
            box: MartinGeorgiev\Doctrine\DBAL\Types\Box
            'box[]': MartinGeorgiev\Doctrine\DBAL\Types\BoxArray
            circle: MartinGeorgiev\Doctrine\DBAL\Types\Circle
            'circle[]': MartinGeorgiev\Doctrine\DBAL\Types\CircleArray
            line: MartinGeorgiev\Doctrine\DBAL\Types\Line
            'line[]': MartinGeorgiev\Doctrine\DBAL\Types\LineArray
            lseg: MartinGeorgiev\Doctrine\DBAL\Types\Lseg
            'lseg[]': MartinGeorgiev\Doctrine\DBAL\Types\LsegArray
            path: MartinGeorgiev\Doctrine\DBAL\Types\Path
            'path[]': MartinGeorgiev\Doctrine\DBAL\Types\PathArray
            point: MartinGeorgiev\Doctrine\DBAL\Types\Point
            'point[]': MartinGeorgiev\Doctrine\DBAL\Types\PointArray
            polygon: MartinGeorgiev\Doctrine\DBAL\Types\Polygon
            'polygon[]': MartinGeorgiev\Doctrine\DBAL\Types\PolygonArray

            # Cube types
            cube: MartinGeorgiev\Doctrine\DBAL\Types\Cube
            'cube[]': MartinGeorgiev\Doctrine\DBAL\Types\CubeArray

            # PostGIS spatial types
            geometry: MartinGeorgiev\Doctrine\DBAL\Types\Geometry
            'geometry[]': MartinGeorgiev\Doctrine\DBAL\Types\GeometryArray
            geography: MartinGeorgiev\Doctrine\DBAL\Types\Geography
            'geography[]': MartinGeorgiev\Doctrine\DBAL\Types\GeographyArray

            # Range types
            daterange: MartinGeorgiev\Doctrine\DBAL\Types\DateRange
            'daterange[]': MartinGeorgiev\Doctrine\DBAL\Types\DateRangeArray
            int4range: MartinGeorgiev\Doctrine\DBAL\Types\Int4Range
            'int4range[]': MartinGeorgiev\Doctrine\DBAL\Types\Int4RangeArray
            int8range: MartinGeorgiev\Doctrine\DBAL\Types\Int8Range
            'int8range[]': MartinGeorgiev\Doctrine\DBAL\Types\Int8RangeArray
            numrange: MartinGeorgiev\Doctrine\DBAL\Types\NumRange
            'numrange[]': MartinGeorgiev\Doctrine\DBAL\Types\NumRangeArray
            tsrange: MartinGeorgiev\Doctrine\DBAL\Types\TsRange
            'tsrange[]': MartinGeorgiev\Doctrine\DBAL\Types\TsRangeArray
            tstzrange: MartinGeorgiev\Doctrine\DBAL\Types\TstzRange
            'tstzrange[]': MartinGeorgiev\Doctrine\DBAL\Types\TstzRangeArray

            # Multirange types
            datemultirange: MartinGeorgiev\Doctrine\DBAL\Types\DateMultirange
            'datemultirange[]': MartinGeorgiev\Doctrine\DBAL\Types\DateMultirangeArray
            int4multirange: MartinGeorgiev\Doctrine\DBAL\Types\Int4Multirange
            'int4multirange[]': MartinGeorgiev\Doctrine\DBAL\Types\Int4MultirangeArray
            int8multirange: MartinGeorgiev\Doctrine\DBAL\Types\Int8Multirange
            'int8multirange[]': MartinGeorgiev\Doctrine\DBAL\Types\Int8MultirangeArray
            nummultirange: MartinGeorgiev\Doctrine\DBAL\Types\NumMultirange
            'nummultirange[]': MartinGeorgiev\Doctrine\DBAL\Types\NumMultirangeArray
            tsmultirange: MartinGeorgiev\Doctrine\DBAL\Types\TsMultirange
            'tsmultirange[]': MartinGeorgiev\Doctrine\DBAL\Types\TsMultirangeArray
            tstzmultirange: MartinGeorgiev\Doctrine\DBAL\Types\TstzMultirange
            'tstzmultirange[]': MartinGeorgiev\Doctrine\DBAL\Types\TstzMultirangeArray

            # Case-insensitive text types
            citext: MartinGeorgiev\Doctrine\DBAL\Types\Citext
            'citext[]': MartinGeorgiev\Doctrine\DBAL\Types\CitextArray

            # ULID types
            ulid: MartinGeorgiev\Doctrine\DBAL\Types\Ulid
            'ulid[]': MartinGeorgiev\Doctrine\DBAL\Types\UlidArray

            # Text search types
            tsquery: MartinGeorgiev\Doctrine\DBAL\Types\Tsquery
            'tsquery[]': MartinGeorgiev\Doctrine\DBAL\Types\TsqueryArray
            tsvector: MartinGeorgiev\Doctrine\DBAL\Types\Tsvector
            'tsvector[]': MartinGeorgiev\Doctrine\DBAL\Types\TsvectorArray

            # Monetary types
            money: MartinGeorgiev\Doctrine\DBAL\Types\Money
            'money[]': MartinGeorgiev\Doctrine\DBAL\Types\MoneyArray

            # Key-value types
            hstore: MartinGeorgiev\Doctrine\DBAL\Types\Hstore
            'hstore[]': MartinGeorgiev\Doctrine\DBAL\Types\HstoreArray

            # Hierarchical types
            lquery: MartinGeorgiev\Doctrine\DBAL\Types\Lquery
            'lquery[]': MartinGeorgiev\Doctrine\DBAL\Types\LqueryArray
            ltree: MartinGeorgiev\Doctrine\DBAL\Types\Ltree
            'ltree[]': MartinGeorgiev\Doctrine\DBAL\Types\LtreeArray
            ltxtquery: MartinGeorgiev\Doctrine\DBAL\Types\Ltxtquery
            'ltxtquery[]': MartinGeorgiev\Doctrine\DBAL\Types\LtxtqueryArray

            # XML types
            xml: MartinGeorgiev\Doctrine\DBAL\Types\Xml
            'xml[]': MartinGeorgiev\Doctrine\DBAL\Types\XmlArray

            # Vector types
            halfvec: MartinGeorgiev\Doctrine\DBAL\Types\Halfvec
            sparsevec: MartinGeorgiev\Doctrine\DBAL\Types\Sparsevec
            vector: MartinGeorgiev\Doctrine\DBAL\Types\Vector
```

> **User-defined enum types**: For each PostgreSQL native `ENUM` type, create a concrete class extending `MartinGeorgiev\Doctrine\DBAL\Types\Enum` and register it like the examples above.
> Columns holding an array of that enum get a second concrete class extending `MartinGeorgiev\Doctrine\DBAL\Types\EnumArray`, registered alongside the scalar one:
>
> ```yaml
> doctrine:
>     dbal:
>         types:
>             status: App\Doctrine\Type\StatusType
>             status[]: App\Doctrine\Type\StatusArrayType
> ```
>
> See [ENUM-TYPE.md](ENUM-TYPE.md) for a full example.

> **User-defined composite types**: For each PostgreSQL composite (row) type, create a concrete class extending `MartinGeorgiev\Doctrine\DBAL\Types\Composite` and register it like the examples above.
> Columns holding an array of that composite get a second concrete class extending `MartinGeorgiev\Doctrine\DBAL\Types\CompositeArray`, registered alongside the scalar one:
>
> ```yaml
> doctrine:
>     dbal:
>         types:
>             inventory_item: App\Doctrine\Type\InventoryItemType
>             inventory_item[]: App\Doctrine\Type\InventoryItemArrayType
> ```
>
> See [COMPOSITE-TYPE.md](COMPOSITE-TYPE.md) for a full example.


### Configure Type Mappings

Add mapping between DBAL and PostgreSQL data types. PostgreSQL normally prefixes array data-types with `_`. Note the PostgreSQL-specific naming for integers (`int2`, `int4`, `int8`).

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        connections:
            default:
                mapping_types:
                    # Binary type mappings
                    bytea: !php/const MartinGeorgiev\Doctrine\DBAL\Type::BYTEA
                    'bytea[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::BYTEA_ARRAY
                    _bytea: !php/const MartinGeorgiev\Doctrine\DBAL\Type::BYTEA_ARRAY

                    # Bit type mappings
                    bit: !php/const MartinGeorgiev\Doctrine\DBAL\Type::BIT
                    'bit[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::BIT_ARRAY
                    _bit: !php/const MartinGeorgiev\Doctrine\DBAL\Type::BIT_ARRAY
                    'bit varying': !php/const MartinGeorgiev\Doctrine\DBAL\Type::BIT_VARYING
                    varbit: !php/const MartinGeorgiev\Doctrine\DBAL\Type::BIT_VARYING
                    'bit varying[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::BIT_VARYING_ARRAY
                    _varbit: !php/const MartinGeorgiev\Doctrine\DBAL\Type::BIT_VARYING_ARRAY

                    # Array type mappings
                    'bool[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::BOOL_ARRAY
                    _bool: !php/const MartinGeorgiev\Doctrine\DBAL\Type::BOOL_ARRAY
                    'smallint[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::SMALLINT_ARRAY
                    _int2: !php/const MartinGeorgiev\Doctrine\DBAL\Type::SMALLINT_ARRAY
                    'integer[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::INTEGER_ARRAY
                    _int4: !php/const MartinGeorgiev\Doctrine\DBAL\Type::INTEGER_ARRAY
                    'bigint[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::BIGINT_ARRAY
                    _int8: !php/const MartinGeorgiev\Doctrine\DBAL\Type::BIGINT_ARRAY
                    'double precision[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::DOUBLE_PRECISION_ARRAY
                    _float8: !php/const MartinGeorgiev\Doctrine\DBAL\Type::DOUBLE_PRECISION_ARRAY
                    'numeric[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::NUMERIC_ARRAY
                    _numeric: !php/const MartinGeorgiev\Doctrine\DBAL\Type::NUMERIC_ARRAY
                    'real[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::REAL_ARRAY
                    _float4: !php/const MartinGeorgiev\Doctrine\DBAL\Type::REAL_ARRAY
                    'text[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::TEXT_ARRAY
                    _text: !php/const MartinGeorgiev\Doctrine\DBAL\Type::TEXT_ARRAY
                    'uuid[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::UUID_ARRAY
                    _uuid: !php/const MartinGeorgiev\Doctrine\DBAL\Type::UUID_ARRAY
                    'varchar[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::VARCHAR_ARRAY
                    _varchar: !php/const MartinGeorgiev\Doctrine\DBAL\Type::VARCHAR_ARRAY

                    # Case-insensitive text type mappings
                    citext: !php/const MartinGeorgiev\Doctrine\DBAL\Type::CITEXT
                    'citext[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::CITEXT_ARRAY
                    _citext: !php/const MartinGeorgiev\Doctrine\DBAL\Type::CITEXT_ARRAY

                    # ULID type mappings
                    ulid: !php/const MartinGeorgiev\Doctrine\DBAL\Type::ULID
                    'ulid[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::ULID_ARRAY
                    _ulid: !php/const MartinGeorgiev\Doctrine\DBAL\Type::ULID_ARRAY

                    # Datetime array type mappings
                    'date[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::DATE_ARRAY
                    _date: !php/const MartinGeorgiev\Doctrine\DBAL\Type::DATE_ARRAY
                    interval: !php/const MartinGeorgiev\Doctrine\DBAL\Type::INTERVAL
                    'interval[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::INTERVAL_ARRAY
                    _interval: !php/const MartinGeorgiev\Doctrine\DBAL\Type::INTERVAL_ARRAY
                    'time[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::TIME_ARRAY
                    _time: !php/const MartinGeorgiev\Doctrine\DBAL\Type::TIME_ARRAY
                    'timestamp[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::TIMESTAMP_ARRAY
                    _timestamp: !php/const MartinGeorgiev\Doctrine\DBAL\Type::TIMESTAMP_ARRAY
                    'timestamptz[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::TIMESTAMPTZ_ARRAY
                    _timestamptz: !php/const MartinGeorgiev\Doctrine\DBAL\Type::TIMESTAMPTZ_ARRAY
                    timetz: !php/const MartinGeorgiev\Doctrine\DBAL\Type::TIMETZ
                    'timetz[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::TIMETZ_ARRAY
                    _timetz: !php/const MartinGeorgiev\Doctrine\DBAL\Type::TIMETZ_ARRAY

                    # JSON type mappings
                    'json[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::JSON_ARRAY
                    _json: !php/const MartinGeorgiev\Doctrine\DBAL\Type::JSON_ARRAY
                    jsonb: !php/const MartinGeorgiev\Doctrine\DBAL\Type::JSONB
                    'jsonb[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::JSONB_ARRAY
                    _jsonb: !php/const MartinGeorgiev\Doctrine\DBAL\Type::JSONB_ARRAY

                    # Network type mappings
                    cidr: !php/const MartinGeorgiev\Doctrine\DBAL\Type::CIDR
                    'cidr[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::CIDR_ARRAY
                    _cidr: !php/const MartinGeorgiev\Doctrine\DBAL\Type::CIDR_ARRAY
                    inet: !php/const MartinGeorgiev\Doctrine\DBAL\Type::INET
                    'inet[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::INET_ARRAY
                    _inet: !php/const MartinGeorgiev\Doctrine\DBAL\Type::INET_ARRAY
                    macaddr: !php/const MartinGeorgiev\Doctrine\DBAL\Type::MACADDR
                    'macaddr[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::MACADDR_ARRAY
                    _macaddr: !php/const MartinGeorgiev\Doctrine\DBAL\Type::MACADDR_ARRAY
                    macaddr8: !php/const MartinGeorgiev\Doctrine\DBAL\Type::MACADDR8
                    'macaddr8[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::MACADDR8_ARRAY
                    _macaddr8: !php/const MartinGeorgiev\Doctrine\DBAL\Type::MACADDR8_ARRAY

                    # Geometric type mappings
                    box: !php/const MartinGeorgiev\Doctrine\DBAL\Type::BOX
                    'box[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::BOX_ARRAY
                    _box: !php/const MartinGeorgiev\Doctrine\DBAL\Type::BOX_ARRAY
                    circle: !php/const MartinGeorgiev\Doctrine\DBAL\Type::CIRCLE
                    'circle[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::CIRCLE_ARRAY
                    _circle: !php/const MartinGeorgiev\Doctrine\DBAL\Type::CIRCLE_ARRAY
                    line: !php/const MartinGeorgiev\Doctrine\DBAL\Type::LINE
                    'line[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::LINE_ARRAY
                    _line: !php/const MartinGeorgiev\Doctrine\DBAL\Type::LINE_ARRAY
                    lseg: !php/const MartinGeorgiev\Doctrine\DBAL\Type::LSEG
                    'lseg[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::LSEG_ARRAY
                    _lseg: !php/const MartinGeorgiev\Doctrine\DBAL\Type::LSEG_ARRAY
                    path: !php/const MartinGeorgiev\Doctrine\DBAL\Type::PATH
                    'path[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::PATH_ARRAY
                    _path: !php/const MartinGeorgiev\Doctrine\DBAL\Type::PATH_ARRAY
                    point: !php/const MartinGeorgiev\Doctrine\DBAL\Type::POINT
                    'point[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::POINT_ARRAY
                    _point: !php/const MartinGeorgiev\Doctrine\DBAL\Type::POINT_ARRAY
                    polygon: !php/const MartinGeorgiev\Doctrine\DBAL\Type::POLYGON
                    'polygon[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::POLYGON_ARRAY
                    _polygon: !php/const MartinGeorgiev\Doctrine\DBAL\Type::POLYGON_ARRAY

                    # Cube type mappings
                    cube: !php/const MartinGeorgiev\Doctrine\DBAL\Type::CUBE
                    'cube[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::CUBE_ARRAY
                    _cube: !php/const MartinGeorgiev\Doctrine\DBAL\Type::CUBE_ARRAY

                    # PostGIS spatial type mappings
                    geometry: !php/const MartinGeorgiev\Doctrine\DBAL\Type::GEOMETRY
                    'geometry[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::GEOMETRY_ARRAY
                    _geometry: !php/const MartinGeorgiev\Doctrine\DBAL\Type::GEOMETRY_ARRAY
                    geography: !php/const MartinGeorgiev\Doctrine\DBAL\Type::GEOGRAPHY
                    'geography[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::GEOGRAPHY_ARRAY
                    _geography: !php/const MartinGeorgiev\Doctrine\DBAL\Type::GEOGRAPHY_ARRAY

                    # Range type mappings
                    daterange: !php/const MartinGeorgiev\Doctrine\DBAL\Type::DATERANGE
                    'daterange[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::DATERANGE_ARRAY
                    _daterange: !php/const MartinGeorgiev\Doctrine\DBAL\Type::DATERANGE_ARRAY
                    int4range: !php/const MartinGeorgiev\Doctrine\DBAL\Type::INT4RANGE
                    'int4range[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::INT4RANGE_ARRAY
                    _int4range: !php/const MartinGeorgiev\Doctrine\DBAL\Type::INT4RANGE_ARRAY
                    int8range: !php/const MartinGeorgiev\Doctrine\DBAL\Type::INT8RANGE
                    'int8range[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::INT8RANGE_ARRAY
                    _int8range: !php/const MartinGeorgiev\Doctrine\DBAL\Type::INT8RANGE_ARRAY
                    numrange: !php/const MartinGeorgiev\Doctrine\DBAL\Type::NUMRANGE
                    'numrange[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::NUMRANGE_ARRAY
                    _numrange: !php/const MartinGeorgiev\Doctrine\DBAL\Type::NUMRANGE_ARRAY
                    tsrange: !php/const MartinGeorgiev\Doctrine\DBAL\Type::TSRANGE
                    'tsrange[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::TSRANGE_ARRAY
                    _tsrange: !php/const MartinGeorgiev\Doctrine\DBAL\Type::TSRANGE_ARRAY
                    tstzrange: !php/const MartinGeorgiev\Doctrine\DBAL\Type::TSTZRANGE
                    'tstzrange[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::TSTZRANGE_ARRAY
                    _tstzrange: !php/const MartinGeorgiev\Doctrine\DBAL\Type::TSTZRANGE_ARRAY

                    # Multirange type mappings
                    datemultirange: !php/const MartinGeorgiev\Doctrine\DBAL\Type::DATEMULTIRANGE
                    _datemultirange: !php/const MartinGeorgiev\Doctrine\DBAL\Type::DATEMULTIRANGE_ARRAY
                    'datemultirange[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::DATEMULTIRANGE_ARRAY
                    int4multirange: !php/const MartinGeorgiev\Doctrine\DBAL\Type::INT4MULTIRANGE
                    'int4multirange[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::INT4MULTIRANGE_ARRAY
                    _int4multirange: !php/const MartinGeorgiev\Doctrine\DBAL\Type::INT4MULTIRANGE_ARRAY
                    int8multirange: !php/const MartinGeorgiev\Doctrine\DBAL\Type::INT8MULTIRANGE
                    'int8multirange[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::INT8MULTIRANGE_ARRAY
                    _int8multirange: !php/const MartinGeorgiev\Doctrine\DBAL\Type::INT8MULTIRANGE_ARRAY
                    nummultirange: !php/const MartinGeorgiev\Doctrine\DBAL\Type::NUMMULTIRANGE
                    'nummultirange[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::NUMMULTIRANGE_ARRAY
                    _nummultirange: !php/const MartinGeorgiev\Doctrine\DBAL\Type::NUMMULTIRANGE_ARRAY
                    tsmultirange: !php/const MartinGeorgiev\Doctrine\DBAL\Type::TSMULTIRANGE
                    'tsmultirange[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::TSMULTIRANGE_ARRAY
                    _tsmultirange: !php/const MartinGeorgiev\Doctrine\DBAL\Type::TSMULTIRANGE_ARRAY
                    tstzmultirange: !php/const MartinGeorgiev\Doctrine\DBAL\Type::TSTZMULTIRANGE
                    'tstzmultirange[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::TSTZMULTIRANGE_ARRAY
                    _tstzmultirange: !php/const MartinGeorgiev\Doctrine\DBAL\Type::TSTZMULTIRANGE_ARRAY

                    # Text search type mappings
                    tsquery: !php/const MartinGeorgiev\Doctrine\DBAL\Type::TSQUERY
                    'tsquery[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::TSQUERY_ARRAY
                    _tsquery: !php/const MartinGeorgiev\Doctrine\DBAL\Type::TSQUERY_ARRAY
                    tsvector: !php/const MartinGeorgiev\Doctrine\DBAL\Type::TSVECTOR
                    'tsvector[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::TSVECTOR_ARRAY
                    _tsvector: !php/const MartinGeorgiev\Doctrine\DBAL\Type::TSVECTOR_ARRAY

                    # Monetary type mappings
                    money: !php/const MartinGeorgiev\Doctrine\DBAL\Type::MONEY
                    'money[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::MONEY_ARRAY
                    _money: !php/const MartinGeorgiev\Doctrine\DBAL\Type::MONEY_ARRAY

                    # Key-value type mappings
                    hstore: !php/const MartinGeorgiev\Doctrine\DBAL\Type::HSTORE
                    'hstore[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::HSTORE_ARRAY
                    _hstore: !php/const MartinGeorgiev\Doctrine\DBAL\Type::HSTORE_ARRAY

                    # Hierarchical type mappings
                    lquery: !php/const MartinGeorgiev\Doctrine\DBAL\Type::LQUERY
                    'lquery[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::LQUERY_ARRAY
                    _lquery: !php/const MartinGeorgiev\Doctrine\DBAL\Type::LQUERY_ARRAY
                    ltree: !php/const MartinGeorgiev\Doctrine\DBAL\Type::LTREE
                    'ltree[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::LTREE_ARRAY
                    _ltree: !php/const MartinGeorgiev\Doctrine\DBAL\Type::LTREE_ARRAY
                    ltxtquery: !php/const MartinGeorgiev\Doctrine\DBAL\Type::LTXTQUERY
                    'ltxtquery[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::LTXTQUERY_ARRAY
                    _ltxtquery: !php/const MartinGeorgiev\Doctrine\DBAL\Type::LTXTQUERY_ARRAY

                    # XML type mappings
                    xml: !php/const MartinGeorgiev\Doctrine\DBAL\Type::XML
                    'xml[]': !php/const MartinGeorgiev\Doctrine\DBAL\Type::XML_ARRAY
                    _xml: !php/const MartinGeorgiev\Doctrine\DBAL\Type::XML_ARRAY

                    # Vector type mappings
                    halfvec: !php/const MartinGeorgiev\Doctrine\DBAL\Type::HALFVEC
                    sparsevec: !php/const MartinGeorgiev\Doctrine\DBAL\Type::SPARSEVEC
                    vector: !php/const MartinGeorgiev\Doctrine\DBAL\Type::VECTOR
```


### Register DQL Functions

Register the functions you'll use in your DQL queries. The full set of available functions and operators can be found in the [Available Functions and Operators](AVAILABLE-FUNCTIONS-AND-OPERATORS.md) documentation and its specialized sub-pages:
- [Array and JSON Functions](ARRAY-AND-JSON-FUNCTIONS.md)
- [PostGIS Spatial Functions](SPATIAL-FUNCTIONS-AND-OPERATORS.md)
- [Text and Pattern Functions](TEXT-AND-PATTERN-FUNCTIONS.md)
- [Date and Range Functions](DATE-AND-RANGE-FUNCTIONS.md)
- [Mathematical Functions](MATHEMATICAL-FUNCTIONS.md)
- [Utility Functions](UTILITY-FUNCTIONS.md)

```yaml
# config/packages/doctrine.yaml
doctrine:
    orm:
        entity_managers:
            your_connection:
                dql:
                    string_functions:
                        # alternative implementation of ALL() and ANY() where subquery is not required, useful for arrays
                        ALL_OF: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\All
                        ANY_OF: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Any

                        # operators for working with array and json(b) data
                        GREATEST: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Greatest
                        LEAST: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Least
                        CONTAINS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Contains # @>
                        IS_CONTAINED_BY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IsContainedBy # <@
                        OVERLAPS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Overlaps # &&
                        RIGHT_EXISTS_ON_LEFT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TheRightExistsOnTheLeft # ?
                        ALL_ON_RIGHT_EXIST_ON_LEFT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AllOnTheRightExistOnTheLeft # ?&
                        ANY_ON_RIGHT_EXISTS_ON_LEFT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AnyOnTheRightExistsOnTheLeft # ?|
                        RETURNS_VALUE_FOR_JSON_VALUE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ReturnsValueForJsonValue # @?
                        DELETE_AT_PATH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DeleteAtPath # #-

                        # array and string specific functions
                        IN_ARRAY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\InArray
                        ANY_VALUE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AnyValue
                        ARRAY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr
                        ARRAY_APPEND: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAppend
                        ARRAY_CARDINALITY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCardinality
                        ARRAY_CAT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCat
                        ARRAY_DIMENSIONS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayDimensions
                        ARRAY_FILL: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayFill
                        ARRAY_LENGTH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLength
                        ARRAY_LOWER: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLower
                        ARRAY_NUMBER_OF_DIMENSIONS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayNumberOfDimensions
                        ARRAY_POSITION: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPosition
                        ARRAY_POSITIONS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPositions
                        ARRAY_PREPEND: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPrepend
                        ARRAY_REMOVE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayRemove
                        ARRAY_REPLACE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReplace
                        ARRAY_REVERSE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReverse
                        ARRAY_SAMPLE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArraySample
                        ARRAY_SHUFFLE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayShuffle
                        ARRAY_SORT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArraySort
                        ARRAY_TO_JSON: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToJson
                        ARRAY_TO_STRING: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToString
                        ARRAY_UPPER: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayUpper
                        ASCII: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ascii
                        BTRIM: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Btrim
                        CASEFOLD: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Casefold
                        CHAR_LENGTH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\CharLength
                        CHR: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Chr
                        CONCAT_WS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ConcatWs
                        DECODE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Decode
                        ENCODE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Encode
                        FORMAT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Format
                        INITCAP: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Initcap
                        LEFT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Left
                        LPAD: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Lpad
                        LTRIM: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltrim
                        OCTET_LENGTH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\OctetLength
                        QUOTE_IDENT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\QuoteIdent
                        QUOTE_LITERAL: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\QuoteLiteral
                        QUOTE_NULLABLE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\QuoteNullable
                        REPEAT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Repeat
                        REVERSE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Reverse
                        RIGHT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Right
                        RPAD: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Rpad
                        RTRIM: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Rtrim
                        SPLIT_PART: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SplitPart
                        STARTS_WITH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StartsWith
                        ROW: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Row
                        STRPOS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Strpos
                        STRING_TO_ARRAY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringToArray
                        TRANSLATE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Translate
                        UNNEST: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unnest

                        # json specific functions
                        JSON_ARRAY_LENGTH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonArrayLength
                        JSON_BUILD_ARRAY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonBuildArray
                        JSON_BUILD_OBJECT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonBuildObject
                        JSON_EACH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEach
                        JSON_EACH_TEXT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEachText
                        JSON_EXISTS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonExists
                        JSON_EXTRACT_PATH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonExtractPath
                        JSON_EXTRACT_PATH_TEXT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonExtractPathText
                        JSON_GET_FIELD: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField
                        JSON_GET_FIELD_AS_INTEGER: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsInteger
                        JSON_GET_FIELD_AS_TEXT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsText
                        JSON_GET_OBJECT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObject
                        JSON_GET_OBJECT_AS_TEXT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObjectAsText
                        JSON_OBJECT_KEYS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonObjectKeys
                        JSON_QUERY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonQuery
                        JSON_SCALAR: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonScalar
                        JSON_SERIALIZE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonSerialize
                        JSON_STRIP_NULLS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonStripNulls
                        JSON_TYPEOF: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonTypeof
                        JSON_VALUE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonValue
                        TO_JSON: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJson
                        ROW_TO_JSON: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RowToJson

                        # jsonb specific functions
                        JSONB_ARRAY_ELEMENTS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElements
                        JSONB_ARRAY_ELEMENTS_TEXT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElementsText
                        JSONB_ARRAY_LENGTH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayLength
                        JSONB_BUILD_ARRAY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbBuildArray
                        JSONB_BUILD_OBJECT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbBuildObject
                        JSONB_EACH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEach
                        JSONB_EACH_TEXT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEachText
                        JSONB_EXISTS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExists
                        JSONB_EXTRACT_PATH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExtractPath
                        JSONB_EXTRACT_PATH_TEXT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExtractPathText
                        JSONB_INSERT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbInsert
                        JSONB_OBJECT_KEYS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectKeys
                        JSONB_PATH_EXISTS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathExists
                        JSONB_PATH_MATCH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathMatch
                        JSONB_PATH_QUERY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathQuery
                        JSONB_PATH_QUERY_ARRAY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathQueryArray
                        JSONB_PATH_QUERY_FIRST: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathQueryFirst
                        JSONB_PRETTY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPretty
                        JSONB_SET: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbSet
                        JSONB_SET_LAX: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbSetLax
                        JSONB_STRIP_NULLS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbStripNulls
                        JSONB_TYPEOF: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbTypeof
                        TO_JSONB: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJsonb

                        # text search specific
                        ARRAY_TO_TSVECTOR: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToTsvector
                        JSON_TO_TSVECTOR: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonToTsvector
                        JSONB_TO_TSVECTOR: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbToTsvector
                        PHRASETO_TSQUERY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PhrasetoTsquery
                        PLAINTO_TSQUERY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PlaintoTsquery
                        SETWEIGHT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Setweight
                        STRIP: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Strip
                        TO_TSQUERY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery
                        TO_TSVECTOR: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector
                        TS_HEADLINE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TsHeadline
                        TS_RANK: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TsRank
                        TS_RANK_CD: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TsRankCd
                        TSMATCH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsmatch
                        TSQUERY_PHRASE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TsqueryPhrase
                        TSVECTOR_TO_ARRAY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TsvectorToArray
                        WEBSEARCH_TO_TSQUERY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\WebsearchToTsquery

                        # date specific functions
                        AGE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Age
                        AT_TIME_ZONE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AtTimeZone
                        CLOCK_TIMESTAMP: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ClockTimestamp
                        DATE_ADD: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateAdd
                        DATE_BIN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateBin
                        DATE_EXTRACT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateExtract
                        DATE_OVERLAPS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateOverlaps
                        DATE_PART: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DatePart
                        DATE_SUBTRACT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateSubtract
                        DATE_TRUNC: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateTrunc
                        GENERATE_TIME_SERIES: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\GenerateTimeSeries
                        ISFINITE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Isfinite
                        JUSTIFY_DAYS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JustifyDays
                        JUSTIFY_HOURS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JustifyHours
                        JUSTIFY_INTERVAL: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JustifyInterval
                        MAKE_DATE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeDate
                        MAKE_TIME: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeTime
                        MAKE_TIMESTAMP: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeTimestamp
                        MAKE_TIMESTAMPTZ: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeTimestamptz
                        STATEMENT_TIMESTAMP: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StatementTimestamp
                        TRANSACTION_TIMESTAMP: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TransactionTimestamp

                        # range functions
                        DATERANGE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Daterange
                        INT4RANGE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int4range
                        INT8RANGE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int8range
                        NUMRANGE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Numrange
                        TSRANGE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsrange
                        TSTZRANGE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tstzrange
                        
                        # Arithmetic functions
                        CBRT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cbrt
                        CEIL: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ceil
                        DEGREES: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Degrees
                        DIV: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Div
                        ERF: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Erf
                        ERFC: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Erfc
                        EXP: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exp
                        FACTORIAL: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Factorial
                        FLOOR: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Floor
                        GAMMA: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Gamma
                        GCD: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Gcd
                        GENERATE_NUMERIC_SERIES: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\GenerateNumericSeries
                        LCM: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Lcm
                        LGAMMA: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Lgamma
                        LN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ln
                        LOG: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Log
                        MIN_SCALE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MinScale
                        PI: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Pi
                        POWER: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Power
                        RADIANS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Radians
                        RANDOM: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Random
                        RANDOM_NORMAL: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RandomNormal
                        ROUND: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Round
                        SCALE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Scale
                        SIGN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sign
                        TRIM_SCALE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TrimScale
                        TRUNC: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trunc
                        WIDTH_BUCKET: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\WidthBucket

                        # Trigonometric functions
                        ACOS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Acos
                        ACOSD: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Acosd
                        ACOSH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Acosh
                        ASIN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Asin
                        ASIND: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Asind
                        ASINH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Asinh
                        ATAN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Atan
                        ATAN2: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Atan2
                        ATAN2D: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Atan2d
                        ATAND: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Atand
                        ATANH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Atanh
                        COS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cos
                        COSD: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cosd
                        COSH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cosh
                        COT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cot
                        COTD: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cotd
                        SIN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sin
                        SIND: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sind
                        SINH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sinh
                        TAN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tan
                        TAND: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tand
                        TANH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tanh

                        # other operators
                        COMPOSITE_FIELD: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\CompositeField
                        ILIKE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ilike
                        SIMILAR_TO: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SimilarTo
                        NOT_SIMILAR_TO: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotSimilarTo
                        UNACCENT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unaccent
                        REGEXP: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Regexp
                        IREGEXP: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IRegexp
                        NOT_REGEXP: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotRegexp
                        NOT_IREGEXP: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotIRegexp
                        REGEXP_COUNT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpCount
                        REGEXP_INSTR: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpInstr
                        REGEXP_LIKE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpLike
                        REGEXP_MATCH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpMatch
                        REGEXP_REPLACE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpReplace
                        REGEXP_SUBSTR: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpSubstr
                        STRCONCAT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StrConcat
                        DISTANCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Distance

                        # vector distance functions
                        COSINE_DISTANCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Vector\CosineDistance
                        INNER_PRODUCT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Vector\InnerProduct
                        L2_DISTANCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Vector\L2Distance

                        # ltree functions
                        INDEX: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Index
                        LCA: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Lca
                        LTREE2TEXT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Ltree2text
                        NLEVEL: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Nlevel
                        SUBLTREE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Subltree
                        SUBPATH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Subpath
                        TEXT2LTREE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Text2ltree

                        # ltree match operators
                        MATCHES_LQUERY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\MatchesLquery # ~
                        MATCHES_LTXTQUERY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\MatchesLtxtquery # @
                        MATCHES_ANY_LQUERY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\MatchesAnyLquery # ?

                        # hstore functions
                        HSTORE_AKEYS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\Akeys
                        HSTORE_AVALS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\Avals
                        HSTORE_DEFINED: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\Defined
                        HSTORE_DELETE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\Delete
                        HSTORE_SKEYS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\Skeys
                        HSTORE_SVALS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\Svals
                        HSTORE_TO_JSON: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\HstoreToJson
                        HSTORE_TO_JSON_LOOSE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\HstoreToJsonLoose

                        # PostGIS functions
                        ST_3DDFULLYWITHIN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DDFullyWithin
                        ST_3DDISTANCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DDistance
                        ST_3DDWITHIN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DDWithin
                        ST_3DINTERSECTS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DIntersects
                        ST_3DLENGTH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DLength
                        ST_3DPERIMETER: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DPerimeter
                        ST_AREA: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area
                        ST_ASEWKT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsEWKT
                        ST_ASGEOJSON: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsGeoJSON
                        ST_ASTEXT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_AsText
                        ST_AZIMUTH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Azimuth
                        ST_BOUNDARY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Boundary
                        ST_BUFFER: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Buffer
                        ST_CENTROID: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Centroid
                        ST_CLIPBYBOX2D: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ClipByBox2D
                        ST_CLOSESTPOINT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ClosestPoint
                        ST_COLLECT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Collect
                        ST_COLLECTIONEXTRACT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CollectionExtract
                        ST_COLLECTIONHOMOGENIZE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CollectionHomogenize
                        ST_CONCAVEHULL: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ConcaveHull
                        ST_CONTAINS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Contains
                        ST_CONTAINSPROPERLY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ContainsProperly
                        ST_CONVEXHULL: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ConvexHull
                        ST_COORDDIM: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CoordDim
                        ST_COVERAGEUNION: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CoverageUnion
                        ST_COVEREDBY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CoveredBy
                        ST_COVERS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Covers
                        ST_CROSSES: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Crosses
                        ST_CURVEN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CurveN
                        ST_CURVETOLINE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CurveToLine
                        ST_DFULLYWITHIN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_DFullyWithin
                        ST_DIFFERENCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Difference
                        ST_DISJOINT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Disjoint
                        ST_DISTANCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Distance
                        ST_DWITHIN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_DWithin
                        ST_ENDPOINT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_EndPoint
                        ST_ENVELOPE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Envelope
                        ST_EQUALS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals
                        ST_EXTERIORRING: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ExteriorRing
                        ST_FLIPCOORDINATES: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_FlipCoordinates
                        ST_FORCE2D: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Force2D
                        ST_FORCE3D: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Force3D
                        ST_FORCE4D: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Force4D
                        ST_FRECHETDISTANCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_FrechetDistance
                        ST_GENERATEPOINTS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeneratePoints
                        ST_GEOMETRYN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeometryN
                        ST_GEOMETRYTYPE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeometryType
                        ST_GEOMFROMEWKT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromEWKT
                        ST_GEOMFROMGEOJSON: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromGeoJSON
                        ST_GEOMFROMTEXT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_GeomFromText
                        ST_HASM: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_HasM
                        ST_HASZ: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_HasZ
                        ST_HAUSDORFFDISTANCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_HausdorffDistance
                        ST_INTERIORRINGN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_InteriorRingN
                        ST_INTERSECTION: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Intersection
                        ST_INTERSECTS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Intersects
                        ST_ISCLOSED: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_IsClosed
                        ST_ISEMPTY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_IsEmpty
                        ST_ISVALID: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_IsValid
                        ST_ISVALIDREASON: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_IsValidReason
                        ST_LENGTH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length
                        ST_LENGTH2D: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length2D
                        ST_LETTERS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Letters
                        ST_LINECROSSINGDIRECTION: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineCrossingDirection
                        ST_LINEEXTEND: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineExtend
                        ST_LINEINTERPOLATEPOINT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineInterpolatePoint
                        ST_LINELOCATEPOINT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineLocatePoint
                        ST_LINESUBSTRING: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineSubstring
                        ST_LINETOCURVE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineToCurve
                        ST_MAKEENVELOPE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_MakeEnvelope
                        ST_MAKELINE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_MakeLine
                        ST_MAKEPOINT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_MakePoint
                        ST_MAKEVALID: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_MakeValid
                        ST_MAXDISTANCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_MaxDistance
                        ST_MINIMUMBOUNDINGCIRCLE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_MinimumBoundingCircle
                        ST_NPOINTS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_NPoints
                        ST_NUMCURVES: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_NumCurves
                        ST_NUMGEOMETRIES: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_NumGeometries
                        ST_NUMINTERIORRINGS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_NumInteriorRings
                        ST_NUMPOINTS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_NumPoints
                        ST_OFFSETCURVE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_OffsetCurve
                        ST_ORDERINGEQUALS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_OrderingEquals
                        ST_OVERLAPS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Overlaps
                        ST_PERIMETER: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Perimeter
                        ST_POINT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Point
                        ST_POINTINSIDECIRCLE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_PointInsideCircle
                        ST_POINTN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_PointN
                        ST_POINTONSURFACE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_PointOnSurface
                        ST_PROJECT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Project
                        ST_RELATE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Relate
                        ST_RELATEMATCH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_RelateMatch
                        ST_REMOVEIRRELEVANTPOINTSFORVIEW: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_RemoveIrrelevantPointsForView
                        ST_REMOVESMALLPARTS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_RemoveSmallParts
                        ST_REVERSE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Reverse
                        ST_ROTATE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Rotate
                        ST_SCALE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Scale
                        ST_SEGMENTIZE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Segmentize
                        ST_SETSRID: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SetSRID
                        ST_SHORTESTLINE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ShortestLine
                        ST_SIMPLIFY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Simplify
                        ST_SIMPLIFYPOLYGONHULL: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SimplifyPolygonHull
                        ST_SIMPLIFYPRESERVETOPOLOGY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SimplifyPreserveTopology
                        ST_SIMPLIFYVW: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SimplifyVW
                        ST_SNAP: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Snap
                        ST_SPLIT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Split
                        ST_SRID: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SRID
                        ST_STARTPOINT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_StartPoint
                        ST_SUBDIVIDE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Subdivide
                        ST_SYMDIFFERENCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SymDifference
                        ST_TILEENVELOPE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_TileEnvelope
                        ST_TOUCHES: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Touches
                        ST_TRANSFORM: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Transform
                        ST_TRANSLATE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Translate
                        ST_TRIANGULATEPOLYGON: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_TriangulatePolygon
                        ST_UNARYUNION: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_UnaryUnion
                        ST_UNION: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Union
                        ST_VORONOIPOLYGONS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_VoronoiPolygons
                        ST_WITHIN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Within
                        ST_X: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_X
                        ST_Y: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Y
                        ST_Z: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Z

                        # PostGIS operators
                        BOUNDING_BOX_DISTANCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\BoundingBoxDistance # <#>
                        GEOMETRY_DISTANCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\GeometryDistance # <->
                        ND_CENTROID_DISTANCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\NDimensionalCentroidDistance # <<->>
                        ND_OVERLAPS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\NDimensionalOverlaps # &&&
                        OVERLAPS_ABOVE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\OverlapsAbove # |&>
                        OVERLAPS_BELOW: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\OverlapsBelow # &<|
                        OVERLAPS_LEFT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\OverlapsLeft # &<
                        OVERLAPS_RIGHT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\OverlapsRight # &>
                        SPATIAL_CONTAINED_BY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\SpatialContainedBy # @
                        SPATIAL_CONTAINS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\SpatialContains # ~
                        SPATIAL_SAME: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\SpatialSame # ~=
                        STRICTLY_ABOVE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\StrictlyAbove # |>>
                        STRICTLY_BELOW: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\StrictlyBelow # <<|
                        STRICTLY_LEFT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\StrictlyLeft # <<
                        STRICTLY_RIGHT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\StrictlyRight # >>
                        TRAJECTORY_DISTANCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\TrajectoryDistance # |=|

                        # pg_trgm functions
                        SIMILARITY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\Similarity
                        STRICT_WORD_SIMILARITY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\StrictWordSimilarity
                        WORD_SIMILARITY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\WordSimilarity

                        # pg_trgm operators
                        ARE_SIMILAR: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\AreSimilar # %
                        CONTAINS_STRICT_WORD_SIMILAR_TO: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\ContainsStrictWordSimilarTo # %>>
                        CONTAINS_WORD_SIMILAR_TO: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\ContainsWordSimilarTo # %>
                        IS_STRICT_WORD_SIMILAR_TO: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\IsStrictWordSimilarTo # <<%
                        IS_WORD_SIMILAR_TO: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\IsWordSimilarTo # <%
                        REVERSE_STRICT_WORD_SIMILARITY_DISTANCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\ReverseStrictWordSimilarityDistance # <->>>
                        REVERSE_WORD_SIMILARITY_DISTANCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\ReverseWordSimilarityDistance # <->>
                        SIMILARITY_DISTANCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\SimilarityDistance # <->
                        STRICT_WORD_SIMILARITY_DISTANCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\StrictWordSimilarityDistance # <<<->
                        WORD_SIMILARITY_DISTANCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\WordSimilarityDistance # <<->

                        # fuzzystrmatch functions
                        DAITCH_MOKOTOFF: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\DaitchMokotoff
                        DIFFERENCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\Difference
                        DMETAPHONE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\Dmetaphone
                        DMETAPHONE_ALT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\DmetaphoneAlt
                        LEVENSHTEIN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\Levenshtein
                        LEVENSHTEIN_LESS_EQUAL: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\LevenshteinLessEqual
                        METAPHONE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\Metaphone
                        SOUNDEX: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\Soundex

                        # network address functions
                        ABBREV: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Abbrev
                        BROADCAST: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Broadcast
                        FAMILY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Family
                        HOST: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Host
                        HOSTMASK: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Hostmask
                        INET_MERGE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\InetMerge
                        INET_SAME_FAMILY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\InetSameFamily
                        MASKLEN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Masklen
                        NETMASK: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Netmask
                        NETWORK: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Network
                        SET_MASKLEN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\SetMasklen

                        # aggregation functions
                        ARRAY_AGG: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAgg
                        BIT_AND: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BitAnd
                        BIT_OR: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BitOr
                        BIT_XOR: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BitXor
                        BOOL_AND: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BoolAnd
                        BOOL_OR: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BoolOr
                        CORR: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Corr
                        COVAR_POP: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\CovarPop
                        COVAR_SAMP: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\CovarSamp
                        EVERY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Every
                        FILTER: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Filter
                        JSON_AGG: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonAgg
                        JSON_OBJECT_AGG: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonObjectAgg
                        JSONB_AGG: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbAgg
                        JSONB_OBJECT_AGG: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectAgg
                        MODE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Mode
                        PERCENTILE_CONT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PercentileCont
                        PERCENTILE_DISC: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PercentileDisc
                        RANGE_AGG: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RangeAgg
                        RANGE_INTERSECT_AGG: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RangeIntersectAgg
                        STDDEV: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Stddev
                        STDDEV_POP: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StddevPop
                        STRING_AGG: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringAgg
                        VAR_POP: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\VarPop
                        VARIANCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Variance

                        # Window functions
                        CUME_DIST: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\CumeDist
                        DENSE_RANK: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DenseRank
                        FIRST_VALUE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\FirstValue
                        LAG: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Lag
                        LAST_VALUE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\LastValue
                        LEAD: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Lead
                        NTH_VALUE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NthValue
                        NTILE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ntile
                        OVER: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Over
                        PERCENT_RANK: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PercentRank
                        RANK: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Rank
                        ROW_NUMBER: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RowNumber

                        # XML functions
                        XML_IS_WELL_FORMED: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlIsWellFormed
                        XML_IS_WELL_FORMED_CONTENT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlIsWellFormedContent
                        XML_IS_WELL_FORMED_DOCUMENT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlIsWellFormedDocument
                        XMLAGG: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlAgg
                        XMLCOMMENT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlComment
                        XMLCONCAT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlConcat
                        XMLEXISTS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlExists
                        XMLPI: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlPi
                        XMLTEXT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlText
                        XPATH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Xpath
                        XPATH_EXISTS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XpathExists

                        # hashing and checksum functions
                        CRC32: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Crc32
                        CRC32C: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Crc32c
                        MD5: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Md5
                        REVERSE_BYTES: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ReverseBytes
                        SHA224: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sha224
                        SHA256: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sha256
                        SHA384: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sha384
                        SHA512: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sha512

                        # uuid functions
                        GEN_RANDOM_UUID: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\GenRandomUuid
                        UUID_EXTRACT_TIMESTAMP: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\UuidExtractTimestamp
                        UUID_EXTRACT_VERSION: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\UuidExtractVersion
                        UUIDV4: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Uuidv4
                        UUIDV7: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Uuidv7

                        # type conversion and formatting functions
                        CAST: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cast
                        TO_CHAR: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToChar
                        TO_DATE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToDate
                        TO_NUMBER: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToNumber
                        TO_TIMESTAMP: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTimestamp
```

### Usage in Entities

Once configured, you can use the PostgreSQL types in your Symfony entities:

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Interval;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Type::JSONB)]
    private array $specifications = [];

    #[ORM\Column(type: Type::TEXT_ARRAY)]
    private array $categories = [];

    #[ORM\Column(type: Type::POINT)]
    private Point $manufacturingLocation;

    #[ORM\Column(type: Type::NUMRANGE)]
    private NumericRange $priceRange;

    #[ORM\Column(type: Type::DATERANGE)]
    private DateRange $availabilityPeriod;

    #[ORM\Column(type: Type::INET)]
    private string $originServerIp;

    #[ORM\Column(type: Type::INTERVAL)]
    private Interval $duration;

    #[ORM\Column(type: Type::MONEY)]
    private string $price;

    #[ORM\Column(type: Type::LTREE)]
    private Ltree $pathFromRoot;

    #[ORM\Column(type: Type::XML)]
    private string $configXml;
}
```

