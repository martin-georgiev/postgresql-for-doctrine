<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayNumberOfDimensions;

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
            'gets number of dimensions of array field' => 'SELECT array_ndims(c0_.array1) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'gets number of dimensions of array field' => \sprintf('SELECT ARRAY_NUMBER_OF_DIMENSIONS(e.array1) FROM %s e', ContainsArrays::class),
        ];
    }
}
