<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

class ArrTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new Arr('ARRAY');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ARRAY' => Arr::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'creates array from multiple literal values' => "SELECT ARRAY['foo', 'bar', 'baz'] AS sclr_0 FROM ContainsArrays c0_",
            'creates array from column references' => 'SELECT ARRAY[c0_.array1] AS sclr_0 FROM ContainsArrays c0_',
            'creates array from mix of column references and literals' => "SELECT ARRAY[c0_.array1, 'test-value', c0_.array2] AS sclr_0 FROM ContainsArrays c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'creates array from multiple literal values' => \sprintf("SELECT ARRAY('foo', 'bar', 'baz') FROM %s e", ContainsArrays::class),
            'creates array from column references' => \sprintf('SELECT ARRAY(e.array1) FROM %s e', ContainsArrays::class),
            'creates array from mix of column references and literals' => \sprintf("SELECT ARRAY(e.array1, 'test-value', e.array2) FROM %s e", ContainsArrays::class),
        ];
    }
}
