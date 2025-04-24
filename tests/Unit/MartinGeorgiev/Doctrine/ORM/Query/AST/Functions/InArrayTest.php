<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\InArray;

class InArrayTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'IN_ARRAY' => InArray::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'checks if value is in array' => 'SELECT ? = ANY(c0_.array1) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks if value is in array' => \sprintf('SELECT IN_ARRAY(:value, e.array1) FROM %s e', ContainsArrays::class),
        ];
    }
}
