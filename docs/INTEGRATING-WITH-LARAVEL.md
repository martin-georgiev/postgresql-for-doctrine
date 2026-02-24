## Integration with Laravel

This guide covers integration with Laravel 11.x using the community-supported [Laravel Doctrine](https://www.laraveldoctrine.org/) package. Laravel doesn't provide official Doctrine integration, but Laravel Doctrine provides excellent support for modern Laravel versions.

### Installation

First, install Laravel Doctrine ORM:

```bash
composer require laravel-doctrine/orm
```

Then publish the configuration:

```bash
php artisan vendor:publish --tag="config" --provider="LaravelDoctrine\ORM\DoctrineServiceProvider"
```

### Register DBAL Types

Register the DBAL types you plan to use. The **full set** of available types can be found in [AVAILABLE-TYPES.md](AVAILABLE-TYPES.md).

```php
<?php
// config/doctrine.php

return [
    // ... other configuration

    'managers' => [
        'default' => [
            // ... other configuration

            'type_mappings' => [
                // Array type mappings
                '_bool' => 'bool[]',
                'bool[]' => 'bool[]',
                'smallint[]' => 'smallint[]',
                '_int2' => 'smallint[]',
                'integer[]' => 'integer[]',
                '_int4' => 'integer[]',
                'bigint[]' => 'bigint[]',
                '_int8' => 'bigint[]',
                'double precision[]' => 'double precision[]',
                '_float8' => 'double precision[]',
                'real[]' => 'real[]',
                '_float4' => 'real[]',
                '_text' => 'text[]',
                'text[]' => 'text[]',
                'uuid[]' => 'uuid[]',
                '_uuid' => 'uuid[]',

                // JSON type mappings
                'jsonb' => 'jsonb',
                '_jsonb' => 'jsonb[]',
                'jsonb[]' => 'jsonb[]',

                // Network type mappings
                'cidr' => 'cidr',
                'cidr[]' => 'cidr[]',
                '_cidr' => 'cidr[]',
                'inet' => 'inet',
                'inet[]' => 'inet[]',
                '_inet' => 'inet[]',
                'macaddr' => 'macaddr',
                'macaddr[]' => 'macaddr[]',
                '_macaddr' => 'macaddr[]',
                'macaddr8' => 'macaddr8',
                'macaddr8[]' => 'macaddr8[]',
                '_macaddr8' => 'macaddr8[]',

                // Spatial type mappings
                'point' => 'point',
                'point[]' => 'point[]',
                '_point' => 'point[]',
                'geometry' => 'geometry',
                'geometry[]' => 'geometry[]',
                '_geometry' => 'geometry[]',
                'geography' => 'geography',
                'geography[]' => 'geography[]',
                '_geography' => 'geography[]',

                // Range type mappings
                'daterange' => 'daterange',
                'int4range' => 'int4range',
                'int8range' => 'int8range',
                'numrange' => 'numrange',
                'tsrange' => 'tsrange',
                'tstzrange' => 'tstzrange',

                // Text search type mappings
                'tsquery' => 'tsquery',
                'tsvector' => 'tsvector',

                // Hierarchical type mappings
                'ltree' => 'ltree'
            ],
        ],
    ],

    'custom_types' => [
        // Array types
        'bool[]' => MartinGeorgiev\Doctrine\DBAL\Types\BooleanArray::class,
        'bigint[]' => MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray::class,
        'integer[]' => MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray::class,
        'smallint[]' => MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray::class,
        'double precision[]' => MartinGeorgiev\Doctrine\DBAL\Types\DoublePrecisionArray::class,
        'real[]' => MartinGeorgiev\Doctrine\DBAL\Types\RealArray::class,
        'text[]' => MartinGeorgiev\Doctrine\DBAL\Types\TextArray::class,
        'uuid[]' => MartinGeorgiev\Doctrine\DBAL\Types\UuidArray::class,

        // JSON types
        'jsonb' => MartinGeorgiev\Doctrine\DBAL\Types\Jsonb::class,
        'jsonb[]' => MartinGeorgiev\Doctrine\DBAL\Types\JsonbArray::class,

        // Network types
        'cidr' => MartinGeorgiev\Doctrine\DBAL\Types\Cidr::class,
        'cidr[]' => MartinGeorgiev\Doctrine\DBAL\Types\CidrArray::class,
        'inet' => MartinGeorgiev\Doctrine\DBAL\Types\Inet::class,
        'inet[]' => MartinGeorgiev\Doctrine\DBAL\Types\InetArray::class,
        'macaddr' => MartinGeorgiev\Doctrine\DBAL\Types\Macaddr::class,
        'macaddr[]' => MartinGeorgiev\Doctrine\DBAL\Types\MacaddrArray::class,
        'macaddr8' => MartinGeorgiev\Doctrine\DBAL\Types\Macaddr8::class,
        'macaddr8[]' => MartinGeorgiev\Doctrine\DBAL\Types\Macaddr8Array::class,

        // Spatial types
        'point' => MartinGeorgiev\Doctrine\DBAL\Types\Point::class,
        'point[]' => MartinGeorgiev\Doctrine\DBAL\Types\PointArray::class,
        'geometry' => MartinGeorgiev\Doctrine\DBAL\Types\Geometry::class,
        'geometry[]' => MartinGeorgiev\Doctrine\DBAL\Types\GeometryArray::class,
        'geography' => MartinGeorgiev\Doctrine\DBAL\Types\Geography::class,
        'geography[]' => MartinGeorgiev\Doctrine\DBAL\Types\GeographyArray::class,

        // Range types
        'daterange' => MartinGeorgiev\Doctrine\DBAL\Types\DateRange::class,
        'int4range' => MartinGeorgiev\Doctrine\DBAL\Types\Int4Range::class,
        'int8range' => MartinGeorgiev\Doctrine\DBAL\Types\Int8Range::class,
        'numrange' => MartinGeorgiev\Doctrine\DBAL\Types\NumRange::class,
        'tsrange' => MartinGeorgiev\Doctrine\DBAL\Types\TsRange::class,
        'tstzrange' => MartinGeorgiev\Doctrine\DBAL\Types\TstzRange::class,

        // Text search types
        'tsquery' => MartinGeorgiev\Doctrine\DBAL\Types\Tsquery::class,
        'tsvector' => MartinGeorgiev\Doctrine\DBAL\Types\Tsvector::class,

        // Hierarchical types
        'ltree' => MartinGeorgiev\Doctrine\DBAL\Types\Ltree::class,
    ],

    // ... other configuration
];
```


### Register DQL Functions

Register the functions you'll use in your DQL queries. The full set of available functions and operators can be found in the [Available Functions and Operators](AVAILABLE-FUNCTIONS-AND-OPERATORS.md) documentation and its specialized sub-pages:
- [Array and JSON Functions](ARRAY-AND-JSON-FUNCTIONS.md)
- [PostGIS Spatial Functions](SPATIAL-FUNCTIONS-AND-OPERATORS.md)
- [Text and Pattern Functions](TEXT-AND-PATTERN-FUNCTIONS.md)
- [Date and Range Functions](DATE-AND-RANGE-FUNCTIONS.md)
- [Mathematical Functions](MATHEMATICAL-FUNCTIONS.md)

Add the function configuration to your `config/doctrine.php`:

```php
<?php
// config/doctrine.php

return [
    // ... other configuration

    'custom_string_functions' => [
        # alternative implementation of ALL() and ANY() where subquery is not required, useful for arrays
        'ALL_OF' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\All::class,
        'ANY_OF' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Any::class,

        # operators for working with array and json(b) data
        'GREATEST' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Greatest::class,
        'LEAST' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Least::class,
        'CONTAINS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Contains::class, // @>
        'IS_CONTAINED_BY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IsContainedBy::class, // <@
        'OVERLAPS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Overlaps::class, // &&
        'RIGHT_EXISTS_ON_LEFT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TheRightExistsOnTheLeft::class, // ?
        'ALL_ON_RIGHT_EXIST_ON_LEFT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AllOnTheRightExistOnTheLeft::class, // ?&
        'ANY_ON_RIGHT_EXISTS_ON_LEFT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AnyOnTheRightExistsOnTheLeft::class, // ?|
        'RETURNS_VALUE_FOR_JSON_VALUE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ReturnsValueForJsonValue::class, // @?
        'DELETE_AT_PATH' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DeleteAtPath::class, // #-

        # array and string specific functions
        'IN_ARRAY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\InArray::class,
        'ANY_VALUE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AnyValue::class,
        'ARRAY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr::class,
        'ARRAY_APPEND' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAppend::class,
        'ARRAY_CARDINALITY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCardinality::class,
        'ARRAY_CAT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCat::class,
        'ARRAY_DIMENSIONS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayDimensions::class,
        'ARRAY_FILL' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayFill::class,
        'ARRAY_LENGTH' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLength::class,
        'ARRAY_LOWER' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLower::class,
        'ARRAY_NUMBER_OF_DIMENSIONS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayNumberOfDimensions::class,
        'ARRAY_POSITION' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPosition::class,
        'ARRAY_POSITIONS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPositions::class,
        'ARRAY_PREPEND' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPrepend::class,
        'ARRAY_REMOVE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayRemove::class,
        'ARRAY_REPLACE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReplace::class,
        'ARRAY_SAMPLE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArraySample::class,
        'ARRAY_SHUFFLE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayShuffle::class,
        'ARRAY_TO_JSON' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToJson::class,
        'ARRAY_TO_STRING' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToString::class,
        'ARRAY_UPPER' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayUpper::class,
        'SPLIT_PART' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SplitPart::class,
        'STARTS_WITH' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StartsWith::class,
        'STRING_TO_ARRAY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringToArray::class,
        'UNNEST' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unnest::class,

        # json specific functions
        'JSON_ARRAY_LENGTH' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonArrayLength::class,
        'JSON_BUILD_ARRAY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonBuildArray::class,
        'JSON_BUILD_OBJECT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonBuildObject::class,
        'JSON_EACH' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEach::class,
        'JSON_EACH_TEXT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEachText::class,
        'JSON_EXISTS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonExists::class,
        'JSON_GET_FIELD' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField::class,
        'JSON_GET_FIELD_AS_TEXT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsText::class,
        'JSON_GET_FIELD_AS_INTEGER' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsInteger::class,
        'JSON_GET_OBJECT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObject::class,
        'JSON_GET_OBJECT_AS_TEXT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObjectAsText::class,
        'JSON_OBJECT_KEYS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonObjectKeys::class,
        'JSON_QUERY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonQuery::class,
        'JSON_SCALAR' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonScalar::class,
        'JSON_SERIALIZE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonSerialize::class,
        'JSON_STRIP_NULLS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonStripNulls::class,
        'JSON_VALUE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonValue::class,
        'TO_JSON' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJson::class,
        'ROW_TO_JSON' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RowToJson::class,

        # jsonb specific functions
        'JSONB_ARRAY_ELEMENTS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElements::class,
        'JSONB_ARRAY_ELEMENTS_TEXT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElementsText::class,
        'JSONB_ARRAY_LENGTH' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayLength::class,
        'JSONB_BUILD_ARRAY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbBuildArray::class,
        'JSONB_BUILD_OBJECT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbBuildObject::class,
        'JSONB_EACH' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEach::class,
        'JSONB_EACH_TEXT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEachText::class,
        'JSONB_EXISTS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExists::class,
        'JSONB_INSERT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbInsert::class,
        'JSONB_OBJECT_KEYS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectKeys::class,
        'JSONB_PATH_EXISTS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathExists::class,
        'JSONB_PATH_MATCH' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathMatch::class,
        'JSONB_PATH_QUERY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathQuery::class,
        'JSONB_PATH_QUERY_ARRAY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathQueryArray::class,
        'JSONB_PATH_QUERY_FIRST' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathQueryFirst::class,
        'JSONB_PRETTY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPretty::class,
        'JSONB_SET' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbSet::class,
        'JSONB_SET_LAX' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbSetLax::class,
        'JSONB_STRIP_NULLS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbStripNulls::class,
        'TO_JSONB' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJsonb::class,

        # text search specific
        'TO_TSQUERY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery::class,
        'TO_TSVECTOR' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector::class,
        'TSMATCH' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsmatch::class,

        # date specific functions
        'DATE_ADD' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateAdd::class,
        'DATE_BIN' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateBin::class,
        'DATE_EXTRACT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateExtract::class,
        'DATE_OVERLAPS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateOverlaps::class,
        'DATE_SUBTRACT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateSubtract::class,
        'DATE_TRUNC' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateTrunc::class,

        # range functions
        'DATERANGE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Daterange::class,
        'INT4RANGE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int4range::class,
        'INT8RANGE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int8range::class,
        'NUMRANGE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Numrange::class,
        'TSRANGE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsrange::class,
        'TSTZRANGE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tstzrange::class,
        
        # Arithmetic functions
        'CBRT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cbrt::class,
        'CEIL' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ceil::class,
        'DEGREES' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Degrees::class,
        'EXP' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exp::class,
        'FLOOR' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Floor::class,
        'LN' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ln::class,
        'LOG' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Log::class,
        'PI' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Pi::class,
        'POWER' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Power::class,
        'RADIANS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Radians::class,
        'RANDOM' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Random::class,
        'ROUND' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Round::class,
        'SIGN' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sign::class,
        'TRUNC' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trunc::class,
        'WIDTH_BUCKET' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\WidthBucket::class,

        # other operators
        'CAST' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cast::class,
        'ILIKE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ilike::class,
        'SIMILAR_TO' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SimilarTo::class,
        'NOT_SIMILAR_TO' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotSimilarTo::class,
        'UNACCENT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unaccent::class,
        'REGEXP' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Regexp::class,
        'IREGEXP' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IRegexp::class,
        'NOT_REGEXP' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotRegexp::class,
        'NOT_IREGEXP' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotIRegexp::class,
        'REGEXP_COUNT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpCount::class,
        'REGEXP_INSTR' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpInstr::class,
        'REGEXP_LIKE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpLike::class,
        'REGEXP_MATCH' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpMatch::class,
        'REGEXP_REPLACE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpReplace::class,
        'REGEXP_SUBSTR' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpSubstr::class,
        'STRCONCAT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StrConcat::class, // the `||` operator
        'ROW' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Row::class,
        'DISTANCE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Distance::class,

        # aggregation functions
        'ARRAY_AGG' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAgg::class,
        'JSON_AGG' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonAgg::class,
        'JSON_OBJECT_AGG' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonObjectAgg::class,
        'JSONB_AGG' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbAgg::class,
        'JSONB_OBJECT_AGG' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectAgg::class,
        'STRING_AGG' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringAgg::class,
        'XML_AGG' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlAgg::class,

        # data type formatting functions
        'TO_CHAR' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToChar::class,
        'TO_DATE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToDate::class,
        'TO_NUMBER' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToNumber::class,
        'TO_TIMESTAMP' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTimestamp::class,
    ],

    ...
];
```


### Alternative: Event Subscriber for Type Registration

If you prefer to register types programmatically, you can create an event subscriber:

```php
<?php

declare(strict_types=1);

namespace App\Doctrine\EventSubscribers;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Types\Type;

class PostgreSQLTypesSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::postConnect,
        ];
    }

    public function postConnect(ConnectionEventArgs $args): void
    {
        $this->registerArrayTypes();
        $this->registerJsonTypes();
        $this->registerNetworkTypes();
        $this->registerSpatialTypes();
        $this->registerRangeTypes();
        $this->registerHierarchicalTypes();
    }

    private function registerArrayTypes(): void
    {
        $this->addTypeIfNotExists('bool[]', \MartinGeorgiev\Doctrine\DBAL\Types\BooleanArray::class);
        $this->addTypeIfNotExists('bigint[]', \MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray::class);
        $this->addTypeIfNotExists('integer[]', \MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray::class);
        $this->addTypeIfNotExists('smallint[]', \MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray::class);
        $this->addTypeIfNotExists('double precision[]', \MartinGeorgiev\Doctrine\DBAL\Types\DoublePrecisionArray::class);
        $this->addTypeIfNotExists('real[]', \MartinGeorgiev\Doctrine\DBAL\Types\RealArray::class);
        $this->addTypeIfNotExists('text[]', \MartinGeorgiev\Doctrine\DBAL\Types\TextArray::class);
        $this->addTypeIfNotExists('uuid[]', \MartinGeorgiev\Doctrine\DBAL\Types\UuidArray::class);
    }

    private function registerJsonTypes(): void
    {
        $this->addTypeIfNotExists('jsonb', \MartinGeorgiev\Doctrine\DBAL\Types\Jsonb::class);
        $this->addTypeIfNotExists('jsonb[]', \MartinGeorgiev\Doctrine\DBAL\Types\JsonbArray::class);
    }

    private function registerNetworkTypes(): void
    {
        $this->addTypeIfNotExists('cidr', \MartinGeorgiev\Doctrine\DBAL\Types\Cidr::class);
        $this->addTypeIfNotExists('cidr[]', \MartinGeorgiev\Doctrine\DBAL\Types\CidrArray::class);
        $this->addTypeIfNotExists('inet', \MartinGeorgiev\Doctrine\DBAL\Types\Inet::class);
        $this->addTypeIfNotExists('inet[]', \MartinGeorgiev\Doctrine\DBAL\Types\InetArray::class);
        $this->addTypeIfNotExists('macaddr', \MartinGeorgiev\Doctrine\DBAL\Types\Macaddr::class);
        $this->addTypeIfNotExists('macaddr[]', \MartinGeorgiev\Doctrine\DBAL\Types\MacaddrArray::class);
    }

    private function registerSpatialTypes(): void
    {
        $this->addTypeIfNotExists('point', \MartinGeorgiev\Doctrine\DBAL\Types\Point::class);
        $this->addTypeIfNotExists('point[]', \MartinGeorgiev\Doctrine\DBAL\Types\PointArray::class);
        $this->addTypeIfNotExists('geometry', \MartinGeorgiev\Doctrine\DBAL\Types\Geometry::class);
        $this->addTypeIfNotExists('geometry[]', \MartinGeorgiev\Doctrine\DBAL\Types\GeometryArray::class);
        $this->addTypeIfNotExists('geography', \MartinGeorgiev\Doctrine\DBAL\Types\Geography::class);
        $this->addTypeIfNotExists('geography[]', \MartinGeorgiev\Doctrine\DBAL\Types\GeographyArray::class);
    }

    private function registerRangeTypes(): void
    {
        $this->addTypeIfNotExists('daterange', \MartinGeorgiev\Doctrine\DBAL\Types\DateRange::class);
        $this->addTypeIfNotExists('int4range', \MartinGeorgiev\Doctrine\DBAL\Types\Int4Range::class);
        $this->addTypeIfNotExists('int8range', \MartinGeorgiev\Doctrine\DBAL\Types\Int8Range::class);
        $this->addTypeIfNotExists('numrange', \MartinGeorgiev\Doctrine\DBAL\Types\NumRange::class);
        $this->addTypeIfNotExists('tsrange', \MartinGeorgiev\Doctrine\DBAL\Types\TsRange::class);
        $this->addTypeIfNotExists('tstzrange', \MartinGeorgiev\Doctrine\DBAL\Types\TstzRange::class);
    }

    private function registerHierarchicalTypes(): void
    {
        $this->addTypeIfNotExists('ltree', \MartinGeorgiev\Doctrine\DBAL\Types\Ltree::class);
    }

    private function addTypeIfNotExists(string $name, string $className): void
    {
        if (!Type::hasType($name)) {
            Type::addType($name, $className);
        }
    }
}
```

Register this subscriber in your `config/doctrine.php`:

```php
// config/doctrine.php
return [
    // ... other configuration

    'managers' => [
        'default' => [
            // ... other configuration

            'events' => [
                'subscribers' => [
                    \App\Doctrine\EventSubscribers\PostgreSQLTypesSubscriber::class,
                ],
            ],
        ],
    ],
];
```


### Usage in Entities

Once configured, you can use PostgreSQL types in your Laravel entities:

```php
<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
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

    #[ORM\Column(type: 'jsonb')]
    private array $specifications = [];

    #[ORM\Column(type: 'text[]')]
    private array $categories = [];

    #[ORM\Column(type: 'point')]
    private Point $location;

    #[ORM\Column(type: 'geometry')]
    private WktSpatialData $shape;

    #[ORM\Column(type: 'numrange')]
    private NumericRange $priceRange;

    #[ORM\Column(type: 'daterange')]
    private DateRange $availabilityPeriod;

    #[ORM\Column(type: 'inet')]
    private string $originServerIp;

    #[ORM\Column(type: 'ltree')]
    private Ltree $pathFromRoot;
}
```

### Service Provider for Advanced Configuration

If you need more control over the type registration, you can create a custom service provider:

```php
<?php

declare(strict_types=1);

namespace App\Providers;

use Doctrine\DBAL\Types\Type;
use Illuminate\Support\ServiceProvider;
use LaravelDoctrine\ORM\DoctrineServiceProvider;

class PostgreSQLDoctrineServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->resolving('em', function ($entityManager) {
            $this->registerPlatformTypeMappings($entityManager);
        });
    }

    public function boot(): void
    {
        $this->registerCustomTypes();
    }

    private function registerCustomTypes(): void
    {
        // Register types if not already registered
        $types = [
            'jsonb' => \MartinGeorgiev\Doctrine\DBAL\Types\Jsonb::class,
            'text[]' => \MartinGeorgiev\Doctrine\DBAL\Types\TextArray::class,
            'point' => \MartinGeorgiev\Doctrine\DBAL\Types\Point::class,
            'numrange' => \MartinGeorgiev\Doctrine\DBAL\Types\NumRange::class,
            'ltree' => \MartinGeorgiev\Doctrine\DBAL\Types\Ltree::class,
            // Add other types as needed...
        ];

        foreach ($types as $name => $class) {
            if (!Type::hasType($name)) {
                Type::addType($name, $class);
            }
        }
    }

    private function registerPlatformTypeMappings($entityManager): void
    {
        $platform = $entityManager->getConnection()->getDatabasePlatform();

        $mappings = [
            'jsonb' => 'jsonb',
            '_text' => 'text[]',
            'text[]' => 'text[]',
            'point' => 'point',
            '_point' => 'point[]',
            'numrange' => 'numrange',
            'ltree' => 'ltree',
            // Add other mappings as needed...
        ];

        foreach ($mappings as $dbType => $doctrineType) {
            $platform->registerDoctrineTypeMapping($dbType, $doctrineType);
        }
    }
}
```

Register this provider in `config/app.php`:

```php
// config/app.php
'providers' => [
    // ... other providers
    App\Providers\PostgreSQLDoctrineServiceProvider::class,
],
```

### Artisan Commands

You can use Laravel Doctrine's Artisan commands for schema management:

```bash
# Generate migrations
php artisan doctrine:migrations:diff

# Run migrations
php artisan doctrine:migrations:migrate

# Generate entities from database
php artisan doctrine:generate:entities

# Validate schema
php artisan doctrine:schema:validate
```
