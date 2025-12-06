## Integration with Symfony

This guide covers integration with Symfony 6.4+ and 7.x using the DoctrineBundle. For older Symfony versions, please refer to previous documentation versions.

### Register DBAL Types

Register the DBAL types you plan to use. The full set of available types can be found in [AVAILABLE-TYPES.md](AVAILABLE-TYPES.md).

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            # Array types
            'bool[]': MartinGeorgiev\Doctrine\DBAL\Types\BooleanArray
            'smallint[]': MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray
            'integer[]': MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray
            'bigint[]': MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray
            'double precision[]': MartinGeorgiev\Doctrine\DBAL\Types\DoublePrecisionArray
            'real[]': MartinGeorgiev\Doctrine\DBAL\Types\RealArray
            'text[]': MartinGeorgiev\Doctrine\DBAL\Types\TextArray

            # JSON types
            jsonb: MartinGeorgiev\Doctrine\DBAL\Types\Jsonb
            'jsonb[]': MartinGeorgiev\Doctrine\DBAL\Types\JsonbArray

            # Network types
            cidr: MartinGeorgiev\Doctrine\DBAL\Types\Cidr
            'cidr[]': MartinGeorgiev\Doctrine\DBAL\Types\CidrArray
            inet: MartinGeorgiev\Doctrine\DBAL\Types\Inet
            'inet[]': MartinGeorgiev\Doctrine\DBAL\Types\InetArray
            macaddr: MartinGeorgiev\Doctrine\DBAL\Types\Macaddr
            'macaddr[]': MartinGeorgiev\Doctrine\DBAL\Types\MacaddrArray

            # Spatial types
            point: MartinGeorgiev\Doctrine\DBAL\Types\Point
            'point[]': MartinGeorgiev\Doctrine\DBAL\Types\PointArray
            geometry: MartinGeorgiev\Doctrine\DBAL\Types\Geometry
            'geometry[]': MartinGeorgiev\Doctrine\DBAL\Types\GeometryArray
            geography: MartinGeorgiev\Doctrine\DBAL\Types\Geography
            'geography[]': MartinGeorgiev\Doctrine\DBAL\Types\GeographyArray

            # Range types
            daterange: MartinGeorgiev\Doctrine\DBAL\Types\DateRange
            int4range: MartinGeorgiev\Doctrine\DBAL\Types\Int4Range
            int8range: MartinGeorgiev\Doctrine\DBAL\Types\Int8Range
            numrange: MartinGeorgiev\Doctrine\DBAL\Types\NumRange
            tsrange: MartinGeorgiev\Doctrine\DBAL\Types\TsRange
            tstzrange: MartinGeorgiev\Doctrine\DBAL\Types\TstzRange

            # Hierarchical types
            ltree: MartinGeorgiev\Doctrine\DBAL\Types\Ltree
```


### Configure Type Mappings

Add mapping between DBAL and PostgreSQL data types. PostgreSQL normally prefixes array data-types with `_`. Note the PostgreSQL-specific naming for integers (`int2`, `int4`, `int8`).

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        connections:
            default:
                mapping_types:
                    # Array type mappings
                    'bool[]': 'bool[]'
                    _bool: 'bool[]'
                    'smallint[]': 'smallint[]'
                    _int2: 'smallint[]'
                    'integer[]': 'integer[]'
                    _int4: 'integer[]'
                    'bigint[]': 'bigint[]'
                    _int8: 'bigint[]'
                    'double precision[]': 'double precision[]'
                    _float8: 'double precision[]'
                    'real[]': 'real[]'
                    _float4: 'real[]'
                    'text[]': 'text[]'
                    _text: 'text[]'

                    # JSON type mappings
                    jsonb: jsonb
                    'jsonb[]': 'jsonb[]'
                    _jsonb: 'jsonb[]'

                    # Network type mappings
                    cidr: cidr
                    'cidr[]': 'cidr[]'
                    _cidr: 'cidr[]'
                    inet: inet
                    'inet[]': 'inet[]'
                    _inet: 'inet[]'
                    macaddr: macaddr
                    'macaddr[]': 'macaddr[]'
                    _macaddr: 'macaddr[]'

                    # Spatial type mappings
                    point: point
                    'point[]': 'point[]'
                    _point: 'point[]'
                    geometry: geometry
                    'geometry[]': 'geometry[]'
                    _geometry: 'geometry[]'
                    geography: geography
                    'geography[]': 'geography[]'
                    _geography: 'geography[]'

                    # Range type mappings
                    daterange: daterange
                    int4range: int4range
                    int8range: int8range
                    numrange: numrange
                    tsrange: tsrange
                    tstzrange: tstzrange

                    # Hierarchical type mappings
                    ltree: ltree
```


### Register DQL Functions

Register the functions you'll use in your DQL queries. The full set of available functions and operators can be found in the [Available Functions and Operators](AVAILABLE-FUNCTIONS-AND-OPERATORS.md) documentation and its specialized sub-pages:
- [Array and JSON Functions](ARRAY-AND-JSON-FUNCTIONS.md)
- [PostGIS Spatial Functions](SPATIAL-FUNCTIONS-AND-OPERATORS.md)
- [Text and Pattern Functions](TEXT-AND-PATTERN-FUNCTIONS.md)
- [Date and Range Functions](DATE-AND-RANGE-FUNCTIONS.md)
- [Mathematical Functions](MATHEMATICAL-FUNCTIONS.md)

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
                        
                        # Arithmetic functions
                        CBRT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cbrt
                        CEIL: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ceil
                        DEGREES: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Degrees
                        EXP: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exp
                        FLOOR: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Floor
                        LN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ln
                        LOG: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Log
                        PI: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Pi
                        POWER: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Power
                        RADIANS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Radians
                        RANDOM: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Random
                        ROUND: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Round
                        SIGN: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sign
                        TRUNC: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trunc
                        WIDTH_BUCKET: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\WidthBucket

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

                        # data type formatting functions
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

    #[ORM\Column(type: Type::LTREE)]
    private Ltree $pathFromRoot;
}
```

### Environment-Specific Configuration

For different environments, you can override configuration in environment-specific files:

```yaml
# config/packages/dev/doctrine.yaml
doctrine:
    dbal:
        logging: true
        profiling: true
    orm:
        auto_generate_proxy_classes: true

# config/packages/prod/doctrine.yaml
doctrine:
    orm:
        auto_generate_proxy_classes: false
        metadata_cache_driver:
            type: pool
            pool: doctrine.system_cache_pool
        query_cache_driver:
            type: pool
            pool: doctrine.system_cache_pool
        result_cache_driver:
            type: pool
            pool: doctrine.result_cache_pool
```

### Service Container Integration

If you need to register types programmatically (e.g., in a bundle), you can do so in a service:

```php
<?php

namespace App\Service;

use Doctrine\DBAL\Types\Type;
use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\Jsonb;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(lazy: true)]
class DoctrineTypeRegistrar
{
    public function registerTypes(): void
    {
        if (!Type::hasType(Type::JSONB)) {
            Type::addType(Type::JSONB, Jsonb::class);
        }

        // Register other types as needed...
    }
}
```
