[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/martin-georgiev/postgresql-for-doctrine/badges/quality-score.png)](https://scrutinizer-ci.com/g/martin-georgiev/postgresql-for-doctrine/?branch=master)
[![Build Status](https://api.travis-ci.org/martin-georgiev/postgresql-for-doctrine.svg?branch=master)](https://www.travis-ci.org/martin-georgiev/postgresql-for-doctrine)
[![Coverage Status](https://coveralls.io/repos/github/martin-georgiev/postgresql-for-doctrine/badge.svg?branch=master)](https://coveralls.io/github/martin-georgiev/postgresql-for-doctrine?branch=master)
[![Latest Stable Version](https://poser.pugx.org/martin-georgiev/postgresql-for-doctrine/version)](https://packagist.org/packages/martin-georgiev/postgresql-for-doctrine)
[![Total Downloads](https://poser.pugx.org/martin-georgiev/postgresql-for-doctrine/downloads)](https://packagist.org/packages/martin-georgiev/postgresql-for-doctrine)
----
## What's this?
This package provides Doctrine support for some specific PostgreSQL 9.4+ features:

* Support of JSONB and array data types for integers, TEXT and JSONB
* Implementation of the most commonly used functions when working with array and JSON data types
* Functions for basic support of text search

It can be integrated in a simple manner with Symfony, Laravel or any other framework that benefits from Doctrine2 usage.

You can easily extend package's behaviour with your own array-like data types or other desired functions. Just follow the few steps in section **Extend It!** below.

----
## How to Install?
Easiest possible way is through [Composer](https://getcomposer.org/download/)

    composer require martin-georgiev/postgresql-for-doctrine

----
## Integration with Symfony
*Register the new DBAL types*

```yaml
# Usually part of config.yml
doctrine:
    dbal:
        types: # register the new types
            jsonb: MartinGeorgiev\Doctrine\DBAL\Types\Jsonb
            jsonb[]: MartinGeorgiev\Doctrine\DBAL\Types\JsonbArray
            smallint[]: MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray
            integer[]: MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray
            bigint[]: MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray
            text[]: MartinGeorgiev\Doctrine\DBAL\Types\TextArray
```

*Add mapping between DBAL and PostgreSQL data types*

```yaml
# Usually part of config.yml
doctrine:
    dbal:
        connections:
            your_connection:
                mapping_types:
                    jsonb: jsonb
                    jsonb[]: jsonb[]
                    _jsonb: jsonb[]
                    smallint[]: smallint[]
                    _int2: smallint[]
                    integer[]: integer[]
                    _int4: integer[]
                    bigint[]: bigint[]
                    _int8: bigint[]
                    text[]: text[]
                    _text: text[]
```

*Register the functions you'll use in your DQL queries*

```yaml
# Usually part of config.yml
doctrine:
    orm:
        entity_managers:
            your_connection:
                dql:
                    string_functions:
                        ALL_OF: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\All
                        ANY_OF: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Any
                        ARRAY_APPEND: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAppend
                        ARRAY_CARDINALITY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCardinality
                        ARRAY_CAT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCat
                        ARRAY_LENGTH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLength
                        ARRAY_PREPEND: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPrepend
                        ARRAY_REMOVE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayRemove
                        ARRAY_REPLACE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReplace
                        ARRAY_TO_STRING: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToString
                        ARRAY_TO_JSON: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToJson
                        STRING_TO_ARRAY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringToArray
                        CONTAINS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Contains
                        OVERLAPS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Overlaps
                        GREATEST: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Greatest
                        LEAST: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Least
                        IN_ARRAY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\InArray
                        IS_CONTAINED_BY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IsContainedBy
                        JSON_GET_FIELD: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField
                        JSON_GET_FIELD_AS_TEXT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsText
                        JSON_GET_FIELD_AS_INTEGER: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsInteger
                        JSON_GET_OBJECT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObject
                        JSON_GET_OBJECT_AS_TEXT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObjectAsText
                        JSON_GET_OBJECT_AS_INTEGER: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObjectAsInteger
                        JSONB_ARRAY_LENGTH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayLength
                        JSONB_EACH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEach
                        JSONB_EACH_TEXT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEachText
                        JSONB_EXISTS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExists
                        JSONB_INSERT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbInsert
                        JSONB_OBJECT_KEYS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectKeys
                        JSONB_SET: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbSet
```

## Integration with Laravel 5
Unfortunately, Laravel still doesn't come with native Doctrine2 integration.
The steps below are based on integration with [Laravel Doctrine](http://www.laraveldoctrine.org/).

*Register the new functions and data type mappings*

```php
<?php
# Usually part of config/doctrine.php

return [
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

    'custom_types' => [
        'jsonb' => MartinGeorgiev\Doctrine\DBAL\Types\Jsonb::class,
        'jsonb[]' => MartinGeorgiev\Doctrine\DBAL\Types\JsonbArray::class,
        'text[]' => MartinGeorgiev\Doctrine\DBAL\Types\TextArray::class,
        'bigint[]' => MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray::class,
        'integer[]' => MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray::class,
        'smallint[]' => MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray::class,
    ],

    'custom_string_functions' => [
        // Postgresql specific functions not supported natively by Doctrine or Doctrine Extensions
        'GREATEST' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Greatest::class,
        'LEAST' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Least::class,

        // Array data types related functions
        'ALL_OF' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\All::class,
        'ANY_OF' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Any::class,
        'IN_ARRAY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\InArray::class,
        'ARRAY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr::class,
        'ARRAY_APPEND' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAppend::class,
        'ARRAY_CARDINALITY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCardinality::class,
        'ARRAY_CAT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCat::class,
        'ARRAY_PREPEND' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPrepend::class,
        'ARRAY_REMOVE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayRemove::class,
        'ARRAY_REPLACE' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReplace::class,
        'ARRAY_TO_STRING' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToString::class,
        'STRING_TO_ARRAY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringToArray::class,

        // Functions and operators used by both array and json(-b) data types
        'CONTAINS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Contains::class,
        'IS_CONTAINED_BY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IsContainedBy::class,

        // Json(-b) data type related functions and operators
        'JSON_GET_FIELD' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField::class,
        'JSON_GET_FIELD_AS_TEXT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsText::class,
        'JSON_GET_FIELD_AS_INTEGER' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsInteger::class,
        'JSON_GET_OBJECT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObject::class,
        'JSON_GET_OBJECT_AS_TEXT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObjectAsText::class,
        'JSONB_ARRAY_ELEMENTS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElements::class,
        'JSONB_ARRAY_ELEMENTS_TEXT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElementsText::class,
        'JSONB_ARRAY_LENGTH' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayLength::class,
        'JSONB_EACH' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEach::class,
        'JSONB_EACH_TEXT' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEachText::class,
        'JSONB_EXISTS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExists::class,
        'JSONB_OBJECT_KEYS' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectKeys::class,

        // Text search related functions and operators
        'TO_TSQUERY' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery::class,
        'TO_TSVECTOR' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector::class,
        'TSMATCH' => MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsmatch::class,
    ],

    ...
];
```

*Create an EventSubscriber for the new data types*

```php
<?php

namespace Acme\Handlers\Events;

use Doctrine\Common\EventSubscriber as Subscriber;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Types\Type;

class DoctrineEventSubscriber implements Subscriber
{
    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postConnect,
        ];
    }

    /**
     * @throws DBALException
     */
    public function postConnect(ConnectionEventArgs $args) 
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

namespace Acme\Providers;

use LaravelDoctrine\ORM\DoctrineServiceProvider as LaravelDoctrineServiceProvider;

class DoctrineServiceProvider extends LaravelDoctrineServiceProvider
{
    public function register()
    {
        parent::register();

        $this->registerDoctrineTypeMapping();
    }

    private function registerDoctrineTypeMapping()
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

## Integration with Doctrine2

```php
<?php

use Doctrine\DBAL\Types\Type;

Type::addType('jsonb', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Jsonb");
Type::addType('jsonb[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\JsonbArray");
Type::addType('smallint[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\SmallIntArray");
Type::addType('integer[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\IntegerArray");
Type::addType('bigint[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\BigIntArray");
Type::addType('text[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\TextArray");
```

----
## Extend It!

**How to add more array-like data types?**

1) Extend *MartinGeorgiev\Doctrine\DBAL\Types\AbstractTypeArray*

2) You must give the new datatype a unique within your application name. For this purpose, you can use the *TYPE_NAME* constant.

3) Depending on your new datatype's nature you may also need to overwrite some of the following methods:

    `transformPostgresArrayToPHPArray()`. e.g. this will be valid for PostgreSQL's JSON datatype
    
    `transformArrayItemForPHP()`, in almost every case you will need to adjust the returned method to your specific needs
    
    `isValidArrayItemForDatabase()`, I encourage you to check that every item in your PHP array is actually compatible with your database datatype

**How to add more functions?**

1) Extend *MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AbstractFunction*

2) Add behavior to your new function with overwriting *customiseFunction()* method. Use *setFunctionPrototype()* and *addLiteralMapping()*.

Example:

```php
<?php

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class ArrayAppend extends AbstractFunction
{
    protected function customiseFunction()
    {
        $this->setFunctionPrototype('array_append(%s, %s)');
        $this->addLiteralMapping('StringPrimary'); # this will correspond to param №1 in the prototype set in setFunctionPrototype
        $this->addLiteralMapping('InputParameter'); # this will correspond to param №2 in the prototype set in setFunctionPrototype
        # Add as much literal mappings as you need.
    }
}
```

*Beware that you cannot use **?** as part of any function prototype in Doctrine as this will result in faulty query parsing.*

----
## License
This package is licensed under the MIT License. Please, respect that!
