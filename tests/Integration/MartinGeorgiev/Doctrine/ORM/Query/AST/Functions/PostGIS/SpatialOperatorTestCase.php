<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS;

use Tests\Integration\MartinGeorgiev\TestCase as BaseTestCase;

abstract class SpatialOperatorTestCase extends BaseTestCase
{
    private const TABLE_NAME = 'containsgeometries';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestTableForSpatialFixture();
        $this->insertTestDataForSpatialFixture();
    }

    protected function createTestTableForSpatialFixture(): void
    {
        $this->createTestSchema();
        $this->dropTestTableIfItExists(self::TABLE_NAME);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, self::TABLE_NAME);
        $sql = \sprintf('
            CREATE TABLE %s (
                id SERIAL PRIMARY KEY,
                geometry1 GEOMETRY,
                geometry2 GEOMETRY,
                geography1 GEOGRAPHY,
                geography2 GEOGRAPHY
            )
        ', $fullTableName);

        $this->connection->executeStatement($sql);
    }

    protected function insertTestDataForSpatialFixture(): void
    {
        // Insert test data with various spatial relationships
        $testData = [
            'id=1' => [
                'geometry1' => 'SRID=4326;POINT(0 0)',
                'geometry2' => 'SRID=4326;POINT(1 1)',
                'geography1' => 'SRID=4326;POINT(-9.1393 38.7223)',
                'geography2' => 'SRID=4326;POINT(-0.1276 51.5074)',
            ],
            'id=2' => [
                'geometry1' => 'SRID=4326;POLYGON((0 0, 0 4, 4 4, 4 0, 0 0))',
                'geometry2' => 'SRID=4326;POLYGON((1 1, 1 3, 3 3, 3 1, 1 1))',
                'geography1' => 'SRID=4326;POLYGON((-9.2 38.6, -9.2 38.8, -9.0 38.8, -9.0 38.6, -9.2 38.6))', // Lisbon area
                'geography2' => 'SRID=4326;POLYGON((-0.2 51.4, -0.2 51.6, 0.0 51.6, 0.0 51.4, -0.2 51.4))', // London area
            ],
            'id=3' => [
                'geometry1' => 'SRID=4326;LINESTRING(0 0, 1 1, 2 2)',
                'geometry2' => 'SRID=4326;LINESTRING(3 3, 4 4, 5 5)',
                'geography1' => 'SRID=4326;LINESTRING(-9.1393 38.7223, -9.1293 38.7323)', // Lisbon area
                'geography2' => 'SRID=4326;LINESTRING(-0.1276 51.5074, -0.1176 51.5174)', // London area
            ],
            'id=4' => [
                'geometry1' => 'POLYGON((0 0, 0 2, 2 2, 2 0, 0 0))',
                'geometry2' => 'POLYGON((1 1, 1 3, 3 3, 3 1, 1 1))',
                'geography1' => 'SRID=4326;POLYGON((-9.2 38.6, -9.2 38.8, -9.0 38.8, -9.0 38.6, -9.2 38.6))', // Lisbon area
                'geography2' => 'SRID=4326;POLYGON((-0.2 51.4, -0.2 51.6, 0.0 51.6, 0.0 51.4, -0.2 51.4))', // London area
            ],
            'id=5' => [
                'geometry1' => 'LINESTRING(-1 -1, 5 5)',
                'geometry2' => 'POLYGON((0 0, 0 4, 4 4, 4 0, 0 0))',
                'geography1' => 'SRID=4326;LINESTRING(-9.1393 38.7223, -9.1293 38.7323)', // Lisbon area
                'geography2' => 'SRID=4326;LINESTRING(-0.1276 51.5074, -0.1176 51.5174)', // London area
            ],
            'id=6' => [
                'geometry1' => 'POLYGON((1 1, 1 3, 3 3, 3 1, 1 1))',
                'geometry2' => 'POLYGON((0 0, 0 4, 4 4, 4 0, 0 0))',
                'geography1' => 'SRID=4326;POLYGON((-9.2 38.6, -9.2 38.8, -9.0 38.8, -9.0 38.6, -9.2 38.6))', // Lisbon area
                'geography2' => 'SRID=4326;POLYGON((-0.2 51.4, -0.2 51.6, 0.0 51.6, 0.0 51.4, -0.2 51.4))', // London area
            ],
            'id=7' => [
                'geometry1' => 'POLYGON((0 0, 0 2, 2 2, 2 0, 0 0))',
                'geometry2' => 'POLYGON((0 0, 0 2, 2 2, 2 0, 0 0))',
                'geography1' => 'SRID=4326;POLYGON((-9.2 38.6, -9.2 38.8, -9.0 38.8, -9.0 38.6, -9.2 38.6))', // Lisbon area
                'geography2' => 'SRID=4326;POLYGON((-0.2 51.4, -0.2 51.6, 0.0 51.6, 0.0 51.4, -0.2 51.4))', // London area
            ],
            'id=8' => [
                'geometry1' => 'POLYGON((0 0, 0 2, 2 2, 2 0, 0 0))',
                'geometry2' => 'POLYGON((2 0, 2 2, 4 2, 4 0, 2 0))',
                'geography1' => 'SRID=4326;POLYGON((-9.2 38.6, -9.2 38.8, -9.0 38.8, -9.0 38.6, -9.2 38.6))', // Lisbon area
                'geography2' => 'SRID=4326;POLYGON((-0.2 51.4, -0.2 51.6, 0.0 51.6, 0.0 51.4, -0.2 51.4))', // London area
            ],
            'id=9' => [
                'geometry1' => 'LINESTRING(0 0, 4 4)',
                'geometry2' => 'POINT(2 2)',
                'geography1' => 'SRID=4326;LINESTRING(-9.1393 38.7223, -9.1293 38.7323)', // Lisbon area
                'geography2' => 'SRID=4326;LINESTRING(-0.1276 51.5074, -0.1176 51.5174)', // London area
            ],
            'id=10 (projected coordinate system geometries for testing with linear measurements)' => [
                'geometry1' => 'SRID=3857;LINESTRING(0 0, 1000 0, 1000 1000)', // L-shaped line: 1000m + 1000m = 2000m total in Web Mercator
                'geometry2' => 'SRID=3857;POLYGON((0 0, 0 1000, 1000 1000, 1000 0, 0 0))', // 1km x 1km square
                'geography1' => 'SRID=4326;POLYGON((-9.2 38.6, -9.2 38.8, -9.0 38.8, -9.0 38.6, -9.2 38.6))', // Lisbon area
                'geography2' => 'SRID=4326;POLYGON((-0.2 51.4, -0.2 51.6, 0.0 51.6, 0.0 51.4, -0.2 51.4))', // London area
            ],
            'id=11 (3D geometry with Z coordinate)' => [
                'geometry1' => 'POINT Z(0 0 5)',
                'geometry2' => 'POINT Z(1 1 10)',
                'geography1' => 'SRID=4326;POINT(-9.1393 38.7223)',
                'geography2' => 'SRID=4326;POINT(-0.1276 51.5074)',
            ],
            'id=12 (geometry with M coordinate)' => [
                'geometry1' => 'POINT M(0 0 5)',
                'geometry2' => 'POINT M(1 1 10)',
                'geography1' => 'SRID=4326;POINT(-9.1393 38.7223)',
                'geography2' => 'SRID=4326;POINT(-0.1276 51.5074)',
            ],
            'id=13 (polygon adjacent to id=4)' => [
                'geometry1' => 'POLYGON((2 0, 2 2, 4 2, 4 0, 2 0))',
                'geometry2' => 'POLYGON((0 0, 0 2, 2 2, 2 0, 0 0))',
                'geography1' => 'SRID=4326;POINT(-9.1393 38.7223)',
                'geography2' => 'SRID=4326;POINT(-0.1276 51.5074)',
            ],
            'id=14 (compound curve with 3 components)' => [
                'geometry1' => 'COMPOUNDCURVE((0 0, 1 1), CIRCULARSTRING(1 1, 2 0, 3 1), (3 1, 4 0))',
                'geometry2' => 'COMPOUNDCURVE((0 0, 1 1), CIRCULARSTRING(1 1, 2 0, 3 1))',
                'geography1' => 'SRID=4326;POINT(-9.1393 38.7223)',
                'geography2' => 'SRID=4326;POINT(-0.1276 51.5074)',
            ],
        ];

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, self::TABLE_NAME);
        foreach ($testData as $row) {
            $sql = \sprintf('
                INSERT INTO %s (geometry1, geometry2, geography1, geography2)
                VALUES (ST_GeomFromText(?), ST_GeomFromText(?), ST_GeogFromText(?), ST_GeogFromText(?))
            ', $fullTableName);

            $this->connection->executeStatement($sql, [
                $row['geometry1'],
                $row['geometry2'],
                $row['geography1'],
                $row['geography2'],
            ]);
        }
    }
}
