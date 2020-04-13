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
                'jsonb' => 'jsonb',
                '_jsonb' => 'jsonb[]',
                'jsonb[]' => 'jsonb[]',
                'guid' => 'guid',
                '_guid' => 'guid[]',
                'guid[]' => 'guid[]',
                '_int2' => 'smallint[]',
                'smallint[]' => 'smallint[]',
                '_int4' => 'integer[]',
                'integer[]' => 'integer[]',
                '_int8' => 'bigint',
                'bigint[]' => 'bigint[]',
                '_text' => 'text[]',
                'text[]' => 'text[]',
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
        'jsonb' => MartinGeorgiev\Doctrine\DBAL\Types\Jsonb::class,
        'jsonb[]' => MartinGeorgiev\Doctrine\DBAL\Types\JsonbArray::class,
        'text[]' => MartinGeorgiev\Doctrine\DBAL\Types\TextArray::class,
        'bigint[]' => MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray::class,
        'integer[]' => MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray::class,
        'smallint[]' => MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray::class,
    ],
];
```


*Register the functions you'll use in your DQL queries*

Full set of the available functions and operators can be found [here](AVAILABLE-FUNCTIONS-AND-OPERATORS.md).

```php
<?php
# Usually part of config/doctrine.php

return [
    'custom_string_functions' => [
        # alternative implementation of ALL() and ANY() where subquery is not required, useful for arrays
        'ALL_OF' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\All::class,
        'ANY_OF' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Any::class,
        
        # operators for working with array and json(b) data
        'CONTAINS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Contains::class,
        'IS_CONTAINED_BY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IsContainedBy::class,
        'OVERLAPS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Overlaps::class,
        'GREATEST' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Greatest::class,
        'LEAST' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Least::class,
        
        # array and string specific functions
        'IN_ARRAY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\InArray::class,
        'ARRAY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr::class,
        'ARRAY_AGG' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAgg::class,
        'ARRAY_APPEND' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAppend::class,
        'ARRAY_CARDINALITY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCardinality::class,
        'ARRAY_CAT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCat::class,
        'ARRAY_PREPEND' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPrepend::class,
        'ARRAY_REMOVE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayRemove::class,
        'ARRAY_REPLACE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReplace::class,
        'ARRAY_TO_STRING' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToString::class,
        'STRING_AGG' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringAgg::class,
        'STRING_TO_ARRAY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringToArray::class,
        'UNNEST' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unnest::class,

        # json specific functions
        'JSON_AGG' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonAgg::class,
        'JSON_ARRAY_LENGTH' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonArrayLength::class,
        'JSON_GET_FIELD' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField::class,
        'JSON_GET_FIELD_AS_TEXT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsText::class,
        'JSON_GET_FIELD_AS_INTEGER' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsInteger::class,
        'JSON_GET_OBJECT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObject::class,
        'JSON_GET_OBJECT_AS_TEXT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObjectAsText::class,
        'JSON_OBJECT_AGG' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonObjectAgg::class,
        'JSON_OBJECT_KEYS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonObjectKeys::class,
        'JSON_STRIP_NULLS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonStripNulls::class,
        
        # jsonb specific functions
        'JSONB_AGG' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbAgg::class,
        'JSONB_ARRAY_ELEMENTS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElements::class,
        'JSONB_ARRAY_ELEMENTS_TEXT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElementsText::class,
        'JSONB_ARRAY_LENGTH' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayLength::class,
        'JSONB_EACH' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEach::class,
        'JSONB_EACH_TEXT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEachText::class,
        'JSONB_EXISTS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExists::class,
        'JSONB_OBJECT_AGG' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectAgg::class,
        'JSONB_OBJECT_KEYS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectKeys::class,
        'JSONB_SET' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbSet::class,
        'JSONB_STRIP_NULLS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbStripNulls::class,

        # text search specific
        'TO_TSQUERY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery::class,
        'TO_TSVECTOR' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector::class,
        'TSMATCH' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsmatch::class,
        
        # other operators
        'ILIKE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ilike::class,
        'SIMILAR_TO' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SimilarTo::class,
        'NOT_SIMILAR_TO' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotSimilarTo::class,
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
        if (!Type::hasType('jsonb')) {
            Type::addType('jsonb', \MartinGeorgiev\Doctrine\DBAL\Types\Jsonb::class);
        }
        if (!Type::hasType('jsonb[]')) {
            Type::addType('jsonb[]', \MartinGeorgiev\Doctrine\DBAL\Types\JsonbArray::class);
        }
        if (!Type::hasType('text[]')) {
            Type::addType('text[]', \MartinGeorgiev\Doctrine\DBAL\Types\TextArray::class);
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