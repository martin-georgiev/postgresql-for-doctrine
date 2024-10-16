<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPrepend;

class ArrayPrependTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_PREPEND' => ArrayPrepend::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT array_prepend(1885, c0_.array1) AS sclr_0 FROM ContainsArrays c0_',
            "SELECT array_prepend('red', c0_.array1) AS sclr_0 FROM ContainsArrays c0_",
            "SELECT array_prepend(?, c0_.array1) AS sclr_0 FROM ContainsArrays c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ARRAY_PREPEND(1885, e.array1) FROM %s e', ContainsArrays::class),
            \sprintf("SELECT ARRAY_PREPEND('red', e.array1) FROM %s e", ContainsArrays::class),
            \sprintf("SELECT ARRAY_PREPEND(:dql_parameter, e.array1) FROM %s e", ContainsArrays::class),
        ];
    }
}
