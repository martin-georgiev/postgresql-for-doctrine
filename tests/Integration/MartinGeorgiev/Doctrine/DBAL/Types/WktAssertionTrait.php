<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

/**
 * Provides WKT normalization and comparison to handle PostgreSQL formatting differences
 * (spaces after commas, decimal normalization, SRID prefixes).
 */
trait WktAssertionTrait
{
    protected function assertWktEquals(WktSpatialData $expected, WktSpatialData $actual): void
    {
        $this->assertEquals(
            $this->normalizeWkt((string) $expected),
            $this->normalizeWkt((string) $actual),
            'WKT spatial data mismatch'
        );
    }

    /**
     * Handles SRID prefixes, whitespace, and decimal formatting differences.
     */
    private function normalizeWkt(string $wkt): string
    {
        // Remove SRID prefix for comparison if present
        if (\str_starts_with($wkt, 'SRID=')) {
            $parts = \explode(';', $wkt, 2);
            $wkt = $parts[1] ?? $wkt;
        }

        // Normalize whitespace: PostgreSQL returns WKT without spaces after commas
        $normalized = \preg_replace('/,\s+/', ',', $wkt);
        if ($normalized === null) {
            return $wkt;
        }

        // Normalize decimal numbers: PostgreSQL may return -9.0 as -9
        $result = \preg_replace('/(\d)\.0(?=\D|$)/', '$1', $normalized);

        return $result ?? $normalized;
    }
}
