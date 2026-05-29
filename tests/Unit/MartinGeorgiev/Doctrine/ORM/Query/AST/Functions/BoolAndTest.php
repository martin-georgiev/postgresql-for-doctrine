<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsIntegers;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BoolAnd;

class BoolAndTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'BOOL_AND' => BoolAnd::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'aggregates boolean field' => 'SELECT bool_and(c0_.integer1) AS sclr_0 FROM ContainsIntegers c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'aggregates boolean field' => \sprintf('SELECT BOOL_AND(e.integer1) FROM %s e', ContainsIntegers::class),
        ];
    }
}
