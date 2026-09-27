# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> Spatial types (foundations)

A `geometry` or `geography` column maps to the `WktSpatialData` value object, which holds the value as WKT or EWKT text. This page shows how to build one, which geometry types it accepts, how to constrain a column, and what fails.

> **See also:** [PostGIS spatial functions and operators](SPATIAL-FUNCTIONS-AND-OPERATORS.md) for working with spatial data in queries

## Creating spatial data

Build a `WktSpatialData` from a string, from its parts, or with the point shortcuts:

### From a WKT or EWKT string
```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

$point = WktSpatialData::fromString('POINT(1 2)');
$pointWithSrid = WktSpatialData::fromString('SRID=4326;POINT(-122.4194 37.7749)');
$line = WktSpatialData::fromString('LINESTRING(0 0, 1 1, 2 2)');
```

### From its parts
```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\GeometryType;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DimensionalModifier;

$point = WktSpatialData::fromComponents(
    GeometryType::POINT,
    '1 2'
);

// With SRID
$pointWithSrid = WktSpatialData::fromComponents(
    GeometryType::POINT,
    '-122.4194 37.7749',
    4326
);

// With dimensional modifier
$line3d = WktSpatialData::fromComponents(
    GeometryType::LINESTRING,
    '0 0 1, 1 1 2, 2 2 3',
    null,
    DimensionalModifier::Z
);

// With all parameters
$polygon4d = WktSpatialData::fromComponents(
    GeometryType::POLYGON,
    '0 0 0 1, 0 1 0 1, 1 1 0 1, 1 0 0 1, 0 0 0 1',
    4326,
    DimensionalModifier::ZM
);
```

### Point shortcuts
```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

// Simple 2D point
$point = WktSpatialData::point(1, 2);
// Result: POINT(1 2)

// Point with SRID (common for geographic coordinates)
$location = WktSpatialData::point(-122.4194, 37.7749, 4326);
// Result: SRID=4326;POINT(-122.4194 37.7749)

// 3D point with elevation
$point3d = WktSpatialData::point3d(-122.4194, 37.7749, 100);
// Result: POINT Z(-122.4194 37.7749 100)

// 3D point with SRID
$location3d = WktSpatialData::point3d(-122.4194, 37.7749, 100, 4326);
// Result: SRID=4326;POINT Z(-122.4194 37.7749 100)
```

## Supported geometry types

`WktSpatialData` accepts every geometry type PostGIS has; the `GeometryType` enum lists them.

### Basic geometry types
```php
// Point geometry
$point = WktSpatialData::fromString('POINT(1 2)');
$point3d = WktSpatialData::fromString('POINT Z(1 2 3)');
$pointMeasured = WktSpatialData::fromString('POINT M(1 2 4)');
$point4d = WktSpatialData::fromString('POINT ZM(1 2 3 4)');

// Line geometry
$line = WktSpatialData::fromString('LINESTRING(0 0, 1 1, 2 2)');
$line3d = WktSpatialData::fromString('LINESTRING Z(0 0 0, 1 1 1, 2 2 2)');

// Polygon geometry
$polygon = WktSpatialData::fromString('POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))');
$polygonWithHoles = WktSpatialData::fromString('POLYGON((0 0, 0 3, 3 3, 3 0, 0 0), (1 1, 1 2, 2 2, 2 1, 1 1))');
```

### Multi-geometry types
```php
// Multi-point
$multiPoint = WktSpatialData::fromString('MULTIPOINT((1 2), (3 4), (5 6))');

// Multi-line
$multiLine = WktSpatialData::fromString('MULTILINESTRING((0 0, 1 1), (2 2, 3 3))');

// Multi-polygon
$multiPolygon = WktSpatialData::fromString('MULTIPOLYGON(((0 0, 0 1, 1 1, 1 0, 0 0)), ((2 2, 2 3, 3 3, 3 2, 2 2)))');
```

### Collection types
```php
// Geometry collection
$collection = WktSpatialData::fromString('GEOMETRYCOLLECTION(POINT(1 2), LINESTRING(0 0, 1 1))');
```

### Curved geometry types
```php
// Circular string
$circularString = WktSpatialData::fromString('CIRCULARSTRING(0 0, 1 1, 2 0)');

// Compound curve
$compoundCurve = WktSpatialData::fromString('COMPOUNDCURVE((0 0, 1 1), CIRCULARSTRING(1 1, 2 0, 3 1))');

// Curve polygon
$curvePolygon = WktSpatialData::fromString('CURVEPOLYGON(CIRCULARSTRING(0 0, 1 1, 2 0, 0 0))');

// Multi-curve
$multiCurve = WktSpatialData::fromString('MULTICURVE((0 0, 1 1), CIRCULARSTRING(1 1, 2 0, 3 1))');

// Multi-surface
$multiSurface = WktSpatialData::fromString('MULTISURFACE(CURVEPOLYGON(CIRCULARSTRING(0 0, 1 1, 2 0, 0 0)))');
```

### Triangle and TIN types
```php
// Triangle
$triangle = WktSpatialData::fromString('TRIANGLE((0 0, 1 0, 0.5 1, 0 0))');

// TIN (Triangulated Irregular Network)
$tin = WktSpatialData::fromString('TIN(((0 0, 1 0, 0.5 1, 0 0)), ((1 0, 2 0, 1.5 1, 1 0)))');

// Polyhedral surface
$polyhedralSurface = WktSpatialData::fromString('POLYHEDRALSURFACE(((0 0, 0 1, 1 1, 1 0, 0 0)), ((0 0, 0 1, 0 0 1, 0 0)))');
```

## Column options for DDL

By default `geometry` and `geography` columns are declared as bare `GEOMETRY` / `GEOGRAPHY`. A bare `GEOMETRY` column accepts any subtype and any SRID. A bare `GEOGRAPHY` column is [narrower](https://postgis.net/docs/using_postgis_dbmanagement.html#Create_Geography_Tables): it accepts only geography-compatible subtypes (PostGIS rejects `TIN`, for example) and geodetic lon/lat SRIDs, and stores SRID-less input as SRID 4326. Two column options add a PostGIS type modifier so the constraint is enforced by PostgreSQL itself:

| Option | Type | Meaning |
|---|---|---|
| `geometry_type` | string | The geometry subtype, such as `Point`, `LineString`, `MultiPolygon`. Case-insensitive. May carry a dimensional modifier suffix: `PointZ`, `PointM`, `PointZM`. Use `Geometry` for "any subtype". |
| `srid` | int | The spatial reference system identifier, such as `4326`. Must be a non-negative integer. |

```php
use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

#[ORM\Entity]
class Place
{
    // GEOGRAPHY(POINT,4326)
    #[ORM\Column(type: 'geography', options: ['geometry_type' => 'Point', 'srid' => 4326])]
    private WktSpatialData $location;

    // GEOMETRY(POLYGONZ) - 3D polygons, SRID unconstrained
    #[ORM\Column(type: 'geometry', options: ['geometry_type' => 'PolygonZ'])]
    private WktSpatialData $volume;

    // GEOMETRY(GEOMETRY,3857) - any subtype, but SRID pinned to Web Mercator
    #[ORM\Column(type: 'geometry', options: ['srid' => 3857])]
    private WktSpatialData $tileShape;

    // GEOMETRY - unconstrained
    #[ORM\Column(type: 'geometry')]
    private WktSpatialData $shape;
}
```

Both options are optional and independent:

- Neither option → bare `GEOMETRY` / `GEOGRAPHY`.
- `geometry_type` only → `GEOMETRY(POINT)`.
- `srid` only → `GEOMETRY(GEOMETRY,4326)`, since PostGIS requires a subtype whenever an SRID is given.

### Caveats

- The options only shape the DDL that Doctrine generates. They do not alter value conversion - a `WktSpatialData` carrying a different subtype is still handed to PostgreSQL, which rejects it at insert time.
- `geography` only supports lon/lat reference systems; PostgreSQL rejects `GEOGRAPHY(POINT,3857)` at `CREATE TABLE` time.
- A constrained column coerces values that carry no SRID: inserting `POINT(1 2)` into `GEOMETRY(POINT,4326)` stores `SRID=4326;POINT(1 2)`.
- Doctrine's schema comparator does not understand PostGIS type modifiers, so `doctrine:schema:update` and diff-based migration generation may report spurious changes for these columns. Manage them with explicit migrations.
- [Spatial (GiST) indexes](https://postgis.net/docs/using_postgis_dbmanagement.html#gist_indexes) are not covered by these options. Declare them in a migration with raw SQL: `CREATE INDEX idx_place_location ON place USING GIST (location);`

## Geography vs geometry specifics

- Both accept WKT and [EWKT](https://postgis.net/docs/using_postgis_dbmanagement.html#EWKB_EWKT), which adds the SRID in front: `SRID=4326;POINT(1 2)`.
- A `geography` value always has an SRID. PostGIS stores SRID-less input as 4326, so `POINT(1 2)` reads back as `SRID=4326;POINT(1 2)`.
- Both normalize the dimensional modifiers (`Z`, `M`, `ZM`) the same way.

## Arrays

- `GEOMETRY[]` and `GEOGRAPHY[]` bind through DBAL parameter binding, with any number of elements.
- A `null` element is written as a SQL NULL element and read back as `null`.

See [Geometry and geography arrays](GEOMETRY-ARRAYS.md) for details and examples.

## Registering and binding

### Registration

```php
use Doctrine\DBAL\Types\Type as DoctrineType;

DoctrineType::addType('geometry', \MartinGeorgiev\Doctrine\DBAL\Types\Geometry::class);
DoctrineType::addType('geometry[]', \MartinGeorgiev\Doctrine\DBAL\Types\GeometryArray::class);
DoctrineType::addType('geography', \MartinGeorgiev\Doctrine\DBAL\Types\Geography::class);
DoctrineType::addType('geography[]', \MartinGeorgiev\Doctrine\DBAL\Types\GeographyArray::class);
```

### Binding a single value

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

$qb = $connection->createQueryBuilder();
$qb->insert('places')->values(['location' => ':location']);
$qb->setParameter('location', WktSpatialData::fromString('SRID=4326;POINT(-122.4194 37.7749)'), 'geography');
$qb->executeStatement();
```

### Binding an array

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

$qb = $connection->createQueryBuilder();
$qb->insert('locations')->values(['geometries' => ':geometries']);
$qb->setParameter('geometries', [
    WktSpatialData::fromString('POINT(0 0)'),
    WktSpatialData::fromString('POINT(1 1)'),
], 'geometry[]');
$qb->executeStatement();
```

See [Geometry and geography arrays](GEOMETRY-ARRAYS.md) for more array examples.

## Error handling and validation

A malformed string fails when you build the value object, before anything reaches PostgreSQL. A wrong value handed to the DBAL type fails in the type.

### Invalid WKT

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidWktSpatialDataException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

try {
    // Unknown geometry type
    $invalid = WktSpatialData::fromString('INVALID(1 2)');
} catch (InvalidWktSpatialDataException $e) {
    // Throws: "Unsupported geometry type: 'INVALID'. Supported types: POINT, LINESTRING, POLYGON, …"
}

try {
    // Empty coordinate section
    $empty = WktSpatialData::fromString('POINT()');
} catch (InvalidWktSpatialDataException $e) {
    // Throws: "Invalid Wkt: empty coordinate/body section"
}

try {
    // Invalid SRID format
    $invalidSrid = WktSpatialData::fromString('SRID=abc;POINT(1 2)');
} catch (InvalidWktSpatialDataException $e) {
    // Throws: "Invalid Srid value in Ewkt: 'abc'"
}

try {
    // Missing semicolon in EWKT
    $missingSemicolon = WktSpatialData::fromString('SRID=4326POINT(1 2)');
} catch (InvalidWktSpatialDataException $e) {
    // Throws: "Invalid Ewkt: missing semicolon after Srid prefix"
}
```

### Invalid values in the DBAL type

```php
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeometryForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeometryForPHPException;

// Invalid type passed to a geometry column
try {
    $geometryType->convertToDatabaseValue('not a geometry', $platform);
} catch (InvalidGeometryForDatabaseException $e) {
    // Throws: "Value must be a Geometry value object, 'not a geometry' given"
}

// Invalid format from the database
try {
    $geometryType->convertToPHPValue('invalid wkt from db', $platform);
} catch (InvalidGeometryForPHPException $e) {
    // Throws: "Invalid Geometry value object format: 'invalid wkt from db'"
}
```

### Checking a value before you use it

`fromString()` is the validator: catch its exception to test a string, then read the type and SRID from the value object.

```php
function validateSpatialData(string $wkt): bool {
    try {
        WktSpatialData::fromString($wkt);
        return true;
    } catch (InvalidWktSpatialDataException) {
        return false;
    }
}

$spatialData = WktSpatialData::fromString('SRID=4326;POINT(-122 37)');
$spatialData->getGeometryType(); // GeometryType::POINT
$spatialData->getSrid();         // 4326, or null when the string carries no SRID
```

## How the values are parsed

`WktSpatialData::fromString()` reads WKT and EWKT and writes the dimensional modifier in one spelling:

- `POINTZ(...)` → `POINT Z(...)`
- `LINESTRINGM(...)` → `LINESTRING M(...)`
- `POLYGONZM(...)` → `POLYGON ZM(...)`
- `POINT Z (...)` → `POINT Z(...)`
- `SRID=4326;POINT Z (...)` → `SRID=4326;POINT Z(...)`

The type names and modifiers it accepts come from two enums, `GeometryType` and `DimensionalModifier`.

`GeometryArray` and `GeographyArray` share the `SpatialDataArray` base. It splits a PostgreSQL array literal into items, quoted or not, and keeps an item whole when its coordinate list contains commas or nested parentheses.
