[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/martin_georgiev/postgresql-for-doctrine/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/martin_georgiev/postgresql-for-doctrine/?branch=master)
[![Build Status](https://api.travis-ci.org/martin-georgiev/postgresql-for-doctrine.svg?branch=master)](https://api.travis-ci.org/martin-georgiev/postgresql-for-doctrine.svg?branch=master)
----
## What's this?
This package provides Doctrine2 support for some specific PostgreSQL 9.4+ features:

* Support of JSONB and array data types for integers, TEXT and JSONB
* Implementation of the most commonly used functions when working with array and JSON data types
* Functions for basic support of text search

It can be integrated in a simple manner with Symfony, Laravel or any other framework that benefits from Doctrine2 usage.

You can easily extend package's behaviour with your own array-like data types or other desired functions. Just follow the few steps in section **Extend It!** below.

----
## How to Install?
Easiest possible way is through [Composer](https://getcomposer.org/download/)

    composer require "martin-georgiev/postgresql-for-doctrine=~0.9"

----
## Integration with Symfony2
*Register the new DBAL types*

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

*Add mapping between DBAL and PostgreSQL data types*

    # Usually part of config.yml
    doctrine:
        dbal:
            connections:
                your_conenction:
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


## Integration with Laravel 5
Unfortunately, Laravel still doesn't come with native Doctrine2 integration.
The steps below are based on [FoxxMD's fork](https://github.com/FoxxMD/laravel-doctrine) of [mitchellvanw/laravel-doctrine](https://github.com/mitchellvanw/laravel-doctrine) integration. The package also works smoothly with [Laravel Doctrine](http://www.laraveldoctrine.org/).

1) Register the functions and datatype mappings:

    <?php
    # Usually part of config/doctrine.php

    return [
        'entity_managers' => [
            'name_of_your_entity_manager' => [
                'dql' => [
                    'string_functions' => [
                        // Array data types related functions
                        'ALL_OF' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\All', // Avoid conflict with Doctrine's ALL implementation
                        'ANY_OF' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Any', // Avoid conflict with Doctrine's ANY implementation
                        'ARRAY' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr',
                        'ARRAY_APPEND' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAppend',
                        'ARE_OVERLAPING_EACH_OTHER' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAreOverlapingEachOther',
                        'ARRAY_CARDINALITY' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCardinality',
                        'ARRAY_CAT' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCat',
                        'ARRAY_LENGTH' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLength',
                        'ARRAY_PREPEND' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPrepend',
                        'ARRAY_REMOVE' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayRemove',
                        'ARRAY_REPLACE' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReplace',
                        'ARRAY_TO_STRING' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToString',
                        'STRING_TO_ARRAY' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringToArray',
                        'LEAST' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Least',
                        'GREATEST' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Greatest',
                        'IN_ARRAY' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\InArray',

                        // Functions and operators used by both array and json(-b) data types
                        'CONTAINS' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Contains',
                        'IS_CONTAINED_BY' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IsContainedBy',

                        // Json(-b) data type related functions and operators
                        'JSON_GET_FIELD' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField',
                        'JSON_GET_FIELD_AS_INTEGER' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsInteger',
                        'JSON_GET_FIELD_AS_TEXT' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsText',
                        'JSON_GET_OBJECT' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObject',
                        'JSON_GET_OBJECT_AS_TEXT' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObjectAsText',
                        'JSONB_ARRAY_ELEMENTS' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElements',
                        'JSONB_ARRAY_ELEMENTS_TEXT' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElementsText',
                        'JSONB_ARRAY_LENGTH' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayLength',
                        'JSONB_EACH' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEach',
                        'JSONB_EACH_TEXT' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEachText',
                        'JSONB_EXISTS' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExists',
                        'JSONB_OBJECT_KEYS' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectKeys',

                        // Basic text search related functions and operators
                        'TO_TSQUERY' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery',
                        'TO_TSVECTOR' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector',
                        'TSMATCH' => 'MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsmatch',
                    ],
                ],
                'mapping_types' => [
                    'jsonb' => 'jsonb',
                    '_jsonb' => 'jsonb[]',
                    'jsonb[]' => 'jsonb[]',
                    '_int2' => 'smallint[]',
                    'smallint[]' => 'smallint[]',
                    '_int4' => 'integer[]',
                    'integer[]' => 'integer[]',
                    '_int8' => 'bigint',
                    'bigint[]' => 'bigint[]',
                ],

2) Add EventSubscriber for Doctrine

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
         * @param ConnectionEventArgs $args
         * @throws DBALException
         */
        public function postConnect(ConnectionEventArgs $args)
        {
            Type::addType('jsonb', "\MartinGeorgiev\Doctrine\DBAL\Types\Jsonb");
            Type::addType('jsonb[]', "\MartinGeorgiev\Doctrine\DBAL\Types\JsonbArray");
            Type::addType('bigint[]', "\MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray");
            Type::addType('integer[]', "\MartinGeorgiev\Doctrine\DBAL\Types\IntegerArray");
            Type::addType('smallint[]', "\MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray");
            Type::addType('text[]', "\MartinGeorgiev\Doctrine\DBAL\Types\TextArray");
        }
    }

3) Add the EventSubscriber for Doctrine to a ServiceProvider

    <?php

    namespace Acme\Providers;

    use Config;
    use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
    use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
    use Doctrine\Common\Persistence\ManagerRegistry as DoctrineManagerRegistry;
    use Acme\Handlers\Events\DoctrineEventSubscriber;

    /**
     * Class EventServiceProvider
     * @package Quantum\Providers
     */
    class EventServiceProvider extends ServiceProvider
    {
        /**
         * Register Doctrine Events as well.
         */
        public function register()
        {
            $this->registerDoctrineEvents();
            $this->registerDoctrineTypeMapping();
        }

        /**
         * Register any other events for your application.
         * @param DispatcherContract $events
         * @return void
         */
        public function boot(DispatcherContract $events)
        {
            parent::boot($events);
        }

        /**
         * Register Doctrine events.
         */
        private function registerDoctrineEvents()
        {
            $eventManager = $this->registry()->getConnection()->getEventManager();
            $eventManager->addEventSubscriber(new DoctrineEventSubscriber);
        }

        /**
         * Register any custom Doctrine type mappings
         */
        private function registerDoctrineTypeMapping()
        {
            $databasePlatform = $this->registry()->getConnection()->getDatabasePlatform();
            $entityManagers = Config::get('doctrine.entity_managers');
            foreach ($entityManagers as $entityManager) {
                if (array_key_exists('mapping_types', $entityManager)) {
                    foreach ($entityManager['mapping_types'] as $dbType => $doctrineName) {
                        $databasePlatform->registerDoctrineTypeMapping($dbType, $doctrineName);
                    }
                }
            }
        }

        /**
         * Get the entity manager registry
         * @return DoctrineManagerRegistry
         */
        function registry()
        {
            return app(DoctrineManagerRegistry::class);
        }
    }


## Integration with Doctrine2
    <?php

    use Doctrine\DBAL\Types\Type;

    Type::addType('jsonb', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Jsonb");
    Type::addType('jsonb[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\JsonbArray");
    Type::addType('smallint[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\SmallIntArray");
    Type::addType('integer[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\IntegerArray");
    Type::addType('bigint[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\BigIntArray");
    Type::addType('text[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\TextArray");

----
## Extend It!

**How to add more array-like data types?**

1) Extend *MartinGeorgiev\Doctrine\DBAL\Types\AbstractTypeArray*

2) You must give the new datatype a unique within your application name. For this purpose, you can use the *TYPE_NAME* constant.

3) Depending on your new datatype's nature you may also need to overwrite some of the following methods:

    transformPostgresArrayToPHPArray() # E.g. this will be valid for PostgreSQL's JSON datatype
    transformArrayItemForPHP() # In almost every case you will need to adjust the returned method to your specific needs
    isValidArrayItemForDatabase() # It is encouraged to check that every element part of your PHP array is actually compatible with your database datatype

**How to add more functions?**

1) Extend *MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AbstractFunction*

2) Add behavior to your new function with overwriting *customiseFunction()* method. Use *setFunctionPrototype()* and *addLiteralMapping()* as in this example:

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

*Beware that you cannot use **?** as part of any function prototype in Doctrine as this will result in faulty query parsing.*

----
## License
This package is licensed under the MIT License. Please, respect that!
