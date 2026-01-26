<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCompact;

class ArrayCompactTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_COMPACT' => ArrayCompact::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'compacts text array' => 'SELECT array_compact(c0_.textArray) AS sclr_0 FROM ContainsArrays c0_',
            'compacts integer array' => 'SELECT array_compact(c0_.integerArray) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'compacts text array' => \sprintf('SELECT ARRAY_COMPACT(e.textArray) FROM %s e', ContainsArrays::class),
            'compacts integer array' => \sprintf('SELECT ARRAY_COMPACT(e.integerArray) FROM %s e', ContainsArrays::class),
        ];
    }
}

