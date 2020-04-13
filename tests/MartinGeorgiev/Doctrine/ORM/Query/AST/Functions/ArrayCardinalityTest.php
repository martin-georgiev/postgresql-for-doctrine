<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCardinality;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArrays;

class ArrayCardinalityTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_CARDINALITY' => ArrayCardinality::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT cardinality(c0_.array1) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ARRAY_CARDINALITY(e.array1) FROM %s e', ContainsArrays::class),
        ];
    }
}
