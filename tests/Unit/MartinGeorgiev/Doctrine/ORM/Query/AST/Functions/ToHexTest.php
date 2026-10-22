<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsIntegers;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToHex;

final class ToHexTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_HEX' => ToHex::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'writes a literal in hexadecimal' => 'SELECT to_hex(255) AS sclr_0 FROM ContainsIntegers c0_',
            'writes a field in hexadecimal' => 'SELECT to_hex(c0_.integer1) AS sclr_0 FROM ContainsIntegers c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'writes a literal in hexadecimal' => \sprintf('SELECT TO_HEX(255) FROM %s e', ContainsIntegers::class),
            'writes a field in hexadecimal' => \sprintf('SELECT TO_HEX(e.integer1) FROM %s e', ContainsIntegers::class),
        ];
    }
}
