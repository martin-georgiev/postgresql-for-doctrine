<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPositions;

class ArrayPositionsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_POSITIONS' => ArrayPositions::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'finds string element' => "SELECT array_positions(c0_.textArray, 'new-value') AS sclr_0 FROM ContainsArrays c0_",
            'finds numeric element' => 'SELECT array_positions(c0_.textArray, 42) AS sclr_0 FROM ContainsArrays c0_',
            'finds element using parameter' => 'SELECT array_positions(c0_.textArray, ?) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'finds string element' => \sprintf("SELECT ARRAY_POSITIONS(e.textArray, 'new-value') FROM %s e", ContainsArrays::class),
            'finds numeric element' => \sprintf('SELECT ARRAY_POSITIONS(e.textArray, 42) FROM %s e', ContainsArrays::class),
            'finds element using parameter' => \sprintf('SELECT ARRAY_POSITIONS(e.textArray, :dql_parameter) FROM %s e', ContainsArrays::class),
        ];
    }
}
