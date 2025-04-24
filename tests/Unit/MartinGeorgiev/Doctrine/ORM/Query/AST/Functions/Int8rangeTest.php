<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsIntegers;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int8range;

class Int8rangeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'INT8RANGE' => Int8range::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT int8range(c0_.integer1, c0_.integer2) AS sclr_0 FROM ContainsIntegers c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT INT8RANGE(e.integer1, e.integer2) FROM %s e', ContainsIntegers::class),
        ];
    }
}
