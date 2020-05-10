<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayNumberOfDimensions;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsArrays;

class ArrayNumberOfDimensionsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_NUMBER_OF_DIMENSIONS' => ArrayNumberOfDimensions::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT array_ndims(c0_.array1) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ARRAY_NUMBER_OF_DIMENSIONS(e.array1) FROM %s e', ContainsArrays::class),
        ];
    }
}
