<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeographyForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidGeographyForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Geography;

final class GeographyTest extends BaseSpatialTypeTestCase
{
    protected function createFixture(): Geography
    {
        return new Geography();
    }

    protected function getExpectedTypeName(): string
    {
        return 'geography';
    }

    protected function getForPHPExceptionClass(): string
    {
        return InvalidGeographyForPHPException::class;
    }

    protected function getForDatabaseExceptionClass(): string
    {
        return InvalidGeographyForDatabaseException::class;
    }
}
