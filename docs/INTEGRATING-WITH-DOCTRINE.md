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

Type::addType('double precision[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\DoublePrecisionArray");
Type::addType('real[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\RealArray");

Type::addType('text[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\TextArray");
Type::addType('jsonb', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Jsonb");
Type::addType('jsonb[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\JsonbArray");

Type::addType('cidr', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Cidr");
Type::addType('cidr[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\CidrArray");
Type::addType('inet', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Inet");
Type::addType('inet[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\InetArray");
Type::addType('macaddr', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Macaddr");
Type::addType('macaddr[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\MacaddrArray");

Type::addType('point', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Point");
Type::addType('point[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\PointArray");
```


*Register the functions you'll use in your DQL queries*


Full set of the available functions and extra operators can be found [here](AVAILABLE-FUNCTIONS-AND-OPERATORS.md).

```php
<?php

use Doctrine\ORM\Configuration;

$configuration = new Configuration();

# alternative implementation of ALL() and ANY() where subquery is not required, useful for arrays
$configuration->addCustomStringFunction('ALL_OF', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\All::class);
$configuration->addCustomStringFunction('ANY_OF', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Any::class);

# operators for working with array and json(b) data
$configuration->addCustomStringFunction('GREATEST', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Greatest::class);
$configuration->addCustomStringFunction('LEAST', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Least::class);
$configuration->addCustomStringFunction('CONTAINS', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Contains::class); # @>
$configuration->addCustomStringFunction('IS_CONTAINED_BY', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IsContainedBy::class); # <@
$configuration->addCustomStringFunction('OVERLAPS', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Overlaps::class); # &&
$configuration->addCustomStringFunction('RIGHT_EXISTS_ON_LEFT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TheRightExistsOnTheLeft::class); # ?
$configuration->addCustomStringFunction('ALL_ON_RIGHT_EXIST_ON_LEFT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AllOnTheRightExistOnTheLeft::class); # ?&
$configuration->addCustomStringFunction('ANY_ON_RIGHT_EXISTS_ON_LEFT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AnyOnTheRightExistsOnTheLeft::class); # ?|
$configuration->addCustomStringFunction('RETURNS_VALUE_FOR_JSON_VALUE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ReturnsValueForJsonValue::class); # @?
$configuration->addCustomStringFunction('DELETE_AT_PATH', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DeleteAtPath::class); # #-

# array and string specific functions
$configuration->addCustomStringFunction('IN_ARRAY', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\InArray::class);
$configuration->addCustomStringFunction('ANY_VALUE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AnyValue::class);
$configuration->addCustomStringFunction('ARRAY', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr::class);
$configuration->addCustomStringFunction('ARRAY_APPEND', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAppend::class);
$configuration->addCustomStringFunction('ARRAY_CARDINALITY', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCardinality::class);
$configuration->addCustomStringFunction('ARRAY_CAT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCat::class);
$configuration->addCustomStringFunction('ARRAY_DIMENSIONS', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayDimensions::class);
$configuration->addCustomStringFunction('ARRAY_LENGTH', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLength::class);
$configuration->addCustomStringFunction('ARRAY_NUMBER_OF_DIMENSIONS', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayNumberOfDimensions::class);
$configuration->addCustomStringFunction('ARRAY_POSITION', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPosition::class);
$configuration->addCustomStringFunction('ARRAY_POSITIONS', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPositions::class);
$configuration->addCustomStringFunction('ARRAY_PREPEND', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPrepend::class);
$configuration->addCustomStringFunction('ARRAY_REMOVE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayRemove::class);
$configuration->addCustomStringFunction('ARRAY_REPLACE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReplace::class);
$configuration->addCustomStringFunction('ARRAY_SHUFFLE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayShuffle::class);
$configuration->addCustomStringFunction('ARRAY_TO_JSON', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToJson::class);
$configuration->addCustomStringFunction('ARRAY_TO_STRING', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToString::class);
$configuration->addCustomStringFunction('SPLIT_PART', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SplitPart::class);
$configuration->addCustomStringFunction('STARTS_WITH', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StartsWith::class);
$configuration->addCustomStringFunction('STRING_TO_ARRAY', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringToArray::class);
$configuration->addCustomStringFunction('UNNEST', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unnest::class);

# json specific functions
$configuration->addCustomStringFunction('JSON_ARRAY_LENGTH', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonArrayLength::class);
$configuration->addCustomStringFunction('JSON_BUILD_OBJECT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonBuildObject::class);
$configuration->addCustomStringFunction('JSON_EACH', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEach::class);
$configuration->addCustomStringFunction('JSON_EACH_TEXT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEachText::class);
$configuration->addCustomStringFunction('JSON_EXISTS', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonExists::class);
$configuration->addCustomStringFunction('JSON_GET_FIELD', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField::class);
$configuration->addCustomStringFunction('JSON_GET_FIELD_AS_INTEGER', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsInteger::class);
$configuration->addCustomStringFunction('JSON_GET_FIELD_AS_TEXT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsText::class);
$configuration->addCustomStringFunction('JSON_GET_OBJECT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObject::class);
$configuration->addCustomStringFunction('JSON_GET_OBJECT_AS_TEXT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObjectAsText::class);
$configuration->addCustomStringFunction('JSON_OBJECT_KEYS', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonObjectKeys::class);
$configuration->addCustomStringFunction('JSON_QUERY', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonQuery::class);
$configuration->addCustomStringFunction('JSON_SCALAR', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonScalar::class);
$configuration->addCustomStringFunction('JSON_SERIALIZE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonSerialize::class);
$configuration->addCustomStringFunction('JSON_STRIP_NULLS', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonStripNulls::class);
$configuration->addCustomStringFunction('JSON_TYPEOF', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonTypeof::class);
$configuration->addCustomStringFunction('JSON_VALUE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonValue::class);
$configuration->addCustomStringFunction('TO_JSON', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJson::class);
$configuration->addCustomStringFunction('ROW_TO_JSON', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RowToJson::class);

# jsonb specific functions
$configuration->addCustomStringFunction('JSONB_ARRAY_ELEMENTS', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElements::class);
$configuration->addCustomStringFunction('JSONB_ARRAY_ELEMENTS_TEXT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElementsText::class);
$configuration->addCustomStringFunction('JSONB_ARRAY_LENGTH', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayLength::class);
$configuration->addCustomStringFunction('JSONB_BUILD_OBJECT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbBuildObject::class);
$configuration->addCustomStringFunction('JSONB_EACH', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEach::class);
$configuration->addCustomStringFunction('JSONB_EACH_TEXT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEachText::class);
$configuration->addCustomStringFunction('JSONB_EXISTS', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExists::class);
$configuration->addCustomStringFunction('JSONB_INSERT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbInsert::class);
$configuration->addCustomStringFunction('JSONB_OBJECT_KEYS', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectKeys::class);
$configuration->addCustomStringFunction('JSONB_PATH_EXISTS', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathExists::class);
$configuration->addCustomStringFunction('JSONB_PATH_MATCH', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathMatch::class);
$configuration->addCustomStringFunction('JSONB_PATH_QUERY', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathQuery::class);
$configuration->addCustomStringFunction('JSONB_PATH_QUERY_ARRAY', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathQueryArray::class);
$configuration->addCustomStringFunction('JSONB_PATH_QUERY_FIRST', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathQueryFirst::class);
$configuration->addCustomStringFunction('JSONB_PRETTY', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPretty::class);
$configuration->addCustomStringFunction('JSONB_SET', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbSet::class);
$configuration->addCustomStringFunction('JSONB_SET_LAX', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbSetLax::class);
$configuration->addCustomStringFunction('JSONB_STRIP_NULLS', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbStripNulls::class);
$configuration->addCustomStringFunction('TO_JSONB', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJsonb::class);

# text search specific
$configuration->addCustomStringFunction('TO_TSQUERY', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery::class);
$configuration->addCustomStringFunction('TO_TSVECTOR', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector::class);
$configuration->addCustomStringFunction('TSMATCH', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsmatch::class);

# date specific functions
$configuration->addCustomStringFunction('DATE_ADD', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateAdd::class);
$configuration->addCustomStringFunction('DATE_BIN', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateBin::class);
$configuration->addCustomStringFunction('DATE_EXTRACT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateExtract::class);
$configuration->addCustomStringFunction('DATE_OVERLAPS', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateOverlaps::class);
$configuration->addCustomStringFunction('DATE_SUBTRACT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateSubtract::class);

# range functions
$configuration->addCustomStringFunction('DATERANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Daterange::class);
$configuration->addCustomStringFunction('INT4RANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int4range::class);
$configuration->addCustomStringFunction('INT8RANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int8range::class);
$configuration->addCustomStringFunction('NUMRANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Numrange::class);
$configuration->addCustomStringFunction('TSRANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsrange::class);
$configuration->addCustomStringFunction('TSTZRANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tstzrange::class);

# other operators
$configuration->addCustomStringFunction('CAST', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cast::class);
$configuration->addCustomStringFunction('ILIKE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ilike::class);
$configuration->addCustomStringFunction('SIMILAR_TO', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SimilarTo::class);
$configuration->addCustomStringFunction('NOT_SIMILAR_TO', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotSimilarTo::class);
$configuration->addCustomStringFunction('UNACCENT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unaccent::class);
$configuration->addCustomStringFunction('REGEXP', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Regexp::class);
$configuration->addCustomStringFunction('IREGEXP', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IRegexp::class);
$configuration->addCustomStringFunction('NOT_REGEXP', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotRegexp::class);
$configuration->addCustomStringFunction('NOT_IREGEXP', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotIRegexp::class);
$configuration->addCustomStringFunction('REGEXP_LIKE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\FlaggedRegexpLike::class);
$configuration->addCustomStringFunction('REGEXP_COUNT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpCount::class);
$configuration->addCustomStringFunction('REGEXP_INSTR', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpInstr::class);
$configuration->addCustomStringFunction('REGEXP_SUBSTR', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpSubstr::class);
$configuration->addCustomStringFunction('REGEXP_MATCH', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpMatch::class);
$configuration->addCustomStringFunction('STRCONCAT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StrConcat::class);
$configuration->addCustomStringFunction('REGEXP_REPLACE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpReplace::class);
$configuration->addCustomStringFunction('ROW', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Row::class);
$configuration->addCustomStringFunction('DISTANCE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Distance::class);

# aggregation functions
$configuration->addCustomStringFunction('ARRAY_AGG', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAgg::class);
$configuration->addCustomStringFunction('JSON_AGG', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonAgg::class);
$configuration->addCustomStringFunction('JSON_OBJECT_AGG', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonObjectAgg::class);
$configuration->addCustomStringFunction('JSONB_AGG', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbAgg::class);
$configuration->addCustomStringFunction('JSONB_OBJECT_AGG', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectAgg::class);
$configuration->addCustomStringFunction('STRING_AGG', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringAgg::class);
$configuration->addCustomStringFunction('XML_AGG', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlAgg::class);

$em = EntityManager::create($dbParams, $configuration);
```

*Then you need to register type mappings like below, based on [documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/cookbook/custom-mapping-types.html)*

```php
$platform = $em->getConnection()->getDatabasePlatform();

$platform->registerDoctrineTypeMapping('bool[]','bool[]');
$platform->registerDoctrineTypeMapping('_bool','bool[]');
$platform->registerDoctrineTypeMapping('smallint[]','smallint[]');
$platform->registerDoctrineTypeMapping('_int2','smallint[]');
$platform->registerDoctrineTypeMapping('integer[]','integer[]');
$platform->registerDoctrineTypeMapping('_int4','integer[]');
$platform->registerDoctrineTypeMapping('bigint[]','bigint[]');
$platform->registerDoctrineTypeMapping('_int8','bigint[]');

$platform->registerDoctrineTypeMapping('double precision[]','double precision[]');
$platform->registerDoctrineTypeMapping('_float8','double precision[]');
$platform->registerDoctrineTypeMapping('real[]','real[]');
$platform->registerDoctrineTypeMapping('_float4','real[]');

$platform->registerDoctrineTypeMapping('text[]','text[]');
$platform->registerDoctrineTypeMapping('_text','text[]');
$platform->registerDoctrineTypeMapping('jsonb','jsonb');
$platform->registerDoctrineTypeMapping('jsonb[]','jsonb[]');
$platform->registerDoctrineTypeMapping('_jsonb','jsonb[]');

$platform->registerDoctrineTypeMapping('cidr[]','cidr[]');
$platform->registerDoctrineTypeMapping('_cidr','cidr[]');
$platform->registerDoctrineTypeMapping('inet[]','inet[]');
$platform->registerDoctrineTypeMapping('_inet','inet[]');
$platform->registerDoctrineTypeMapping('macaddr[]','macaddr[]');
$platform->registerDoctrineTypeMapping('_macaddr','macaddr[]');

$platform->registerDoctrineTypeMapping('point','point');
$platform->registerDoctrineTypeMapping('point[]','point[]');
$platform->registerDoctrineTypeMapping('_point','point[]');
...

```
