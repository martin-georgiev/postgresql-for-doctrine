<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayUpper;

class ArrayUpperTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_UPPER' => ArrayUpper::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'gets upper bound for text array' => 'SELECT array_upper(c0_.textArray, 1) AS sclr_0 FROM ContainsArrays c0_',
            'gets upper bound for integer array' => 'SELECT array_upper(c0_.integerArray, 1) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'gets upper bound for text array' => \sprintf('SELECT ARRAY_UPPER(e.textArray, 1) FROM %s e', ContainsArrays::class),
            'gets upper bound for integer array' => \sprintf('SELECT ARRAY_UPPER(e.integerArray, 1) FROM %s e', ContainsArrays::class),
        ];
    }
}
