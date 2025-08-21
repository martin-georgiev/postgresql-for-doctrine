<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

abstract class SpatialArrayTypeTestCase extends TestCase
{
    /**
     * Perform a round-trip using ARRAY[...] constructor for insertion for spatial WKT arrays.
     *
     * @param non-empty-string $elementPgType
     */
    protected function runArrayConstructorRoundTrip(string $typeName, string $columnType, string $elementPgType, WktSpatialData ...$spatialData): void
    {
        [$tableName, $columnName] = $this->prepareTestTable($columnType);

        try {
            $placeholders = \implode(',', \array_fill(0, \count($spatialData), '?::'.$elementPgType));
            $sql = \sprintf('INSERT INTO %s.%s ("%s") VALUES (ARRAY[%s])', self::DATABASE_SCHEMA, $tableName, $columnName, $placeholders);
            /** @var list<string> $params */
            $params = \array_values(\array_map(static fn (WktSpatialData $wktSpatialData): string => (string) $wktSpatialData, $spatialData));
            $this->connection->executeStatement($sql, $params);

            $retrieved = $this->fetchConvertedValue($typeName, $tableName, $columnName);
            $this->assertRoundTrip($typeName, $spatialData, $retrieved);
        } finally {
            $this->dropTestTableIfItExists($tableName);
        }
    }

    /**
     * Insert an array of spatial WKTs using ARRAY[...] with per-element casts, then retrieve and assert.
     *
     * @param non-empty-string $elementPgType 'geometry' or 'geography'
     */
    protected function runArrayConstructorTypeTest(string $typeName, string $columnType, string $elementPgType, WktSpatialData ...$spatialData): void
    {
        $this->runArrayConstructorRoundTrip($typeName, $columnType, $elementPgType, ...$spatialData);
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        \assert(\is_array($expected) && \is_array($actual));

        $toString = static fn (WktSpatialData $wktSpatialData): string => (string) $wktSpatialData;

        /** @var list<WktSpatialData> $expected */
        /** @var list<string> $expectedStrings */
        $expectedStrings = \array_values(\array_map($toString, $expected));

        /** @var list<WktSpatialData> $actual */
        /** @var list<string> $actualStrings */
        $actualStrings = \array_values(\array_map($toString, $actual));

        $stripDefaultSrid = static fn (string $wkt): string => \str_starts_with($wkt, 'SRID=4326;') ? \substr($wkt, 10) : $wkt;

        $expectedStrings = \array_map($stripDefaultSrid, $expectedStrings);
        $actualStrings = \array_map($stripDefaultSrid, $actualStrings);

        $this->assertEquals($expectedStrings, $actualStrings, \sprintf('Array type %s round-trip failed', $typeName));
    }
}
