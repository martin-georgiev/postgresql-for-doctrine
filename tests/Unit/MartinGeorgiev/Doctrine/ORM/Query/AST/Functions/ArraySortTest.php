<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArraySort;

class ArraySortTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_SORT' => ArraySort::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'sorts text array' => 'SELECT array_sort(c0_.textArray) AS sclr_0 FROM ContainsArrays c0_',
            'sorts integer array' => 'SELECT array_sort(c0_.integerArray) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'sorts text array' => \sprintf('SELECT ARRAY_SORT(e.textArray) FROM %s e', ContainsArrays::class),
            'sorts integer array' => \sprintf('SELECT ARRAY_SORT(e.integerArray) FROM %s e', ContainsArrays::class),
        ];
    }
}
