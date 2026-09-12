# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> Spatial Types (Foundations)

This document describes the core primitives used by the spatial DBAL types: parsing, normalization, and enum-driven patterns.

## SpatialDataArray base class

`SpatialDataArray` is the base for `GeometryArray` and `GeographyArray`. It provides:
- Parsing of PostgreSQL array literals containing WKT/EWKT elements
  - Handles nested parentheses and quoted/unquoted array elements
  - Splits correctly even when commas occur inside coordinate lists
- Normalization of dimensional modifiers and spacing
  - `POINTZ(...)` → `POINT Z(...)`
  - `LINESTRINGM(...)` → `LINESTRING M(...)`
  - `POLYGONZM(...)` → `POLYGON ZM(...)`
  - `POINT Z (...)` → `POINT Z(...)`
  - `SRID=4326;POINT Z (...)` → `SRID=4326;POINT Z(...)`

Parsing outputs a list of `WktSpatialData` value objects that Doctrine DBAL can bind.

> 📖 **See also**: [PostGIS Spatial Functions and Operators](SPATIAL-FUNCTIONS-AND-OPERATORS.md) for working with spatial data in queries

## Enum-driven patterns

Two enums drive normalization so the code and docs remain consistent:
- `GeometryType` – set of supported geometry type names (`POINT`, `LINESTRING`, `POLYGON`, etc.)
- `DimensionalModifier` – dimensional markers (`Z`, `M`, `ZM`)

Regex patterns for geometry type detection and dimensional modifier handling are built from these enums instead of hardcoded strings.

## Creating Spatial Data

The `WktSpatialData` value object provides multiple ways to create spatial data:

### From WKT String (Traditional)
```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

// Parse complete WKT/EWKT strings
$point = WktSpatialData::fromWkt('POINT(1 2)');
$pointWithSrid = WktSpatialData::fromWkt('SRID=4326;POINT(-122.4194 37.7749)');
$line = WktSpatialData::fromWkt('LINESTRING(0 0, 1 1, 2 2)');
```

### From Components (Programmatic)
```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\GeometryType;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DimensionalModifier;

// Build from individual components
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

### Convenience Methods for Points
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

## Supported Geometry Types

The library supports all PostGIS geometry types through the `GeometryType` enum:

### Basic Geometry Types
```php
// Point geometry
$point = WktSpatialData::fromWkt('POINT(1 2)');
$point3d = WktSpatialData::fromWkt('POINT Z(1 2 3)');
$pointMeasured = WktSpatialData::fromWkt('POINT M(1 2 4)');
$point4d = WktSpatialData::fromWkt('POINT ZM(1 2 3 4)');

// Line geometry
$line = WktSpatialData::fromWkt('LINESTRING(0 0, 1 1, 2 2)');
$line3d = WktSpatialData::fromWkt('LINESTRING Z(0 0 0, 1 1 1, 2 2 2)');

// Polygon geometry
$polygon = WktSpatialData::fromWkt('POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))');
$polygonWithHoles = WktSpatialData::fromWkt('POLYGON((0 0, 0 3, 3 3, 3 0, 0 0), (1 1, 1 2, 2 2, 2 1, 1 1))');
```

### Multi-Geometry Types
```php
// Multi-point
$multiPoint = WktSpatialData::fromWkt('MULTIPOINT((1 2), (3 4), (5 6))');

// Multi-line
$multiLine = WktSpatialData::fromWkt('MULTILINESTRING((0 0, 1 1), (2 2, 3 3))');

// Multi-polygon
$multiPolygon = WktSpatialData::fromWkt('MULTIPOLYGON(((0 0, 0 1, 1 1, 1 0, 0 0)), ((2 2, 2 3, 3 3, 3 2, 2 2)))');
```

### Collection Types
```php
// Geometry collection
$collection = WktSpatialData::fromWkt('GEOMETRYCOLLECTION(POINT(1 2), LINESTRING(0 0, 1 1))');
```

### Circular Geometry Types (PostGIS Extensions)
```php
// Circular string
$circularString = WktSpatialData::fromWkt('CIRCULARSTRING(0 0, 1 1, 2 0)');

// Compound curve
$compoundCurve = WktSpatialData::fromWkt('COMPOUNDCURVE((0 0, 1 1), CIRCULARSTRING(1 1, 2 0, 3 1))');

// Curve polygon
$curvePolygon = WktSpatialData::fromWkt('CURVEPOLYGON(CIRCULARSTRING(0 0, 1 1, 2 0, 0 0))');

// Multi-curve
$multiCurve = WktSpatialData::fromWkt('MULTICURVE((0 0, 1 1), CIRCULARSTRING(1 1, 2 0, 3 1))');

// Multi-surface
$multiSurface = WktSpatialData::fromWkt('MULTISURFACE(CURVEPOLYGON(CIRCULARSTRING(0 0, 1 1, 2 0, 0 0)))');
```

### Triangle and TIN Types
```php
// Triangle
$triangle = WktSpatialData::fromWkt('TRIANGLE((0 0, 1 0, 0.5 1, 0 0))');

// TIN (Triangulated Irregular Network)
$tin = WktSpatialData::fromWkt('TIN(((0 0, 1 0, 0.5 1, 0 0)), ((1 0, 2 0, 1.5 1, 1 0)))');

// Polyhedral surface
$polyhedralSurface = WktSpatialData::fromWkt('POLYHEDRALSURFACE(((0 0, 0 1, 1 1, 1 0, 0 0)), ((0 0, 0 1, 0 0 1, 0 0)))');
```

## Column options for DDL

By default `geometry` and `geography` columns are declared as bare `GEOMETRY` / `GEOGRAPHY`. A bare `GEOMETRY` column accepts any subtype and any SRID. A bare `GEOGRAPHY` column is narrower: it accepts only geography-compatible subtypes (PostGIS rejects e.g. `TIN`) and geodetic lon/lat SRIDs, and stores SRID-less input as SRID 4326. Two column options add a PostGIS type modifier so the constraint is enforced by PostgreSQL itself:

| Option | Type | Meaning |
|---|---|---|
| `geometry_type` | string | The geometry subtype, e.g. `Point`, `LineString`, `MultiPolygon`. Case-insensitive. May carry a dimensional modifier suffix: `PointZ`, `PointM`, `PointZM`. Use `Geometry` for "any subtype". |
| `srid` | int | The spatial reference system identifier, e.g. `4326`. Must be a non-negative integer. |

```php
use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

#[ORM\Entity]
class Place
{
    // GEOGRAPHY(POINT,4326)
    #[ORM\Column(type: 'geography', options: ['geometry_type' => 'Point', 'srid' => 4326])]
    private WktSpatialData $location;

    // GEOMETRY(POLYGONZ) — 3D polygons, SRID unconstrained
    #[ORM\Column(type: 'geometry', options: ['geometry_type' => 'PolygonZ'])]
    private WktSpatialData $volume;

    // GEOMETRY(GEOMETRY,3857) — any subtype, but SRID pinned to Web Mercator
    #[ORM\Column(type: 'geometry', options: ['srid' => 3857])]
    private WktSpatialData $tileShape;

    // GEOMETRY — unconstrained
    #[ORM\Column(type: 'geometry')]
    private WktSpatialData $shape;
}
```

Both options are optional and independent:

- Neither option → bare `GEOMETRY` / `GEOGRAPHY`.
- `geometry_type` only → `GEOMETRY(POINT)`.
- `srid` only → `GEOMETRY(GEOMETRY,4326)`, since PostGIS requires a subtype whenever an SRID is given.

### Caveats

- The options only shape the DDL that Doctrine generates. They do not alter value conversion — a `WktSpatialData` carrying a different subtype is still handed to PostgreSQL, which rejects it at insert time.
- `geography` only supports lon/lat reference systems; PostgreSQL rejects e.g. `GEOGRAPHY(POINT,3857)` at `CREATE TABLE` time.
- A constrained column coerces values that carry no SRID: inserting `POINT(1 2)` into `GEOMETRY(POINT,4326)` stores `SRID=4326;POINT(1 2)`.
- Doctrine's schema comparator does not understand PostGIS type modifiers, so `doctrine:schema:update` and diff-based migration generation may report spurious changes for these columns. Manage them with explicit migrations.
- Spatial (GiST) indexes are not covered by these options. Declare them in a migration with raw SQL: `CREATE INDEX idx_place_location ON place USING GIST (location);`

## Geography vs Geometry specifics

- Geometry accepts WKT and EWKT (`SRID=...;...`).
- Geography commonly uses SRID 4326; EWKT is supported (e.g., `SRID=4326;POINT(...)`).
- Dimensional modifiers (Z, M, ZM) are normalized consistently for both types.

## Arrays

- `GEOMETRY[]` and `GEOGRAPHY[]` bind through DBAL parameter binding, with any number of elements.
- A `null` element is written as a SQL NULL element and read back as `null`.

See [GEOMETRY-ARRAYS.md](./GEOMETRY-ARRAYS.md) for details and examples.

## Minimal examples

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
$qb->setParameter('location', WktSpatialData::fromWkt('SRID=4326;POINT(-122.4194 37.7749)'), 'geography');
$qb->executeStatement();
```

### Binding an array

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

$qb = $connection->createQueryBuilder();
$qb->insert('locations')->values(['geometries' => ':geometries']);
$qb->setParameter('geometries', [
    WktSpatialData::fromWkt('POINT(0 0)'),
    WktSpatialData::fromWkt('POINT(1 1)'),
], 'geometry[]');
$qb->executeStatement();
```

See [GEOMETRY-ARRAYS.md](./GEOMETRY-ARRAYS.md) for more array examples.

## Error Handling and Validation

The spatial types provide error handling for invalid spatial data:

### Common Validation Errors

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidWktSpatialDataException;

try {
    // Invalid WKT format
    $invalid = WktSpatialData::fromWkt('INVALID(1 2)');
} catch (InvalidWktSpatialDataException $e) {
    // Throws: "Unsupported geometry type: INVALID"
}

try {
    // Empty coordinate section
    $empty = WktSpatialData::fromWkt('POINT()');
} catch (InvalidWktSpatialDataException $e) {
    // Throws: "Empty coordinate section in WKT"
}

try {
    // Invalid SRID format
    $invalidSrid = WktSpatialData::fromWkt('SRID=abc;POINT(1 2)');
} catch (InvalidWktSpatialDataException $e) {
    // Throws: "Invalid SRID value: abc"
}

try {
    // Missing semicolon in EWKT
    $missingSemicolon = WktSpatialData::fromWkt('SRID=4326POINT(1 2)');
} catch (InvalidWktSpatialDataException $e) {
    // Throws: "Missing semicolon in EWKT format"
}
```

### Database Conversion Errors

```php
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeometryForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeographyForPHPException;

// Invalid type passed to geometry column
try {
    $qb->setParameter('geom', 'not a geometry', 'geometry');
} catch (InvalidGeometryForPHPException $e) {
    // Throws: "Invalid type for geometry column"
}

// Invalid format from database
try {
    $geometryType->convertToPHPValue('invalid wkt from db', $platform);
} catch (InvalidGeometryForDatabaseException $e) {
    // Throws: "Invalid format for geometry value"
}
```

### Validation Best Practices

```php
// Validate WKT before database operations
function validateSpatialData(string $wkt): bool {
    try {
        WktSpatialData::fromWkt($wkt);
        return true;
    } catch (InvalidWktSpatialDataException) {
        return false;
    }
}

// Check geometry type before processing
$spatialData = WktSpatialData::fromWkt('POINT(1 2)');
if ($spatialData->getGeometryType() === GeometryType::POINT) {
    // Process point-specific logic
}

// Validate SRID for geography operations
$geographyData = WktSpatialData::fromWkt('SRID=4326;POINT(-122 37)');
if ($geographyData->getSrid() === 4326) {
    // Valid for geography operations
}
```
