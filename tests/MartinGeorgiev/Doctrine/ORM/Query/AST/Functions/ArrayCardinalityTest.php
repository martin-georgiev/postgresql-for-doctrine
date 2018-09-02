<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCardinality;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

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
            'SELECT cardinality(c0_.array) AS sclr_0 FROM ContainsArray c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            sprintf('SELECT ARRAY_CARDINALITY(e.array) FROM %s e', ContainsArray::class),
        ];
    }
}
