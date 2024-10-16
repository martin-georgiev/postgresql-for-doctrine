<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayRemove;

class ArrayRemoveTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_REMOVE' => ArrayRemove::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT array_remove(c0_.array1, 1944) AS sclr_0 FROM ContainsArrays c0_',
            "SELECT array_remove(c0_.array1, 'peach') AS sclr_0 FROM ContainsArrays c0_",
            "SELECT array_remove(c0_.array1, ?) AS sclr_0 FROM ContainsArrays c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ARRAY_REMOVE(e.array1, 1944) FROM %s e', ContainsArrays::class),
            \sprintf("SELECT ARRAY_REMOVE(e.array1, 'peach') FROM %s e", ContainsArrays::class),
            \sprintf("SELECT ARRAY_REMOVE(e.array1, :dql_parameter) FROM %s e", ContainsArrays::class),
        ];
    }
}
