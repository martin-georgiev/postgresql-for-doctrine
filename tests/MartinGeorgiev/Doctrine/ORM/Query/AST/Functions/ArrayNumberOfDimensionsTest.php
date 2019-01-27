<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayNumberOfDimensions;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

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
            'SELECT array_ndims(c0_.array) AS sclr_0 FROM ContainsArray c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ARRAY_NUMBER_OF_DIMENSIONS(e.array) FROM %s e', ContainsArray::class),
        ];
    }
}
