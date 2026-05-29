<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsIntegers;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BitAnd;

class BitAndTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'BIT_AND' => BitAnd::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'aggregates integer field with bitwise AND' => 'SELECT bit_and(c0_.integer1) AS sclr_0 FROM ContainsIntegers c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'aggregates integer field with bitwise AND' => \sprintf('SELECT BIT_AND(e.integer1) FROM %s e', ContainsIntegers::class),
        ];
    }
}
