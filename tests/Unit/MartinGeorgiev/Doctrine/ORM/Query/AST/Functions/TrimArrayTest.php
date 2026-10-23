<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TrimArray;

final class TrimArrayTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TRIM_ARRAY' => TrimArray::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'trims a literal number of elements' => 'SELECT trim_array(c0_.textArray, 1) AS sclr_0 FROM ContainsArrays c0_',
            'trims a parameterized number of elements' => 'SELECT trim_array(c0_.textArray, ?) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'trims a literal number of elements' => \sprintf('SELECT TRIM_ARRAY(e.textArray, 1) FROM %s e', ContainsArrays::class),
            'trims a parameterized number of elements' => \sprintf('SELECT TRIM_ARRAY(e.textArray, :dql_parameter) FROM %s e', ContainsArrays::class),
        ];
    }
}
