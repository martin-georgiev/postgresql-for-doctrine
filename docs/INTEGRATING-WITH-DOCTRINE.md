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

# Register aggregation functions
$configuration->addCustomStringFunction('ARRAY_AGG', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAgg::class);
$configuration->addCustomStringFunction('JSON_AGG', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonAgg::class);
$configuration->addCustomStringFunction('JSON_OBJECT_AGG', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonObjectAgg::class);
$configuration->addCustomStringFunction('JSONB_AGG', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbAgg::class);
$configuration->addCustomStringFunction('JSONB_OBJECT_AGG', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectAgg::class);
$configuration->addCustomStringFunction('STRING_AGG', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringAgg::class);
$configuration->addCustomStringFunction('XML_AGG', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlAgg::class);
# Register json functions
$configuration->addCustomStringFunction('JSON_ARRAY_LENGTH', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonArrayLength::class);
$configuration->addCustomStringFunction('JSON_BUILD_OBJECT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonBuildObject::class);
$configuration->addCustomStringFunction('JSON_EACH', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEach::class);
$configuration->addCustomStringFunction('JSON_EACH_TEXT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEachText::class);
$configuration->addCustomStringFunction('JSON_EXISTS', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonExists::class);
$configuration->addCustomStringFunction('JSON_GET_FIELD', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField::class);
$configuration->addCustomStringFunction('JSON_GET_FIELD_AS_TEXT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsText::class);
$configuration->addCustomStringFunction('JSON_GET_FIELD_AS_INTEGER', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsInteger::class);
$configuration->addCustomStringFunction('JSON_GET_OBJECT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObject::class);
$configuration->addCustomStringFunction('JSON_GET_OBJECT_AS_TEXT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObjectAsText::class);
$configuration->addCustomStringFunction('JSON_OBJECT_KEYS', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonObjectKeys::class);
$configuration->addCustomStringFunction('JSON_QUERY', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonQuery::class);
$configuration->addCustomStringFunction('JSON_SCALAR', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonScalar::class);
$configuration->addCustomStringFunction('JSON_SERIALIZE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonSerialize::class);
$configuration->addCustomStringFunction('JSON_STRIP_NULLS', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonStripNulls::class);
$configuration->addCustomStringFunction('JSON_VALUE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonValue::class);
$configuration->addCustomStringFunction('TO_JSON', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJson::class);
$configuration->addCustomStringFunction('ROW_TO_JSON', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RowToJson::class);
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
# Register array value extracting functions
$configuration->addCustomStringFunction('ALL_OF', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\All::class);
$configuration->addCustomStringFunction('ANY_OF', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Any::class);
$configuration->addCustomStringFunction('ANY_VALUE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AnyValue::class);

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
