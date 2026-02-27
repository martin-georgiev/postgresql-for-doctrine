Clarification on usage of `ILIKE`, `CONTAINS`, `IS_CONTAINED_BY`, `DATE_OVERLAPS` and some other operator-like functions
---

`Error: Expected =, <, <=, <>, >, >=, !=, got 'ILIKE'"` (or similar) is probably one of the most common DQL errors you may experience when working with this library. The cause for is that when parsing the DQL Doctrine won't recognize `ILIKE` as a known operator. In fact `ILIKE` is registered as a boolean function.
Doctrine doesn't provide easy support for implementing custom operators. This may change in the future but for now it is easier to trick the DQL parser with a boolean expression.

Example intent with PostgreSQL:
```sql
SELECT * FROM emails WHERE subject ILIKE 'Test email';
```

Intuitively, one may assume the below DQL. However it will not work:
```sql
SELECT e
FROM EmailEntity e
WHERE e.subject ILIKE 'Test email'
```

The correct DQL is with a boolean expression that will parse correctly and can look like this:
```sql
SELECT e
FROM EmailEntity e
WHERE ILIKE(e.subject, 'Test email') = TRUE
```

Using JSON_BUILD_OBJECT and JSONB_BUILD_OBJECT
---

These functions currently only support string literals and object references as arguments. Here are some valid examples:

> 📖 **See also**: [Array and JSON Functions](ARRAY-AND-JSON-FUNCTIONS.md) for complete JSON/JSONB function documentation

```sql
-- Basic usage with string literals and entity properties
SELECT JSON_BUILD_OBJECT('name', e.userName, 'email', e.userEmail) FROM User e

-- Multiple key-value pairs
SELECT JSONB_BUILD_OBJECT('id', e.id, 'status', 'active', 'type', e.userType) FROM Employee e

-- Invalid usage (will not work):
SELECT JSON_BUILD_OBJECT('count', COUNT(*))  -- Aggregate functions not supported
SELECT JSONB_BUILD_OBJECT('number', 123)     -- All number types, NULL and boolean values not supported currently
```

Note: Keys must always be string literals, while values can be either string literals or object property references.

Using JSON Path Functions
---

PostgreSQL 14+ introduced JSON path functions that provide a powerful way to query JSON data. Here are some examples:

> 📖 **See also**: [Array and JSON Functions](ARRAY-AND-JSON-FUNCTIONS.md) for complete JSONB path function documentation

```sql
-- Check if a JSON path exists with a condition
SELECT e FROM Entity e WHERE JSONB_PATH_EXISTS(e.jsonData, '$.items[*] ? (@.price > 100)') = TRUE

-- Check if a JSON path matches a condition
SELECT e FROM Entity e WHERE JSONB_PATH_MATCH(e.jsonData, 'exists($.items[*] ? (@.price >= 50 && @.price <= 100))') = TRUE

-- Extract all items matching a path query
SELECT e.id, JSONB_PATH_QUERY(e.jsonData, '$.items[*].name') FROM Entity e

-- Extract all items as an array
SELECT e.id, JSONB_PATH_QUERY_ARRAY(e.jsonData, '$.items[*].id') FROM Entity e

-- Extract the first item matching a path query
SELECT e.id, JSONB_PATH_QUERY_FIRST(e.jsonData, '$.items[*] ? (@.featured == true)') FROM Entity e
```

Using Regular Expression Functions
---

PostgreSQL 15+ introduced additional regular expression functions that provide more flexibility when working with text data:

> 📖 **See also**: [Text and Pattern Functions](TEXT-AND-PATTERN-FUNCTIONS.md) for complete regular expression and text processing documentation

```sql
-- Count occurrences of a pattern
SELECT e.id, REGEXP_COUNT(e.text, '\d{3}-\d{2}-\d{4}') as ssn_count FROM Entity e

-- Find position of a pattern
SELECT e.id, REGEXP_INSTR(e.text, 'important') as position FROM Entity e

-- Extract substring matching a pattern
SELECT e.id, REGEXP_SUBSTR(e.text, 'https?://[\w.-]+') as url FROM Entity e
```

Using Date Functions
---

PostgreSQL 14+ introduced additional date functions that provide more flexibility when working with dates and timestamps:

> 📖 **See also**: [Date and Range Functions](DATE-AND-RANGE-FUNCTIONS.md) for complete date/time and range function documentation

```sql
-- Bin timestamps into 15-minute intervals
SELECT DATE_BIN('15 minutes', e.createdAt, '2001-01-01') FROM Entity e

-- Add an interval to a timestamp (timezone parameter is optional)
SELECT DATE_ADD(e.timestampWithTz, '1 day') FROM Entity e
SELECT DATE_ADD(e.timestampWithTz, '1 day', 'Europe/London') FROM Entity e

-- Subtract an interval from a timestamp (timezone parameter is optional)
SELECT DATE_SUBTRACT(e.timestampWithTz, '2 hours') FROM Entity e
SELECT DATE_SUBTRACT(e.timestampWithTz, '2 hours', 'UTC') FROM Entity e

-- Truncate a timestamp to a specified precision (timezone parameter is optional)
SELECT DATE_TRUNC('day', e.timestampWithTz) FROM Entity e
SELECT DATE_TRUNC('day', e.timestampWithTz, 'UTC') FROM Entity e
```

Using Range Types
---

PostgreSQL range types allow you to work with ranges of values efficiently. Here are practical examples:

> 📖 **See also**: [Range Types](RANGE-TYPES.md) for complete range value object documentation and [Date and Range Functions](DATE-AND-RANGE-FUNCTIONS.md) for range functions

```php
// Entity with range fields
#[ORM\Entity]
class Product
{
    #[ORM\Column(type: 'numrange')]
    private ?NumericRange $priceRange = null;

    #[ORM\Column(type: 'daterange')]
    private ?DateRange $availabilityPeriod = null;
}

// Create ranges
$product = new Product();
$product->setPriceRange(new NumericRange(10.50, 99.99));
$product->setAvailabilityPeriod(new DateRange(
    new \DateTimeImmutable('2024-01-01'),
    new \DateTimeImmutable('2024-12-31')
));

// Check if values are in range
if ($product->getPriceRange()->contains(25.00)) {
    echo "Price is in range";
}
```

```sql
-- Find products with overlapping price ranges
SELECT p FROM Product p WHERE OVERLAPS(p.priceRange, NUMRANGE(20, 50)) = TRUE

-- Find products available in a specific period
SELECT p FROM Product p WHERE CONTAINS(p.availabilityPeriod, DATERANGE('2024-06-01', '2024-06-30')) = TRUE

-- Find products with prices in a specific range
SELECT p FROM Product p WHERE p.priceRange @> 25.0
```


Using PostgreSQL Composite Types
---

PostgreSQL composite types allow you to define custom structured types with named fields. This library provides the `COMPOSITE_FIELD` function to access fields from composite type columns in DQL.

> 📖 **See also**: [PostgreSQL Composite Types Documentation](https://www.postgresql.org/docs/17/rowtypes.html)

### Creating Composite Types in PostgreSQL

```sql
-- Create a composite type for inventory items
CREATE TYPE inventory_item AS (
    name TEXT,
    supplier_id INTEGER,
    price NUMERIC(10,2)
);

-- Create a table using the composite type
CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    item inventory_item
);

-- Insert data using ROW constructor
INSERT INTO products (item) VALUES (ROW('Widget', 1, 9.99));
```

### Accessing Composite Fields in DQL

```sql
-- Access a field from a composite type
SELECT COMPOSITE_FIELD(p.item, 'name') FROM Product p

-- Use composite fields in WHERE clauses
SELECT p FROM Product p WHERE COMPOSITE_FIELD(p.item, 'price') > 10.00
```

### Entity Configuration

```php
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Map composite type column as string - the actual type is handled by PostgreSQL
    #[ORM\Column(type: 'string')]
    private string $item;
}
```

Using PostGIS Types
---


### Using PostGIS Types with Doctrine DBAL (Geometry/Geography)

```php
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

// Insert a single geometry value
$qb = $connection->createQueryBuilder();
$qb->insert('places')->values(['location' => ':wktSpatialData']);
$qb->setParameter('wktSpatialData', WktSpatialData::fromWkt('POINT(1 2)'), 'geometry');
$qb->executeStatement();

// Insert a single geography value with SRID
$qb = $connection->createQueryBuilder();
$qb->insert('places')->values(['boundary' => ':wktSpatialData']);
$qb->setParameter('wktSpatialData', WktSpatialData::fromWkt('SRID=4326;POINT(-122.4194 37.7749)'), 'geography');
$qb->executeStatement();

// Insert a single-item geometry[] array
$qb = $connection->createQueryBuilder();
$qb->insert('routes')->values(['geometriesLines' => ':wktSpatialData']);
$qb->setParameter('wktSpatialData', [WktSpatialData::fromWkt('LINESTRING(0 0, 1 1)')], 'geometry[]');
$qb->executeStatement();
```

Dimensional modifiers are supported and normalized:

```
POINTZ(1 2 3)              => POINT Z(1 2 3)
LINESTRINGM(0 0 1, 1 1 2)  => LINESTRING M(0 0 1, 1 1 2)
POLYGONZM((...))           => POLYGON ZM((...))
POINT Z (1 2 3)            => POINT Z(1 2 3)
```

### Using PostGIS Spatial Operators in DQL

PostGIS spatial operators allow you to perform spatial queries using bounding box relationships and distance calculations. **Important**: All spatial operators return boolean values and shall be used with `= TRUE` or `= FALSE` in DQL.

> 📖 **See also**: [PostGIS Spatial Functions and Operators](SPATIAL-FUNCTIONS-AND-OPERATORS.md) for complete spatial function documentation

#### Bounding Box Spatial Relationships

```sql
-- Find geometries to the left of a reference point
SELECT e FROM Entity e WHERE STRICTLY_LEFT(e.geometry, 'POINT(0 0)') = TRUE

-- Find geometries that spatially contain a point (bounding box level)
SELECT e FROM Entity e WHERE SPATIAL_CONTAINS(e.polygon, 'POINT(1 1)') = TRUE

-- Find geometries contained within a bounding box
SELECT e FROM Entity e WHERE SPATIAL_CONTAINED_BY(e.geometry, 'POLYGON((0 0, 10 10, 20 20, 0 0))') = TRUE

-- Check if two geometries have the same bounding box
SELECT e FROM Entity e WHERE SPATIAL_SAME(e.geometry1, e.geometry2) = TRUE

-- Vertical relationships
SELECT e FROM Entity e WHERE STRICTLY_ABOVE(e.geometry, 'LINESTRING(0 0, 5 0)') = TRUE
SELECT e FROM Entity e WHERE OVERLAPS_BELOW(e.geometry, 'POLYGON((0 5, 5 5, 5 10, 0 10, 0 5))') = TRUE

-- 3D spatial relationships
SELECT e FROM Entity e WHERE ND_OVERLAPS(e.geometry3d, 'POLYGON Z((0 0 0, 1 1 1, 2 2 2, 0 0 0))') = TRUE
```

#### Distance-Based Queries

```sql
-- Find the 10 nearest geometries to a point
SELECT e, GEOMETRY_DISTANCE(e.geometry, 'POINT(0 0)') as distance
FROM Entity e
ORDER BY distance
LIMIT 10

-- Find geometries within a specific distance (using bounding box distance for performance)
SELECT e FROM Entity e WHERE BOUNDING_BOX_DISTANCE(e.geometry, 'POINT(0 0)') < 1000

-- Calculate trajectory distances (for linestrings with measure values)
SELECT TRAJECTORY_DISTANCE(e.trajectory1, e.trajectory2) as closest_approach
FROM Entity e
WHERE e.trajectory1 IS NOT NULL

-- 3D distance calculations
SELECT e, ND_CENTROID_DISTANCE(e.geometry3d1, e.geometry3d2) as distance3d
FROM Entity e
WHERE ND_BOUNDING_BOX_DISTANCE(e.geometry3d1, e.geometry3d2) < 500
```

#### Operator Conflicts and Best Practices

Some operators have different meanings for different data types. Use specific function names to avoid conflicts:

```sql
-- ✅ CORRECT: Use specific function names
SELECT e FROM Entity e WHERE CONTAINS(e.tags, ARRAY('tag1')) = TRUE      -- Array containment
SELECT e FROM Entity e WHERE SPATIAL_CONTAINS(e.polygon, e.point) = TRUE -- Spatial containment
SELECT e FROM Entity e WHERE REGEXP(e.text, 'pattern') = TRUE            -- Text pattern matching

-- ❌ AVOID: Ambiguous usage that might conflict
-- The @ and ~ operators have different meanings for arrays vs spatial data
```

#### Performance Tips

```sql
-- Use bounding box operators for initial filtering (they use spatial indexes)
SELECT e FROM Entity e
WHERE OVERLAPS(e.geometry, 'POLYGON((0 0, 10 10, 20 20, 0 0))') = TRUE
  AND ST_Intersects(e.geometry, 'POLYGON((0 0, 10 10, 20 20, 0 0))')  -- Exact check

-- Use distance operators for nearest neighbor queries
SELECT e FROM Entity e
ORDER BY GEOMETRY_DISTANCE(e.geometry, 'POINT(0 0)')
LIMIT 10
```

For multi-item arrays, see [GEOMETRY-ARRAYS.md](./GEOMETRY-ARRAYS.md) for Doctrine DQL limitations and the suggested workarounds.

The library provides DBAL type support for PostGIS `geometry` and `geography` types. Example usage:

```sql
CREATE TABLE places (
    id SERIAL PRIMARY KEY,
    location GEOMETRY,
    boundary GEOGRAPHY
);
```

```php
use Doctrine\DBAL\Types\Type;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Geometry as GeometryValueObject;

Type::addType('geography', MartinGeorgiev\Doctrine\DBAL\Types\Geography::class);
Type::addType('geometry', MartinGeorgiev\Doctrine\DBAL\Types\Geometry::class);

$location = GeometryValueObject::fromWKT('SRID=4326;POINT(-122.4194 37.7749)');
$entity->setLocation($location);
```

Notes:
- Values round-trip as EWKT/WKT strings at the database boundary.
- Integration tests automatically enable the `postgis` extension; ensure PostGIS is available in your environment.
