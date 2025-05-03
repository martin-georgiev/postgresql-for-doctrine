# Available operators

| PostgreSQL operator | Register for DQL as | Implemented by
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
| ilike | ILIKE ([Usage note](USE-CASES-AND-EXAMPLES.md)) | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ilike` |
| similar to | SIMILAR_TO | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SimilarTo` |
| not similar to | NOT_SIMILAR_TO | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotSimilarTo` |
| ~ | REGEXP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Regexp` |
| ~* | IREGEXP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IRegexp` |
| !~ | NOT_REGEXP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotRegexp` |
| !~* | NOT_IREGEXP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotIRegexp` |
| @@ | TSMATCH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsmatch` |
| \|\| | STRCONCAT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StrConcat` |

# Available functions

| PostgreSQL functions | Register for DQL as | Implemented by
|---|---|---|
| all | ALL_OF | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\All` |
| any | ANY_OF | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Any` |
| any_value | ANY_VALUE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AnyValue` |
| array_agg | ARRAY_AGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAgg` |
| array_append | ARRAY_APPEND | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAppend` |
| array_cat | ARRAY_CAT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCat` |
| array_dims | ARRAY_DIMENSIONS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayDimensions` |
| array_length | ARRAY_LENGTH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLength` |
| array_ndims | ARRAY_NUMBER_OF_DIMENSIONS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayNumberOfDimensions` |
| array_position | ARRAY_POSITION | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPosition` |
| array_positions | ARRAY_POSITIONS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPositions` |
| array_prepend | ARRAY_PREPEND | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPrepend` |
| array_remove | ARRAY_REMOVE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayRemove` |
| array_replace | ARRAY_REPLACE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReplace` |
| array_shuffle | ARRAY_SHUFFLE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayShuffle` |
| array_to_json | ARRAY_TO_JSON | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToJson` |
| array_to_string | ARRAY_TO_STRING | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToString` |
| cardinality | ARRAY_CARDINALITY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cardinality` |
| cast | CAST | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cast` |
| date_add | DATE_ADD | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateAdd` |
| date_bin | DATE_BIN | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateBin` |
| date_subtract | DATE_SUBTRACT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateSubtract` |
| daterange | DATERANGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Daterange` |
| extract | DATE_EXTRACT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateExtract` |
| greatest | GREATEST | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Greatest` |
| int4range | INT4RANGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int4range` |
| int8range | INT8RANGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int8range` |
| json_agg | JSON_AGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonAgg` |
| json_array_length | JSON_ARRAY_LENGTH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonArrayLength` |
| json_build_object | JSON_BUILD_OBJECT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonBuildObject` |
| json_each | JSON_EACH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEach` |
| json_each_text | JSON_EACH_TEXT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEachText` |
| json_exists | JSON_EXISTS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonExists` |
| json_object_agg | JSON_OBJECT_AGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonObjectAgg` |
| json_object_keys | JSON_OBJECT_KEYS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonObjectKeys` |
| json_query | JSON_QUERY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonQuery` |
| json_scalar | JSON_SCALAR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonScalar` |
| json_serialize | JSON_SERIALIZE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonSerialize` |
| json_strip_nulls | JSON_STRIP_NULLS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonStripNulls` |
| json_typeof | JSON_TYPEOF | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonTypeof` |
| json_value | JSON_VALUE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonValue` |
| jsonb_array_elements | JSONB_ARRAY_ELEMENTS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElements` |
| jsonb_agg | JSONB_AGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbAgg` |
| jsonb_array_elements_text | JSONB_ARRAY_ELEMENTS_TEXT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElementsText` |
| jsonb_array_length | JSONB_ARRAY_LENGTH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayLength` |
| jsonb_build_object | JSONB_BUILD_OBJECT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbBuildObject` |
| jsonb_each | JSONB_EACH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEach` |
| jsonb_each_text | JSONB_EACH_TEXT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEachText` |
| jsonb_exists | JSONB_EXISTS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExists` |
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
| least | LEAST | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Least` |
| numrange | NUMRANGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Numrange` |
| overlaps | DATE_OVERLAPS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateOverlaps` |
| regexp_count | REGEXP_COUNT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpCount` |
| regexp_instr | REGEXP_INSTR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpInstr` |
| regexp_like | REGEXP_LIKE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpLike` |
| regexp_match | REGEXP_MATCH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpMatch` |
| regexp_replace | REGEXP_REPLACE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpReplace` |
| regexp_substr | REGEXP_SUBSTR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpSubstr` |
| row | ROW | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Row` |
| row_to_json | ROW_TO_JSON | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RowToJson` |
| split_part | SPLIT_PART | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SplitPart` |
| starts_with | STARTS_WITH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StartsWith` |
| string_agg | STRING_AGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringAgg` |
| string_to_array | STRING_TO_ARRAY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringToArray` |
| to_json | TO_JSON | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJson` |
| to_jsonb | TO_JSONB | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJsonb` |
| to_tsquery | TO_TSQUERY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery` |
| to_tsvector | TO_TSVECTOR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector` |
| tsrange | TSRANGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsrange` |
| tstzrange | TSTZRANGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tstzrange` |
| unaccent | UNACCENT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unaccent` |
| unnest | UNNEST | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unnest` |
| xmlagg | XML_AGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlAgg` |


# Bonus helpers

| PostgreSQL functions | Register for DQL as | Implemented by
|---|---|---|
| array | ARRAY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr` |
| value = ANY(list of values) | IN_ARRAY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\InArray` |
| CAST(json ->> node as BIGINT) | JSON_GET_FIELD_AS_INTEGER | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsInteger` |
