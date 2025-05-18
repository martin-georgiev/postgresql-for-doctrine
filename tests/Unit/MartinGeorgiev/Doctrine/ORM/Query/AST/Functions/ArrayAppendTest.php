<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

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
            'appends string element to array' => "SELECT array_append(c0_.textArray, 'new-value') AS sclr_0 FROM ContainsArrays c0_",
            'appends numeric element to array' => 'SELECT array_append(c0_.textArray, 42) AS sclr_0 FROM ContainsArrays c0_',
            'appends element using parameter' => 'SELECT array_append(c0_.textArray, ?) AS sclr_0 FROM ContainsArrays c0_',
            'appends null to array' => 'SELECT array_append(c0_.textArray, null) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'appends string element to array' => \sprintf("SELECT ARRAY_APPEND(e.textArray, 'new-value') FROM %s e", ContainsArrays::class),
            'appends numeric element to array' => \sprintf('SELECT ARRAY_APPEND(e.textArray, 42) FROM %s e', ContainsArrays::class),
            'appends element using parameter' => \sprintf('SELECT ARRAY_APPEND(e.textArray, :dql_parameter) FROM %s e', ContainsArrays::class),
            'appends null to array' => \sprintf('SELECT ARRAY_APPEND(e.textArray, NULL) FROM %s e', ContainsArrays::class),
        ];
    }
}
