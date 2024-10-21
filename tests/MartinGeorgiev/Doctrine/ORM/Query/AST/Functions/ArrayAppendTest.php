<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAppend;

class ArrayAppendTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_APPEND' => ArrayAppend::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT array_append(c0_.array1, 1989) AS sclr_0 FROM ContainsArrays c0_',
            "SELECT array_append(c0_.array1, 'country') AS sclr_0 FROM ContainsArrays c0_",
            'SELECT array_append(c0_.array1, ?) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ARRAY_APPEND(e.array1, 1989) FROM %s e', ContainsArrays::class),
            \sprintf("SELECT ARRAY_APPEND(e.array1, 'country') FROM %s e", ContainsArrays::class),
            \sprintf('SELECT ARRAY_APPEND(e.array1, :dql_parameter) FROM %s e', ContainsArrays::class),
        ];
    }
}
