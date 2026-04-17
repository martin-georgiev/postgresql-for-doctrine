<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidVectorForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidVectorForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\Vector;

final class VectorTest extends BaseVectorTypeTestCase
{
    protected function getExpectedTypeName(): string
    {
        return 'vector';
    }

    protected function getExpectedSQLTypeName(): string
    {
        return 'VECTOR';
    }

    protected function createFixture(): Vector
    {
        return new Vector();
    }

    protected function getDatabaseExceptionClass(): string
    {
        return InvalidVectorForDatabaseException::class;
    }

    protected function getPHPExceptionClass(): string
    {
        return InvalidVectorForPHPException::class;
    }
}
