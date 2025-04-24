<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLength;

class ArrayLengthTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_LENGTH' => ArrayLength::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'gets length of array with hardcoded dimension' => 'SELECT array_length(c0_.array1, 1) AS sclr_0 FROM ContainsArrays c0_',
            'gets length of array with parameterized dimension' => 'SELECT array_length(c0_.array1, ?) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'gets length of array with hardcoded dimension' => \sprintf('SELECT ARRAY_LENGTH(e.array1, 1) FROM %s e', ContainsArrays::class),
            'gets length of array with parameterized dimension' => \sprintf('SELECT ARRAY_LENGTH(e.array1, :dql_parameter) FROM %s e', ContainsArrays::class),
        ];
    }
}
