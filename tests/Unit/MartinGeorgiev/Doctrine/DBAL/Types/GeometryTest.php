<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeometryForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeometryForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Geometry;

final class GeometryTest extends BaseSpatialTypeTestCase
{
    protected function createFixture(): Geometry
    {
        return new Geometry();
    }

    protected function getExpectedTypeName(): string
    {
        return 'geometry';
    }

    protected function getForPHPExceptionClass(): string
    {
        return InvalidGeometryForPHPException::class;
    }

    protected function getForDatabaseExceptionClass(): string
    {
        return InvalidGeometryForDatabaseException::class;
    }
}
