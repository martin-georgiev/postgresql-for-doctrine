<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Contains;

class ContainsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CONTAINS' => Contains::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'contains array of integers' => "SELECT (c0_.array1 @> '{681,1185,1878}') AS sclr_0 FROM ContainsArrays c0_",
            'contains array of strings' => "SELECT (c0_.array1 @> '{\"foo\",\"bar\"}') AS sclr_0 FROM ContainsArrays c0_",
            'contains single element' => "SELECT (c0_.array1 @> '{42}') AS sclr_0 FROM ContainsArrays c0_",
            'contains using parameter' => 'SELECT (c0_.array1 @> ?) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'contains array of integers' => \sprintf("SELECT CONTAINS(e.array1, '{681,1185,1878}') FROM %s e", ContainsArrays::class),
            'contains array of strings' => \sprintf("SELECT CONTAINS(e.array1, '{\"foo\",\"bar\"}') FROM %s e", ContainsArrays::class),
            'contains single element' => \sprintf("SELECT CONTAINS(e.array1, '{42}') FROM %s e", ContainsArrays::class),
            'contains using parameter' => \sprintf('SELECT CONTAINS(e.array1, :parameter) FROM %s e', ContainsArrays::class),
        ];
    }
}
