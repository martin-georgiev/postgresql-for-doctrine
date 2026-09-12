# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> Geometry and Geography Arrays

This document explains the usage of PostgreSQL `geometry` and `geography` array types in Doctrine DBAL.

> 📖 **See also**: [PostGIS Spatial Functions and Operators](SPATIAL-FUNCTIONS-AND-OPERATORS.md) for spatial functions that work with geometry and geography data

## Overview

The `GeometryArray` and `GeographyArray` types provide support for PostgreSQL's `GEOMETRY[]` and `GEOGRAPHY[]` array types, allowing you to store collections of spatial data in a single database column. 

## Registration and Type Mapping

```php
use Doctrine\DBAL\Types\Type as DoctrineType;

DoctrineType::addType('geometry', \MartinGeorgiev\Doctrine\DBAL\Types\Geometry::class);
DoctrineType::addType('geometry[]', \MartinGeorgiev\Doctrine\DBAL\Types\GeometryArray::class);
DoctrineType::addType('geography', \MartinGeorgiev\Doctrine\DBAL\Types\Geography::class);
DoctrineType::addType('geography[]', \MartinGeorgiev\Doctrine\DBAL\Types\GeographyArray::class);

$platform = $connection->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('geometry', 'geometry');
$platform->registerDoctrineTypeMapping('_geometry', 'geometry[]');
$platform->registerDoctrineTypeMapping('geography', 'geography');
$platform->registerDoctrineTypeMapping('_geography', 'geography[]');
```

## Basic Usage

### Entity Definition

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;
use Doctrine\ORM\Mapping as ORM;

class Location
{
    /**
     * @var WktSpatialData[]
     * @ORM\Column(type="geometry[]")
     */
    private array $geometries;

    /**
     * @var WktSpatialData[]
     * @ORM\Column(type="geography[]")
     */
    private array $geographies;

    public function setGeometries(WktSpatialData ...$geometries): void
    {
        $this->geometries = $geometries;
    }
}
```

### Parameter Binding with DBAL

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

// Single-item geometry[] array (supported)
$qb = $connection->createQueryBuilder();
$qb->insert('locations')->values(['geometries' => ':wktSpatialData']);
$qb->setParameter('wktSpatialData', [WktSpatialData::fromWkt('POINT(0 0)')], 'geometry[]');
$qb->executeStatement();

// Single geography value
$qb = $connection->createQueryBuilder();
$qb->insert('places')->values(['locations' => ':wktSpatialData']);
$qb->setParameter('wktSpatialData', WktSpatialData::fromWkt('SRID=4326;POINT(-122.4194 37.7749)'), 'geography');
$qb->executeStatement();
```


### Working Examples

```php
// Single-item arrays
$singleGeometry = [WktSpatialData::fromWkt('POINT(0 0)')];
$singleGeography = [WktSpatialData::fromWkt('SRID=4326;POINT(-122.4194 37.7749)')];

// Complex single geometries
$complexGeometry = [WktSpatialData::fromWkt('POLYGON((0 0,0 1,1 1,1 0,0 0))')];
$geometryWithSrid = [WktSpatialData::fromWkt('SRID=4326;LINESTRING(-122 37,-121 38)')];
```

## Multi-Item Arrays

Multi-item `geometry[]` and `geography[]` arrays bind through Doctrine DBAL like any other array type:

```php
$entity->setGeometries([
    WktSpatialData::fromWkt('POINT(1 2)'),
    WktSpatialData::fromWkt('LINESTRING(0 0,1 1)'),
]);
```

A `null` element is written as a SQL NULL element and read back as `null`.

## Normalization Rules (Dimensional Modifiers)

The library normalizes dimensional modifiers based on enums for geometry types and modifiers.

Examples:

```text
POINTZ(1 2 3)               => POINT Z(1 2 3)
LINESTRINGM(0 0 1, 1 1 2)   => LINESTRING M(0 0 1, 1 1 2)
POLYGONZM((...))            => POLYGON ZM((...))
POINT Z (1 2 3)             => POINT Z(1 2 3)
SRID=4326;POINT Z (1 2 3)   => SRID=4326;POINT Z(1 2 3)
```

See also: Spatial foundations and parser behavior in the Spatial Types document.

```php
// Build arrays in application code, then use raw SQL with placeholders
$geometries = ['POINT(1 2)', 'POINT(3 4)', 'LINESTRING(0 0,1 1)'];
$placeholders = implode(',', array_fill(0, count($geometries), '?::geometry'));
$sql = "INSERT INTO locations (geometries) VALUES (ARRAY[$placeholders])";
$connection->executeStatement($sql, $geometries);
```

### Option 4: JSON Storage Alternative

For complex multi-item scenarios, consider using JSON storage:

```php
/**
 * @ORM\Column(type="json")
 */
private array $geometriesAsJson;

public function setGeometries(array $wktStrings): void
{
    $this->geometriesAsJson = $wktStrings;
}

public function getGeometries(): array
{
    return array_map(
        fn(string $wkt) => WktSpatialData::fromWkt($wkt),
        $this->geometriesAsJson
    );
}
```

## Test Coverage

### Integration Tests
- ✅ **Single-item arrays**: Fully tested against real PostgreSQL database
- ✅ **Multi-item arrays**: Bound through DBAL, including arrays carrying a `null` element
- ✅ **All geometry types**: POINT, LINESTRING, POLYGON, MULTIPOINT, etc.
- ✅ **Dimensional modifiers**: Z, M, ZM coordinates
- ✅ **SRID support**: EWKT format with coordinate systems
- ✅ **Geography specifics**: Auto-SRID behavior, world coordinates

The integration tests cover single-item and multi-item arrays bound through DBAL, alongside the SQL `ARRAY[]` constructor form.

### Unit Tests
- ✅ **Multi-item arrays**: Tested for parsing logic
- ✅ **Mixed scenarios**: Different geometry types, SRIDs, dimensions
- ✅ **Edge cases**: Empty arrays, complex combinations
- ✅ **Round-trip conversion**: Database ↔ PHP object conversion

## Supported Features

### Geometry Types
- ✅ POINT, LINESTRING, POLYGON
- ✅ MULTIPOINT, MULTILINESTRING, MULTIPOLYGON
- ✅ GEOMETRYCOLLECTION
- ✅ All other PostGIS geometry types as of v3.5

### Coordinate Systems
- ✅ **SRID support**: `SRID=4326;POINT(-122 37)`
- ✅ **Dimensional modifiers**: Z (elevation), M (measure), ZM
- ✅ **Mixed coordinates**: Arrays with different SRIDs/dimensions

### Geography Features
- ✅ **Auto-SRID**: Geography types automatically get SRID=4326 if none is provided
- ✅ **World coordinates**: Null Island, poles, date line
- ✅ **Geographic calculations**: Proper spherical geometry

## Best Practices

1. **Use single-item arrays** when possible for maximum compatibility
2. **Test thoroughly** with your specific geometry combinations
3. **Consider alternatives** (JSON, separate tables) for complex multi-item scenarios
4. **Use raw SQL** when you need multi-item arrays and can control the SQL generation
5. **State tested versions** — e.g., "Verified on PostgreSQL 16.x + PostGIS 3.5.x"; monitor PostGIS updates in case this changes.

## Performance Considerations

- **Single-item arrays**: Excellent performance, full PostgreSQL optimization
- **Indexing**: GiST/operator classes only support spatial types like `geometry`/`geography` and cannot directly index SQL array types like `geometry[]`. For proper spatial indexing, consider:
  - Normalizing arrays into separate geometry rows with individual GiST indexes
  - Materializing a single geometry (e.g., union or bounding geometry) into a `geometry` column for GiST indexing
  - See the [PostGIS FAQ on spatial indexes](https://postgis.net/documentation/faq/spatial-indexes/) for details
- **Query optimization**: Use appropriate spatial operators and indexes on individual geometry columns, not arrays
