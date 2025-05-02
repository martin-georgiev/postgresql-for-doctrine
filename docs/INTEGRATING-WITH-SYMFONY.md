## Integration with Symfony


*Register the DBAL types you plan to use*

Full set of the available types can be found [here](AVAILABLE-TYPES.md).

```yaml
# Usually part of config.yml
doctrine:
    dbal:
        types: # register the new types
            bool[]: MartinGeorgiev\Doctrine\DBAL\Types\BooleanArray
            smallint[]: MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray
            integer[]: MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray
            bigint[]: MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray
            
            double precision[]: MartinGeorgiev\Doctrine\DBAL\Types\DoublePrecisionArray
            real[]: MartinGeorgiev\Doctrine\DBAL\Types\RealArray
            
            text[]: MartinGeorgiev\Doctrine\DBAL\Types\TextArray
            jsonb: MartinGeorgiev\Doctrine\DBAL\Types\Jsonb
            jsonb[]: MartinGeorgiev\Doctrine\DBAL\Types\JsonbArray
            
            cidr: MartinGeorgiev\Doctrine\DBAL\Types\Cidr
            cidr[]: MartinGeorgiev\Doctrine\DBAL\Types\CidrArray
            inet: MartinGeorgiev\Doctrine\DBAL\Types\Inet
            inet[]: MartinGeorgiev\Doctrine\DBAL\Types\InetArray
            macaddr: MartinGeorgiev\Doctrine\DBAL\Types\Macaddr
            macaddr[]: MartinGeorgiev\Doctrine\DBAL\Types\MacaddrArray

            point: MartinGeorgiev\Doctrine\DBAL\Types\Point
            point[]: MartinGeorgiev\Doctrine\DBAL\Types\PointArray
```


*Add mapping between DBAL and PostgreSQL data types*

PostgreSQL will normally prefix array data-types with `_`.
Beware of the specific to PostgreSQL primary way of data-type naming for integers (`int2`, `int4`, `int8`).


```yaml
# Usually part of config.yml
doctrine:
    dbal:
        connections:
            your_connection:
                mapping_types:
                    bool[]: bool[]
                    _bool: bool[]
                    smallint[]: smallint[]
                    _int2: smallint[]
                    integer[]: integer[]
                    _int4: integer[]
                    bigint[]: bigint[]
                    _int8: bigint[]
                    
                    double precision[]: double precision[]
                    _float8: double precision[]
                    real[]: real[]
                    _float4: real[]
                    
                    text[]: text[]
                    _text: text[]
                    jsonb: jsonb
                    jsonb[]: jsonb[]
                    _jsonb: jsonb[]
                    
                    cidr: cidr
                    cidr[]: cidr[]
                    _cidr: cidr[]
                    inet: inet
                    inet[]: inet[]
                    _inet: inet[]
                    macaddr: macaddr
                    macaddr[]: macaddr[]
                    _macaddr: macaddr[]

                    point: point
                    point[]: point[]
                    _point: point[]
```


*Register the functions you'll use in your DQL queries*

Full set of the available functions and extra operators can be found [here](AVAILABLE-FUNCTIONS-AND-OPERATORS.md).

```yaml
# Usually part of config.yml
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
                        ARRAY_LENGTH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLength
                        ARRAY_NUMBER_OF_DIMENSIONS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayNumberOfDimensions
                        ARRAY_POSITION: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPosition
                        ARRAY_POSITIONS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPositions
                        ARRAY_PREPEND: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPrepend
                        ARRAY_REMOVE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayRemove
                        ARRAY_REPLACE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReplace
                        ARRAY_SHUFFLE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayShuffle
                        ARRAY_TO_JSON: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToJson
                        ARRAY_TO_STRING: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToString
                        SPLIT_PART: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SplitPart
                        STARTS_WITH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StartsWith
                        STRING_TO_ARRAY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringToArray
                        UNNEST: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unnest

                        # json specific functions
                        JSON_ARRAY_LENGTH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonArrayLength
                        JSON_BUILD_OBJECT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonBuildObject
                        JSON_EACH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEach
                        JSON_EACH_TEXT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEachText
                        JSON_EXISTS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonExists
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
                        JSONB_BUILD_OBJECT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbBuildObject
                        JSONB_EACH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEach
                        JSONB_EACH_TEXT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEachText
                        JSONB_EXISTS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExists
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
                        TO_JSONB: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJsonb

                        # text search specific
                        TO_TSQUERY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery
                        TO_TSVECTOR: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector
                        TSMATCH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsmatch

                        # date specific functions
                        DATE_ADD: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateAdd
                        DATE_BIN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateBin
                        DATE_EXTRACT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateExtract
                        DATE_OVERLAPS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateOverlaps
                        DATE_SUBTRACT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateSubtract

                        # range functions
                        DATERANGE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Daterange
                        INT4RANGE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int4range
                        INT8RANGE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int8range
                        NUMRANGE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Numrange
                        TSRANGE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsrange
                        TSTZRANGE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tstzrange

                        # other operators
                        CAST: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cast
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
                        ROW: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Row
                        STRCONCAT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StrConcat
                        DISTANCE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Distance

                        # aggregation functions
                        ARRAY_AGG: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAgg
                        JSON_AGG: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonAgg
                        JSON_OBJECT_AGG: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonObjectAgg
                        JSONB_AGG: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbAgg
                        JSONB_OBJECT_AGG: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectAgg
                        STRING_AGG: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringAgg
                        XML_AGG: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlAgg
```
