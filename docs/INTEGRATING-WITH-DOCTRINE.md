## Integration with Doctrine

This guide covers integration with Doctrine DBAL 3.x/4.x and ORM 2.14+/3.x. For older versions, please refer to previous documentation versions.

### Register DBAL Types

Register the DBAL types you plan to use. The **full set** of available types can be found in [AVAILABLE-TYPES.md](AVAILABLE-TYPES.md).

```php
<?php

use Doctrine\DBAL\Types\Type;

// Array types
Type::addType('bool[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\BooleanArray");
Type::addType('smallint[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\SmallIntArray");
Type::addType('integer[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\IntegerArray");
Type::addType('bigint[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\BigIntArray");
Type::addType('double precision[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\DoublePrecisionArray");
Type::addType('real[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\RealArray");
Type::addType('text[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\TextArray");
Type::addType('uuid[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\UuidArray");

// JSON types
Type::addType('jsonb', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Jsonb");
Type::addType('jsonb[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\JsonbArray");

// Network types
Type::addType('cidr', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Cidr");
Type::addType('cidr[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\CidrArray");
Type::addType('inet', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Inet");
Type::addType('inet[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\InetArray");
Type::addType('macaddr', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Macaddr");
Type::addType('macaddr[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\MacaddrArray");

// Spatial types
Type::addType('point', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Point");
Type::addType('point[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\PointArray");
Type::addType('geometry', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Geometry");
Type::addType('geometry[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\GeometryArray");
Type::addType('geography', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Geography");
Type::addType('geography[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\GeographyArray");

// Range types
Type::addType('daterange', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\DateRange");
Type::addType('int4range', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Int4Range");
Type::addType('int8range', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Int8Range");
Type::addType('numrange', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\NumRange");
Type::addType('tsrange', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\TsRange");
Type::addType('tstzrange', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\TstzRange");

// Hierarchical types
Type::addType('ltree', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Ltree");
```


### Register DQL Functions

Register the functions you'll use in your DQL queries. The full set of available functions and operators can be found in the [Available Functions and Operators](AVAILABLE-FUNCTIONS-AND-OPERATORS.md) documentation and its specialized sub-pages:
- [Array and JSON Functions](ARRAY-AND-JSON-FUNCTIONS.md)
- [PostGIS Spatial Functions](SPATIAL-FUNCTIONS-AND-OPERATORS.md)
- [Text and Pattern Functions](TEXT-AND-PATTERN-FUNCTIONS.md)
- [Date and Range Functions](DATE-AND-RANGE-FUNCTIONS.md)
- [Mathematical Functions](MATHEMATICAL-FUNCTIONS.md)

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
$configuration->addCustomStringFunction('DATE_TRUNC', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateTrunc::class);

# range functions
$configuration->addCustomStringFunction('DATERANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Daterange::class);
$configuration->addCustomStringFunction('INT4RANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int4range::class);
$configuration->addCustomStringFunction('INT8RANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int8range::class);
$configuration->addCustomStringFunction('NUMRANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Numrange::class);
$configuration->addCustomStringFunction('TSRANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsrange::class);
$configuration->addCustomStringFunction('TSTZRANGE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tstzrange::class);

# Arithmetic functions
$configuration->addCustomStringFunction('CBRT', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cbrt::class);
$configuration->addCustomStringFunction('CEIL', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ceil::class);
$configuration->addCustomStringFunction('DEGREES', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Degrees::class);
$configuration->addCustomStringFunction('EXP', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exp::class);
$configuration->addCustomStringFunction('FLOOR', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Floor::class);
$configuration->addCustomStringFunction('LN', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ln::class);
$configuration->addCustomStringFunction('LOG', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Log::class);
$configuration->addCustomStringFunction('PI', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Pi::class);
$configuration->addCustomStringFunction('POWER', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Power::class);
$configuration->addCustomStringFunction('RADIANS', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Radians::class);
$configuration->addCustomStringFunction('RANDOM', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Random::class);
$configuration->addCustomStringFunction('ROUND', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Round::class);
$configuration->addCustomStringFunction('SIGN', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sign::class);
$configuration->addCustomStringFunction('TRUNC', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trunc::class);
$configuration->addCustomStringFunction('WIDTH_BUCKET', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\WidthBucket::class);

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
$configuration->addCustomStringFunction('REGEXP_LIKE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpLike::class);
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

# data type formatting functions
$configuration->addCustomStringFunction('TO_CHAR', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToChar::class);
$configuration->addCustomStringFunction('TO_DATE', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToDate::class);
$configuration->addCustomStringFunction('TO_NUMBER', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToNumber::class);
$configuration->addCustomStringFunction('TO_TIMESTAMP', MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTimestamp::class);

$em = EntityManager::create($dbParams, $configuration);
```

### Register Platform Type Mappings

Register type mappings between PostgreSQL and Doctrine types. This is required for schema introspection and migrations. Based on the [Doctrine documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/current/cookbook/custom-mapping-types.html).

```php
<?php

$platform = $em->getConnection()->getDatabasePlatform();

// Array type mappings
$platform->registerDoctrineTypeMapping('bool[]', 'bool[]');
$platform->registerDoctrineTypeMapping('_bool', 'bool[]');
$platform->registerDoctrineTypeMapping('smallint[]', 'smallint[]');
$platform->registerDoctrineTypeMapping('_int2', 'smallint[]');
$platform->registerDoctrineTypeMapping('integer[]', 'integer[]');
$platform->registerDoctrineTypeMapping('_int4', 'integer[]');
$platform->registerDoctrineTypeMapping('bigint[]', 'bigint[]');
$platform->registerDoctrineTypeMapping('_int8', 'bigint[]');
$platform->registerDoctrineTypeMapping('double precision[]', 'double precision[]');
$platform->registerDoctrineTypeMapping('_float8', 'double precision[]');
$platform->registerDoctrineTypeMapping('real[]', 'real[]');
$platform->registerDoctrineTypeMapping('_float4', 'real[]');
$platform->registerDoctrineTypeMapping('text[]', 'text[]');
$platform->registerDoctrineTypeMapping('_text', 'text[]');
$platform->registerDoctrineTypeMapping('uuid[]', 'uuid[]');
$platform->registerDoctrineTypeMapping('_uuid', 'uuid[]');

// JSON type mappings
$platform->registerDoctrineTypeMapping('jsonb', 'jsonb');
$platform->registerDoctrineTypeMapping('jsonb[]', 'jsonb[]');
$platform->registerDoctrineTypeMapping('_jsonb', 'jsonb[]');

// Network type mappings
$platform->registerDoctrineTypeMapping('cidr', 'cidr');
$platform->registerDoctrineTypeMapping('cidr[]', 'cidr[]');
$platform->registerDoctrineTypeMapping('_cidr', 'cidr[]');
$platform->registerDoctrineTypeMapping('inet', 'inet');
$platform->registerDoctrineTypeMapping('inet[]', 'inet[]');
$platform->registerDoctrineTypeMapping('_inet', 'inet[]');
$platform->registerDoctrineTypeMapping('macaddr', 'macaddr');
$platform->registerDoctrineTypeMapping('macaddr[]', 'macaddr[]');
$platform->registerDoctrineTypeMapping('_macaddr', 'macaddr[]');

// Spatial type mappings
$platform->registerDoctrineTypeMapping('point', 'point');
$platform->registerDoctrineTypeMapping('point[]', 'point[]');
$platform->registerDoctrineTypeMapping('_point', 'point[]');
$platform->registerDoctrineTypeMapping('geometry', 'geometry');
$platform->registerDoctrineTypeMapping('geometry[]', 'geometry[]');
$platform->registerDoctrineTypeMapping('_geometry', 'geometry[]');
$platform->registerDoctrineTypeMapping('geography', 'geography');
$platform->registerDoctrineTypeMapping('geography[]', 'geography[]');
$platform->registerDoctrineTypeMapping('_geography', 'geography[]');

// Range type mappings
$platform->registerDoctrineTypeMapping('daterange', 'daterange');
$platform->registerDoctrineTypeMapping('int4range', 'int4range');
$platform->registerDoctrineTypeMapping('int8range', 'int8range');
$platform->registerDoctrineTypeMapping('numrange', 'numrange');
$platform->registerDoctrineTypeMapping('tsrange', 'tsrange');
$platform->registerDoctrineTypeMapping('tstzrange', 'tstzrange');

// Hierarchical mappings
$platform->registerDoctrineTypeMapping('ltree','ltree');
```

### Usage in Entities

Once types are registered, you can use them in your Doctrine entities:

```php
<?php

use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateRange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\NumericRange;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

#[ORM\Entity]
class MyEntity
{
    #[ORM\Column(type: 'jsonb')]
    private array $metadata = [];

    #[ORM\Column(type: 'text[]')]
    private array $tags = [];

    #[ORM\Column(type: 'point')]
    private Point $location;

    #[ORM\Column(type: 'geometry')]
    private WktSpatialData $shape;

    #[ORM\Column(type: 'numrange')]
    private NumericRange $priceRange;

    #[ORM\Column(type: 'daterange')]
    private DateRange $validPeriod;

    #[ORM\Column(type: 'inet')]
    private string $ipAddress;

    #[ORM\Column(type: 'ltree')]
    private Ltree $pathFromRoot;
}
```
