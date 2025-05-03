## Integration with Laravel

Laravel doesn't provide official Doctrine2 integration. However the community supported [Laravel Doctrine](http://www.laraveldoctrine.org/) project is very promising. The steps below are based on integration with it and Laravel 5.3. Hopefully this will stay relevant for future Laravel versions.

*Register the DBAL types you plan to use*

Full set of the available types can be found [here](AVAILABLE-TYPES.md).

```php
<?php
# Usually part of config/doctrine.php

return [
    ...
    'managers' => [
        'default' => [
            ...

            'type_mappings' => [
                '_bool' => 'bool[]',
                'bool[]' => 'bool[]',
                'smallint[]' => 'smallint[]',
                '_int2' => 'smallint[]',
                'integer[]' => 'integer[]',
                '_int4' => 'integer[]',
                'bigint[]' => 'bigint[]',
                '_int8' => 'bigint',
                
                'double precision[]' => 'double precision[]',
                '_float8' => 'double precision[]',
                'real[]' => 'real[]',
                '_float4' => 'real',
                
                '_text' => 'text[]',
                'text[]' => 'text[]',
                'jsonb' => 'jsonb',
                '_jsonb' => 'jsonb[]',
                'jsonb[]' => 'jsonb[]',
                
                'cidr' => 'cidr',
                'cidr[]' => 'cidr[]',
                '_cidr' => 'cidr[]',
                'inet' => 'inet',
                'inet[]' => 'inet[]',
                '_inet' => 'inet[]',
                'macaddr' => 'macaddr',
                'macaddr[]' => 'macaddr[]',
                '_macaddr' => 'macaddr[]',
                
                'point' => 'point',
                'point[]' => 'point[]',
                '_point' => 'point[]',
            ],
        ],
    ],
];
```


*Add mapping between DBAL and PostgreSQL data types*

```php
<?php
# Usually part of config/doctrine.php

return [
    ...
    'custom_types' => [
        'bool[]' => MartinGeorgiev\Doctrine\DBAL\Types\BooleanArray::class,
        'bigint[]' => MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray::class,
        'integer[]' => MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray::class,
        'smallint[]' => MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray::class,
        
        'double precision[]' => MartinGeorgiev\Doctrine\DBAL\Types\DoublePrecisionArray::class,
        'real[]' => MartinGeorgiev\Doctrine\DBAL\Types\RealArray::class,
        
        'text[]' => MartinGeorgiev\Doctrine\DBAL\Types\TextArray::class,
        'jsonb' => MartinGeorgiev\Doctrine\DBAL\Types\Jsonb::class,
        'jsonb[]' => MartinGeorgiev\Doctrine\DBAL\Types\JsonbArray::class,
        
        'cnet' => MartinGeorgiev\Doctrine\DBAL\Types\Cnet::class,
        'cnet[]' => MartinGeorgiev\Doctrine\DBAL\Types\CnetArray::class,
        'inet' => MartinGeorgiev\Doctrine\DBAL\Types\Inet::class,
        'inet[]' => MartinGeorgiev\Doctrine\DBAL\Types\InetArray::class,
        'macaddr' => MartinGeorgiev\Doctrine\DBAL\Types\Macaddr::class,
        'macaddr[]' => MartinGeorgiev\Doctrine\DBAL\Types\MacaddrArray::class,
        
        'point' => MartinGeorgiev\Doctrine\DBAL\Types\Point::class,
        'point[]' => MartinGeorgiev\Doctrine\DBAL\Types\PointArray::class,
    ],
];
```


*Register the functions you'll use in your DQL queries*

Full set of the available functions and extra operators can be found [here](AVAILABLE-FUNCTIONS-AND-OPERATORS.md).

```php
<?php
# Usually part of config/doctrine.php

return [
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
        'ARRAY_LENGTH' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLength::class,
        'ARRAY_NUMBER_OF_DIMENSIONS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayNumberOfDimensions::class,
        'ARRAY_POSITION' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPosition::class,
        'ARRAY_POSITIONS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPositions::class,
        'ARRAY_PREPEND' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPrepend::class,
        'ARRAY_REMOVE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayRemove::class,
        'ARRAY_REPLACE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReplace::class,
        'ARRAY_SHUFFLE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayShuffle::class,
        'ARRAY_TO_JSON' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToJson::class,
        'ARRAY_TO_STRING' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToString::class,
        'SPLIT_PART' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SplitPart::class,
        'STARTS_WITH' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StartsWith::class,
        'STRING_TO_ARRAY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringToArray::class,
        'UNNEST' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unnest::class,

        # json specific functions
        'JSON_ARRAY_LENGTH' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonArrayLength::class,
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

        # range functions
        'DATERANGE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Daterange::class,
        'INT4RANGE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int4range::class,
        'INT8RANGE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int8range::class,
        'NUMRANGE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Numrange::class,
        'TSRANGE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsrange::class,
        'TSTZRANGE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tstzrange::class,

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
    ],

    ...
];
```


*Create an EventSubscriber for the new data types*

```php
<?php

declare(strict_types=1);

namespace Acme\Handlers\Events;

use Doctrine\Common\EventSubscriber as Subscriber;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Types\Type;

class DoctrineEventSubscriber implements Subscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::postConnect,
        ];
    }

    /**
     * @throws DBALException
     */
    public function postConnect(ConnectionEventArgs $args): void
    {
        if (!Type::hasType('bool[]')) {
            Type::addType('bool[]', \MartinGeorgiev\Doctrine\DBAL\Types\BooleanArray::class);
        }
        if (!Type::hasType('bigint[]')) {
            Type::addType('bigint[]', \MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray::class);
        }
        if (!Type::hasType('integer[]')) {
            Type::addType('integer[]', \MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray::class);
        }
        if (!Type::hasType('smallint[]')) {
            Type::addType('smallint[]', \MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray::class);
        }
        if (!Type::hasType('double precision[]')) {
            Type::addType('double precision[]', \MartinGeorgiev\Doctrine\DBAL\Types\DoublePrecisionArray::class);
        }
        if (!Type::hasType('real[]')) {
            Type::addType('real[]', \MartinGeorgiev\Doctrine\DBAL\Types\RealArray::class);
        }
        if (!Type::hasType('text[]')) {
            Type::addType('text[]', \MartinGeorgiev\Doctrine\DBAL\Types\TextArray::class);
        }
        if (!Type::hasType('jsonb')) {
            Type::addType('jsonb', \MartinGeorgiev\Doctrine\DBAL\Types\Jsonb::class);
        }
        if (!Type::hasType('jsonb[]')) {
            Type::addType('jsonb[]', \MartinGeorgiev\Doctrine\DBAL\Types\JsonbArray::class);
        }
        if (!Type::hasType('cidr')) {
            Type::addType('cidr', \MartinGeorgiev\Doctrine\DBAL\Types\Cidr::class);
        }
        if (!Type::hasType('cidr[]')) {
            Type::addType('cidr[]', \MartinGeorgiev\Doctrine\DBAL\Types\CidrArray::class);
        }
        if (!Type::hasType('inet')) {
            Type::addType('inet', \MartinGeorgiev\Doctrine\DBAL\Types\Inet::class);
        }
        if (!Type::hasType('inet[]')) {
            Type::addType('inet[]', \MartinGeorgiev\Doctrine\DBAL\Types\InetArray::class);
        }
        if (!Type::hasType('macaddr')) {
            Type::addType('macaddr', \MartinGeorgiev\Doctrine\DBAL\Types\Macaddr::class);
        }
        if (!Type::hasType('macaddr[]')) {
            Type::addType('macaddr[]', \MartinGeorgiev\Doctrine\DBAL\Types\MacaddrArray::class);
        }
        if (!Type::hasType('point')) {
            Type::addType('point', \MartinGeorgiev\Doctrine\DBAL\Types\Point::class);
        }
        if (!Type::hasType('point[]')) {
            Type::addType('point[]', \MartinGeorgiev\Doctrine\DBAL\Types\PointArray::class);
        }
    }
}
```


*Register the new data type mappings in DoctrineServiceProvider*

```php
<?php

declare(strict_types=1);

namespace Acme\Providers;

use LaravelDoctrine\ORM\DoctrineServiceProvider as LaravelDoctrineServiceProvider;

class DoctrineServiceProvider extends LaravelDoctrineServiceProvider
{
    public function register(): void
    {
        parent::register();

        $this->registerDoctrineTypeMapping();
    }

    private function registerDoctrineTypeMapping(): void
    {
        $databasePlatform = $this->app->make('registry')->getConnection()->getDatabasePlatform();
        $entityManagers = $this->app->make('config')->get('doctrine.managers');
        foreach ($entityManagers as $entityManager) {
            if (!array_key_exists('type_mappings', $entityManager)) {
                continue;
            }
            foreach ($entityManager['type_mappings'] as $dbType => $doctrineName) {
                $databasePlatform->registerDoctrineTypeMapping($dbType, $doctrineName);
            }
        }
    }
}
```
