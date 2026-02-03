# Array and JSON Functions and Operators

This document covers PostgreSQL array and JSON/JSONB operators and functions available in this library.

> 📖 **See also**: [Common Use Cases and Examples](USE-CASES-AND-EXAMPLES.md) for practical JSON and array usage examples

## Array and JSON Operators

**⚠️ Important**: Some PostgreSQL operators have multiple meanings depending on the data types involved. This library provides specific DQL function names to avoid conflicts:

| Operator | Array/JSON Usage | Spatial Usage | Text/Pattern Usage |
|---|---|---|---|
| `@>` | `CONTAINS` (arrays contain elements) | Works automatically with geometry/geography | N/A |
| `<@` | `IS_CONTAINED_BY` (element in array) | Works automatically with geometry/geography | N/A |
| `&&` | `OVERLAPS` (arrays/ranges overlap) | Works automatically with geometry/geography | N/A |

**Usage Guidelines:**
- **Arrays/JSON**: Use `CONTAINS`, `IS_CONTAINED_BY`, `OVERLAPS` for array and JSON operations
- **Boolean operators**: All operators return boolean values and **should be used with `= TRUE` or `= FALSE` in DQL**

### Array and JSON Operators

| PostgreSQL operator | Register for DQL as | Implemented by |
|---|---|---|
| @> | CONTAINS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Contains` |
| <@ | IS_CONTAINED_BY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IsContainedBy` |
| && | OVERLAPS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Overlaps` |
| ? | RIGHT_EXISTS_ON_LEFT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TheRightExistsOnTheLeft` |
| ?& | ALL_ON_RIGHT_EXIST_ON_LEFT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AllOnTheRightExistOnTheLeft` |
| ?\| | ANY_ON_RIGHT_EXISTS_ON_LEFT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AnyOnTheRightExistsOnTheLeft` |
| @? | RETURNS_VALUE_FOR_JSON_VALUE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ReturnsValueForJsonValue` |
| #- | DELETE_AT_PATH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DeleteAtPath` |
| -> | JSON_GET_FIELD | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField` |
| ->> | JSON_GET_FIELD_AS_TEXT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsText`|
| #> | JSON_GET_OBJECT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObject` |
| #>> | JSON_GET_OBJECT_AS_TEXT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObjectAsText` |

## Array Functions

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| all | ALL_OF | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\All` |
| any | ANY_OF | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Any` |
| array_agg | ARRAY_AGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAgg` |
| array_append | ARRAY_APPEND | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAppend` |
| array_cat | ARRAY_CAT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCat` |
| array_dims | ARRAY_DIMENSIONS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayDimensions` |
| array_fill | ARRAY_FILL | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayFill` |
| array_length | ARRAY_LENGTH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLength` |
| array_lower | ARRAY_LOWER | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLower` |
| array_ndims | ARRAY_NUMBER_OF_DIMENSIONS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayNumberOfDimensions` |
| array_position | ARRAY_POSITION | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPosition` |
| array_positions | ARRAY_POSITIONS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPositions` |
| array_prepend | ARRAY_PREPEND | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPrepend` |
| array_remove | ARRAY_REMOVE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayRemove` |
| array_replace | ARRAY_REPLACE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReplace` |
| array_reverse | ARRAY_REVERSE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReverse` |
| array_sample | ARRAY_SAMPLE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArraySample` |
| array_shuffle | ARRAY_SHUFFLE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayShuffle` |
| array_sort | ARRAY_SORT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArraySort` |
| array_to_json | ARRAY_TO_JSON | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToJson` |
| array_to_string | ARRAY_TO_STRING | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToString` |
| array_upper | ARRAY_UPPER | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayUpper` |
| cardinality | ARRAY_CARDINALITY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cardinality` |
| string_to_array | STRING_TO_ARRAY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringToArray` |
| unnest | UNNEST | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unnest` |

## JSON Functions

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| json_agg | JSON_AGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonAgg` |
| json_array_length | JSON_ARRAY_LENGTH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonArrayLength` |
| json_build_array | JSON_BUILD_ARRAY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonBuildArray` |
| json_build_object | JSON_BUILD_OBJECT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonBuildObject` |
| json_each | JSON_EACH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEach` |
| json_each_text | JSON_EACH_TEXT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEachText` |
| json_exists | JSON_EXISTS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonExists` |
| json_extract_path | JSON_EXTRACT_PATH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonExtractPath` |
| json_extract_path_text | JSON_EXTRACT_PATH_TEXT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonExtractPathText` |
| json_object_agg | JSON_OBJECT_AGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonObjectAgg` |
| json_object_keys | JSON_OBJECT_KEYS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonObjectKeys` |
| json_query | JSON_QUERY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonQuery` |
| json_scalar | JSON_SCALAR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonScalar` |
| json_serialize | JSON_SERIALIZE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonSerialize` |
| json_strip_nulls | JSON_STRIP_NULLS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonStripNulls` |
| json_typeof | JSON_TYPEOF | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonTypeof` |
| json_value | JSON_VALUE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonValue` |
| to_json | TO_JSON | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJson` |
| to_jsonb | TO_JSONB | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJsonb` |

## JSONB Functions

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| jsonb_array_elements | JSONB_ARRAY_ELEMENTS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElements` |
| jsonb_agg | JSONB_AGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbAgg` |
| jsonb_array_elements_text | JSONB_ARRAY_ELEMENTS_TEXT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElementsText` |
| jsonb_array_length | JSONB_ARRAY_LENGTH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayLength` |
| jsonb_build_array | JSONB_BUILD_ARRAY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbBuildArray` |
| jsonb_build_object | JSONB_BUILD_OBJECT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbBuildObject` |
| jsonb_each | JSONB_EACH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEach` |
| jsonb_each_text | JSONB_EACH_TEXT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEachText` |
| jsonb_exists | JSONB_EXISTS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExists` |
| jsonb_extract_path | JSONB_EXTRACT_PATH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExtractPath` |
| jsonb_extract_path_text | JSONB_EXTRACT_PATH_TEXT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExtractPathText` |
| jsonb_insert | JSONB_INSERT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbInsert` |
| jsonb_object_agg | JSONB_OBJECT_AGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectAgg` |
| jsonb_object_keys | JSONB_OBJECT_KEYS |`MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectKeys` |
| jsonb_path_exists | JSONB_PATH_EXISTS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathExists` |
| jsonb_path_match | JSONB_PATH_MATCH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathMatch` |
| jsonb_path_query | JSONB_PATH_QUERY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathQuery` |
| jsonb_path_query_array | JSONB_PATH_QUERY_ARRAY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathQueryArray` |
| jsonb_path_query_first | JSONB_PATH_QUERY_FIRST | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathQueryFirst` |
| jsonb_pretty | JSONB_PRETTY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPretty` |
| jsonb_set | JSONB_SET | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbSet` |
| jsonb_set_lax | JSONB_SET_LAX | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbSetLax` |
| jsonb_strip_nulls | JSONB_STRIP_NULLS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbStripNulls` |
| jsonb_to_tsvector | JSONB_TO_TSVECTOR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbToTsvector` |
| jsonb_typeof | JSONB_TYPEOF | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbTypeof` |

## Bonus Helpers

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| array | ARRAY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr` |
| value = ANY(list of values) | IN_ARRAY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\InArray` |
| CAST(json ->> node as BIGINT) | JSON_GET_FIELD_AS_INTEGER | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsInteger` |

## Usage Examples

```sql
-- Array and JSON operations
-- Check if array contains specific elements
SELECT e FROM Entity e WHERE CONTAINS(e.tags, ARRAY['important', 'urgent']) = TRUE

-- Find entities with overlapping arrays
SELECT e FROM Entity e WHERE OVERLAPS(e.categories, ARRAY['admin', 'user']) = TRUE

-- Extract JSON field values
SELECT e, JSON_GET_FIELD_AS_TEXT(e.metadata, 'status') as status FROM Entity e

-- Aggregate values into arrays
SELECT e.category, ARRAY_AGG(e.id) as entity_ids FROM Entity e GROUP BY e.category

-- Build JSON objects
SELECT e.id, JSON_BUILD_OBJECT('name', e.name, 'type', e.type) as json_data FROM Entity e

-- Advanced array operations
-- Shuffle array elements
SELECT e, ARRAY_SHUFFLE(e.tags) as shuffled_tags FROM Entity e

-- Replace array elements
SELECT e, ARRAY_REPLACE(e.categories, 'old', 'new') as updated_categories FROM Entity e

-- Check array dimensions
SELECT e, ARRAY_DIMENSIONS(e.matrix) as dimensions FROM Entity e
WHERE ARRAY_NUMBER_OF_DIMENSIONS(e.matrix) > 1
```

**💡 Tips for Usage:**
1. **Boolean operators** should be used with `= TRUE` or `= FALSE` in DQL
2. **Array functions** provide efficient PostgreSQL array operations
3. **JSON functions** support both JSON and JSONB data types
4. **JSONB functions** offer better performance for complex JSON operations
