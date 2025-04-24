<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayDimensions;

class ArrayDimensionsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_DIMENSIONS' => ArrayDimensions::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'gets dimensions of array field' => 'SELECT array_dims(c0_.array1) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'gets dimensions of array field' => \sprintf('SELECT ARRAY_DIMENSIONS(e.array1) FROM %s e', ContainsArrays::class),
        ];
    }
}
