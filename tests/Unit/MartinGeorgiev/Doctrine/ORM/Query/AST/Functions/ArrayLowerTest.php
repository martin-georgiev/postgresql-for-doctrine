<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLower;

class ArrayLowerTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_LOWER' => ArrayLower::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'gets lower bound for text array' => 'SELECT array_lower(c0_.textArray, 1) AS sclr_0 FROM ContainsArrays c0_',
            'gets lower bound for integer array' => 'SELECT array_lower(c0_.integerArray, 1) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'gets lower bound for text array' => \sprintf('SELECT ARRAY_LOWER(e.textArray, 1) FROM %s e', ContainsArrays::class),
            'gets lower bound for integer array' => \sprintf('SELECT ARRAY_LOWER(e.integerArray, 1) FROM %s e', ContainsArrays::class),
        ];
    }
}

