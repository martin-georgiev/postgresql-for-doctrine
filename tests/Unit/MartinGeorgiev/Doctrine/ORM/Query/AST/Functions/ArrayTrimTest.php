<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayTrim;

class ArrayTrimTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_TRIM' => ArrayTrim::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'trims text array bounds' => 'SELECT array_trim(c0_.textArray, 1) AS sclr_0 FROM ContainsArrays c0_',
            'trims integer array bounds' => 'SELECT array_trim(c0_.integerArray, 1) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'trims text array bounds' => \sprintf('SELECT ARRAY_TRIM(e.textArray, 1) FROM %s e', ContainsArrays::class),
            'trims integer array bounds' => \sprintf('SELECT ARRAY_TRIM(e.integerArray, 1) FROM %s e', ContainsArrays::class),
        ];
    }
}

