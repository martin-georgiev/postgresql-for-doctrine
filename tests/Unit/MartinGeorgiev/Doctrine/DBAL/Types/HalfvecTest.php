<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidHalfvecForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidHalfvecForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Halfvec;

final class HalfvecTest extends BaseVectorTypeTestCase
{
    protected function getExpectedTypeName(): string
    {
        return 'halfvec';
    }

    protected function getExpectedSQLTypeName(): string
    {
        return 'HALFVEC';
    }

    protected function createFixture(): Halfvec
    {
        return new Halfvec();
    }

    protected function getDatabaseExceptionClass(): string
    {
        return InvalidHalfvecForDatabaseException::class;
    }

    protected function getPHPExceptionClass(): string
    {
        return InvalidHalfvecForPHPException::class;
    }
}
