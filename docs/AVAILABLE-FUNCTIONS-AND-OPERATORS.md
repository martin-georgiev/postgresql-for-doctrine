# Available Operators

## Operator Conflicts and Usage Notes

**⚠️ Important**: Some PostgreSQL operators have multiple meanings depending on the data types involved. This library provides specific DQL function names to avoid conflicts:

| Operator | Array/JSON Usage | Spatial Usage | Text/Pattern Usage |
|---|---|---|---|
| `@>` | `CONTAINS` (arrays contain elements) | Works automatically with geometry/geography | N/A |
| `<@` | `IS_CONTAINED_BY` (element in array) | Works automatically with geometry/geography | N/A |
| `@` | N/A | `SPATIAL_CONTAINED_BY` (bounding box contained) | N/A |
| `~` | N/A | `SPATIAL_CONTAINS` (bounding box contains) | `REGEXP` (text pattern matching) |
| `&&` | `OVERLAPS` (arrays/ranges overlap) | Works automatically with geometry/geography | N/A |

**Usage Guidelines:**
- **Arrays/JSON**: Use `CONTAINS`, `IS_CONTAINED_BY`, `OVERLAPS` for array and JSON operations
- **Spatial**: Use `SPATIAL_CONTAINS`, `SPATIAL_CONTAINED_BY` for explicit spatial bounding box operations
- **Text**: Use `REGEXP`, `IREGEXP` for pattern matching
- **Boolean operators**: All spatial operators return boolean values and **should be used with `= TRUE` or `= FALSE` in DQL**

## General Operators

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
| ilike | ILIKE ([Usage note](USE-CASES-AND-EXAMPLES.md)) | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ilike` |
| similar to | SIMILAR_TO | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SimilarTo` |
| not similar to | NOT_SIMILAR_TO | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotSimilarTo` |
| ~ | REGEXP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Regexp` |
| ~* | IREGEXP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IRegexp` |
| !~ | NOT_REGEXP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotRegexp` |
| !~* | NOT_IREGEXP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotIRegexp` |
| @@ | TSMATCH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsmatch` |
| \|\| | STRCONCAT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StrConcat` |

## PostGIS Spatial Operators

**⚠️ Important**: Some operators have dual meanings for different data types. Use the specific DQL function names to avoid conflicts:

- **`@`**: Use `CONTAINS` for arrays/JSON, `SPATIAL_CONTAINED_BY` for geometry/geography
- **`~`**: Use `REGEXP` for text patterns, `SPATIAL_CONTAINS` for geometry/geography
- **`&&`**: Use `OVERLAPS` for arrays/JSON, spatial overlaps work automatically with geometry/geography

**📝 Compatibility Notes**:
- Most bounding box operators work primarily with **geometry** types
- **Geography** types have limited operator support (mainly `&&`, `<->`, `<@>`)
- **3D/n-dimensional operators** may require explicit type casting: `ST_GeomFromText('POINT Z(0 0 0)')`
- Some advanced operators (`&&&`, `<<#>>`) may not be available in all PostGIS versions

### Bounding Box Operators

These operators work with geometry and geography bounding boxes. All return boolean values and **should be used with `= TRUE` or `= FALSE` in DQL**.

| PostgreSQL operator | Register for DQL as | Description | Implemented by |
|---|---|---|---|
| &< | OVERLAPS_LEFT | Returns TRUE if A's bounding box overlaps or is to the left of B's | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\OverlapsLeft` |
| &> | OVERLAPS_RIGHT | Returns TRUE if A's bounding box overlaps or is to the right of B's | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\OverlapsRight` |
| << | STRICTLY_LEFT | Returns TRUE if A's bounding box is strictly to the left of B's | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\StrictlyLeft` |
| >> | STRICTLY_RIGHT | Returns TRUE if A's bounding box is strictly to the right of B's | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\StrictlyRight` |
| @ | SPATIAL_CONTAINED_BY | Returns TRUE if A's bounding box is contained by B's (**spatial version**) | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\SpatialContainedBy` |
| ~ | SPATIAL_CONTAINS | Returns TRUE if A's bounding box contains B's (**spatial version**) | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\SpatialContains` |
| ~= | SPATIAL_SAME | Returns TRUE if A's bounding box is the same as B's | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\SpatialSame` |
| \|&> | OVERLAPS_ABOVE | Returns TRUE if A's bounding box overlaps or is above B's | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\OverlapsAbove` |
| \|>> | STRICTLY_ABOVE | Returns TRUE if A's bounding box is strictly above B's | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\StrictlyAbove` |
| &<\| | OVERLAPS_BELOW | Returns TRUE if A's bounding box overlaps or is below B's | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\OverlapsBelow` |
| <<\| | STRICTLY_BELOW | Returns TRUE if A's bounding box is strictly below B's | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\StrictlyBelow` |
| &&& | ND_OVERLAPS | Returns TRUE if A's n-D bounding box intersects B's n-D bounding box | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\NDimensionalOverlaps` |

**Usage Examples:**
```sql
-- Find geometries to the left of a reference point
SELECT e FROM Entity e WHERE STRICTLY_LEFT(e.geometry, 'POINT(0 0)') = TRUE

-- Find overlapping polygons
SELECT e FROM Entity e WHERE SPATIAL_CONTAINS(e.polygon, e.point) = TRUE

-- 3D spatial relationships
SELECT e FROM Entity e WHERE ND_OVERLAPS(e.geometry3d, 'POLYGON Z((0 0 0, 1 1 1, 2 2 2, 0 0 0))') = TRUE
```

### Distance Operators

These operators calculate distances between geometries. All return numeric values.

| PostgreSQL operator | Register for DQL as | Description | Implemented by |
|---|---|---|---|
| <-> | GEOMETRY_DISTANCE | Returns the 2D distance between A and B geometries | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\GeometryDistance` |
| <@> | DISTANCE | Returns distance between points (legacy operator) | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Distance` |
| \|=\| | TRAJECTORY_DISTANCE | Returns distance between trajectories at closest point of approach | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\TrajectoryDistance` |
| <#> | BOUNDING_BOX_DISTANCE | Returns the 2D distance between A and B bounding boxes | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\BoundingBoxDistance` |
| <<->> | ND_CENTROID_DISTANCE | Returns n-D distance between centroids of bounding boxes | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\NDimensionalCentroidDistance` |
| <<#>> | ND_BOUNDING_BOX_DISTANCE | Returns the n-D distance between A and B bounding boxes | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\NDimensionalBoundingBoxDistance` |

**Usage Examples:**
```sql
-- Find nearest geometries
SELECT e, GEOMETRY_DISTANCE(e.geometry, 'POINT(0 0)') as distance
FROM Entity e ORDER BY distance LIMIT 10

-- Bounding box distance for index optimization
SELECT e FROM Entity e WHERE BOUNDING_BOX_DISTANCE(e.geometry, 'POINT(0 0)') < 1000

-- 3D distance calculations
SELECT ND_CENTROID_DISTANCE(e.geometry3d1, e.geometry3d2) as distance FROM Entity e
```

# Available Functions

| PostgreSQL functions | Register for DQL as | Implemented by |
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
| ceil | CEIL | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ceil` |
| date_add | DATE_ADD | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateAdd` |
| date_bin | DATE_BIN | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateBin` |
| date_subtract | DATE_SUBTRACT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateSubtract` |
| daterange | DATERANGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Daterange` |
| extract | DATE_EXTRACT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateExtract` |
| floor | FLOOR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Floor` |
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
| round | ROUND | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Round` |
| row | ROW | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Row` |
| row_to_json | ROW_TO_JSON | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RowToJson` |
| split_part | SPLIT_PART | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SplitPart` |
| starts_with | STARTS_WITH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StartsWith` |
| string_agg | STRING_AGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringAgg` |
| string_to_array | STRING_TO_ARRAY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringToArray` |
| to_char | TO_CHAR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToChar` |
| to_date | TO_DATE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToDate` |
| to_json | TO_JSON | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJson` |
| to_jsonb | TO_JSONB | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJsonb` |
| to_number | TO_NUMBER | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToNumber` |
| to_timestamp | TO_TIMESTAMP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTimestamp` |
| to_tsquery | TO_TSQUERY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery` |
| to_tsvector | TO_TSVECTOR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector` |
| trunc | TRUNC | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trunc` |
| tsrange | TSRANGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsrange` |
| tstzrange | TSTZRANGE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tstzrange` |
| unaccent | UNACCENT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unaccent` |
| unnest | UNNEST | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unnest` |
| xmlagg | XML_AGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlAgg` |
| cbrt | CBRT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cbrt` |
| degrees | DEGREES | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Degrees` |
| exp | EXP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exp` |
| ln | LN | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ln` |
| log | LOG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Log` |
| pi | PI | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Pi` |
| power | POWER | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Power` |
| radians | RADIANS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Radians` |
| random | RANDOM | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Random` |
| sign | SIGN | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sign` |
| width_bucket | WIDTH_BUCKET | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\WidthBucket` |

## PostGIS Spatial Relationship Functions

These functions determine spatial relationships between geometries. Most return boolean values and **should be used with `= TRUE` or `= FALSE` in DQL**, but there are exceptions: `ST_Relate(geom, geom)` returns text (intersection matrix) and `ST_LineCrossingDirection` returns integer (crossing behavior).

| PostgreSQL functions | Register for DQL as | Description | Implemented by |
|---|---|---|---|
| ST_3DDWithin | ST_3DDWITHIN | Tests if two 3D geometries are within a given 3D distance | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DDWithin` |
| ST_3DDFullyWithin | ST_3DDFULLYWITHIN | Tests if two 3D geometries are entirely within a given 3D distance | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DDFullyWithin` |
| ST_3DIntersects | ST_3DINTERSECTS | Tests if two geometries spatially intersect in 3D | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DIntersects` |
| ST_Contains | ST_CONTAINS | Tests if every point of B lies in A, and their interiors have a point in common | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Contains` |
| ST_ContainsProperly | ST_CONTAINSPROPERLY | Tests if every point of B lies in the interior of A | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ContainsProperly` |
| ST_CoveredBy | ST_COVEREDBY | Tests if every point of A lies in B | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CoveredBy` |
| ST_Covers | ST_COVERS | Tests if every point of B lies in A | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Covers` |
| ST_Crosses | ST_CROSSES | Tests if two geometries have some, but not all, interior points in common | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Crosses` |
| ST_DFullyWithin | ST_DFULLYWITHIN | Tests if a geometry is entirely inside a distance of another | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_DFullyWithin` |
| ST_Disjoint | ST_DISJOINT | Tests if two geometries have no points in common | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Disjoint` |
| ST_DWithin | ST_DWITHIN | Tests if two geometries are within a given distance | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_DWithin` |
| ST_Equals | ST_EQUALS | Tests if two geometries include the same set of points | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Equals` |
| ST_Intersects | ST_INTERSECTS | Tests if two geometries intersect (they have at least one point in common) | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Intersects` |
| ST_LineCrossingDirection | ST_LINECROSSINGDIRECTION | Returns a number indicating the crossing behavior of two LineStrings | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineCrossingDirection` |
| ST_OrderingEquals | ST_ORDERINGEQUALS | Tests if two geometries represent the same geometry and have points in the same directional order | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_OrderingEquals` |
| ST_Overlaps | ST_OVERLAPS | Tests if two geometries have the same dimension and intersect, but each has at least one point not in the other | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Overlaps` |
| ST_PointInsideCircle | ST_POINTINSIDECIRCLE | Tests if a point geometry is inside a circle defined by a center and radius | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_PointInsideCircle` |
| ST_Relate | ST_RELATE | Tests if two geometries have a topological relationship matching an Intersection Matrix pattern, or computes their Intersection Matrix | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Relate` |
| ST_RelateMatch | ST_RELATEMATCH | Tests if a DE-9IM Intersection Matrix matches an Intersection Matrix pattern | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_RelateMatch` |
| ST_Touches | ST_TOUCHES | Tests if two geometries have at least one point in common, but their interiors do not intersect | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Touches` |
| ST_Within | ST_WITHIN | Tests if every point of A lies in B, and their interiors have a point in common | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Within` |

**Usage Examples:**
```sql
-- Test if geometries intersect
SELECT e FROM Entity e WHERE ST_Intersects(e.geometry, 'POINT(0 0)') = TRUE

-- Test if one geometry contains another
SELECT e FROM Entity e WHERE ST_Contains(e.polygon, e.point) = TRUE

-- Test if geometries are within a distance
SELECT e FROM Entity e WHERE ST_DWithin(e.geometry, 'POINT(0 0)', 1000) = TRUE

-- Test topological relationships with intersection matrix
SELECT e FROM Entity e WHERE ST_Relate(e.geometry1, e.geometry2, 'T*T***T**') = TRUE

-- Get intersection matrix between two geometries
SELECT e, ST_Relate(e.geometry1, e.geometry2) as matrix FROM Entity e

-- Test if geometries are disjoint (no intersection)
SELECT e FROM Entity e WHERE ST_Relate(e.geometry1, e.geometry2, 'FF*FF****') = TRUE

-- Test if one geometry contains another (contains relationship)
SELECT e FROM Entity e WHERE ST_Relate(e.geometry1, e.geometry2, 'T*****FF*') = TRUE

-- Test if geometries touch (boundary intersection only)
SELECT e FROM Entity e WHERE ST_Relate(e.geometry1, e.geometry2, 'FT*******') = TRUE

-- Test if point is inside circle
SELECT e FROM Entity e WHERE ST_PointInsideCircle(e.point, 0, 0, 1000) = TRUE

-- Analyze line crossing behavior
SELECT e, ST_LineCrossingDirection(e.line1, e.line2) as crossing FROM Entity e
WHERE ST_LineCrossingDirection(e.line1, e.line2) != 0

-- Find lines that cross from left to right
SELECT e FROM Entity e WHERE ST_LineCrossingDirection(e.line1, e.line2) = 1

-- Find lines that cross from right to left
SELECT e FROM Entity e WHERE ST_LineCrossingDirection(e.line1, e.line2) = -1

-- Find lines with multiple crossings
SELECT e FROM Entity e WHERE ST_LineCrossingDirection(e.line1, e.line2) = 2

-- Calculate areas and perimeters
SELECT e, ST_Area(e.polygon) as area, ST_Perimeter(e.polygon) as perimeter 
FROM Entity e WHERE e.polygon IS NOT NULL

-- Calculate 3D measurements
SELECT e, ST_3DLength(e.line3d) as length3d, ST_3DPerimeter(e.polygon3d) as perimeter3d
FROM Entity e WHERE e.line3d IS NOT NULL OR e.polygon3d IS NOT NULL

-- Find geometries within distance
SELECT e, ST_Distance(e.geometry, 'POINT(0 0)') as distance
FROM Entity e ORDER BY distance LIMIT 10

-- Calculate azimuth between points
SELECT e, ST_Azimuth(e.point1, e.point2) as azimuth_radians,
       DEGREES(ST_Azimuth(e.point1, e.point2)) as azimuth_degrees
FROM Entity e WHERE e.point1 IS NOT NULL AND e.point2 IS NOT NULL

-- Geometric operations and transformations
-- Create buffer around geometry
SELECT e, ST_Buffer(e.geometry, 100) as buffered_geometry FROM Entity e

-- Simplify complex geometries
SELECT e, ST_Simplify(e.complex_geometry, 0.5) as simplified_geometry FROM Entity e

-- Transform coordinate systems
SELECT e, ST_Transform(e.geometry, 4326) as wgs84_geometry FROM Entity e

-- Get convex hull
SELECT e, ST_ConvexHull(e.geometry) as convex_hull FROM Entity e

-- Scale and rotate geometries
SELECT e, ST_Scale(ST_Rotate(e.geometry, PI()/4), 2, 2) as scaled_rotated_geometry FROM Entity e

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

-- Text and pattern matching
-- Case-insensitive pattern matching
SELECT e FROM Entity e WHERE IREGEXP(e.name, '^admin.*') = TRUE

-- Extract text using regex
SELECT e, REGEXP_SUBSTR(e.description, 'version [0-9.]+') as version FROM Entity e

-- Replace text patterns
SELECT e, REGEXP_REPLACE(e.content, 'old_pattern', 'new_pattern') as updated_content FROM Entity e

-- Check if text starts with specific string
SELECT e FROM Entity e WHERE STARTS_WITH(e.name, 'user_') = TRUE

-- Full-text search
SELECT e FROM Entity e WHERE TSMATCH(e.search_vector, 'query & terms') = TRUE

-- Date and range operations
-- Add days to date
SELECT e, DATE_ADD(e.created_at, 30) as expiry_date FROM Entity e

-- Extract date components
SELECT e, DATE_EXTRACT(e.timestamp, 'YEAR') as year FROM Entity e

-- Check date overlaps
SELECT e FROM Entity e WHERE DATE_OVERLAPS(e.period1, e.period2) = TRUE

-- Create date ranges
SELECT e, DATERANGE(e.start_date, e.end_date) as date_range FROM Entity e

-- Mathematical operations
SELECT e, POWER(e.value, 2) as squared, SQRT(e.value) as root FROM Entity e
WHERE e.value > 0

**📝 Notes:**
- `ST_Relate` is a variadic function that accepts 2 or 3 arguments:
  - With 2 arguments: returns text (intersection matrix)
  - With 3 arguments: returns boolean (relationship test)
- `ST_LineCrossingDirection` returns an integer (0, 1, -1, or 2) indicating crossing behavior:
  - `0`: No crossing
  - `1`: Left to right crossing
  - `-1`: Right to left crossing
  - `2`: Multiple crossings
- All other functions return boolean values and should be used with `= TRUE` or `= FALSE` in DQL

**🔍 DE-9IM Intersection Matrix Patterns for ST_Relate:**

The DE-9IM (Dimensionally Extended 9-Intersection Model) uses a 9-character pattern where each character represents the intersection between:
- Interior (I), Boundary (B), and Exterior (E) of geometry A
- Interior (I), Boundary (B), and Exterior (E) of geometry B

Common patterns:
- `FF*FF****` = Disjoint (no intersection)
- `T*****FF*` = Contains (A contains B)
- `T*T***T**` = Intersects (geometries intersect)
- `FT*******` = Touches (boundary intersection only)
- `F**T*****` = Within (A is within B)
- `T*T***T**` = Overlaps (partial overlap)

**📊 Function Return Types:**
- **Boolean functions**: Use with `= TRUE` or `= FALSE` in DQL
- **Numeric functions**: Return values for calculations and ordering
- **Geometry functions**: Return new geometries for further operations
- **Text functions**: Return strings for pattern matching and display

## PostGIS Measurement Functions

These functions calculate various measurements of geometries including lengths, areas, distances, and angles.

| PostgreSQL functions | Register for DQL as | Description | Implemented by |
|---|---|---|---|
| ST_Length | ST_LENGTH | Returns the 2D length of LineString/MultiLineString or perimeter of areal geometries | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length` |
| ST_Length2D | ST_LENGTH2D | Returns the 2D length, ignoring Z coordinates | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Length2D` |
| ST_3DLength | ST_3DLENGTH | Returns the 3D length of LineString/MultiLineString or 3D perimeter of areal geometries | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DLength` |
| ST_Area | ST_AREA | Returns the area of polygon/multi-polygon geometries | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Area` |
| ST_Perimeter | ST_PERIMETER | Returns the 2D perimeter of polygon/multi-polygon geometries | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Perimeter` |
| ST_3DPerimeter | ST_3DPERIMETER | Returns the 3D perimeter of polygon/multi-polygon geometries | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DPerimeter` |
| ST_Distance | ST_DISTANCE | Returns the 2D distance between two geometries | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Distance` |
| ST_3DDistance | ST_3DDISTANCE | Returns the 3D distance between two geometries | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_3DDistance` |
| ST_Centroid | ST_CENTROID | Returns the geometric center of a geometry | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Centroid` |
| ST_MaxDistance | ST_MAXDISTANCE | Returns the maximum distance between two geometries | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_MaxDistance` |
| ST_HausdorffDistance | ST_HAUSDORFFDISTANCE | Returns the Hausdorff distance between two geometries | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_HausdorffDistance` |
| ST_FrechetDistance | ST_FRECHETDISTANCE | Returns the Fréchet distance between two geometries | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_FrechetDistance` |
| ST_Azimuth | ST_AZIMUTH | Returns the azimuth between two points in radians | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Azimuth` |
| ST_Project | ST_PROJECT | Projects a point along a geodesic by distance and azimuth | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Project` |

## PostGIS Overlay Functions

These functions perform geometric operations between geometries including intersection, union, difference, and splitting.

| PostgreSQL functions | Register for DQL as | Description | Implemented by |
|---|---|---|---|
| ST_Intersection | ST_INTERSECTION | Returns the point set intersection of two geometries | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Intersection` |
| ST_Union | ST_UNION | Returns the point set union of two geometries | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Union` |
| ST_UnaryUnion | ST_UNARYUNION | Performs unary union on a geometry (dissolves internal boundaries) | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_UnaryUnion` |
| ST_Difference | ST_DIFFERENCE | Returns the point set difference (A - B) | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Difference` |
| ST_SymDifference | ST_SYMDIFFERENCE | Returns the symmetric difference of two geometries | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SymDifference` |
| ST_Split | ST_SPLIT | Splits a geometry by another geometry | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Split` |
| ST_Subdivide | ST_SUBDIVIDE | Subdivides a geometry into smaller parts | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Subdivide` |
| ST_ClipByBox2D | ST_CLIPBYBOX2D | Clips a geometry by a 2D box | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ClipByBox2D` |
| ST_Collect | ST_COLLECT | Collects geometries into a geometry collection | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Collect` |
| ST_CollectionExtract | ST_COLLECTIONEXTRACT | Extracts a specific type from a geometry collection | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CollectionExtract` |
| ST_CollectionHomogenize | ST_COLLECTIONHOMOGENIZE | Homogenizes a geometry collection | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CollectionHomogenize` |

## PostGIS Geometry Processing Functions

These functions modify and transform geometries including buffering, simplification, coordinate system changes, and geometric transformations.

| PostgreSQL functions | Register for DQL as | Description | Implemented by |
|---|---|---|---|
| ST_Buffer | ST_BUFFER | Creates a buffer around a geometry | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Buffer` |
| ST_Simplify | ST_SIMPLIFY | Simplifies geometry using Douglas-Peucker algorithm | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Simplify` |
| ST_SimplifyPreserveTopology | ST_SIMPLIFYPRESERVETOPOLOGY | Simplifies geometry while preserving topology | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SimplifyPreserveTopology` |
| ST_SimplifyVW | ST_SIMPLIFYVW | Simplifies geometry using Visvalingam-Whyatt algorithm | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SimplifyVW` |
| ST_ConvexHull | ST_CONVEXHULL | Returns the convex hull of a geometry | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ConvexHull` |
| ST_Envelope | ST_ENVELOPE | Returns the bounding box as a polygon | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Envelope` |
| ST_Boundary | ST_BOUNDARY | Returns the boundary of a geometry | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Boundary` |
| ST_Transform | ST_TRANSFORM | Transforms geometry to different coordinate system | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Transform` |
| ST_Reverse | ST_REVERSE | Reverses the order of points in a geometry | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Reverse` |
| ST_Force2D | ST_FORCE2D | Forces geometry to 2D by removing Z/M coordinates | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Force2D` |
| ST_Force3D | ST_FORCE3D | Forces geometry to 3D by adding Z coordinate if needed | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Force3D` |
| ST_Force4D | ST_FORCE4D | Forces geometry to 4D by adding Z and M coordinates if needed | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Force4D` |
| ST_CurveToLine | ST_CURVETOLINE | Converts curved geometries to linear geometries | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CurveToLine` |
| ST_LineToCurve | ST_LINETOCURVE | Converts linear geometries to curved geometries where possible | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineToCurve` |
| ST_Scale | ST_SCALE | Scales a geometry by given factors | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Scale` |
| ST_Rotate | ST_ROTATE | Rotates a geometry by given angle | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Rotate` |
| ST_Translate | ST_TRANSLATE | Translates a geometry by given offsets | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Translate` |

# Bonus Helpers

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| array | ARRAY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr` |
| value = ANY(list of values) | IN_ARRAY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\InArray` |
| CAST(json ->> node as BIGINT) | JSON_GET_FIELD_AS_INTEGER | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsInteger` |
