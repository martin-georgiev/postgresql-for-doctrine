<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayDimensions;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

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
            'SELECT array_dims(c0_.array) AS sclr_0 FROM ContainsArray c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ARRAY_DIMENSIONS(e.array) FROM %s e', ContainsArray::class),
        ];
    }
}
