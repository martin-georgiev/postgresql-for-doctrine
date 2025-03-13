<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;

class ArrTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY' => Arr::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            // Multiple literal values
            "SELECT ARRAY['foo', 'bar', 'baz'] AS sclr_0 FROM ContainsArrays c0_",
            // Column references
            'SELECT ARRAY[c0_.array1] AS sclr_0 FROM ContainsArrays c0_',
            // Mix of column references and literals
            "SELECT ARRAY[c0_.array1, 'test-value', c0_.array2] AS sclr_0 FROM ContainsArrays c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            // Multiple literal values
            \sprintf("SELECT ARRAY('foo', 'bar', 'baz') FROM %s e", ContainsArrays::class),
            // Column references
            \sprintf('SELECT ARRAY(e.array1) FROM %s e', ContainsArrays::class),
            // Mix of column references and literals
            \sprintf("SELECT ARRAY(e.array1, 'test-value', e.array2) FROM %s e", ContainsArrays::class),
        ];
    }
}
