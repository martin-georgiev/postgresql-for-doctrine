# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> Geometry and geography arrays

This page shows how to use the PostgreSQL `geometry[]` and `geography[]` array types with Doctrine.

> **See also:** [PostGIS spatial functions and operators](SPATIAL-FUNCTIONS-AND-OPERATORS.md) for spatial functions that work with geometry and geography data

## Overview

The `GeometryArray` and `GeographyArray` types provide support for PostgreSQL's `GEOMETRY[]` and `GEOGRAPHY[]` array types, allowing you to store collections of spatial data in a single database column. 

## Registration and type mapping

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

## Basic usage

### Entity definition

```php
use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

#[ORM\Entity]
class Location
{
    /** @var list<?WktSpatialData> */
    #[ORM\Column(type: 'geometry[]')]
    private array $geometries;

    /** @var list<?WktSpatialData> */
    #[ORM\Column(type: 'geography[]')]
    private array $geographies;

    /** @param list<?WktSpatialData> $geometries */
    public function setGeometries(array $geometries): void
    {
        $this->geometries = $geometries;
    }
}
```

### Parameter binding with DBAL

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

// Single-item geometry[] array
$qb = $connection->createQueryBuilder();
$qb->insert('locations')->values(['geometries' => ':wktSpatialData']);
$qb->setParameter('wktSpatialData', [WktSpatialData::fromString('POINT(0 0)')], 'geometry[]');
$qb->executeStatement();

// Single geography value
$qb = $connection->createQueryBuilder();
$qb->insert('places')->values(['locations' => ':wktSpatialData']);
$qb->setParameter('wktSpatialData', WktSpatialData::fromString('SRID=4326;POINT(-122.4194 37.7749)'), 'geography');
$qb->executeStatement();
```


### Working examples

```php
// Single-item arrays
$singleGeometry = [WktSpatialData::fromString('POINT(0 0)')];
$singleGeography = [WktSpatialData::fromString('SRID=4326;POINT(-122.4194 37.7749)')];

// Complex single geometries
$complexGeometry = [WktSpatialData::fromString('POLYGON((0 0,0 1,1 1,1 0,0 0))')];
$geometryWithSrid = [WktSpatialData::fromString('SRID=4326;LINESTRING(-122 37,-121 38)')];
```

## Multi-item arrays

Multi-item `geometry[]` and `geography[]` arrays bind through Doctrine DBAL like any other array type:

```php
$entity->setGeometries([
    WktSpatialData::fromString('POINT(1 2)'),
    WktSpatialData::fromString('LINESTRING(0 0,1 1)'),
]);
```

A `null` element is written as a SQL NULL element and read back as `null`.

## Normalization rules (dimensional modifiers)

The library normalizes dimensional modifiers based on enums for geometry types and modifiers.

Examples:

```text
POINTZ(1 2 3)               => POINT Z(1 2 3)
LINESTRINGM(0 0 1, 1 1 2)   => LINESTRING M(0 0 1, 1 1 2)
POLYGONZM((...))            => POLYGON ZM((...))
POINT Z (1 2 3)             => POINT Z(1 2 3)
SRID=4326;POINT Z (1 2 3)   => SRID=4326;POINT Z(1 2 3)
```

> **See also:** [Spatial types](SPATIAL-TYPES.md) for the parser behind these rules

## Supported features

### Geometry types
- POINT, LINESTRING, POLYGON
- MULTIPOINT, MULTILINESTRING, MULTIPOLYGON
- GEOMETRYCOLLECTION
- All other PostGIS geometry types as of v3.5

### Coordinate systems
- **SRID support**: `SRID=4326;POINT(-122 37)`
- **Dimensional modifiers**: Z (elevation), M (measure), ZM
- **Mixed coordinates**: Arrays with different SRIDs/dimensions

### Geography features
- **Auto-SRID**: Geography types [automatically get SRID=4326 if none is provided](https://postgis.net/docs/using_postgis_dbmanagement.html#Create_Geography_Tables)
- **World coordinates**: Null Island, poles, date line
- **Geographic calculations**: Proper spherical geometry

## Performance considerations

- **Indexing**: GiST/operator classes only support spatial types like `geometry`/`geography` and cannot directly index SQL array types like `geometry[]`. For proper spatial indexing, consider:
  - Normalizing arrays into separate geometry rows with individual GiST indexes
  - Materializing a single geometry (e.g., union or bounding geometry) into a `geometry` column for GiST indexing
  - See the [PostGIS FAQ on spatial indexes](https://postgis.net/documentation/faq/spatial-indexes/) for details
- **Query optimization**: Use appropriate spatial operators and indexes on individual geometry columns, not arrays
