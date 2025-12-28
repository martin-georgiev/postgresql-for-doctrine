# PostGIS Spatial Functions and Operators

This document covers PostGIS spatial functions and operators available in this library for working with geometry and geography data types.

> 📖 **See also**: [Spatial Types](SPATIAL-TYPES.md) for geometry/geography types and [Common Use Cases and Examples](USE-CASES-AND-EXAMPLES.md) for practical spatial query examples

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

## PostGIS Geometry Accessor Functions

These functions return properties and information about geometries.

| PostgreSQL functions | Register for DQL as | Description | Implemented by |
|---|---|---|---|
| ST_CurveN | ST_CURVEN | Returns the Nth curve of a CompoundCurve | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CurveN` |
| ST_HasM | ST_HASM | Returns true if the geometry has an M (measure) coordinate | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_HasM` |
| ST_HasZ | ST_HASZ | Returns true if the geometry has a Z coordinate | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_HasZ` |
| ST_NumCurves | ST_NUMCURVES | Returns the number of curves in a CompoundCurve | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_NumCurves` |

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

## PostGIS Coverage Functions

These functions work with polygonal coverages (sets of non-overlapping polygons that share edges).

| PostgreSQL functions | Register for DQL as | Description | Implemented by |
|---|---|---|---|
| ST_CoverageUnion | ST_COVERAGEUNION | Computes the union of a set of polygons forming a coverage by removing shared edges | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CoverageUnion` |

## PostGIS Geometry Processing Functions

These functions modify and transform geometries including buffering, simplification, coordinate system changes, and geometric transformations.

| PostgreSQL functions | Register for DQL as | Description | Implemented by |
|---|---|---|---|
| ST_Boundary | ST_BOUNDARY | Returns the boundary of a geometry | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Boundary` |
| ST_Buffer | ST_BUFFER | Creates a buffer around a geometry | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Buffer` |
| ST_ConcaveHull | ST_CONCAVEHULL | Returns a possibly concave geometry that encloses all input geometries (PostGIS 3.3+) | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ConcaveHull` |
| ST_ConvexHull | ST_CONVEXHULL | Returns the convex hull of a geometry | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_ConvexHull` |
| ST_CurveToLine | ST_CURVETOLINE | Converts curved geometries to linear geometries | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_CurveToLine` |
| ST_Envelope | ST_ENVELOPE | Returns the bounding box as a polygon | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Envelope` |
| ST_Force2D | ST_FORCE2D | Forces geometry to 2D by removing Z/M coordinates | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Force2D` |
| ST_Force3D | ST_FORCE3D | Forces geometry to 3D by adding Z coordinate if needed | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Force3D` |
| ST_Force4D | ST_FORCE4D | Forces geometry to 4D by adding Z and M coordinates if needed | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Force4D` |
| ST_Letters | ST_LETTERS | Creates geometries that look like letters (PostGIS 3.3+) | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Letters` |
| ST_LineExtend | ST_LINEEXTEND | Returns a line extended forwards and backwards by specified distances | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineExtend` |
| ST_LineToCurve | ST_LINETOCURVE | Converts linear geometries to curved geometries where possible | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_LineToCurve` |
| ST_RemoveIrrelevantPointsForView | ST_REMOVEIRRELEVANTPOINTSFORVIEW | Removes points that are irrelevant for rendering a geometry at a given view | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_RemoveIrrelevantPointsForView` |
| ST_RemoveSmallParts | ST_REMOVESMALLPARTS | Removes small polygon rings and linestrings from a geometry | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_RemoveSmallParts` |
| ST_Reverse | ST_REVERSE | Reverses the order of points in a geometry | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Reverse` |
| ST_Rotate | ST_ROTATE | Rotates a geometry by given angle | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Rotate` |
| ST_Scale | ST_SCALE | Scales a geometry by given factors | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Scale` |
| ST_Simplify | ST_SIMPLIFY | Simplifies geometry using Douglas-Peucker algorithm | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Simplify` |
| ST_SimplifyPolygonHull | ST_SIMPLIFYPOLYGONHULL | Computes a simplified topology-preserving outer or inner hull of a polygon (PostGIS 3.3+) | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SimplifyPolygonHull` |
| ST_SimplifyPreserveTopology | ST_SIMPLIFYPRESERVETOPOLOGY | Simplifies geometry while preserving topology | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SimplifyPreserveTopology` |
| ST_SimplifyVW | ST_SIMPLIFYVW | Simplifies geometry using Visvalingam-Whyatt algorithm | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_SimplifyVW` |
| ST_Transform | ST_TRANSFORM | Transforms geometry to different coordinate system | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Transform` |
| ST_Translate | ST_TRANSLATE | Translates a geometry by given offsets | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_Translate` |
| ST_TriangulatePolygon | ST_TRIANGULATEPOLYGON | Computes the constrained Delaunay triangulation of a polygon (PostGIS 3.3+) | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\ST_TriangulatePolygon` |

## Usage Examples

```sql
-- Bounding box operations
-- Find geometries to the left of a reference point
SELECT e FROM Entity e WHERE STRICTLY_LEFT(e.geometry, 'POINT(0 0)') = TRUE

-- Find overlapping polygons
SELECT e FROM Entity e WHERE SPATIAL_CONTAINS(e.polygon, e.point) = TRUE

-- 3D spatial relationships
SELECT e FROM Entity e WHERE ND_OVERLAPS(e.geometry3d, 'POLYGON Z((0 0 0, 1 1 1, 2 2 2, 0 0 0))') = TRUE

-- Distance calculations
-- Find nearest geometries
SELECT e, GEOMETRY_DISTANCE(e.geometry, 'POINT(0 0)') as distance
FROM Entity e ORDER BY distance LIMIT 10

-- Bounding box distance for index optimization
SELECT e FROM Entity e WHERE BOUNDING_BOX_DISTANCE(e.geometry, 'POINT(0 0)') < 1000

-- 3D distance calculations
SELECT ND_CENTROID_DISTANCE(e.geometry3d1, e.geometry3d2) as distance FROM Entity e

-- Spatial relationship tests
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

-- Test if point is inside circle
SELECT e FROM Entity e WHERE ST_PointInsideCircle(e.point, 0, 0, 1000) = TRUE

-- Analyze line crossing behavior
SELECT e, ST_LineCrossingDirection(e.line1, e.line2) as crossing FROM Entity e
WHERE ST_LineCrossingDirection(e.line1, e.line2) != 0

-- Measurements
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
```

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

**💡 Tips for Usage:**
1. **Boolean functions** should be used with `= TRUE` or `= FALSE` in DQL
2. **Spatial functions** work best with proper geometry types and indexes
3. **3D functions** require geometries with Z coordinates
4. **Geography types** have limited operator support compared to geometry types
