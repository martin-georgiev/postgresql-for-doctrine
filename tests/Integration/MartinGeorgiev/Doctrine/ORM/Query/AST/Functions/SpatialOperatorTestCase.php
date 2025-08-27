<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Tests\Integration\MartinGeorgiev\TestCase as BaseTestCase;

abstract class SpatialOperatorTestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestTableForSpatialFixture();
        $this->insertTestDataForSpatialFixture();
    }

    protected function createTestTableForSpatialFixture(): void
    {
        $tableName = 'containsgeometries';

        $this->createTestSchema();
        $this->dropTestTableIfItExists($tableName);

        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);
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
        $tableName = 'containsgeometries';
        $fullTableName = \sprintf('%s.%s', self::DATABASE_SCHEMA, $tableName);

        // Insert test data with various spatial relationships
        $testData = [
            [
                'geometry1' => 'POINT(0 0)',
                'geometry2' => 'POINT(1 1)',
                'geography1' => 'SRID=4326;POINT(-9.1393 38.7223)',
                'geography2' => 'SRID=4326;POINT(-0.1276 51.5074)',
            ],
            [
                'geometry1' => 'POLYGON((0 0, 0 2, 2 2, 2 0, 0 0))',
                'geometry2' => 'POLYGON((1 1, 1 3, 3 3, 3 1, 1 1))',
                'geography1' => 'SRID=4326;POLYGON((-9.2 38.6, -9.2 38.8, -9.0 38.8, -9.0 38.6, -9.2 38.6))', // Lisbon area
                'geography2' => 'SRID=4326;POLYGON((-0.2 51.4, -0.2 51.6, 0.0 51.6, 0.0 51.4, -0.2 51.4))',   // London area
            ],
            [
                'geometry1' => 'LINESTRING(0 0, 1 1, 2 2)',
                'geometry2' => 'LINESTRING(3 3, 4 4, 5 5)',
                'geography1' => 'SRID=4326;LINESTRING(-9.1393 38.7223, -9.1293 38.7323)', // Lisbon area
                'geography2' => 'SRID=4326;LINESTRING(-0.1276 51.5074, -0.1176 51.5174)', // London area
            ],
        ];

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
