<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReplace;

class ArrayReplaceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_REPLACE' => ArrayReplace::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'replaces string element in array' => "SELECT array_replace(c0_.array1, 'old-value', 'new-value') AS sclr_0 FROM ContainsArrays c0_",
            'replaces numeric element in array' => 'SELECT array_replace(c0_.array1, 42, 43) AS sclr_0 FROM ContainsArrays c0_',
            'replaces element using parameters' => 'SELECT array_replace(c0_.array1, ?, ?) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'replaces string element in array' => \sprintf("SELECT ARRAY_REPLACE(e.array1, 'old-value', 'new-value') FROM %s e", ContainsArrays::class),
            'replaces numeric element in array' => \sprintf('SELECT ARRAY_REPLACE(e.array1, 42, 43) FROM %s e', ContainsArrays::class),
            'replaces element using parameters' => \sprintf('SELECT ARRAY_REPLACE(e.array1, :old_value, :new_value) FROM %s e', ContainsArrays::class),
        ];
    }
}
