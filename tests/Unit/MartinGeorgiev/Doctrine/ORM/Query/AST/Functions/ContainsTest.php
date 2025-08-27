<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
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
            'contains array of integers' => "SELECT (c0_.textArray @> '{681,1185,1878}') AS sclr_0 FROM ContainsArrays c0_",
            'contains array of strings' => "SELECT (c0_.textArray @> '{\"foo\",\"bar\"}') AS sclr_0 FROM ContainsArrays c0_",
            'contains single element' => "SELECT (c0_.textArray @> '{42}') AS sclr_0 FROM ContainsArrays c0_",
            'contains using parameter' => 'SELECT (c0_.textArray @> ?) AS sclr_0 FROM ContainsArrays c0_',
            'contains with spatial geometries' => 'SELECT (c0_.geometry1 @> c0_.geometry2) AS sclr_0 FROM ContainsGeometries c0_',
            'contains with spatial literal' => "SELECT (c0_.geometry1 @> 'POINT(1 2)') AS sclr_0 FROM ContainsGeometries c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'contains array of integers' => \sprintf("SELECT CONTAINS(e.textArray, '{681,1185,1878}') FROM %s e", ContainsArrays::class),
            'contains array of strings' => \sprintf("SELECT CONTAINS(e.textArray, '{\"foo\",\"bar\"}') FROM %s e", ContainsArrays::class),
            'contains single element' => \sprintf("SELECT CONTAINS(e.textArray, '{42}') FROM %s e", ContainsArrays::class),
            'contains using parameter' => \sprintf('SELECT CONTAINS(e.textArray, :parameter) FROM %s e', ContainsArrays::class),
            'contains with spatial geometries' => \sprintf('SELECT CONTAINS(e.geometry1, e.geometry2) FROM %s e', ContainsGeometries::class),
            'contains with spatial literal' => \sprintf("SELECT CONTAINS(e.geometry1, 'POINT(1 2)') FROM %s e", ContainsGeometries::class),
        ];
    }
}
