<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCardinality;

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
            'gets cardinality of array' => 'SELECT cardinality(c0_.array1) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'gets cardinality of array' => \sprintf('SELECT ARRAY_CARDINALITY(e.array1) FROM %s e', ContainsArrays::class),
        ];
    }
}
