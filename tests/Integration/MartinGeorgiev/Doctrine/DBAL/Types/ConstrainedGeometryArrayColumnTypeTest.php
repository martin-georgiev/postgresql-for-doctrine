<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

final class ConstrainedGeometryArrayColumnTypeTest extends ConstrainedSpatialColumnTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'geometry[]';
    }

    protected function getExpectedColumnType(): string
    {
        return 'GEOMETRY(POINT,4326)[]';
    }

    protected function toColumnValue(WktSpatialData $wktSpatialData): array
    {
        return [$wktSpatialData];
    }
}
