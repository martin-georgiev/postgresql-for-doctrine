## Integration with Symfony


*Register the DBAL types you plan to use*

Full set of the available types can be found [here](AVAILABLE-TYPES.md).

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

PostgreSQL will normally prefix array data-types with `_`.
Beware of the specific to PostgreSQL primary way of data-type naming for integers (`int2`, `int4`, `int8`).


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

Full set of the available types can be found [here](AVAILABLE-FUNCTIONS-AND-OPERATORS.md).

```yaml
# Usually part of config.yml
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
                        CONTAINS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Contains
                        IS_CONTAINED_BY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IsContainedBy
                        OVERLAPS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Overlaps
                        GREATEST: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Greatest
                        LEAST: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Least
                        
                        # array specific functions
                        ARRAY_APPEND: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAppend
                        ARRAY_CARDINALITY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCardinality
                        ARRAY_CAT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCat
                        ARRAY_DIMENSIONS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayDimensions
                        ARRAY_LENGTH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLength
                        ARRAY_NUMBER_OF_DIMENSIONS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayNumberOfDimensions
                        ARRAY_PREPEND: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPrepend
                        ARRAY_REMOVE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayRemove
                        ARRAY_REPLACE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReplace
                        ARRAY_TO_JSON: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToJson
                        ARRAY_TO_STRING: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToString
                        STRING_TO_ARRAY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringToArray
                        IN_ARRAY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\InArray
                        
                        # json specific functions
                        JSON_ARRAY_LENGTH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonArrayLength
                        JSON_EACH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEach
                        JSON_EACH_TEXT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEachText
                        JSON_GET_FIELD: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField
                        JSON_GET_FIELD_AS_INTEGER: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsInteger
                        JSON_GET_FIELD_AS_TEXT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsText
                        JSON_GET_OBJECT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObject
                        JSON_GET_OBJECT_AS_TEXT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObjectAsText
                        JSON_OBJECT_KEYS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonObjectKeys
                        JSON_STRIP_NULLS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonStripNulls
                        TO_JSON: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJson
                        
                        # jsonb specific functions
                        JSONB_ARRAY_ELEMENTS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElements
                        JSONB_ARRAY_ELEMENTS_TEXT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElementsText
                        JSONB_ARRAY_LENGTH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayLength
                        JSONB_EACH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEach
                        JSONB_EACH_TEXT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEachText
                        JSONB_EXISTS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExists
                        JSONB_INSERT: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbInsert
                        JSONB_OBJECT_KEYS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectKeys
                        JSONB_SET: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbSet
                        JSONB_STRIP_NULLS: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbStripNulls
                        TO_JSONB: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJsonb
                        
                        # text search specific
                        TO_TSQUERY: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery
                        TO_TSVECTOR: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector
                        TSMATCH: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsmatch
                        
                        # other operators
                        ILIKE: MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ilike
```