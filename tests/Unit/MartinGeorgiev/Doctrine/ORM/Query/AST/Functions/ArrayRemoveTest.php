<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

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
            'removes string element from array' => "SELECT array_remove(c0_.array1, 'value-to-remove') AS sclr_0 FROM ContainsArrays c0_",
            'removes numeric element from array' => 'SELECT array_remove(c0_.array1, 42) AS sclr_0 FROM ContainsArrays c0_',
            'removes element using parameter' => 'SELECT array_remove(c0_.array1, ?) AS sclr_0 FROM ContainsArrays c0_',
            'removes null from array' => 'SELECT array_remove(c0_.array1, null) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'removes string element from array' => \sprintf("SELECT ARRAY_REMOVE(e.array1, 'value-to-remove') FROM %s e", ContainsArrays::class),
            'removes numeric element from array' => \sprintf('SELECT ARRAY_REMOVE(e.array1, 42) FROM %s e', ContainsArrays::class),
            'removes element using parameter' => \sprintf('SELECT ARRAY_REMOVE(e.array1, :dql_parameter) FROM %s e', ContainsArrays::class),
            'removes null from array' => \sprintf('SELECT ARRAY_REMOVE(e.array1, NULL) FROM %s e', ContainsArrays::class),
        ];
    }
}
