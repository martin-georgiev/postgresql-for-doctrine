# Spatial Types (Foundations)

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

## Enum-driven patterns

Two enums drive normalization so the code and docs remain consistent:
- `GeometryType` – set of supported geometry type names (`POINT`, `LINESTRING`, `POLYGON`, etc.)
- `DimensionalModifier` – dimensional markers (`Z`, `M`, `ZM`)

Regex patterns for geometry type detection and dimensional modifier handling are built from these enums instead of hardcoded strings.

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

## Geography vs Geometry specifics

- Geometry accepts WKT and EWKT (`SRID=...;...`).
- Geography commonly uses SRID 4326; EWKT is supported (e.g., `SRID=4326;POINT(...)`).
- Dimensional modifiers (Z, M, ZM) are normalized consistently for both types.

## Arrays and multi-item caveat

- Single-item `GEOMETRY[]` and `GEOGRAPHY[]` arrays work with DBAL parameter binding.
- Multi-item arrays have a PostGIS limitation with array literal parsing. Use one of:
  - ARRAY constructor with per-element casts in raw SQL: `ARRAY[?::geometry, ?::geometry]`
  - Multiple single-item operations
  - Application-level array building

See [GEOMETRY-ARRAYS.md](./GEOMETRY-ARRAYS.md) for details, workarounds, and examples.

## Minimal examples

### Registration

```php
use Doctrine\DBAL\Types\Type;

Type::addType('geometry', \MartinGeorgiev\Doctrine\DBAL\Types\Geometry::class);
Type::addType('geometry[]', \MartinGeorgiev\Doctrine\DBAL\Types\GeometryArray::class);
Type::addType('geography', \MartinGeorgiev\Doctrine\DBAL\Types\Geography::class);
Type::addType('geography[]', \MartinGeorgiev\Doctrine\DBAL\Types\GeographyArray::class);
```

### Binding a single value

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

$qb = $connection->createQueryBuilder();
$qb->insert('places')->values(['location' => ':location']);
$qb->setParameter('location', WktSpatialData::fromWkt('SRID=4326;POINT(-122.4194 37.7749)'), 'geography');
$qb->executeStatement();
```

### Binding a single-item array

```php
$qb = $connection->createQueryBuilder();
$qb->insert('locations')->values(['geometries' => ':geometries']);
$qb->setParameter('geometries', [WktSpatialData::fromWkt('POINT(0 0)')], 'geometry[]');
$qb->executeStatement();
```

For multi-item arrays, see the raw SQL ARRAY constructor examples in [GEOMETRY-ARRAYS.md](./GEOMETRY-ARRAYS.md).

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
