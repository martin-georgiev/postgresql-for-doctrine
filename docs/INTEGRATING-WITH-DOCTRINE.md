## Integration with Doctrine


*Register the DBAL types you plan to use*

Full set of the available types can be found [here](AVAILABLE-TYPES.md).

```php
<?php

use Doctrine\DBAL\Types\Type;

Type::addType('bool[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\BooleanArray");
Type::addType('smallint[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\SmallIntArray");
Type::addType('integer[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\IntegerArray");
Type::addType('bigint[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\BigIntArray");
Type::addType('text[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\TextArray");
Type::addType('jsonb', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Jsonb");
Type::addType('jsonb[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\JsonbArray");
```


*Register the functions you'll use in your DQL queries*


Full set of the available functions and extra operators can be found [here](AVAILABLE-FUNCTIONS-AND-OPERATORS.md).

```php
<?php

use Doctrine\ORM\Configuration;

$configuration = new Configuration();

# Register text search functions
$configuration->addCustomStringFunction('TO_TSQUERY', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery::class);
$configuration->addCustomStringFunction('TO_TSVECTOR', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector::class);
$configuration->addCustomStringFunction('TSMATCH', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsmatch::class);
# Register range functions
$configuration->addCustomStringFunction('DATERANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Daterange::class);
$configuration->addCustomStringFunction('INT4RANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int4range::class);
$configuration->addCustomStringFunction('INT8RANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int8range::class);
$configuration->addCustomStringFunction('NUMRANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Numrange::class);
$configuration->addCustomStringFunction('TSRANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsrange::class);
$configuration->addCustomStringFunction('TSTZRANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tstzrange::class);

$em = EntityManager::create($dbParams, $configuration);
```

*Then you need to register type mappings like below, based on [documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/cookbook/custom-mapping-types.html)*

```php
$platform = $em->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('bool[]','bool[]');
$platform->registerDoctrineTypeMapping('_bool','bool[]');
$platform->registerDoctrineTypeMapping('integer[]','integer[]');
$platform->registerDoctrineTypeMapping('_int4','integer[]');
$platform->registerDoctrineTypeMapping('bigint[]','bigint[]');
$platform->registerDoctrineTypeMapping('_int8','bigint[]');
$platform->registerDoctrineTypeMapping('text[]','text[]');
$platform->registerDoctrineTypeMapping('_text','text[]');
$platform->registerDoctrineTypeMapping('jsonb','jsonb');
...

```
