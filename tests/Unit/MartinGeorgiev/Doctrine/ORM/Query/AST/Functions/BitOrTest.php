<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsIntegers;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BitOr;

class BitOrTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'BIT_OR' => BitOr::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'aggregates integer field with bitwise OR' => 'SELECT bit_or(c0_.integer1) AS sclr_0 FROM ContainsIntegers c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'aggregates integer field with bitwise OR' => \sprintf('SELECT BIT_OR(e.integer1) FROM %s e', ContainsIntegers::class),
        ];
    }
}
