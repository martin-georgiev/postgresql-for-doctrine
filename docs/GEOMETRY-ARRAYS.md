# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> Geometry and geography arrays

A `geometry[]` or `geography[]` column holds several spatial values in one row. This library maps it to a PHP list of `WktSpatialData` value objects, one per item, with `null` for a `NULL` item.

> **See also:** [PostGIS spatial functions and operators](SPATIAL-FUNCTIONS-AND-OPERATORS.md) for spatial functions that work with geometry and geography data

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

The platform mappings let schema introspection recognise the columns. PostgreSQL names an array column's type after its item type with a leading underscore.

## Mapping a column

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

## Writing and reading

Pass a list of `WktSpatialData` to the entity, or bind one with DBAL:

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

$location->setGeometries([
    WktSpatialData::fromString('POINT(1 2)'),
    WktSpatialData::fromString('SRID=3857;LINESTRING(0 0, 1 1)'),
    null,
]);

$qb = $connection->createQueryBuilder();
$qb->insert('locations')->values(['geometries' => ':geometries']);
$qb->setParameter('geometries', [WktSpatialData::fromString('POINT(0 0)')], 'geometry[]');
$qb->executeStatement();
```

Loading the entity gives the same list back. The items of one array may mix geometry types, SRIDs and dimensions, and every type in [Supported geometry types](SPATIAL-TYPES.md#supported-geometry-types) works as an item. A `geography` item written without an SRID reads back with `SRID=4326`, because [PostGIS gives every geography value one](https://postgis.net/docs/using_postgis_dbmanagement.html#Create_Geography_Tables).

## Dimensional modifiers

`WktSpatialData` writes each dimensional modifier in one spelling, so the inputs on the left read back as the values on the right:

```text
POINTZ(1 2 3)               => POINT Z(1 2 3)
LINESTRINGM(0 0 1, 1 1 2)   => LINESTRING M(0 0 1, 1 1 2)
POLYGONZM((...))            => POLYGON ZM((...))
POINT Z (1 2 3)             => POINT Z(1 2 3)
SRID=4326;POINT Z (1 2 3)   => SRID=4326;POINT Z(1 2 3)
```

> **See also:** [Spatial types](SPATIAL-TYPES.md) for the parser behind these rules

## Indexing

A GiST index cannot index an array column, so the items of a `geometry[]` get no spatial index. When you query the items spatially, either:

- store each geometry in its own row, with a GiST index on that column, or
- keep a `geometry` column next to the array that holds their union or bounding box, and index that.

The [PostGIS FAQ on spatial indexes](https://postgis.net/documentation/faq/spatial-indexes/) has more.
