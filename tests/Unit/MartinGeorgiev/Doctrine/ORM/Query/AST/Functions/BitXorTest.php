<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsIntegers;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BitXor;

class BitXorTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'BIT_XOR' => BitXor::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'aggregates integer field with bitwise XOR' => 'SELECT bit_xor(c0_.integer1) AS sclr_0 FROM ContainsIntegers c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'aggregates integer field with bitwise XOR' => \sprintf('SELECT BIT_XOR(e.integer1) FROM %s e', ContainsIntegers::class),
        ];
    }
}
