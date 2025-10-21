<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReverse;

class ArrayReverseTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_REVERSE' => ArrayReverse::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'reverses text array' => 'SELECT array_reverse(c0_.textArray) AS sclr_0 FROM ContainsArrays c0_',
            'reverses integer array' => 'SELECT array_reverse(c0_.integerArray) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'reverses text array' => \sprintf('SELECT ARRAY_REVERSE(e.textArray) FROM %s e', ContainsArrays::class),
            'reverses integer array' => \sprintf('SELECT ARRAY_REVERSE(e.integerArray) FROM %s e', ContainsArrays::class),
        ];
    }
}
