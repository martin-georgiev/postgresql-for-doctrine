<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

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
            'replaces string element in array' => "SELECT array_replace(c0_.textArray, 'old-value', 'new-value') AS sclr_0 FROM ContainsArrays c0_",
            'replaces numeric element in array' => 'SELECT array_replace(c0_.integerArray, 42, 43) AS sclr_0 FROM ContainsArrays c0_',
            'replaces element using parameters' => 'SELECT array_replace(c0_.textArray, ?, ?) AS sclr_0 FROM ContainsArrays c0_',
            'replaces null with string' => "SELECT array_replace(c0_.textArray, null, 'new-value') AS sclr_0 FROM ContainsArrays c0_",
            'replaces value with null' => "SELECT array_replace(c0_.textArray, 'old-value', null) AS sclr_0 FROM ContainsArrays c0_",
            'replaces null with null' => 'SELECT array_replace(c0_.textArray, null, null) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'replaces string element in array' => \sprintf("SELECT ARRAY_REPLACE(e.textArray, 'old-value', 'new-value') FROM %s e", ContainsArrays::class),
            'replaces numeric element in array' => \sprintf('SELECT ARRAY_REPLACE(e.integerArray, 42, 43) FROM %s e', ContainsArrays::class),
            'replaces element using parameters' => \sprintf('SELECT ARRAY_REPLACE(e.textArray, :old_value, :new_value) FROM %s e', ContainsArrays::class),
            'replaces null with string' => \sprintf("SELECT ARRAY_REPLACE(e.textArray, NULL, 'new-value') FROM %s e", ContainsArrays::class),
            'replaces value with null' => \sprintf("SELECT ARRAY_REPLACE(e.textArray, 'old-value', NULL) FROM %s e", ContainsArrays::class),
            'replaces null with null' => \sprintf('SELECT ARRAY_REPLACE(e.textArray, NULL, NULL) FROM %s e', ContainsArrays::class),
        ];
    }
}
